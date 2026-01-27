<?php
/**
 * Wishlist Management
 * Handles adding/removing products from user's wishlist
 */

session_start();
require_once 'includes/data.php';
require_once 'includes/auth.php';

$pageTitle = 'My Wishlist';

// Handle AJAX requests BEFORE requireLogin (so we can return proper JSON)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    // Check if user is logged in for AJAX requests
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login to use wishlist', 'redirect' => 'signin.php']);
        exit;
    }
    
    // Initialize wishlist if not set
    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
        initializeWishlistFromFile();
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
        saveUserWishlist($_SESSION['user_email'], $_SESSION['wishlist']);
        echo json_encode(['success' => true, 'message' => 'Added to wishlist', 'count' => count($_SESSION['wishlist'])]);
    } elseif ($action === 'remove' && $productId > 0) {
        $_SESSION['wishlist'] = array_filter($_SESSION['wishlist'], function($id) use ($productId) {
            return $id !== $productId;
        });
        $_SESSION['wishlist'] = array_values($_SESSION['wishlist']); // Re-index array
        saveUserWishlist($_SESSION['user_email'], $_SESSION['wishlist']);
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

// Load wishlist from file on page load
if (!isset($_SESSION['wishlist'])) {
    initializeWishlistFromFile();
}

// Ensure wishlist is an array
if (!is_array($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// Get wishlist items with product details
$wishlistItems = [];
if (!empty($_SESSION['wishlist'])) {
    foreach ($_SESSION['wishlist'] as $productId) {
        $product = getProductById($productId);
        if ($product) {
            $wishlistItems[] = $product;
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
    <script src="js/wishlist.js"></script>
    <script src="js/toast.js"></script>
    <script src="js/cart.js"></script>

    <style>
        /* Wishlist-specific styles - Only for this page */
        .wishlist-product-card {
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .wishlist-product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.2);
        }

        .wishlist-product-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .wishlist-product-link:hover .product-title {
            color: #667eea;
        }

        .wishlist-product-card .product-actions {
            position: relative;
            z-index: 5;
        }

        .wishlist-product-card .product-actions button,
        .wishlist-product-card .product-actions a {
            pointer-events: auto;
        }
    </style>

    <!-- My Wishlist Page -->
    <section class="container" style="padding: 40px 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 class="section-title">❤️ My Wishlist</h1>
            <div style="background: #ffe8ee; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; color: #dc2626;">
                <?php echo count($wishlistItems); ?> item<?php echo count($wishlistItems) !== 1 ? 's' : ''; ?>
            </div>
        </div>

        <?php if (count($wishlistItems) > 0): ?>
            <div class="products-grid" style="margin-top: 30px;">
                <?php foreach ($wishlistItems as $product): ?>
                    <div class="product-card wishlist-product-card" style="position: relative;">
                        <!-- Heart Icon -->
                        <div onclick="toggleWishlist(event, <?php echo $product['id']; ?>)" 
                             style="
                                position: absolute;
                                top: 10px;
                                right: 10px;
                                font-size: 24px;
                                cursor: pointer;
                                z-index: 10;
                                background: white;
                                width: 40px;
                                height: 40px;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                                transition: all 0.3s ease;
                             "
                             onmouseover="this.style.transform='scale(1.1)'"
                             onmouseout="this.style.transform='scale(1)'"
                             class="heart-icon"
                             data-product-id="<?php echo $product['id']; ?>">
                            ❤️
                        </div>

                        <!-- Clickable Product Link -->
                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="wishlist-product-link">
                            <div class="product-image"><?php echo $product['emoji']; ?></div>
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-rating"><?php echo renderStars($product['rating']); ?> <?php echo $product['rating']; ?> (<?php echo $product['reviews']; ?> reviews)</div>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                        </a>
                        
                        <div class="product-footer">
                            <span class="stock-info">Stock: <?php echo $product['stock']; ?> units</span>
                            <div class="product-actions">
                                <?php if ($product['stock'] > 0): ?>
                                    <button type="button" onclick="(function(e, id, name){ e.preventDefault(); e.stopPropagation(); var fd = new FormData(); fd.append('action', 'add'); fd.append('product_id', id); fd.append('quantity', 1); fetch('cart.php', {method: 'POST', body: fd}).then(res => res.json()).then(data => { if(data.success) { showToast('🛒 ' + name + ' added to cart!', 'success', 3500); var badge = document.querySelector('.badge'); if(badge){ badge.textContent = data.cartCount || (parseInt(badge.textContent) + 1); badge.style.display = 'flex'; } } else if(data.alreadyInCart) { showToast('ℹ️ ' + name + ' is already in your cart!', 'info', 3500); } else { showToast('❌ ' + (data.message || 'Error adding to cart'), 'error', 3000); } }).catch(() => showToast('❌ Error adding to cart', 'error', 3000)); return false; })(event, <?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')" class="btn btn-primary btn-add-cart" data-product-id="<?php echo $product['id']; ?>">
                                        Add to Cart
                                    </button>
                                    <a href="checkout.php?product_id=<?php echo $product['id']; ?>&qty=1" class="btn btn-buy-now" onclick="event.stopPropagation();">Buy Now</a>
                                <?php else: ?>
                                    <button class="btn btn-disabled" disabled>Out of Stock</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <!-- Empty Wishlist Message -->
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 60px; margin-bottom: 20px;">🤍</div>
                <h2 style="color: #666; margin-bottom: 10px;">Your Wishlist is Empty</h2>
                <p style="color: #999; margin-bottom: 30px;">Start adding products to your wishlist to save them for later!</p>
                <a href="products.php" class="btn btn-primary" style="display: inline-block; padding: 12px 30px; text-decoration: none;">
                    Explore Products
                </a>
            </div>
        <?php endif; ?>
    </section>

<?php include 'includes/footer.php'; ?>
