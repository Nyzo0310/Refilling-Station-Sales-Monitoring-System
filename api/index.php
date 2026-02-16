<?php

// Set up storage for read-only filesystem on Vercel
$storagePath = '/tmp/storage/bootstrap/cache';
if (!is_dir($storagePath)) {
    mkdir($storagePath, 0755, true);
}

// Redirect storage paths to /tmp
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('SESSION_DRIVER=cookie'); // Vercel is serverless, use cookies for sessions
putenv('LOG_CHANNEL=stderr');    // Send errors to Vercel logs

// Create necessary directories
@mkdir('/tmp/storage/framework/views', 0755, true);
@mkdir('/tmp/storage/framework/sessions', 0755, true);

require __DIR__ . '/../public/index.php';
