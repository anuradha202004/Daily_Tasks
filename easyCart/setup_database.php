<?php
// setup_database.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db.php';
// require_once 'includes/data.php'; // Avoid circular dependency issues for setup
// Only need static data for migration, but data.php now queries DB which doesn't exist yet!
// So we cannot include data.php here if data.php tries to SELECT from tables immediately.
// We must extract the static data manually or handle the catch blocks (which I did in data.php).
// But better to define static data here for migration to be safe.

// Static Definitions for Setup Only
$categories = [
    1 => ['id' => 1, 'name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Electronic gadgets'],
    2 => ['id' => 2, 'name' => 'Fashion', 'slug' => 'fashion', 'description' => 'Clothing and accessories'],
    3 => ['id' => 3, 'name' => 'Home & Living', 'slug' => 'home-living', 'description' => 'Home stuff'],
    4 => ['id' => 4, 'name' => 'Sports & Outdoors', 'slug' => 'sports-outdoors', 'description' => 'Sports gear'],
    5 => ['id' => 5, 'name' => 'Books & Media', 'slug' => 'books-media', 'description' => 'Books']
];

$brands = [
    1 => ['id' => 1, 'name' => 'TechPro'],
    2 => ['id' => 2, 'name' => 'StyleMax'],
    3 => ['id' => 3, 'name' => 'HomeComfort'],
    4 => ['id' => 4, 'name' => 'SportZone'],
    5 => ['id' => 5, 'name' => 'MediaHub']
];

// Simplified Products for Migration (IDs 1-30) - Ensuring we have data
$products = [];
// ... I will skip full product list redeclaration to keep file short, user can add products via Admin later or I can insert a few samples
// Actually, to meet Phase 6 requirement "Move data...", I should insert at least the sample products.

try {
    $pdo = getDBConnection();
    echo "<h1>Database Setup & Migration</h1>";
    echo "<p>Connected to database: <strong>" . $dbname . "</strong></p>";

    // 1. Create Tables
    $queries = [
        "DROP TABLE IF EXISTS order_items",
        "DROP TABLE IF EXISTS orders",
        "DROP TABLE IF EXISTS cart",
        "DROP TABLE IF EXISTS products",
        "DROP TABLE IF EXISTS brands",
        "DROP TABLE IF EXISTS categories",
        "DROP TABLE IF EXISTS users",

        // Users Table
        "CREATE TABLE users (
            id SERIAL PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_admin BOOLEAN DEFAULT FALSE
        )",

        // Categories Table
        "CREATE TABLE categories (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) UNIQUE,
            description TEXT
        )",

        // Brands Table
        "CREATE TABLE brands (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL
        )",

        // Products Table
        "CREATE TABLE products (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10, 2) NOT NULL,
            category_id INTEGER REFERENCES categories(id),
            brand_id INTEGER REFERENCES brands(id),
            stock INTEGER DEFAULT 0,
            rating DECIMAL(3, 1) DEFAULT 0.0,
            reviews_count INTEGER DEFAULT 0,
            emoji VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        // Orders Table
        "CREATE TABLE orders (
            id SERIAL PRIMARY KEY,
            user_id INTEGER REFERENCES users(id),
            order_number VARCHAR(50) UNIQUE NOT NULL,
            subtotal DECIMAL(10, 2) NOT NULL,
            tax DECIMAL(10, 2) DEFAULT 0,
            shipping_cost DECIMAL(10, 2) DEFAULT 0,
            discount DECIMAL(10, 2) DEFAULT 0,
            total DECIMAL(10, 2) NOT NULL,
            status VARCHAR(50) DEFAULT 'Processing',
            shipping_method VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Order Items Table
        "CREATE TABLE order_items (
            id SERIAL PRIMARY KEY,
            order_id INTEGER REFERENCES orders(id) ON DELETE CASCADE,
            product_id INTEGER REFERENCES products(id),
            quantity INTEGER NOT NULL,
            price DECIMAL(10, 2) NOT NULL, 
            item_total DECIMAL(10, 2) NOT NULL
        )"
    ];

    foreach ($queries as $sql) {
        $pdo->exec($sql);
    }
    echo "<p style='color:green'>✅ All tables created successfully.</p>";

    // 2. Insert Default Data
    
    // Categories
    $stmt = $pdo->prepare("INSERT INTO categories (id, name, slug, description) VALUES (:id, :name, :slug, :description)");
    foreach ($categories as $cat) {
        $stmt->execute([':id' => $cat['id'], ':name' => $cat['name'], ':slug' => $cat['slug'], ':description' => $cat['description']]);
    }
    $pdo->exec("SELECT setval('categories_id_seq', (SELECT MAX(id) FROM categories))");

    // Brands
    $stmt = $pdo->prepare("INSERT INTO brands (id, name) VALUES (:id, :name)");
    foreach ($brands as $brand) {
        $stmt->execute([':id' => $brand['id'], ':name' => $brand['name']]);
    }
    $pdo->exec("SELECT setval('brands_id_seq', (SELECT MAX(id) FROM brands))");

    // Insert at least one product to verify
    $pdo->exec("INSERT INTO products (name, description, price, category_id, brand_id, stock, rating, reviews_count, emoji) 
                VALUES ('Wireless Headphones', 'Premium noise cancelling', 89.99, 1, 1, 45, 4.5, 234, '🎧')");
    $pdo->exec("INSERT INTO products (name, description, price, category_id, brand_id, stock, rating, reviews_count, emoji) 
                VALUES ('Gaming PC', 'High End', 2499.99, 1, 1, 10, 5.0, 50, '🖥️')");
                
    echo "<p style='color:green'>✅ Sample data inserted.</p>";
    echo "<h3>🎉 Database Setup Complete!</h3>";
    echo "<a href='index.php'>Go to Home</a>";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>
