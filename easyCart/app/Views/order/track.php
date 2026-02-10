<?php include TEMPLATES_PATH . '/header.php'; ?>

<!-- Track Order Page -->
<section class="container order-confirmation-section">
    <h1 class="section-title">Track Your Order</h1>

    <?php if (!$data['trackingResult']): ?>
        <!-- Tracking Form -->
        <div class="tracking-form-container">
            <div class="tracking-form-box">
                <div class="tracking-form-header">
                    <div class="tracking-icon">📦</div>
                    <h2 class="tracking-form-title">Track Your Package</h2>
                    <p class="tracking-form-desc">Enter your order details to see the current status</p>
                </div>

                <?php if ($data['trackingError']): ?>
                    <div class="error-alert">
                        ⚠️ <?php echo htmlspecialchars($data['trackingError']); ?>
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
                            <?php echo htmlspecialchars($data['trackingResult']['order_number']); ?>
                        </p>
                    </div>
                    <div>
                        <label class="info-label">Order Date</label>
                        <p class="info-value">
                            <?php echo date('M d, Y', strtotime($data['trackingResult']['date'])); ?>
                        </p>
                    </div>
                    <div>
                        <label class="info-label">Total Amount</label>
                        <p class="info-value info-value-total">
                            <?php echo formatPrice($data['trackingResult']['total']); ?>
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
                $currentStatus = $data['trackingResult']['status'];
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
                                        echo date('M d, Y', strtotime($data['trackingResult']['date']));
                                    } else {
                                        // Estimate dates for other stages
                                        $daysToAdd = $index * 2;
                                        echo date('M d, Y', strtotime($data['trackingResult']['date'] . " +{$daysToAdd} days"));
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
            </div>

            <!-- Order Items -->
            <?php if (isset($data['trackingResult']['items']) && count($data['trackingResult']['items']) > 0): ?>
                <div class="order-items-box">
                    <h3 class="info-card-title">Order Items</h3>
                    
                    <?php foreach ($data['trackingResult']['items'] as $item): ?>
                        <?php 
                        $product = $item['product'] ?? getProductById($item['product_id']);
                        ?>
                        <?php if ($product): ?>
                            <div class="item-row-track">
                                <div class="item-emoji">
                                    <?php echo $product['emoji'] ?? '📦'; ?>
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
                                        <?php echo formatPrice($item['itemTotal'] ?? ($item['quantity'] * ($product['price'] ?? 0))); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="tracking-footer" style="border-top: none; margin-top: 10px;">
                <a href="track-order" class="btn btn-primary btn-track-another">
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
