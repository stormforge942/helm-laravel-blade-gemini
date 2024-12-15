<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WordPressService;
use App\Jobs\UpdateFooterMenu;
class RunFooterMenuJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:run-footer-menu-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adding Blog to menu in footer';

    /**
     * Execute the console command.
     */
    public function handle(WordPressService $wordPressService)
    {
        UpdateFooterMenu::dispatch($wordPressService);
        $this->info('UpdateFooterMenu job has been dispatched.');
        return Command::SUCCESS;
    }
}
