<?php
/**
 * Bootstrap File - Application Entry Point
 * Initializes the application and loads core components
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base paths
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('VIEWS_PATH', APP_PATH . '/Views');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoloader
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = APP_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    
    return false;
});

// Load configuration
require_once CONFIG_PATH . '/database/config.php';

// Load helper functions
require_once APP_PATH . '/helpers.php';

// Initialize database connection
$pdo = getDBConnection();
