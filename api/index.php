<?php

// 1. Ensure storage sub-directories exist in /tmp for Vercel
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

// 2. Setup Vercel Environment Variables
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

// 3. Strict TiDB SSL Forcing
$caPath = __DIR__ . '/../isrgrootx1.pem';
if (empty(getenv('MYSQL_ATTR_SSL_CA'))) {
    $_ENV['MYSQL_ATTR_SSL_CA'] = 'isrgrootx1.pem';
    putenv('MYSQL_ATTR_SSL_CA=isrgrootx1.pem');
}
if (empty(getenv('DB_SSL_VERIFY'))) {
    $_ENV['DB_SSL_VERIFY'] = 'true';
    putenv('DB_SSL_VERIFY=true');
}

// 4. PRE-BOOT CONNECTION DIAGNOSTIC (The "Life Saver")
if (getenv('DB_HOST')) {
    $dbHost = trim(getenv('DB_HOST'));
    $dbPort = trim(getenv('DB_PORT'));
    $dbUser = trim(getenv('DB_USERNAME'));
    $dbPass = trim(getenv('DB_PASSWORD'));
    $dbName = trim(getenv('DB_DATABASE'));

    try {
        $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::MYSQL_ATTR_SSL_CA => $caPath,
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        // If we get here, connection works! Proceed to Laravel.
    } catch (\PDOException $e) {
        // CONNECTION FAILED - Capture the detailed error before Laravel tries
        if (ob_get_length()) ob_clean();
        header('HTTP/1.1 500 Internal Server Error');
        echo "<div style='font-family:sans-serif; padding: 20px; border: 2px solid red; background: #fff5f5;'>";
        echo "<h1 style='color:#c53030;'>❌ Database Connection Denied</h1>";
        echo "<p><b>The TiDB Server said:</b> <span style='color:red'>" . htmlspecialchars($e->getMessage()) . "</span></p>";
        echo "<hr>";
        echo "<h3>What the Server Sees:</h3>";
        echo "<ul>";
        echo "<li><b>TiDB Host:</b> " . htmlspecialchars($dbHost) . " (Port $dbPort)</li>";
        echo "<li><b>Username:</b> " . htmlspecialchars($dbUser) . "</li>";
        echo "<li><b>Database:</b> " . htmlspecialchars($dbName) . "</li>";
        echo "<li><b>SSL File Found?</b> " . (file_exists($caPath) ? "<span style='color:green'>YES</span>" : "<span style='color:red'>NO! (Vercel missed it)</span>") . "</li>";
        echo "<li><b>SSL Path:</b> " . htmlspecialchars($caPath) . "</li>";
        echo "<li><b>Password Length:</b> " . strlen($dbPass) . " chars</li>";
        echo "</ul>";
        echo "<h3>Next Steps:</h3>";
        echo "<ol>";
        echo "<li>If it says <b>Access denied</b>, your <b>Password</b> or <b>IP Whitelist</b> in TiDB is still blocking it.</li>";
        echo "<li>If it says <b>Unknown database</b>, change your Vercel DB_DATABASE to 'db_refilling'.</li>";
        echo "<li>If SSL is <b>NO</b>, I need to check the build process.</li>";
        echo "</ol>";
        echo "</div>";
        exit;
    }
}

// 5. Boot Laravel
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require __DIR__ . '/../bootstrap/app.php';

    $request = Illuminate\Http\Request::capture();
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    if (ob_get_length()) ob_clean();
    echo "<h1>CRITICAL ERROR</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
