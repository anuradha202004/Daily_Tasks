<?php include TEMPLATES_PATH . '/header.php'; ?>

<!-- Scripts -->
<script src="<?php echo baseUrl('js/wishlist.js'); ?>"></script>
<script src="<?php echo baseUrl('js/cart.js'); ?>"></script>
<script src="<?php echo baseUrl('js/toast.js'); ?>"></script>

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
                <?php foreach ($data['categories'] as $category): ?>
                    <label class="custom-checkbox">
                        <input type="checkbox" name="categories[]" value="<?php echo $category['id']; ?>"
                            <?php echo in_array($category['id'], $data['filters']['categories']) ? 'checked' : ''; ?>
                            onchange="this.form.submit()">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="filter-group">
                <div class="filter-title">Brands</div>
                <?php if (!empty($data['brands'])): ?>
                    <?php foreach ($data['brands'] as $brand): ?>
                        <label class="custom-checkbox">
                            <input type="checkbox" name="brands[]" value="<?php echo $brand['id']; ?>"
                                <?php echo in_array($brand['id'], $data['filters']['brands']) ? 'checked' : ''; ?>
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
                           value="<?php echo $data['filters']['min_price']; ?>" min="<?php echo $data['filters']['global_min']; ?>" max="<?php echo $data['filters']['global_max']; ?>">
                    <span class="text-mute">-</span>
                    <input type="number" name="max_price" class="price-input" placeholder="Max" 
                           value="<?php echo $data['filters']['max_price']; ?>" min="<?php echo $data['filters']['global_min']; ?>" max="<?php echo $data['filters']['global_max']; ?>">
                </div>
            </div>

            <div class="filter-group">
                <button type="submit" class="btn-apply">Apply Filters</button>
                <a href="products" class="btn-reset">Reset Filters</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main>
            <!-- Toolbar -->
            <div class="products-toolbar">
                <div class="text-dark-600">
                    Found <span class="text-primary-blue"><?php echo count($data['products']); ?></span> items
                </div>
                
                <div class="flex-align-gap-10">
                    <span class="text-small-666">Sort by:</span>
                    <select name="sort" class="sort-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="newest" <?php echo $data['filters']['sort'] == 'newest' ? 'selected' : ''; ?>>Newest Arrivals</option>
                        <option value="popular" <?php echo $data['filters']['sort'] == 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                        <option value="price_asc" <?php echo $data['filters']['sort'] == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo $data['filters']['sort'] == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="name_asc" <?php echo $data['filters']['sort'] == 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <?php if (count($data['products']) > 0): ?>
                <div class="products-grid">
                    <?php foreach ($data['products'] as $index => $product): ?>
                        <?php $isWishlisted = isset($_SESSION['wishlist']) && in_array($product['id'], $_SESSION['wishlist']); ?>
                        
                        <!-- MODERN PRODUCT CARD -->
                        <?php 
                            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product['name'])));
                            $productUrl = baseUrl('product/' . $slug);
                        ?>
                        <div class="product-card" onclick="window.location.href='<?php echo $productUrl; ?>'">
                            
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
                                        <?php 
                                            $imgSrc = $product['image'];
                                            if (strpos($imgSrc, 'http') === 0) {
                                                // External URL, use as is
                                            } else {
                                                // Local file: normalize path
                                                // If it starts with public/ or /public/, remove it
                                                $imgSrc = preg_replace('/^(\/)?public\//', '', $imgSrc);
                                                $imgSrc = baseUrl($imgSrc);
                                            }
                                        ?>
                                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
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
                                            <a href="checkout?product_id=<?php echo $product['id']; ?>&qty=1&reset_shipping=1" class="btn-modern btn-gradient-buy">
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
                    <p class="mb-20-gray">Try adjusting your filters or price range.</p>
                    <a href="products" class="btn btn-primary inline-block">Clear All Filters</a>
                </div>
            <?php endif; ?>
        </main>
    </form>
</section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
