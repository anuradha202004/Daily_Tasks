<?php
/**
 * Helper Functions
 * Global utility functions used across the application
 */

/**
 * Get database connection
 * @return PDO
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        $config = require CONFIG_PATH . '/database/config.php';
        
        $dsn = sprintf(
            "%s:host=%s;port=%s;dbname=%s",
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['database']
        );
        
        try {
            $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            die("<h3>Database Connection Failed:</h3> " . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Format price with currency symbol
 * @param float $price
 * @return string
 */
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

/**
 * Redirect to a URL
 * @param string $url
 */
function redirect($url) {
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

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

/**
 * Get current user
 * @return array|null
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'name' => $_SESSION['user_name'] ?? 'User'
        ];
    }
    return null;
}

/**
 * Require login - redirect if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('/signin.php?redirect=1');
    }
}
