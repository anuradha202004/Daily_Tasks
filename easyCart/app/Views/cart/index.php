<?php include TEMPLATES_PATH . '/header.php'; ?>

<!-- Shopping Cart Page -->
<section class="container cart-section">
    <h1 class="section-title">Shopping Cart</h1>

    <!-- Login Prompt for Non-Logged-In Users -->
    <?php if (!isLoggedIn() && count($data['cartItems']) > 0): ?>
        <div class="cart-login-prompt">
            <div class="cart-login-text">
                <h3>🔒 Secure Checkout Available</h3>
                <p>Please log in to proceed with checkout and complete your purchase securely.</p>
            </div>
            <div class="cart-login-actions">
                <a href="signin" class="btn-login-blue">
                    Login
                </a>
                <a href="signup" class="btn-signup-outline">
                    Sign Up
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (count($data['cartItems']) > 0): ?>
        <div class="cart-grid">
            <!-- Cart Items -->
            <div>
                <div class="cart-items-container">
                    <!-- Cart Header -->
                    <div class="cart-header">
                        <h3 id="cart-page-count">
                            <?php echo count($data['cartItems']); ?> 
                            Item<?php echo count($data['cartItems']) !== 1 ? 's' : ''; ?> in Cart
                        </h3>
                    </div>

                    <!-- Cart Items List -->
                    <?php foreach ($data['cartItems'] as $index => $item): ?>
                        <div class="cart-item" data-product-id="<?php echo $item['product']['id']; ?>" data-product-price="<?php echo $item['product']['price']; ?>">
                            <!-- Product Image & Name -->
                            <div class="cart-product-info">
                                <!-- Product Image -->
                                <div class="cart-img-wrapper">
                                    <?php if (!empty($item['product']['image'])): ?>
                                        <?php 
                                            $imgSrc = $item['product']['image'];
                                            if (strpos($imgSrc, 'http') === 0) {
                                                // External URL
                                            } else {
                                                $imgSrc = preg_replace('/^(\/)?public\//', '', $imgSrc);
                                                $imgSrc = baseUrl($imgSrc);
                                            }
                                        ?>
                                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" class="cart-img">
                                    <?php else: ?>
                                        <span style="font-size: 2rem;"><?php echo $item['product']['emoji']; ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Product Details -->
                                <div class="cart-details">
                                    <?php 
                                        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $item['product']['name'])));
                                        $productUrl = baseUrl('product/' . $slug);
                                    ?>
                                    <h3 class="product-title">
                                        <a href="<?php echo $productUrl; ?>"><?php echo htmlspecialchars($item['product']['name']); ?></a>
                                    </h3>
                                    <div class="cart-meta">
                                        <span class="meta-badge-blue">
                                            Unit: <?php echo formatPrice($item['product']['price']); ?>
                                        </span>
                                        <span class="meta-badge-gray">
                                            Stock: <?php echo $item['product']['stock']; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="qty-control-group">
                                <button type="button" onclick="decreaseQuantity(this)" class="qty-btn">−</button>
                                <input type="number" class="quantity-input qty-input" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['product']['stock']; ?>" 
                                       onchange="updateQuantityAndSummary(this)">
                                <button type="button" onclick="increaseQuantity(this)" class="qty-btn">+</button>
                            </div>

                            <!-- Total Price -->
                            <div class="cart-total-col">
                                <div class="cart-total-label">Total</div>
                                <p class="item-total cart-item-total">
                                    <?php echo formatPrice($item['itemTotal']); ?>
                                </p>
                            </div>

                            <!-- Remove Button -->
                            <button type="button" onclick="removeCartItem(this)" class="btn-remove">
                                🗑️ Remove
                            </button>
                        </div>
                    <?php endforeach; ?>

                    <!-- Clear Cart Button -->
                    <div class="cart-clear-section">
                        <form method="POST" class="inline-form" action="cart">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn-clear-cart">🧹Clear Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div>
                <div class="cart-summary-box">
                    <h3 class="summary-title">Order Summary</h3>

                    <div class="cart-summary-divider">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span class="summary-val" id="summary-subtotal"><?php echo formatPrice($data['subtotal']); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Bulk Discount</span>
                            <span class="summary-val discount" id="summary-discount">-<?php echo formatPrice($data['discount']); ?></span>
                        </div>

                    </div>

                    <div class="summary-total-row">
                        <span>Total Amount</span>
                        <span class="summary-total-val" id="summary-total">
                            <?php echo formatPrice($data['total']); ?>
                        </span>
                    </div>

                    <a href="<?php echo isLoggedIn() ? 'checkout?reset_shipping=1' : 'signin?redirect=cart'; ?>" class="btn-checkout">
                        <?php echo isLoggedIn() ? 'Proceed to Checkout' : 'Login to Checkout'; ?>
                    </a>

                    <a href="products" class="btn-continue">
                        Continue Shopping
                    </a>
                </div>

                <!-- Promo Info -->
                <div class="secure-info-box">
                    <p class="secure-text">
                        🔒 <strong>Secure Checkout:</strong> All transactions are protected
                    </p>
                </div>

                <!-- Bulk Discount Info -->
                <?php
                $currentDiscount = $data['discount'];
                ?>
                
                <?php if ($currentDiscount > 0): ?>
                    <div class="secure-info-box" style="background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%); border-left-color: #10b981;">
                        <p class="secure-text" style="color: #10b981;">
                            🎉 <strong>Quantity Discount Active:</strong> You're saving <?php echo formatPrice($currentDiscount); ?>
                            <br><small style="font-size: 12px; opacity: 0.8;">You get 1% off for every unit of a product in your cart!</small>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="secure-info-box" style="background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%); border-left-color: #2563eb;">
                        <p class="secure-text" style="color: #2563eb;">
                            💡 <strong>Smart Tip:</strong> The more quantity you buy of a single product, the higher your discount! (1% per item)
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Empty Cart Message -->
        <div class="cart-empty-container">
            <div class="cart-empty-icon">🛒</div>
            <h2 class="cart-empty-title">Your Cart is Empty</h2>
            <p class="cart-empty-text">Add some products to get started!</p>
            <a href="products" class="btn btn-primary btn-start-shopping">
                Start Shopping
            </a>
        </div>
    <?php endif; ?>
</section>

<script src="<?php echo baseUrl('js/cart.js'); ?>"></script>
<?php include TEMPLATES_PATH . '/footer.php'; ?>
