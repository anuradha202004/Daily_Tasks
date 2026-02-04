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
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; padding: 40px; background: #fff; }
        .invoice-box { max-width: 800px; margin: 0 auto; border: 1px solid #eee; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,.15); }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; }
        .invoice-info { text-align: right; }
        .invoice-info h1 { margin: 0; font-size: 24px; color: #333; }
        .invoice-info p { margin: 5px 0 0; color: #666; font-size: 14px; }
        .columns { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .column { flex: 1; }
        .column h3 { font-size: 14px; text-transform: uppercase; color: #999; margin: 0 0 10px; }
        .column p { margin: 0; font-weight: 500; font-size: 15px; }
        .column .sub-text { font-weight: normal; font-size: 14px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { text-align: left; padding: 12px; background: #f8f9fa; border-bottom: 2px solid #eee; font-size: 14px; text-transform: uppercase; color: #666; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .total-section { display: flex; justify-content: flex-end; }
        .total-table { width: 300px; }
        .total-table td { text-align: right; border: none; padding: 5px 12px; }
        .total-table tr.final-total td { font-size: 18px; font-weight: bold; color: #2563eb; padding-top: 10px; border-top: 2px solid #eee; }
        .footer { margin-top: 50px; text-align: center; color: #999; font-size: 12px; padding-top: 20px; border-top: 1px solid #eee; }
        .print-btn { display: inline-block; padding: 10px 20px; background: #2563eb; color: white; border-radius: 5px; text-decoration: none; font-weight: bold; margin-bottom: 20px; cursor: pointer; border: none; }
        @media print {
            .print-btn, .back-btn { display: none; }
            body { padding: 0; }
            .invoice-box { border: none; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>
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
                <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($item['product']['name']); ?></strong>
                    </td>
                    <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                    <td style="text-align: right;">$<?php echo number_format($item['product']['price'], 2); ?></td>
                    <td style="text-align: right;">$<?php echo number_format($item['itemTotal'], 2); ?></td>
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
