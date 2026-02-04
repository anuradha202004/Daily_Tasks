<?php
require_once 'app/Core/db.php';
$pdo = getDBConnection();

$stmt = $pdo->query('SELECT * FROM catalog_product_image LIMIT 0');

echo "Actual columns in table:\n";
for($i=0; $i<$stmt->columnCount(); $i++) {
    $meta = $stmt->getColumnMeta($i);
    echo "  " . ($i+1) . ". " . $meta['name'] . "\n";
}
?>
