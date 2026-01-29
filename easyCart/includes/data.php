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
    ],
    6 => [
        'id' => 6,
        'name' => 'Toys & Games',
        'slug' => 'toys-games',
        'description' => 'Fun and educational toys'
    ],
    7 => [
        'id' => 7,
        'name' => 'Beauty & Personal Care',
        'slug' => 'beauty-personal-care',
        'description' => 'Beauty and grooming products'
    ],
    8 => [
        'id' => 8,
        'name' => 'Health & Wellness',
        'slug' => 'health-wellness',
        'description' => 'Health and fitness supplements'
    ],
    9 => [
        'id' => 9,
        'name' => 'Automotive',
        'slug' => 'automotive',
        'description' => 'Car accessories and tools'
    ],
    10 => [
        'id' => 10,
        'name' => 'Pet Supplies',
        'slug' => 'pet-supplies',
        'description' => 'Food and toys for your pets'
    ],
    11 => [
        'id' => 11,
        'name' => 'Jewelry & Watches',
        'slug' => 'jewelry-watches',
        'description' => 'Luxury jewelry and timepieces'
    ],
    12 => [
        'id' => 12,
        'name' => 'Garden & Tools',
        'slug' => 'garden-tools',
        'description' => 'Gardening equipment and home tools'
    ]
];

// Brands Data
$brands = [
    1 => ['id' => 1, 'name' => 'TechPro'],
    2 => ['id' => 2, 'name' => 'StyleMax'],
    3 => ['id' => 3, 'name' => 'HomeComfort'],
    4 => ['id' => 4, 'name' => 'SportZone'],
    5 => ['id' => 5, 'name' => 'MediaHub'],
    6 => ['id' => 6, 'name' => 'ToyVerse'],
    7 => ['id' => 7, 'name' => 'GlowUp'],
    8 => ['id' => 8, 'name' => 'WellnessWay'],
    9 => ['id' => 9, 'name' => 'AutoDrive'],
    10 => ['id' => 10, 'name' => 'PetLove'],
    11 => ['id' => 11, 'name' => 'JewelGleam'],
    12 => ['id' => 12, 'name' => 'GreenThumb']
];

// Products Data
$products = [
    1 => [
        'id' => 1,
        'name' => 'Wireless Headphone',
        'description' => 'Wireless Headphone with noise cancellation',
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
    40 => [
        'id' => 40,
        'name' => 'Digital Drawing Tablet',
        'description' => 'Graphics tablet for digital art and design',
        'price' => 89.00,
        'category_id' => 1,
        'brand_id' => 1,
        'stock' => 30,
        'rating' => 4.7,
        'reviews' => 92,
        'emoji' => '🎨'
    ],
    41 => ['id' => 41, 'name' => 'Lego Star Castle', 'description' => 'Creative building block set', 'price' => 59.99, 'category_id' => 6, 'brand_id' => 6, 'stock' => 40, 'rating' => 4.9, 'reviews' => 312, 'emoji' => '🏰'],
    42 => ['id' => 42, 'name' => 'Remote Control Car', 'description' => 'High-speed RC car with rechargeable battery', 'price' => 45.00, 'category_id' => 6, 'brand_id' => 6, 'stock' => 60, 'rating' => 4.7, 'reviews' => 156, 'emoji' => '🏎️'],
    43 => ['id' => 43, 'name' => 'Skin Care Serum', 'description' => 'Hydrating vitamin C facial serum', 'price' => 29.99, 'category_id' => 7, 'brand_id' => 7, 'stock' => 100, 'rating' => 4.6, 'reviews' => 245, 'emoji' => '🧴'],
    44 => ['id' => 44, 'name' => 'Matte Lipstick', 'description' => 'Long-lasting waterproof matte lipstick', 'price' => 18.50, 'category_id' => 7, 'brand_id' => 7, 'stock' => 150, 'rating' => 4.5, 'reviews' => 389, 'emoji' => '💄'],
    45 => ['id' => 45, 'name' => 'Whey Protein', 'description' => 'High-quality chocolate whey protein powder', 'price' => 49.99, 'category_id' => 8, 'brand_id' => 8, 'stock' => 80, 'rating' => 4.8, 'reviews' => 512, 'emoji' => '💪'],
    46 => ['id' => 46, 'name' => 'Yoga Block Set', 'description' => 'Supportive foam blocks for yoga practice', 'price' => 15.99, 'category_id' => 8, 'brand_id' => 8, 'stock' => 120, 'rating' => 4.4, 'reviews' => 98, 'emoji' => '🧱'],
    47 => ['id' => 47, 'name' => 'Car Vacuum', 'description' => 'Portable high-suction car vacuum cleaner', 'price' => 39.99, 'category_id' => 9, 'brand_id' => 9, 'stock' => 55, 'rating' => 4.3, 'reviews' => 127, 'emoji' => '🧹'],
    48 => ['id' => 48, 'name' => 'Digital Tire Gauge', 'description' => 'Accurate digital pressure gauge for tires', 'price' => 12.99, 'category_id' => 9, 'brand_id' => 9, 'stock' => 200, 'rating' => 4.6, 'reviews' => 234, 'emoji' => '📏'],
    49 => ['id' => 49, 'name' => 'Dog Chew Toy', 'description' => 'Durable rubber chew toy for active dogs', 'price' => 9.99, 'category_id' => 10, 'brand_id' => 10, 'stock' => 300, 'rating' => 4.7, 'reviews' => 456, 'emoji' => '🦴'],
    50 => ['id' => 50, 'name' => 'Pet Grooming Brush', 'description' => 'Self-cleaning slicker brush for dogs and cats', 'price' => 14.50, 'category_id' => 10, 'brand_id' => 10, 'stock' => 150, 'rating' => 4.8, 'reviews' => 321, 'emoji' => '🪮'],
    51 => ['id' => 51, 'name' => 'Golden Necklace', 'description' => '18k gold plated elegant chain necklace', 'price' => 85.00, 'category_id' => 11, 'brand_id' => 11, 'stock' => 40, 'rating' => 4.9, 'reviews' => 167, 'emoji' => '📿'],
    52 => ['id' => 52, 'name' => 'Classic Silver Watch', 'description' => 'Stainless steel analog watch for men', 'price' => 120.00, 'category_id' => 11, 'brand_id' => 11, 'stock' => 25, 'rating' => 4.7, 'reviews' => 89, 'emoji' => '⌚'],
    53 => ['id' => 53, 'name' => 'Pruning Shears', 'description' => 'Sharp stainless steel garden pruning shears', 'price' => 19.99, 'category_id' => 12, 'brand_id' => 12, 'stock' => 100, 'rating' => 4.5, 'reviews' => 212, 'emoji' => '✂️'],
    54 => ['id' => 54, 'name' => 'Hand Tool Set', 'description' => 'Essential 10-piece home repair tool kit', 'price' => 35.00, 'category_id' => 12, 'brand_id' => 12, 'stock' => 60, 'rating' => 4.6, 'reviews' => 145, 'emoji' => '🛠️'],
    55 => ['id' => 55, 'name' => 'Drone Explorer', 'description' => '4K camera drone with GPS and auto-return', 'price' => 299.99, 'category_id' => 1, 'brand_id' => 1, 'stock' => 15, 'rating' => 4.8, 'reviews' => 88, 'emoji' => '🚁'],
    56 => ['id' => 56, 'name' => 'Action Camera', 'description' => 'Waterproof 4K action cam for sports', 'price' => 110.00, 'category_id' => 1, 'brand_id' => 1, 'stock' => 45, 'rating' => 4.5, 'reviews' => 210, 'emoji' => '📹'],
    57 => ['id' => 57, 'name' => 'Silk Scarf', 'description' => 'Hand-painted 100% natural silk scarf', 'price' => 45.00, 'category_id' => 2, 'brand_id' => 2, 'stock' => 70, 'rating' => 4.9, 'reviews' => 123, 'emoji' => '🧣'],
    58 => ['id' => 58, 'name' => 'Men\'s Leather Wallet', 'description' => 'Bifold RFID blocking leather wallet', 'price' => 30.00, 'category_id' => 2, 'brand_id' => 2, 'stock' => 130, 'rating' => 4.6, 'reviews' => 345, 'emoji' => '👛'],
    59 => ['id' => 59, 'name' => 'Air Purifier', 'description' => 'HEPA filter air purifier for home', 'price' => 150.00, 'category_id' => 3, 'brand_id' => 3, 'stock' => 25, 'rating' => 4.7, 'reviews' => 198, 'emoji' => '🌬️'],
    60 => ['id' => 60, 'name' => 'Slow Cooker', 'description' => '6-quart programmable slow cooker', 'price' => 59.99, 'category_id' => 3, 'brand_id' => 3, 'stock' => 40, 'rating' => 4.4, 'reviews' => 267, 'emoji' => '🍲'],
    61 => ['id' => 61, 'name' => 'Board Game Classic', 'description' => 'Strategic family fun board game', 'price' => 35.00, 'category_id' => 6, 'brand_id' => 6, 'stock' => 200, 'rating' => 4.8, 'reviews' => 512, 'emoji' => '🎲'],
    62 => ['id' => 62, 'name' => 'Plush Bear', 'description' => 'Extra soft giant teddy bear (3 feet)', 'price' => 40.00, 'category_id' => 6, 'brand_id' => 6, 'stock' => 50, 'rating' => 4.9, 'reviews' => 345, 'emoji' => '🧸'],
    63 => ['id' => 63, 'name' => 'Hair Dryer Pro', 'description' => 'Ionic hair dryer with multiple settings', 'price' => 85.00, 'category_id' => 7, 'brand_id' => 7, 'stock' => 65, 'rating' => 4.6, 'reviews' => 278, 'emoji' => '🌬️'],
    64 => ['id' => 64, 'name' => 'Face Steamer', 'description' => 'Nano-ionic professional facial steamer', 'price' => 42.00, 'category_id' => 7, 'brand_id' => 7, 'stock' => 90, 'rating' => 4.5, 'reviews' => 156, 'emoji' => '🧖'],
    65 => ['id' => 65, 'name' => 'Multivitamin', 'description' => 'Daily essential multi-vitamins (90 caps)', 'price' => 25.00, 'category_id' => 8, 'brand_id' => 8, 'stock' => 250, 'rating' => 4.7, 'reviews' => 623, 'emoji' => '💊'],
    66 => ['id' => 66, 'name' => 'Resistance Bands', 'description' => 'Set of 5 heavy-duty resistance bands', 'price' => 20.00, 'category_id' => 8, 'brand_id' => 8, 'stock' => 180, 'rating' => 4.6, 'reviews' => 421, 'emoji' => '🎗️'],
    67 => ['id' => 67, 'name' => 'Jump Starter', 'description' => 'Portable car battery jump starter', 'price' => 89.99, 'category_id' => 9, 'brand_id' => 9, 'stock' => 30, 'rating' => 4.9, 'reviews' => 145, 'emoji' => '⚡'],
    68 => ['id' => 68, 'name' => 'Dash Cam', 'description' => '1080p car dashboard camera with night vision', 'price' => 65.00, 'category_id' => 9, 'brand_id' => 9, 'stock' => 75, 'rating' => 4.4, 'reviews' => 289, 'emoji' => '📷'],
    69 => ['id' => 69, 'name' => 'Cat Scratching Post', 'description' => 'Sisal-wrapped post with interactive toy', 'price' => 28.00, 'category_id' => 10, 'brand_id' => 10, 'stock' => 60, 'rating' => 4.7, 'reviews' => 178, 'emoji' => '🐈'],
    70 => ['id' => 70, 'name' => 'Hamster Cage', 'description' => 'Spacious habitat with wheel and tunnels', 'price' => 45.00, 'category_id' => 10, 'brand_id' => 10, 'stock' => 40, 'rating' => 4.6, 'reviews' => 134, 'emoji' => '🐹'],
    71 => ['id' => 71, 'name' => 'Pearl Earrings', 'description' => 'Sterling silver freshwater pearl studs', 'price' => 55.00, 'category_id' => 11, 'brand_id' => 11, 'stock' => 50, 'rating' => 4.8, 'reviews' => 112, 'emoji' => '💎'],
    72 => ['id' => 72, 'name' => 'Tennis Bracelet', 'description' => 'Elegant cubic zirconia line bracelet', 'price' => 75.00, 'category_id' => 11, 'brand_id' => 11, 'stock' => 35, 'rating' => 4.7, 'reviews' => 95, 'emoji' => '✨'],
    73 => ['id' => 73, 'name' => 'Watering Can', 'description' => '1.5-gallon vintage style metal watering can', 'price' => 24.00, 'category_id' => 12, 'brand_id' => 12, 'stock' => 85, 'rating' => 4.5, 'reviews' => 167, 'emoji' => '🚿'],
    74 => ['id' => 74, 'name' => 'Garden Kneeler', 'description' => 'Padded folding bench for garden comfort', 'price' => 38.00, 'category_id' => 12, 'brand_id' => 12, 'stock' => 45, 'rating' => 4.7, 'reviews' => 134, 'emoji' => '🍀'],
    75 => ['id' => 75, 'name' => 'Tablet Reader', 'description' => 'E-ink screen reader for book lovers', 'price' => 119.99, 'category_id' => 5, 'brand_id' => 5, 'stock' => 55, 'rating' => 4.6, 'reviews' => 212, 'emoji' => '📖'],
    76 => ['id' => 76, 'name' => 'Vinyl Turntable', 'description' => 'Modern record player with Bluetooth out', 'price' => 99.00, 'category_id' => 5, 'brand_id' => 5, 'stock' => 20, 'rating' => 4.8, 'reviews' => 145, 'emoji' => '💽'],
    77 => ['id' => 77, 'name' => 'Wireless Doorbell', 'description' => 'Smart doorbell with chime and waterproof button', 'price' => 25.99, 'category_id' => 3, 'brand_id' => 3, 'stock' => 150, 'rating' => 4.4, 'reviews' => 389, 'emoji' => '🔔'],
    78 => ['id' => 78, 'name' => 'Digital Camera', 'description' => 'Compact point-and-shoot 20MP camera', 'price' => 145.00, 'category_id' => 1, 'brand_id' => 1, 'stock' => 40, 'rating' => 4.3, 'reviews' => 127, 'emoji' => '📸'],
    79 => ['id' => 79, 'name' => 'Hoodie Sweater', 'description' => 'Stay cozy with our premium cotton hoodie', 'price' => 45.00, 'category_id' => 2, 'brand_id' => 2, 'stock' => 200, 'rating' => 4.6, 'reviews' => 451, 'emoji' => '🧥'],
    80 => ['id' => 80, 'name' => 'Espresso Machine', 'description' => 'Semi-auto espresso maker for home baristas', 'price' => 189.99, 'category_id' => 3, 'brand_id' => 3, 'stock' => 15, 'rating' => 4.9, 'reviews' => 92, 'emoji' => '☕'],
    81 => ['id' => 81, 'name' => 'Educational Robot', 'description' => 'Programmable robot kit for STEM learning', 'price' => 75.00, 'category_id' => 6, 'brand_id' => 6, 'stock' => 35, 'rating' => 4.8, 'reviews' => 156, 'emoji' => '🤖'],
    82 => ['id' => 82, 'name' => 'Nail Kit Pro', 'description' => 'Professional gel manicure kit with UV lamp', 'price' => 49.99, 'category_id' => 7, 'brand_id' => 7, 'stock' => 120, 'rating' => 4.6, 'reviews' => 234, 'emoji' => '💅'],
    83 => ['id' => 83, 'name' => 'Massage Gun', 'description' => 'Deep tissue muscle massager with 6 heads', 'price' => 69.00, 'category_id' => 8, 'brand_id' => 8, 'stock' => 55, 'rating' => 4.7, 'reviews' => 289, 'emoji' => '🔫'],
    84 => ['id' => 84, 'name' => 'Car Organizer', 'description' => 'Trunk storage organizer with multiple compartments', 'price' => 22.00, 'category_id' => 9, 'brand_id' => 9, 'stock' => 90, 'rating' => 4.5, 'reviews' => 167, 'emoji' => '🗳️'],
    85 => ['id' => 85, 'name' => 'Pet Water Fountain', 'description' => 'Automatic circulating water flow for pets', 'price' => 35.00, 'category_id' => 10, 'brand_id' => 10, 'stock' => 75, 'rating' => 4.8, 'reviews' => 212, 'emoji' => '⛲'],
    86 => ['id' => 86, 'name' => 'Silver Cufflinks', 'description' => 'Solid silver engraved designer cufflinks', 'price' => 42.00, 'category_id' => 11, 'brand_id' => 11, 'stock' => 60, 'rating' => 4.7, 'reviews' => 88, 'emoji' => '💼'],
    87 => ['id' => 87, 'name' => 'Bird Feeder', 'description' => 'Hanging wild bird feeder for garden', 'price' => 18.50, 'category_id' => 12, 'brand_id' => 12, 'stock' => 110, 'rating' => 4.6, 'reviews' => 145, 'emoji' => '🐦'],
    88 => ['id' => 88, 'name' => 'World Map Poster', 'description' => 'Large laminated scratch-off world map', 'price' => 15.00, 'category_id' => 5, 'brand_id' => 5, 'stock' => 300, 'rating' => 4.5, 'reviews' => 567, 'emoji' => '🗺️'],
    89 => ['id' => 89, 'name' => 'External SSD', 'description' => 'High-speed 1TB portable solid state drive', 'price' => 110.00, 'category_id' => 1, 'brand_id' => 1, 'stock' => 65, 'rating' => 4.9, 'reviews' => 345, 'emoji' => '💾'],
    90 => ['id' => 90, 'name' => 'Tracksuit Set', 'description' => 'Stylish and breathable training tracksuit', 'price' => 65.00, 'category_id' => 2, 'brand_id' => 2, 'stock' => 140, 'rating' => 4.4, 'reviews' => 189, 'emoji' => '🏃'],
    91 => ['id' => 91, 'name' => 'Toaster Pro', 'description' => 'Wide-slot intelligent stainless steel toaster', 'price' => 45.00, 'category_id' => 3, 'brand_id' => 3, 'stock' => 80, 'rating' => 4.6, 'reviews' => 234, 'emoji' => '🍞'],
    92 => ['id' => 92, 'name' => 'Musical Mobile', 'description' => 'Soothing musical mobile for baby crib', 'price' => 32.00, 'category_id' => 6, 'brand_id' => 6, 'stock' => 95, 'rating' => 4.8, 'reviews' => 112, 'emoji' => '🎶'],
    93 => ['id' => 93, 'name' => 'Essential Oils', 'description' => 'Set of 6 organic therapeutic grade oils', 'price' => 20.00, 'category_id' => 7, 'brand_id' => 7, 'stock' => 250, 'rating' => 4.7, 'reviews' => 567, 'emoji' => '🌿'],
    94 => ['id' => 94, 'name' => 'Sleep Mask', 'description' => '3D contoured comfortable blackout sleep mask', 'price' => 12.00, 'category_id' => 8, 'brand_id' => 8, 'stock' => 400, 'rating' => 4.6, 'reviews' => 789, 'emoji' => '🎭'],
    95 => ['id' => 95, 'name' => 'Sun Shade', 'description' => 'Foldable car windshield solar protector', 'price' => 15.00, 'category_id' => 9, 'brand_id' => 9, 'stock' => 200, 'rating' => 4.4, 'reviews' => 231, 'emoji' => '☀️'],
    96 => ['id' => 96, 'name' => 'Pet Carrier', 'description' => 'Soft-sided airline approved pet traveler', 'price' => 39.00, 'category_id' => 10, 'brand_id' => 10, 'stock' => 55, 'rating' => 4.7, 'reviews' => 156, 'emoji' => '🎒'],
    97 => ['id' => 97, 'name' => 'Crystal Pendant', 'description' => 'Amethyst crystal lucky charm pendant', 'price' => 25.00, 'category_id' => 11, 'brand_id' => 11, 'stock' => 120, 'rating' => 4.5, 'reviews' => 178, 'emoji' => '🔮'],
    98 => ['id' => 98, 'name' => 'Solar Garden Light', 'description' => 'Set of 4 solar-powered outdoor path lights', 'price' => 34.00, 'category_id' => 12, 'brand_id' => 12, 'stock' => 150, 'rating' => 4.6, 'reviews' => 289, 'emoji' => '💡'],
    99 => ['id' => 99, 'name' => 'Sketchbook', 'description' => 'Premium 100-page acid-free drawing paper', 'price' => 18.00, 'category_id' => 5, 'brand_id' => 5, 'stock' => 200, 'rating' => 4.7, 'reviews' => 145, 'emoji' => '🗒️'],
    100 => ['id' => 100, 'name' => 'Gaming Mouse Pad', 'description' => 'Large RGB gaming mouse mat with silk surface', 'price' => 28.00, 'category_id' => 1, 'brand_id' => 1, 'stock' => 110, 'rating' => 4.8, 'reviews' => 312, 'emoji' => '🖱️']
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
        'tax' => 26.99,
        'shipping' => 40.00,
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
        'tax' => 14.40,
        'shipping' => 40.00,
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
        'tax' => 39.60,
        'shipping' => 40.00,
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

/**
 * Calculate discount based on quantity
 * 5-9 units: 10% discount
 * 10+ units: 20% discount
 */
function calculateBulkDiscount($price, $quantity) {
    if ($quantity >= 10) {
        return ($price * $quantity) * 0.20; // 20% off
    } elseif ($quantity >= 5) {
        return ($price * $quantity) * 0.10; // 10% off
    }
    return 0;
}

// Helper function to check if product is in wishlist
function isProductInWishlist($productId) {
    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }
    return in_array($productId, $_SESSION['wishlist']);
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

// ============================================
// CART & WISHLIST PERSISTENCE FUNCTIONS
// ============================================

// Get cart data file path for a user
function getCartFilePath($userId) {
    $dataDir = __DIR__ . '/../data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    return $dataDir . '/cart_' . md5($userId) . '.json';
}

// Get wishlist data file path for a user
function getWishlistFilePath($userId) {
    $dataDir = __DIR__ . '/../data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    return $dataDir . '/wishlist_' . md5($userId) . '.json';
}

// Load cart from file for logged-in user
function loadUserCart($userId) {
    $cartFile = getCartFilePath($userId);
    
    if (file_exists($cartFile) && filesize($cartFile) > 0) {
        $fileContent = file_get_contents($cartFile);
        $cart = json_decode($fileContent, true);
        
        if (is_array($cart)) {
            return $cart;
        }
    }
    
    return [];
}

// Save cart to file for logged-in user
function saveUserCart($userId, $cart) {
    $cartFile = getCartFilePath($userId);
    $jsonData = json_encode($cart, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents($cartFile, $jsonData);
}

// Load wishlist from file for logged-in user
function loadUserWishlist($userId) {
    $wishlistFile = getWishlistFilePath($userId);
    
    if (file_exists($wishlistFile) && filesize($wishlistFile) > 0) {
        $fileContent = file_get_contents($wishlistFile);
        $wishlist = json_decode($fileContent, true);
        
        if (is_array($wishlist)) {
            return $wishlist;
        }
    }
    
    return [];
}

// Save wishlist to file for logged-in user
function saveUserWishlist($userId, $wishlist) {
    $wishlistFile = getWishlistFilePath($userId);
    $jsonData = json_encode($wishlist, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents($wishlistFile, $jsonData);
}

// Initialize cart from file on login
function initializeCartFromFile() {
    if (isLoggedIn() && isset($_SESSION['user_email'])) {
        $cart = loadUserCart($_SESSION['user_email']);
        $_SESSION['cart'] = $cart;
    }
}

// Initialize wishlist from file on login
function initializeWishlistFromFile() {
    if (isLoggedIn() && isset($_SESSION['user_email'])) {
        $wishlist = loadUserWishlist($_SESSION['user_email']);
        $_SESSION['wishlist'] = $wishlist;
    }
}
?>
