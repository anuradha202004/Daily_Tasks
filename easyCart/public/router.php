<?php
// Router for PHP Built-in Server when running from /public
// This allows proper URL rewriting (e.g. /products -> index.php?url=products)

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// 1. Serve static files directly if they exist
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Let PHP serve the file
}

// 2. Handle 404 for missing static assets (don't route to index.php)
if (preg_match('/\.(?:css|js|png|jpg|jpeg|gif|ico|svg)$/', $uri)) {
    return false; // Will return 404 standard
}

// 3. Route everything else to index.php
$_GET['url'] = ltrim($uri, '/');
require 'index.php';
