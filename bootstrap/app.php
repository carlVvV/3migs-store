<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'cart.sync' => \App\Http\Middleware\CartSyncMiddleware::class,
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth.sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        
        // Apply cart sync middleware to web routes
        $middleware->web(append: [
            \App\Http\Middleware\CartSyncMiddleware::class,
        ]);

        // Ensure stateful requests for API (for cookie/session auth) - exclude PSGC routes
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        
        // Create a custom middleware group for PSGC routes (no stateful middleware, no CSRF)
        $middleware->group('psgc', [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
        
        // Use custom CSRF middleware
        $middleware->web(replace: [
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class => \App\Http\Middleware\VerifyCsrfToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
