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

    <!-- Profile Page -->
    <section class="container" style="padding: 40px 0;">
        <h1 class="section-title">My Profile</h1>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 40px;">
            <!-- Profile Information -->
            <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0; margin-bottom: 25px;">Account Information</h2>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: #999; font-size: 12px; text-transform: uppercase; margin-bottom: 5px;">Full Name</label>
                    <p style="margin: 0; font-size: 16px; color: #333; font-weight: 500;">
                        <?php echo htmlspecialchars($currentUser['name']); ?>
                    </p>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: #999; font-size: 12px; text-transform: uppercase; margin-bottom: 5px;">Email Address</label>
                    <p style="margin: 0; font-size: 16px; color: #333; font-weight: 500;">
                        <?php echo htmlspecialchars($currentUser['email']); ?>
                    </p>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: #999; font-size: 12px; text-transform: uppercase; margin-bottom: 5px;">Member Since</label>
                    <p style="margin: 0; font-size: 16px; color: #333; font-weight: 500;">
                        January 22, 2026
                    </p>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: #999; font-size: 12px; text-transform: uppercase; margin-bottom: 5px;">Account Status</label>
                    <p style="margin: 0; font-size: 16px;">
                        <span style="display: inline-block; background: #d4edda; color: #155724; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                            ✓ Active
                        </span>
                    </p>
                </div>

                <hr style="margin: 25px 0;">

                <div style="margin-bottom: 25px;">
                    <h3 style="margin-bottom: 15px; color: #2563eb;">Quick Actions</h3>
                    <a href="cart.php" class="btn btn-primary" style="display: block; text-align: center; padding: 12px; text-decoration: none; margin-bottom: 10px;">
                        View My Cart
                    </a>
                    <a href="orders.php" class="btn" style="display: block; text-align: center; padding: 12px; text-decoration: none; margin-bottom: 10px; color: #2563eb; border: 1px solid #2563eb; border-radius: 5px;">
                        View My Orders
                    </a>
                    <a href="products.php" class="btn" style="display: block; text-align: center; padding: 12px; text-decoration: none; color: #2563eb; border: 1px solid #2563eb; border-radius: 5px;">
                        Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Account Summary -->
            <div>
                <!-- Statistics Card -->
                <div style="background: #f0f4f8; padding: 25px; border-radius: 8px; margin-bottom: 20px;">
                    <h3 style="margin-top: 0; margin-bottom: 20px; color: #2563eb;">Shopping Stats</h3>
                    
                    <div style="margin-bottom: 20px;">
                        <div style="font-size: 28px; font-weight: bold; color: #2563eb;">
                            <?php 
                            $totalOrders = count($orders);
                            echo $totalOrders;
                            ?>
                        </div>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Total Orders</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <div style="font-size: 28px; font-weight: bold; color: #d32f2f;">
                            <?php 
                            $totalSpent = 0;
                            foreach ($orders as $order) {
                                $totalSpent += $order['subtotal'];
                            }
                            echo formatPrice($totalSpent);
                            ?>
                        </div>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Total Spent</p>
                    </div>

                    <div>
                        <div style="font-size: 28px; font-weight: bold; color: #28a745;">
                            <?php 
                            $completedOrders = count(array_filter($orders, function($o) { return $o['status'] === 'Delivered'; }));
                            echo $completedOrders;
                            ?>
                        </div>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Completed Orders</p>
                    </div>
                </div>

                <!-- Help Card -->
                <div style="background: #e8f4f8; padding: 20px; border-radius: 8px; border-left: 4px solid #2563eb;">
                    <h4 style="margin-top: 0; margin-bottom: 10px;">Need Help?</h4>
                    <p style="margin: 0 0 15px 0; font-size: 14px; color: #555;">
                        Visit our customer support for any questions.
                    </p>
                    <a href="#" style="color: #2563eb; text-decoration: none; font-weight: 500; font-size: 14px;">
                        Contact Support →
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Orders Preview -->
        <section style="margin-top: 40px;">
            <h2 style="margin-bottom: 20px;">Recent Orders</h2>
            <?php
            $recentOrders = array_slice($orders, 0, 3, true);
            if (count($recentOrders) > 0):
            ?>
                <div style="background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <?php foreach ($recentOrders as $order): ?>
                        <div style="padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <p style="margin: 0 0 5px 0; font-weight: bold;">
                                    <?php echo htmlspecialchars($order['order_number']); ?>
                                </p>
                                <p style="margin: 0; color: #666; font-size: 14px;">
                                    <?php echo date('F d, Y', strtotime($order['date'])); ?>
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <p style="margin: 0; font-weight: bold;">
                                    <?php echo formatPrice($order['subtotal']); ?>
                                </p>
                                <p style="margin: 5px 0 0 0;">
                                    <span style="background: <?php echo $order['status'] === 'Delivered' ? '#d4edda' : '#fff3cd'; ?>; color: <?php echo $order['status'] === 'Delivered' ? '#155724' : '#856404'; ?>; padding: 4px 8px; border-radius: 12px; font-size: 12px;">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div style="text-align: center; padding: 20px;">
                    <a href="orders.php" style="color: #2563eb; text-decoration: none; font-weight: 500;">
                        View All Orders →
                    </a>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px 20px; background: #f8f9fa; border-radius: 8px;">
                    <p style="color: #666; margin-bottom: 20px;">No orders yet</p>
                    <a href="products.php" class="btn btn-primary" style="display: inline-block; text-decoration: none;">
                        Start Shopping
                    </a>
                </div>
            <?php endif; ?>
        </section>
    </section>

<?php include 'includes/footer.php'; ?>
