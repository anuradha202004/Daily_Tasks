<?php
session_start();

// Include data
require_once 'data.php';

$pageTitle = 'Products';

// Get selected category (if any)
$selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : null;

// Filter products by category if selected
$displayProducts = $selectedCategory 
    ? getProductsByCategory($selectedCategory)
    : $products;

// Sort by latest (or by ID for consistency)
$displayProducts = array_reverse($displayProducts, true);
?>
<?php include 'header.php'; ?>

    <!-- Products Page -->
    <section class="container" style="padding: 40px 0;">
        <h1 class="section-title">Our Products</h1>
        
        <div class="category-filters">
            <a href="products.php" class="filter-btn <?php echo !$selectedCategory ? 'active' : ''; ?>">All Products</a>
            <?php foreach ($categories as $category): ?>
                <a href="products.php?category=<?php echo $category['id']; ?>" 
                   class="filter-btn <?php echo $selectedCategory == $category['id'] ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
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
                    <div class="product-card">
                        <div class="product-image"><?php echo $product['emoji']; ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-rating"><?php echo renderStars($product['rating']); ?> <?php echo $product['rating']; ?> (<?php echo $product['reviews']; ?> reviews)</div>
                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                        <div class="product-footer">
                            <span class="stock-info">Stock: <?php echo $product['stock']; ?> units</span>
                            <div class="product-actions">
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                                <?php if ($product['stock'] > 0): ?>
                                    <a href="checkout.php?product_id=<?php echo $product['id']; ?>&qty=1" class="btn btn-buy-now">Buy Now</a>
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

<?php include 'footer.php'; ?>
