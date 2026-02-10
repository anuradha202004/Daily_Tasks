<?php
// Simple PHP Router for Built-in Server
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// 1. Serve static files directly if they exist in root
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Serve file as-is
}

// 2. Serve static files from public folder if they exist there
// This handles cases where we run php -S from root but request /css/style.css
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    $file = __DIR__ . '/public' . $uri;
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    
    $mimes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
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
        'json'  => 'application/json',
        'map'   => 'application/json',
        'txt'   => 'text/plain',
        'html'  => 'text/html',
        'xml'   => 'application/xml',
    ];

    $contentType = $mimes[$ext] ?? 'text/plain';
    
    header('Content-Type: ' . $contentType);
    readfile($file);
    exit;
}

// 3. Handle everything else through public/index.php (MVC)
$_GET['url'] = ltrim($uri, '/');
require 'public/index.php';
exit;
