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

// ============================================
// SHIPPING OPTIONS & COST CALCULATION
// ============================================

/**
 * Calculate shipping cost based on selected method and subtotal
 * 
 * @param string $method Shipping method (standard, express, whiteglove, freight)
 * @param float $subtotal Order subtotal
 * @return float Calculated shipping cost
 */
function calculateShippingCost($method, $subtotal) {
    switch ($method) {
        case 'standard':
            // Standard Shipping: Flat $40
            return 40.00;
            
        case 'express':
            // Express Shipping: Flat $80 OR 10% of subtotal (whichever is lower)
            return min(80.00, $subtotal * 0.10);
            
        case 'whiteglove':
            // White Glove Delivery: Flat $150 OR 5% of subtotal (whichever is lower)
            return min(150.00, $subtotal * 0.05);

        case 'freight':
            // Freight Shipping: 3% of subtotal, Minimum $200
            return max(200.00, $subtotal * 0.03);
        
        default:
            return 40.00; // Default to standard shipping
    }
}

// Calculate subtotal
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

// Get selected shipping method (default to standard)
$selectedShipping = isset($_POST['shipping_method']) ? $_POST['shipping_method'] : 
                   (isset($_GET['shipping_method']) ? $_GET['shipping_method'] : 'standard');

// Calculate shipping cost
$shippingCost = calculateShippingCost($selectedShipping, $subtotal);

// Calculate tax (18% on Subtotal + Shipping)
$taxableAmount = $subtotal + $shippingCost;
$tax = $taxableAmount * 0.18;

// Calculate final total
$total = $subtotal + $shippingCost + $tax;

// Define shipping options for display
$shippingOptions = [
    'standard' => [
        'name' => 'Standard Shipping',
        'description' => 'Delivery in 5-7 business days',
        'cost' => 40.00,
        'icon' => '📦'
    ],
    'express' => [
        'name' => 'Express Shipping',
        'description' => 'Delivery in 2-3 business days',
        'cost' => min(80.00, $subtotal * 0.10),
        'calculation' => 'Flat $80 OR 10% of subtotal (whichever is lower)',
        'icon' => '⚡'
    ],
    'whiteglove' => [
        'name' => 'White Glove Delivery',
        'description' => 'Premium delivery with installation',
        'cost' => min(150.00, $subtotal * 0.05),
        'calculation' => 'Flat $150 OR 5% of subtotal (whichever is lower)',
        'icon' => '🎩'
    ],
    'freight' => [
        'name' => 'Freight Shipping',
        'description' => 'For large or bulk orders',
        'cost' => max(200.00, $subtotal * 0.03),
        'calculation' => '3% of subtotal, Minimum $200',
        'icon' => '🚛'
    ]
];

// Handle checkout completion
$checkoutMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'complete_order') {
    // Validate form data
    $required_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state', 'zip', 'card_number', 'shipping_method'];
    $all_filled = true;
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $all_filled = false;
            break;
        }
    }
    
    if ($all_filled) {
        // Recalculate with submitted shipping method
        $finalShippingMethod = $_POST['shipping_method'];
        $finalShippingCost = calculateShippingCost($finalShippingMethod, $subtotal);
        $finalTaxableAmount = $subtotal + $finalShippingCost;
        $finalTax = $finalTaxableAmount * 0.18;
        $finalTotal = $subtotal + $finalShippingCost + $finalTax;
        
        $_SESSION['last_order'] = [
            'subtotal' => $subtotal,
            'shipping_method' => $finalShippingMethod,
            'shipping_method_name' => $shippingOptions[$finalShippingMethod]['name'],
            'shipping_cost' => $finalShippingCost,
            'tax' => $finalTax,
            'tax_rate' => 18,
            'total' => $finalTotal,
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

    <style>
        /* Shipping Options Card Styling */
        .shipping-options {
            display: grid;
            gap: 15px;
            margin-top: 10px;
        }

        .shipping-option {
            position: relative;
            display: block;
            background: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .shipping-option:hover {
            border-color: #2563eb;
            background: #f8faff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        }

        .shipping-option.selected {
            border-color: #2563eb;
            background: #f0f7ff;
            box-shadow: 0 0 0 1px #2563eb;
        }

        .shipping-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .shipping-option-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .shipping-option-header {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .shipping-icon {
            font-size: 32px;
            background: #fff;
            width: 54px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .shipping-option-details h4 {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 4px 0;
        }

        .shipping-option-details p {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
        }

        .shipping-calc {
            display: block;
            font-size: 11px;
            color: #3b82f6;
            margin-top: 4px;
            font-weight: 600;
        }

        .shipping-option-price {
            font-size: 18px;
            font-weight: 800;
            color: #2563eb;
            white-space: nowrap;
        }

        /* Custom Radio Checkmark */
        .radio-checkmark {
            position: absolute;
            top: 20px;
            right: 20px;
            height: 20px;
            width: 20px;
            background-color: #fff;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            transition: all 0.2s ease;
            display: none; /* Hide if we want simple design, but let's keep it optional */
        }

        .shipping-option.selected .radio-checkmark {
            border-color: #2563eb;
            border-width: 6px;
        }

        /* Order Summary Enhancements */
        #shipping-method-name {
            display: block;
            color: #2563eb;
            font-weight: 600;
            font-size: 11px;
            margin-top: 2px;
        }

        .total-row small {
            display: block;
            font-size: 10px;
            color: #9ca3af;
            font-weight: normal;
        }

        .grand-total {
            background: #f0f7ff;
            margin: 0 -25px;
            padding: 20px 25px !important;
            border-top: 2px dashed #2563eb !important;
        }
    </style>

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

                        <!-- Shipping Options Card -->
                        <div class="checkout-card">
                            <div class="card-header">
                                <span class="card-icon">🚚</span>
                                <h3>Shipping Method <span class="required">*</span></h3>
                            </div>
                            <div class="card-body">
                                <div class="shipping-options">
                                    <?php foreach ($shippingOptions as $key => $option): ?>
                                        <label class="shipping-option <?php echo $key === $selectedShipping ? 'selected' : ''; ?>" for="shipping_<?php echo $key; ?>">
                                            <input 
                                                type="radio" 
                                                name="shipping_method" 
                                                id="shipping_<?php echo $key; ?>" 
                                                value="<?php echo $key; ?>" 
                                                data-cost="<?php echo $option['cost']; ?>"
                                                <?php echo $key === $selectedShipping ? 'checked' : ''; ?>
                                                onchange="updateShippingCost()"
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
                                <span id="checkout-subtotal" data-value="<?php echo $subtotal; ?>"><?php echo formatPrice($subtotal); ?></span>
                            </div>
                            <div class="total-row shipping-row">
                                <span>
                                    Shipping
                                    <small id="shipping-method-name">(<?php echo $shippingOptions[$selectedShipping]['name']; ?>)</small>
                                </span>
                                <span id="checkout-shipping" data-value="<?php echo $shippingCost; ?>">
                                    <?php echo formatPrice($shippingCost); ?>
                                </span>
                            </div>
                            <div class="total-row">
                                <span>
                                    Tax (18%)
                                    <small>on Subtotal + Shipping</small>
                                </span>
                                <span id="checkout-tax" data-value="<?php echo $tax; ?>"><?php echo formatPrice($tax); ?></span>
                            </div>
                            <div class="total-row grand-total">
                                <span>Total Payable</span>
                                <span id="checkout-total" data-value="<?php echo $total; ?>"><?php echo formatPrice($total); ?></span>
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
