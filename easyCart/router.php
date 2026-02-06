<?php
// Simple PHP Router for Built-in Server
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// 1. Serve static files directly if they exist
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Serve file as-is
}

// 2. Handle root path
if ($uri === '/' || $uri === '/index') {
    require 'index.php';
    exit;
}

// 3. Auto-append .php if file exists
$file = __DIR__ . $uri . '.php';
if (file_exists($file)) {
    require $file;
    exit;
}

// 4. Handle 404
http_response_code(404);
echo "404 Not Found";
