<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo htmlspecialchars($data['order']['order_number']); ?> - EasyCart</title>
    <link rel="stylesheet" href="<?php echo baseUrl('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/home.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/auth.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/cart.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/product.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/wishlist.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/order.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/checkout.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/admin.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/profile.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/user-menu.css'); ?>">
    <link rel="stylesheet" href="<?php echo baseUrl('css/invoice.css'); ?>">
</head>
<body class="invoice-page">
    <div class="print-btn-container">
        <button onclick="window.print()" class="print-btn">🖨️ Print Invoice</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="logo">🛒 EasyCart</div>
            <div class="invoice-info">
                <h1>INVOICE</h1>
                <p>#<?php echo htmlspecialchars($data['order']['order_number']); ?></p>
                <p>Date: <?php echo date('F d, Y', strtotime($data['order']['date'])); ?></p>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <h3>Billed To</h3>
                <?php $cust = $data['order']['billing_customer'] ?? $data['order']['customer']; ?>
                <p><?php echo htmlspecialchars($cust['first_name'] . ' ' . $cust['last_name']); ?></p>
                <p class="sub-text"><?php echo htmlspecialchars($cust['address']); ?></p>
                <p class="sub-text"><?php echo htmlspecialchars($cust['city'] . ', ' . $cust['state'] . ' ' . $cust['zip']); ?></p>
                <p class="sub-text"><?php echo htmlspecialchars($cust['email']); ?></p>
            </div>
            <div class="column">
                <h3>Shipped To</h3>
                <?php $ship = $data['order']['customer']; ?>
                <p><?php echo htmlspecialchars($ship['first_name'] . ' ' . $ship['last_name']); ?></p>
                <p class="sub-text"><?php echo htmlspecialchars($ship['address']); ?></p>
                <p class="sub-text"><?php echo htmlspecialchars($ship['city'] . ', ' . $ship['state'] . ' ' . $ship['zip']); ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="col-item">Item</th>
                    <th class="col-center">Quantity</th>
                    <th class="col-right">Price</th>
                    <th class="col-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['order']['items'] as $item): 
                    $productName = $item['product']['name'] ?? 'Unknown Product';
                    $qty = $item['quantity'];
                    $itemTotal = $item['itemTotal'];
                    $productPrice = $qty > 0 ? $itemTotal / $qty : 0;
                ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($productName); ?></strong>
                    </td>
                    <td class="col-center"><?php echo $qty; ?></td>
                    <td class="col-right"><?php echo formatPrice($productPrice); ?></td>
                    <td class="col-right"><?php echo formatPrice($itemTotal); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            <table class="total-table">
                <tr>
                    <td>Subtotal:</td>
                    <td><?php echo formatPrice($data['order']['subtotal']); ?></td>
                </tr>
                <tr>
                    <td>Tax (18%):</td>
                    <td><?php echo formatPrice($data['order']['tax'] ?? 0); ?></td>
                </tr>
                <tr>
                    <td>Shipping:</td>
                    <td><?php echo formatPrice($data['order']['shipping_cost'] ?? $data['order']['shipping'] ?? 0); ?></td>
                </tr>
                <?php 
                $discount = ($data['order']['discount'] ?? 0) + ($data['order']['promo_discount'] ?? 0);
                if ($discount > 0): ?>
                <tr class="total-discount">
                    <td>Discount:</td>
                    <td>-<?php echo formatPrice($discount); ?></td>
                </tr>
                <?php endif; ?>
                <tr class="final-total">
                    <td>Total:</td>
                    <td><?php echo formatPrice($data['order']['total']); ?></td>
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
