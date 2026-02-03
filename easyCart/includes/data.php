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

// 1. Fetch Categories from Database (New Schema)
$categories = [];
try {
    $stmt = $pdo->query("
        SELECT 
            category_id as id, 
            name, 
            slug, 
            description 
        FROM catalog_category_attribute
        ORDER BY id
    ");
    while ($row = $stmt->fetch()) {
        $categories[$row['id']] = $row;
    }
} catch (PDOException $e) { 
    error_log("Categories Fetch Error: " . $e->getMessage());
}

// 2. Fetch Brands from Database (Extracted from product attributes in new schema)
$brands = [];
try {
    // In the new schema, brand is an attribute of the product. 
    // We can simulate the brands array by selecting distinct brands.
    $stmt = $pdo->query("SELECT DISTINCT brand as name FROM catalog_product_attribute WHERE brand IS NOT NULL");
    $i = 1;
    while ($row = $stmt->fetch()) {
        $brands[$i] = ['id' => $i, 'name' => $row['name']];
        $i++;
    }
} catch (PDOException $e) { /* Fail silently */ }

// 3. Fetch Products from Database (New Schema)
$products = [];
try {
    $stmt = $pdo->query("
        SELECT 
            e.entity_id as id, 
            e.sku, 
            e.name, 
            e.created_at,
            a.price, 
            a.description, 
            a.color, 
            a.size, 
            a.brand, 
            a.stock, 
            a.emoji,
            i.image_path as image
        FROM catalog_product_entity e
        LEFT JOIN catalog_product_attribute a ON e.entity_id = a.product_id
        LEFT JOIN catalog_product_image i ON e.entity_id = i.product_id AND i.is_primary = TRUE
        ORDER BY e.entity_id
    ");
    
    while ($row = $stmt->fetch()) {
        // Cast types
        $row['price'] = (float)($row['price'] ?? 0);
        $row['stock'] = (int)($row['stock'] ?? 0);
        // Default values for missing columns in new schema but expected by UI
        $row['rating'] = 4.5; // Placeholder
        $row['reviews'] = 10; // Placeholder
        $row['emoji'] = $row['emoji'] ?? '📦'; // Use fetched emoji or fallback
        
        // Map category_id 
        // We need to fetch the category for this product
        // This query inside loop is inefficient but simple for migration
        // Or we can join in the main query
        $catStmt = $pdo->prepare("SELECT category_id FROM catalog_category_products WHERE product_id = ? LIMIT 1");
        $catStmt->execute([$row['id']]);
        $catId = $catStmt->fetchColumn();
        $row['category_id'] = $catId ? (int)$catId : 1;
        $row['brand_id'] = 1; // Placeholder
        
        $products[$row['id']] = $row;
    }
} catch (PDOException $e) { 
    error_log("Products Fetch Error: " . $e->getMessage());
}

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

// ============================================
// ORDER MANAGEMENT (DB - New Schema)
// ============================================

/**
 * Create a new order in the database (sales_order)
 */
function createOrder($userId, $orderData, $items) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // 1. Insert Sales Order
        $stmt = $pdo->prepare("
            INSERT INTO sales_order 
            (increment_id, user_id, subtotal, tax_amount, shipping_amount, discount_amount, grand_total, status, shipping_method, customer_email, created_at)
            VALUES 
            (:increment_id, :user_id, :subtotal, :tax_amount, :shipping_amount, :discount_amount, :grand_total, :status, :shipping_method, :customer_email, NOW())
            RETURNING id
        ");
        
        // Use customer email if userId is null (guest) or just store it anyway
        $userEmail = $userId ? getUserEmail($userId) : ($orderData['customer_email'] ?? null);

        $stmt->execute([
            ':increment_id' => $orderData['order_number'],
            ':user_id' => $userId, // Can be NULL for guest
            ':subtotal' => $orderData['subtotal'],
            ':tax_amount' => $orderData['tax'],
            ':shipping_amount' => $orderData['shipping_cost'],
            ':discount_amount' => $orderData['discount'],
            ':grand_total' => $orderData['total'],
            ':status' => $orderData['status'],
            ':shipping_method' => $orderData['shipping_method'],
            ':customer_email' => $userEmail
        ]);
        
        $orderId = $stmt->fetchColumn();
        
        // 2. Insert Order Items (sales_order_products)
        $itemStmt = $pdo->prepare("
            INSERT INTO sales_order_products (order_id, product_id, sku, name, price, qty_ordered, row_total)
            VALUES (:order_id, :product_id, :sku, :name, :price, :qty_ordered, :row_total)
        ");
        
        foreach ($items as $item) {
            $itemStmt->execute([
                ':order_id' => $orderId,
                ':product_id' => $item['product']['id'],
                ':sku' => $item['product']['sku'],
                ':name' => $item['product']['name'],
                ':price' => $item['product']['price'],
                ':qty_ordered' => $item['quantity'],
                ':row_total' => $item['itemTotal']
            ]);
            
            // Optional: Update Stock in catalog_product_attribute
            $updateStock = $pdo->prepare("UPDATE catalog_product_attribute SET stock = stock - :qty WHERE product_id = :pid");
            $updateStock->execute([':qty' => $item['quantity'], ':pid' => $item['product']['id']]);
        }
        
        // 3. Insert Order Address
        if (isset($orderData['customer'])) {
            $cust = $orderData['customer'];
            $addrStmt = $pdo->prepare("
                INSERT INTO sales_order_address (parent_id, address_type, firstname, lastname, email, telephone, street, city, region, postcode)
                VALUES (:pid, 'shipping', :fn, :ln, :email, :tel, :str, :city, :reg, :zip)
            ");
            $addrStmt->execute([
                ':pid' => $orderId,
                ':fn' => $cust['first_name'],
                ':ln' => $cust['last_name'],
                ':email' => $cust['email'],
                ':tel' => $cust['phone'],
                ':str' => $cust['address'],
                ':city' => $cust['city'],
                ':reg' => $cust['state'],
                ':zip' => $cust['zip']
            ]);
        }
        
        // 4. Deactivate used cart
        if (isset($_SESSION['cart_id'])) {
             $updateCart = $pdo->prepare("UPDATE sales_cart SET is_active = FALSE WHERE id = ?");
             $updateCart->execute([$_SESSION['cart_id']]);
             unset($_SESSION['cart_id']);
        }
        
        $pdo->commit();
        return true;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Order Creation Failed: " . $e->getMessage());
        return false;
    }
}

// Helper to get user email (if simple auth used)
function getUserEmail($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}


// ============================================
// CART PERSISTENCE FUNCTIONS (DB - sales_cart)
// ============================================

/**
 * Sync Session Cart to Database
 * Called after every cart modification
 */
function syncCartToDb($sessionId, $userId, $cartItems) {
    global $pdo;
    
    try {
        // 1. Check if we already have an active cart_id in session
        $cartId = isset($_SESSION['cart_id']) ? $_SESSION['cart_id'] : null;
        
        if (!$cartId) {
            // Check if there is an active cart for this user (if logged in) or session_id
            if ($userId) {
                $stmt = $pdo->prepare("SELECT id FROM sales_cart WHERE user_id = ? AND is_active = TRUE ORDER BY id DESC LIMIT 1");
                $stmt->execute([$userId]);
            } else {
                $stmt = $pdo->prepare("SELECT id FROM sales_cart WHERE session_id = ? AND is_active = TRUE ORDER BY id DESC LIMIT 1");
                $stmt->execute([$sessionId]);
            }
            $cartId = $stmt->fetchColumn();
        }

        // 2. If no active cart, create one
        if (!$cartId) {
            if (empty($cartItems)) return; // Don't create empty cart in DB
            
            $stmt = $pdo->prepare("INSERT INTO sales_cart (session_id, user_id, is_active) VALUES (:sid, :uid, TRUE) RETURNING id");
            $stmt->execute([':sid' => $sessionId, ':uid' => $userId]);
            $cartId = $stmt->fetchColumn();
            $_SESSION['cart_id'] = $cartId;
        } else {
            // Ensure session matches
            $_SESSION['cart_id'] = $cartId;
            // If user logged in, link cart
            if ($userId) {
                $pdo->prepare("UPDATE sales_cart SET user_id = ? WHERE id = ?")->execute([$userId, $cartId]);
            }
        }

        // 3. Sync Products
        // Strategy: Delete all items in this cart and re-insert (Simplest for sync). 
        // For production with huge carts, this might be optimized to diff.
        
        $pdo->beginTransaction();
        
        // Remove existing items
        $pdo->prepare("DELETE FROM sales_cart_products WHERE cart_id = ?")->execute([$cartId]);
        
        // Insert current items
        if (!empty($cartItems)) {
            $insertStmt = $pdo->prepare("INSERT INTO sales_cart_products (cart_id, product_id, quantity) VALUES (?, ?, ?)");
            foreach ($cartItems as $item) {
                $insertStmt->execute([$cartId, $item['product_id'], $item['quantity']]);
            }
        }
        
        $pdo->commit();
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log("Cart Sync Failed: " . $e->getMessage());
    }
}

/**
 * Load Cart from Database
 * Called on login or session start
 */
function loadCartFromDb($sessionId, $userId = null) {
    global $pdo;
    
    try {
        $cartId = null;
        
        // Try finding by User ID first
        if ($userId) {
            $stmt = $pdo->prepare("SELECT id FROM sales_cart WHERE user_id = ? AND is_active = TRUE ORDER BY id DESC LIMIT 1");
            $stmt->execute([$userId]);
            $cartId = $stmt->fetchColumn();
        }
        
        // If not found, try by Session ID
        if (!$cartId) {
            $stmt = $pdo->prepare("SELECT id FROM sales_cart WHERE session_id = ? AND is_active = TRUE ORDER BY id DESC LIMIT 1");
            $stmt->execute([$sessionId]);
            $cartId = $stmt->fetchColumn();
        }
        
        if ($cartId) {
            $_SESSION['cart_id'] = $cartId;
            
            // Load items
            $stmt = $pdo->prepare("SELECT product_id, quantity FROM sales_cart_products WHERE cart_id = ?");
            $stmt->execute([$cartId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $cart = [];
            foreach ($items as $item) {
                $cart[$item['product_id']] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity']
                ];
            }
            return $cart;
        }
    } catch (PDOException $e) {
        error_log("Cart Load Failed: " . $e->getMessage());
    }
    
    return [];
}

// ============================================
// WISHLIST PERSISTENCE (File-based for now)
// ============================================

function getWishlistFilePath($userId) {
    // Legacy file support for wishlist
    $dataDir = __DIR__ . '/../data';
    if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);
    return $dataDir . '/wishlist_' . md5($userId) . '.json';
}

function loadUserWishlist($userId) {
    $wishlistFile = getWishlistFilePath($userId);
    if (file_exists($wishlistFile)) {
        $content = file_get_contents($wishlistFile);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }
    return [];
}

function saveUserWishlist($userId, $wishlist) {
    $wishlistFile = getWishlistFilePath($userId);
    file_put_contents($wishlistFile, json_encode($wishlist, JSON_PRETTY_PRINT));
}

// ============================================
// INITIALIZATION HELPERS
// ============================================

// Initialize cart from DB
function initializeCart() {
    // If we have a logged in user, try to load their cart
    // OR if we have a session but no cart loaded, check DB
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $sessionId = session_id();
    
    // Only load if not already loaded or if we just logged in
    // For now, let's just merge/load.
    $dbCart = loadCartFromDb($sessionId, $userId);
    
    if (!empty($dbCart)) {
        // If we have items in session that are NOT in DB, we should probably merge them?
        // For simplicity, DB wins or we merge.
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            // Merge: Session items override DB items or add to them
            // In a real app, you'd ask the user. Here, let's assume session (most recent action) + DB
            foreach ($_SESSION['cart'] as $pid => $item) {
                $dbCart[$pid] = $item;
            }
        }
        $_SESSION['cart'] = $dbCart;
    }
}

// Initialize wishlist from file
function initializeWishlist() {
    if (isLoggedIn() && isset($_SESSION['user_email'])) {
        $savedWishlist = loadUserWishlist($_SESSION['user_email']);
        if (!empty($savedWishlist)) {
            $_SESSION['wishlist'] = $savedWishlist;
        }
    }
}

// Check for legacy calls just in case
if (!function_exists('saveUserCart')) {
    function saveUserCart($userId, $cart) {
        // Redirect to DB sync
        syncCartToDb(session_id(), isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null, $cart);
    }
}
if (!function_exists('loadUserCart')) {
    function loadUserCart($email) {
         // Attempt to find user ID from session if available, otherwise just use session ID based load?
         // This is mostly for auth.php which calls it right after login.
         // If logged in, we should have user_id in session.
         if (isset($_SESSION['user_id'])) {
             return loadCartFromDb(session_id(), $_SESSION['user_id']);
         }
         return [];
    }
}
if (!function_exists('initializeCartFromFile')) {
    function initializeCartFromFile() {
        initializeCart();
    }
}
if (!function_exists('initializeWishlistFromFile')) {
    function initializeWishlistFromFile() {
        initializeWishlist();
    }
}
?>
