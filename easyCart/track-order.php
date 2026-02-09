<?php
session_start();

// Include data and auth
// Load Application Bootstrap
require_once 'app/bootstrap.php';

$pageTitle = 'Track Order';

// Handle order tracking lookup
$trackingResult = null;
$trackingError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'track_order') {
    $orderNumber = trim($_POST['order_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (empty($orderNumber) || empty($email)) {
        $trackingError = 'Please enter both order number and email address.';
    } else {
        // Check if it's the last order in session
        if (isset($_SESSION['last_order']) && 
            $_SESSION['last_order']['order_number'] === $orderNumber &&
            strtolower($_SESSION['last_order']['customer']['email']) === strtolower($email)) {
            $trackingResult = $_SESSION['last_order'];
        } else {
            // Check in static orders (from data.php)
            global $orders;
            foreach ($orders as $order) {
                if ($order['order_number'] === $orderNumber) {
                    // For demo purposes, we'll accept any email for static orders
                    $trackingResult = $order;
                    break;
                }
            }
            
            if (!$trackingResult) {
                $trackingError = 'Order not found. Please check your order number and email address.';
            }
        }
    }
}
?>
<?php include TEMPLATES_PATH . '/header.php'; ?>

    <!-- Track Order Page -->
    <section class="container order-confirmation-section">
        <h1 class="section-title">Track Your Order</h1>

        <?php if (!$trackingResult): ?>
            <!-- Tracking Form -->
            <div class="tracking-form-container">
                <div class="tracking-form-box">
                    <div class="tracking-form-header">
                        <div class="tracking-icon">📦</div>
                        <h2 class="tracking-form-title">Track Your Package</h2>
                        <p class="tracking-form-desc">Enter your order details to see the current status</p>
                    </div>

                    <?php if ($trackingError): ?>
                        <div class="error-alert">
                            ⚠️ <?php echo htmlspecialchars($trackingError); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <input type="hidden" name="action" value="track_order">

                        <div class="form-group-track">
                            <label class="form-label-track">Order Number</label>
                            <input 
                                type="text" 
                                name="order_number" 
                                class="form-input-track"
                                placeholder="e.g., ORD123456" 
                                required 
                                value="<?php echo isset($_POST['order_number']) ? htmlspecialchars($_POST['order_number']) : ''; ?>"
                            >
                        </div>

                        <div class="form-group-track">
                            <label class="form-label-track">Email Address</label>
                            <input 
                                type="email" 
                                name="email" 
                                class="form-input-track"
                                placeholder="your.email@example.com" 
                                required 
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            >
                        </div>

                        <button type="submit" class="btn-track">
                            🔍 Track Order
                        </button>
                    </form>

                    <div class="tracking-footer">
                        <p class="footer-help-text">Need help?</p>
                        <a href="#" class="footer-link">Contact Support</a>
                        <span class="separator">|</span>
                        <a href="orders" class="footer-link">View All Orders</a>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Tracking Results -->
            <div class="monitor-container">
                <!-- Success Message -->
                <div class="alert-box alert-success">
                    ✓ Order Found! Here's your tracking information.
                </div>

                <!-- Order Info Header -->
                <div class="order-info-header">
                    <div class="order-info-grid">
                        <div>
                            <label class="info-label">Order Number</label>
                            <p class="info-value">
                                <?php echo htmlspecialchars($trackingResult['order_number']); ?>
                            </p>
                        </div>
                        <div>
                            <label class="info-label">Order Date</label>
                            <p class="info-value">
                                <?php echo date('M d, Y', strtotime($trackingResult['date'])); ?>
                            </p>
                        </div>
                        <div>
                            <label class="info-label">Total Amount</label>
                            <p class="info-value info-value-total">
                                $<?php echo number_format($trackingResult['total'], 2); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tracking Timeline -->
                <div class="tracking-timeline-box">
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
                    $currentStatus = $trackingResult['status'];
                    $currentIndex = array_search($currentStatus, $statuses);
                    
                    if ($currentStatus === 'Cancelled') {
                        $currentIndex = -1;
                    }
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
                                <span class="step-label" style="
                                    color: <?php echo $isCurrentStatus ? $statusColor : '#666'; ?>;
                                ">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                                
                                <!-- Step Date/Time -->
                                <span class="step-date">
                                    <?php
                                    if ($isActive) {
                                        if ($index === 0) {
                                            echo date('M d, Y', strtotime($trackingResult['date']));
                                        } else {
                                            // Estimate dates for other stages
                                            $daysToAdd = $index * 2;
                                            echo date('M d, Y', strtotime($trackingResult['date'] . " +{$daysToAdd} days"));
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
                                <strong>📦 Estimated Delivery:</strong> <?php echo date('F d, Y', strtotime($trackingResult['date'] . ' +5 days')); ?> to <?php echo date('F d, Y', strtotime($trackingResult['date'] . ' +7 days')); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Order Items -->
                <?php if (isset($trackingResult['items']) && count($trackingResult['items']) > 0): ?>
                    <div class="order-items-box">
                        <h3 class="info-card-title">Order Items</h3>
                        
                        <?php foreach ($trackingResult['items'] as $item): ?>
                            <?php 
                            // Handle both formats: items with 'product' key or direct product_id
                            if (isset($item['product'])) {
                                $product = $item['product'];
                            } else {
                                $product = getProductById($item['product_id']);
                            }
                            ?>
                            <?php if ($product): ?>
                                <div class="item-row-track">
                                    <div class="item-emoji">
                                        <?php echo $product['emoji']; ?>
                                    </div>
                                    <div class="item-details">
                                        <h4 class="item-name">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </h4>
                                        <p class="item-qty">
                                            Quantity: <?php echo $item['quantity']; ?>
                                        </p>
                                    </div>
                                    <div class="item-total-col">
                                        <p class="item-price">
                                            $<?php echo number_format(isset($item['itemTotal']) ? $item['itemTotal'] : ($item['quantity'] * $item['price']), 2); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Actions -->
                <div class="tracking-footer" style="border-top: none; margin-top: 10px;">
                    <a href="track-order.php" class="btn btn-primary btn-track-another">
                        Track Another Order
                    </a>
                    <a href="products" class="btn-shopping-outline">
                        Continue Shopping
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
