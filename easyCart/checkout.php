<?php
session_start();

// Include data and auth
require_once 'includes/data.php';
require_once 'includes/auth.php';

$pageTitle = 'Checkout';

// Require login
requireLogin();

// Check if coming from Buy Now button
$directProduct = null;
$directQuantity = 1;
$isBuyNow = false;

// Handle both GET (initial load) and POST (form submission)
if (isset($_REQUEST['product_id']) && isset($_REQUEST['qty'])) {
    $directProduct = getProductById(intval($_REQUEST['product_id']));
    $directQuantity = intval($_REQUEST['qty']);
    $isBuyNow = true;
    
    if (!$directProduct) {
        header('Location: products.php');
        exit;
    }
}

// Get cart items from session
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// If coming from Buy Now, ONLY use the direct product (ignore cart)
if ($isBuyNow && $directProduct) {
    $cartItems = [
        $directProduct['id'] => [
            'product_id' => $directProduct['id'],
            'quantity' => $directQuantity
        ]
    ];
}

// If cart is empty and no direct product, redirect to cart page
if (count($cartItems) === 0) {
    header('Location: cart.php');
    exit;
}

// Calculate totals
$subtotal = 0;
$cartItemsWithDetails = [];

foreach ($cartItems as $productId => $cartItem) {
    $product = getProductById($productId);
    if ($product) {
        $itemTotal = $product['price'] * $cartItem['quantity'];
        $subtotal += $itemTotal;
        
        $cartItemsWithDetails[] = [
            'product' => $product,
            'quantity' => $cartItem['quantity'],
            'itemTotal' => $itemTotal
        ];
    }
}

$tax = $subtotal * 0.10;
$shipping = $subtotal > 50 ? 0 : 9.99;
$total = $subtotal + $tax + $shipping;

// Handle checkout completion
$checkoutMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'complete_order') {
    // Validate form data
    $required_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state', 'zip', 'card_number'];
    $all_filled = true;
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $all_filled = false;
            break;
        }
    }
    
    if ($all_filled) {
        $_SESSION['last_order'] = [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'status' => 'Processing',
            'order_number' => 'ORD' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
            'customer' => [
                'first_name' => htmlspecialchars($_POST['first_name']),
                'last_name' => htmlspecialchars($_POST['last_name']),
                'email' => htmlspecialchars($_POST['email']),
                'phone' => htmlspecialchars($_POST['phone']),
                'address' => htmlspecialchars($_POST['address']),
                'city' => htmlspecialchars($_POST['city']),
                'state' => htmlspecialchars($_POST['state']),
                'zip' => htmlspecialchars($_POST['zip'])
            ],
            'items' => $cartItemsWithDetails,
            'date' => date('Y-m-d H:i:s')
        ];
        
        // Clear cart ONLY if checking out from cart (not Buy Now)
        if (!$isBuyNow) {
            $_SESSION['cart'] = [];
            if (isLoggedIn() && isset($_SESSION['user_email'])) {
                saveUserCart($_SESSION['user_email'], $_SESSION['cart']);
            }
        }
        
        header('Location: order-confirmation.php');
        exit;
    } else {
        $checkoutMessage = 'Please fill in all required fields.';
    }
}
?>
<?php include 'includes/header.php'; ?>
    <script src="js/validation.js"></script>
    <script src="js/checkout.js"></script>

    <!-- Modern Checkout Page -->
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

            <?php if ($checkoutMessage): ?>
                <div class="checkout-alert checkout-alert-error">
                    <span class="alert-icon">⚠️</span>
                    <?php echo htmlspecialchars($checkoutMessage); ?>
                </div>
            <?php endif; ?>

            <div class="checkout-grid">
                <!-- Checkout Form -->
                <div class="checkout-form-wrapper">
                    <form method="POST" action="" id="checkoutForm" onsubmit="return validateCheckoutForm()">
                        <input type="hidden" name="action" value="complete_order">
                        <?php if ($isBuyNow && $directProduct): ?>
                            <input type="hidden" name="product_id" value="<?php echo $directProduct['id']; ?>">
                            <input type="hidden" name="qty" value="<?php echo $directQuantity; ?>">
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
                                        <input type="text" name="first_name" class="checkout-input" placeholder="John" required>
                                    </div>
                                    <div class="form-field">
                                        <label class="field-label">Last Name <span class="required">*</span></label>
                                        <input type="text" name="last_name" class="checkout-input" placeholder="Doe" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-field">
                                        <label class="field-label">Email Address <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <span class="input-icon">📧</span>
                                            <input type="email" name="email" class="checkout-input with-icon" placeholder="john@example.com" required>
                                        </div>
                                    </div>
                                    <div class="form-field">
                                        <label class="field-label">Phone Number <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <span class="input-icon">📱</span>
                                            <input type="tel" name="phone" class="checkout-input with-icon" placeholder="+1 (555) 000-0000" required>
                                        </div>
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
                                        <input type="text" name="address" class="checkout-input with-icon" placeholder="123 Main Street, Apt 4B" required>
                                    </div>
                                </div>
                                <div class="form-row three-cols">
                                    <div class="form-field">
                                        <label class="field-label">City <span class="required">*</span></label>
                                        <input type="text" name="city" class="checkout-input" placeholder="New York" required>
                                    </div>
                                    <div class="form-field">
                                        <label class="field-label">State <span class="required">*</span></label>
                                        <input type="text" name="state" class="checkout-input" placeholder="NY" required>
                                    </div>
                                    <div class="form-field">
                                        <label class="field-label">Zip Code <span class="required">*</span></label>
                                        <input type="text" name="zip" class="checkout-input" placeholder="10001" required>
                                    </div>
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
                                        <input type="text" name="card_number" class="checkout-input with-icon" placeholder="1234 5678 9012 3456" maxlength="19" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-field">
                                        <label class="field-label">Expiry Date <span class="required">*</span></label>
                                        <input type="text" name="expiry" class="checkout-input" placeholder="MM/YY" maxlength="5" required>
                                    </div>
                                    <div class="form-field">
                                        <label class="field-label">CVV <span class="required">*</span></label>
                                        <div class="input-with-icon">
                                            <input type="text" name="cvv" class="checkout-input" placeholder="•••" maxlength="3" required>
                                            <span class="cvv-help" title="3 digits on the back of your card">?</span>
                                        </div>
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
                                Complete Order - <span id="btn-total"><?php echo formatPrice($total); ?></span>
                            </button>
                            <a href="cart.php" class="btn-back-cart">
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
                            <span class="item-count"><?php echo count($cartItemsWithDetails); ?> item<?php echo count($cartItemsWithDetails) > 1 ? 's' : ''; ?></span>
                        </div>

                        <!-- Items List -->
                        <div class="summary-items">
                            <?php foreach ($cartItemsWithDetails as $index => $item): ?>
                                <div class="summary-item" data-product-id="<?php echo $item['product']['id']; ?>" data-product-price="<?php echo $item['product']['price']; ?>" data-stock="<?php echo $item['product']['stock']; ?>">
                                    <div class="item-emoji"><?php echo $item['product']['emoji']; ?></div>
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
                                <input type="text" placeholder="Promo code" class="promo-input">
                                <button type="button" class="promo-btn">Apply</button>
                            </div>
                        </div>

                        <!-- Totals -->
                        <div class="summary-totals">
                            <div class="total-row">
                                <span>Subtotal</span>
                                <span id="checkout-subtotal"><?php echo formatPrice($subtotal); ?></span>
                            </div>
                            <div class="total-row">
                                <span>Tax (10%)</span>
                                <span id="checkout-tax"><?php echo formatPrice($tax); ?></span>
                            </div>
                            <div class="total-row shipping-row">
                                <span>Shipping</span>
                                <span id="checkout-shipping" class="<?php echo $shipping === 0 ? 'free-shipping' : ''; ?>">
                                    <?php echo $shipping === 0 ? '✓ Free' : formatPrice($shipping); ?>
                                </span>
                            </div>
                            <div class="total-row grand-total">
                                <span>Total</span>
                                <span id="checkout-total"><?php echo formatPrice($total); ?></span>
                            </div>
                        </div>

                        <!-- Trust Badges -->
                        <div class="trust-badges">
                            <div class="trust-badge">
                                <span class="badge-icon">🔒</span>
                                <span>SSL Secured</span>
                            </div>
                            <div class="trust-badge">
                                <span class="badge-icon">✓</span>
                                <span>Safe Payment</span>
                            </div>
                            <div class="trust-badge">
                                <span class="badge-icon">↩️</span>
                                <span>Easy Returns</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
