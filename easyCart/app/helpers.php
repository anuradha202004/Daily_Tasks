<?php
/**
 * Helper Functions
 * Global utility functions used across the application
 */



/**
 * Redirect to a URL
 * @param string $url
 */
function redirect($url) {
    // If url starts with /, assume it is internal
    if (strpos($url, '/') === 0) {
        $url = ltrim($url, '/');
    }
    header("Location: $url");
    exit;
}

/**
 * Get the base URL
 * @return string
 */
function baseUrl($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . '://' . $host . '/' . ltrim($path, '/');
}

/**
 * Load a view file
 * @param string $view
 * @param array $data
 */
function view($view, $data = []) {
    extract($data);
    $viewPath = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
    
    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        die("View not found: $view");
    }
}

/**
 * Sanitize output
 * @param string $string
 * @return string
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
