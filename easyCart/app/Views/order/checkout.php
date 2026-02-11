<?php include TEMPLATES_PATH . '/header.php'; ?>
<script src="<?php echo URL_ROOT; ?>/js/validation.js"></script>
<script src="<?php echo URL_ROOT; ?>/js/checkout.js"></script>

<!-- Pass Promo Valid to JS -->
<script>window.activePromo = <?php echo json_encode($data['appliedPromo']); ?>;</script>

<section class="checkout-page">
    <div class="checkout-container">
        <!-- Checkout Header -->
        <div class="checkout-header">
            <h1 class="checkout-title">
                <span class="checkout-icon">🛒</span>
                Secure Checkout
            </h1>
            <p class="checkout-subtitle">Complete your order in just a few steps</p>
        </div>

        <!-- Progress Steps -->
        <div class="checkout-progress">
            <div class="progress-step active">
                <div class="step-number">1</div>
                <span class="step-label">Details</span>
            </div>
            <div class="progress-line active"></div>
            <div class="progress-step active">
                <div class="step-number">2</div>
                <span class="step-label">Shipping</span>
            </div>
            <div class="progress-line active"></div>
            <div class="progress-step active">
                <div class="step-number">3</div>
                <span class="step-label">Payment</span>
            </div>
        </div>

        <?php if (!empty($data['checkoutMessage'])): ?>
            <div class="checkout-alert checkout-alert-error">
                <span class="alert-icon">⚠️</span>
                <?php echo htmlspecialchars($data['checkoutMessage']); ?>
            </div>
        <?php endif; ?>

        <div class="checkout-grid">
            <!-- Checkout Form -->
            <div class="checkout-form-wrapper">
                <form method="POST" action="<?php echo URL_ROOT; ?>/checkout" id="checkoutForm" onsubmit="return validateCheckoutForm()">
                    <input type="hidden" name="action" value="complete_order">
                    <?php if ($data['isBuyNow'] && $data['directProduct']): ?>
                        <input type="hidden" name="product_id" value="<?php echo $data['directProduct']['id']; ?>">
                        <input type="hidden" name="qty" value="<?php echo $data['directQuantity']; ?>">
                    <?php endif; ?>

                    <!-- Personal Information Card -->
                    <div class="checkout-card">
                        <div class="card-header">
                            <span class="card-icon">👤</span>
                            <h3>Personal Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-field">
                                    <label class="field-label">First Name <span class="required">*</span></label>
                                    <input type="text" name="first_name" id="firstName" class="checkout-input" placeholder="John" required>
                                    <small class="error-message input-error-msg" id="firstNameError"></small>
                                </div>
                                <div class="form-field">
                                    <label class="field-label">Last Name <span class="required">*</span></label>
                                    <input type="text" name="last_name" id="lastName" class="checkout-input" placeholder="Doe" required>
                                    <small class="error-message input-error-msg" id="lastNameError"></small>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-field">
                                    <label class="field-label">Email Address <span class="required">*</span></label>
                                    <div class="input-with-icon">
                                        <span class="input-icon">📧</span>
                                        <input type="email" name="email" id="email" class="checkout-input with-icon" placeholder="john@example.com" required>
                                    </div>
                                    <small class="error-message input-error-msg" id="emailError"></small>
                                </div>
                                <div class="form-field">
                                    <label class="field-label">Phone Number <span class="required">*</span></label>
                                    <div class="input-with-icon">
                                        <span class="input-icon">📱</span>
                                        <input type="tel" name="phone" id="phone" class="checkout-input with-icon" placeholder="9876543210" required>
                                    </div>
                                    <small class="error-message input-error-msg" id="phoneError"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address Card -->
                    <div class="checkout-card">
                        <div class="card-header">
                            <span class="card-icon">📦</span>
                            <h3>Shipping Address</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-field full-width">
                                <label class="field-label">Street Address <span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <span class="input-icon">🏠</span>
                                    <input type="text" name="address" id="address" class="checkout-input with-icon" placeholder="123 Main Street, Apt 4B" required>
                                </div>
                                <small class="error-message input-error-msg" id="addressError"></small>
                            </div>
                            <div class="form-row three-cols">
                                <div class="form-field">
                                    <label class="field-label">City <span class="required">*</span></label>
                                    <input type="text" name="city" id="city" class="checkout-input" placeholder="New York" required>
                                    <small class="error-message input-error-msg" id="cityError"></small>
                                </div>
                                <div class="form-field">
                                    <label class="field-label">State <span class="required">*</span></label>
                                    <input type="text" name="state" id="state" class="checkout-input" placeholder="NY" required>
                                    <small class="error-message input-error-msg" id="stateError"></small>
                                </div>
                                <div class="form-field">
                                    <label class="field-label">Zip Code <span class="required">*</span></label>
                                    <input type="text" name="zip" id="zip" class="checkout-input" placeholder="10001" required>
                                    <small class="error-message input-error-msg" id="zipError"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Address Card -->
                    <div class="checkout-card">
                        <div class="card-header">
                            <span class="card-icon">🧾</span>
                            <h3>Billing Address</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-field full-width mb-20">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="billing_same" id="billing_same" checked onchange="toggleBilling()">
                                    <span class="checkmark"></span>
                                    <span class="billing-text">Same as shipping address</span>
                                </label>
                            </div>

                            <div id="billing-address-section" class="billing-section hidden">
                                <div class="form-field full-width">
                                    <label class="field-label">Billing Street Address <span class="required">*</span></label>
                                    <div class="input-with-icon">
                                        <span class="input-icon">🏠</span>
                                        <input type="text" name="billing_address" class="checkout-input with-icon" placeholder="123 Billing St">
                                    </div>
                                </div>
                                <div class="form-row three-cols">
                                    <div class="form-field">
                                        <label class="field-label">City <span class="required">*</span></label>
                                        <input type="text" name="billing_city" class="checkout-input" placeholder="New York">
                                    </div>
                                    <div class="form-field">
                                        <label class="field-label">State <span class="required">*</span></label>
                                        <input type="text" name="billing_state" class="checkout-input" placeholder="NY">
                                    </div>
                                    <div class="form-field">
                                        <label class="field-label">Zip Code <span class="required">*</span></label>
                                        <input type="text" name="billing_zip" class="checkout-input" placeholder="10001">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        function toggleBilling() {
                            const isSame = document.getElementById('billing_same').checked;
                            const billingSection = document.getElementById('billing-address-section');
                            const billingInputs = billingSection.querySelectorAll('input');
                            
                            if (isSame) {
                                billingSection.style.display = 'none';
                                billingInputs.forEach(input => input.removeAttribute('required'));
                            } else {
                                billingSection.style.display = 'block';
                                billingInputs.forEach(input => input.setAttribute('required', 'required'));
                            }
                        }
                    </script>

                    <!-- Shipping Options Card -->
                    <div class="checkout-card">
                        <div class="card-header">
                            <span class="card-icon">🚚</span>
                            <h3>Shipping Method <span class="required">*</span></h3>
                        </div>
                        <small class="error-message shipping-error-msg" id="shippingError"></small>
                        <div class="card-body">
                            <div class="shipping-options">
                                <?php foreach ($data['shippingOptions'] as $key => $option): ?>
                                    <?php
                                    // Determine if this option should be disabled
                                    $isDisabled = false;
                                    if ($data['subtotal'] < 300) {
                                        if (in_array($key, ['whiteglove', 'freight'])) $isDisabled = true;
                                    } else {
                                        if (in_array($key, ['standard', 'express'])) $isDisabled = true;
                                    }
                                    ?>
                                    <label class="shipping-option <?php echo $key === $data['selectedShipping'] ? 'selected' : ''; ?> <?php echo $isDisabled ? 'disabled-option' : ''; ?>" for="shipping_<?php echo $key; ?>">
                                        <input 
                                            type="radio" 
                                            name="shipping_method" 
                                            id="shipping_<?php echo $key; ?>" 
                                            value="<?php echo $key; ?>" 
                                            data-cost="<?php echo $option['cost']; ?>"
                                            <?php echo $key === $data['selectedShipping'] ? 'checked' : ''; ?>
                                            <?php echo $isDisabled ? 'disabled' : ''; ?>
                                            onchange="updateShippingCost()"
                                            autocomplete="off"
                                            required
                                        >
                                        <div class="shipping-option-content">
                                            <div class="shipping-option-header">
                                                <span class="shipping-icon"><?php echo $option['icon']; ?></span>
                                                <div class="shipping-option-details">
                                                    <h4><?php echo $option['name']; ?></h4>
                                                    <p><?php echo $option['description']; ?></p>
                                                    <?php if (isset($option['calculation'])): ?>
                                                        <small class="shipping-calc"><?php echo $option['calculation']; ?></small>
                                                    <?php endif; ?>
                                                    <?php if (isset($option['label'])): ?>
                                                        <small class="shipping-info-label" style="display: block; font-size: 0.75rem; color: #6b7280; margin-top: 4px;"><?php echo $option['label']; ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="shipping-option-price">
                                                <?php echo formatPrice($option['cost']); ?>
                                            </div>
                                        </div>
                                        <span class="radio-checkmark"></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information Card -->
                    <div class="checkout-card">
                        <div class="card-header">
                            <span class="card-icon">💳</span>
                            <h3>Payment Information</h3>
                            <div class="payment-badges">
                                <span class="payment-badge">VISA</span>
                                <span class="payment-badge">MC</span>
                                <span class="payment-badge">AMEX</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-field full-width">
                                <label class="field-label">Card Number <span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <span class="input-icon">💳</span>
                                    <input type="text" name="card_number" id="cardNumber" class="checkout-input with-icon" placeholder="1234 5678 9012 3456" maxlength="19" required>
                                </div>
                                <small class="error-message input-error-msg" id="cardError"></small>
                            </div>
                            <div class="form-row">
                                <div class="form-field">
                                    <label class="field-label">Expiry Date <span class="required">*</span></label>
                                    <input type="text" name="expiry" id="expiry" class="checkout-input" placeholder="MM/YY" maxlength="5" required>
                                    <small class="error-message input-error-msg" id="expiryError"></small>
                                </div>
                                <div class="form-field">
                                    <label class="field-label">CVV <span class="required">*</span></label>
                                    <div class="input-with-icon">
                                        <input type="text" name="cvv" id="cvv" class="checkout-input" placeholder="•••" maxlength="3" required>
                                        <span class="cvv-help" title="3 digits on the back of your card">?</span>
                                    </div>
                                    <small class="error-message input-error-msg" id="cvvError"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="checkout-terms">
                        <label class="terms-checkbox">
                            <input type="checkbox" name="agree_terms" required>
                            <span class="checkmark"></span>
                            <span class="terms-text">
                                I agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <!-- Action Buttons -->
                    <div class="checkout-actions">
                        <button type="submit" class="btn-checkout-submit">
                            <span class="btn-icon">🔒</span>
                            Complete Order - <span id="btn-total"><?php echo formatPrice($data['total']); ?></span>
                        </button>
                        <a href="cart" class="btn-back-cart">
                            <span>←</span> Back to Cart
                        </a>
                    </div>
                </form>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="checkout-summary-wrapper">
                <div class="checkout-summary">
                    <div class="summary-header">
                        <h3>
                            <span class="summary-icon">📋</span>
                            Order Summary
                        </h3>
                        <span class="item-count"><?php echo count($data['cartItemsWithDetails']); ?> item<?php echo count($data['cartItemsWithDetails']) > 1 ? 's' : ''; ?></span>
                    </div>

                    <!-- Items List -->
                    <div class="summary-items">
                        <?php foreach ($data['cartItemsWithDetails'] as $index => $item): ?>
                            <div class="summary-item" data-product-id="<?php echo $item['product']['id']; ?>" data-product-price="<?php echo $item['product']['price']; ?>" data-stock="<?php echo $item['product']['stock']; ?>">
                                <div class="item-emoji-wrapper">
                                    <?php if (!empty($item['product']['image'])): ?>
                                        <img src="<?php echo strpos($item['product']['image'], 'http') === 0 ? $item['product']['image'] : URL_ROOT . '/' . ltrim($item['product']['image'], '/'); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" class="item-img">
                                    <?php else: ?>
                                        <?php echo $item['product']['emoji']; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="item-details">
                                    <span class="item-name"><?php echo htmlspecialchars($item['product']['name']); ?></span>
                                    <div class="item-qty-controls">
                                        <button type="button" class="qty-btn-small" onclick="decrementCheckoutQty(this)">−</button>
                                        <input type="number" class="qty-input-small" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['product']['stock']; ?>" readonly>
                                        <button type="button" class="qty-btn-small" onclick="incrementCheckoutQty(this)">+</button>
                                    </div>
                                </div>
                                <div class="item-price"><?php echo formatPrice($item['itemTotal']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Promo Code -->
                    <div class="promo-section">
                        <div class="promo-input-wrapper">
                            <input type="text" placeholder="Promo code" class="promo-input" id="promo-code-input" value="<?php echo $data['appliedPromo'] ? htmlspecialchars($data['appliedPromo']['code']) : ''; ?>" <?php echo $data['appliedPromo'] ? 'disabled' : ''; ?>>
                            <button type="button" class="promo-btn" id="promo-btn" onclick="<?php echo $data['appliedPromo'] ? 'removePromoCode()' : 'applyPromoCode()'; ?>"><?php echo $data['appliedPromo'] ? 'Remove' : 'Apply'; ?></button>
                        </div>
                        <div id="promo-message" class="promo-msg <?php echo $data['appliedPromo'] ? 'promo-applied' : ''; ?>">
                            <?php echo $data['appliedPromo'] ? 'Promo applied: ' . ($data['appliedPromo']['type'] == 'percent' ? $data['appliedPromo']['value'] . '%' : '$' . $data['appliedPromo']['value']) . ' Off' : ''; ?>
                        </div>
                    </div>

                    <!-- Discount Status Info -->
                    <div id="checkout-discount-info" class="promo-section" style="margin-top: 10px; border-top: 1px solid #eee; padding-top: 15px; display: none;">
                        <div id="discount-tier-msg" class="secure-info-box" style="padding: 12px; margin-top: 0; font-size: 13px;">
                            <!-- Dynamically populated by JS -->
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="summary-totals">
                        <div class="total-row">
                            <span>Subtotal</span>
                            <span id="checkout-subtotal" data-value="<?php echo $data['subtotal']; ?>"><?php echo formatPrice($data['subtotal']); ?></span>
                        </div>
                        <div class="total-row discount-row">
                            <span>Bulk Discount</span>
                            <span id="checkout-discount" data-value="<?php echo $data['discount']; ?>">-<?php echo formatPrice($data['discount']); ?></span>
                        </div>
                        <div class="total-row promo-row" style="display: <?php echo $data['appliedPromo'] ? 'flex' : 'none'; ?>;" id="promo-row">
                            <span>Promo Discount</span>
                            <span id="checkout-promo" data-value="<?php echo $data['promoDiscount']; ?>">-<?php echo formatPrice($data['promoDiscount']); ?></span>
                        </div>
                        
                        <hr class="checkout-divider">
                        
                        <div class="total-row">
                            <span class="font-bold">Discounted Subtotal</span>
                            <span id="checkout-discounted-subtotal" class="font-bold"><?php echo formatPrice($data['subtotal'] - $data['discount'] - $data['promoDiscount']); ?></span>
                        </div>

                        <div class="total-row shipping-row">
                            <span>
                                Shipping
                                <small id="shipping-method-name">(<?php echo $data['shippingOptions'][$data['selectedShipping']]['name']; ?>)</small>
                            </span>
                            <span id="checkout-shipping" data-value="<?php echo $data['shippingCost']; ?>">
                                <?php echo formatPrice($data['shippingCost']); ?>
                            </span>
                        </div>
                        <div class="total-row">
                            <span>
                                Tax (18%)
                                <small>on Discounted Subtotal + Shipping</small>
                            </span>
                            <span id="checkout-tax" data-value="<?php echo $data['tax']; ?>">
                                <?php echo formatPrice($data['tax']); ?>
                            </span>
                        </div>

                        <hr class="checkout-divider">

                        <div class="total-row final-total">
                            <span>Total</span>
                            <span id="checkout-total"><?php echo formatPrice($data['total']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
