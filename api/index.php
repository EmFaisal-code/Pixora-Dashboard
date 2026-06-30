<?php
define('LARAVEL_START', microtime(true));

// ===== STATIC FILE SERVER =====
// Vercel tidak bisa melayani file statis dengan benar untuk PHP,
// jadi kita buat PHP sendiri yang melayaninya.
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Cek apakah request adalah untuk file statis di /build/
if (preg_match('/^\/build\/(.+)$/', $requestUri, $matches)) {
    $staticFile = __DIR__ . '/../public/build/' . $matches[1];
    if (file_exists($staticFile)) {
        $ext = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));
        $mimeTypes = [
            'css'   => 'text/css',
            'js'    => 'application/javascript',
            'json'  => 'application/json',
            'png'   => 'image/png',
            'jpg'   => 'image/jpeg',
            'jpeg'  => 'image/jpeg',
            'gif'   => 'image/gif',
            'svg'   => 'image/svg+xml',
            'ico'   => 'image/x-icon',
            'woff'  => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf'   => 'font/ttf',
            'eot'   => 'application/vnd.ms-fontobject',
        ];
        header('Content-Type: ' . ($mimeTypes[$ext] ?? 'application/octet-stream'));
        header('Cache-Control: public, max-age=31536000, immutable');
        readfile($staticFile);
        exit;
    }
}

// ===== LARAVEL BOOTSTRAP =====
// Beritahu Laravel agar menggunakan folder /tmp yang bisa ditulis di Vercel
$_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/events.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp';
$_ENV['CACHE_STORE'] = 'array';
$_ENV['SESSION_DRIVER'] = 'cookie';
$_ENV['LOG_CHANNEL'] = 'stderr';

// Paksa HTTPS agar tidak muncul pesan "not secure"
$_ENV['APP_URL'] = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'pixora-dashboard-zeta.vercel.app');
$_SERVER['HTTPS'] = 'on';

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

// Paksa Laravel menggunakan /tmp untuk semua folder storage
$app->useStoragePath('/tmp');

$app->handleRequest(Illuminate\Http\Request::capture());

