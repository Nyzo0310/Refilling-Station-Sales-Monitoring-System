<?php

// Set up storage for read-only filesystem on Vercel
$storagePath = '/tmp/storage/bootstrap/cache';
if (!is_dir($storagePath)) {
    mkdir($storagePath, 0755, true);
}

// Redirect storage paths to /tmp
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('SESSION_DRIVER=cookie'); 
putenv('LOG_CHANNEL=stderr');
putenv('APP_DEBUG=true');
putenv('APP_ENV=production'); // Keep production but force debug

// Create necessary directories
@mkdir('/tmp/storage/framework/views', 0755, true);
@mkdir('/tmp/storage/framework/sessions', 0755, true);
@mkdir('/tmp/storage/bootstrap/cache', 0755, true);

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    error_log('CRITICAL ERROR: ' . $e->getMessage());
    error_log('STACK TRACE: ' . $e->getTraceAsString());
    echo "<h1>Critical Error</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
