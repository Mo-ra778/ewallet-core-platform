<?php

// Ensure serverless storage and cache directories exist in /tmp
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/storage/logs',
    '/tmp/views'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Normalize SCRIPT_NAME and REQUEST_URI for Laravel routing on Vercel
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

if (isset($_SERVER['REQUEST_URI']) && str_starts_with($_SERVER['REQUEST_URI'], '/api/index.php')) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen('/api/index.php')) ?: '/';
}

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and set writable serverless storage path...
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath('/tmp/storage');

$app->handleRequest(\Illuminate\Http\Request::capture());
