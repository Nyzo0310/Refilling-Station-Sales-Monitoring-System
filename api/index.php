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

// Redirect environment variables for Vercel to writable /tmp
$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['APP_BOOTSTRAP_CACHE'] = '/tmp/storage/bootstrap/cache';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/storage/bootstrap/cache/config.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/storage/bootstrap/cache/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/storage/bootstrap/cache/packages.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/storage/bootstrap/cache/routes.php';
$_ENV['SESSION_DRIVER'] = 'cookie';

putenv('APP_STORAGE=/tmp/storage');
putenv('APP_BOOTSTRAP_CACHE=/tmp/storage/bootstrap/cache');
putenv('APP_CONFIG_CACHE=/tmp/storage/bootstrap/cache/config.php');
putenv('APP_SERVICES_CACHE=/tmp/storage/bootstrap/cache/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/storage/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/storage/bootstrap/cache/routes.php');
putenv('SESSION_DRIVER=cookie');

// Strict TiDB SSL Forcing
if (empty(getenv('MYSQL_ATTR_SSL_CA'))) {
    $_ENV['MYSQL_ATTR_SSL_CA'] = 'isrgrootx1.pem';
    putenv('MYSQL_ATTR_SSL_CA=isrgrootx1.pem');
}
if (empty(getenv('DB_SSL_VERIFY'))) {
    $_ENV['DB_SSL_VERIFY'] = 'true';
    putenv('DB_SSL_VERIFY=true');
}

try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require __DIR__ . '/../bootstrap/app.php';

    // Force HTTPS on Vercel without using Facades (prevents "Facade root not set" error)
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $_SERVER['HTTPS'] = 'on';
    }

    $request = Illuminate\Http\Request::capture();
    
    // Handle Trusted Proxies for Vercel
    $request->setTrustedProxies([$request->getClientIp()], \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR | \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO | \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT | \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST);

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    if (ob_get_length()) ob_clean();
    header('HTTP/1.1 500 Internal Server Error');
    echo "<h1>APPLICATION ERROR</h1>";
    echo "<p>Something went wrong. Please check your cloud environment settings.</p>";
    if (getenv('APP_DEBUG') === 'true') {
        echo "<p><b>Debug Message:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
