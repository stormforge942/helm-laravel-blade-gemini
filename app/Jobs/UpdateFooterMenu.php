<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Corcel\Model\Option;
use App\Models\Site;
use App\Models\Post;
use Corcel\Model\Meta\PostMeta;
use Corcel\Model\Taxonomy;
use Corcel\Model\Term;
use Corcel\Model\TermRelationship;
use Illuminate\Support\Facades\DB;
use App\Services\WordPressService;

class UpdateFooterMenu implements ShouldQueue
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
    { {
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
                    // Fetch the 'Footer Quick Links' menu ID
                    $footerMenu = Term::on($connectionName)->whereHas('taxonomy', function ($query) {
                        $query->where('taxonomy', 'nav_menu');
                    })->where('name', 'Footer Quick Links')->first();

                    $post = Post::on($connectionName)->where('post_title', 'Blog')->where(function ($query) {
                        $query->where('post_status', 'publish')
                            ->orWhere('post_status', 'draft');
                    })->first();
                    if (!empty($post)) {
                        if ($post->post_status == 'draft') {
                            $post->post_status = 'publish';
                            $post->save();
                        }
                        // Check if the menu exists and fetch the term ID
                        if ($footerMenu) {
                            $menuId = $footerMenu->term_id;
                            // Fetch the "Blog" item in the primary menu
                            $postMenuId = Post::on($connectionName)->create($postData);
                            $postMenuId->menu_order = '2';
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
                            $termRelationship->term_taxonomy_id = $menuId;
                            $termRelationship->term_order = 0;
                            $termRelationship->save();
                        }
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
}
