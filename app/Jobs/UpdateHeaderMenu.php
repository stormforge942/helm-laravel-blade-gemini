<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\WordPressService;
use Corcel\Model\Option;
use App\Models\Site;
use App\Models\Post;
use Corcel\Model\Meta\PostMeta;
use Corcel\Model\Taxonomy;
use Corcel\Model\Term;
use Corcel\Model\TermRelationship;
use Illuminate\Support\Facades\DB;

class UpdateHeaderMenu implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $wordPressService;
    /**
     * Create a new job instance.
     */
    public function __construct(WordPressService $wordPressService)
    {
        $this->wordPressService = $wordPressService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $postData = [
            'post_title' => 'Blog',
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'nav_menu_item',
            'post_author' => '2',
        ];
        $sites = Site::all();
        foreach ($sites as $site) {
            try {
                $connectionName = $this->wordPressService->connectToWp($site->id);

                $post = Post::on($connectionName)->where('post_title', 'Blog')->where(function ($query) {
                    $query->where('post_status', 'publish')
                        ->orWhere('post_status', 'draft');
                })->first();
                if (!empty($post)) {
                    if ($post->post_status == 'draft') {
                        $post->post_status = 'publish';
                        $post->save();
                    }
                    $theme = Option::on($connectionName)->where('option_name', 'template')->first();
                    $themeData = '';
                    if ($theme->option_value == 'understrap') {
                        $themeData = Option::on($connectionName)->where('option_name', 'theme_mods_understrap')->first();
                    } else if ($theme->option_value == 'Divi') {
                        $themeData = Option::on($connectionName)->where('option_name', 'theme_mods_Divi-Child')->first();
                    }
                    $themeData = unserialize($themeData->option_value);
                    $primaryMenu = $themeData['nav_menu_locations']['primary'];
                    // Fetch the "Blog" item in the primary menu
                    $post_meta_vlaues = PostMeta::on($connectionName)->where('meta_key', '_menu_item_object_id')->where('meta_value', $post->ID)->get();
                    foreach ($post_meta_vlaues as $post_meta) {
                        $post_meta_id = $post_meta->post_id;
                        $post_meta->delete();
                        Post::on($connectionName)->find($post_meta_id)->delete();
                        TermRelationship::on($connectionName)->where('object_id', $post_meta_id)->delete();
                    }

                    $postMenuId = Post::on($connectionName)->create($postData);
                    $postMenuId->menu_order = '30';
                    $postMenuId->save();
                    $postMenuId = $postMenuId->ID;

                    $metaFields = [
                        '_menu_item_type' => 'post_type',
                        '_menu_item_menu_item_parent' => 0,
                        '_menu_item_object_id' => $post->ID,
                        '_menu_item_object' => 'page',
                        '_menu_item_target' => '',
                        '_menu_item_classes' => '',
                        '_menu_item_xfn' => '',
                        '_menu_item_url' => '',
                    ];

                    foreach ($metaFields as $metaKey => $metaValue) {
                        PostMeta::on($connectionName)->updateOrCreate([
                            'post_id' => $postMenuId,
                            'meta_key' => $metaKey,
                        ], [
                            'meta_value' => $metaValue,
                        ]);
                    }

                    $termRelationship = TermRelationship::on($connectionName)->newModelInstance();
                    $termRelationship->object_id = $postMenuId;
                    $termRelationship->term_taxonomy_id = $primaryMenu;
                    $termRelationship->term_order = 0;
                    $termRelationship->save();
                }
            } catch (\Exception $e) {
                // Log the error or handle it as needed
                // \Log::error("Error updating SEO for site: {$site->id} - {$e->getMessage()}");
            } finally {
                // Ensure the connection is closed
                DB::disconnect($connectionName);
            }
        }
    }
}
