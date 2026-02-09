<?php
// Load Application Bootstrap
require_once 'app/bootstrap.php';

$pageTitle = 'Order Confirmation';

// Get order from DB if ID is provided (for viewing history)
if (isset($_GET['id'])) {
    $fetchedOrder = getOrderByNumber($_GET['id']);
    if ($fetchedOrder) {
        $mappedOrder = [
             'order_number' => $fetchedOrder['increment_id'],
             'date' => $fetchedOrder['created_at'],
             'status' => $fetchedOrder['status'],
             'total' => $fetchedOrder['grand_total'],
             'subtotal' => $fetchedOrder['subtotal'],
             'tax' => $fetchedOrder['tax_amount'],
             'shipping_cost' => $fetchedOrder['shipping_amount'],
             'promo_discount' => $fetchedOrder['discount_amount'],
             'shipping_method_name' => $fetchedOrder['shipping_method'] ?? 'Standard',
             'customer' => [
                 'first_name' => $fetchedOrder['first_name'],
                 'last_name' => $fetchedOrder['last_name'],
                 'email' => $fetchedOrder['email'],
                 'phone' => $fetchedOrder['phone'],
                 'address' => $fetchedOrder['address'],
                 'city' => $fetchedOrder['city'],
                 'state' => $fetchedOrder['state'],
                 'zip' => $fetchedOrder['zip']
             ],
             'items' => []
        ];
        
        // Fetch items details
        foreach ($fetchedOrder['items'] as $item) {
             $productDetails = getProductById($item['product_id']);
             $mappedOrder['items'][] = [
                 'product_id' => $item['product_id'],
                 'quantity' => $item['qty_ordered'] ?? $item['quantity'] ?? 1,
                 'itemTotal' => $item['price'] * ($item['qty_ordered'] ?? $item['quantity'] ?? 1),
                 'product' => [
                     'name' => $item['name'] ?? $productDetails['name'],
                     'emoji' => $productDetails['emoji'] ?? '📦'
                 ]
             ];
        }
        
        $_SESSION['last_order'] = $mappedOrder;
    }
}

// Get last order from session
$lastOrder = isset($_SESSION['last_order']) ? $_SESSION['last_order'] : null;

// If no order, redirect to products
if (!$lastOrder) {
    header('Location: products');
    exit;
}

// Handle cancel order request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_order') {
    // Update Database Status
    $orderNumber = $_SESSION['last_order']['order_number'];
    global $pdo;
    $stmt = $pdo->prepare("UPDATE sales_order SET status = 'Cancelled' WHERE increment_id = ?");
    $stmt->execute([$orderNumber]);

    // Update Session Status
    $_SESSION['last_order']['status'] = 'Cancelled';
    $_SESSION['last_order']['cancelled_date'] = date('Y-m-d H:i:s');
    $cancellationMessage = 'Order has been cancelled successfully. You will receive a refund within 5-7 business days.';
}

// Initialize order status if not exists
if (!isset($_SESSION['last_order']['status'])) {
    $_SESSION['last_order']['status'] = 'Processing';
    $_SESSION['last_order']['date'] = date('Y-m-d H:i:s');
    $_SESSION['last_order']['order_number'] = 'ORD' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
}
?>
<?php include TEMPLATES_PATH . '/header.php'; ?>
    <script src="public/js/order-confirmation.js"></script>

    <!-- Order Confirmation Page -->
    <section class="container order-confirmation-section">
        <div class="order-header">
            <?php if (isset($_SESSION['last_order']['status']) && $_SESSION['last_order']['status'] === 'Cancelled'): ?>
                <div class="status-icon error">✗</div>
                <h1 class="order-title">Order Cancelled</h1>
                <p class="order-subtitle">This order has been cancelled.</p>
            <?php else: ?>
                <div class="status-icon success">✓</div>
                <h1 class="order-title">Order Confirmed!</h1>
                <p class="order-subtitle">Thank you for your purchase</p>
            <?php endif; ?>
            <p class="order-number">Order Number: <strong><?php echo htmlspecialchars($_SESSION['last_order']['order_number']); ?></strong></p>
        </div>

        <?php if (isset($cancellationMessage)): ?>
            <div class="alert-box alert-success">
                ✓ <?php echo htmlspecialchars($cancellationMessage); ?>
            </div>
        <?php endif; ?>

        <!-- Order Tracking Timeline -->
        <div class="tracking-container">
            <h3 class="tracking-title">Order Tracking</h3>
            
            <?php
            $statuses = ['Processing', 'Shipped', 'Delivered', 'Completed'];
            $statusColors = [
                'Processing' => '#f59e0b',
                'Shipped' => '#3b82f6',
                'Delivered' => '#10b981',
                'Completed' => '#6b7280',
                'Cancelled' => '#dc2626'
            ];
            $currentStatus = $_SESSION['last_order']['status'];
            $currentIndex = array_search($currentStatus, $statuses);
            ?>

            <div class="timeline-wrapper">
                <!-- Timeline line background -->
                <div class="timeline-line-bg"></div>
                
                <!-- Timeline line progress -->
                <?php if ($currentIndex >= 0): ?>
                    <div class="timeline-line-progress" style="width: calc(<?php echo (($currentIndex / 3) * 100); ?>% + 30px);"></div>
                <?php endif; ?>

                <!-- Status Steps -->
                <?php foreach ($statuses as $index => $status): ?>
                    <?php
                    $isActive = $currentIndex >= $index;
                    $isCurrentStatus = $currentStatus === $status;
                    $statusColor = $statusColors[$status];
                    ?>
                    <div class="timeline-step">
                        <!-- Step Circle -->
                        <div class="step-circle" style="
                            background: <?php echo $isActive ? $statusColor : '#e5e7eb'; ?>;
                            box-shadow: 0 0 0 2px <?php echo $isActive ? $statusColor : '#e5e7eb'; ?>;
                        ">
                            <?php if ($isActive && $index !== 0): ?>✓<?php else: ?><?php echo $index + 1; ?><?php endif; ?>
                        </div>
                        
                        <!-- Step Label -->
                        <span class="step-label" style="color: <?php echo $isCurrentStatus ? $statusColor : '#666'; ?>;">
                            <?php echo htmlspecialchars($status); ?>
                        </span>
                        
                        <!-- Step Date/Time -->
                        <span class="step-date">
                            <?php
                            if ($isActive) {
                                if ($index === 0) {
                                    echo date('M d, Y', strtotime($_SESSION['last_order']['date']));
                                } else {
                                    // Estimate dates for other stages
                                    $daysToAdd = $index * 2;
                                    echo date('M d, Y', strtotime($_SESSION['last_order']['date'] . " +{$daysToAdd} days"));
                                }
                            } else {
                                echo 'Pending';
                            }
                            ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Current Status Info -->
            <div class="current-status-box" style="border-left-color: <?php echo $statusColors[$currentStatus]; ?>;">
                <h4 class="current-status-title" style="color: <?php echo $statusColors[$currentStatus]; ?>;">
                    Current Status: <?php echo htmlspecialchars($currentStatus); ?>
                </h4>
                <p class="current-status-desc">
                    <?php
                    if ($currentStatus === 'Processing') {
                        echo 'Your order is being processed. We\'re preparing your items for shipment.';
                    } elseif ($currentStatus === 'Shipped') {
                        echo 'Your order has been shipped! Track your package with the tracking number provided in your email.';
                    } elseif ($currentStatus === 'Delivered') {
                        echo 'Your order has been delivered. Thank you for shopping with us!';
                    } elseif ($currentStatus === 'Completed') {
                        echo 'Your order is complete. We hope you enjoyed your purchase!';
                    } elseif ($currentStatus === 'Cancelled') {
                        echo 'Your order has been cancelled. Refund will be processed within 5-7 business days.';
                    }
                    ?>
                </p>
            </div>

            <!-- Estimated Delivery -->
            <?php if ($currentStatus !== 'Cancelled' && $currentStatus !== 'Completed'): ?>
                <div class="estimated-delivery-box">
                    <p class="delivery-text">
                        <strong>📦 Estimated Delivery:</strong> <?php echo date('F d, Y', strtotime($_SESSION['last_order']['date'] . ' +5 days')); ?> to <?php echo date('F d, Y', strtotime($_SESSION['last_order']['date'] . ' +7 days')); ?>
                    </p>
                </div>
            <?php endif; ?>


        </div>

        <div class="order-grid">
            <!-- Order Details -->
            <div>
                <!-- Customer Info -->
                <div class="info-card">
                    <h3 class="info-card-title">Shipping To</h3>
                    <p class="customer-info-text customer-name">
                        <?php echo htmlspecialchars($lastOrder['customer']['first_name']) . ' ' . htmlspecialchars($lastOrder['customer']['last_name']); ?>
                    </p>
                    <p class="customer-info-text">
                        <?php echo htmlspecialchars($lastOrder['customer']['address']); ?><br>
                        <?php echo htmlspecialchars($lastOrder['customer']['city']) . ', ' . htmlspecialchars($lastOrder['customer']['state']) . ' ' . htmlspecialchars($lastOrder['customer']['zip']); ?>
                    </p>
                    <p class="customer-info-text">
                        Email: <?php echo htmlspecialchars($lastOrder['customer']['email']); ?><br>
                        Phone: <?php echo htmlspecialchars($lastOrder['customer']['phone']); ?>
                    </p>
                </div>

                <!-- Ordered Items -->
                <div class="info-card">
                    <h3 class="info-card-title">Ordered Items</h3>
                    
                    <?php foreach ($lastOrder['items'] as $item): ?>
                        <div class="item-row">
                            <div class="item-emoji">
                                <?php echo $item['product']['emoji']; ?>
                            </div>
                            <div class="item-details">
                                <h4 class="item-name">
                                    <?php echo htmlspecialchars($item['product']['name']); ?>
                                </h4>
                                <p class="item-qty">
                                    Quantity: <?php echo $item['quantity']; ?>
                                </p>
                            </div>
                            <div class="item-total-col">
                                <p class="item-price">
                                    $<?php echo number_format($item['itemTotal'], 2); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="summary-card">
                    <h3 class="info-card-title">Order Summary</h3>

                    <div class="summary-details">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>$<?php echo number_format($lastOrder['subtotal'], 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Tax (18%)</span>
                            <span>$<?php echo number_format($lastOrder['tax'], 2); ?></span>
                        </div>
                        <?php if (isset($lastOrder['promo_discount']) && $lastOrder['promo_discount'] > 0): ?>
                        <div class="summary-row summary-discount">
                            <span>Promo Discount</span>
                            <span>-$<?php echo number_format($lastOrder['promo_discount'], 2); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="summary-row">
                            <span>Shipping (<?php echo htmlspecialchars($lastOrder['shipping_method_name'] ?? 'Standard'); ?>)</span>
                            <span>$<?php echo number_format($lastOrder['shipping_cost'] ?? 0, 2); ?></span>
                        </div>
                    </div>

                    <div class="summary-total-row">
                        <span>Total</span>
                        <span class="total-price">$<?php echo number_format($lastOrder['total'], 2); ?></span>
                    </div>

                    <?php if (isset($_SESSION['last_order']['status']) && $_SESSION['last_order']['status'] === 'Cancelled'): ?>
                        <div class="alert-box alert-danger" style="margin-top: 20px;">
                            <p style="margin: 0; font-weight: 500;">
                                ✗ Order Cancelled
                            </p>
                            <p style="margin: 5px 0 0 0; font-size: 12px;">
                                Refund pending (5-7 days)
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="alert-box alert-success" style="margin-top: 20px;">
                            <p style="margin: 0; font-weight: 500;">
                                ✓ Order Placed Successfully
                            </p>
                            <p style="margin: 5px 0 0 0; font-size: 12px;">
                                Order placed on <?php echo date('F d, Y \a\t h:i A', strtotime($lastOrder['date'])); ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Next Steps -->
                    <div class="next-steps-container">
                        <h4 style="margin-bottom: 10px;">What's Next?</h4>
                        <ul class="next-steps-list">
                            <li>Confirmation email sent to your inbox</li>
                            <li>Order processing within 24 hours</li>
                            <li>Tracking info will be emailed</li>
                            <li>Estimated delivery: 5-7 business days</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <?php if (isset($_SESSION['last_order']['status']) && $_SESSION['last_order']['status'] === 'Processing'): ?>
                            <button onclick="openCancelModal()" class="btn-cancel">
                                ❌ Cancel Order
                            </button>
                        <?php endif; ?>
                        <a href="profile" class="btn btn-primary btn-dashboard">
                            Go to Dashboard
                        </a>
                        <a href="invoice?latest=true" target="_blank" class="btn btn-download">
                            📄 Download Invoice
                        </a>
                        <a href="products" class="btn-shopping">
                            Continue Shopping
                        </a>
                        <?php if (isset($_SESSION['last_order']['status']) && $_SESSION['last_order']['status'] === 'Cancelled'): ?>
                            <button onclick="window.location.href='products'" class="btn-buy-again">
                                🔄 Buy Again
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="help-section">
            <h3 style="margin-top: 0;">Need Help?</h3>
            <p class="help-text">
                For any questions about your order, please contact our customer support:
            </p>
            <p class="contact-info">
                📧 Email: <strong>support@easycart.com</strong><br>
                📞 Phone: <strong>+1 (555) 123-4567</strong><br>
                ⏰ Available: Monday - Friday, 9:00 AM - 6:00 PM
            </p>
        </div>
    </section>

    <!-- Cancel Order Modal -->
    <div id="cancelModal" class="modal-overlay">
        <div class="modal-content">
            <h2 class="modal-title">Cancel Order?</h2>
            <p class="modal-text">
                Are you sure you want to cancel this order? This action cannot be undone. Your refund will be processed within 5-7 business days.
            </p>
            
            <form method="POST" class="modal-actions">
                <input type="hidden" name="action" value="cancel_order">
                <button type="submit" class="btn-confirm-cancel">
                    Yes, Cancel Order
                </button>
                <button type="button" onclick="closeCancelModal()" class="btn-keep-order">
                    No, Keep Order
                </button>
            </form>
        </div>
    </div>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
