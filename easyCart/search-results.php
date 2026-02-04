<?php
session_start();

// Load Application Bootstrap
require_once 'app/bootstrap.php';

// Load cart and wishlist from file if user is logged in
// Maintain session state for logged-in users
if (isLoggedIn()) {
    $currentUser = getCurrentUser();
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = loadUserCart($currentUser['id']);
    }
    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = loadUserWishlist($currentUser['id']);
    }
}

$query = isset($_GET['q']) ? $_GET['q'] : '';
$searchResults = searchProducts($query);

$pageTitle = $query ? "Search: $query" : "Search Products";
?>
<?php include TEMPLATES_PATH . '/header.php'; ?>
    <script src="public/js/toast.js"></script>
    <script src="public/js/wishlist.js"></script>

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
                        
                        <div class="product-image" style="overflow: hidden; display: flex; align-items: center; justify-content: center; background: #fff;">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: contain; transition: transform 0.3s ease;">
                            <?php else: ?>
                                <?php echo $product['emoji']; ?>
                            <?php endif; ?>
                        </div>

                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-rating"><?php echo renderStars($product['rating']); ?> <?php echo $product['rating']; ?> (<?php echo $product['reviews']; ?> reviews)</div>
                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                        <div class="product-footer">
                            <span class="stock-info">Stock: <?php echo $product['stock']; ?> units</span>
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

<?php include TEMPLATES_PATH . '/footer.php'; ?>
