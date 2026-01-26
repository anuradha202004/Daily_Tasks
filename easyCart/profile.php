<?php
session_start();

require_once 'includes/auth.php';
require_once 'includes/data.php';

$pageTitle = 'My Profile';

// Require login
requireLogin();

$currentUser = getCurrentUser();
?>
<?php include 'includes/header.php'; ?>

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
    </section>

<?php include 'includes/footer.php'; ?>
