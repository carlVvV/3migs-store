<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Load Vite helper functions
        require_once app_path('helpers/vite_helper.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
