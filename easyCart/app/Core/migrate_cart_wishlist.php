<?php
require_once __DIR__ . '/db.php';

try {
    $pdo = getDBConnection();
    
    // 1. Create Wishlist Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS wishlist (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT uk_wishlist_user_product UNIQUE (user_id, product_id)
        );
    ");
    echo "Wishlist table created/verified.\n";
    
    // 2. Create Cart Tables
    // For simplicity, we'll store cart items directly linked to user, 
    // or use a structured cart parent -> items child relationship.
    // Let's go with a simple User -> Items mapping for now since the app 
    // mainly treats cart as a list of items.
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS checkout_cart (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            quantity INTEGER DEFAULT 1,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT uk_cart_user_product UNIQUE (user_id, product_id)
        );
    ");
    echo "Checkout Cart table created/verified.\n";

} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage());
}
?>
