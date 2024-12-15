<?php

namespace App\Console\Commands;

use App\Models\Site;
use Illuminate\Console\Command;

class SiteMaker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:site-maker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Site::create([
            'domain_name' => 'wordpress',
            'site_url' => 'http://wordpress.test/',
            'db_name' => encrypt('jasmine'),
            'db_username' => encrypt('root'),
            'db_password' => encrypt(''),
            'wp_prefix' => encrypt('wp_'),
            'niche' => 'Concrete',
            'server' => 'saphire'
        ]);
    }
}
