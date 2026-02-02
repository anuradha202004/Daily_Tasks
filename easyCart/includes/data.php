<?php
/**
 * Data Management for EasyCart (Database Integrated)
 * Handles Products, Categories, Brands, and Orders via PostgreSQL
 */

require_once 'db.php';
require_once 'auth.php'; // Needed for User ID in cart functions if used

// Initialize DB Connection
if (!isset($pdo)) {
    $pdo = getDBConnection();
}

// 1. Fetch Categories from Database
$categories = [];
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY id");
    while ($row = $stmt->fetch()) {
        $categories[$row['id']] = $row;
    }
} catch (PDOException $e) { /* Fail silently or log */ }

// 2. Fetch Brands from Database
$brands = [];
try {
    $stmt = $pdo->query("SELECT * FROM brands ORDER BY id");
    while ($row = $stmt->fetch()) {
        $brands[$row['id']] = $row;
    }
} catch (PDOException $e) { /* Fail silently */ }

// 3. Fetch Products from Database
$products = [];
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id");
    while ($row = $stmt->fetch()) {
        // Cast types to match original static data structure
        $row['price'] = (float)$row['price'];
        $row['stock'] = (int)$row['stock'];
        $row['rating'] = (float)$row['rating'];
        $row['reviews'] = (int)$row['reviews_count']; // Map reviews_count back to 'reviews' key
        unset($row['reviews_count']);
        
        $products[$row['id']] = $row;
    }
} catch (PDOException $e) { /* Fail silently */ }

// Placeholder for orders (for backward compatibility if anything accessed $orders directly, though mostly unused)
$orders = []; 


// ============================================
// HELPER FUNCTIONS
// ============================================

// Helper function to get product by ID
function getProductById($id) {
    global $products;
    return $products[$id] ?? null;
}

// Helper function to get category by ID
function getCategoryById($id) {
    global $categories;
    return $categories[$id] ?? null;
}

// Helper function to get brand by ID
function getBrandById($id) {
    global $brands;
    return $brands[$id] ?? null;
}

// Helper function to get all products by category
function getProductsByCategory($category_id) {
    global $products;
    return array_filter($products, function($product) use ($category_id) {
        return $product['category_id'] == $category_id;
    });
}

// Helper function to search products
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

// Helper function to calculate bulk discount
function calculateBulkDiscount($price, $quantity) {
    // Bulk discount: 1% discount per item (e.g. 2 items = 2%, 5 items = 5%)
    // Matches logic in js/checkout.js
    if ($quantity > 0) {
        // Calculate total price for these items
        $itemTotal = $price * $quantity;
        // Discount is itemTotal * (quantity in percent)
        // e.g. 5 items = $itemTotal * 0.05
        return $itemTotal * ($quantity / 100);
    }
    return 0;
}

// Helper function to format price
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Helper function to check if product is in wishlist
function isProductInWishlist($productId) {
    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }
    return in_array($productId, $_SESSION['wishlist']);
}

// Helper function to render star rating
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

// ============================================
// ORDER MANAGEMENT (DB)
// ============================================

/**
 * Create a new order in the database
 */
function createOrder($userId, $orderData, $items) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // 1. Insert Order
        $stmt = $pdo->prepare("
            INSERT INTO orders 
            (user_id, order_number, subtotal, tax, shipping_cost, discount, total, status, shipping_method, created_at)
            VALUES 
            (:user_id, :order_number, :subtotal, :tax, :shipping_cost, :discount, :total, :status, :shipping_method, NOW())
            RETURNING id
        ");
        
        $stmt->execute([
            ':user_id' => $userId, // Can be NULL for guest checkout
            ':order_number' => $orderData['order_number'],
            ':subtotal' => $orderData['subtotal'],
            ':tax' => $orderData['tax'],
            ':shipping_cost' => $orderData['shipping_cost'],
            ':discount' => $orderData['discount'], // Includes bulk + promo
            ':total' => $orderData['total'],
            ':status' => $orderData['status'],
            ':shipping_method' => $orderData['shipping_method']
        ]);
        
        $orderId = $stmt->fetchColumn();
        
        // 2. Insert Order Items
        $itemStmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price, item_total)
            VALUES (:order_id, :product_id, :quantity, :price, :item_total)
        ");
        
        foreach ($items as $item) {
            $itemStmt->execute([
                ':order_id' => $orderId,
                ':product_id' => $item['product']['id'],
                ':quantity' => $item['quantity'],
                ':price' => $item['product']['price'],
                ':item_total' => $item['itemTotal']
            ]);
            
            // Optional: Update Stock
            // $pdo->exec("UPDATE products SET stock = stock - {$item['quantity']} WHERE id = {$item['product']['id']}");
        }
        
        $pdo->commit();
        return true;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Order Creation Failed: " . $e->getMessage());
        return false;
    }
}


// ============================================
// CART & WISHLIST PERSISTENCE FUNCTIONS
// ============================================

// Get cart data file path for a user
function getCartFilePath($userId) {
    $dataDir = __DIR__ . '/../data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    return $dataDir . '/cart_' . md5($userId) . '.json';
}

// Get wishlist data file path for a user
function getWishlistFilePath($userId) {
    $dataDir = __DIR__ . '/../data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    return $dataDir . '/wishlist_' . md5($userId) . '.json';
}

// Load cart from file for logged-in user
function loadUserCart($userId) {
    $cartFile = getCartFilePath($userId);
    
    if (file_exists($cartFile) && filesize($cartFile) > 0) {
        $fileContent = file_get_contents($cartFile);
        $cart = json_decode($fileContent, true);
        
        if (is_array($cart)) {
            return $cart;
        }
    }
    
    return [];
}

// Save cart to file for logged-in user
function saveUserCart($userId, $cart) {
    $cartFile = getCartFilePath($userId);
    $jsonData = json_encode($cart, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents($cartFile, $jsonData);
}

// Load wishlist from file for logged-in user
function loadUserWishlist($userId) {
    $wishlistFile = getWishlistFilePath($userId);
    
    if (file_exists($wishlistFile) && filesize($wishlistFile) > 0) {
        $fileContent = file_get_contents($wishlistFile);
        $wishlist = json_decode($fileContent, true);
        
        if (is_array($wishlist)) {
            return $wishlist;
        }
    }
    
    return [];
}

// Save wishlist to file for logged-in user
function saveUserWishlist($userId, $wishlist) {
    $wishlistFile = getWishlistFilePath($userId);
    $jsonData = json_encode($wishlist, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents($wishlistFile, $jsonData);
}

// Initialize cart from file (called in index.php)
function initializeCartFromFile() {
    if (isLoggedIn() && isset($_SESSION['user_email'])) {
        $savedCart = loadUserCart($_SESSION['user_email']);
        if (!empty($savedCart)) {
            $_SESSION['cart'] = $savedCart;
        }
    }
}

// Initialize wishlist from file (called in index.php)
function initializeWishlistFromFile() {
    if (isLoggedIn() && isset($_SESSION['user_email'])) {
        $savedWishlist = loadUserWishlist($_SESSION['user_email']);
        if (!empty($savedWishlist)) {
            $_SESSION['wishlist'] = $savedWishlist;
        }
    }
}
?>
