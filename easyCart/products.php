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
    <!-- Page Styles Moved to style.css -->

    <!-- Page Content -->
    <div class="page-header">
        <div class="container">
            <h1 class="section-title mb-0">Our Products</h1>
        </div>
    </div>

    <section class="container pb-60">
        <!-- Mobile Filter Toggle -->
        <button class="filter-toggle" onclick="document.querySelector('.filters-sidebar').classList.toggle('active')">
            🔍 Show Filters & Sort
        </button>

        <form id="filterForm" class="products-layout" method="GET" action="products">
            
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
                        <div class="text-small-mute">No brands available</div>
                    <?php endif; ?>
                </div>

                <div class="filter-group">
                    <div class="filter-title">Price Range</div>
                    <div class="price-inputs">
                        <input type="number" name="min_price" class="price-input" placeholder="Min" 
                               value="<?php echo $minPrice; ?>" min="<?php echo $globalMinPrice; ?>" max="<?php echo $globalMaxPrice; ?>">
                        <span class="text-mute">-</span>
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
                    <div class="text-dark-600">
                        Found <span class="text-primary-blue"><?php echo count($displayProducts); ?></span> items
                    </div>
                    
                    <div class="flex-align-gap-10">
                        <span class="text-small-666">Sort by:</span>
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
                                            <span class="emoji-60"><?php echo $product['emoji']; ?></span>
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
                                            ⭐ <?php echo $product['rating']; ?> <span class="text-gray-400-normal">(<?php echo $product['reviews']; ?>)</span>
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
                                                <button onclick="event.preventDefault(); event.stopPropagation(); addToCart(<?php echo $product['id']; ?>, 1);" 
                                                        class="btn-modern btn-outline-cart btn-add-cart" 
                                                        data-product-id="<?php echo $product['id']; ?>">
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
                    <div class="empty-state empty-state-box">
                        <span class="font-40">🔍</span>
                        <h3 class="mt-20-dark">No products found</h3>
                        <p class="mb-20-gray">Try adjusting your filters or price range.</p>>
                        <a href="/products" class="btn btn-primary inline-block">Clear All Filters</a>
                    </div>
                <?php endif; ?>
            </main>
        </form>
    </section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
