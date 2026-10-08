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
->withMiddleware(function (Middleware $middleware) {
    // "Under maintenance" gate — runs before everything else. See config/maintenance.php.
    $middleware->prepend(\App\Http\Middleware\SiteMaintenance::class);
    $middleware->encryptCookies(except: [
        \App\Http\Middleware\SiteMaintenance::PREVIEW_COOKIE,
    ]);

    $middleware->validateCsrfTokens(except: [
        'webhooks/authorize-net',
        'webhooks/commas',
    ]);
    $middleware->alias([
        'referral'   => \App\Http\Middleware\ReferralMiddleware::class,
        'admin.auth' => \App\Http\Middleware\AdminAuth::class,
    ]);
})

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
