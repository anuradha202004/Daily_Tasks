<?php
// router.php for PHP built-in server

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// If the file exists in the public directory, serve it directly
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false; // serve the requested resource as-is
}

// Otherwise, route to public/index.php
$_GET['url'] = ltrim($uri, '/');
require_once __DIR__ . '/public/index.php';
?>
