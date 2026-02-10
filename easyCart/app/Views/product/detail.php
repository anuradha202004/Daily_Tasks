<?php include TEMPLATES_PATH . '/header.php'; ?>
<script src="<?php echo baseUrl('js/product-detail.js'); ?>"></script>
<script src="<?php echo baseUrl('js/wishlist.js'); ?>"></script>
<script src="<?php echo baseUrl('js/cart.js'); ?>"></script>
<script src="<?php echo baseUrl('js/toast.js'); ?>"></script>

<div class="product-page-wrapper">
    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="products">Products</a> / 
            <a href="products?category=<?php echo $data['product']['category_id']; ?>"><?php echo htmlspecialchars($data['category']['name']); ?></a> / 
            <span class="text-main-500"><?php echo htmlspecialchars($data['product']['name']); ?></span>
        </div>

        <div class="product-main-grid">
            <!-- Visuals -->
            <div class="product-gallery">
                <div class="main-image-frame">
                    <?php if (!empty($data['product']['image'])): ?>
                        <?php 
                            $imgSrc = $data['product']['image'];
                            if (strpos($imgSrc, 'http') === 0) {
                                // External URL
                            } else {
                                $imgSrc = preg_replace('/^(\/)?public\//', '', $imgSrc);
                                $imgSrc = baseUrl($imgSrc);
                            }
                        ?>
                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($data['product']['name']); ?>">
                    <?php else: ?>
                        <div class="emoji-large"><?php echo $data['product']['emoji']; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isLoggedIn()): ?>
                    <!-- Wishlist Toggle -->
                    <div onclick="toggleWishlist(event, <?php echo $data['product']['id']; ?>)" 
                            class="wishlist-toggle-btn"
                            data-product-id="<?php echo $data['product']['id']; ?>">
                            <?php echo (isset($_SESSION['wishlist']) && in_array($data['product']['id'], $_SESSION['wishlist'])) ? '❤️' : '🤍'; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Content -->
            <div class="product-details">
                <h1><?php echo htmlspecialchars($data['product']['name']); ?></h1>
                
                <div class="meta-row">
                    <span class="rating-badge">★ <?php echo $data['product']['rating']; ?></span>
                    <span><?php echo $data['product']['reviews']; ?> Reviews</span>
                    <span class="separator">|</span>
                    <span>Brand: <strong><?php echo htmlspecialchars($data['brand']['name'] ?? 'Generic'); ?></strong></span>
                </div>

                <div class="price-row">
                    <span class="current-price"><?php echo formatPrice($data['product']['price']); ?></span>
                    <?php if ($data['product']['stock'] > 0): ?>
                        <span class="stock-status in-stock">In Stock (<?php echo $data['product']['stock']; ?>)</span>
                    <?php else: ?>
                        <span class="stock-status out-stock">Out of Stock</span>
                    <?php endif; ?>
                </div>

                <p class="description-text"><?php echo htmlspecialchars($data['product']['description']); ?></p>

                <!-- Form -->
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="options-grid">
                        <div class="option-group">
                            <label class="option-label">Select Color</label>
                            <div class="color-selector">
                                <div class="color-dot active color-dot-black"></div>
                                <div class="color-dot color-dot-blue"></div>
                                <div class="color-dot color-dot-red"></div>
                                <div class="color-dot color-dot-green"></div>
                            </div>
                        </div>

                        <div class="option-group">
                            <label class="option-label">Quantity</label>
                            <div class="quantity-wrapper">
                                <button type="button" class="qty-btn" onclick="updateQty(-1)">−</button>
                                <input type="number" id="qtyInput" name="quantity" class="qty-input" value="1" min="1" max="<?php echo $data['product']['stock']; ?>" readonly>
                                <button type="button" class="qty-btn" onclick="updateQty(1, <?php echo $data['product']['stock']; ?>)">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <?php if ($data['product']['stock'] > 0): ?>
                            <button type="button" class="btn-action btn-cart" onclick="addToCartAjax(event, <?php echo $data['product']['id']; ?>, '<?php echo addslashes($data['product']['name']); ?>')">
                                Add to Cart
                            </button>
                            <button type="button" class="btn-action btn-buy" onclick="buyNow(<?php echo $data['product']['id']; ?>, <?php echo $data['product']['stock']; ?>)">
                                Buy Now
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn-action btn-cart btn-unavailable" disabled>
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
        <h3 class="section-title-small">You Might Also Like</h3>
        <div class="products-grid">
            <?php
            foreach ($data['related'] as $rel):
                if ($rel['id'] == $data['product']['id']) continue;
                $isWishlisted = isset($_SESSION['wishlist']) && in_array($rel['id'], $_SESSION['wishlist']);
            ?>
                <?php 
                    $relSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $rel['name'])));
                    $relUrl = baseUrl('product/' . $relSlug);
                ?>
                <div class="product-card" onclick="window.location.href='<?php echo $relUrl; ?>'">
                    
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
                                <?php 
                                    $relImgFn = $rel['image'];
                                    if (strpos($relImgFn, 'http') === 0) {
                                        // External URL
                                    } else {
                                        $relImgFn = preg_replace('/^(\/)?public\//', '', $relImgFn);
                                        $relImgFn = baseUrl($relImgFn);
                                    }
                                ?>
                                <img src="<?php echo $relImgFn; ?>" alt="<?php echo htmlspecialchars($rel['name']); ?>">
                            <?php else: ?>
                                <span class="emoji-medium"><?php echo $rel['emoji']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-info">
                        <div class="product-category"><?php echo htmlspecialchars($rel['brand'] ?? 'General'); ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($rel['name']); ?></h3>
                        
                        <div class="product-price-row">
                            <div class="price-current"><?php echo formatPrice($rel['price']); ?></div>
                            <div class="rating-block">
                                ⭐ <?php echo $rel['rating']; ?> <span class="review-text-muted">(<?php echo $rel['reviews']; ?>)</span>
                            </div>
                        </div>

                        <div class="product-footer">
                            <div class="action-buttons" onclick="event.stopPropagation();">
                                <a href="<?php echo $relUrl; ?>" class="btn-modern btn-gradient-buy" style="width: 100%;">View Details</a>
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
    const currentProductId = <?php echo $data['product']['id']; ?>;
    const maxStock = <?php echo $data['product']['stock']; ?>;

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

    const isUserLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;

    function buyNow(id, stock) {
        const qty = document.getElementById('qtyInput').value;
        const checkoutUrl = BASE_URL + `checkout?product_id=${id}&qty=${qty}&reset_shipping=1`;
        
        if (!isUserLoggedIn) {
            // Redirect to signin with return URL
            window.location.href = BASE_URL + `signin?redirect=${encodeURIComponent(`checkout?product_id=${id}&qty=${qty}&reset_shipping=1`)}`;
        } else {
            // User is logged in, go directly to checkout
            window.location.href = checkoutUrl;
        }
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

        fetch('<?php echo baseUrl('cart'); ?>', { method: 'POST', body: fd })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(`🛒 ${name} added to cart!`, 'success', 3500);
                    
                    // Update default header badge
                    const badges = document.querySelectorAll('.badge');
                    badges.forEach(b => {
                        if (!b.classList.contains('wishlist-badge')) {
                            // Only update if existing text is a number
                            if (!isNaN(parseInt(b.textContent)) || b.style.display === 'none') {
                                b.textContent = data.cartCount;
                                b.style.display = 'flex';
                            }
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
