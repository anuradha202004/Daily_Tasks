<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'app/Core/db.php';

$pdo = getDBConnection();

echo "Simple test insertion...\n";

try {
    // Clear table
    $pdo->exec("TRUNCATE TABLE catalog_product_image");
    
    // Insert ONE test record using exact column order
    $stmt = $pdo->prepare("
        INSERT INTO catalog_product_image (product_id, image_path, is_primary, position, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    $result = $stmt->execute([1, 'public/img/products/laptop.png', true, 1]);
    
    if ($result) {
        echo "✅ Insert successful!\n";
        
        // Verify
        $count = $pdo->query("SELECT COUNT(*) FROM catalog_product_image")->fetchColumn();
        echo "Count: $count\n";
        
        // Show the record
        $row = $pdo->query("SELECT * FROM catalog_product_image LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        print_r($row);
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
