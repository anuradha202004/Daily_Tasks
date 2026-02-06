<?php
session_start();

// Load Application Bootstrap
require_once 'app/bootstrap.php';

$pageTitle = 'Products';

// Load cart and wishlist from file if user is logged in
if (isLoggedIn()) {
    if (!isset($_SESSION['cart'])) initializeCartFromFile();
    if (!isset($_SESSION['wishlist'])) initializeWishlistFromFile();
}

/**
 * ==========================================
 * FILTERING LOGIC
 * ==========================================
 */

// 1. Get Global Price Range for Slider Limits
$allPrices = array_column($products, 'price');
$globalMinPrice = floor(min($allPrices));
$globalMaxPrice = ceil(max($allPrices));

// 2. Get Filter Parameters
$selectedCategories = isset($_GET['categories']) ? (is_array($_GET['categories']) ? $_GET['categories'] : [$_GET['categories']]) : [];
$selectedBrands = isset($_GET['brands']) ? (is_array($_GET['brands']) ? $_GET['brands'] : [$_GET['brands']]) : [];
$minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : $globalMinPrice;
$maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : $globalMaxPrice;
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// 3. Apply Filters
$displayProducts = array_filter($products, function($product) use ($selectedCategories, $selectedBrands, $minPrice, $maxPrice) {
    // Category Filter
    if (!empty($selectedCategories)) {
        if (!in_array($product['category_id'], $selectedCategories)) {
            return false;
        }
    }

    // Brand Filter
    if (!empty($selectedBrands)) {
        if (!isset($product['brand_id']) || !in_array($product['brand_id'], $selectedBrands)) {
            return false;
        }
    }
    
    // Price Filter
    if ($product['price'] < $minPrice || $product['price'] > $maxPrice) {
        return false;
    }
    
    return true;
});

// 4. Apply Sorting
switch ($sortBy) {
    case 'price_asc':
        usort($displayProducts, function($a, $b) { return $a['price'] <=> $b['price']; });
        break;
    case 'price_desc':
        usort($displayProducts, function($a, $b) { return $b['price'] <=> $a['price']; });
        break;
    case 'name_asc':
        usort($displayProducts, function($a, $b) { return strcasecmp($a['name'], $b['name']); });
        break;
    case 'popular':
        // Mock polarity based on reviews
        usort($displayProducts, function($a, $b) { return $b['reviews'] <=> $a['reviews']; });
        break;
    case 'newest':
    default:
        // Already sorted by ID/Created usually, but let's reverse ID for "newest"
        usort($displayProducts, function($a, $b) { return $b['id'] <=> $a['id']; });
        break;
}

?>
<?php include TEMPLATES_PATH . '/header.php'; ?>
    <!-- Scripts -->
    <script src="/public/js/wishlist.js"></script>
    <script src="/public/js/cart.js"></script>
    <script src="/public/js/toast.js"></script>

    <!-- Page Styles -->
    <style>
        /* Modern Variables */
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --secondary-color: #10b981;
            --text-dark: #1f2937;
            --text-gray: #6b7280;
            --bg-light: #f9fafb;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --glass-bg: rgba(255, 255, 255, 0.95);
        }

        /* Enhanced Page Header */
        .page-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 40px 0;
            margin-bottom: 40px;
            border-bottom: 1px solid #dee2e6;
        }

        /* Grid Layout */
        .products-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 40px;
            align-items: start;
        }

        /* Modern Sidebar with Custom Scrolling */
        .filters-sidebar {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            border: 1px solid rgba(229, 231, 235, 0.8);
            box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.05); /* Softer shadow */
            position: sticky;
            top: 85px; /* Offset for fixed header if present, or just top spacing */
            max-height: calc(100vh - 100px);
            overflow-y: auto;
            overscroll-behavior: contain; /* Prevent body scroll when sidebar scrolls */
            transition: all 0.3s ease;
        }

        /* Custom Scrollbar */
        .filters-sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .filters-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .filters-sidebar::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            border-radius: 20px;
            border: 2px solid transparent;
            background-clip: content-box;
        }
        .filters-sidebar::-webkit-scrollbar-thumb:hover {
            background-color: #9ca3af;
        }

        .filter-group {
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 1px dashed #e5e7eb; /* Modern dashed divider */
        }

        .filter-group:last-child {
            border: none;
            margin: 0;
            padding: 0;
        }

        .filter-title {
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 18px;
            color: #374151;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Sleek Checkboxes */
        .custom-checkbox {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            cursor: pointer;
            font-size: 0.95rem;
            color: var(--text-gray);
            transition: all 0.2s;
            padding: 4px 0;
        }

        .custom-checkbox:hover {
            color: var(--primary-color);
            transform: translateX(2px);
        }

        .custom-checkbox input {
            width: 18px;
            height: 18px;
            margin-right: 12px;
            accent-color: var(--primary-color);
            cursor: pointer;
            border-radius: 4px;
        }

        /* Price Inputs */
        .price-inputs {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .price-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s;
            background: #f9fafb;
        }

        .price-input:focus {
            background: #fff;
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        /* Toolbar */
        .products-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 15px 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(229, 231, 235, 0.5);
        }

        .sort-select {
            padding: 8px 32px 8px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background-color: #fff;
            font-size: 0.9rem;
            cursor: pointer;
            color: var(--text-dark);
            font-weight: 500;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%236b7280%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 10px;
            appearance: none;
            transition: border-color 0.2s;
        }

        .sort-select:hover {
            border-color: var(--text-gray);
        }

        /* Buttons */
        .btn-apply {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
        }

        .btn-apply:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 8px rgba(79, 70, 229, 0.3);
        }

        .btn-reset {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .btn-reset:hover {
            color: var(--primary-color);
        }

        /* =================================
           🔥 MODERN PRODUCT CARDS
           ================================= */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
        }

        .product-card {
            background: #fff;
            border: 1px solid rgba(229, 231, 235, 0.5);
            border-radius: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: rgba(79, 70, 229, 0.2);
        }

        .product-image-container {
            position: relative;
            padding-top: 100%; /* 1:1 Aspect Ratio */
            background: #f3f4f6;
            overflow: hidden;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-image-content {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.08); /* Modern subtler zoom */
        }

        /* Floating Wishlist Button */
        .card-wishlist-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            cursor: pointer;
            z-index: 10;
            font-size: 1.25rem;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            opacity: 1;
            transform: translateY(0);
        }

        .card-wishlist-btn:hover {
            transform: scale(1.15);
            box-shadow: 0 8px 16px rgba(0,0,0,0.12);
        }

        /* "New" Badge */
        .badge-new {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--primary-color);
            color: white;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            z-index: 10;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3);
        }

        .product-info {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-category {
            font-size: 0.8rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .product-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 8px 0;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }

        .product-price-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .price-current {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-dark);
        }

        .rating-block {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.85rem;
            color: #f59e0b;
            font-weight: 600;
        }

        .product-footer {
            margin-top: auto;
            border-top: 1px solid #f3f4f6;
            padding-top: 15px;
        }

        /* Modern Action Buttons */
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .btn-modern {
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            text-align: center;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-outline-cart {
            background: transparent;
            border: 2px solid #e5e7eb;
            color: var(--text-dark);
        }

        .btn-outline-cart:hover {
            border-color: var(--text-dark);
            background: var(--text-dark);
            color: white;
        }

        .btn-gradient-buy {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3);
        }

        .btn-gradient-buy:hover {
            filter: brightness(110%);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(124, 58, 237, 0.4);
        }
        
        .btn-disabled {
            background: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
            grid-column: span 2;
        }

        /* Stock Indicator */
        .stock-indicator {
            font-size: 0.75rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stock-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .in-stock { color: #059669; }
        .in-stock .stock-dot { background: #10b981; }
        
        .low-stock { color: #d97706; }
        .low-stock .stock-dot { background: #f59e0b; }
        
        .out-stock { color: #dc2626; }
        .out-stock .stock-dot { background: #ef4444; }

        /* Mobile */
        .filter-toggle {
            display: none;
            width: 100%;
            padding: 14px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
            color: var(--text-dark);
            box-shadow: var(--card-shadow);
        }

        @media (max-width: 992px) {
            .products-layout {
                grid-template-columns: 1fr;
            }
            .filters-sidebar {
                display: none;
                margin-bottom: 30px;
            }
            .filters-sidebar.active {
                display: block;
                animation: slideDown 0.3s ease;
            }
            .filter-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <!-- Page Content -->
    <div class="page-header">
        <div class="container">
            <h1 class="section-title" style="margin-bottom: 0;">Our Products</h1>
        </div>
    </div>

    <section class="container" style="padding-bottom: 60px;">
        <!-- Mobile Filter Toggle -->
        <button class="filter-toggle" onclick="document.querySelector('.filters-sidebar').classList.toggle('active')">
            🔍 Show Filters & Sort
        </button>

        <form id="filterForm" class="products-layout" method="GET" action="products.php">
            
            <!-- Sidebar -->
            <aside class="filters-sidebar">
                <div class="filter-group">
                    <div class="filter-title">Categories</div>
                    <?php foreach ($categories as $category): ?>
                        <label class="custom-checkbox">
                            <input type="checkbox" name="categories[]" value="<?php echo $category['id']; ?>"
                                <?php echo in_array($category['id'], $selectedCategories) ? 'checked' : ''; ?>
                                onchange="this.form.submit()">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="filter-group">
                    <div class="filter-title">Brands</div>
                    <?php if (!empty($brands)): ?>
                        <?php foreach ($brands as $brand): ?>
                            <label class="custom-checkbox">
                                <input type="checkbox" name="brands[]" value="<?php echo $brand['id']; ?>"
                                    <?php echo in_array($brand['id'], $selectedBrands) ? 'checked' : ''; ?>
                                    onchange="this.form.submit()">
                                <?php echo htmlspecialchars($brand['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="color: #999; font-size: 13px;">No brands available</div>
                    <?php endif; ?>
                </div>

                <div class="filter-group">
                    <div class="filter-title">Price Range</div>
                    <div class="price-inputs">
                        <input type="number" name="min_price" class="price-input" placeholder="Min" 
                               value="<?php echo $minPrice; ?>" min="<?php echo $globalMinPrice; ?>" max="<?php echo $globalMaxPrice; ?>">
                        <span style="color: #999;">-</span>
                        <input type="number" name="max_price" class="price-input" placeholder="Max" 
                               value="<?php echo $maxPrice; ?>" min="<?php echo $globalMinPrice; ?>" max="<?php echo $globalMaxPrice; ?>">
                    </div>
                </div>

                <div class="filter-group">
                    <button type="submit" class="btn-apply">Apply Filters</button>
                    <a href="/products" class="btn-reset">Reset Filters</a>
                </div>
            </aside>

            <!-- Main Content -->
            <main>
                <!-- Toolbar -->
                <div class="products-toolbar">
                    <div style="font-weight: 600; color: #555;">
                        Found <span style="color: #2563eb;"><?php echo count($displayProducts); ?></span> items
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 14px; color: #666;">Sort by:</span>
                        <select name="sort" class="sort-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="newest" <?php echo $sortBy == 'newest' ? 'selected' : ''; ?>>Newest Arrivals</option>
                            <option value="popular" <?php echo $sortBy == 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                            <option value="price_asc" <?php echo $sortBy == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_desc" <?php echo $sortBy == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="name_asc" <?php echo $sortBy == 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <?php if (count($displayProducts) > 0): ?>
                    <div class="products-grid">
                        <?php foreach ($displayProducts as $index => $product): ?>
                            <?php $isWishlisted = isset($_SESSION['wishlist']) && in_array($product['id'], $_SESSION['wishlist']); ?>
                            
                            <!-- MODERN PRODUCT CARD -->
                            <div class="product-card" onclick="window.location.href='product-detail?id=<?php echo $product['id']; ?>'">
                                
                                <!-- Wishlist & Badges -->
                                <?php if (isLoggedIn()): ?>
                                    <div class="card-wishlist-btn" 
                                         onclick="event.stopPropagation(); toggleWishlist(event, <?php echo $product['id']; ?>)"
                                         data-product-id="<?php echo $product['id']; ?>">
                                        <?php echo $isWishlisted ? '❤️' : '🤍'; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- "New" Badge for first 3 items or specific IDs -->
                                <?php if ($index < 3): ?>
                                    <span class="badge-new">NEW</span>
                                <?php endif; ?>

                                <!-- Image -->
                                <div class="product-image-container">
                                    <div class="product-image-content product-image">
                                        <?php if (!empty($product['image'])): ?>
                                            <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php else: ?>
                                            <span style="font-size: 60px;"><?php echo $product['emoji']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="product-info">
                                    <div class="product-category"><?php echo htmlspecialchars($product['brand'] ?? 'General'); ?></div>
                                    <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    
                                    <div class="product-price-row">
                                        <div class="price-current"><?php echo formatPrice($product['price']); ?></div>
                                        <div class="rating-block">
                                            ⭐ <?php echo $product['rating']; ?> <span style="color: #9ca3af; font-weight: 400;">(<?php echo $product['reviews']; ?>)</span>
                                        </div>
                                    </div>

                                    <!-- Stock Status -->
                                    <div class="product-footer">
                                        <?php 
                                            $stockClass = 'in-stock';
                                            $stockText = 'In Stock';
                                            if ($product['stock'] == 0) {
                                                $stockClass = 'out-stock';
                                                $stockText = 'Out of Stock';
                                            } elseif ($product['stock'] < 10) {
                                                $stockClass = 'low-stock';
                                                $stockText = 'Running Low';
                                            }
                                        ?>
                                        <div class="stock-indicator <?php echo $stockClass; ?>">
                                            <span class="stock-dot"></span> <?php echo $stockText; ?>
                                        </div>

                                        <!-- Actions -->
                                        <div class="action-buttons" onclick="event.stopPropagation();">
                                            <?php if ($product['stock'] > 0): ?>
                                                <button onclick="(function(e, id, name){ e.preventDefault(); e.stopPropagation(); var fd = new FormData(); fd.append('action', 'add'); fd.append('product_id', id); fd.append('quantity', 1); fetch('cart', {method: 'POST', body: fd}).then(res => res.json()).then(data => { if(data.success) { showToast('🛒 ' + name + ' added to cart!', 'success', 3500); var badge = document.querySelector('.badge'); if(badge){ badge.textContent = data.cartCount || (parseInt(badge.textContent) + 1); badge.style.display = 'flex'; } } else if(data.alreadyInCart) { showToast('ℹ️ ' + name + ' is already in your cart!', 'info', 3500); } else { showToast('❌ ' + (data.message || 'Error adding to cart'), 'error', 3000); } }).catch(() => showToast('❌ Error adding to cart', 'error', 3000)); return false; })(event, <?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')" 
                                                        class="btn-modern btn-outline-cart">
                                                    Add to Cart
                                                </button>
                                                <a href="/checkout?product_id=<?php echo $product['id']; ?>&qty=1&reset_shipping=1" class="btn-modern btn-gradient-buy">
                                                    Buy Now
                                                </a>
                                            <?php else: ?>
                                                <button class="btn-modern btn-disabled" disabled>Out of Stock</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END CARD -->

                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state" style="text-align: center; padding: 60px 20px; background: #f8f9fa; border-radius: 12px;">
                        <span style="font-size: 40px;">🔍</span>
                        <h3 style="color: #333; margin-top: 20px;">No products found</h3>
                        <p style="color: #666; margin-bottom: 20px;">Try adjusting your filters or price range.</p>
                        <a href="/products" class="btn btn-primary" style="display: inline-block;">Clear All Filters</a>
                    </div>
                <?php endif; ?>
            </main>
        </form>
    </section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
