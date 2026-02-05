<?php
// router.php - Handles Clean URLs for PHP Built-in Server

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 1. Static Files: If it exists, check for .php enforcement
$file = __DIR__ . $path;
if (file_exists($file) && !is_dir($file)) {
    // If requesting a .php file directly, redirect to clean URL
    // (Mimics .htaccess RewriteRule ^(.*)\.php$ /$1 [R=301,L])
    if (substr($path, -4) === '.php') {
        $cleanPath = substr($path, 0, -4);
        // Special case: /index -> /
        if ($cleanPath === '/index') {
            $cleanPath = '/';
        }
        
        // Preserve POST data using 307, otherwise 301
        $responseCode = ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'HEAD') ? 301 : 307;
        
        $queryString = $_SERVER['QUERY_STRING'];
        $redirectUrl = $cleanPath . ($queryString ? '?' . $queryString : '');
        
        header("Location: " . $redirectUrl, true, $responseCode);
        exit();
    }
    
    // Serve the file as-is
    return false; 
}

// 2. Handle Extensionless URLs (Internal Rewrite)
// Check if the path corresponds to a .php file
// (Mimics .htaccess RewriteCond %{REQUEST_FILENAME}.php -f)
$phpFile = $file . '.php';
if (file_exists($phpFile)) {
    require $phpFile;
    exit();
}

// 3. Handle Directory Index (for /) automatically by returning false
return false;
?>
