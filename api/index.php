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

// Force session driver to cookie for Vercel
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

// Strict TiDB SSL Forcing
$caPath = __DIR__ . '/../isrgrootx1.pem';
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

    $request = Illuminate\Http\Request::capture();
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    // Check if it's a DB error to provide specific guidance
    $msg = $e->getMessage();
    
    if (str_contains($msg, 'Access denied') || str_contains($msg, 'Unknown database') || str_contains($msg, 'insecure transport') || str_contains($msg, 'Connection')) {
        if (ob_get_length()) ob_clean();
        echo "<h1>CRITICAL DATABASE DIAGNOSTIC</h1>";
        echo "<p><b>Error:</b> " . htmlspecialchars($msg) . "</p>";
        echo "<hr>";
        echo "<h3>Environment Check:</h3>";
        echo "<ul>";
        echo "<li><b>DB_HOST:</b> " . htmlspecialchars(getenv('DB_HOST')) . "</li>";
        echo "<li><b>DB_PORT:</b> " . htmlspecialchars(getenv('DB_PORT')) . "</li>";
        echo "<li><b>DB_DATABASE:</b> " . htmlspecialchars(getenv('DB_DATABASE')) . "</li>";
        echo "<li><b>DB_USERNAME:</b> " . htmlspecialchars(getenv('DB_USERNAME')) . "</li>";
        echo "<li><b>Specified CA Cert Path:</b> " . htmlspecialchars(getenv('MYSQL_ATTR_SSL_CA')) . "</li>";
        echo "<li><b>CA Cert Physical Path:</b> " . htmlspecialchars($caPath) . "</li>";
        echo "<li><b>Cert File Exists locally?</b> " . (file_exists($caPath) ? '<b style="color:green">YES</b>' : '<b style="color:red">NO (MISSING!)</b>') . "</li>";
        echo "<li><b>Password Provided?</b> " . (getenv('DB_PASSWORD') ? 'YES (' . strlen(getenv('DB_PASSWORD')) . ' chars)' : 'NO') . "</li>";
        echo "</ul>";
        
        echo "<h3>Manual Connection Test:</h3>";
        try {
            $dsn = "mysql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT');
            $pdo = new PDO($dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [
                PDO::MYSQL_ATTR_SSL_CA => $caPath,
                PDO::ATTR_TIMEOUT => 5
            ]);
            echo '<p style="color:green"><b>SUCCESS! Raw PHP was able to connect to TiDB Host.</b></p>';
        } catch (\Exception $ex) {
            echo '<p style="color:red"><b>FAILURE! Raw PHP could not connect:</b> ' . htmlspecialchars($ex->getMessage()) . '</p>';
        }

        echo "<p><i>Tip: If Cert exists but connection fails, double check '0.0.0.0/0' in TiDB Networking.</i></p>";
    } else {
        throw $e; 
    }
}
