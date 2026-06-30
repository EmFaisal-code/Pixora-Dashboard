<?php
define('LARAVEL_START', microtime(true));

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

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

// Paksa Laravel menggunakan /tmp untuk semua folder storage (logs, framework, dll)
$app->useStoragePath('/tmp');

$app->handleRequest(Illuminate\Http\Request::capture());
