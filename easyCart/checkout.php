<?php
session_start();

// Include data and auth
require_once 'data.php';
require_once 'auth.php';

$pageTitle = 'Checkout';

// Require login
requireLogin();

// Check if coming from Buy Now button
$directProduct = null;
$directQuantity = 1;

if (isset($_GET['product_id']) && isset($_GET['qty'])) {
    $directProduct = getProductById(intval($_GET['product_id']));
    $directQuantity = intval($_GET['qty']);
    
    if (!$directProduct) {
        header('Location: products.php');
        exit;
    }
}

// Get cart items from session
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// If coming from Buy Now and no cart items, use the direct product
if ($directProduct && count($cartItems) === 0) {
    $cartItems = [
        $directProduct['id'] => [
            'product_id' => $directProduct['id'],
            'quantity' => $directQuantity
        ]
    ];
}

// If cart is empty and no direct product, redirect to cart page
if (count($cartItems) === 0 && !$directProduct) {
    header('Location: cart.php');
    exit;
}

// Calculate totals (same logic as cart page)
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

// Handle checkout
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
        // In a real application, you would:
        // 1. Process payment with payment gateway
        // 2. Save order to database
        // 3. Send confirmation email
        // For now, we'll just clear the cart and show success
        
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
        
        // Clear cart
        $_SESSION['cart'] = [];
        
        header('Location: order-confirmation.php');
        exit;
    } else {
        $checkoutMessage = 'Please fill in all required fields.';
    }
}
?>
<?php include 'header.php'; ?>

    <!-- Checkout Page -->
    <section class="container" style="padding: 40px 0;">
        <h1 class="section-title">Checkout</h1>

        <?php if ($checkoutMessage): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #f5c6cb;">
                <?php echo htmlspecialchars($checkoutMessage); ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Checkout Form -->
            <div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="complete_order">

                    <!-- Personal Information -->
                    <div style="background: #fff; padding: 25px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <h3 style="margin-top: 0; margin-bottom: 20px;">Personal Information</h3>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">First Name *</label>
                                <input type="text" name="first_name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Last Name *</label>
                                <input type="text" name="last_name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Email Address *</label>
                                <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Phone Number *</label>
                                <input type="tel" name="phone" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div style="background: #fff; padding: 25px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <h3 style="margin-top: 0; margin-bottom: 20px;">Shipping Address</h3>

                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Address *</label>
                            <input type="text" name="address" placeholder="Street address" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>

                        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">City *</label>
                                <input type="text" name="city" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">State *</label>
                                <input type="text" name="state" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Zip Code *</label>
                                <input type="text" name="zip" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div style="background: #fff; padding: 25px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <h3 style="margin-top: 0; margin-bottom: 20px;">Payment Information</h3>

                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Card Number *</label>
                            <input type="text" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Expiry Date *</label>
                                <input type="text" name="expiry" placeholder="MM/YY" maxlength="5" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">CVV *</label>
                                <input type="text" name="cvv" placeholder="123" maxlength="3" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Conditions -->
                    <div style="background: #f0f4f8; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="agree_terms" required style="margin-right: 10px; cursor: pointer;">
                            <span>I agree to the <a href="#" style="color: #2563eb;">Terms and Conditions</a> and <a href="#" style="color: #2563eb;">Privacy Policy</a></span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 16px; cursor: pointer;">
                        Complete Order - <?php echo formatPrice($total); ?>
                    </button>

                    <a href="cart.php" style="display: block; text-align: center; padding: 12px; margin-top: 10px; color: #2563eb; text-decoration: none; border: 1px solid #2563eb; border-radius: 4px;">
                        Back to Cart
                    </a>
                </form>
            </div>

            <!-- Order Summary -->
            <div>
                <div style="background: #f8f9fa; padding: 25px; border-radius: 8px; border: 2px solid #dee2e6; position: sticky; top: 100px;">
                    <h3 style="margin-top: 0; margin-bottom: 20px;">Order Summary</h3>

                    <!-- Items List -->
                    <div style="margin-bottom: 20px; max-height: 300px; overflow-y: auto;">
                        <?php foreach ($cartItemsWithDetails as $item): ?>
                            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #dee2e6; align-items: center;">
                                <div style="flex: 1;">
                                    <div style="font-size: 24px; display: inline-block; margin-right: 10px;">
                                        <?php echo $item['product']['emoji']; ?>
                                    </div>
                                    <span style="font-size: 14px;"><?php echo htmlspecialchars($item['product']['name']); ?></span>
                                    <div style="font-size: 12px; color: #666; margin-top: 3px;">
                                        Qty: <?php echo $item['quantity']; ?>
                                    </div>
                                </div>
                                <div style="text-align: right; font-weight: bold;">
                                    <?php echo formatPrice($item['itemTotal']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Totals -->
                    <div style="padding-top: 20px; border-top: 2px solid #dee2e6;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Subtotal</span>
                            <span><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Tax (10%)</span>
                            <span><?php echo formatPrice($tax); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                            <span>Shipping</span>
                            <span><?php echo $shipping === 0 ? 'Free' : formatPrice($shipping); ?></span>
                        </div>

                        <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; padding: 15px 0; border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                            <span>Total</span>
                            <span style="color: #d32f2f;"><?php echo formatPrice($total); ?></span>
                        </div>
                    </div>

                    <!-- Security Info -->
                    <div style="margin-top: 20px; padding: 15px; background: #d4edda; border-radius: 4px; text-align: center;">
                        <p style="margin: 0; font-size: 12px; color: #155724;">
                            🔒 Secure SSL Encrypted Payment
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>
