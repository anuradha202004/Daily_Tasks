<?php
/**
 * Data Management for EasyCart (Database Integrated)
 * Handles Products, Categories, Brands, and Orders via PostgreSQL
 */

require_once 'db.php';

// Initialize DB Connection
if (!isset($pdo)) {
    $pdo = getDBConnection();
}

// 1. Fetch Categories from Database
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

// 2. Fetch Brands from Database
$brands = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT brand as name FROM catalog_product_attribute WHERE brand IS NOT NULL");
    $i = 1;
    while ($row = $stmt->fetch()) {
        $brands[$i] = ['id' => $i, 'name' => $row['name']];
        $i++;
    }
} catch (PDOException $e) { 
    error_log("Brands Fetch Error: " . $e->getMessage());
}

// 3. Fetch Products from Database
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
        $row['price'] = (float)($row['price'] ?? 0);
        $row['stock'] = (int)($row['stock'] ?? 0);
        $row['rating'] = 4.5;
        $row['reviews'] = 10;
        $row['emoji'] = $row['emoji'] ?? '📦';
        
        // Get category for this product
        $catStmt = $pdo->prepare("SELECT category_id FROM catalog_category_products WHERE product_id = ? LIMIT 1");
        $catStmt->execute([$row['id']]);
        $catId = $catStmt->fetchColumn();
        $row['category_id'] = $catId ? (int)$catId : 1;
        $row['brand_id'] = 1;
        
        $products[$row['id']] = $row;
    }
} catch (PDOException $e) { 
    error_log("Products Fetch Error: " . $e->getMessage());
}

// Helper Functions
function getProductById($id) {
    global $products;
    return $products[$id] ?? null;
}

function getCategoryById($id) {
    global $categories;
    return $categories[$id] ?? null;
}

function getBrandById($id) {
    global $brands;
    return $brands[$id] ?? null;
}

function getProductsByCategory($category_id) {
    global $products;
    return array_filter($products, function($product) use ($category_id) {
        return $product['category_id'] == $category_id;
    });
}

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

function calculateBulkDiscount($price, $quantity) {
    if ($quantity > 0) {
        $itemTotal = $price * $quantity;
        return $itemTotal * ($quantity / 100);
    }
    return 0;
}

function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function isProductInWishlist($productId) {
    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }
    return in_array($productId, $_SESSION['wishlist']);
}

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

// Order Management (Database)
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
        
        $userEmail = $userId ? getUserEmail($userId) : ($orderData['customer_email'] ?? null);

        $stmt->execute([
            ':increment_id' => $orderData['order_number'],
            ':user_id' => $userId,
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
        
        // 2. Insert Order Items
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
            
            // Update Stock
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
        
        $pdo->commit();
        return true;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Order Creation Failed: " . $e->getMessage());
        return false;
    }
}

function getUserEmail($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}

// Cart and Wishlist (File-based for compatibility)
$dataDir = defined('STORAGE_PATH') ? STORAGE_PATH . '/data' : __DIR__ . '/../../storage/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

function getCartFilePath($userId) {
    global $dataDir;
    return $dataDir . '/cart_' . md5($userId) . '.json';
}

function getWishlistFilePath($userId) {
    global $dataDir;
    return $dataDir . '/wishlist_' . md5($userId) . '.json';
}

function loadUserCart($userId) {
    $cartFile = getCartFilePath($userId);
    if (file_exists($cartFile)) {
        $content = file_get_contents($cartFile);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }
    return [];
}

function saveUserCart($userId, $cart) {
    $cartFile = getCartFilePath($userId);
    file_put_contents($cartFile, json_encode($cart, JSON_PRETTY_PRINT));
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

function getUserOrders($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                o.id,
                o.increment_id as order_number,
                o.subtotal,
                o.tax_amount as tax,
                o.shipping_amount as shipping_cost,
                o.discount_amount as discount,
                o.grand_total as total,
                o.status,
                o.shipping_method,
                o.created_at
            FROM sales_order o
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Get User Orders Error: " . $e->getMessage());
        return [];
    }
}

function getOrderByNumber($orderNumber) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                o.*,
                a.firstname as first_name,
                a.lastname as last_name,
                a.email,
                a.telephone as phone,
                a.street as address,
                a.city,
                a.region as state,
                a.postcode as zip
            FROM sales_order o
            LEFT JOIN sales_order_address a ON o.id = a.parent_id
            WHERE o.increment_id = ?
        ");
        $stmt->execute([$orderNumber]);
        $order = $stmt->fetch();
        
        if ($order) {
            // Get order items
            $itemStmt = $pdo->prepare("
                SELECT * FROM sales_order_products WHERE order_id = ?
            ");
            $itemStmt->execute([$order['id']]);
            $order['items'] = $itemStmt->fetchAll();
        }
        
        return $order;
    } catch (PDOException $e) {
        error_log("Get Order Error: " . $e->getMessage());
        return null;
    }
}
?>
