<?php
session_start();

// Include data and auth
require_once 'data.php';
require_once 'auth.php';

$pageTitle = 'Shopping Cart';

// Require login
requireLogin();

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : null;
    
    if ($action === 'remove' && isset($_POST['product_id'])) {
        $productId = intval($_POST['product_id']);
        unset($_SESSION['cart'][$productId]);
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
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
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

$tax = $subtotal * 0.10; // 10% tax
$total = $subtotal + $tax;
?>
<?php include 'header.php'; ?>

    <!-- Shopping Cart Page -->
    <section class="container" style="padding: 40px 0;">
        <h1 class="section-title">Shopping Cart</h1>

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
                            <div style="padding: 20px; border-bottom: 1px solid #dee2e6; display: flex; gap: 20px; align-items: center;">
                                <!-- Product Image -->
                                <div style="font-size: 50px; flex-shrink: 0;">
                                    <?php echo $item['product']['emoji']; ?>
                                </div>

                                <!-- Product Details -->
                                <div style="flex: 1;">
                                    <h4 style="margin: 0 0 5px 0;">
                                        <a href="product-detail.php?id=<?php echo $item['product']['id']; ?>" style="color: #2563eb; text-decoration: none;">
                                            <?php echo htmlspecialchars($item['product']['name']); ?>
                                        </a>
                                    </h4>
                                    <p style="margin: 0; color: #666; font-size: 14px;">
                                        <?php echo formatPrice($item['product']['price']); ?> each
                                    </p>
                                </div>

                                <!-- Quantity & Price -->
                                <div style="text-align: right;">
                                    <form method="POST" style="display: flex; gap: 10px; align-items: center; margin-bottom: 10px;">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['product']['stock']; ?>" 
                                               style="width: 60px; padding: 5px; border: 1px solid #ddd; border-radius: 4px;">
                                        <button type="submit" style="padding: 5px 10px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">Update</button>
                                    </form>
                                    <p style="margin: 0; font-weight: bold; font-size: 16px;">
                                        <?php echo formatPrice($item['itemTotal']); ?>
                                    </p>
                                </div>

                                <!-- Remove Button -->
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                    <button type="submit" style="background: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer;">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>

                        <!-- Clear Cart Button -->
                        <div style="padding: 20px; text-align: right;">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="clear">
                                <button type="submit" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
                                    Clear Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div>
                    <div style="background: #f8f9fa; padding: 25px; border-radius: 8px; border: 2px solid #dee2e6;">
                        <h3 style="margin-top: 0; margin-bottom: 20px;">Order Summary</h3>

                        <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #dee2e6;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span>Subtotal</span>
                                <span><?php echo formatPrice($subtotal); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span>Tax (10%)</span>
                                <span><?php echo formatPrice($tax); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span>Shipping</span>
                                <span><?php echo $subtotal > 50 ? 'Free' : formatPrice(9.99); ?></span>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 18px; font-weight: bold;">
                            <span>Total</span>
                            <span style="color: #d32f2f;">
                                <?php 
                                $finalTotal = $total;
                                if ($subtotal <= 50) {
                                    $finalTotal += 9.99;
                                }
                                echo formatPrice($finalTotal);
                                ?>
                            </span>
                        </div>

                        <a href="checkout.php" class="btn btn-primary" style="display: block; text-align: center; padding: 15px; text-decoration: none;">
                            Proceed to Checkout
                        </a>

                        <a href="products.php" style="display: block; text-align: center; padding: 12px; margin-top: 10px; color: #2563eb; text-decoration: none; border: 1px solid #2563eb; border-radius: 4px;">
                            Continue Shopping
                        </a>
                    </div>

                    <!-- Promo Code -->
                    <div style="background: #e8f4f8; padding: 15px; border-radius: 8px; margin-top: 20px;">
                        <p style="margin: 0 0 10px 0; font-size: 14px; color: #666;">
                            <strong>Free Shipping:</strong> Orders over $50
                        </p>
                        <p style="margin: 0; font-size: 14px; color: #666;">
                            <strong>Secure Checkout:</strong> All transactions are protected
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

<?php include 'footer.php'; ?>
