<?php
require_once 'data.php';
require_once 'auth.php';

$query = isset($_GET['q']) ? $_GET['q'] : '';
$searchResults = searchProducts($query);

$pageTitle = $query ? "Search: $query" : "Search Products";
?>
<?php include 'header.php'; ?>

    <!-- Search Results Page -->
    <section class="container" style="padding: 40px 0;">
        <h1 class="section-title">Search Results</h1>
        
        <div class="search-info" style="background: linear-gradient(135deg, #e0e7ff 0%, #f3e8ff 100%); padding: 20px; border-radius: 12px; margin: 20px 0; border-left: 4px solid #2563eb;">
            <p style="font-size: 16px; color: #1f2937; margin: 0;">
                Showing results for: <strong style="color: #2563eb; font-size: 18px;"><?php echo htmlspecialchars($query); ?></strong>
            </p>
            <p style="margin-top: 8px; color: #6b7280; font-size: 14px;">
                Found <strong><?php echo count($searchResults); ?></strong> product(s)
            </p>
        </div>

        <?php if (count($searchResults) > 0): ?>
            <div class="products-grid" style="margin-top: 30px;">
                <?php foreach ($searchResults as $product): ?>
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
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 80px; margin-bottom: 20px;">🔍</div>
                <h2 style="font-size: 28px; font-weight: 700; color: #1f2937; margin-bottom: 12px;">No Products Found</h2>
                <p style="font-size: 16px; color: #6b7280; margin-bottom: 8px;">
                    We couldn't find any products matching "<strong><?php echo htmlspecialchars($query); ?></strong>"
                </p>
                <p style="font-size: 14px; color: #9ca3af; margin-bottom: 20px;">
                    Try searching with different keywords or browse our <a href="products.php" style="color: #2563eb; text-decoration: none; font-weight: 600;">full product catalog</a>
                </p>
            </div>
        <?php endif; ?>
    </section>

<?php include 'footer.php'; ?>
