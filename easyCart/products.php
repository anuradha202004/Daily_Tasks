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
$minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : $globalMinPrice;
$maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : $globalMaxPrice;
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// 3. Apply Filters
$displayProducts = array_filter($products, function($product) use ($selectedCategories, $minPrice, $maxPrice) {
    // Category Filter
    if (!empty($selectedCategories)) {
        if (!in_array($product['category_id'], $selectedCategories)) {
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
    <script src="public/js/wishlist.js"></script>
    <script src="public/js/cart.js"></script>
    <script src="public/js/toast.js"></script>

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
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --glass-bg: rgba(255, 255, 255, 0.95);
        }

        /* Enhanced Page Header */
        .page-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px 0; /* Reduced padding */
            margin-bottom: 30px;
            border-bottom: 1px solid #dee2e6;
        }

        /* Grid Layout */
        .products-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 40px;
            align-items: start;
        }

        /* Modern Sidebar */
        .filters-sidebar {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            border: 1px solid rgba(229, 231, 235, 0.5);
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 20px;
            transition: all 0.3s ease;
        }

        .filter-group {
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 1px solid #f3f4f6;
        }

        .filter-group:last-child {
            border: none;
            margin: 0;
            padding: 0;
        }

        .filter-title {
            font-size: 0.95rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 15px;
            color: var(--text-dark);
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

        .btn-buy-now {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            box-shadow: 0 4px 6px rgba(245, 158, 11, 0.2);
        }
        
        .btn-buy-now:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(245, 158, 11, 0.3);
        }

        /* Product Cards Override */
        .product-card {
            background: #fff;
            border: 1px solid rgba(229, 231, 235, 0.5);
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: var(--card-shadow);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow);
            border-color: rgba(79, 70, 229, 0.2);
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .btn-add-cart {
            flex: 1;
        }

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
                                <?php echo in_array($category['id'], $selectedCategories) ? 'checked' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </label>
                    <?php endforeach; ?>
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
                    <a href="products.php" class="btn-reset">Reset Filters</a>
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
                        <?php foreach ($displayProducts as $product): ?>
                            <?php $isWishlisted = isset($_SESSION['wishlist']) && in_array($product['id'], $_SESSION['wishlist']); ?>
                            <div class="product-card" style="position: relative; cursor: pointer;" onclick="window.location.href='product-detail.php?id=<?php echo $product['id']; ?>'">
                                <?php if (isLoggedIn()): ?>
                                <div onclick="event.stopPropagation(); toggleWishlist(event, <?php echo $product['id']; ?>)" 
                                     style="position: absolute; top: 10px; right: 10px; font-size: 24px; cursor: pointer; z-index: 10; background: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: all 0.3s ease;"
                                     class="heart-icon"
                                     data-product-id="<?php echo $product['id']; ?>">
                                    <?php echo $isWishlisted ? '❤️' : '🤍'; ?>
                                </div>
                                <?php endif; ?>
                                <div class="product-image" style="overflow: hidden; display: flex; align-items: center; justify-content: center; background: #fff;">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: contain; transition: transform 0.3s ease;">
                                    <?php else: ?>
                                        <?php echo $product['emoji']; ?>
                                    <?php endif; ?>
                                </div>
                                <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <div class="product-rating"><?php echo renderStars($product['rating']); ?> <?php echo $product['rating']; ?> (<?php echo $product['reviews']; ?>)</div>
                                <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                                <div class="product-footer">
                                    <span class="stock-info">Stock: <?php echo $product['stock']; ?></span>
                                    <div class="product-actions" onclick="event.stopPropagation();">
                                        <?php if ($product['stock'] > 0): ?>
                                            <button type="button" onclick="(function(e, id, name){ e.preventDefault(); e.stopPropagation(); var fd = new FormData(); fd.append('action', 'add'); fd.append('product_id', id); fd.append('quantity', 1); fetch('cart.php', {method: 'POST', body: fd}).then(res => res.json()).then(data => { if(data.success) { showToast('🛒 ' + name + ' added to cart!', 'success', 3500); var badge = document.querySelector('.badge'); if(badge){ badge.textContent = data.cartCount || (parseInt(badge.textContent) + 1); badge.style.display = 'flex'; } } else if(data.alreadyInCart) { showToast('ℹ️ ' + name + ' is already in your cart!', 'info', 3500); } else { showToast('❌ ' + (data.message || 'Error adding to cart'), 'error', 3000); } }).catch(() => showToast('❌ Error adding to cart', 'error', 3000)); return false; })(event, <?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')" class="btn btn-primary btn-add-cart" data-product-id="<?php echo $product['id']; ?>">
                                                Add to Cart
                                            </button>
                                            <a href="checkout.php?product_id=<?php echo $product['id']; ?>&qty=1&reset_shipping=1" class="btn btn-buy-now">Buy Now</a>
                                        <?php else: ?>
                                            <button class="btn btn-disabled" disabled>Out of Stock</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state" style="text-align: center; padding: 60px 20px; background: #f8f9fa; border-radius: 12px;">
                        <div style="font-size: 40px; margin-bottom: 20px;">🔍</div>
                        <h3 style="color: #333; margin-bottom: 10px;">No products found</h3>
                        <p style="color: #666; margin-bottom: 20px;">Try adjusting your filters or price range.</p>
                        <a href="products.php" class="btn btn-primary" style="display: inline-block;">Clear All Filters</a>
                    </div>
                <?php endif; ?>
            </main>
        </form>
    </section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
