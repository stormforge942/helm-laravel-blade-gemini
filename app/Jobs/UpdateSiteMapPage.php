<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\Site;
use App\Services\WordPressService;
use Corcel\Model\Meta\PostMeta;
use Corcel\Model\Option;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateSiteMapPage implements ShouldQueue
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

        $sites = Site::all();
        foreach ($sites as $site) {
            try {
                $connectionName = $this->wordPressService->connectToWp($site->id);
                $theme = Option::on($connectionName)->where('option_name', 'template')->first();
                if ($theme->option_value == 'understrap') {
                    $post = Post::on($connectionName)->where('post_title', 'Sitemap')->where(function ($query) {
                        $query->where('post_status', 'publish');
                    })->first();

                    PostMeta::on($connectionName)->updateOrCreate([
                        'post_id' => $post->ID,
                        'meta_key' => 'right_side_bar_services',
                    ], [
                        'meta_value' => '<h1>Sitemap</h1>[rank_math_html_sitemap]',
                    ]);
                }
            } catch (\Exception $e) {
                // Log the error or handle it as needed
                \Log::error("Error updating SEO for site: {$site->id} - {$e->getMessage()}");
            } finally {
                // Ensure the connection is closed
                DB::disconnect($connectionName);
            }
        }
    }
}
