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
// Define base paths
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CORE_PATH', APP_PATH . '/Core');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('RESOURCES_PATH', BASE_PATH . '/resources');
define('TEMPLATES_PATH', RESOURCES_PATH . '/templates');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoloader - Register logic for standard classes
require_once __DIR__ . '/Autoloader.php';

// Load configuration
if (file_exists(CONFIG_PATH . '/database/config.php')) {
    require_once CONFIG_PATH . '/database/config.php';
}

// Load legacy components temporarily until refactored
// require_once CORE_PATH . '/db.php'; // Replaced by Core_Database
require_once CORE_PATH . '/data.php'; // Still needed for now
require_once CORE_PATH . '/auth.php'; // Still needed for now

// Auto-logout if user was deleted from DB
checkUserExists();

// Initialize database connection using new Core_Database if needed globally
// But for now, we let classes instantiate it on demand.

// Load helper functions
require_once APP_PATH . '/helpers.php';
