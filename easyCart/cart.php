<?php
session_start();
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
// Include data and auth
// Load Application Bootstrap
require_once 'app/bootstrap.php';
// Disable error display to prevent JSON corruption
ini_set('display_errors', 0);
ini_set('log_errors', 1);

$pageTitle = 'Shopping Cart';
// Load cart from file/db on page load (for logged-in users OR guests)
// Note: loadUserCart now handles null $userId by using guest session ID
if (!isset($_SESSION['cart'])) {
    $userId = isLoggedIn() ? getCurrentUser()['id'] : null;
    $dbCart = loadUserCart($userId);
    
    // If DB has items, use them. 
    // If DB is empty but we just started, initialized empty array
    $_SESSION['cart'] = $dbCart;
}

// Handle AJAX cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : null;
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
              (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
              ($action === 'add' && isset($_POST['product_id']));
    
    // Determine User ID for saving cart
    $userId = null;
    if (isLoggedIn()) {
        $currentUser = getCurrentUser();
        $userId = $currentUser['id'];
    }
    
    // Handle add action (AJAX)
    if ($action === 'add' && isset($_POST['product_id'])) {
        $productId = intval($_POST['product_id']);
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        $product = getProductById($productId);
        
        if (!$product || $product['stock'] <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Product not available']);
            exit;
        }
        
        $alreadyInCart = isset($_SESSION['cart'][$productId]);
        
        // Check if product already in cart - don't add again
        if ($alreadyInCart) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'alreadyInCart' => true,
                'message' => 'Product already in cart',
                'productName' => $product['name']
            ]);
            exit;
        }
        
        // Add new product to cart
        $_SESSION['cart'][$productId] = [
            'product_id' => $productId,
            'quantity' => min($quantity, $product['stock'])
        ];
        
        // Save cart (DB sync handles guest/user distinction)
        saveUserCart($userId, $_SESSION['cart']);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Product added to cart',
            'productName' => $product['name'],
            'cartCount' => count($_SESSION['cart'])
        ]);
        exit;
    }
    
    if ($action === 'remove' && isset($_POST['product_id'])) {
        $productId = intval($_POST['product_id']);
        unset($_SESSION['cart'][$productId]);
        // Save cart
        saveUserCart($userId, $_SESSION['cart']);
        
        if ($isAjax) {
            $summary = calculateCartSummary();
            header('Content-Type: application/json');
            echo json_encode(array_merge(['success' => true, 'cartCount' => count($_SESSION['cart'])], $summary));
            exit;
        }
    } elseif ($action === 'update' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $productId = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            // Check stock limit
            $product = getProductById($productId);
            if ($product && $quantity <= $product['stock']) {
                if (!isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId] = ['product_id' => $productId];
                }
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            }
        }
        // Save cart
        // Save cart
        saveUserCart($userId, $_SESSION['cart']);
        
        if ($isAjax) {
            $summary = calculateCartSummary();
            header('Content-Type: application/json');
            echo json_encode(array_merge(['success' => true, 'cartCount' => count($_SESSION['cart'])], $summary));
            exit;
        }
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
        // Save cart
        // Save cart
        saveUserCart($userId, $_SESSION['cart']);
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'cartCount' => 0]);
            exit;
        }
    } elseif ($action === 'get_count') {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'cartCount' => count($_SESSION['cart'])]);
        exit;
    }
}

// Function to calculate cart summary for dynamic updates
function calculateCartSummary() {
    $subtotal = 0;
    $discount = 0;
    $items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    foreach ($items as $pid => $item) {
        $product = getProductById($pid);
        if ($product) {
            $itemPrice = $product['price'] * $item['quantity'];
            $subtotal += $itemPrice;
            $discount += calculateBulkDiscount($product['price'], $item['quantity']);
        }
    }
    
    $shipping = 0; // Calculated at checkout
    $taxableAmount = 0;
    $tax = 0; // Calculated at checkout
    $total = $subtotal - $discount;
    
    return [
        'subtotal' => $subtotal,
        'discount' => $discount,
        'tax' => $tax,
        'shipping' => $shipping,
        'total' => $total,
        'formattedSubtotal' => formatPrice($subtotal),
        'formattedDiscount' => '-' . formatPrice($discount),
        'formattedTax' => formatPrice($tax),
        'formattedShipping' => $shipping == 0 ? 'Free' : formatPrice($shipping),
        'formattedTotal' => formatPrice($total)
    ];
}

// Get cart items
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Calculate totals
$subtotal = 0;
$discount = 0;
$cartItemsWithDetails = [];

foreach ($cartItems as $productId => $cartItem) {
    $product = getProductById($productId);
    if ($product) {
        $itemTotal = $product['price'] * $cartItem['quantity'];
        $subtotal += $itemTotal;
        $discount += calculateBulkDiscount($product['price'], $cartItem['quantity']);
        
        $cartItemsWithDetails[] = [
            'product' => $product,
            'quantity' => $cartItem['quantity'],
            'itemTotal' => $itemTotal
        ];
    }
}

$shipping = 0; // Calculated at checkout
$tax = 0; // Calculated at checkout
$total = $subtotal - $discount;
?>
<?php include TEMPLATES_PATH . '/header.php'; ?>

    <!-- Shopping Cart Page -->
    <section class="container cart-section">
        <h1 class="section-title">Shopping Cart</h1>

        <!-- Login Prompt for Non-Logged-In Users -->
        <?php if (!isLoggedIn() && count($cartItemsWithDetails) > 0): ?>
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

        <?php if (count($cartItemsWithDetails) > 0): ?>
            <div class="cart-grid">
                <!-- Cart Items -->
                <div>
                    <div class="cart-items-container">
                        <!-- Cart Header -->
                        <div class="cart-header">
                            <h3 id="cart-page-count">
                                <?php echo count($cartItemsWithDetails); ?> 
                                Item<?php echo count($cartItemsWithDetails) !== 1 ? 's' : ''; ?> in Cart
                            </h3>
                        </div>

                        <!-- Cart Items List -->
                        <?php foreach ($cartItemsWithDetails as $index => $item): ?>
                            <div class="cart-item" data-product-id="<?php echo $item['product']['id']; ?>" data-product-price="<?php echo $item['product']['price']; ?>">
                                <!-- Product Image & Name -->
                                <div class="cart-product-info">
                                    <!-- Product Image -->
                                    <div class="cart-img-wrapper">
                                        <?php if (!empty($item['product']['image'])): ?>
                                            <img src="<?php echo $item['product']['image']; ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" class="cart-img">
                                        <?php else: ?>
                                            <?php echo $item['product']['emoji']; ?>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Product Details -->
                                    <div class="cart-details">
                                        <h4 class="cart-product-title">
                                            <a href="product-detail?id=<?php echo $item['product']['id']; ?>" class="cart-product-link">
                                                <?php echo htmlspecialchars($item['product']['name']); ?>
                                            </a>
                                        </h4>
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
                            <form method="POST" class="inline-form">
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
                                <span class="summary-val" id="summary-subtotal"><?php echo formatPrice($subtotal); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Bulk Discount</span>
                                <span class="summary-val discount" id="summary-discount">-<?php echo formatPrice($discount); ?></span>
                            </div>

                        </div>

                        <div class="summary-total-row">
                            <span>Total Amount</span>
                            <span class="summary-total-val" id="summary-total">
                                <?php echo formatPrice($total); ?>
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

    <script src="public/js/cart.js"></script>
    <?php include TEMPLATES_PATH . '/footer.php'; ?>
