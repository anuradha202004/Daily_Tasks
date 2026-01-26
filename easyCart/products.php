<?php
session_start();

// Include data and auth
require_once 'includes/data.php';
require_once 'includes/auth.php';

$pageTitle = 'Products';

// Load cart and wishlist from file if user is logged in
if (isLoggedIn()) {
    if (!isset($_SESSION['cart'])) {
        initializeCartFromFile();
    }
    if (!isset($_SESSION['wishlist'])) {
        initializeWishlistFromFile();
    }
}

// Get selected category (if any)
$selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : null;

// Filter products by category if selected
$displayProducts = $selectedCategory 
    ? getProductsByCategory($selectedCategory)
    : $products;

// Sort by latest (or by ID for consistency)
$displayProducts = array_reverse($displayProducts, true);
?>
<?php include 'includes/header.php'; ?>
    <script src="js/wishlist.js"></script>
    <script src="js/cart.js"></script>

    <!-- Products Page -->
    <section class="container" style="padding: 40px 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 class="section-title">Our Products</h1>
            <div style="background: #e8f4f8; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; color: #2563eb;">
                Showing <?php echo count($displayProducts); ?> product<?php echo count($displayProducts) !== 1 ? 's' : ''; ?>
            </div>
        </div>
        
        <div class="category-filters">
            <a href="products.php" class="filter-btn <?php echo !$selectedCategory ? 'active' : ''; ?>">All Products (<?php echo count($products); ?>)</a>
            <?php foreach ($categories as $category): ?>
                <?php $categoryProducts = getProductsByCategory($category['id']); ?>
                <a href="products.php?category=<?php echo $category['id']; ?>" 
                   class="filter-btn <?php echo $selectedCategory == $category['id'] ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($category['name']); ?> (<?php echo count($categoryProducts); ?>)
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($selectedCategory && isset($categories[$selectedCategory])): ?>
            <div class="category-info" style="background: #f0f4f8; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h3><?php echo htmlspecialchars($categories[$selectedCategory]['name']); ?></h3>
                <p><?php echo htmlspecialchars($categories[$selectedCategory]['description']); ?></p>
                <p style="margin-top: 10px; color: #666;">Found <?php echo count($displayProducts); ?> product(s)</p>
            </div>
        <?php endif; ?>

        <?php if (count($displayProducts) > 0): ?>
            <div class="products-grid" style="margin-top: 30px;">
                <?php foreach ($displayProducts as $product): ?>
                    <?php $isWishlisted = isset($_SESSION['wishlist']) && in_array($product['id'], $_SESSION['wishlist']); ?>
                    <div class="product-card" style="position: relative; cursor: pointer;" onclick="window.location.href='product-detail.php?id=<?php echo $product['id']; ?>'">
                        <?php if (isLoggedIn()): ?>
                        <div onclick="event.stopPropagation(); toggleWishlist(event, <?php echo $product['id']; ?>)" 
                             style="position: absolute; top: 10px; right: 10px; font-size: 24px; cursor: pointer; z-index: 10; background: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: all 0.3s ease;"
                             onmouseover="this.style.transform='scale(1.1)'"
                             onmouseout="this.style.transform='scale(1)'"
                             class="heart-icon"
                             data-product-id="<?php echo $product['id']; ?>">
                            <?php echo $isWishlisted ? '❤️' : '🤍'; ?>
                        </div>
                        <?php endif; ?>
                        <div class="product-image"><?php echo $product['emoji']; ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-rating"><?php echo renderStars($product['rating']); ?> <?php echo $product['rating']; ?> (<?php echo $product['reviews']; ?> reviews)</div>
                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                        <div class="product-footer">
                            <span class="stock-info">Stock: <?php echo $product['stock']; ?> units</span>
                            <div class="product-actions" onclick="event.stopPropagation();">
                                <?php if ($product['stock'] > 0): ?>
                                    <button type="button" onclick="(function(e, id){ e.preventDefault(); e.stopPropagation(); alert('Product added successfully'); var fd = new FormData(); fd.append('action', 'add'); fd.append('product_id', id); fd.append('quantity', 1); fetch('cart.php', {method: 'POST', body: fd}).then(function(){ var badge = document.querySelector('.badge'); if(badge){ var count = parseInt(badge.textContent) || 0; badge.textContent = count + 1; badge.style.display = 'flex'; } }); return false; })(event, <?php echo $product['id']; ?>)" class="btn btn-primary btn-add-cart" data-product-id="<?php echo $product['id']; ?>">
                                        Add to Cart
                                    </button>
                                    <a href="<?php echo isLoggedIn() ? 'checkout.php?product_id=' . $product['id'] . '&qty=1' : 'signin.php?redirect=products'; ?>" class="btn btn-buy-now">Buy Now</a>
                                <?php else: ?>
                                    <button class="btn btn-disabled" disabled>Out of Stock</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 0;">
                <p style="font-size: 18px; color: #666;">No products found in this category.</p>
            </div>
        <?php endif; ?>
    </section>

<?php include 'includes/footer.php'; ?>
