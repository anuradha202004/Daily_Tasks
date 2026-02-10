<?php
$log = "Request URI: " . $_SERVER['REQUEST_URI'] . "\nGET url: " . ($_GET['url'] ?? 'NOT SET') . "\n";
file_put_contents(__DIR__ . '/../debug_log.txt', $log, FILE_APPEND);

// Start Session
session_start();

// Define Constants
define('APP_ROOT', dirname(__DIR__));
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// Ensure URL_ROOT points to the public directory
$urlRoot = $protocol . "://" . $host . ($scriptName === '/' ? '' : $scriptName);
define('URL_ROOT', $urlRoot);

// Load Bootstrap (Defines Paths, Data, db, helpers)
require_once __DIR__ . '/../app/bootstrap.php';

// Instantiate Router using new class name
$router = new Core_Router();

// Define Routes matching old pages to new Controllers
$router->add('', 'Controller_Home', 'index');
$router->add('search-results', 'Controller_Product', 'search');
$router->add('products', 'Controller_Product', 'index');
$router->add('product/{slug}', 'Controller_Product', 'detail');
$router->add('product-detail', 'Controller_Product', 'detail'); // Keep old route for compatibility
$router->add('cart', 'Controller_Cart', 'index');
$router->add('checkout', 'Controller_Order', 'checkout');
$router->add('order-confirmation', 'Controller_Order', 'confirmation');
$router->add('invoice', 'Controller_Order', 'invoice');
$router->add('orders', 'Controller_Order', 'index');
$router->add('track-order', 'Controller_Order', 'track');
$router->add('signin', 'Controller_Auth', 'signin');
$router->add('signup', 'Controller_Auth', 'signup');
$router->add('logout', 'Controller_Auth', 'logout');
$router->add('profile', 'Controller_Profile', 'index');
$router->add('wishlist', 'Controller_Wishlist', 'index');
$router->add('wishlist/add', 'Controller_Wishlist', 'add');
$router->add('wishlist/remove', 'Controller_Wishlist', 'remove');
$router->add('about', 'Controller_Page', 'about');
$router->add('contact', 'Controller_Page', 'contact');
$router->add('contact-process', 'Controller_Page', 'processContact');
$router->add('admin/dashboard', 'Controller_Admin', 'dashboard');
$router->add('admin/import_export', 'Controller_Admin', 'importExport');

// Dispatch
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Fallback: If URL is empty but REQUEST_URI is not root, parse it manually
// This fixes the issue with PHP built-in server where $_GET['url'] might not be passed correctly
if (empty($url) && isset($_SERVER['REQUEST_URI'])) {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    
    // Remove the script path from the URI if it exists (e.g. /easyCart/public -> /)
    if (strpos($requestUri, $scriptName) === 0 && $scriptName !== '/') {
        $requestUri = substr($requestUri, strlen($scriptName));
    }
    
    $url = ltrim($requestUri, '/');
}

$router->dispatch($url);
