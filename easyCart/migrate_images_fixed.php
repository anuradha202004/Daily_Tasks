<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'app/Core/db.php';

$pdo = getDBConnection();

echo "=== Product Image Migration (Fixed) ===\n\n";

try {
    // Get all products
    $products = $pdo->query("SELECT entity_id, name FROM catalog_product_entity ORDER BY entity_id")->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($products) . " products\n\n";
    
    // Clear existing images
    $pdo->exec("TRUNCATE TABLE catalog_product_image");
    echo "Cleared existing images\n\n";
    
    // Image mapping
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
    
    // Prepare insert with ONLY the columns that exist
    $insertStmt = $pdo->prepare("
        INSERT INTO catalog_product_image (product_id, image_path, is_primary)
        VALUES (?, ?, ?)
    ");
    
    $success = 0;
    foreach ($products as $product) {
        $pid = $product['entity_id'];
        $name = strtolower($product['name']);
        $imagePath = null;
        
        // Match by keyword
        foreach ($imageMap as $keyword => $path) {
            if (strpos($name, $keyword) !== false) {
                $imagePath = $path;
                break;
            }
        }
        
        // Fallback
        if (!$imagePath) {
            $imagePath = $defaultImages[$pid % count($defaultImages)];
        }
        
        $insertStmt->execute([$pid, $imagePath, true]);
        $success++;
        
        if ($success <= 5 || $success % 20 == 0) {
            echo "  ✓ Product $pid: $imagePath\n";
        }
    }
    
    echo "\n✅ Successfully migrated $success product images!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
