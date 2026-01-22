<?php
/**
 * Static Data File for EasyCart
 * Contains all product, category, and brand data
 */

// Categories Data
$categories = [
    1 => [
        'id' => 1,
        'name' => 'Electronics',
        'slug' => 'electronics',
        'description' => 'Electronic gadgets and devices'
    ],
    2 => [
        'id' => 2,
        'name' => 'Fashion',
        'slug' => 'fashion',
        'description' => 'Clothing and accessories'
    ],
    3 => [
        'id' => 3,
        'name' => 'Home & Living',
        'slug' => 'home-living',
        'description' => 'Home and lifestyle products'
    ],
    4 => [
        'id' => 4,
        'name' => 'Sports & Outdoors',
        'slug' => 'sports-outdoors',
        'description' => 'Sports and outdoor equipment'
    ],
    5 => [
        'id' => 5,
        'name' => 'Books & Media',
        'slug' => 'books-media',
        'description' => 'Books and multimedia content'
    ]
];

// Brands Data
$brands = [
    1 => ['id' => 1, 'name' => 'TechPro'],
    2 => ['id' => 2, 'name' => 'StyleMax'],
    3 => ['id' => 3, 'name' => 'HomeComfort'],
    4 => ['id' => 4, 'name' => 'SportZone'],
    5 => ['id' => 5, 'name' => 'MediaHub']
];

// Products Data
$products = [
    1 => [
        'id' => 1,
        'name' => 'Wireless Headphones',
        'description' => 'Premium wireless headphones with noise cancellation',
        'price' => 89.99,
        'category_id' => 1,
        'brand_id' => 1,
        'stock' => 45,
        'rating' => 4.5,
        'reviews' => 234,
        'emoji' => '🎧'
    ],
    2 => [
        'id' => 2,
        'name' => 'Smart Watch',
        'description' => 'Feature-rich smartwatch with health tracking',
        'price' => 199.99,
        'category_id' => 1,
        'brand_id' => 1,
        'stock' => 30,
        'rating' => 4.7,
        'reviews' => 189,
        'emoji' => '⌚'
    ],
    3 => [
        'id' => 3,
        'name' => 'Running Shoes',
        'description' => 'Comfortable running shoes for all terrains',
        'price' => 79.99,
        'category_id' => 2,
        'brand_id' => 2,
        'stock' => 120,
        'rating' => 4.3,
        'reviews' => 456,
        'emoji' => '👟'
    ],
    4 => [
        'id' => 4,
        'name' => 'Designer Handbag',
        'description' => 'Elegant designer handbag with premium materials',
        'price' => 149.99,
        'category_id' => 2,
        'brand_id' => 2,
        'stock' => 25,
        'rating' => 4.6,
        'reviews' => 98,
        'emoji' => '👜'
    ],
    5 => [
        'id' => 5,
        'name' => 'Coffee Maker',
        'description' => 'Automatic coffee maker with programmable features',
        'price' => 69.99,
        'category_id' => 3,
        'brand_id' => 3,
        'stock' => 55,
        'rating' => 4.4,
        'reviews' => 312,
        'emoji' => '☕'
    ],
    6 => [
        'id' => 6,
        'name' => 'Yoga Mat',
        'description' => 'Non-slip yoga mat with extra cushioning',
        'price' => 29.99,
        'category_id' => 4,
        'brand_id' => 4,
        'stock' => 200,
        'rating' => 4.5,
        'reviews' => 521,
        'emoji' => '🧘'
    ],
    7 => [
        'id' => 7,
        'name' => 'Bluetooth Speaker',
        'description' => 'Portable Bluetooth speaker with rich sound',
        'price' => 59.99,
        'category_id' => 1,
        'brand_id' => 1,
        'stock' => 80,
        'rating' => 4.4,
        'reviews' => 267,
        'emoji' => '🔊'
    ],
    8 => [
        'id' => 8,
        'name' => 'Novel Collection',
        'description' => 'Bestselling novel collection bundle',
        'price' => 24.99,
        'category_id' => 5,
        'brand_id' => 5,
        'stock' => 150,
        'rating' => 4.8,
        'reviews' => 445,
        'emoji' => '📚'
    ],
    9 => [
        'id' => 9,
        'name' => 'Desk Lamp',
        'description' => 'LED desk lamp with adjustable brightness',
        'price' => 39.99,
        'category_id' => 3,
        'brand_id' => 3,
        'stock' => 90,
        'rating' => 4.2,
        'reviews' => 178,
        'emoji' => '💡'
    ],
    10 => [
        'id' => 10,
        'name' => 'Backpack',
        'description' => 'Durable backpack with laptop compartment',
        'price' => 54.99,
        'category_id' => 2,
        'brand_id' => 2,
        'stock' => 65,
        'rating' => 4.5,
        'reviews' => 334,
        'emoji' => '🎒'
    ],
    11 => [
        'id' => 11,
        'name' => 'Fitness Tracker',
        'description' => 'Water-resistant fitness tracker with GPS',
        'price' => 49.99,
        'category_id' => 1,
        'brand_id' => 1,
        'stock' => 110,
        'rating' => 4.3,
        'reviews' => 289,
        'emoji' => '⌚'
    ],
    12 => [
        'id' => 12,
        'name' => 'Table Clock',
        'description' => 'Modern table clock with alarm features',
        'price' => 19.99,
        'category_id' => 3,
        'brand_id' => 3,
        'stock' => 75,
        'rating' => 4.1,
        'reviews' => 156,
        'emoji' => '⏰'
    ],
    13 => [
        'id' => 13,
        'name' => 'Wireless Mouse',
        'description' => 'Ergonomic wireless mouse with precision control',
        'price' => 34.99,
        'category_id' => 1,
        'brand_id' => 1,
        'stock' => 140,
        'rating' => 4.4,
        'reviews' => 278,
        'emoji' => '🖱️'
    ],
    14 => [
        'id' => 14,
        'name' => 'USB-C Cable',
        'description' => 'Fast charging USB-C cable (1m)',
        'price' => 14.99,
        'category_id' => 1,
        'brand_id' => 1,
        'stock' => 300,
        'rating' => 4.6,
        'reviews' => 512,
        'emoji' => '🔌'
    ],
    15 => [
        'id' => 15,
        'name' => 'Phone Stand',
        'description' => 'Adjustable phone stand for desk',
        'price' => 12.99,
        'category_id' => 1,
        'brand_id' => 1,
        'stock' => 250,
        'rating' => 4.3,
        'reviews' => 423,
        'emoji' => '📱'
    ],
    16 => [
        'id' => 16,
        'name' => 'Winter Jacket',
        'description' => 'Waterproof winter jacket with thermal lining',
        'price' => 129.99,
        'category_id' => 2,
        'brand_id' => 2,
        'stock' => 45,
        'rating' => 4.7,
        'reviews' => 189,
        'emoji' => '🧥'
    ],
    17 => [
        'id' => 17,
        'name' => 'Jeans',
        'description' => 'Classic blue denim jeans',
        'price' => 59.99,
        'category_id' => 2,
        'brand_id' => 2,
        'stock' => 180,
        'rating' => 4.4,
        'reviews' => 567,
        'emoji' => '👖'
    ],
    18 => [
        'id' => 18,
        'name' => 'Sunglasses',
        'description' => 'UV protection designer sunglasses',
        'price' => 89.99,
        'category_id' => 2,
        'brand_id' => 2,
        'stock' => 95,
        'rating' => 4.5,
        'reviews' => 234,
        'emoji' => '😎'
    ],
    19 => [
        'id' => 19,
        'name' => 'Bed Pillow',
        'description' => 'Memory foam pillow for better sleep',
        'price' => 44.99,
        'category_id' => 3,
        'brand_id' => 3,
        'stock' => 120,
        'rating' => 4.6,
        'reviews' => 456,
        'emoji' => '🛏️'
    ],
    20 => [
        'id' => 20,
        'name' => 'Blanket',
        'description' => 'Soft and warm fleece blanket',
        'price' => 32.99,
        'category_id' => 3,
        'brand_id' => 3,
        'stock' => 200,
        'rating' => 4.5,
        'reviews' => 389,
        'emoji' => '🛁'
    ],
    21 => [
        'id' => 21,
        'name' => 'Kitchen Knife Set',
        'description' => 'Professional 5-piece kitchen knife set',
        'price' => 74.99,
        'category_id' => 3,
        'brand_id' => 3,
        'stock' => 60,
        'rating' => 4.7,
        'reviews' => 234,
        'emoji' => '🔪'
    ],
    22 => [
        'id' => 22,
        'name' => 'Dumbbells Set',
        'description' => 'Adjustable dumbbells (5-25 lbs)',
        'price' => 99.99,
        'category_id' => 4,
        'brand_id' => 4,
        'stock' => 40,
        'rating' => 4.6,
        'reviews' => 312,
        'emoji' => '🏋️'
    ],
    23 => [
        'id' => 23,
        'name' => 'Bicycle',
        'description' => 'Mountain bike with 21 gears',
        'price' => 249.99,
        'category_id' => 4,
        'brand_id' => 4,
        'stock' => 25,
        'rating' => 4.5,
        'reviews' => 178,
        'emoji' => '🚴'
    ],
    24 => [
        'id' => 24,
        'name' => 'Basketball',
        'description' => 'Official size professional basketball',
        'price' => 29.99,
        'category_id' => 4,
        'brand_id' => 4,
        'stock' => 85,
        'rating' => 4.4,
        'reviews' => 156,
        'emoji' => '🏀'
    ],
    25 => [
        'id' => 25,
        'name' => 'Tent',
        'description' => '2-person camping tent with rain fly',
        'price' => 139.99,
        'category_id' => 4,
        'brand_id' => 4,
        'stock' => 35,
        'rating' => 4.6,
        'reviews' => 267,
        'emoji' => '⛺'
    ]
];

// Static Order History Data (Single User)
$orders = [
    1 => [
        'id' => 1,
        'order_number' => '#ORD-001',
        'date' => '2026-01-15',
        'items' => [
            ['product_id' => 1, 'quantity' => 1, 'price' => 89.99],
            ['product_id' => 6, 'quantity' => 2, 'price' => 29.99]
        ],
        'subtotal' => 149.97,
        'status' => 'Delivered'
    ],
    2 => [
        'id' => 2,
        'order_number' => '#ORD-002',
        'date' => '2026-01-10',
        'items' => [
            ['product_id' => 3, 'quantity' => 1, 'price' => 79.99]
        ],
        'subtotal' => 79.99,
        'status' => 'Delivered'
    ],
    3 => [
        'id' => 3,
        'order_number' => '#ORD-003',
        'date' => '2026-01-05',
        'items' => [
            ['product_id' => 4, 'quantity' => 1, 'price' => 149.99],
            ['product_id' => 5, 'quantity' => 1, 'price' => 69.99]
        ],
        'subtotal' => 219.98,
        'status' => 'Processing'
    ]
];

// Helper function to get product by ID
function getProductById($id) {
    global $products;
    return $products[$id] ?? null;
}

// Helper function to get category by ID
function getCategoryById($id) {
    global $categories;
    return $categories[$id] ?? null;
}

// Helper function to get brand by ID
function getBrandById($id) {
    global $brands;
    return $brands[$id] ?? null;
}

// Helper function to get all products by category
function getProductsByCategory($category_id) {
    global $products;
    return array_filter($products, function($product) use ($category_id) {
        return $product['category_id'] == $category_id;
    });
}

// Helper function to search products
function searchProducts($query) {
    global $products;
    $query = strtolower(trim($query));
    
    if (empty($query)) {
        return $products;
    }
    
    return array_filter($products, function($product) use ($query) {
        return strpos(strtolower($product['name']), $query) !== false ||
               strpos(strtolower($product['description']), $query) !== false;
    });
}

// Helper function to format price
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Helper function to render star rating
function renderStars($rating) {
    $full_stars = floor($rating);
    $has_half = ($rating - $full_stars) >= 0.5;
    
    $stars = str_repeat('★', $full_stars);
    if ($has_half && $full_stars < 5) {
        $stars .= '☆';
        $stars .= str_repeat('☆', 4 - $full_stars);
    } else {
        $stars .= str_repeat('☆', 5 - $full_stars);
    }
    
    return $stars;
}
?>
