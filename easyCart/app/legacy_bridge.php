<?php
/**
 * Legacy Compatibility Layer
 * Provides backward compatibility for existing code
 * Maps old function calls to new MVC structure
 */

require_once __DIR__ . '/bootstrap.php';

use Services\AuthService;
use Services\CartService;
use Services\OrderService;
use Services\ProductService;
use Services\WishlistService;

// Initialize services
$authService = new AuthService();
$cartService = new CartService();
$orderService = new OrderService();
$productService = new ProductService();
$wishlistService = new WishlistService();

// ============================================
// PRODUCT FUNCTIONS
// ============================================

// Fetch products, categories, and brands
$products = [];
$categories = [];
$brands = [];

try {
    $productsArray = $productService->getAllProducts();
    foreach ($productsArray as $product) {
        $products[$product['id']] = $product;
    }
    
    $categories = $productService->getAllCategories();
    $brands = $productService->getAllBrands();
} catch (Exception $e) {
    error_log("Data loading error: " . $e->getMessage());
}

if (!function_exists('getProductById')) {
    function getProductById($id) {
        global $products;
        return $products[$id] ?? null;
    }
}

if (!function_exists('getCategoryById')) {
    function getCategoryById($id) {
        global $categories;
        return $categories[$id] ?? null;
    }
}

if (!function_exists('getBrandById')) {
    function getBrandById($id) {
        global $brands;
        return $brands[$id] ?? null;
    }
}

if (!function_exists('getProductsByCategory')) {
    function getProductsByCategory($category_id) {
        global $products;
        return array_filter($products, function($product) use ($category_id) {
            return $product['category_id'] == $category_id;
        });
    }
}

if (!function_exists('searchProducts')) {
    function searchProducts($query) {
        global $products;
        $query = strtolower(trim($query));
        
        if (empty($query)) {
            return $products;
        }
        
        return array_filter($products, function($product) use ($query) {
            return strpos(strtolower($product['name']), $query) !== false ||
                   strpos(strtolower($product['description']), $query) !== false;
        });
    }
}

if (!function_exists('renderStars')) {
    function renderStars($rating) {
        $full_stars = floor($rating);
        $has_half = ($rating - $full_stars) >= 0.5;
        
        $stars = str_repeat('★', $full_stars);
        if ($has_half && $full_stars < 5) {
            $stars .= '☆';
            $stars .= str_repeat('☆', 4 - $full_stars);
        } else {
            $stars .= str_repeat('☆', 5 - $full_stars);
        }
        
        return $stars;
    }
}

if (!function_exists('isProductInWishlist')) {
    function isProductInWishlist($productId) {
        if (!isset($_SESSION['wishlist'])) {
            $_SESSION['wishlist'] = [];
        }
        return in_array($productId, $_SESSION['wishlist']);
    }
}

// ============================================
// AUTHENTICATION FUNCTIONS
// ============================================

function registerUser($email, $password, $name, $confirmPassword) {
    global $authService;
    return $authService->register($email, $password, $name, $confirmPassword);
}

function loginUser($email, $password) {
    global $authService;
    return $authService->login($email, $password);
}

function logoutUser() {
    global $authService;
    return $authService->logout();
}

// ============================================
// CART FUNCTIONS
// ============================================

function syncCartToDb($sessionId, $userId, $cartItems) {
    global $cartService;
    $cartService->syncCartToDb($sessionId, $userId, $cartItems);
}

function loadCartFromDb($sessionId, $userId = null) {
    global $cartService;
    return $cartService->loadCartFromDb($sessionId, $userId);
}

function saveUserCart($userId, $cart) {
    global $cartService;
    $sessionId = session_id();
    $actualUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $cartService->syncCartToDb($sessionId, $actualUserId, $cart);
}

function loadUserCart($email) {
    global $cartService;
    if (isset($_SESSION['user_id'])) {
        return $cartService->loadCartFromDb(session_id(), $_SESSION['user_id']);
    }
    return [];
}

function initializeCart() {
    global $cartService;
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $sessionId = session_id();
    
    $dbCart = $cartService->loadCartFromDb($sessionId, $userId);
    
    if (!empty($dbCart)) {
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $pid => $item) {
                $dbCart[$pid] = $item;
            }
        }
        $_SESSION['cart'] = $dbCart;
    }
}

function initializeCartFromFile() {
    initializeCart();
}

// ============================================
// WISHLIST FUNCTIONS
// ============================================

function loadUserWishlist($userId) {
    global $wishlistService;
    return $wishlistService->loadUserWishlist($userId);
}

function saveUserWishlist($userId, $wishlist) {
    global $wishlistService;
    $wishlistService->saveUserWishlist($userId, $wishlist);
}

function initializeWishlist() {
    global $wishlistService;
    if (isLoggedIn() && isset($_SESSION['user_email'])) {
        $savedWishlist = $wishlistService->loadUserWishlist($_SESSION['user_email']);
        if (!empty($savedWishlist)) {
            $_SESSION['wishlist'] = $savedWishlist;
        }
    }
}

function initializeWishlistFromFile() {
    initializeWishlist();
}

// ============================================
// ORDER FUNCTIONS
// ============================================

function createOrder($userId, $orderData, $items, $deactivateCart = true) {
    global $orderService;
    return $orderService->createOrder($userId, $orderData, $items, $deactivateCart);
}

function getUserEmail($userId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}

function getUserOrders($userId) {
    global $orderService;
    return $orderService->getUserOrders($userId);
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

function calculateBulkDiscount($price, $quantity) {
    if ($quantity > 0) {
        $itemTotal = $price * $quantity;
        return $itemTotal * ($quantity / 100);
    }
    return 0;
}

// Initialize cart and wishlist if user is logged in
if (isLoggedIn()) {
    if (!isset($_SESSION['cart'])) {
        initializeCart();
    }
    if (!isset($_SESSION['wishlist'])) {
        initializeWishlist();
    }
}

// Initialize empty cart and wishlist if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}
