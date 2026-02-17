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

// Redirect environment variables
$_ENV['APP_ENV'] = 'production';
$_ENV['APP_DEBUG'] = 'true';
$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['APP_STORAGE'] = '/tmp/storage';

putenv('APP_ENV=production');
putenv('APP_DEBUG=true');
putenv('LOG_CHANNEL=stderr');
putenv('APP_STORAGE=/tmp/storage');

try {
    // Verify core files exist
    $coreFiles = [
        __DIR__ . '/../public/index.php',
        __DIR__ . '/../bootstrap/app.php',
        __DIR__ . '/../vendor/autoload.php',
        __DIR__ . '/../vendor/laravel/framework/src/Illuminate/View/ViewServiceProvider.php',
    ];

    foreach ($coreFiles as $file) {
        if (!file_exists($file)) {
            throw new \Exception("Missing core file: " . $file);
        }
    }

    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    echo "<h1>Critical Error</h1>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    
    echo "<h3>Environment Debug:</h3>";
    echo "<pre>";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Current Dir: " . __DIR__ . "\n";
    echo "Base Path: " . realpath(__DIR__ . '/..') . "\n";
    echo "Storage Path Bound: " . (isset($app) ? $app->storagePath() : 'N/A') . "\n";
    echo "</pre>";
}
