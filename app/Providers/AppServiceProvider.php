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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS for assets on production
        if (env('FORCE_HTTPS', false) || env('APP_ENV') === 'production') {
            \URL::forceScheme('https');
        }

        // Ensure 'admin' middleware alias is registered (in case Kernel aliasing is missing)
        try {
            app('router')->aliasMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);
        } catch (\Throwable $e) {
            // no-op: router may not be resolved yet in some CLI contexts
        }
    }
}
