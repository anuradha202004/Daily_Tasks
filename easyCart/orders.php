<?php
session_start();

// Include data and auth
// Load Application Bootstrap
require_once 'app/bootstrap.php';

$pageTitle = 'My Orders';

// Require login
requireLogin();

// Handle Order Cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $cancelOrderId = $_POST['order_id'];
    $currentUser = getCurrentUser();
    
    // Security Check: Verify order belongs to user and is Processing
    $stmt = $pdo->prepare("SELECT id, status FROM sales_order WHERE user_id = ? AND increment_id = ?");
    $stmt->execute([$currentUser['id'], $cancelOrderId]);
    $orderToCancel = $stmt->fetch();
    
    if ($orderToCancel && $orderToCancel['status'] === 'Processing') {
        $updateStmt = $pdo->prepare("UPDATE sales_order SET status = 'Cancelled' WHERE id = ?");
        if ($updateStmt->execute([$orderToCancel['id']])) {
             $_SESSION['flash_message'] = "Order #$cancelOrderId has been cancelled successfully.";
             $_SESSION['flash_type'] = "success";
        } else {
             $_SESSION['flash_message'] = "Failed to cancel order.";
             $_SESSION['flash_type'] = "error";
        }
    } else {
        $_SESSION['flash_message'] = "Order cannot be cancelled. It may have already been processed.";
        $_SESSION['flash_type'] = "error";
    }
    
    // Refresh to show status change
    header("Location: orders.php");
    exit;
}

// Get order history from database
$currentUser = getCurrentUser();
$allOrders = getUserOrders($currentUser['id']) ?? [];
if (isset($_SESSION['last_order'])) {
    $sessionOrderNum = $_SESSION['last_order']['order_number'] ?? null;
    $alreadyExists = false;
    
    foreach ($allOrders as $existingOrder) {
        if ($existingOrder['order_number'] === $sessionOrderNum) {
            $alreadyExists = true;
            break;
        }
    }

    if (!$alreadyExists) {
        // Add latest order from session if not in DB list
        $lastOrderData = [
            'id' => 'recent_' . time(),
            'order_number' => $_SESSION['last_order']['order_number'] ?? 'ORD' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
            'date' => $_SESSION['last_order']['date'] ?? date('Y-m-d H:i:s'),
            'status' => $_SESSION['last_order']['status'] ?? 'Processing',
            'subtotal' => $_SESSION['last_order']['subtotal'] ?? 0,
            'tax' => $_SESSION['last_order']['tax'] ?? 0,
            'shipping' => $_SESSION['last_order']['shipping_cost'] ?? 0,
            'shipping_method_name' => $_SESSION['last_order']['shipping_method_name'] ?? 'Standard',
            'total' => $_SESSION['last_order']['total'] ?? 0,
            'items' => $_SESSION['last_order']['items'] ?? [],
            'customer' => $_SESSION['last_order']['customer'] ?? []
        ];
        array_unshift($allOrders, $lastOrderData);
    }
}
?>
<?php include TEMPLATES_PATH . '/header.php'; ?>

    <!-- My Orders Page -->
    <section class="container orders-section">
        <h1 class="section-title">My Orders</h1>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="flash-message <?php echo $_SESSION['flash_type'] === 'success' ? 'flash-success' : 'flash-error'; ?>">
                <?php 
                    echo $_SESSION['flash_message']; 
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (count($allOrders) > 0): ?>
            <div>
                <?php foreach ($allOrders as $order): ?>
                     <div class="order-card">
                        <!-- Order Header with Status Badge -->
                        <div class="order-card-header">
                            <div>
                                <label class="order-label">Order ID</label>
                                <p class="order-value order-value-lg">
                                    <?php echo htmlspecialchars($order['order_number']); ?>
                                </p>
                            </div>
                            <div>
                                <label class="order-label">Date</label>
                                <p class="order-value">
                                    <?php echo date('F d, Y', strtotime($order['date'])); ?>
                                </p>
                            </div>
                            <div>
                                <label class="order-label">Status</label>
                                <p class="order-value">
                                    <?php
                                    $statusColors = [
                                        'Processing' => ['bg' => '#fff3cd', 'color' => '#856404'],
                                        'Shipped' => ['bg' => '#cfe2ff', 'color' => '#084298'],
                                        'Delivered' => ['bg' => '#d4edda', 'color' => '#155724'],
                                        'Completed' => ['bg' => '#e2e3e5', 'color' => '#41464b'],
                                        'Cancelled' => ['bg' => '#f8d7da', 'color' => '#842029']
                                    ];
                                    $statusColor = $statusColors[$order['status']] ?? ['bg' => '#e5e7eb', 'color' => '#374151'];
                                    ?>
                                    <span class="order-status-badge" style="background: <?php echo $statusColor['bg']; ?>; color: <?php echo $statusColor['color']; ?>;">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span>
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <label class="order-label">Total</label>
                                <p class="order-total-value">
                                    $<?php 
                                        $finalTotal = isset($order['total']) ? $order['total'] : ($order['subtotal'] + ($order['tax'] ?? 0) + ($order['shipping_cost'] ?? 0));
                                        echo number_format($finalTotal, 2); 
                                    ?>
                                </p>
                            </div>
                        </div>

                        <!-- Mini Timeline for Tracking -->
                        <div class="mini-timeline-box">
                            <?php
                            $statuses = ['Processing', 'Shipped', 'Delivered', 'Completed'];
                            $currentStatus = $order['status'];
                            $currentIndex = array_search($currentStatus, $statuses);
                            if ($currentStatus === 'Cancelled') {
                                $currentIndex = -1;
                            }
                            ?>
                            <div class="mini-timeline-wrapper">
                                <!-- Background line -->
                                <div class="mini-timeline-bg"></div>
                                
                                <!-- Progress line -->
                                <?php if ($currentIndex >= 0): ?>
                                    <div class="mini-timeline-progress" style="width: calc(<?php echo (($currentIndex / 3) * 100); ?>% + 30px);"></div>
                                <?php endif; ?>

                                <!-- Steps -->
                                <?php foreach ($statuses as $index => $status): ?>
                                    <?php 
                                    $isActive = ($currentIndex >= $index); 
                                    // Local style override needed for dynamic colors as mapped in PHP above
                                    // Or simplified to generic active/inactive
                                    // For now, let's keep the dynamic color logic but use classes for structure
                                    $stepColor = $isActive ? ($index == 0 ? '#f59e0b' : ($index == 1 ? '#3b82f6' : ($index == 2 ? '#10b981' : '#6b7280'))) : '#e5e7eb';
                                    ?>
                                    <div class="mini-timeline-step">
                                        <div class="mini-step-circle" style="
                                            background: <?php echo $stepColor; ?>;
                                            box-shadow: 0 0 0 1px <?php echo $stepColor; ?>;
                                        ">
                                            <?php if ($isActive && $index !== 0): ?>✓<?php else: ?><?php echo $index + 1; ?><?php endif; ?>
                                        </div>
                                        <span class="mini-step-label" style="color: <?php echo $isActive ? $stepColor : '#999'; ?>;">
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="order-items-list">                            
                            <?php if (isset($order['items']) && count($order['items']) > 0): ?>
                                <?php foreach ($order['items'] as $item): ?>
                                    <?php $product = getProductById($item['product_id'] ?? null); ?>
                                    <?php if ($product): ?>
                                        <div class="order-item-row">
                                            <div class="order-item-emoji">
                                                <?php echo $product['emoji']; ?>
                                            </div>
                                            <div class="order-item-details">
                                                <h5 class="order-item-title">
                                                    <?php echo htmlspecialchars($product['name']); ?>
                                                </h5>
                                                <p class="order-item-meta">
                                                    Quantity: <strong><?php echo $item['quantity']; ?></strong> × <strong>$<?php echo number_format($item['price'], 2); ?></strong>
                                                </p>
                                            </div>
                                            <div style="text-align: right;">
                                                <p class="order-item-total">
                                                    $<?php echo number_format($item['quantity'] * $item['price'], 2); ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Order Summary and Actions Row -->
                        <div class="order-actions-row">
                            <!-- Left: Details and Actions -->
                            <div>
                                <!-- Action Links -->
                                <div class="order-actions-container">
                                    <a href="order-confirmation?id=<?php echo $order['order_number']; ?>" class="btn-view-details">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Additional Actions -->
            <div class="support-box">
                <p class="support-text">
                    Have questions about your order?
                </p>
                <a href="#" class="support-link">
                    Contact Customer Support
                </a>
                <span class="support-separator">•</span>
                <a href="products" class="support-link">
                    Continue Shopping
                </a>
            </div>

        <?php else: ?>
            <!-- No Orders Message -->
            <div class="no-orders-container">
                <div class="no-orders-icon">📦</div>
                <h2 class="no-orders-title">No Orders Yet</h2>
                <p class="no-orders-text">You haven't placed any orders yet. Start shopping now!</p>
                <a href="products" class="btn btn-primary" style="display: inline-block; padding: 12px 30px; text-decoration: none;">
                    Shop Now
                </a>
            </div>
        <?php endif; ?>
    </section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
