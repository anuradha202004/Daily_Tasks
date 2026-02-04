<?php
// Test Database Connection
require_once 'includes/db.php';

echo "<h2>Testing PostgreSQL Connection</h2>";

try {
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test tables
    echo "<h3>Checking Tables:</h3>";
    
    $tables = [
        'users',
        'catalog_product_entity',
        'catalog_product_attribute',
        'catalog_category_attribute',
        'catalog_category_products',
        'sales_order',
        'sales_order_products',
        'sales_order_address'
    ];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch();
            echo "<p style='color: green;'>✅ Table '$table' exists - {$result['count']} rows</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Table '$table' - Error: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Connection failed: " . $e->getMessage() . "</p>";
}
?>
