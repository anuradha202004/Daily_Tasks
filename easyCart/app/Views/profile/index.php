<?php include TEMPLATES_PATH . '/header.php'; ?>

<!-- Profile Page - Modern Design -->
<section class="container profile-section">
    <h1 class="profile-title">👤 My Profile</h1>

    <div class="profile-grid">
        <!-- Profile Information -->
        <div class="profile-card">
            <div class="profile-card-header">
                <div class="profile-card-icon">👤</div>
                <h2 class="profile-card-title">Account Information</h2>
            </div>

            <div class="profile-field">
                <label class="profile-field-label">Full Name</label>
                <p class="profile-field-value">
                    <?php echo htmlspecialchars($data['user']['name']); ?>
                </p>
            </div>

            <div class="profile-field">
                <label class="profile-field-label">Email Address</label>
                <p class="profile-field-value">
                    <?php echo htmlspecialchars($data['user']['email']); ?>
                </p>
            </div>

            <div class="profile-field">
                <label class="profile-field-label">Member Since</label>
                <p class="profile-field-value">
                    January 22, 2026
                </p>
            </div>

            <div class="profile-field">
                <label class="profile-field-label">Account Status</label>
                <p class="profile-field-value">
                    <span class="status-badge">
                        ✓ Active
                    </span>
                </p>
            </div>

            <hr class="profile-divider">

            <div>
                <h3 class="quick-actions-header">Quick Actions</h3>
                <a href="cart" class="action-btn action-btn-primary">
                    🛒 View My Cart
                </a>
                <a href="orders" class="action-btn action-btn-secondary">
                    📦 View My Orders
                </a>
                <a href="products" class="action-btn action-btn-secondary">
                    🛍️ Continue Shopping
                </a>
            </div>
        </div>

        <!-- Account Summary -->
        <div>
            <!-- Statistics Card -->
            <div class="stats-card">
                <h3 class="stats-card-header">📊 Shopping Stats</h3>
                
                <div class="stats-item">
                    <div class="stats-value">
                        <?php 
                        $activeOrders = array_filter($data['orders'], function($o) {
                            return isset($o['status']) && $o['status'] !== 'Cancelled';
                        });
                        echo count($activeOrders);
                        ?>
                    </div>
                    <p class="stats-label">Total Orders</p>
                </div>

                <div class="stats-item">
                    <div class="stats-value">
                        <?php 
                        $totalSpent = 0;
                        foreach ($data['orders'] as $order) {
                            if (isset($order['status']) && $order['status'] === 'Cancelled') {
                                continue;
                            }
                            if (isset($order['total'])) {
                                $totalSpent += $order['total'];
                            } else {
                                $totalSpent += $order['subtotal'] + ($order['tax'] ?? 0) + ($order['shipping'] ?? 0);
                            }
                        }
                        echo formatPrice($totalSpent);
                        ?>
                    </div>
                    <p class="stats-label">Total Spent</p>
                </div>

                <div class="stats-item">
                    <div class="stats-value">
                        <?php 
                        $completedOrders = count(array_filter($data['orders'], function($o) { return isset($o['status']) && $o['status'] === 'Delivered'; }));
                        echo $completedOrders;
                        ?>
                    </div>
                    <p class="stats-label">Completed Orders</p>
                </div>
            </div>

            <!-- Help Card -->
            <div class="help-card mt-25">
                <h4 class="help-card-title">Need Help?</h4>
                <p class="help-card-text">
                    Visit our customer support for any questions or assistance.
                </p>
                <a href="index#contact" class="help-card-link">
                    Contact Support →
                </a>
            </div>
        </div>
    </div>

    <!-- Order Analytics Chart (Full Width) -->
    <div class="chart-card mb-40">
        <div class="flex-between-start">
            <div>
                <h3 class="chart-card-header">📈 Order Analysis</h3>
                <p class="chart-card-subtitle">Comprehensive view of your orders, including cancellations.</p>
            </div>
            <div class="flex-gap-15">
                <div class="flex-align-center-gap-5">
                    <span class="dot-green"></span>
                    <span class="font-small-555">Successful</span>
                </div>
                <div class="flex-align-center-gap-5">
                    <span class="dot-red"></span>
                    <span class="font-small-555">Cancelled</span>
                </div>
            </div>
        </div>
        
        <?php if (count($data['orders']) > 0): ?>
            <div class="chart-container h-400">
                <canvas id="orderChart"></canvas>
            </div>
        <?php else: ?>
            <div class="empty-chart-state">
                <div class="emoji-3rem-opacity">📊</div>
                <p class="text-gray-600-m0">No order data available yet</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Orders Preview -->
    <section class="mt-40">
        <h2 class="recent-orders-header">Recent Orders</h2>
        <?php
        $recentOrders = array_slice($data['orders'], 0, 3, true);
        if (count($recentOrders) > 0):
        ?>
            <div class="orders-container">
                <?php foreach ($recentOrders as $order): ?>
                    <div class="order-item">
                        <div class="order-info">
                            <p class="order-number">
                                <?php echo htmlspecialchars($order['order_number']); ?>
                            </p>
                            <p class="order-date">
                                <?php echo date('F d, Y', strtotime($order['date'])); ?>
                            </p>
                        </div>
                        <div class="order-details">
                            <p class="order-price">
                                <?php echo formatPrice($order['subtotal']); ?>
                            </p>
                            <span class="order-status <?php echo 'status-' . strtolower($order['status'] ?? 'processing'); ?>">
                                <?php echo htmlspecialchars($order['status'] ?? 'Processing'); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="view-all-link">
                <a href="orders">
                    View All Orders →
                </a>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <p class="empty-state-text">No orders yet. Start shopping to see your orders here!</p>
                <a href="products" class="action-btn action-btn-primary inline-flex">
                    🛍️ Start Shopping
                </a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    
    <!-- Order Analytics Chart Script -->
    <script>
    <?php if (count($data['orders']) > 0): ?>
        // Prepare chart data from PHP backend
        const orderData = <?php 
            // Sort orders by date
            $chartOrders = $data['orders']; // Create copy to avoid modifying original info
            usort($chartOrders, function($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            
            // Prepare data for chart
            $chartData = array_map(function($order) {
                return [
                    'date' => date('M d', strtotime($order['date'])),
                    'amount' => floatval($order['subtotal'] + ($order['tax'] ?? 0) + ($order['shipping'] ?? 0)),
                    'status' => $order['status'] ?? 'Completed',
                    'orderNumber' => $order['order_number']
                ];
            }, $chartOrders);
            
            echo json_encode($chartData);
        ?>;

        // Prepare chart data
        const orderLabels = orderData.map(item => item.date);
        const successAmounts = orderData.map(item => {
            if (item.status === 'Cancelled') return 0;
            return item.amount;
        });
        const cancelledAmounts = orderData.map(item => {
            if (item.status === 'Cancelled') return item.amount;
            return 0;
        });
        
        const ctx = document.getElementById('orderChart').getContext('2d');
        
        // Gradients
        const gradientSuccess = ctx.createLinearGradient(0, 0, 0, 400);
        gradientSuccess.addColorStop(0, 'rgba(16, 185, 129, 0.7)');
        gradientSuccess.addColorStop(1, 'rgba(16, 185, 129, 0.1)');

        const gradientCancel = ctx.createLinearGradient(0, 0, 0, 400);
        gradientCancel.addColorStop(0, 'rgba(239, 68, 68, 0.7)');
        gradientCancel.addColorStop(1, 'rgba(239, 68, 68, 0.1)');

        new Chart(ctx, {
            type: 'bar', // Switch to bar for better comparison
            data: {
                labels: orderLabels,
                datasets: [
                    {
                        label: 'Successful Orders',
                        data: successAmounts,
                        backgroundColor: gradientSuccess,
                        borderColor: '#10b981',
                        borderWidth: 2,
                        borderRadius: 6,
                        barPercentage: 0.6,
                    },
                    {
                        label: 'Cancelled Orders',
                        data: cancelledAmounts,
                        backgroundColor: gradientCancel,
                        borderColor: '#ef4444',
                        borderWidth: 2,
                        borderRadius: 6,
                        barPercentage: 0.6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false // Custom legend used
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#1f2937',
                        bodyColor: '#4b5563',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            },
                            font: {
                                family: "'Inter', sans-serif"
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: "'Inter', sans-serif"
                            }
                        }
                    }
                }
            }
        });

    <?php endif; ?>
    </script>
</section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
