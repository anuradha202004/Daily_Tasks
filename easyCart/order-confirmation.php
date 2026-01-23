<?php
session_start();

$pageTitle = 'Order Confirmation';

// Get last order from session
$lastOrder = isset($_SESSION['last_order']) ? $_SESSION['last_order'] : null;

// If no order, redirect to products
if (!$lastOrder) {
    header('Location: products.php');
    exit;
}

// Handle cancel order request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_order') {
    // Set order status to cancelled
    $_SESSION['last_order']['status'] = 'Cancelled';
    $_SESSION['last_order']['cancelled_date'] = date('Y-m-d H:i:s');
    $cancellationMessage = 'Order has been cancelled successfully. You will receive a refund within 5-7 business days.';
}

// Initialize order status if not exists
if (!isset($_SESSION['last_order']['status'])) {
    $_SESSION['last_order']['status'] = 'Processing';
    $_SESSION['last_order']['date'] = date('Y-m-d H:i:s');
    $_SESSION['last_order']['order_number'] = 'ORD' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
}
?>
<?php include 'header.php'; ?>

    <!-- Order Confirmation Page -->
    <section class="container" style="padding: 40px 0;">
        <div style="text-align: center; margin-bottom: 40px;">
            <div style="font-size: 60px; margin-bottom: 20px; color: #28a745;">✓</div>
            <h1 style="margin-bottom: 10px;">Order Confirmed!</h1>
            <p style="color: #666; font-size: 18px;">Thank you for your purchase</p>
            <p style="color: #999; font-size: 14px;">Order Number: <strong><?php echo htmlspecialchars($_SESSION['last_order']['order_number']); ?></strong></p>
        </div>

        <?php if (isset($cancellationMessage)): ?>
            <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 30px; text-align: center;">
                ✓ <?php echo htmlspecialchars($cancellationMessage); ?>
            </div>
        <?php endif; ?>

        <!-- Order Tracking Timeline -->
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
            $currentStatus = $_SESSION['last_order']['status'];
            $currentIndex = array_search($currentStatus, $statuses);
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
                                    echo date('M d, Y', strtotime($_SESSION['last_order']['date']));
                                } else {
                                    // Estimate dates for other stages
                                    $daysToAdd = $index * 2;
                                    echo date('M d, Y', strtotime($_SESSION['last_order']['date'] . " +{$daysToAdd} days"));
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
                        <strong>📦 Estimated Delivery:</strong> <?php echo date('F d, Y', strtotime($_SESSION['last_order']['date'] . ' +5 days')); ?> to <?php echo date('F d, Y', strtotime($_SESSION['last_order']['date'] . ' +7 days')); ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Cancel Order Button -->
            <?php if ($currentStatus === 'Processing'): ?>
                <div style="margin-top: 20px;">
                    <button onclick="openCancelModal()" style="
                        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                        color: white;
                        border: none;
                        padding: 12px 24px;
                        border-radius: 8px;
                        cursor: pointer;
                        font-weight: 500;
                        font-size: 14px;
                        transition: transform 0.2s ease;
                    " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        ❌ Cancel Order
                    </button>
                </div>
            <?php endif; ?>

            <!-- Order Status Simulation (Demo Feature) -->
            <?php if ($currentStatus !== 'Cancelled' && $currentStatus !== 'Completed'): ?>
                <div style="margin-top: 30px; padding: 20px; background: #f0f4f8; border-radius: 8px; border: 2px dashed #cbd5e1;">
                    <h4 style="margin: 0 0 15px 0; color: #475569; font-size: 14px;">
                        🎮 Demo Controls - Simulate Order Progress
                    </h4>
                    <p style="margin: 0 0 15px 0; font-size: 12px; color: #64748b;">
                        Use these buttons to simulate order status changes for demonstration purposes.
                    </p>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <?php if ($currentStatus === 'Processing'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="simulate_status">
                                <input type="hidden" name="new_status" value="Shipped">
                                <button type="submit" style="
                                    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                                    color: white;
                                    border: none;
                                    padding: 10px 20px;
                                    border-radius: 6px;
                                    cursor: pointer;
                                    font-weight: 500;
                                    font-size: 13px;
                                    transition: transform 0.2s ease;
                                " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    📦 Ship Order
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($currentStatus === 'Shipped'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="simulate_status">
                                <input type="hidden" name="new_status" value="Delivered">
                                <button type="submit" style="
                                    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                                    color: white;
                                    border: none;
                                    padding: 10px 20px;
                                    border-radius: 6px;
                                    cursor: pointer;
                                    font-weight: 500;
                                    font-size: 13px;
                                    transition: transform 0.2s ease;
                                " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    ✓ Mark as Delivered
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($currentStatus === 'Delivered'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="simulate_status">
                                <input type="hidden" name="new_status" value="Completed">
                                <button type="submit" style="
                                    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
                                    color: white;
                                    border: none;
                                    padding: 10px 20px;
                                    border-radius: 6px;
                                    cursor: pointer;
                                    font-weight: 500;
                                    font-size: 13px;
                                    transition: transform 0.2s ease;
                                " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    ✓ Complete Order
                                </button>
                            </form>
                        <?php endif; ?>

                        <!-- Reset to Processing -->
                        <?php if ($currentStatus !== 'Processing'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="simulate_status">
                                <input type="hidden" name="new_status" value="Processing">
                                <button type="submit" style="
                                    background: #e5e7eb;
                                    color: #374151;
                                    border: 1px solid #d1d5db;
                                    padding: 10px 20px;
                                    border-radius: 6px;
                                    cursor: pointer;
                                    font-weight: 500;
                                    font-size: 13px;
                                    transition: transform 0.2s ease;
                                " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    🔄 Reset to Processing
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 40px;">
            <!-- Order Details -->
            <div>
                <!-- Customer Info -->
                <div style="background: #fff; padding: 25px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin-top: 0; margin-bottom: 15px;">Shipping To</h3>
                    <p style="margin: 5px 0; font-weight: 500;">
                        <?php echo htmlspecialchars($lastOrder['customer']['first_name']) . ' ' . htmlspecialchars($lastOrder['customer']['last_name']); ?>
                    </p>
                    <p style="margin: 5px 0; color: #666;">
                        <?php echo htmlspecialchars($lastOrder['customer']['address']); ?><br>
                        <?php echo htmlspecialchars($lastOrder['customer']['city']) . ', ' . htmlspecialchars($lastOrder['customer']['state']) . ' ' . htmlspecialchars($lastOrder['customer']['zip']); ?>
                    </p>
                    <p style="margin: 5px 0; color: #666;">
                        Email: <?php echo htmlspecialchars($lastOrder['customer']['email']); ?><br>
                        Phone: <?php echo htmlspecialchars($lastOrder['customer']['phone']); ?>
                    </p>
                </div>

                <!-- Ordered Items -->
                <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin-top: 0; margin-bottom: 15px;">Ordered Items</h3>
                    
                    <?php foreach ($lastOrder['items'] as $item): ?>
                        <div style="display: flex; padding: 15px 0; border-bottom: 1px solid #eee; align-items: center;">
                            <div style="font-size: 40px; margin-right: 15px;">
                                <?php echo $item['product']['emoji']; ?>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 5px 0;">
                                    <?php echo htmlspecialchars($item['product']['name']); ?>
                                </h4>
                                <p style="margin: 0; color: #666; font-size: 14px;">
                                    Quantity: <?php echo $item['quantity']; ?>
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <p style="margin: 0; font-weight: bold;">
                                    $<?php echo number_format($item['itemTotal'], 2); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div style="background: #f8f9fa; padding: 25px; border-radius: 8px; border: 2px solid #dee2e6;">
                    <h3 style="margin-top: 0; margin-bottom: 20px;">Order Summary</h3>

                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Subtotal</span>
                            <span>$<?php echo number_format($lastOrder['subtotal'], 2); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Tax (10%)</span>
                            <span>$<?php echo number_format($lastOrder['tax'], 2); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                            <span>Shipping</span>
                            <span><?php echo $lastOrder['subtotal'] > 50 ? 'Free' : '$9.99'; ?></span>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; padding: 15px 0; border-top: 2px solid #dee2e6; border-bottom: 2px solid #dee2e6;">
                        <span>Total</span>
                        <span style="color: #d32f2f;">$<?php echo number_format($lastOrder['total'], 2); ?></span>
                    </div>

                    <div style="margin-top: 20px; padding: 15px; background: #d4edda; border-radius: 8px;">
                        <p style="margin: 0; color: #155724; font-weight: 500; text-align: center;">
                            ✓ Order Placed Successfully
                        </p>
                        <p style="margin: 5px 0 0 0; color: #155724; font-size: 12px; text-align: center;">
                            Order placed on <?php echo date('F d, Y \a\t h:i A', strtotime($lastOrder['date'])); ?>
                        </p>
                    </div>

                    <!-- Next Steps -->
                    <div style="margin-top: 20px;">
                        <h4 style="margin-bottom: 10px;">What's Next?</h4>
                        <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #666;">
                            <li>Confirmation email sent to your inbox</li>
                            <li>Order processing within 24 hours</li>
                            <li>Tracking info will be emailed</li>
                            <li>Estimated delivery: 5-7 business days</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div style="margin-top: 20px;">
                        <a href="orders.php" class="btn btn-primary" style="display: block; text-align: center; padding: 12px; text-decoration: none; margin-bottom: 10px;">
                            View My Orders
                        </a>
                        <a href="products.php" style="display: block; text-align: center; padding: 12px; color: #2563eb; text-decoration: none; border: 1px solid #2563eb; border-radius: 4px;">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Section -->
        <div style="background: #e8f4f8; padding: 20px; border-radius: 8px; margin-top: 40px;">
            <h3 style="margin-top: 0;">Need Help?</h3>
            <p style="margin: 10px 0; color: #555;">
                For any questions about your order, please contact our customer support:
            </p>
            <p style="margin: 5px 0; color: #555;">
                📧 Email: <strong>support@easycart.com</strong><br>
                📞 Phone: <strong>+1 (555) 123-4567</strong><br>
                ⏰ Available: Monday - Friday, 9:00 AM - 6:00 PM
            </p>
        </div>
    </section>

    <!-- Cancel Order Modal -->
    <div id="cancelModal" style="
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    ">
        <div style="
            background: white;
            border-radius: 12px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        ">
            <h2 style="margin: 0 0 15px 0; color: #333;">Cancel Order?</h2>
            <p style="color: #666; margin: 0 0 20px 0; line-height: 1.6;">
                Are you sure you want to cancel this order? This action cannot be undone. Your refund will be processed within 5-7 business days.
            </p>
            
            <form method="POST" style="display: flex; gap: 10px;">
                <input type="hidden" name="action" value="cancel_order">
                <button type="submit" style="
                    flex: 1;
                    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                    color: white;
                    border: none;
                    padding: 12px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: 500;
                    font-size: 14px;
                    transition: transform 0.2s ease;
                " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    Yes, Cancel Order
                </button>
                <button type="button" onclick="closeCancelModal()" style="
                    flex: 1;
                    background: #e5e7eb;
                    color: #333;
                    border: none;
                    padding: 12px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: 500;
                    font-size: 14px;
                    transition: transform 0.2s ease;
                " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    No, Keep Order
                </button>
            </form>
        </div>
    </div>

    <script>
        function openCancelModal() {
            document.getElementById('cancelModal').style.display = 'flex';
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('cancelModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeCancelModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeCancelModal();
            }
        });
    </script>

<?php include 'footer.php'; ?>
