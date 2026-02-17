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
$_ENV['APP_CONFIG_CACHE'] = '/tmp/storage/bootstrap/cache/config.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/storage/bootstrap/cache/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/storage/bootstrap/cache/packages.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/storage/bootstrap/cache/routes.php';

// Force session driver to cookie for Vercel (database sessions need SSL, which might fail early)
$_ENV['SESSION_DRIVER'] = 'cookie';

putenv('APP_STORAGE=/tmp/storage');
putenv('APP_BOOTSTRAP_CACHE=/tmp/storage/bootstrap/cache');
putenv('APP_CONFIG_CACHE=/tmp/storage/bootstrap/cache/config.php');
putenv('APP_SERVICES_CACHE=/tmp/storage/bootstrap/cache/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/storage/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/storage/bootstrap/cache/routes.php');

putenv('APP_ENV=production');
putenv('APP_DEBUG=true');
putenv('LOG_CHANNEL=stderr');
putenv('SESSION_DRIVER=cookie');

// Force SSL for TiDB if not set
if (empty(getenv('MYSQL_ATTR_SSL_CA')) && empty($_ENV['MYSQL_ATTR_SSL_CA'])) {
    // If we suspect TiDB (usually via host), we can force it
    // For now, let's just set the default CA we have in Git
    $_ENV['MYSQL_ATTR_SSL_CA'] = 'isrgrootx1.pem';
    putenv('MYSQL_ATTR_SSL_CA=isrgrootx1.pem');
}

try {
    // Autoload check
    $autoloader = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloader)) {
        throw new \Exception("Autoloader not found at: " . $autoloader);
    }
    require $autoloader;

    // Bootstrap Laravel
    $app = require __DIR__ . '/../bootstrap/app.php';

    // Manual Handle
    $request = Illuminate\Http\Request::capture();
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    try {
        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);
    } catch (\Throwable $e) {
        if (ob_get_length()) ob_clean();
        echo "<h1>CRITICAL APPLICATION ERROR</h1>";
        echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
        echo "<p><b>File:</b> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
        echo "<h3>Stack Trace:</h3>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }

} catch (\Throwable $e) {
    echo "<h1>CRITICAL STARTUP ERROR</h1>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
