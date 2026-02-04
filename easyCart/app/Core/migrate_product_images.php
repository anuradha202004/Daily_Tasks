<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/db.php';

try {
    $pdo = getDBConnection();
    
    echo "=== Product Image Migration ===\n\n";
    
    // Get all products
    $stmt = $pdo->query("SELECT entity_id, name FROM catalog_product_entity ORDER BY entity_id");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($products) . " products\n\n";
    
    // Image mapping based on product name keywords
    $imageMap = [
        'laptop' => 'public/img/products/laptop.png',
        'headphone' => 'public/img/products/headphones.png',
        'watch' => 'public/img/products/smartwatch.png',
        'shoe' => 'public/img/products/sneakers.png',
        'sneaker' => 'public/img/products/sneakers.png'
    ];
    
    $defaultImages = [
        'public/img/products/laptop.png',
        'public/img/products/headphones.png',
        'public/img/products/smartwatch.png',
        'public/img/products/sneakers.png'
    ];
    
    // Clear existing images
    $pdo->exec("DELETE FROM catalog_product_image");
    echo "Cleared existing product images\n\n";
    
    // Insert images for each product
    $insertStmt = $pdo->prepare("
        INSERT INTO catalog_product_image (product_id, image_path, is_primary, position)
        VALUES (:product_id, :image_path, :is_primary, :position)
    ");
    
    $count = 0;
    foreach ($products as $product) {
        $productId = $product['entity_id'];
        $productName = strtolower($product['name']);
        $imagePath = null;
        
        // Try to match by keyword
        foreach ($imageMap as $keyword => $path) {
            if (strpos($productName, $keyword) !== false) {
                $imagePath = $path;
                break;
            }
        }
        
        // Fallback to rotating default images
        if (!$imagePath) {
            $imagePath = $defaultImages[$productId % count($defaultImages)];
        }
        
        // Insert into database
        try {
            $insertStmt->execute([
                ':product_id' => $productId,
                ':image_path' => $imagePath,
                ':is_primary' => true,
                ':position' => 1
            ]);
            $count++;
            echo "Product ID $productId ({$product['name']}): $imagePath\n";
        } catch (PDOException $e) {
            echo "ERROR for Product ID $productId: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n✅ Successfully migrated $count product images!\n";
    
} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage() . "\n");
}
?>
