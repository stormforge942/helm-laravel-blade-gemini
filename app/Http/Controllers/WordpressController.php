<?php

namespace App\Http\Controllers;

use App\Services\WordPressService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Corcel\Model\Option;
use App\Models\Site;
use App\Models\Post;
use Corcel\Model\Meta\PostMeta;
use Corcel\Model\Taxonomy;
use Corcel\Model\Term;
use Corcel\Model\TermRelationship;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;


class WordpressController extends Controller
{
    protected $wordPressService;

    public function __construct(WordPressService $wordPressService)
    {
        $this->wordPressService = $wordPressService;
    }

    private function getServersAndSites()
    {
        $user = auth()->user();
        $restrictedSites = $user->restrictedSites;

        if ($restrictedSites->isEmpty()) {
            // User can access all sites, so retrieve all servers and their sites
            $servers = $this->wordPressService->getServers();
            $sites = Site::whereIn('server', $servers->pluck('name'))->get(); // Assuming `name` is the server identifier
        } else {
            // User is restricted, so retrieve only the allowed servers and sites
            $allowedServers = $restrictedSites->pluck('server')->unique();
            $servers = $this->wordPressService->getServers()->filter(function ($server) use ($allowedServers) {
                return $allowedServers->contains($server);
            });

            // Filter sites on these servers that are specifically allowed for the user
            $sites = Site::whereIn('id', $restrictedSites->pluck('id'))->get();
        }

        return compact('servers', 'sites');
    }


    public function googlePoi()
    {
        $data = $this->getServersAndSites();
        return view('creation.neighborhoods.google-poi', $data);
    }

    public function servicePage()
    {
        $data = $this->getServersAndSites();
        return view('creation.pages.service', $data);
    }

    public function blogPost()
    {
        $data = $this->getServersAndSites();

        // Fetch all unique niches from the database
        $niches = Site::select('niche')
            ->whereNotNull('niche')
            ->where('niche', '!=', '')
            ->orderBy('niche', 'asc')
            ->groupBy('niche')
            ->get();

        // Define breadcrumbs for the blog post
        $breadcrumbs = [
            ['name' => 'Creation', 'url' => route('creation.index')],
            ['name' => 'Create Blog Post', 'url' => null] // Current page
        ];
        $data['breadcrumbs'] = $breadcrumbs;

        return view('creation.posts.blog', array_merge($data, compact('niches')));
    }

    public function getPages(Request $request)
    {
        $siteId = $request->input('siteId');

        try {
            $this->wordPressService->setDb($siteId);

            $pages = collect(); // Initialize an empty collection

            try {
                $pages = Post::on('wordpress')
                    ->status('publish')
                    ->take(3)
                    ->get();

                // Check if the second query returns results
                if (!$pages->isEmpty()) {
                    // If second query has results, attempt the first query
                    try {
                        $tempPages = Post::on('wordpress')
                            ->status('publish')
                            ->orderBy('post_date', 'desc')
                            ->take(3)
                            ->get();

                        \Log::info('Query executed.', ['tempPages' => $tempPages->toArray()]);

                        // If the first query succeeds, use its results
                        if (!$tempPages->isEmpty()) {
                            $pages = $tempPages;
                        }
                    } catch (\Exception $e) {
                        // Log the error for debugging
                        \Log::error('Second query failed after first query succeeded: ' . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                // Log the error for debugging
                \Log::error('First query failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve pages: ' . $e->getMessage(),
                ], 500); // 500 Internal Server Error
            }

            // Check if the final results are empty
            if ($pages->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pages found.'
                ], 404); // 404 Not Found
            }

            return response()->json([
                'success' => true,
                'data' => $pages
            ]);
        } catch (\Exception $e) {
            // If setting the database connection fails, return an error message in JSON format
            \Log::error('Failed to connect to the site: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to the site: ' . $e->getMessage(),
            ], 400); // 400 Bad Request
        }
    }


    public function createServicePage(Request $request)
{
    $siteId = $request->input('siteId');

    // Validate input
    $validated = $request->validate([
        'page_title' => 'required|string|max:255',
        'page_content' => 'required|string',
        'post_status' => 'required|string|in:draft,publish',
        'rank_math_focus_keyword' => 'nullable|string',
        'rank_math_description' => 'nullable|string|max:255',
    ]);

    try {
        $this->wordPressService->setDb($siteId);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to connect to the site: ' . $e->getMessage(),
        ], 400);
    }

    $pageContent = $validated['page_content'];
    $pageContent = preg_replace_callback('/<h1\b([^>]*)>/i', function ($matches) {
        $attributes = $matches[1];
        if (!preg_match('/text-align\s*:\s*center/i', $attributes)) {
            $attributes .= ' style="text-align: center;"';
        }
        return '<h1' . $attributes . '>';
    }, $pageContent, 1);

    $postData = [
        'post_title' => $validated['page_title'],
        'post_content' => '',
        'post_status' => $validated['post_status'],
        'post_type' => 'page',
        'post_author' => '2'
    ];

    try {
        $postId = $this->wordPressService->createPost($postData);

        $metaFields = [
            '_wp_page_template' => 'page-templates/right-sidebarpage.php',
            '_right_side_bar_services' => 'field_639c87c4a11f7',
            'right_side_bar_services' => $pageContent, // Use modified page content
           'right_side_bar_text_block_with_background_image_button_heading_tag' => 'h1',
            '_right_side_bar_text_block_with_background_image_button_heading_tag' => 'field_639c9ff6cb281',
            'right_side_bar_text_block_with_background_image_button_section_heading' => 'Get in Touch Today!',
            '_right_side_bar_text_block_with_background_image_button_section_heading' => 'field_639c9ff6cb282',
            'right_side_bar_text_block_with_background_image_button_section_content' => 'We want to hear from you about your {niche} needs. No {niche} problem in {city} is too big or too small for our experienced team! Call us or fill out our form today!',
            '_right_side_bar_text_block_with_background_image_button_section_content' => 'field_639c9ff6cb283',
            'right_side_bar_text_block_with_background_image_button_section_button' => '',
            '_right_side_bar_text_block_with_background_image_button_section_button' => 'field_639c9ff6cb284',
            'right_side_bar_text_block_with_background_image_button_section_button_1' => '',
            '_right_side_bar_text_block_with_background_image_button_section_button_1' => 'field_639c9ff6cb285',
            'right_side_bar_text_block_with_background_image_button_section_image' => '',
            'right_side_bar_text_block_with_background_image_button_section_image' => 'field_639c9ff6cb286',
            'right_side_bar_text_block_with_background_image_button' => '',
            '_right_side_bar_text_block_with_background_image_button' => 'field_639c9ff6cb280',
            'right_side_bar_services_1' => '',
            '_right_side_bar_services_1' => 'field_639c961da11fb',
            'right_side_bar' => '',
            '_right_side_bar' => 'field_639c975619425',
            '_edit_last' => 2
        ];

        foreach ($metaFields as $metaKey => $metaValue) {
            PostMeta::updateOrCreate([
                'post_id' => $postId,
                'meta_key' => $metaKey,
            ], [
                'meta_value' => $metaValue,
            ]);
        }

        $serviceTerm = Term::where(DB::raw('BINARY name'), 'Service' . "\r")
                ->first();
            $siteUrl = Option::get('siteurl');
            $this->addServiceToMenu($postId, $siteUrl); //adding service to Services menu.
            if ($serviceTerm) {

                // Add a row to the term_relationships table directly
                TermRelationship::insert([
                    'object_id' => $postId,
                    'term_taxonomy_id' => $serviceTerm->term_id
                ]);
            } else {
                \Log::error("Term 'Service' not found.");
            }
            
        // Add/update Rank Math SEO meta tags and description
        if ($request->filled('rank_math_focus_keyword')) {
            $this->wordPressService->updateRankMathMetaTags($postId, $validated['rank_math_focus_keyword']);
        }

        if ($request->filled('rank_math_description')) {
            $this->wordPressService->updateRankMathMetaDescription($postId, $validated['rank_math_description']);
        }

        return redirect()->back()->with('success', 'Service page created successfully.');
    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Failed to create Service page: ' . $e->getMessage());
    }
}

    public function getPosts(Request $request)
    {
        $siteId = $request->input('siteId');

        try {
            $this->wordPressService->setDb($siteId);

            // Retrieve posts

            $posts = Post::on('wordpress')
                ->type('post')
                ->status('publish')
                ->orderBy('post_date', 'desc')
                ->take(3)
                ->get();

            if ($posts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No posts found.'
                ], 404); // 404 Not Found
            }

            return response()->json([
                'success' => true,
                'data' => $posts
            ]);
        } catch (\Exception $e) {
            // If the connection fails, return an error message in JSON format
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to the site: ' . $e->getMessage(),
            ], 400); // 400 Bad Request
        }
    }

    public function getAllBlogPosts(Request $request)
    {
        $siteId = $request->input('siteId');

        try {
            $this->wordPressService->setDb($siteId);

            // Retrieve all posts with their meta data
            $posts = Post::on('wordpress')
                ->with('meta')
                ->type('post')
                ->status('publish')
                ->orderBy('post_date', 'desc')
                ->get();

            // Filter out posts that belong to the "Neighborhoods" category
            $filteredPosts = $posts->filter(function ($post) {
                $terms = $post->terms;
                $mainCategory = $post->main_category;
                $keywords = $post->keywords;

                $categoryFilter = isset($terms['category']) && isset($terms['category']['neighborhoods']) && $terms['category']['neighborhoods'] == 'Neighborhoods';
                $mainCategoryFilter = $mainCategory == 'Neighborhoods';
                $keywordsFilter = in_array('Neighborhoods', $keywords);

                return !$categoryFilter || !$mainCategoryFilter || !$keywordsFilter;
            })->map(function ($post) {
                // Include all necessary data for form population
                return [
                    'id' => $post->ID,
                    'post_title' => $post->post_title,
                    'post_content' => $post->post_content,
                    'post_status' => $post->post_status,
                    'categories' => $post->terms['category'] ?? [],
                    'rank_math_focus_keyword' => $post->meta->where('meta_key', 'rank_math_focus_keyword')->first()->meta_value ?? '',
                    'rank_math_description' => $post->meta->where('meta_key', 'rank_math_description')->first()->meta_value ?? '',
                    'terms' => $post->taxonomies ?? []
                ];
            });

            if ($filteredPosts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No posts found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $filteredPosts->values()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to the site: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function getCategories(Request $request)
    {
        $siteId = $request->input('siteId');

        try {
            $this->wordPressService->setDb($siteId);

            // Retrieve all categories
            $categories = Taxonomy::on('wordpress')
                ->where('taxonomy', 'category')
                ->get()
                ->map(function ($taxonomy) {
                    return [
                        'id' => $taxonomy->term->term_id,
                        'name' => $taxonomy->term->name,
                        'slug' => $taxonomy->term->slug,
                    ];
                });

            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No categories found.'
                ], 404); // 404 Not Found
            }

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            // If the connection fails, return an error message in JSON format
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to the site: ' . $e->getMessage(),
            ], 400); // 400 Bad Request
        }
    }

    public function createBlogPost(Request $request)
    {
        $siteId = $request->input('siteId');

        // Log the incoming request data
        \Log::info('Create Blog Post Request Data:', $request->all());

        // Validate input
        $validated = $request->validate([
            'post_title' => 'required|string|max:255',
            'post_content' => 'required|string',
            'post_category' => 'nullable|array',
            'post_status' => 'required|string|in:draft,publish',
            'new_category' => 'nullable|string|max:255',
            'rank_math_focus_keyword' => 'nullable|string',
            'rank_math_description' => 'nullable|string|max:255',
        ]);

        // Log the validated data
        \Log::info('Validated Data:', $validated);

        try {
            $post = $this->wordPressService->setDb($siteId);
            // Log successful connection
            \Log::info("Successfully connected to the site with siteId: $siteId");
        } catch (\Exception $e) {
            // Log connection failure
            \Log::error('Failed to connect to the site:', ['siteId' => $siteId, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to the site: ' . $e->getMessage(),
            ], 400); // 400 Bad Request
        }

        $postData = [
            'post_title' => $validated['post_title'],
            'post_content' => $validated['post_content'],
            'post_status' => $validated['post_status'],
            'post_type' => 'post',
            'post_author' => '2',
        ];

        try {
            // Create the new post
            $postId = $this->wordPressService->createPost($postData);

            // Initialize the categories array
            $categories = $validated['post_category'] ?? [];

            // Handle new category
            if ($request->filled('new_category')) {
                $newCategory = $this->wordPressService->createNewCategory($request->new_category);
                $categories[] = $newCategory->term_taxonomy_id; // Add the new category to the categories array
            }

            // Attach categories to the post
            if (!empty($categories)) {
                $this->wordPressService->attachCategories($postId, $categories);
            }

            // Add/update Rank Math SEO meta tags and description
            if ($request->filled('rank_math_focus_keyword')) {
                $this->wordPressService->updateRankMathMetaTags($postId, $validated['rank_math_focus_keyword']);
            }

            if ($request->filled('rank_math_description')) {
                $this->wordPressService->updateRankMathMetaDescription($postId, $validated['rank_math_description']);
            }

            // dd($request->all());
            return redirect()->back()->with([
                'success' => 'Blog post created successfully.',
                'post' => $post,
            ]);
        } catch (Exception $e) {
            // dd($request->all());
            // Log the failure
            \Log::error('Failed to create blog post:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create blog post: ' . $e->getMessage());
        }
    }

    public function updateServicePage(Request $request, $pageId)
    {
        $siteId = $request->input('siteId');
        $pageId = $pageId;

        // Validate input
        $validated = $request->validate([
            'page_title' => 'required|string|max:255',
            'page_content' => 'required|string',
            //'page_category' => 'nullable|string|max:255',
            'post_status' => 'required|string|in:draft,publish',
            'rank_math_focus_keyword' => 'nullable|string',
            'rank_math_description' => 'nullable|string|max:255',
        ]);

        try {
            $this->wordPressService->setDb($siteId);
        } catch (\Exception $e) {
            // Log connection failure
            \Log::error('Failed to connect to the site:', ['siteId' => $siteId, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to the site: ' . $e->getMessage(),
            ], 400); // 400 Bad Request
        }

        $postData = [
            'ID' => $pageId,
            'post_title' => $validated['page_title'],
            'post_content' => $validated['page_content'],
            'post_status' => $validated['post_status'],
            'post_type' => 'page',
            'post_author' => '2',
        ];

        try {
            $this->wordPressService->updatePost($postData);

            // Add/update Rank Math SEO meta tags and description
            if ($request->filled('rank_math_focus_keyword')) {
                $this->wordPressService->updateRankMathMetaTags($pageId, $validated['rank_math_focus_keyword']);
            }

            if ($request->filled('rank_math_description')) {
                $this->wordPressService->updateRankMathMetaDescription($pageId, $validated['rank_math_description']);
            }

            return redirect()->back()->with([
                'success' => 'Service page updated successfully.'
            ]);
        } catch (Exception $e) {
            \Log::error('Failed to update service page:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update service page: ' . $e->getMessage());
        }
    }

    public function updateBlogPost(Request $request, $postId)
    {
        $siteId = $request->input('siteId');
        $postId = $request->input('post_id');

        // Validate input
        $validated = $request->validate([
            'post_title' => 'required|string|max:255',
            'post_content' => 'required|string',
            'post_category' => 'nullable|array',
            'post_status' => 'required|string|in:draft,publish',
            'new_category' => 'nullable|string|max:255',
            'rank_math_focus_keyword' => 'nullable|string',
            'rank_math_description' => 'nullable|string|max:255',
        ]);

        try {
            $this->wordPressService->setDb($siteId);
        } catch (\Exception $e) {
            // Log connection failure
            \Log::error('Failed to connect to the site:', ['siteId' => $siteId, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to the site: ' . $e->getMessage(),
            ], 400); // 400 Bad Request
        }

        $postData = [
            'ID' => $postId,
            'post_title' => $validated['post_title'],
            'post_content' => $validated['post_content'],
            'post_status' => $validated['post_status'],
            'post_type' => 'post',
            'post_author' => '2',
        ];

        try {

            $this->wordPressService->updatePost($postData);

            // Initialize the categories array
            $categories = $validated['post_category'] ?? [];

            // Handle new category
            if ($request->filled('new_category')) {
                $newCategory = $this->wordPressService->createNewCategory($request->new_category);
                $categories[] = $newCategory->term_taxonomy_id; // Add the new category to the categories array
            }

            $this->wordPressService->setPostCategories($postId, $categories);


            // Add/update Rank Math SEO meta tags and description
            if ($request->filled('rank_math_focus_keyword')) {
                $this->wordPressService->updateRankMathMetaTags($postId, $validated['rank_math_focus_keyword']);
            }

            if ($request->filled('rank_math_description')) {
                $this->wordPressService->updateRankMathMetaDescription($postId, $validated['rank_math_description']);
            }

            return redirect()->back()->with([
                'success' => 'Blog post updated successfully.'
            ]);
        } catch (Exception $e) {
            \Log::error('Failed to update blog post:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update blog post: ' . $e->getMessage());
        }
    }

    public function getSitesByServer(Request $request)
    {
        $user = auth()->user();
        $server = $request->input('server');
        $restrictedSites = $user->restrictedSites;

        if ($restrictedSites->isEmpty()) {
            $sites = Site::where('server', $server)->orderBy('site_url', 'asc')->get(['id', 'site_url']);
        } else {
            $sites = $restrictedSites->where('server', $server)->sortBy('site_url')->values();
        }

        return response()->json($sites);
    }

    public function connectDb($siteId)
    {
        $response = $this->wordPressService->connectToDb($siteId);
        return $response;
    }

    public function addServiceToMenu($postId, $siteUrl)
    {
        $postData = [
            'post_title' => '',
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'nav_menu_item',
            'post_author' => '2',
            'guid' => $siteUrl,
        ];

        $postMenuId = $this->wordPressService->createMenuPost($postData);


        $theme = Option::where('option_name', 'template')->first();
        $themeData = '';
        if ($theme->option_value == 'understrap') {
            $themeData = Option::where('option_name', 'theme_mods_understrap')->first();
        } else if ($theme->option_value == 'Divi') {
            $themeData = Option::where('option_name', 'theme_mods_Divi-Child')->first();
        }
        $themeData = unserialize($themeData->option_value);
        if (key_exists('primary', $themeData['nav_menu_locations'])) {
            $primaryMenu = $themeData['nav_menu_locations']['primary'];
            $servicesMenu = Term::whereHas('taxonomy', function ($query) use ($primaryMenu) {
                $query->where('taxonomy', 'nav_menu')
                    ->where('term_id', $primaryMenu)
                    ->whereHas('posts', function ($query) {
                        $query->where('post_title', 'Services');
                    });
            })->with(['taxonomy.posts' => function ($query) {
                $query->where('post_title', 'Services');
            }])->first();

            $servicesMenuId = $servicesMenu->taxonomy->posts->first()->ID;
            $metaFields = [
                '_menu_item_type' => 'post_type',
                '_menu_item_menu_item_parent' => $servicesMenuId,
                '_menu_item_object_id' => $postId,
                '_menu_item_object' => 'page',
                '_menu_item_target' => '',
                '_menu_item_classes' => '',
                '_menu_item_xfn' => '',
                '_menu_item_url' => '',
            ];

            foreach ($metaFields as $metaKey => $metaValue) {
                PostMeta::updateOrCreate([
                    'post_id' => $postMenuId,
                    'meta_key' => $metaKey,
                ], [
                    'meta_value' => $metaValue,
                ]);
            }
            $termRelationship = new TermRelationship();
            $termRelationship->object_id = $postMenuId;
            $termRelationship->term_taxonomy_id = $primaryMenu;
            $termRelationship->term_order = 0;
            $termRelationship->save();
        }
    }
}
