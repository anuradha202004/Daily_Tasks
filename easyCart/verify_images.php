<?php
require_once 'app/Core/db.php';

$pdo = getDBConnection();

// Check if images were inserted
$stmt = $pdo->query("SELECT COUNT(*) as count FROM catalog_product_image");
$count = $stmt->fetchColumn();

echo "Total images in database: $count\n\n";

if ($count > 0) {
    echo "Sample images:\n";
    $stmt = $pdo->query("
        SELECT i.product_id, e.name, i.image_path, i.is_primary 
        FROM catalog_product_image i
        JOIN catalog_product_entity e ON i.product_id = e.entity_id
        LIMIT 10
    ");
    
    while ($row = $stmt->fetch()) {
        printf("ID: %d | %s | %s | Primary: %s\n", 
            $row['product_id'], 
            $row['name'], 
            $row['image_path'],
            $row['is_primary'] ? 'Yes' : 'No'
        );
    }
}
?>
