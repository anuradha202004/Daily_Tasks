<?php
require_once 'app/bootstrap.php';
$db = Core_Database::getInstance();
$stmt = $db->query("SELECT * FROM catalog_product_entity LIMIT 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
print_r(array_keys($row));
