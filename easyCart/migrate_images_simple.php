<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'app/Core/db.php';

$pdo = getDBConnection();

echo "Testing image insertion...\n\n";

// Test 1: Insert a single image
try {
    $pdo->exec("DELETE FROM catalog_product_image");
    echo "Cleared existing images\n";
    
    $stmt = $pdo->prepare("
        INSERT INTO catalog_product_image (product_id, image_path, is_primary, position)
        VALUES (1, 'public/img/products/laptop.png', true, 1)
    ");
    $stmt->execute();
    echo "✅ Test insert successful!\n";
    
    // Verify
    $count = $pdo->query("SELECT COUNT(*) FROM catalog_product_image")->fetchColumn();
    echo "Images in DB: $count\n\n";
    
    if ($count > 0) {
        echo "SUCCESS! Now running full migration...\n\n";
        
        // Get all products
        $products = $pdo->query("SELECT entity_id, name FROM catalog_product_entity")->fetchAll(PDO::FETCH_ASSOC);
        echo "Found " . count($products) . " products\n";
        
        // Clear again
        $pdo->exec("DELETE FROM catalog_product_image");
        
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
        
        $insertStmt = $pdo->prepare("
            INSERT INTO catalog_product_image (product_id, image_path, is_primary, position)
            VALUES (:pid, :path, true, 1)
        ");
        
        $success = 0;
        $errors = 0;
        
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
            
            try {
                $insertStmt->execute([':pid' => $pid, ':path' => $imagePath]);
                $success++;
                if ($success <= 5) {
                    echo "  ✓ Product $pid: $imagePath\n";
                }
            } catch (Exception $e) {
                $errors++;
                if ($errors <= 3) {
                    echo "  ✗ Product $pid ERROR: " . $e->getMessage() . "\n";
                }
            }
        }
        
        echo "\n✅ Migration complete: $success images inserted, $errors errors\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
