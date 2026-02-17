<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Ensure View service is bound to prevent looping errors on Vercel
if (!$app->bound('view')) {
    $app->register(\Illuminate\View\ViewServiceProvider::class);
}

// Set storage path (for Vercel compatibility)
if (isset($_ENV['APP_STORAGE'])) {
    $app->useStoragePath($_ENV['APP_STORAGE']);
}

return $app;
