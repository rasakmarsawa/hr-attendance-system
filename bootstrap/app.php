<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create()
    // -------------------------------
    // TRUST PROXIES (for Render load balancer)
    // -------------------------------
    ->tap(function (Application $app) {
        $request = $app->make(Request::class);
        $request->setTrustedProxies(
            ['0.0.0.0/0'], // trust all proxies
            Request::HEADER_X_FORWARDED_ALL
        );

        // -------------------------------
        // FORCE HTTPS IN PRODUCTION
        // -------------------------------
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    });
