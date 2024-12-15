<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use App\Models\Neighborhood;
use App\Models\Site;
use App\Models\WpApiDetail;
use App\Models\Post;
use App\Models\NicheContent;

use App\Services\WordPressService;
use App\Services\WordPressApiService;
use Corcel\Model\Meta\PostMeta;
use Corcel\Model\Option;
use Corcel\Model\TermRelationship;
use DOMDocument;
use DOMXPath;
// use GeminiAPI\Laravel\Facades\Gemini;
use Gemini\Laravel\Facades\Gemini;
use App\Services\ContentGenerationService;

class NeighborhoodController extends Controller
{
    protected $wordPressService;
    protected $contentGenerationService;

    public function __construct(WordpressService $wordPressService, ContentGenerationService $contentGenerationService)
    {
        $this->wordPressService = $wordPressService;
        $this->contentGenerationService = $contentGenerationService;
    }

    private function getServersAndSites($niche = null)
    {
        $user = auth()->user();
        $restrictedSites = $user->restrictedSites;

        if ($restrictedSites->isEmpty()) {
            // User can access all sites
            $query = Site::query();
        } else {
            // User is restricted, so only retrieve allowed sites
            $query = Site::whereIn('id', $restrictedSites->pluck('id'));
        }

        // Apply niche filtering if provided
        if ($niche) {
            if (strcasecmp($niche, 'Unspecified') === 0) {
                $query->where(function ($q) {
                    $q->whereNull('niche')
                        ->orWhere('niche', '')
                        ->orWhereRaw('LOWER(TRIM(niche)) = ?', ['unspecified']);
                });
            } else {
                $query->whereRaw('LOWER(TRIM(niche)) = ?', [strtolower($niche)]);
            }
        }

        // Get servers and sites based on the filtered query
        $servers = $query->pluck('server')->unique();
        $sites = $query->orderBy('site_url', 'asc')->get(['id', 'site_url']);

        return compact('servers', 'sites');
    }

    public function index()
    {
        // Get servers and sites based on user's restrictions
        $data = $this->getServersAndSites();

        // Fetch all unique niches from the database
        $niches = NicheContent::select('niche')
            ->whereNotNull('services_content')
            ->where('services_content', '<>', '')
            ->whereNotNull('choose_us_content')
            ->where('choose_us_content', '<>', '')
            ->whereNotNull('contact_us_content')
            ->where('contact_us_content', '<>', '')
            ->get();

        // Fetch all unique niches from the database
        $siteNiches = Site::select('niche')
            ->whereNotNull('niche')
            ->where('niche', '!=', '')
            ->orderBy('niche', 'asc')
            ->groupBy('niche')
            ->get();


        $breadcrumbs = [
            ['name' => 'Creation', 'url' => route('creation.index')],
            ['name' => 'Neighborhoods', 'url' => null] // Current page
        ];

        // Merge the niches into the data array and return the view
        return view('creation.neighborhoods.index', array_merge($data, [
            'niches' => $niches,
            'siteNiches' => $siteNiches,
            'breadcrumbs' => $breadcrumbs
        ]));
    }

    public function createNeighborhood(Request $request)
    {
        $validated = $request->validate([
            'post_title' => 'required|string|max:255',
            'post_content' => 'required|string',
            'post_status' => 'required|string|in:draft,publish',
            'text.*' => 'nullable|string',
            // 'custom_image_id.*' => 'nullable|image|max:1024', //change to correct id
            'map_text.*' => 'nullable|string',
            'weather' => 'nullable|string',
            'keywords.*' => 'nullable|string',
            'thing_text.*' => 'nullable|string',
            'things_image_id.*' => 'nullable|image|max:1024',
            'custom_repeater_field' => 'nullable|string',
            'custom_things_field' => 'nullable|string'
        ]);

        $siteId = $request->input('siteId');

        $wpSite = Site::findOrFail($siteId);

        // Fetch the API details using the site_url and server from the wpSite
        $apiDetails = WpApiDetail::where('site_url', $wpSite->site_url)
            ->where('server', $wpSite->server)
            ->first();
        // Custom field data
        try {
            if ($this->wordPressService->connectToDb($siteId)) {
                $postData = [
                    'post_title' => $validated['post_title'],
                    'post_content' => $validated['post_content'],
                    'post_status' => $validated['post_status'],
                    'post_type' => 'neighborhoods',
                    'post_author' => 2
                ];
                $postId = $this->wordPressService->createPost($postData);

                // Save custom field data
                if (isset($validated['custom_repeater_field'])) {
                    $customRepeaterField = $request->input('custom_repeater_field');
                    $repeaterData = json_decode($customRepeaterField, true);
                    // \Log::info('Decoded repeater data: ', ['repeater_data' => $repeaterData]);

                    // Serialize the data and save it as post meta
                    PostMeta::updateOrCreate(
                        ['post_id' => $postId, 'meta_key' => 'custom_repeater_data'],
                        ['meta_value' => serialize($repeaterData)]
                    );
                }

                // Save custom things repeater data
                if (isset($validated['custom_things_field'])) {
                    $customThingsField = $validated['custom_things_field'];
                    $thingsData = json_decode($customThingsField, true);
                    // \Log::info('Decoded things data: ', ['custom_things_data' => $thingsData]);

                    PostMeta::updateOrCreate(
                        ['post_id' => $postId, 'meta_key' => 'custom_things_data'],
                        ['meta_value' => serialize($thingsData)]
                    );
                }

                if (isset($validated['thing_text'])  && isset($validated['things_image_id'])) {
                    $repeater_data = [];
                    $count = count($validated['thing_text']);

                    for ($i = 0; $i < $count; $i++) {
                        $image = $validated['things_image'][$i];
                        $upload = $this->uploadImageToWordPress($image, $apiDetails);

                        $repeater_data[] = array(
                            'thing_text' => $validated['thing_text'][$i],
                            'things_image_id' => $upload['id'],
                        );
                    }

                    PostMeta::updateOrCreate(
                        ['post_id' => $postId, 'meta_key' => 'custom_things_data'],
                        ['meta_value' => serialize($repeater_data)]
                    );
                }

                if (isset($validated['map_text'])) {
                    $repeater_data = [];
                    $count = count($validated['map_text']);
                    for ($i = 0; $i < $count; $i++) {
                        $repeater_data[] = array(
                            'map_text' => $validated['map_text'][$i],
                        );
                    }
                    PostMeta::updateOrCreate(
                        ['post_id' => $postId, 'meta_key' => 'map_repeater_data'],
                        ['meta_value' => serialize($repeater_data)]
                    );
                }

                if (isset($validated['weather'])) {
                    PostMeta::updateOrCreate(
                        ['post_id' => $postId, 'meta_key' => 'weather_code'],
                        ['meta_value' => $validated['weather']]
                    );
                }

                if (isset($validated['keywords'])) {
                    $repeater_data = [];
                    $count = count($validated['keywords']);
                    for ($i = 0; $i < $count; $i++) {
                        $repeater_data[] = array(
                            'keywords' => $validated['keywords'][$i],
                        );
                    }
                    PostMeta::updateOrCreate(
                        ['post_id' => $postId, 'meta_key' => 'keywords_repeater_data'],
                        ['meta_value' => serialize($repeater_data)]
                    );
                }

                // dd($result);

                return redirect()->back()->with('success', 'Neighborhood post created successfully.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to create neighborhood post: ' . $e->getMessage());
        }
    }

    public function getPoiOptions(Request $request)
    {
        $siteId = $request->input('siteId');
        // Attempt to connect to the database using the siteId
        try {
            $this->wordPressService->connectToDb($siteId);
        } catch (\Exception $e) {
            // If the connection fails, return an error message in JSON format
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to the site: ' . $e->getMessage(),
            ], 400); // 400 Bad Request
        }

        // Fetch the options
        $siteUrl = Option::where('option_name', 'siteurl')
            ->first()
            ->option_value ?? 'WordPress Url';

        $googlePoiHeading = Option::where('option_name', 'google_poi_heading')
            ->first()
            ->option_value ?? '';

        $googlePois = Option::where('option_name', 'bpd_google_poi')
            ->first()
            ->option_value ?? '';

        // Return the data as a JSON response
        return response()->json([
            'success' => true,
            'googlePoiHeading' => $googlePoiHeading,
            'googlePois' => $googlePois,
            'siteUrl' => $siteUrl
        ]);
    }


    public function updatePoiOptions(Request $request)
    {
        $siteId = $request->input('siteId');

        try {
            if ($this->wordPressService->connectToDb($siteId)) {


                $validated = $request->validate([
                    'google_poi_heading' => 'required|string|max:255',
                    'google_pois.*' => 'nullable|string',
                ]);

                Option::updateOrCreate(
                    ['option_name' => 'google_poi_heading'],
                    ['option_value' => $validated['google_poi_heading']]
                );

                if (isset($validated['google_pois'])) {
                    $pois = implode(':', $validated['google_pois']); // Join all POIs with semicolons
                    Option::updateOrCreate(
                        ['option_name' => 'bpd_google_poi'],
                        ['option_value' => $pois]
                    );
                }

                return redirect()->back()->with('success', 'Options updated successfully.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update options: ' . $e->getMessage());
        }
    }


    public function uploadFile(Request $request)
    {

        // Adjusted to match the key used in the request
        $request->validate([
            'file' => 'required|url',
            'siteId' => 'required|exists:wordpress_sites,id',
            'title' => 'nullable|string|max:255',
            'alt' => 'nullable|string'
        ]);

        $siteId = $request->input('siteId');
        $fileUrl = $request->input('file');
        $title = $request->input('title');
        $alt = $request->input('alt');

        $wpSite = Site::findOrFail($siteId);

        $wpDetails = WpApiDetail::where('site_url', $wpSite->site_url)
            ->where('server', $wpSite->server)
            ->firstOrFail();

        // Decrypt username and application password
        $username = decrypt($wpDetails->username);
        $password = decrypt($wpDetails->application_password);

        // Validate and parse the URL
        if (!filter_var($fileUrl, FILTER_VALIDATE_URL)) {
            \Log::error('Invalid URL provided', ['url' => $fileUrl]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid URL provided'
            ]);
        }

        try {
            // Use Guzzle to download the file
            $client = new Client();
            $response = $client->get($fileUrl);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to download file from URL');
            }

            $fileContents = $response->getBody()->getContents();
            $fileName = basename(parse_url($fileUrl, PHP_URL_PATH));
            $tempFilePath = sys_get_temp_dir() . '/' . $fileName;

            // Save the file temporarily
            $bytesWritten = file_put_contents($tempFilePath, $fileContents);
            if ($bytesWritten === false) {
                throw new \Exception('Failed to write file to temporary path');
            }
        } catch (\Exception $e) {
            \Log::error('Error processing file from URL', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error processing file from URL: ' . $e->getMessage()
            ]);
        }

        // Upload file to WordPress using REST API
        $url = $wpDetails->site_url . '/wp-json/wp/v2/media';
        $fileType = mime_content_type($tempFilePath);

        $response = Http::withBasicAuth($username, $password)
            ->attach('file', file_get_contents($tempFilePath), $fileName)
            ->post($url, [
                'title' => $title,
                'alt_text' => $alt,
            ]);

        unlink($tempFilePath);

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'data' => $response->json()
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file to WordPress'
            ]);
        }
    }


    public function getStaticContentByNiche($niche)
    {
        return NicheContent::where('niche', $niche)
            ->whereNotNull('services_content')
            ->whereNotNull('choose_us_content')
            ->whereNotNull('contact_us_content')
            ->first();
    }


    public function createNeighborhoodPosts(Request $request)
    {

        $siteId = $request->input('siteListDropdown');
        $niche = $request->input('niche');
        $postType = 'page';
        $postAuthor = "2"; // default admin account
        $neighborhoods = json_decode($request->input('neighborhoods_list'), true); // Decode the JSON data
        // $directions = $request->input('directions_list');
        $iframe = json_decode($request->input('hidden_iframe'), true);

        $category = "Neighborhoods";
        $categoryId = '';
        $postStatus = 'publish';

        $staticContentResponse = $this->getStaticContentByNiche($niche);

        // Ensure we have a valid response before accessing properties
        if ($staticContentResponse) {
            $contactSection = $staticContentResponse->contact_us_content;
            $servicesSection = $staticContentResponse->services_content;
            $chooseUsSection = $staticContentResponse->choose_us_content;
        } else {
            // Handle the case where no static content is found
            return response()->json([
                'success' => false,
                'message' => 'No static content found for the selected niche.'
            ], 404);
        }

        try {
            $post = $this->wordPressService->setDb($siteId);
            // \Log::info("Successfully connected to the site with siteId: $siteId");
        } catch (\Exception $e) {
            // Log connection failure
            // \Log::error('Failed to connect to the site:', ['siteId' => $siteId, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to the site: ' . $e->getMessage(),
            ], 400); // 400 Bad Request
        }

        // Handle new category
        if ($category) {

            // Check if the category already exists
            $existingCategory = $this->wordPressService->getCategoryByName($category);

            if ($existingCategory) {
                // Use existing category ID
                $categoryId = $existingCategory->term_taxonomy_id;
            } else {
                // Create new category
                $newCategory = $this->wordPressService->createNewCategory($category);
                $categoryId = $newCategory->term_taxonomy_id;
            }
        }

        $doc = new DOMDocument();
        libxml_use_internal_errors(true); // to handle any HTML5 errors
        $doc->loadHTML($contactSection, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($doc);

        // Find the <h2> element with the text "Contact Us"
        $nodes = $xpath->query("//h2[contains(text(), 'Contact Us')]");
        $afterText = '';
        foreach ($nodes as $node) {
            // Split the text node and create a new <a> element for the "Contact Us" part
            $text = $node->textContent;
            $linkText = 'Contact Us';

            // Find the position of the link text
            $startPos = strpos($text, $linkText);
            $endPos = $startPos + strlen($linkText);

            // Create text nodes for the parts before and after the link text
            $beforeText = substr($text, 0, $startPos);
            $afterText = substr($text, $endPos);

            // Clear the existing text in the <h2> element
            $node->nodeValue = '';

            // Create and append the text node for the part before the link text
            if ($beforeText !== '') {
                $beforeNode = $doc->createTextNode($beforeText);
                $node->appendChild($beforeNode);
            }

            // Create and append the <a> element for the link text
            $a = $doc->createElement('a', $linkText);
            $a->setAttribute('href', '/contact-us');
            $a->setAttribute('style', 'text-decoration: underline;');
            $node->appendChild($a);
        }


        $contactSection = $doc->saveHTML();

        foreach ($neighborhoods as $index => $neighborhood) {
            $postTitle = $request->input("post-title-{$index}");
            $postTitle = preg_replace('/^[^a-zA-Z]+/', '', $postTitle);
            $uniqueContent = $request->input("hidden-quill-{$index}");
            // Combine all sections
            $postContent = "

                <br>
                <div class='unique-content'>
                    $uniqueContent
                </div>";
            // if ($index == 0) {
            $postContent .= "
                <br>
                <div class='services-content'>
                    $servicesSection
                </div>
                <br>
                <div class='choose-us-content'>
                    $chooseUsSection
                </div>
                <br>";
            // }

            // $postContent .= "
            //     <h2 class='pt-3'>About</h2>
            //     <br>
            //     <div class='unique-content'>
            //         $uniqueContent
            //     </div>";


            // if ($directions) {
            //     $postContent .= "
            //     <div class='directions-content'>
            //         <h2>Directions to places</h2>
            //         $directions
            //     </div>";
            // }

            $postContent .= "<div class='contact-us-content'>" .
                $contactSection . "<p>$afterText</p>
                </div>
                <br>";

            // $result = $this->contentGenerationService->generateContent($prompt);
            // if (empty($result['error'])) {
            //     $embeded_link = trim($result['content']);
            // } else {
            //     $embeded_link = '';
            // }

            if ($iframe && isset($iframe[0])) {
                $postContent .= "$iframe[0]<br>";
            }
            $postData = [
                'post_title' => $postTitle,
                // 'post_title' => 'Neightborhood Guide',
                'post_content' => $postContent,
                'post_status' => $postStatus,
                'post_type' => $postType,
                'post_author' => $postAuthor
            ];
            try {
                // Create the new post
                $postId = $this->wordPressService->createPost($postData);

                // Attach categories to the post
                if ($categoryId) {
                    $this->wordPressService->attachCategories($postId, $categoryId);
                }

                $metaFields = [
                    '_wp_page_template' => 'page-templates/right-sidebarpage.php',
                    '_right_side_bar_services' => 'field_639c87c4a11f7',
                    'right_side_bar_services' => $postContent,
                ];

                // Add meta fields using PostMeta
                foreach ($metaFields as $metaKey => $metaValue) {
                    PostMeta::create([
                        'post_id' => $postId,
                        'meta_key' => $metaKey,
                        'meta_value' => $metaValue
                    ]);
                }
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Failed to create Neighborhood posts: ' . $e->getMessage());
            }
            $siteUrl = Option::get('siteurl');
            $this->add_neighborhood_to_menu($postId, $siteUrl);
        }
        return redirect()->back()->with('success', 'Neighborhood posts created successfully.');
    }

    public function add_neighborhood_to_menu($postId, $siteUrl)
    {
        $menu = Post::where('post_title', 'Areas We Service')->first();
        $theme = Option::where('option_name', 'template')->first();
        $themeData = '';
        if ($theme->option_value == 'understrap') {
            $themeData = Option::where('option_name', 'theme_mods_understrap')->first();
        } else if ($theme->option_value == 'Divi') {
            $themeData = Option::where('option_name', 'theme_mods_Divi-Child')->first();
        }

        $themeData = unserialize($themeData->option_value);
        $primaryMenu = $themeData['nav_menu_locations']['primary'];

        $parentMenu = 0;
        if ($menu) {

            $parentMenu = $menu->ID;
        } else {
            $postData = [
                'post_title' => 'Areas We Service',
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'nav_menu_item',
                'post_author' => '2',
                'guid' => $siteUrl,
            ];

            $parentMenu = $this->wordPressService->createMenuPost($postData);
            $metaFields = [
                '_menu_item_type' => 'custom',
                '_menu_item_menu_item_parent' => 0,
                '_menu_item_object_id' => $parentMenu,
                '_menu_item_object' => 'custom',
                '_menu_item_target' => '',
                '_menu_item_classes' => '',
                '_menu_item_xfn' => '',
                '_menu_item_url' => '#',
            ];

            foreach ($metaFields as $metaKey => $metaValue) {
                PostMeta::updateOrCreate([
                    'post_id' => $parentMenu,
                    'meta_key' => $metaKey,
                ], [
                    'meta_value' => $metaValue,
                ]);
            }
            $termRelationship = new TermRelationship();
            $termRelationship->object_id = $parentMenu;
            $termRelationship->term_taxonomy_id = $primaryMenu;
            $termRelationship->term_order = 0;
            $termRelationship->save();
        }

        $postData = [
            'post_title' => '',
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'nav_menu_item',
            'post_author' => '2',
            'guid' => $siteUrl,
        ];
        $postMenu = $this->wordPressService->createMenuPost($postData);

        $metaFields = [
            '_menu_item_type' => 'post_type',
            '_menu_item_menu_item_parent' => $parentMenu,
            '_menu_item_object_id' => $postId,
            '_menu_item_object' => 'post',
            '_menu_item_target' => '',
            '_menu_item_classes' => '',
            '_menu_item_xfn' => '',
            '_menu_item_url' => '',
        ];

        foreach ($metaFields as $metaKey => $metaValue) {
            PostMeta::updateOrCreate([
                'post_id' => $postMenu,
                'meta_key' => $metaKey,
            ], [
                'meta_value' => $metaValue,
            ]);
        }

        $termRelationship = new TermRelationship();
        $termRelationship->object_id = $postMenu;
        $termRelationship->term_taxonomy_id = $primaryMenu;
        $termRelationship->term_order = 0;
        $termRelationship->save();
    }
}
