<?php
require_once 'app/Core/db.php';
$pdo = getDBConnection();
$count = $pdo->query('SELECT COUNT(*) FROM catalog_product_entity')->fetchColumn();
echo "Products in database: $count\n";
?>
