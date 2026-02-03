<?php
// seed_products.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db.php';

try {
    $pdo = getDBConnection();
    echo "<h1>Seeding 100 Products</h1>";

    // Fix Sequences first (in case manual inserts messed them up)
    try {
        $pdo->exec("SELECT setval('catalog_product_entity_entity_id_seq', (SELECT MAX(entity_id) FROM catalog_product_entity))");
    } catch (Exception $e) { /* Ignore if sequence doesn't exist or other minor error */ }

    // 1. Ensure we have enough categories
    $categories = [
        1 => 'Electronics',
        2 => 'Fashion', 
        3 => 'Home',
        4 => 'Sports',
        5 => 'Books',
        6 => 'Beauty'
    ];

    $checkCat = $pdo->prepare("SELECT COUNT(*) FROM catalog_category_entity WHERE entity_id = ?");
    
    // Insert missing categories
    foreach ($categories as $id => $name) {
        $checkCat->execute([$id]);
        if ($checkCat->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO catalog_category_entity (entity_id) VALUES (?)")->execute([$id]);
            $pdo->prepare("INSERT INTO catalog_category_attribute (category_id, name, slug, description) VALUES (?, ?, ?, ?) ON CONFLICT DO NOTHING")
                ->execute([$id, $name, strtolower($name), "Best $name products"]);
            echo "Created Category: $name<br>";
        }
    }

    // 2. Data Generators
    $adjectives = ['Premium', 'Deluxe', 'Basic', 'Pro', 'Ultra', 'Smart', 'Eco', 'Vintage', 'Modern', 'Compact'];
    $brands = ['Sony', 'Samsung', 'Apple', 'Nike', 'Adidas', 'IKEA', 'Penguin', 'LOréals', 'Generic', 'TechCo'];
    $colors = ['Black', 'White', 'Red', 'Blue', 'Green', 'Silver', 'Gold', 'Multicolor'];
    $sizes = ['S', 'M', 'L', 'XL', 'One Size', 'N/A'];
    
    $types = [
        1 => ['Headphones', 'Laptop', 'Smartphone', 'Speaker', 'Camera', 'Watch', 'Mouse', 'Keyboard', 'Monitor', 'Tablet'], 
        2 => ['T-Shirt', 'Jeans', 'Jacket', 'Sneakers', 'Hat', 'Socks', 'Dress', 'Scarf', 'Hoodie', 'Boots'], 
        3 => ['Lamp', 'Chair', 'Desk', 'Vase', 'Pillow', 'Rug', 'Clock', 'Planter', 'Mirror', 'Shelf'], 
        4 => ['Ball', 'Racket', 'Bat', 'Gloves', 'Helmet', 'Mat', 'Dumbbell', 'Bike', 'Tent', 'Backpack'], 
        5 => ['Novel', 'Textbook', 'Biography', 'Comic', 'Magazine', 'Notebook', 'Planner', 'Guide', 'Atlas', 'Journal'], 
        6 => ['Lipstick', 'Cream', 'Serum', 'Perfume', 'Shampoo', 'Soap', 'Mask', 'Lotion', 'Brush', 'Palette'] 
    ];

    $emojis = [
        1 => ['🎧', '💻', '📱', '🔊', '📷', '⌚', '🖱️', '⌨️', '🖥️', '📟'],
        2 => ['👕', '👖', '🧥', '👟', '🧢', '🧦', '👗', '🧣', '🧥', '👢'],
        3 => ['💡', '🪑', '🛋️', '🏺', '🛌', '🧶', '🕰️', '🪴', '🪞', '🪜'],
        4 => ['⚽', '🎾', '🏏', '🥊', '🪖', '🧘', '🏋️', '🚲', '⛺', '🎒'],
        5 => ['📕', '📘', '📗', '📙', '📓', '📔', '📒', '🗺️', '📚', '📰'],
        6 => ['💄', '🧴', '🧪', '👃', '🚿', '🧼', '🎭', '👐', '🖌️', '🎨']
    ];

    // Ensure emoji column exists
    $pdo->exec("ALTER TABLE catalog_product_attribute ADD COLUMN IF NOT EXISTS emoji VARCHAR(50)");

    // 3. Generate 100 Products
    $pdo->beginTransaction();

    $stmtEntity = $pdo->prepare("INSERT INTO catalog_product_entity (sku, name) VALUES (?, ?) RETURNING entity_id");
    $stmtAttr = $pdo->prepare("INSERT INTO catalog_product_attribute (product_id, price, description, color, size, brand, stock, emoji) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtImg = $pdo->prepare("INSERT INTO catalog_product_image (product_id, image_path, is_primary) VALUES (?, ?, TRUE)");
    $stmtCat = $pdo->prepare("INSERT INTO catalog_category_products (category_id, product_id) VALUES (?, ?)");

    for ($i = 1; $i <= 100; $i++) {
        $catId = rand(1, 6);
        $typeIndex = rand(0, 9);
        
        $type = $types[$catId][$typeIndex];
        $adj = $adjectives[array_rand($adjectives)];
        $brand = $brands[array_rand($brands)];
        
        $name = "$adj $type";
        $sku = strtoupper(substr($brand, 0, 3)) . '-' . strtoupper(substr($type, 0, 3)) . '-' . str_pad($i + 1000, 4, '0', STR_PAD_LEFT); 
        // offset i to avoid collision with manual SKUs if any (though manual were specific)
        
        // Random Price 10-500
        $price = rand(1000, 50000) / 100; // 10.00 to 500.00
        
        $desc = "This is a $adj quality $type from $brand. Great for daily use.";
        $color = $colors[array_rand($colors)];
        $size = $sizes[array_rand($sizes)];
        $stock = rand(0, 100);
        $emoji = $emojis[$catId][$typeIndex] ?? '📦';

        // Insert Entity
        $stmtEntity->execute([$sku, $name]);
        $pId = $stmtEntity->fetchColumn();

        // Insert Attribute
        $stmtAttr->execute([$pId, $price, $desc, $color, $size, $brand, $stock, $emoji]);
        
        // Insert Category Link
        $stmtCat->execute([$catId, $pId]);
    }

    $pdo->commit();
    echo "<p style='color:green'>✅ Successfully added 100 products.</p>";
    echo "<a href='products.php'>View Products</a>";

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die("Seeding failed: " . $e->getMessage());
}
?>
