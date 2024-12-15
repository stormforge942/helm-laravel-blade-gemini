<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateSiteMapPage;
use App\Services\WordPressService;

class RunSiteMapJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:run-sitemap-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Sitemap with dynamic Sitemap';

    /**
     * Execute the console command.
     */
    public function handle(WordPressService $wordPressService)
    {
        UpdateSiteMapPage::dispatch($wordPressService);
        $this->info('UpdateSiteMapPage job has been dispatched.');
        return Command::SUCCESS;
    }
}
