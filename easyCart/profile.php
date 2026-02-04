<?php
session_start();

// Load Application Bootstrap
require_once 'app/bootstrap.php';

$pageTitle = 'My Profile';

// Require login
requireLogin();

$currentUser = getCurrentUser();
$orders = getUserOrders($currentUser['id']) ?? [];
?>
<?php include TEMPLATES_PATH . '/header.php'; ?>

<style>
/* ========================================
   PROFILE PAGE - DEDICATED STYLES
   ======================================== */

.profile-section {
    padding: 40px 20px;
    max-width: 1200px;
    margin: 0 auto;
    min-height: calc(100vh - 200px);
}

.profile-title {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 40px;
    text-align: center;
    animation: fadeInDown 0.6s ease-out;
}

.profile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 40px;
}

/* Profile Card Styling */
.profile-card {
    background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
    border-radius: 20px;
    padding: 35px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.1);
    border: 1px solid rgba(102, 126, 234, 0.2);
    transition: all 0.3s ease;
    animation: fadeInLeft 0.6s ease-out;
}

.profile-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.2);
}

.profile-card-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid rgba(102, 126, 234, 0.2);
}

.profile-card-icon {
    font-size: 2.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.profile-card-title {
    font-size: 1.5rem;
    color: #333;
    font-weight: 600;
    margin: 0;
}

/* Profile Fields */
.profile-field {
    margin-bottom: 25px;
    padding: 15px;
    background: white;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.profile-field:hover {
    background: #f8f9ff;
    transform: translateX(5px);
}

.profile-field-label {
    display: block;
    font-size: 0.875rem;
    color: #667eea;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.profile-field-value {
    font-size: 1.125rem;
    color: #333;
    font-weight: 500;
    margin: 0;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.profile-divider {
    border: none;
    height: 2px;
    background: linear-gradient(90deg, transparent, #667eea, transparent);
    margin: 30px 0;
}

/* Quick Actions */
.quick-actions-header {
    font-size: 1.25rem;
    color: #333;
    margin-bottom: 20px;
    font-weight: 600;
}

.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 12px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.action-btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.action-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.action-btn-secondary {
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
}

.action-btn-secondary:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}

/* Stats Card */
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    box-shadow: 0 10px 30px rgba(79, 172, 254, 0.3);
    margin-bottom: 25px;
    animation: fadeInRight 0.6s ease-out;
}

.stats-card-header {
    font-size: 1.5rem;
    margin-bottom: 25px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.stats-item {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 15px;
    text-align: center;
    transition: all 0.3s ease;
}

.stats-item:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}

.stats-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.stats-label {
    font-size: 0.95rem;
    opacity: 0.95;
    font-weight: 500;
    margin: 0;
}

/* Chart Card */
.chart-card {
    background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
    border-radius: 20px;
    padding: 30px;
    margin-top: 20px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.1);
    border: 1px solid rgba(102, 126, 234, 0.2);
    animation: fadeInRight 0.7s ease-out;
}

.chart-card-header {
    font-size: 1.5rem;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.chart-card-subtitle {
    font-size: 0.95rem;
    color: #6b7280;
    margin-bottom: 25px;
}

.chart-container {
    position: relative;
    height: 300px;
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.empty-chart-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 15px;
}

/* Help Card */
.help-card {
    background: linear-gradient(135deg, #78b8f0ff 0%, #62ecf3ff 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    box-shadow: 0 10px 30px rgba(79, 172, 254, 0.3);
    animation: fadeInRight 0.7s ease-out;
}

.help-card-title {
    font-size: 1.5rem;
    margin-bottom: 15px;
    font-weight: 600;
}

.help-card-text {
    font-size: 1rem;
    margin-bottom: 20px;
    opacity: 0.95;
    line-height: 1.6;
}

.help-card-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: white;
    font-weight: 600;
    text-decoration: none;
    padding: 12px 24px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.help-card-link:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateX(5px);
}

/* Recent Orders Section */
.recent-orders-header {
    font-size: 2rem;
    color: #333;
    font-weight: 700;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.orders-container {
    background: white;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
    transition: all 0.3s ease;
}

.order-item:last-child {
    border-bottom: none;
}

.order-item:hover {
    background: #f8f9ff;
    border-radius: 12px;
}

.order-info {
    flex: 1;
}

.order-number {
    font-weight: 600;
    color: #333;
    font-size: 1.1rem;
    margin: 0 0 8px 0;
}

.order-date {
    color: #6b7280;
    font-size: 0.9rem;
    margin: 0;
}

.order-details {
    display: flex;
    align-items: center;
    gap: 20px;
}

.order-price {
    font-weight: 700;
    color: #667eea;
    font-size: 1.2rem;
    margin: 0;
}

.order-status {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: capitalize;
}

.status-processing {
    background: #fef3c7;
    color: #92400e;
}

.status-shipped {
    background: #dbeafe;
    color: #1e40af;
}

.status-delivered {
    background: #d1fae5;
    color: #065f46;
}

.view-all-link {
    text-align: center;
    margin-top: 20px;
}

.view-all-link a {
    color: #667eea;
    font-weight: 600;
    text-decoration: none;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.view-all-link a:hover {
    color: #764ba2;
    text-decoration: underline;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
    border-radius: 20px;
}

.empty-state-icon {
    font-size: 5rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.empty-state-text {
    font-size: 1.2rem;
    color: #6b7280;
    margin-bottom: 25px;
}

/* Animations */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .profile-title {
        font-size: 2rem;
    }
    
    .profile-card {
        padding: 25px;
    }
    
    .order-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .order-details {
        width: 100%;
        justify-content: space-between;
    }
}
</style>

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
                        <?php echo htmlspecialchars($currentUser['name']); ?>
                    </p>
                </div>

                <div class="profile-field">
                    <label class="profile-field-label">Email Address</label>
                    <p class="profile-field-value">
                        <?php echo htmlspecialchars($currentUser['email']); ?>
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
                    <a href="cart.php" class="action-btn action-btn-primary">
                        🛒 View My Cart
                    </a>
                    <a href="orders.php" class="action-btn action-btn-secondary">
                        📦 View My Orders
                    </a>
                    <a href="products.php" class="action-btn action-btn-secondary">
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
                            $totalOrders = count($orders);
                            echo $totalOrders;
                            ?>
                        </div>
                        <p class="stats-label">Total Orders</p>
                    </div>

                    <div class="stats-item">
                        <div class="stats-value">
                            <?php 
                            $totalSpent = 0;
                            foreach ($orders as $order) {
                                $totalSpent += $order['subtotal'];
                            }
                            echo formatPrice($totalSpent);
                            ?>
                        </div>
                        <p class="stats-label">Total Spent</p>
                    </div>

                    <div class="stats-item">
                        <div class="stats-value">
                            <?php 
                            $completedOrders = count(array_filter($orders, function($o) { return $o['status'] === 'Delivered'; }));
                            echo $completedOrders;
                            ?>
                        </div>
                        <p class="stats-label">Completed Orders</p>
                    </div>
                </div>

                <!-- Order Analytics Chart -->
                <div class="chart-card">
                    <h3 class="chart-card-header">📈 Order Analytics</h3>
                    <p class="chart-card-subtitle">Your spending trends over time</p>
                    
                    <?php if (count($orders) > 0): ?>
                        <div class="chart-container">
                            <canvas id="orderChart"></canvas>
                        </div>
                    <?php else: ?>
                        <div class="empty-chart-state">
                            <div style="font-size: 3rem; margin-bottom: 10px; opacity: 0.5;">📊</div>
                            <p style="color: #6b7280; margin: 0;">No order data available yet</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Help Card -->
                <div class="help-card">
                    <h4 class="help-card-title">Need Help?</h4>
                    <p class="help-card-text">
                        Visit our customer support for any questions or assistance.
                    </p>
                    <a href="index.php#contact" class="help-card-link">
                        Contact Support →
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Orders Preview -->
        <section style="margin-top: 40px;">
            <h2 class="recent-orders-header">Recent Orders</h2>
            <?php
            $recentOrders = array_slice($orders, 0, 3, true);
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
                                <span class="order-status <?php echo 'status-' . strtolower($order['status']); ?>">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="view-all-link">
                    <a href="orders.php">
                        View All Orders →
                    </a>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <p class="empty-state-text">No orders yet. Start shopping to see your orders here!</p>
                    <a href="products.php" class="action-btn action-btn-primary" style="display: inline-flex;">
                        🛍️ Start Shopping
                    </a>
                </div>
            <?php endif; ?>
        </section>

        <!-- Chart.js Library -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        
        <!-- Order Analytics Chart Script -->
        <script>
        <?php if (count($orders) > 0): ?>
            // Prepare chart data from PHP backend
            const orderData = <?php 
                // Sort orders by date
                usort($orders, function($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                });
                
                // Prepare data for chart
                $chartData = array_map(function($order) {
                    return [
                        'date' => date('M d, Y', strtotime($order['date'])),
                        'amount' => floatval($order['subtotal'] + ($order['tax'] ?? 0) + ($order['shipping'] ?? 0)),
                        'orderNumber' => $order['order_number']
                    ];
                }, $orders);
                
                echo json_encode($chartData);
            ?>;

            // Extract labels and data
            const labels = orderData.map(item => item.date);
            const amounts = orderData.map(item => item.amount);
            const orderNumbers = orderData.map(item => item.orderNumber);

            // Create gradient
            const ctx = document.getElementById('orderChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(102, 126, 234, 0.8)');
            gradient.addColorStop(1, 'rgba(118, 75, 162, 0.2)');

            // Initialize Chart
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Order Amount ($)',
                        data: amounts,
                        backgroundColor: gradient,
                        borderColor: '#667eea',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#667eea',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#764ba2',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14,
                                    weight: '600'
                                },
                                color: '#333',
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 12,
                            borderColor: '#667eea',
                            borderWidth: 1,
                            displayColors: false,
                            callbacks: {
                                title: function(context) {
                                    return orderNumbers[context[0].dataIndex];
                                },
                                label: function(context) {
                                    return 'Amount: $' + context.parsed.y.toFixed(2);
                                },
                                afterLabel: function(context) {
                                    return 'Date: ' + labels[context.dataIndex];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toFixed(0);
                                },
                                font: {
                                    size: 12
                                },
                                color: '#6b7280'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: '#6b7280',
                                maxRotation: 45,
                                minRotation: 45
                            },
                            grid: {
                                display: false,
                                drawBorder: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        <?php endif; ?>
        </script>
    </section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
