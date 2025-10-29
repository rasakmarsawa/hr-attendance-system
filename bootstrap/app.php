<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web([
            \App\Http\Middleware\ForceHttps::class,
        ]);

        $middleware->alias([
            'role' => App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // if (app()->environment('production')) {
        //         return;
        //     }

        $schedule->command('migrate:fresh --seed')->dailyAt('03:00');
    })    
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
