<?php include TEMPLATES_PATH . '/header.php'; ?>

<!-- My Orders Page -->
<section class="container orders-section">
    <h1 class="section-title">My Orders</h1>

    <?php if (isset($data['flash']['message'])): ?>
        <div class="flash-message <?php echo $data['flash']['type'] === 'success' ? 'flash-success' : 'flash-error'; ?>">
            <?php echo htmlspecialchars($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <?php if (count($data['orders']) > 0): ?>
        <div>
            <?php foreach ($data['orders'] as $order): ?>
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
                                $orderStatus = $order['status'] ?? 'Processing';
                                $statusColor = $statusColors[$orderStatus] ?? ['bg' => '#e5e7eb', 'color' => '#374151'];
                                ?>
                                <span class="order-status-badge" style="background: <?php echo $statusColor['bg']; ?>; color: <?php echo $statusColor['color']; ?>;">
                                    <?php echo htmlspecialchars($orderStatus); ?>
                                </span>
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <label class="order-label">Total</label>
                            <p class="order-total-value">
                                <?php 
                                    $finalTotal = isset($order['total']) ? $order['total'] : ($order['subtotal'] + ($order['tax'] ?? 0) + ($order['shipping'] ?? 0));
                                    echo formatPrice($finalTotal); 
                                ?>
                            </p>
                        </div>
                    </div>

                    <!-- Mini Timeline for Tracking -->
                    <div class="mini-timeline-box">
                        <?php
                        $statuses = ['Processing', 'Shipped', 'Delivered', 'Completed'];
                        $currentStatus = $order['status'] ?? 'Processing';
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
                                <?php 
                                    $product = $item['product'] ?? getProductById($item['product_id'] ?? null); 
                                ?>
                                <?php if ($product): ?>
                                    <div class="order-item-row">
                                        <div class="order-item-emoji">
                                            <?php echo $product['emoji'] ?? '📦'; ?>
                                        </div>
                                        <div class="order-item-details">
                                            <h5 class="order-item-title">
                                                <?php echo htmlspecialchars($product['name']); ?>
                                            </h5>
                                            <p class="order-item-meta">
                                                Quantity: <strong><?php echo $item['quantity']; ?></strong> × <strong><?php echo formatPrice($product['price'] ?? 0); ?></strong>
                                            </p>
                                        </div>
                                        <div style="text-align: right;">
                                            <p class="order-item-total">
                                                <?php echo formatPrice($item['itemTotal'] ?? ($item['quantity'] * ($product['price'] ?? 0))); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Order Summary and Actions Row -->
                    <div class="order-actions-row">
                        <div>
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
