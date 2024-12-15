<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WordPressService;

class WordPressServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WordPressService::class, function ($app) {
            return new WordPressService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
