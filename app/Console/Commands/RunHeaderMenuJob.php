<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateHeaderMenu;
use App\Services\WordPressService;
class RunHeaderMenuJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:run-header-menu-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adding Blog to menu in Header';

    /**
     * Execute the console command.
     */
    public function handle(WordPressService $wordPressService)
    {
        UpdateHeaderMenu::dispatch($wordPressService);
        $this->info('UpdateHeaderMenu job has been dispatched.');
        return Command::SUCCESS;
    }
}
