<?php
/**
 * Wishlist Management
 * Handles adding/removing products from user's wishlist
 */

session_start();
// Load Application Bootstrap
require_once 'app/bootstrap.php';

$pageTitle = 'My Wishlist';

// Get current user ID if logged in
$userId = isLoggedIn() ? getCurrentUser()['id'] : null;

// Handle AJAX requests BEFORE requireLogin (so we can return proper JSON)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    // Check if user is logged in for AJAX requests
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login to use wishlist', 'redirect' => 'signin.php']);
        exit;
    }
    
    // Initialize wishlist from DB if not in session
    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = loadUserWishlist($userId);
    }
    
    // Ensure wishlist is an array
    if (!is_array($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }
    
    $action = $_POST['action'];
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    
    if ($action === 'add' && $productId > 0) {
        if (!in_array($productId, $_SESSION['wishlist'])) {
            $_SESSION['wishlist'][] = $productId;
        }
        saveUserWishlist($userId, $_SESSION['wishlist']); // Pass ID, not email
        echo json_encode(['success' => true, 'message' => 'Added to wishlist', 'count' => count($_SESSION['wishlist'])]);
    } elseif ($action === 'remove' && $productId > 0) {
        $_SESSION['wishlist'] = array_filter($_SESSION['wishlist'], function($id) use ($productId) {
            return $id !== $productId;
        });
        $_SESSION['wishlist'] = array_values($_SESSION['wishlist']); // Re-index array
        saveUserWishlist($userId, $_SESSION['wishlist']); // Pass ID, not email
        echo json_encode(['success' => true, 'message' => 'Removed from wishlist', 'count' => count($_SESSION['wishlist'])]);
    } elseif ($action === 'get_wishlist') {
        echo json_encode(['success' => true, 'wishlist' => $_SESSION['wishlist']]);
    } elseif ($action === 'get_count') {
        echo json_encode(['success' => true, 'count' => count($_SESSION['wishlist'])]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

// For page view, require login
requireLogin();

// Load wishlist from DB on page load
if (!isset($_SESSION['wishlist']) && $userId) {
    $_SESSION['wishlist'] = loadUserWishlist($userId);
}

// Ensure wishlist is an array
if (!is_array($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// Get wishlist items with product details
$wishlistItems = [];
if (!empty($_SESSION['wishlist'])) {
    // Reverse to show newest first if desired, or keep as is
    $itemsToShow = array_reverse($_SESSION['wishlist']);
    foreach ($itemsToShow as $productId) {
        $product = getProductById($productId);
        if ($product) {
            $wishlistItems[] = $product;
        }
    }
}
?>
<?php include TEMPLATES_PATH . '/header.php'; ?>
    <script src="public/js/wishlist.js"></script>
    <script src="public/js/toast.js"></script>
    <script src="public/js/cart.js"></script>

    <!-- My Wishlist Page -->
    <section class="container wishlist-section">
        <div class="wishlist-header">
            <h1 class="section-title">❤️ My Wishlist</h1>
            <div class="wishlist-count-badge">
                <?php echo count($wishlistItems); ?> item<?php echo count($wishlistItems) !== 1 ? 's' : ''; ?>
            </div>
        </div>

        <?php if (count($wishlistItems) > 0): ?>
            <div class="products-grid wishlist-grid">
                <?php foreach ($wishlistItems as $product): ?>
                    <?php $isWishlisted = true; // Always true on wishlist page ?>
                    
                    <div class="product-card" onclick="window.location.href='product-detail?id=<?php echo $product['id']; ?>'">
                        
                        <!-- Remove from Wishlist Button -->
                        <div class="card-wishlist-btn" 
                             onclick="event.stopPropagation(); toggleWishlist(event, <?php echo $product['id']; ?>)"
                             data-product-id="<?php echo $product['id']; ?>">
                            ❤️
                        </div>

                        <div class="product-image-container">
                            <div class="product-image-content">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <span class="wishlist-emoji"><?php echo $product['emoji']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="product-info">
                            <div class="product-category"><?php echo htmlspecialchars($product['brand'] ?? 'General'); ?></div>
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            
                            <div class="product-price-row">
                                <div class="price-current"><?php echo formatPrice($product['price']); ?></div>
                                <div class="rating-block">
                                    ⭐ <?php echo $product['rating']; ?> <span class="rating-text-muted">(<?php echo $product['reviews']; ?>)</span>
                                </div>
                            </div>

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

                                <div class="action-buttons" onclick="event.stopPropagation();">
                                    <?php if ($product['stock'] > 0): ?>
                                        <button onclick="(function(e, id, name){ e.preventDefault(); e.stopPropagation(); var fd = new FormData(); fd.append('action', 'add'); fd.append('product_id', id); fd.append('quantity', 1); fetch('cart.php', {method: 'POST', body: fd}).then(res => res.json()).then(data => { if(data.success) { showToast('🛒 ' + name + ' added to cart!', 'success', 3500); var badge = document.querySelector('.badge'); if(badge){ badge.textContent = data.cartCount || (parseInt(badge.textContent) + 1); badge.style.display = 'flex'; } } else if(data.alreadyInCart) { showToast('ℹ️ ' + name + ' is already in your cart!', 'info', 3500); } else { showToast('❌ ' + (data.message || 'Error adding to cart'), 'error', 3000); } }).catch(() => showToast('❌ Error adding to cart', 'error', 3000)); return false; })(event, <?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')" 
                                                class="btn-modern btn-outline-cart">
                                            Add to Cart 
                                        </button>
                                        <a href="checkout?product_id=<?php echo $product['id']; ?>&qty=1&reset_shipping=1" class="btn-modern btn-gradient-buy">
                                            Buy Now
                                        </a>
                                    <?php else: ?>
                                        <button class="btn-modern btn-outline-cart btn-out-stock-wishlist" disabled>Out of Stock</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <!-- Empty Wishlist Message -->
            <div class="empty-wishlist-container">
                <div class="empty-wishlist-icon">🤍</div>
                <h2 class="empty-wishlist-title">Your Wishlist is Empty</h2>
                <p class="empty-wishlist-text">Start adding products to your wishlist to save them for later!</p>
                <a href="products" class="btn btn-primary btn-explore-wishlist">
                    Explore Products
                </a>
            </div>
        <?php endif; ?>
    </section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
