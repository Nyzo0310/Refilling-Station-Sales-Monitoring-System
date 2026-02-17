<?php

// Ensure storage sub-directories exist in /tmp for Vercel
$storageDirectories = [
    '/tmp/storage/bootstrap/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/app/public',
    '/tmp/storage/logs',
];

foreach ($storageDirectories as $directory) {
    if (!is_dir($directory)) {
        @mkdir($directory, 0755, true);
    }
}

// Redirect environment variables for Vercel
$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['APP_BOOTSTRAP_CACHE'] = '/tmp/storage/bootstrap/cache';
putenv('APP_STORAGE=/tmp/storage');
putenv('APP_BOOTSTRAP_CACHE=/tmp/storage/bootstrap/cache');
putenv('APP_ENV=production');
putenv('APP_DEBUG=true');
putenv('LOG_CHANNEL=stderr');
putenv('SESSION_DRIVER=cookie');

try {
    // Early existence checks
    if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
        throw new \Exception("Autoloader not found at " . __DIR__ . '/../vendor/autoload.php');
    }

    // Load Autoloader
    require __DIR__ . '/../vendor/autoload.php';

    // Bootstrap Laravel
    $app = require __DIR__ . '/../bootstrap/app.php';

    // Manual Handle to catch the REAL error and prevent loops
    $request = Illuminate\Http\Request::capture();
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    try {
        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);
    } catch (\Throwable $e) {
        // If we get here, it means Laravel's internal handler failed or was bypassed
        if (ob_get_length()) ob_clean();
        
        echo "<h1>CRITICAL APPLICATION ERROR</h1>";
        echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
        echo "<p><b>File:</b> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
        echo "<h3>Stack Trace:</h3>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
        
        error_log("FATAL: " . $e->getMessage());
    }

} catch (\Throwable $e) {
    echo "<h1>CRITICAL STARTUP ERROR</h1>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    
    error_log("STARTUP FATAL: " . $e->getMessage());
}
