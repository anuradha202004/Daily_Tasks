<?php
require_once 'app/bootstrap.php';

// Check if user is logged in
if (!isLoggedIn()) {
    requireLogin();
}

$currentUser = getCurrentUser();
$order = null;

// Case 1: Fetch by Order Number (from GET)
if (isset($_GET['order_number'])) {
    $orderNumber = $_GET['order_number'];
    // In a real app, you'd fetch from DB. For now, check if it matches session or user history.
    if (isset($_SESSION['last_order']) && $_SESSION['last_order']['order_number'] === $orderNumber) {
        $order = $_SESSION['last_order'];
    } else {
        // Fallback: Check user orders (from file/DB logic if implemented fully)
        // For this demo, we can only safely print the SESSION last order, or mock data if it matches.
        // Assuming we have a getUserOrder($id) function, but orders.php uses getUserOrders().
        $allOrders = getUserOrders($currentUser['id']);
        foreach ($allOrders as $o) {
            if ($o['order_number'] === $orderNumber) {
                $order = $o;
                break;
            }
        }
    }
} 
// Case 2: Latest Order (from Session)
elseif (isset($_GET['latest'])) {
    $order = isset($_SESSION['last_order']) ? $_SESSION['last_order'] : null;
}

// Redirect if no order found
// Redirect if no order found
if (!$order) {
    die("Order not found or access denied.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo htmlspecialchars($order['order_number']); ?> - EasyCart</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <!-- Page Styles Moved to style.css -->
</head>
<body class="invoice-page">
    <div style="text-align: center;">
        <button onclick="window.print()" class="print-btn">🖨️ Print Invoice</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="logo">🛒 EasyCart</div>
            <div class="invoice-info">
                <h1>INVOICE</h1>
                <p>#<?php echo htmlspecialchars($order['order_number']); ?></p>
                <p>Date: <?php echo date('F d, Y', strtotime($order['date'])); ?></p>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <h3>Billed To</h3>
                <?php if (isset($order['billing_customer'])): ?>
                    <p><?php echo htmlspecialchars($order['billing_customer']['first_name'] . ' ' . $order['billing_customer']['last_name']); ?></p>
                    <p class="sub-text"><?php echo htmlspecialchars($order['billing_customer']['address']); ?></p>
                    <p class="sub-text"><?php echo htmlspecialchars($order['billing_customer']['city'] . ', ' . $order['billing_customer']['state'] . ' ' . $order['billing_customer']['zip']); ?></p>
                    <p class="sub-text"><?php echo htmlspecialchars($order['billing_customer']['email']); ?></p>
                <?php else: ?>
                    <!-- Fallback if old order without billing data -->
                    <p><?php echo htmlspecialchars($order['customer']['first_name'] . ' ' . $order['customer']['last_name']); ?></p>
                    <p class="sub-text"><?php echo htmlspecialchars($order['customer']['address']); ?></p>
                    <p class="sub-text"><?php echo htmlspecialchars($order['customer']['city'] . ', ' . $order['customer']['state'] . ' ' . $order['customer']['zip']); ?></p>
                    <p class="sub-text"><?php echo htmlspecialchars($order['customer']['email']); ?></p>
                    <p class="sub-text" style="color: #999; margin-top: 5px; font-size: 12px;">(Same as shipping)</p>
                <?php endif; ?>
            </div>
            <div class="column">
                <h3>Shipped To</h3>
                <p><?php echo htmlspecialchars($order['customer']['first_name'] . ' ' . $order['customer']['last_name']); ?></p>
                <p class="sub-text"><?php echo htmlspecialchars($order['customer']['address']); ?></p>
                <p class="sub-text"><?php echo htmlspecialchars($order['customer']['city'] . ', ' . $order['customer']['state'] . ' ' . $order['customer']['zip']); ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Item</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): 
                    // Handle both Session structure (nested product) and DB structure (flat)
                    $productName = $item['product']['name'] ?? $item['name'] ?? 'Unknown Product';
                    
                    // Retrieve quantities and totals first
                    $qty = isset($item['quantity']) ? (int)$item['quantity'] : (isset($item['qty_ordered']) ? (int)$item['qty_ordered'] : 0);
                    $itemTotal = isset($item['itemTotal']) ? (float)$item['itemTotal'] : (isset($item['row_total']) ? (float)$item['row_total'] : 0);
                    
                    // Try to find explicit price
                    $productPrice = isset($item['product']['price']) ? (float)$item['product']['price'] : (isset($item['price']) ? (float)$item['price'] : 0);
                    
                    // Fallback: Calculate unit price from total if explicit price is missing or zero
                    if ($productPrice <= 0 && $qty > 0) {
                        $productPrice = $itemTotal / $qty;
                    }
                ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($productName); ?></strong>
                    </td>
                    <td style="text-align: center;"><?php echo $qty; ?></td>
                    <td style="text-align: right;">$<?php echo number_format($productPrice, 2); ?></td>
                    <td style="text-align: right;">$<?php echo number_format($itemTotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            <table class="total-table">
                <tr>
                    <td>Subtotal:</td>
                    <td>$<?php echo number_format($order['subtotal'], 2); ?></td>
                </tr>
                <tr>
                    <td>Tax (18%):</td>
                    <td>$<?php echo number_format($order['tax'] ?? 0, 2); ?></td>
                </tr>
                <tr>
                    <td>Shipping:</td>
                    <td>$<?php echo number_format($order['shipping_cost'] ?? $order['shipping'] ?? 0, 2); ?></td>
                </tr>
                <?php 
                $discount = ($order['discount'] ?? 0) + ($order['promo_discount'] ?? 0);
                if ($discount > 0): ?>
                <tr style="color: #10b981;">
                    <td>Discount:</td>
                    <td>-$<?php echo number_format($discount, 2); ?></td>
                </tr>
                <?php endif; ?>
                <tr class="final-total">
                    <td>Total:</td>
                    <td>$<?php echo number_format($order['total'], 2); ?></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>EasyCart Inc. • 123 Tech Avenue, Silicon Valley, CA • support@easycart.com</p>
        </div>
    </div>
</body>
</html>
