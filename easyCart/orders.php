<?php
session_start();

// Include data and auth
require_once 'includes/data.php';
require_once 'includes/auth.php';

$pageTitle = 'My Orders';

// Require login
requireLogin();

// Get static order history from data
global $orders;

// Add last order to the orders list
$allOrders = $orders;
if (isset($_SESSION['last_order'])) {
    // Add latest order from session
    $lastOrderData = [
        'id' => 'recent_' . time(),
        'order_number' => $_SESSION['last_order']['order_number'] ?? 'ORD' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
        'date' => $_SESSION['last_order']['date'] ?? date('Y-m-d H:i:s'),
        'status' => $_SESSION['last_order']['status'] ?? 'Processing',
        'subtotal' => $_SESSION['last_order']['subtotal'] ?? 0,
        'tax' => $_SESSION['last_order']['tax'] ?? 0,
        'shipping' => $_SESSION['last_order']['shipping'] ?? 0,
        'total' => $_SESSION['last_order']['total'] ?? 0,
        'items' => $_SESSION['last_order']['items'] ?? [],
        'customer' => $_SESSION['last_order']['customer'] ?? []
    ];
    array_unshift($allOrders, $lastOrderData);
}
?>
<?php include 'includes/header.php'; ?>

    <!-- My Orders Page -->
    <section class="container" style="padding: 40px 0;">
        <h1 class="section-title">My Orders</h1>

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
                                    $<?php echo number_format($order['subtotal'] + ($order['tax'] ?? 0) + ($order['shipping'] ?? 0), 2); ?>
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
                            <h4 style="margin: 0 0 15px 0; color: #333;">Items Ordered</h4>
                            
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
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; padding: 20px 0; border-top: 1px solid #eee;">
                            <!-- Left: Details and Actions -->
                            <div>
                                <!-- Status Info -->
                                <?php if ($order['status'] === 'Processing'): ?>
                                    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #f59e0b; margin-bottom: 15px;">
                                        <p style="margin: 0; color: #856404; font-weight: 500;">⏳ Order Processing</p>
                                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #856404;">
                                            We're preparing your items for shipment. You'll receive a shipping confirmation shortly.
                                        </p>
                                    </div>
                                <?php elseif ($order['status'] === 'Shipped'): ?>
                                    <div style="background: #cfe2ff; padding: 15px; border-radius: 8px; border-left: 4px solid #3b82f6; margin-bottom: 15px;">
                                        <p style="margin: 0; color: #084298; font-weight: 500;">📦 Order Shipped</p>
                                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #084298;">
                                            Tracking #: <strong><?php echo 'TRACK' . str_pad($order['id'] ?? 1, 9, '0', STR_PAD_LEFT); ?></strong>
                                        </p>
                                    </div>
                                <?php elseif ($order['status'] === 'Delivered'): ?>
                                    <div style="background: #d4edda; padding: 15px; border-radius: 8px; border-left: 4px solid #10b981; margin-bottom: 15px;">
                                        <p style="margin: 0; color: #155724; font-weight: 500;">✓ Order Delivered</p>
                                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #155724;">
                                            Delivered on <?php echo date('F d, Y', strtotime($order['date'] . ' +5 days')); ?>
                                        </p>
                                    </div>
                                <?php elseif ($order['status'] === 'Cancelled'): ?>
                                    <div style="background: #f8d7da; padding: 15px; border-radius: 8px; border-left: 4px solid #dc2626; margin-bottom: 15px;">
                                        <p style="margin: 0; color: #842029; font-weight: 500;">✗ Order Cancelled</p>
                                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #842029;">
                                            Refund will be processed within 5-7 business days.
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <!-- Action Links -->
                                <div style="padding-top: 15px;">
                                    <a href="order-confirmation.php" style="color: #2563eb; text-decoration: none; margin-right: 20px; font-size: 14px; font-weight: 500;">
                                        View Details
                                    </a>
                                    <a href="#" style="color: #2563eb; text-decoration: none; margin-right: 20px; font-size: 14px; font-weight: 500;">
                                        Download Invoice
                                    </a>
                                    <?php if ($order['status'] === 'Delivered'): ?>
                                        <a href="#" style="color: #2563eb; text-decoration: none; font-size: 14px; font-weight: 500;">
                                            Leave Review
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Right: Order Total -->
                            <div style="text-align: right; padding: 15px 0;">
                                <div style="margin-bottom: 15px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
                                        <span>Subtotal:</span>
                                        <span>$<?php echo number_format($order['subtotal'], 2); ?></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
                                        <span>Tax:</span>
                                        <span>$<?php echo number_format($order['tax'] ?? 0, 2); ?></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px;">
                                        <span>Shipping:</span>
                                        <span><?php echo ($order['shipping'] ?? 0) === 0 ? 'Free' : '$' . number_format($order['shipping'], 2); ?></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; padding-top: 15px; border-top: 2px solid #eee;">
                                        <span>Total:</span>
                                        <span style="color: #d32f2f;">
                                            $<?php echo number_format($order['subtotal'] + ($order['tax'] ?? 0) + ($order['shipping'] ?? 0), 2); ?>
                                        </span>
                                    </div>
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

<?php include 'includes/footer.php'; ?>
