<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Corcel\Model\Term;
use Corcel\Model\Taxonomy;
use Corcel\Model\Option;
use Corcel\Model\Attachment;
use Corcel\Model\Meta\PostMeta;

use App\Models\Site;
use App\Modles\Server;
use App\Models\Post;
use Exception;

class WordPressService
{

    public function getServers()
    {
        return Site::select('server')->distinct()->orderBy('server', 'asc')->pluck('server');
    }

    public function getSitesByServer($server)
    {
        return Site::where('server', $server)->get();
    }

    public function getSiteById($siteId)
    {
        return Site::with(['server', 'wpApiDetails'])->find($siteId);
    }

    public function setDb($siteId)
    {
        $site = $this->getSiteById($siteId);

        if (!$site) {
            throw new Exception('Site not found');
        }

        if ($site->server() == '') {
            throw new Exception('Server information is missing for this site');
        }

        // Decrypt configs
        $decryptedDbName = decrypt($site->db_name);
        $decryptedDbUsername = decrypt($site->db_username);
        $decryptedDbPassword = decrypt($site->db_password);
        $decryptedWpPrefix = decrypt($site->wp_prefix);

        Config::set('database.connections.wordpress.database', $decryptedDbName);
        Config::set('database.connections.wordpress.username', $decryptedDbUsername);
        Config::set('database.connections.wordpress.password', $decryptedDbPassword);
        Config::set('database.connections.wordpress.prefix', $decryptedWpPrefix);
        Config::set('database.connections.wordpress.host', $site->server()->first()->ip_address);

        // Reconnect with new settings
        DB::purge('wordpress');
        DB::reconnect('wordpress');
        DB::setDefaultConnection('wordpress');
        // Test the connection
        try {
            DB::connection('wordpress')->getPdo();
            return true;
        } catch (\Exception $e) {
            throw new Exception('Could not connect to the WordPress database: ' . $e->getMessage());
        }
    }

    public function connectToDb($siteId)
    {
        $site = $this->getSiteById($siteId);

        if (!$site) {
            throw new Exception('Site not found');
        }

        if ($site->server() == '') {
            throw new Exception('Server information is missing for this site');
        }

        // Decrypt configs
        $decryptedDbName = decrypt($site->db_name);
        $decryptedDbUsername = decrypt($site->db_username);
        $decryptedDbPassword = decrypt($site->db_password);
        $decryptedWpPrefix = decrypt($site->wp_prefix);

        Config::set('database.connections.wordpress.database', $decryptedDbName);
        Config::set('database.connections.wordpress.username', $decryptedDbUsername);
        Config::set('database.connections.wordpress.password', $decryptedDbPassword);
        Config::set('database.connections.wordpress.prefix', $decryptedWpPrefix);
        Config::set('database.connections.wordpress.host', $site->server()->first()->ip_address);

        // Reconnect with new settings
        DB::purge('wordpress');
        DB::reconnect('wordpress');
        DB::setDefaultConnection('wordpress');

        // Test connection
        try {
            $posts = Post::on('wordpress')
                ->status('publish')
                ->orderBy('post_date', 'desc')
                ->take(5)
                ->get();

            $formattedPosts = $posts->map(function ($post) {
                return ['title' => $post->post_title];
            });

            return response()->json([
                'success' => true,
                'message' => 'Connection successful!',
                'data' => $formattedPosts
            ]);
            // DB::connection('wordpress')->getPdo();
            // dump(DB::connection('wordpress')->getPdo());
            // return true;
        } catch (\Exception $e) {
            throw new Exception('Failed to connect to WordPress database');
        }
    }


    public function connectToWp($siteId)
    {
        $site = $this->getSiteById($siteId);

        if (!$site) {
            throw new Exception('Site not found');
        }

        if ($site->server() == '') {
            throw new Exception('Server information is missing for this site');
        }

        // Decrypt configs
        $decryptedDbName = decrypt($site->db_name);
        $decryptedDbUsername = decrypt($site->db_username);
        $decryptedDbPassword = decrypt($site->db_password);
        $decryptedWpPrefix = decrypt($site->wp_prefix);

        $dbHost = $site->server()->first()->ip_address;

        // Dynamically create a new connection
        $connectionName = 'site_' . $siteId;
        Config::set('database.connections.' . $connectionName, [
            'driver' => 'mysql',
            'host' => $dbHost,
            'database' => $decryptedDbName,
            'username' => $decryptedDbUsername,
            'password' => $decryptedDbPassword,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => $decryptedWpPrefix,
            'strict' => false,
            'engine' => null,
        ]);

        try {
            DB::connection($connectionName)->getPdo();
            // \Log::info('Connection successful!', [
            //     'site_id' => $siteId,
            //     'db_name' => $decryptedDbName
            // ]);
        } catch (\Exception $e) {
            throw new Exception('Failed to connect to WordPress database: ' . $e->getMessage());
        }

        return $connectionName;
    }

    // For bulk posts
    public function createWpPost(array $postData, string $connectionName)
    {
        try {
            $slug = Str::slug($postData['post_title']);  // Ensures the title is converted to a slug format.
            $post = [
                'post_title' => $postData['post_title'],
                'post_content' => $postData['post_content'],
                'post_excerpt' => '',
                'post_status' => $postData['post_status'],
                'post_type' => $postData['post_type'],
                'to_ping' => '',
                'pinged' => '',
                'post_content_filtered' => '',
                'post_name' => $slug,
                'post_author' => $postData['post_author']
            ];

            // Ensure the Corcel model uses the correct connection
            $result = Post::on($connectionName)->create($post);
            return $result->ID;
        } catch (\Exception $e) {
            throw new Exception('Failed to create post: ' . $e->getMessage());
        }
    }


    public function getPosts()
    {
        try {
            $posts = Post::on('wordpress')->status('publish')->get();
            $formattedPosts = $posts->map(function ($post) {
                return ['title' => $post->post_title];
            });

            return response()->json([
                'success' => true,
                'message' => 'Connection successful!',
                'data' => $posts
            ]);
            // DB::connection('wordpress')->getPdo();
            // dump(DB::connection('wordpress')->getPdo());
            // return true;
        } catch (\Exception $e) {
            throw new Exception('Failed to connect to WordPress database');
        }
    }

    public function createPost($postData)
    {
        try {
            $slug = Str::slug($postData['post_title']);  // Ensures the title is converted to a slug format.
            $post = [
                'post_title' => $postData['post_title'],
                'post_content' => htmlspecialchars_decode($postData['post_content']),
                'post_excerpt' => '',
                'post_status' => $postData['post_status'],
                'post_type' => $postData['post_type'],
                'to_ping' => '',
                'pinged' => '',
                'post_content_filtered' => '',
                'post_name' => $slug,
                'post_author' => $postData['post_author']
            ];

            $result = Post::create($post);
            return $result->ID;
        } catch (\Exception $e) {
            throw new Exception('Failed to create post: ' . $e->getMessage());
        }
    }

    public function updatePost($postData)
{
    try {
        $post = Post::find($postData['ID']);

        if (!$post) {
            throw new Exception('Post not found');
        }

        $slug = Str::slug($postData['post_title']);

        $updatedData = [
            'post_title' => $postData['post_title'],
            'post_content' => $postData['post_content'],
            'post_status' => $postData['post_status'],
            'post_name' => $slug,
            'post_excerpt' => '',
            'to_ping' => '',
            'pinged' => '',
            'post_content_filtered' => '',
            'post_name' => $slug,
            'post_author' => $postData['post_author']
        ];

        $post->update($updatedData);

        return $post->ID;
    } catch (\Exception $e) {
        throw new Exception('Failed to update post: ' . $e->getMessage());
    }
}

public function updateOrCreateMeta($postId, $key, $value)
{
    PostMeta::updateOrCreate(
        [
            'post_id' => $postId,
            'meta_key' => $key,
        ],
        ['meta_value' => $value]
    );
}

    public function createNewCategory($categoryName)
    {
        // Create the Term
        $term = new Term();
        $term->name = $categoryName;
        $term->slug = Str::slug($categoryName);
        $term->save();

        // Create the TermTaxonomy
        $taxonomy = new Taxonomy();
        $taxonomy->term_id = $term->term_id;
        $taxonomy->taxonomy = 'category';
        $taxonomy->description = '';
        $taxonomy->parent = 0;
        $taxonomy->count = 0;
        $taxonomy->save();

        return $taxonomy;
    }

    public function attachCategories($postId, $categoryIds)
    {
        $post = Post::find($postId);
        $post->taxonomies()->attach($categoryIds);
    }

    public function checkAndAttachCategory($postId, $categoryName, $connectionName)
    {
        try {
            $category = $this->getCategoryByNameWithConn($categoryName, $connectionName);

            if (!$category) {
                $category = $this->createNewCategoryWithConn($categoryName, $connectionName);
            }

            $this->attachCategoryWithConn($postId, [$category->term_id], $connectionName);
        } catch (\Exception $e) {
            Log::error('Failed to add category', ['message' => $e->getMessage()]);
            throw new \Exception('Failed to add category: ' . $e->getMessage());
        }
    }

    public function setPostCategories($postId, $categoryIds)
{
    try {
        $post = Post::find($postId);
        if (!$post) {
            throw new Exception('Post not found');
        }

        // Sync categories
        $post->taxonomies()->sync($categoryIds, ['taxonomy' => 'category']);

        return true;
    } catch (\Exception $e) {
        \Log::error('Failed to set post categories: ' . $e->getMessage());
        throw new Exception('Failed to set post categories: ' . $e->getMessage());
    }
}


public function createNewCategoryWithConn($categoryName, $connectionName)
{
    try {
        // Create the Term
        $term = new Term();
        $term->setConnection($connectionName);
        $term->name = $categoryName;
        $term->slug = Str::slug($categoryName);
        $term->save();

        // Create the TermTaxonomy
        $taxonomy = new Taxonomy();
        $taxonomy->setConnection($connectionName);
        $taxonomy->term_id = $term->term_id;
        $taxonomy->taxonomy = 'category';
        $taxonomy->description = '';
        $taxonomy->parent = 0;
        $taxonomy->count = 0;
        $taxonomy->save();

        return $taxonomy;
    } catch (\Exception $e) {
        Log::error('Failed to create new category', ['message' => $e->getMessage()]);
        throw new \Exception('Failed to create new category: ' . $e->getMessage());
    }
}

public function getCategoryByNameWithConn($categoryName, $connectionName)
{
    return Taxonomy::on($connectionName)->whereHas('term', function ($query) use ($categoryName) {
        $query->where('name', $categoryName);
    })->where('taxonomy', 'category')->first();
}

public function attachCategoryWithConn($postId, $categoryIds, $connectionName)
{
    try {
        $post = Post::on($connectionName)->find($postId);
        $post->taxonomies()->attach($categoryIds);
    } catch (\Exception $e) {
        Log::error('Failed to attach category to post', ['postId' => $postId, 'categoryIds' => $categoryIds, 'message' => $e->getMessage()]);
        throw new \Exception('Failed to attach category to post: ' . $e->getMessage());
    }
}

    public function getCategoryByName($categoryName)
    {
        return Taxonomy::whereHas('term', function ($query) use ($categoryName) {
            $query->where('name', $categoryName);
        })->where('taxonomy', 'category')->first();
    }


    public function updatePostMeta($postId, $metaKey, $metaValue)
    {
        try {
            PostMeta::updateOrCreate(
                ['post_id' => $postId, 'meta_key' => $metaKey],
                ['meta_value' => $metaValue]
            );
        } catch (\Exception $e) {
            throw new \Exception('Failed to update post meta: ' . $metaKey . ' - ' . $e->getMessage());
        }
    }

    public function updateRankMathMetaDescription($postId, $metaDescription)
    {
        $this->updatePostMeta($postId, 'rank_math_description', $metaDescription);
    }

    public function updateRankMathMetaTags($postId, $metaTags)
    {
        // Sanitize and reformat the tags
        $tagsArray = explode(',', $metaTags);
        $trimmedTags = array_map('trim', $tagsArray); // Trim each tag
        $sanitizedTags = implode(',', $trimmedTags); // Join the tags with commas, no extra spaces

        $this->updatePostMeta($postId, 'rank_math_focus_keyword', $sanitizedTags);
    }


public function updatePostMetaWithConn($postId, $metaKey, $metaValue, $connectionName)
{
    try {
        PostMeta::on($connectionName)->updateOrCreate(
            ['post_id' => $postId, 'meta_key' => $metaKey],
            ['meta_value' => $metaValue]
        );
    } catch (\Exception $e) {
        throw new \Exception('Failed to update post meta: ' . $metaKey . ' - ' . $e->getMessage());
    }
}

public function updateRankMathMetaDescriptionWithConn($postId, $metaDescription, $connectionName)
{
    $this->updatePostMetaWithConn($postId, 'rank_math_description', $metaDescription, $connectionName);
}

public function updateRankMathMetaTagsWithConn($postId, $metaTags, $connectionName)
{
    // Sanitize and reformat the tags
    $tagsArray = explode(',', $metaTags);
    $trimmedTags = array_map('trim', $tagsArray); // Trim each tag
    $sanitizedTags = implode(',', $trimmedTags); // Join the tags with commas, no extra spaces

    $this->updatePostMetaWithConn($postId, 'rank_math_focus_keyword', $sanitizedTags, $connectionName);
}

    public function deletePost($postId)
    {
        // Delete a post using Corcel
        // ...
    }

    public function createMenuPost($postData)
    {
        try {
            if ($postData['post_title'] == '') {
                $post = [
                    'post_title' => '',
                    'post_content' => '',
                    'post_excerpt' => '',
                    'post_status' => $postData['post_status'],
                    'post_type' => $postData['post_type'],
                    'to_ping' => '',
                    'pinged' => '',
                    'post_content_filtered' => '',
                    'post_name' => '',
                    'post_author' => $postData['post_author'],
                    'guid' => $postData['guid']
                ];
                $result = Post::create($post);
                $result->update([
                    'guid' => $postData['guid'] . '?' . $result->ID,
                    'post_name' => $result->ID,
                ]);
            } else {
                $slug = Str::slug($postData['post_title']);  // Ensures the title is converted to a slug format.
                $post = [
                    'post_title' => $postData['post_title'],
                    'post_content' => '',
                    'post_excerpt' => '',
                    'post_status' => $postData['post_status'],
                    'post_type' => $postData['post_type'],
                    'to_ping' => '',
                    'pinged' => '',
                    'post_content_filtered' => '',
                    'post_name' => $slug,
                    'post_author' => $postData['post_author']
                ];

                $result = Post::create($post);
                $result->update([
                    'guid' => $postData['guid'] . '?' . $result->ID,
                    'post_name' => $result->ID,
                ]);
            }

            return $result->ID;
        } catch (\Exception $e) {
            throw new Exception('Failed to create post: ' . $e->getMessage());
        }
    }

    public function fetchPost($id)
{
    try {
        $post = Post::with('categories')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $post
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch post: ' . $e->getMessage()
        ], 400);
    }
}

    public function formatKeywords(string $keywords)
    {
        $keywordsArray = array_map('trim', explode(',', $keywords));
        return implode(', ', $keywordsArray);
    }


   public function getPostMetaValue($postMeta, $postId, $metaKey)
    {
        return $postMeta->where('post_id', $postId)
                       ->where('meta_key', $metaKey)
                       ->value('meta_value');
    }
    
   public function getThemeOptions($connectionName)
{
    $optionName = "theme_options";
    return Option::on($connectionName)
                 ->where('option_name', $optionName)
                 ->value('option_value');
}

public function updateThemeOptions($themeOptions)
{
    // \Log::info('Starting updateThemeOptions method', ['theme_options' => $themeOptions]);

    // Ensure that themeOptions is decoded or unserialized
    if (is_string($themeOptions)) {
        $themeOptions = @json_decode($themeOptions, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $themeOptions = @unserialize($themeOptions);
            if ($themeOptions === false) {
                // \Log::error('Failed to decode or unserialize theme options', ['theme_options' => $themeOptions]);
                return ['success' => false, 'message' => 'Invalid theme options data'];
            }
        }
    }

    // Get existing theme_options
    $existingThemeOptions = Option::where('option_name', 'theme_options')
        ->value('option_value');

    $existingThemeOptionsArray = @unserialize($existingThemeOptions);
    if ($existingThemeOptionsArray === false) {
        $existingThemeOptionsArray = [];
        \Log::warning('Failed to unserialize existing theme options', ['options' => $existingThemeOptions]);
    }

    // Merge new options, giving priority to new values except for hidden fields
    $updatedThemeOptions = $existingThemeOptionsArray;
    if (is_array($themeOptions)) {
        foreach ($themeOptions as $key => $value) {
            if ($key === 'data') {
                // Special handling for 'data' field
                $updatedThemeOptions[$key] = is_string($value) ? json_decode($value, true) : $value;
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $updatedThemeOptions[$key] = $existingThemeOptionsArray[$key] ?? [];
                    \Log::warning('Failed to decode data field in theme options', ['value' => $value]);
                }
            } else {
                // Update all other fields
                $updatedThemeOptions[$key] = $value;
            }
        }
    } else {
        // \Log::error('Theme options are not in array format', ['theme_options' => $themeOptions]);
        return ['success' => false, 'message' => 'Invalid theme options format'];
    }

    // Serialize the updated options
    $newThemeOptionsValue = serialize($updatedThemeOptions);

    try {
        Option::where('option_name', 'theme_options')
            ->update(['option_value' => $newThemeOptionsValue]);
        // \Log::info('Theme options updated successfully');
        return ['success' => true, 'message' => 'Theme options updated successfully'];
    } catch (\Exception $e) {
        \Log::error('Failed to update theme options', ['error' => $e->getMessage()]);
        return ['success' => false, 'message' => 'Failed to update theme options: ' . $e->getMessage()];
    }
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

    public function getImageDetails($imageId, $connectionName)
    {
        if (!$imageId) {
            return null;
        }

        $imagePost = Post::on($connectionName)->where('ID', $imageId)->where('post_type', 'attachment')->first();

        if (!$imagePost) {
            return null;
        }

        $imageUrl = $imagePost->guid; // The URL to the image file
        $altText = PostMeta::on($connectionName)->where('post_id', $imageId)->where('meta_key', '_wp_attachment_image_alt')->value('meta_value') ?? "";

        return [
            'id' => $imageId,
            'url' => $imageUrl,
            'alt' => $altText,
        ];
    }

}
