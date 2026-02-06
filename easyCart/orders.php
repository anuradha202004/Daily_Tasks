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
    <section class="container" style="padding: 40px 0;">
        <h1 class="section-title">My Orders</h1>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div style="background: <?php echo $_SESSION['flash_type'] === 'success' ? '#d1fae5' : '#fee2e2'; ?>; color: <?php echo $_SESSION['flash_type'] === 'success' ? '#065f46' : '#991b1b'; ?>; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
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
                    <div style="background: #fff; border-radius: 8px; padding: 25px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                        <!-- Order Header with Status Badge -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #eee; align-items: center;">
                            <div>
                                <label style="color: #999; font-size: 12px; text-transform: uppercase;">Order ID</label>
                                <p style="margin: 5px 0 0 0; font-weight: bold; font-size: 16px;">
                                    <?php echo htmlspecialchars($order['order_number']); ?>
                                </p>
                            </div>
                            <div>
                                <label style="color: #999; font-size: 12px; text-transform: uppercase;">Date</label>
                                <p style="margin: 5px 0 0 0; font-weight: bold;">
                                    <?php echo date('F d, Y', strtotime($order['date'])); ?>
                                </p>
                            </div>
                            <div>
                                <label style="color: #999; font-size: 12px; text-transform: uppercase;">Status</label>
                                <p style="margin: 5px 0 0 0; font-weight: bold;">
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
                                    <span style="
                                        display: inline-block;
                                        padding: 6px 14px;
                                        border-radius: 20px;
                                        font-size: 12px;
                                        font-weight: 600;
                                        background: <?php echo $statusColor['bg']; ?>;
                                        color: <?php echo $statusColor['color']; ?>;
                                    ">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span>
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <label style="color: #999; font-size: 12px; text-transform: uppercase;">Total</label>
                                <p style="margin: 5px 0 0 0; font-weight: bold; color: #d32f2f; font-size: 18px;">
                                    $<?php 
                                        $finalTotal = isset($order['total']) ? $order['total'] : ($order['subtotal'] + ($order['tax'] ?? 0) + ($order['shipping_cost'] ?? 0));
                                        echo number_format($finalTotal, 2); 
                                    ?>
                                </p>
                            </div>
                        </div>

                        <!-- Mini Timeline for Tracking -->
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <?php
                            $statuses = ['Processing', 'Shipped', 'Delivered', 'Completed'];
                            $statusColors = [
                                'Processing' => '#f59e0b',
                                'Shipped' => '#3b82f6',
                                'Delivered' => '#10b981',
                                'Completed' => '#6b7280'
                            ];
                            $currentStatus = $order['status'];
                            $currentIndex = array_search($currentStatus, $statuses);
                            if ($currentStatus === 'Cancelled') {
                                $currentIndex = -1;
                            }
                            ?>
                            <div style="position: relative; display: flex; justify-content: space-between; align-items: center;">
                                <!-- Background line -->
                                <div style="position: absolute; top: 20px; left: 40px; right: 40px; height: 2px; background: #e5e7eb; z-index: 1;"></div>
                                
                                <!-- Progress line -->
                                <?php if ($currentIndex >= 0): ?>
                                    <div style="position: absolute; top: 20px; left: 40px; height: 2px; background: linear-gradient(90deg, #f59e0b, #3b82f6, #10b981, #6b7280); z-index: 2; width: calc(<?php echo (($currentIndex / 3) * 100); ?>% + 30px);"></div>
                                <?php endif; ?>

                                <!-- Steps -->
                                <?php foreach ($statuses as $index => $status): ?>
                                    <?php $isActive = ($currentIndex >= $index); ?>
                                    <div style="position: relative; z-index: 3; text-align: center; flex: 1;">
                                        <div style="
                                            width: 40px;
                                            height: 40px;
                                            border-radius: 50%;
                                            background: <?php echo $isActive ? $statusColors[$status] : '#e5e7eb'; ?>;
                                            border: 3px solid white;
                                            box-shadow: 0 0 0 1px <?php echo $isActive ? $statusColors[$status] : '#e5e7eb'; ?>;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            color: white;
                                            font-weight: bold;
                                            font-size: 16px;
                                            margin: 0 auto 8px;
                                        ">
                                            <?php if ($isActive && $index !== 0): ?>✓<?php else: ?><?php echo $index + 1; ?><?php endif; ?>
                                        </div>
                                        <span style="font-size: 12px; color: <?php echo $isActive ? $statusColors[$status] : '#999'; ?>; font-weight: 500;">
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div style="margin-bottom: 20px;">                            
                            <?php if (isset($order['items']) && count($order['items']) > 0): ?>
                                <?php foreach ($order['items'] as $item): ?>
                                    <?php $product = getProductById($item['product_id'] ?? null); ?>
                                    <?php if ($product): ?>
                                        <div style="display: flex; padding: 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px; align-items: center;">
                                            <div style="font-size: 40px; margin-right: 20px;">
                                                <?php echo $product['emoji']; ?>
                                            </div>
                                            <div style="flex: 1;">
                                                <h5 style="margin: 0 0 5px 0;">
                                                    <?php echo htmlspecialchars($product['name']); ?>
                                                </h5>
                                                <p style="margin: 0; font-size: 14px; color: #666;">
                                                    Quantity: <strong><?php echo $item['quantity']; ?></strong> × <strong>$<?php echo number_format($item['price'], 2); ?></strong>
                                                </p>
                                            </div>
                                            <div style="text-align: right;">
                                                <p style="margin: 0; font-weight: bold; color: #d32f2f;">
                                                    $<?php echo number_format($item['quantity'] * $item['price'], 2); ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Order Summary and Actions Row -->
                        <div style="padding: 20px 0; border-top: 1px solid #eee;">
                            <!-- Left: Details and Actions -->
                            <div>


                                <!-- Action Links -->
                                <div style="padding-top: 15px;">
                                    <a href="order-confirmation?id=<?php echo $order['order_number']; ?>" style="
                                        display: inline-block;
                                        background-color: #2563eb;
                                        color: white;
                                        padding: 10px 20px;
                                        border-radius: 6px;
                                        text-decoration: none;
                                        font-weight: 500;
                                        font-size: 14px;
                                        transition: background-color 0.2s;
                                    " onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#2563eb'">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Additional Actions -->
            <div style="margin-top: 40px; padding: 20px; background: #e8f4f8; border-radius: 8px; text-align: center;">
                <p style="margin: 0; color: #555;">
                    Have questions about your order?
                </p>
                <a href="#" style="color: #2563eb; text-decoration: none; font-weight: 500;">
                    Contact Customer Support
                </a>
                <span style="margin: 0 10px; color: #999;">•</span>
                <a href="products.php" style="color: #2563eb; text-decoration: none; font-weight: 500;">
                    Continue Shopping
                </a>
            </div>

        <?php else: ?>
            <!-- No Orders Message -->
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 60px; margin-bottom: 20px;">📦</div>
                <h2 style="color: #666; margin-bottom: 10px;">No Orders Yet</h2>
                <p style="color: #999; margin-bottom: 30px;">You haven't placed any orders yet. Start shopping now!</p>
                <a href="products.php" class="btn btn-primary" style="display: inline-block; padding: 12px 30px; text-decoration: none;">
                    Shop Now
                </a>
            </div>
        <?php endif; ?>
    </section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
