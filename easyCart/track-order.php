<?php
session_start();

// Include data and auth
require_once 'data.php';

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
<?php include 'header.php'; ?>

    <!-- Track Order Page -->
    <section class="container" style="padding: 40px 0;">
        <h1 class="section-title">Track Your Order</h1>

        <?php if (!$trackingResult): ?>
            <!-- Tracking Form -->
            <div style="max-width: 600px; margin: 0 auto;">
                <div style="background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="font-size: 60px; margin-bottom: 15px;">📦</div>
                        <h2 style="margin: 0 0 10px 0; color: #333;">Track Your Package</h2>
                        <p style="color: #666; margin: 0;">Enter your order details to see the current status</p>
                    </div>

                    <?php if ($trackingError): ?>
                        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                            ⚠️ <?php echo htmlspecialchars($trackingError); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <input type="hidden" name="action" value="track_order">

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Order Number</label>
                            <input 
                                type="text" 
                                name="order_number" 
                                placeholder="e.g., ORD123456" 
                                required 
                                style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; transition: border-color 0.3s;"
                                onfocus="this.style.borderColor='#2563eb'" 
                                onblur="this.style.borderColor='#ddd'"
                                value="<?php echo isset($_POST['order_number']) ? htmlspecialchars($_POST['order_number']) : ''; ?>"
                            >
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Email Address</label>
                            <input 
                                type="email" 
                                name="email" 
                                placeholder="your.email@example.com" 
                                required 
                                style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; transition: border-color 0.3s;"
                                onfocus="this.style.borderColor='#2563eb'" 
                                onblur="this.style.borderColor='#ddd'"
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            >
                        </div>

                        <button 
                            type="submit" 
                            class="btn btn-primary" 
                            style="width: 100%; padding: 14px; font-size: 16px; cursor: pointer; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none; border-radius: 8px; color: white; font-weight: 600; transition: transform 0.2s;"
                            onmouseover="this.style.transform='scale(1.02)'" 
                            onmouseout="this.style.transform='scale(1)'"
                        >
                            🔍 Track Order
                        </button>
                    </form>

                    <div style="margin-top: 30px; padding-top: 25px; border-top: 1px solid #eee; text-align: center;">
                        <p style="margin: 0 0 10px 0; color: #666; font-size: 14px;">Need help?</p>
                        <a href="#" style="color: #2563eb; text-decoration: none; font-weight: 500; margin-right: 15px;">Contact Support</a>
                        <span style="color: #ddd;">|</span>
                        <a href="orders.php" style="color: #2563eb; text-decoration: none; font-weight: 500; margin-left: 15px;">View All Orders</a>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Tracking Results -->
            <div style="max-width: 900px; margin: 0 auto;">
                <!-- Success Message -->
                <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 30px; text-align: center;">
                    ✓ Order Found! Here's your tracking information.
                </div>

                <!-- Order Info Header -->
                <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; text-align: center;">
                        <div>
                            <label style="color: #999; font-size: 12px; text-transform: uppercase; display: block; margin-bottom: 5px;">Order Number</label>
                            <p style="margin: 0; font-weight: bold; font-size: 18px; color: #333;">
                                <?php echo htmlspecialchars($trackingResult['order_number']); ?>
                            </p>
                        </div>
                        <div>
                            <label style="color: #999; font-size: 12px; text-transform: uppercase; display: block; margin-bottom: 5px;">Order Date</label>
                            <p style="margin: 0; font-weight: bold; font-size: 18px; color: #333;">
                                <?php echo date('M d, Y', strtotime($trackingResult['date'])); ?>
                            </p>
                        </div>
                        <div>
                            <label style="color: #999; font-size: 12px; text-transform: uppercase; display: block; margin-bottom: 5px;">Total Amount</label>
                            <p style="margin: 0; font-weight: bold; font-size: 18px; color: #d32f2f;">
                                $<?php echo number_format($trackingResult['total'], 2); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tracking Timeline -->
                <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
                    <h3 style="margin-top: 0; margin-bottom: 25px;">Order Tracking</h3>
                    
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

                    <div style="position: relative; padding: 20px 0; display: flex; justify-content: space-between;">
                        <!-- Timeline line background -->
                        <div style="position: absolute; top: 40px; left: 60px; right: 60px; height: 2px; background: #e5e7eb; z-index: 1;"></div>
                        
                        <!-- Timeline line progress -->
                        <?php if ($currentIndex >= 0): ?>
                            <div style="position: absolute; top: 40px; left: 60px; height: 2px; background: linear-gradient(90deg, #f59e0b 0%, #3b82f6 33%, #10b981 66%, #6b7280 100%); z-index: 2; width: calc(<?php echo (($currentIndex / 3) * 100); ?>% + 30px);"></div>
                        <?php endif; ?>

                        <!-- Status Steps -->
                        <?php foreach ($statuses as $index => $status): ?>
                            <?php
                            $isActive = $currentIndex >= $index;
                            $isCurrentStatus = $currentStatus === $status;
                            $statusColor = $statusColors[$status];
                            ?>
                            <div style="position: relative; z-index: 3; display: flex; flex-direction: column; align-items: center; flex: 1;">
                                <!-- Step Circle -->
                                <div style="
                                    width: 50px;
                                    height: 50px;
                                    border-radius: 50%;
                                    background: <?php echo $isActive ? $statusColor : '#e5e7eb'; ?>;
                                    border: 3px solid white;
                                    box-shadow: 0 0 0 2px <?php echo $isActive ? $statusColor : '#e5e7eb'; ?>;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    color: white;
                                    font-weight: bold;
                                    font-size: 20px;
                                    margin-bottom: 15px;
                                    transition: all 0.3s ease;
                                ">
                                    <?php if ($isActive && $index !== 0): ?>✓<?php else: ?><?php echo $index + 1; ?><?php endif; ?>
                                </div>
                                
                                <!-- Step Label -->
                                <span style="
                                    font-weight: 500;
                                    color: <?php echo $isCurrentStatus ? $statusColor : '#666'; ?>;
                                    font-size: 14px;
                                    text-align: center;
                                    margin-bottom: 5px;
                                ">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                                
                                <!-- Step Date/Time -->
                                <span style="
                                    font-size: 12px;
                                    color: #999;
                                    text-align: center;
                                ">
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
                    <div style="margin-top: 30px; padding: 15px; background: #f3f4f6; border-radius: 8px; border-left: 4px solid <?php echo $statusColors[$currentStatus]; ?>;">
                        <h4 style="margin: 0 0 10px 0; color: <?php echo $statusColors[$currentStatus]; ?>;">
                            Current Status: <?php echo htmlspecialchars($currentStatus); ?>
                        </h4>
                        <p style="margin: 5px 0 0 0; font-size: 14px; color: #555;">
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
                        <div style="margin-top: 20px; padding: 15px; background: #e8f4f8; border-radius: 8px;">
                            <p style="margin: 0; color: #0369a1; font-size: 14px;">
                                <strong>📦 Estimated Delivery:</strong> <?php echo date('F d, Y', strtotime($trackingResult['date'] . ' +5 days')); ?> to <?php echo date('F d, Y', strtotime($trackingResult['date'] . ' +7 days')); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Order Items -->
                <?php if (isset($trackingResult['items']) && count($trackingResult['items']) > 0): ?>
                    <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
                        <h3 style="margin-top: 0; margin-bottom: 15px;">Order Items</h3>
                        
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
                                <div style="display: flex; padding: 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px; align-items: center;">
                                    <div style="font-size: 40px; margin-right: 15px;">
                                        <?php echo $product['emoji']; ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 5px 0;">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </h4>
                                        <p style="margin: 0; color: #666; font-size: 14px;">
                                            Quantity: <?php echo $item['quantity']; ?>
                                        </p>
                                    </div>
                                    <div style="text-align: right;">
                                        <p style="margin: 0; font-weight: bold;">
                                            $<?php echo number_format(isset($item['itemTotal']) ? $item['itemTotal'] : ($item['quantity'] * $item['price']), 2); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Actions -->
                <div style="text-align: center; margin-top: 30px;">
                    <a href="track-order.php" class="btn btn-primary" style="display: inline-block; padding: 12px 30px; text-decoration: none; margin-right: 10px;">
                        Track Another Order
                    </a>
                    <a href="products.php" style="display: inline-block; padding: 12px 30px; color: #2563eb; text-decoration: none; border: 1px solid #2563eb; border-radius: 4px;">
                        Continue Shopping
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </section>

<?php include 'footer.php'; ?>
