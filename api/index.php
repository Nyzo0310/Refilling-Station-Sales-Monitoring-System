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
putenv('APP_STORAGE=/tmp/storage');
putenv('APP_ENV=production');
putenv('APP_DEBUG=true');
putenv('LOG_CHANNEL=stderr');
putenv('SESSION_DRIVER=cookie');

try {
    if (!file_exists(__DIR__ . '/../public/index.php')) {
        throw new \Exception("Entry point not found: " . __DIR__ . '/../public/index.php');
    }
    
    if (empty(getenv('APP_KEY')) && empty($_ENV['APP_KEY'])) {
        // We can't use env() yet if app isn't booted, but let's check getenv
        if (empty(getenv('APP_KEY'))) {
            throw new \Exception("APP_KEY is missing! Please add it to Vercel Environment Variables.");
        }
    }

    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    echo "<h1>Critical Startup Error</h1>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
