<?php
session_start();

// Include data and auth
require_once 'includes/data.php';
require_once 'includes/auth.php';

$pageTitle = 'Shopping Cart';

// Load cart from file on page load (for logged-in users)
if (isLoggedIn() && !isset($_SESSION['cart'])) {
    initializeCartFromFile();
}

// Initialize empty cart for non-logged-in users
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle AJAX cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : null;
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
              (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
              ($action === 'add' && isset($_POST['product_id']));
    
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
        
        // Save cart for logged-in users
        if (isLoggedIn() && isset($_SESSION['user_email'])) {
            saveUserCart($_SESSION['user_email'], $_SESSION['cart']);
        }
        
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
        if (isLoggedIn() && isset($_SESSION['user_email'])) {
            saveUserCart($_SESSION['user_email'], $_SESSION['cart']);
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
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            }
        }
        if (isLoggedIn() && isset($_SESSION['user_email'])) {
            saveUserCart($_SESSION['user_email'], $_SESSION['cart']);
        }
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
        if (isLoggedIn() && isset($_SESSION['user_email'])) {
            saveUserCart($_SESSION['user_email'], $_SESSION['cart']);
        }
    } elseif ($action === 'get_count') {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'cartCount' => count($_SESSION['cart'])]);
        exit;
    }
}

// Get cart items
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

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

$tax = $subtotal * 0.18; // 18% tax based on Phase 4
$shipping = $subtotal > 500 ? 0 : 40.00; // Standard shipping $40, free over $500
$total = $subtotal + $tax + $shipping;
?>
<?php include 'includes/header.php'; ?>

    <!-- Shopping Cart Page -->
    <section class="container" style="padding: 40px 0;">
        <h1 class="section-title">Shopping Cart</h1>

        <!-- Login Prompt for Non-Logged-In Users -->
        <?php if (!isLoggedIn() && count($cartItemsWithDetails) > 0): ?>
            <div style="background: linear-gradient(135deg, #e3f2fd 0%, #f0f7ff 100%); border: 2px solid #2563eb; padding: 20px; border-radius: 12px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0 0 8px 0; color: #1f2937; font-size: 18px;">🔒 Secure Checkout Available</h3>
                    <p style="margin: 0; color: #666; font-size: 14px;">Please log in to proceed with checkout and complete your purchase securely.</p>
                </div>
                <div style="display: flex; gap: 10px; flex-shrink: 0;">
                    <a href="signin.php" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.background='linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'; this.style.transform='translateY(0)';">
                        Login
                    </a>
                    <a href="signup.php" style="background: white; color: #2563eb; padding: 12px 24px; border: 2px solid #2563eb; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.background='#e3f2fd';" onmouseout="this.style.background='white';">
                        Sign Up
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (count($cartItemsWithDetails) > 0): ?>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 40px;">
                <!-- Cart Items -->
                <div>
                    <div style="background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <!-- Cart Header -->
                        <div style="background: #f8f9fa; padding: 20px; border-bottom: 1px solid #dee2e6;">
                            <h3 style="margin: 0;">
                                <?php echo count($cartItemsWithDetails); ?> 
                                Item<?php echo count($cartItemsWithDetails) !== 1 ? 's' : ''; ?> in Cart
                            </h3>
                        </div>

                        <!-- Cart Items List -->
                        <?php foreach ($cartItemsWithDetails as $index => $item): ?>
                            <div class="cart-item" style="padding: 20px; border-bottom: 2px solid #f0f0f0; display: flex; gap: 20px; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); transition: all 0.3s ease;" onmouseover="this.style.background='linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%)'; this.style.boxShadow='0 4px 12px rgba(37, 99, 235, 0.08)';" onmouseout="this.style.background='linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%)'; this.style.boxShadow='none';" data-product-id="<?php echo $item['product']['id']; ?>" data-product-price="<?php echo $item['product']['price']; ?>">
                                <!-- Product Image & Name -->
                                <div style="display: flex; gap: 15px; align-items: center; flex: 1;">
                                    <!-- Product Image -->
                                    <div style="font-size: 60px; flex-shrink: 0; background: linear-gradient(135deg, #e3f2fd 0%, #f0f7ff 100%); width: 90px; height: 90px; border-radius: 12px; display: flex; align-items: center; justify-content: center; ">
                                        <?php echo $item['product']['emoji']; ?>
                                    </div>

                                    <!-- Product Details -->
                                    <div style="flex: 1; min-width: 0;">
                                        <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">
                                            <a href="product-detail.php?id=<?php echo $item['product']['id']; ?>" style="color: #2563eb; text-decoration: none; transition: color 0.3s ease;" onmouseover="this.style.color='#1d4ed8';" onmouseout="this.style.color='#2563eb';">
                                                <?php echo htmlspecialchars($item['product']['name']); ?>
                                            </a>
                                        </h4>
                                        <div style="display: flex; gap: 15px; font-size: 14px; color: #666;">
                                            <span style="background: #e3f2fd; color: #2563eb; padding: 4px 10px; border-radius: 6px; font-weight: 500;">
                                                Unit: <?php echo formatPrice($item['product']['price']); ?>
                                            </span>
                                            <span style="background: #f0f0f0; color: #666; padding: 4px 10px; border-radius: 6px; font-weight: 500;">
                                                Stock: <?php echo $item['product']['stock']; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quantity Controls -->
                                <div style="display: flex; gap: 8px; align-items: center; flex-shrink: 0; background: white; padding: 8px; border-radius: 8px; border: 2px solid #e3f2fd;">
                                    <button type="button" onclick="decreaseQuantity(this)" style="width: 36px; height: 36px; border-radius: 6px; border: none; background: #e3f2fd; cursor: pointer; font-weight: bold; color: #2563eb; font-size: 18px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='#2563eb'; this.style.color='white';" onmouseout="this.style.background='#e3f2fd'; this.style.color='#2563eb';">−</button>
                                    <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['product']['stock']; ?>" 
                                           style="width: 45px; padding: 6px; border: none; text-align: center; font-weight: 700; color: #2563eb; font-size: 16px; background: #f8f9fa; border-radius: 4px;" onchange="updateQuantityAndSummary(this)">
                                    <button type="button" onclick="increaseQuantity(this)" style="width: 36px; height: 36px; border-radius: 6px; border: none; background: #e3f2fd; cursor: pointer; font-weight: bold; color: #2563eb; font-size: 18px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='#2563eb'; this.style.color='white';" onmouseout="this.style.background='#e3f2fd'; this.style.color='#2563eb';">+</button>
                                </div>

                                <!-- Total Price -->
                                <div style="display: flex; flex-direction: column; align-items: flex-end; flex-shrink: 0; min-width: 120px;">
                                    <div style="font-size: 12px; color: #999; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Total</div>
                                    <p class="item-total" style="margin: 0; font-weight: 700; font-size: 18px; color: #d32f2f;">
                                        <?php echo formatPrice($item['itemTotal']); ?>
                                    </p>
                                </div>

                                <!-- Remove Button -->
                                <button type="button" onclick="removeCartItem(this)" style="background: linear-gradient(135deg, #ff5a5a 0%, #d32f2f 100%); color: white; border: none; padding: 10px 16px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease; flex-shrink: 0;" onmouseover="this.style.background='linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%)'; this.style.transform='scale(1.05)';" onmouseout="this.style.background='linear-gradient(135deg, #ff5a5a 0%, #d32f2f 100%)'; this.style.transform='scale(1)';">
                                    🗑️ Remove
                                </button>
                            </div>
                        <?php endforeach; ?>

                        <!-- Clear Cart Button -->
                        <div style="padding: 20px; text-align: right;">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="clear">
                                <button type="submit" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.background='linear-gradient(135deg, #5a6268 0%, #495057 100%)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='linear-gradient(135deg, #6c757d 0%, #5a6268 100%)'; this.style.transform='translateY(0)';">🧹Clear Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div>
                    <div style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); padding: 25px; border-radius: 12px;  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);">
                        <h3 style="margin-top: 0; margin-bottom: 20px; color: #1f2937; font-size: 20px; font-weight: 600;">Order Summary</h3>

                        <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 2px solid #e3f2fd;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 15px; color: #666;">
                                <span>Subtotal</span>
                                <span id="summary-subtotal" style="font-weight: 500; color: #1f2937;"><?php echo formatPrice($subtotal); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 15px; color: #666;">
                                <span>Tax (18%)</span>
                                <span id="summary-tax" style="font-weight: 500; color: #1f2937;"><?php echo formatPrice($tax); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0; font-size: 15px; color: #666;">
                                <span>Shipping (Standard)</span>
                                <span id="summary-shipping" style="font-weight: 500; color: #1f2937;"><?php echo $shipping === 0 ? 'Free' : formatPrice($shipping); ?></span>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; padding-top: 15px; font-size: 20px; font-weight: bold; color: #2563eb; border-top: 2px solid #e3f2fd;">
                            <span>Total Amount</span>
                            <span id="summary-total" style="color: #d32f2f;">
                                <?php echo formatPrice($total); ?>
                            </span>
                        </div>

                        <a href="<?php echo isLoggedIn() ? 'checkout.php' : 'signin.php?redirect=cart'; ?>" class="btn btn-primary" style="display: block; text-align: center; padding: 15px; text-decoration: none; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border-radius: 8px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; color: white;" onmouseover="this.style.background='linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 16px rgba(37, 99, 235, 0.3)';" onmouseout="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                            <?php echo isLoggedIn() ? 'Proceed to Checkout' : 'Login to Checkout'; ?>
                        </a>

                        <a href="products.php" style="display: block; text-align: center; padding: 12px; margin-top: 10px; color: #2563eb; text-decoration: none; border: 2px solid #2563eb; border-radius: 8px; font-weight: 500; transition: all 0.3s ease;" onmouseover="this.style.background='#e3f2fd';" onmouseout="this.style.background='transparent';">
                            Continue Shopping
                        </a>
                    </div>

                    <!-- Promo Code -->
                    <div style="background: linear-gradient(135deg, #e3f2fd 0%, #f0f7ff 100%); padding: 18px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #2563eb;">
                        <p style="margin: 0 0 10px 0; font-size: 14px; color: #2563eb; font-weight: 500;">
                            🚚 <strong>Free Shipping:</strong> Orders over $50
                        </p>
                        <p style="margin: 0; font-size: 14px; color: #2563eb; font-weight: 500;">
                            🔒 <strong>Secure Checkout:</strong> All transactions are protected
                        </p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Empty Cart Message -->
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 60px; margin-bottom: 20px;">🛒</div>
                <h2 style="color: #666; margin-bottom: 10px;">Your Cart is Empty</h2>
                <p style="color: #999; margin-bottom: 30px;">Add some products to get started!</p>
                <a href="products.php" class="btn btn-primary" style="display: inline-block; padding: 12px 30px; text-decoration: none;">
                    Start Shopping
                </a>
            </div>
        <?php endif; ?>
    </section>

    <script src="js/cart.js"></script>
    <?php include 'includes/footer.php'; ?>
