<?php
require_once __DIR__ . '/db.php';

try {
    $pdo = getDBConnection();
    
    // Create catalog_product_image table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS catalog_product_image (
            id SERIAL PRIMARY KEY,
            product_id INTEGER NOT NULL,
            image_path VARCHAR(255) NOT NULL,
            is_primary BOOLEAN DEFAULT FALSE,
            position INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_product_image_product FOREIGN KEY (product_id) 
                REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE
        );
    ");
    
    echo "✅ catalog_product_image table created successfully!\n";
    
    // Create index for faster lookups
    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_product_image_product_id 
        ON catalog_product_image(product_id);
    ");
    
    echo "✅ Index created successfully!\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
