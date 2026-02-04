<?php
require_once 'app/Core/db.php';
$pdo = getDBConnection();

$stmt = $pdo->query("
    SELECT column_name, data_type 
    FROM information_schema.columns 
    WHERE table_name = 'catalog_product_image'
    ORDER BY ordinal_position
");

echo "Columns in catalog_product_image:\n";
while ($row = $stmt->fetch()) {
    echo "  - {$row['column_name']} ({$row['data_type']})\n";
}
?>
