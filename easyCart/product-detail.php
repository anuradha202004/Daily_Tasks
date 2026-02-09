<?php
session_start();

// Load Application Bootstrap
require_once 'app/bootstrap.php';

// Get product ID from URL
$productId = isset($_GET['id']) ? intval($_GET['id']) : null;
$product = $productId ? getProductById($productId) : null;

// Redirect to products page if product not found
if (!$product) {
    header('Location: products');
    exit;
}

$pageTitle = htmlspecialchars($product['name']);
$category = getCategoryById($product['category_id']);
$brand = getBrandById($product['brand_id']);

// Check if user is logged in
$isLoggedIn = isLoggedIn();

// Handle add to cart logic (kept for functionality)
$addToCartMessage = '';
$addToCartError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if ($quantity > 0 && $quantity <= $product['stock']) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = ['product_id' => $productId, 'quantity' => $quantity];
        }
        
        if ($isLoggedIn && isset($_SESSION['user_email'])) {
            saveUserCart($_SESSION['user_email'], $_SESSION['cart']);
        }
        
        $addToCartMessage = 'Product added to cart successfully!';
    } else {
        $addToCartError = 'Invalid quantity. Please check stock availability.';
    }
}
?>
<?php include TEMPLATES_PATH . '/header.php'; ?>
    <script src="public/js/product-detail.js"></script>
    <script src="public/js/wishlist.js"></script>
    <script src="public/js/cart.js"></script>
    <script src="public/js/toast.js"></script>

    <?php if ($addToCartMessage): ?>
        <script>document.addEventListener('DOMContentLoaded', () => showToast('<?php echo htmlspecialchars($addToCartMessage); ?>', 'success', 3500));</script>
    <?php endif; ?>
    <?php if ($addToCartError): ?>
        <script>document.addEventListener('DOMContentLoaded', () => showToast('<?php echo htmlspecialchars($addToCartError); ?>', 'error', 4000));</script>
    <?php endif; ?>

    <!-- Page Styles Moved to style.css -->

    <div class="product-page-wrapper">
        <div class="container">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="products">Products</a> / 
                <a href="products?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a> / 
                <span style="color: var(--text-main); font-weight: 500;"><?php echo htmlspecialchars($product['name']); ?></span>
            </div>

            <div class="product-main-grid">
                <!-- Visuals -->
                <div class="product-gallery">
                    <div class="main-image-frame">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <div style="font-size: 6rem;"><?php echo $product['emoji']; ?></div>
                        <?php endif; ?>
                        
                        <?php if (isLoggedIn()): ?>
                        <!-- Wishlist Toggle -->
                        <div onclick="toggleWishlist(event, <?php echo $product['id']; ?>)" 
                             style="position: absolute; top: 15px; right: 15px; background: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.1); cursor: pointer; font-size: 1.2rem; transition: transform 0.2s;"
                             onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                             <?php echo (isset($_SESSION['wishlist']) && in_array($product['id'], $_SESSION['wishlist'])) ? '❤️' : '🤍'; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Content -->
                <div class="product-details">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    
                    <div class="meta-row">
                        <span class="rating-badge">★ <?php echo $product['rating']; ?></span>
                        <span><?php echo $product['reviews']; ?> Reviews</span>
                        <span style="color: #d1d5db;">|</span>
                        <span>Brand: <strong><?php echo htmlspecialchars($brand['name'] ?? 'Generic'); ?></strong></span>
                    </div>

                    <div class="price-row">
                        <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                        <?php if ($product['stock'] > 0): ?>
                            <span class="stock-status in-stock">In Stock (<?php echo $product['stock']; ?>)</span>
                        <?php else: ?>
                            <span class="stock-status out-stock">Out of Stock</span>
                        <?php endif; ?>
                    </div>

                    <p class="description-text"><?php echo htmlspecialchars($product['description']); ?></p>

                    <!-- Form -->
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="options-grid">
                            <div class="option-group">
                                <label class="option-label">Select Color</label>
                                <div class="color-selector">
                                    <div class="color-dot active" style="background: #111827;"></div>
                                    <div class="color-dot" style="background: #3b82f6;"></div>
                                    <div class="color-dot" style="background: #ef4444;"></div>
                                    <div class="color-dot" style="background: #10b981;"></div>
                                </div>
                            </div>

                            <div class="option-group">
                                <label class="option-label">Quantity</label>
                                <div class="quantity-wrapper">
                                    <button type="button" class="qty-btn" onclick="updateQty(-1)">−</button>
                                    <input type="number" id="qtyInput" name="quantity" class="qty-input" value="1" min="1" max="<?php echo $product['stock']; ?>" readonly>
                                    <button type="button" class="qty-btn" onclick="updateQty(1, <?php echo $product['stock']; ?>)">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <?php if ($product['stock'] > 0): ?>
                                <button type="button" class="btn-action btn-cart" onclick="addToCartAjax(event, <?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')">
                                    Add to Cart
                                </button>
                                <button type="button" class="btn-action btn-buy" onclick="buyNow(<?php echo $product['id']; ?>, <?php echo $product['stock']; ?>)">
                                    Buy Now
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn-action btn-cart" style="grid-column: span 2; opacity: 0.5;" disabled>
                                    Currently Unavailable
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="features-list">
                        <div class="feature-box">
                            <span class="feature-icon">🚚</span>
                            <div class="feature-text">Fast Delivery</div>
                        </div>
                        <div class="feature-box">
                            <span class="feature-icon">🛡️</span>
                            <div class="feature-text">Secure Checkout</div>
                        </div>
                        <div class="feature-box">
                            <span class="feature-icon">↩️</span>
                            <div class="feature-text">30-Day Returns</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommended Products Reuse -->
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 25px;">You Might Also Like</h3>
            <div class="products-grid">
                <?php
                $related = array_slice(getProductsByCategory($product['category_id']), 0, 4);
                foreach ($related as $rel):
                    if ($rel['id'] == $product['id']) continue;
                    $isWishlisted = isset($_SESSION['wishlist']) && in_array($rel['id'], $_SESSION['wishlist']);
                ?>
                    <div class="product-card" onclick="window.location.href='product-detail?id=<?php echo $rel['id']; ?>'">
                        
                        <?php if (isLoggedIn()): ?>
                            <div class="card-wishlist-btn" 
                                 onclick="event.stopPropagation(); toggleWishlist(event, <?php echo $rel['id']; ?>)"
                                 data-product-id="<?php echo $rel['id']; ?>">
                                <?php echo $isWishlisted ? '❤️' : '🤍'; ?>
                            </div>
                        <?php endif; ?>

                        <div class="product-image-container">
                            <div class="product-image-content">
                                <?php if (!empty($rel['image'])): ?>
                                    <img src="<?php echo $rel['image']; ?>" alt="<?php echo htmlspecialchars($rel['name']); ?>">
                                <?php else: ?>
                                    <span style="font-size: 60px;"><?php echo $rel['emoji']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="product-info">
                            <div class="product-category"><?php echo htmlspecialchars($rel['brand'] ?? 'General'); ?></div>
                            <h3 class="product-title"><?php echo htmlspecialchars($rel['name']); ?></h3>
                            
                            <div class="product-price-row">
                                <div class="price-current"><?php echo formatPrice($rel['price']); ?></div>
                                <div class="rating-block">
                                    ⭐ <?php echo $rel['rating']; ?> <span style="color: #9ca3af; font-weight: 400;">(<?php echo $rel['reviews']; ?>)</span>
                                </div>
                            </div>

                            <div class="product-footer">
                                <div class="action-buttons" onclick="event.stopPropagation();">
                                    <a href="product-detail?id=<?php echo $rel['id']; ?>" class="btn-modern btn-gradient-buy" style="width: 100%;">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        // Persist quantity across refreshes
        const currentProductId = <?php echo $product['id']; ?>;
        const maxStock = <?php echo $product['stock']; ?>;

        document.addEventListener('DOMContentLoaded', () => {
            const savedQty = localStorage.getItem('qty_' + currentProductId);
            const input = document.getElementById('qtyInput');
            if (savedQty && input) {
                let val = parseInt(savedQty);
                if (val < 1) val = 1;
                if (val > maxStock) val = maxStock;
                input.value = val;
            }
        });

        function updateQty(change, max = 100) {
            const input = document.getElementById('qtyInput');
            let val = parseInt(input.value) + change;
            if (val < 1) val = 1;
            if (val > max) val = max;
            input.value = val;
            
            localStorage.setItem('qty_' + currentProductId, val);
        }

        function buyNow(id, stock) {
            const qty = document.getElementById('qtyInput').value;
            window.location.href = `checkout?product_id=${id}&qty=${qty}&reset_shipping=1`;
        }

        function addToCartAjax(e, id, name) {
            e.preventDefault();
            const qty = document.getElementById('qtyInput').value;
            const btn = e.currentTarget;
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '⏳ Adding...';
            btn.disabled = true;

            const fd = new FormData();
            fd.append('action', 'add');
            fd.append('product_id', id);
            fd.append('quantity', qty);

            fetch('cart', { method: 'POST', body: fd })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(`🛒 ${name} added to cart!`, 'success', 3500);
                        
                        // Update default header badge
                        const badges = document.querySelectorAll('.badge');
                        badges.forEach(b => {
                           if (!b.classList.contains('wishlist-badge')) {
                               b.textContent = data.cartCount;
                               b.style.display = 'flex';
                           }
                        });
                        
                        btn.innerHTML = '✓ Added';
                        setTimeout(() => {
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        }, 2000);
                    } else {
                        showToast(data.message || 'Error adding to cart', 'error');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    showToast('Network error', 'error');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        }

        // Color selection logic
        document.querySelectorAll('.color-dot').forEach(dot => {
            dot.addEventListener('click', function() {
                document.querySelectorAll('.color-dot').forEach(d => d.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
<?php include TEMPLATES_PATH . '/footer.php'; ?>
