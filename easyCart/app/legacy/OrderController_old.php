<?php

class OrderController extends Controller {

    public function checkout() {
        // Require login
        if (!isLoggedIn()) {
            $this->redirect('signin?redirect=checkout');
        }

        // Prevent browser caching headers
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        // --- Logic from checkout.php ---

        $directProduct = null;
        $directQuantity = 1;
        $isBuyNow = false;

        // Reset Shipping if requested
        if (isset($_GET['reset_shipping']) && $_GET['reset_shipping'] == '1') {
            unset($_SESSION['selected_shipping']);
        }

        // Handle Buy Now (via GET/POST)
        if (isset($_REQUEST['product_id']) && isset($_REQUEST['qty'])) {
            $pid = intval($_REQUEST['product_id']);
            $directProduct = getProductById($pid);
            $isBuyNow = true;
            
            if (!$directProduct) {
                $this->redirect('products');
            }

            $targetQty = intval($_REQUEST['qty']);

            // Reset shipping/promo if switching products
            if (!isset($_SESSION['checkout_product_id']) || $_SESSION['checkout_product_id'] !== $pid) {
                unset($_SESSION['selected_shipping']);
                unset($_SESSION['applied_promo']);
            }
            $_SESSION['checkout_product_id'] = $pid;

            // Sync with session for Buy Now (creates temporary cart state conceptually)
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            $directQuantity = $targetQty;
        } else {
            if (!$isBuyNow) {
                unset($_SESSION['checkout_product_id']);
            }
        }

        // Determine Cart Items
        $cartItems = $_SESSION['cart'] ?? [];
        if ($isBuyNow && $directProduct) {
            $cartItems = [
                $directProduct['id'] => [
                    'product_id' => $directProduct['id'],
                    'quantity' => $directQuantity
                ]
            ];
        }

        if (count($cartItems) === 0) {
            $this->redirect('cart');
        }

        // Calculate Subtotal & Discount
        $subtotal = 0;
        $discount = 0;
        $cartItemsWithDetails = [];

        foreach ($cartItems as $productId => $cartItem) {
            $product = getProductById($productId);
            if ($product) {
                $itemTotal = $product['price'] * $cartItem['quantity'];
                $subtotal += $itemTotal;
                $discount += calculateBulkDiscount($product['price'], $cartItem['quantity']);
                
                $cartItemsWithDetails[] = [
                    'product' => $product,
                    'quantity' => $cartItem['quantity'],
                    'itemTotal' => $itemTotal
                ];
            }
        }

        // Handle AJAX Actions (Shipping, Promo)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];
            
            if ($action === 'save_shipping') {
                $_SESSION['selected_shipping'] = $_POST['method'];
                $this->sendJson(['success' => true]);
            }
            elseif ($action === 'apply_promo') {
                $this->handlePromoCode();
            }
            elseif ($action === 'remove_promo') {
                unset($_SESSION['applied_promo']);
                $this->sendJson(['success' => true]);
            }
            elseif ($action === 'complete_order') {
                $this->processOrder($subtotal, $discount, $cartItemsWithDetails, $isBuyNow);
                return; // Stop execution if order processed (redirects inside)
            }
        }

        // Shipping Logic
        $selectedShipping = $this->determineShippingMethod($subtotal);
        $shippingCost = $this->calculateShippingCost($selectedShipping, $subtotal);
        
        // Promo Calculation
        $promoDiscount = $this->calculatePromoDiscount($subtotal, $discount);
        
        // Tax & Total
        $taxableAmount = $subtotal - $discount - $promoDiscount + $shippingCost;
        $tax = max(0, $taxableAmount) * 0.18;
        $total = ($subtotal - $discount - $promoDiscount) + $shippingCost + $tax;

        // Shipping Options Data
        $shippingOptions = $this->getShippingOptions($subtotal);

        // Pass data to View
        $data = [
            'title' => 'Checkout',
            'cartItemsWithDetails' => $cartItemsWithDetails,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'promoDiscount' => $promoDiscount,
            'tax' => $tax,
            'shippingCost' => $shippingCost,
            'total' => $total,
            'selectedShipping' => $selectedShipping,
            'shippingOptions' => $shippingOptions,
            'appliedPromo' => $_SESSION['applied_promo'] ?? null,
            'isBuyNow' => $isBuyNow,
            'directProduct' => $directProduct,
            'directQuantity' => $directQuantity,
            'checkoutMessage' => $this->checkoutMessage ?? ''
        ];

        $this->view('order/checkout', $data);
    }

    public function confirmation() {
        // Handle Cancel Order POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_order') {
            $this->cancelOrder();
        }

        // Retrieve Order
        $order = null;
        if (isset($_GET['id'])) {
            $fetchedOrder = getOrderByNumber($_GET['id']);
            if ($fetchedOrder) {
                // Map fetched DB order to session format for view compatibility
                $order = $this->mapOrderData($fetchedOrder);
                $_SESSION['last_order'] = $order;
            }
        } else {
            $order = $_SESSION['last_order'] ?? null;
        }

        if (!$order) {
            $this->redirect('products');
        }

        // Ensure status exists if missing
        if (!isset($order['status'])) {
            $order['status'] = 'Processing';
            $order['date'] = date('Y-m-d H:i:s');
            // Update session
            $_SESSION['last_order']['status'] = 'Processing'; 
        }

        $data = [
            'title' => 'Order Confirmation',
            'order' => $order,
            'cancellationMessage' => $this->cancellationMessage ?? null
        ];

        $this->view('order/confirmation', $data);
    }

    public function index() {
        // Require login
        if (!isLoggedIn()) {
            $this->redirect('signin');
        }

        // Handle Cancellation POST from the orders list
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
            $cancelOrderId = $_POST['order_id'];
            $currentUser = getCurrentUser();
            
            // Re-use logic from orders.php
            global $pdo;
            $stmt = $pdo->prepare("SELECT id, status FROM sales_order WHERE user_id = ? AND increment_id = ?");
            $stmt->execute([$currentUser['id'], $cancelOrderId]);
            $orderToCancel = $stmt->fetch();
            
            if ($orderToCancel && $orderToCancel['status'] === 'Processing') {
                $updateStmt = $pdo->prepare("UPDATE sales_order SET status = 'Cancelled' WHERE id = ?");
                if ($updateStmt->execute([$orderToCancel['id']])) {
                    $_SESSION['flash_message'] = "Order #$cancelOrderId has been cancelled successfully.";
                    $_SESSION['flash_type'] = "success";
                } else {
                    $_SESSION['flash_message'] = "Failed to cancel order.";
                    $_SESSION['flash_type'] = "error";
                }
            } else {
                $_SESSION['flash_message'] = "Order cannot be cancelled. It may have already been processed.";
                $_SESSION['flash_type'] = "error";
            }
            $this->redirect('orders');
        }

        $currentUser = getCurrentUser();
        $allOrders = getUserOrders($currentUser['id']) ?? [];

        // Merge session order if missing
        if (isset($_SESSION['last_order'])) {
            $sessionOrderNum = $_SESSION['last_order']['order_number'] ?? null;
            $exists = false;
            foreach ($allOrders as $o) {
                if ($o['order_number'] === $sessionOrderNum) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $lastOrderData = [
                    'id' => 'recent_' . time(),
                    'order_number' => $sessionOrderNum,
                    'date' => $_SESSION['last_order']['date'] ?? date('Y-m-d H:i:s'),
                    'status' => $_SESSION['last_order']['status'] ?? 'Processing',
                    'subtotal' => $_SESSION['last_order']['subtotal'] ?? 0,
                    'tax' => $_SESSION['last_order']['tax'] ?? 0,
                    'shipping' => $_SESSION['last_order']['shipping_cost'] ?? 0,
                    'total' => $_SESSION['last_order']['total'] ?? 0,
                    'items' => $_SESSION['last_order']['items'] ?? []
                ];
                array_unshift($allOrders, $lastOrderData);
            }
        }

        $data = [
            'title' => 'My Orders',
            'orders' => $allOrders,
            'flash' => [
                'message' => $_SESSION['flash_message'] ?? null,
                'type' => $_SESSION['flash_type'] ?? null
            ]
        ];
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);

        $this->view('order/index', $data);
    }

    public function track() {
        $trackingResult = null;
        $trackingError = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'track_order') {
            $orderNumber = trim($_POST['order_number'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            if (empty($orderNumber) || empty($email)) {
                $trackingError = 'Please enter both order number and email address.';
            } else {
                // Check session
                if (isset($_SESSION['last_order']) && 
                    $_SESSION['last_order']['order_number'] === $orderNumber &&
                    strtolower($_SESSION['last_order']['customer']['email'] ?? '') === strtolower($email)) {
                    $trackingResult = $_SESSION['last_order'];
                } else {
                    // Check database
                    $fetchedOrder = getOrderByNumber($orderNumber);
                    if ($fetchedOrder && strtolower($fetchedOrder['email'] ?? '') === strtolower($email)) {
                        $trackingResult = $this->mapOrderData($fetchedOrder);
                    } else {
                        $trackingError = 'Order not found. Please check your order number and email address.';
                    }
                }
            }
        }

        $data = [
            'title' => 'Track Order',
            'trackingResult' => $trackingResult,
            'trackingError' => $trackingError
        ];

        $this->view('order/track', $data);
    }

    public function invoice() {
        // Require login
        if (!isLoggedIn()) {
             $this->redirect('signin');
        }

        $currentUser = getCurrentUser();
        $order = null;

        // Fetch by Order Number
        if (isset($_GET['order_number'])) {
            $orderNumber = $_GET['order_number'];
            if (isset($_SESSION['last_order']) && $_SESSION['last_order']['order_number'] === $orderNumber) {
                $order = $_SESSION['last_order'];
            } else {
                $fetchedOrder = getOrderByNumber($orderNumber);
                if ($fetchedOrder && $fetchedOrder['user_id'] == $currentUser['id']) {
                    $order = $this->mapOrderData($fetchedOrder);
                }
            }
        } 
        // Latest Order
        elseif (isset($_GET['latest'])) {
            $order = $_SESSION['last_order'] ?? null;
        }

        if (!$order) {
            die("Order not found or access denied.");
        }

        $this->view('order/invoice', ['order' => $order]);
    }

    // --- Private Helper Methods ---

    private function determineShippingMethod($subtotal) {
        $method = 'standard';
        if (isset($_POST['shipping_method'])) {
            $method = $_POST['shipping_method'];
            $_SESSION['selected_shipping'] = $method;
        } elseif (isset($_GET['shipping_method'])) {
            $method = $_GET['shipping_method'];
            $_SESSION['selected_shipping'] = $method;
        } elseif (isset($_SESSION['selected_shipping'])) {
            $method = $_SESSION['selected_shipping'];
        }

        // Validate method against subtotal rules
        $validMethods = ['standard', 'express', 'whiteglove', 'freight'];
        if (!in_array($method, $validMethods)) {
            $method = ($subtotal < 300) ? 'standard' : 'whiteglove';
        }

        if ($subtotal < 300) {
            if ($method === 'whiteglove' || $method === 'freight') $method = 'standard';
        } else {
            if ($method === 'standard' || $method === 'express') $method = 'whiteglove';
        }

        return $method;
    }

    private function calculateShippingCost($method, $subtotal) {
        switch ($method) {
            case 'standard': return 40.00;
            case 'express': return min(80.00, $subtotal * 0.10);
            case 'whiteglove': return min(150.00, $subtotal * 0.05);
            case 'freight': return max(200.00, $subtotal * 0.03);
            default: return 40.00;
        }
    }

    private function getShippingOptions($subtotal) {
        return [
            'standard' => [
                'name' => 'Standard Shipping',
                'description' => 'Delivery in 5-7 business days',
                'cost' => 40.00,
                'icon' => '📦'
            ],
            'express' => [
                'name' => 'Express Shipping',
                'description' => 'Delivery in 2-3 business days',
                'cost' => min(80.00, $subtotal * 0.10),
                'calculation' => 'Flat $80 OR 10% of subtotal (whichever is lower)',
                'icon' => '⚡'
            ],
            'whiteglove' => [
                'name' => 'White Glove Delivery',
                'description' => 'Premium delivery with installation',
                'cost' => min(150.00, $subtotal * 0.05),
                'calculation' => 'Flat $150 OR 5% of subtotal (whichever is lower)',
                'icon' => '🎩'
            ],
            'freight' => [
                'name' => 'Freight Shipping',
                'description' => 'For large or bulk orders',
                'cost' => max(200.00, $subtotal * 0.03),
                'calculation' => '3% of subtotal, Minimum $200',
                'icon' => '🚛'
            ]
        ];
    }

    private function handlePromoCode() {
        $code = strtoupper(trim($_POST['code']));
        $response = ['success' => false, 'message' => 'Invalid code'];
        
        if ($code === 'SAVE10') {
            $_SESSION['applied_promo'] = ['code' => 'SAVE10', 'type' => 'percent', 'value' => 10];
            $response = ['success' => true, 'message' => 'Promo applied: 10% Off!'];
        } elseif ($code === 'FLAT50') {
            $_SESSION['applied_promo'] = ['code' => 'FLAT50', 'type' => 'fixed', 'value' => 50];
            $response = ['success' => true, 'message' => 'Promo applied: $50 Off!'];
        }
        
        $this->sendJson($response);
    }

    private function calculatePromoDiscount($subtotal, $discount) {
        $promoDiscount = 0;
        $appliedPromo = $_SESSION['applied_promo'] ?? null;
        
        if ($appliedPromo) {
            if ($appliedPromo['type'] === 'percent') {
                $promoDiscount = $subtotal * ($appliedPromo['value'] / 100);
            } elseif ($appliedPromo['type'] === 'fixed') {
                $promoDiscount = $appliedPromo['value'];
            }
            
            // Limit discount
            if (($subtotal - $discount - $promoDiscount) < 0) {
                $promoDiscount = max(0, $subtotal - $discount);
            }
        }
        return $promoDiscount;
    }

    private function processOrder($subtotal, $discount, $cartItemsWithDetails, $isBuyNow) {
        $required_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state', 'zip', 'card_number', 'shipping_method'];
        $all_filled = true;
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $all_filled = false;
                break;
            }
        }

        if ($all_filled) {
            $shippingMethod = $this->determineShippingMethod($subtotal); // Recalculate based on POST
            $shippingCost = $this->calculateShippingCost($shippingMethod, $subtotal);
            $promoDiscount = $this->calculatePromoDiscount($subtotal, $discount);
            
            $taxableAmount = $subtotal - $discount - $promoDiscount + $shippingCost;
            $tax = max(0, $taxableAmount) * 0.18;
            $finalTotal = $subtotal - $discount - $promoDiscount + $shippingCost + $tax;

            $_SESSION['last_order'] = [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'promo_discount' => $promoDiscount,
                'shipping_method' => $shippingMethod,
                'shipping_method_name' => $this->getShippingOptions($subtotal)[$shippingMethod]['name'],
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'total' => $finalTotal,
                'status' => 'Processing',
                'order_number' => 'ORD' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                'customer' => [
                    'first_name' => htmlspecialchars($_POST['first_name']),
                    'last_name' => htmlspecialchars($_POST['last_name']),
                    'email' => htmlspecialchars($_POST['email']),
                    'phone' => htmlspecialchars($_POST['phone']),
                    'address' => htmlspecialchars($_POST['address']),
                    'city' => htmlspecialchars($_POST['city']),
                    'state' => htmlspecialchars($_POST['state']),
                    'zip' => htmlspecialchars($_POST['zip'])
                ],
                'items' => $cartItemsWithDetails,
                'date' => date('Y-m-d H:i:s')
            ];

            // DB Save
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $dbOrderData = [
                'order_number' => $_SESSION['last_order']['order_number'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'discount' => $discount + $promoDiscount,
                'total' => $finalTotal,
                'status' => 'Processing',
                'shipping_method' => $shippingMethod,
                'customer' => $_SESSION['last_order']['customer'],
                'customer_email' => $_SESSION['last_order']['customer']['email']
            ];

            if (createOrder($userId, $dbOrderData, $cartItemsWithDetails)) {
                if (!$isBuyNow) {
                    $_SESSION['cart'] = [];
                    saveUserCart($userId, []);
                }
                unset($_SESSION['selected_shipping']);
                $this->redirect('order-confirmation');
            } else {
                $this->checkoutMessage = 'Order creation failed.';
            }
        } else {
            $this->checkoutMessage = 'Please fill in all required fields.';
        }
    }

    private function cancelOrder() {
        $orderNumber = $_SESSION['last_order']['order_number'];
        global $pdo;
        $stmt = $pdo->prepare("UPDATE sales_order SET status = 'Cancelled' WHERE increment_id = ?");
        $stmt->execute([$orderNumber]);

        $_SESSION['last_order']['status'] = 'Cancelled';
        $this->cancellationMessage = 'Order has been cancelled successfully.';
    }

    private function mapOrderData($fetchedOrder) {
        $mappedOrder = [
             'order_number' => $fetchedOrder['increment_id'],
             'date' => $fetchedOrder['created_at'],
             'status' => $fetchedOrder['status'],
             'total' => $fetchedOrder['grand_total'],
             'subtotal' => $fetchedOrder['subtotal'],
             'tax' => $fetchedOrder['tax_amount'],
             'shipping_cost' => $fetchedOrder['shipping_amount'],
             'promo_discount' => $fetchedOrder['discount_amount'],
             'shipping_method_name' => $fetchedOrder['shipping_method'] ?? 'Standard',
             'customer' => [
                 'first_name' => $fetchedOrder['first_name'],
                 'last_name' => $fetchedOrder['last_name'],
                 'email' => $fetchedOrder['email'],
                 'phone' => $fetchedOrder['phone'],
                 'address' => $fetchedOrder['address'],
                 'city' => $fetchedOrder['city'],
                 'state' => $fetchedOrder['state'],
                 'zip' => $fetchedOrder['zip']
             ],
             'items' => []
        ];
        
        foreach ($fetchedOrder['items'] as $item) {
             $productDetails = getProductById($item['product_id']);
             $mappedOrder['items'][] = [
                 'product_id' => $item['product_id'],
                 'quantity' => $item['qty_ordered'] ?? $item['quantity'] ?? 1,
                 'itemTotal' => $item['price'] * ($item['qty_ordered'] ?? $item['quantity'] ?? 1),
                 'product' => [
                     'name' => $item['name'] ?? $productDetails['name'],
                     'emoji' => $productDetails['emoji'] ?? '📦'
                 ]
             ];
        }
        return $mappedOrder;
    }

    private function sendJson($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
