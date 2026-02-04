<?php
/**
 * Admin Authentication & Helper Functions
 */

/**
 * Check if current user is admin
 */
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    
    $user = getCurrentUser();
    return isset($user['is_admin']) && $user['is_admin'] == true;
}

/**
 * Require admin access (redirect if not admin)
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /admin/login.php?error=access_denied');
        exit;
    }
}

/**
 * Get dashboard statistics
 */
function getAdminStats() {
    global $pdo;
    
    $stats = [];
    
    // Total products
    $stmt = $pdo->query("SELECT COUNT(*) FROM catalog_product_entity");
    $stats['total_products'] = $stmt->fetchColumn();
    
    // Total orders
    $stmt = $pdo->query("SELECT COUNT(*) FROM sales_order");
    $stats['total_orders'] = $stmt->fetchColumn();
    
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = FALSE");
    $stats['total_users'] = $stmt->fetchColumn();
    
    // Total revenue
    $stmt = $pdo->query("SELECT COALESCE(SUM(grand_total), 0) FROM sales_order");
    $stats['total_revenue'] = $stmt->fetchColumn();
    
    // Recent orders
    $stmt = $pdo->query("
        SELECT * FROM sales_order 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $stats['recent_orders'] = $stmt->fetchAll();
    
    // Low stock products
    $stmt = $pdo->query("
        SELECT e.entity_id, e.name, a.stock 
        FROM catalog_product_entity e
        JOIN catalog_product_attribute a ON e.entity_id = a.product_id
        WHERE a.stock < 10
        ORDER BY a.stock ASC
        LIMIT 5
    ");
    $stats['low_stock'] = $stmt->fetchAll();
    
    return $stats;
}
?>
