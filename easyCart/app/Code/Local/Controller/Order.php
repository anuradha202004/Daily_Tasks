<?php

class Controller_Order extends Core_Controller {
    
    public function checkout() {
        if (!isLoggedIn()) {
             $this->redirect('signin?redirect=checkout');
             return;
        }

        // Handle POST Actions (AJAX or Form Submit)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            // AJAX Save Shipping
            if ($action === 'save_shipping') {
                $_SESSION['checkout_shipping_method'] = $_POST['method'] ?? 'standard';
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }
            
            // AJAX Remove Promo
            if ($action === 'remove_promo') {
                unset($_SESSION['applied_promo']);
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }

            // Standard Order Placement
            $this->placeOrder();
            return;
        }

        // "Buy Now" logic support
        $productId = $_GET['product_id'] ?? null;
        $qty = $_GET['qty'] ?? 1;
        $isBuyNow = !empty($productId);
        
        $items = [];
        $subtotal = 0;
        $directProduct = null;
        $directQuantity = 0;

        if ($isBuyNow) {
            $productModel = new Model_Product();
            $product = $productModel->load((int)$productId);
            if ($product) {
                $directProduct = $product;
                $directQuantity = (int)$qty;
                $items[] = [
                    'product' => $product,
                    'quantity' => $directQuantity
                ];
                $subtotal = $product['price'] * $directQuantity;
            } else {
                $isBuyNow = false;
            }
        }

        if (!$isBuyNow) {
            $cartModel = new Model_Cart();
            $items = $cartModel->load();
            if (empty($items)) {
                $this->redirect('cart');
                return;
            }
            $totals = $cartModel->getTotals();
            $subtotal = $totals['subtotal'];
        }

        // Prepare cartItemsWithDetails (View expects this specific structure for summary)
        $cartItemsWithDetails = [];
        foreach ($items as $item) {
            if (!isset($item['product']['id'])) continue;
            $cartItemsWithDetails[] = [
                'product' => $item['product'],
                'quantity' => $item['quantity'],
                'itemTotal' => $item['product']['price'] * $item['quantity']
            ];
        }

        // Calculate bulk discount: 1% discount per item (e.g. 2 items = 2%, 5 items = 5%)
        $discount = 0;
        foreach ($items as $item) {
            $qty = (int)$item['quantity'];
            if ($qty > 0) {
                $itemTotal = $item['product']['price'] * $qty;
                $discount += $itemTotal * ($qty / 100);
            }
        }
        
        $promoDiscount = 0; // Promo logic placeholder
        $discountedSubtotal = $subtotal - $discount - $promoDiscount;

        // Shipping Options matching view logic
        $shippingOptions = [
            'standard' => [
                'name' => 'Standard Shipping',
                'cost' => 40.00,
                'icon' => '📦',
                'description' => '5-7 Business Days',
                'label' => 'Flat $40'
            ],
            'express' => [
                'name' => 'Express Shipping',
                'cost' => min(80.00, $subtotal * 0.10),
                'icon' => '🚀',
                'description' => '1-2 Business Days',
                'label' => 'Flat $80 OR 10% of subtotal (whichever is lower)'
            ],
            'whiteglove' => [
                'name' => 'White Glove Delivery',
                'cost' => min(150.00, $subtotal * 0.05),
                'icon' => '🤵',
                'description' => 'Scheduled Appointment',
                'label' => 'Flat $150 OR 5% of subtotal (whichever is lower)'
            ],
            'freight' => [
                'name' => 'Freight Delivery',
                'cost' => max(200.00, $subtotal * 0.03),
                'icon' => '🚛',
                'description' => '7-14 Business Days',
                'label' => '3% of subtotal or Minimum $200'
            ]
        ];

        // Default selection based on "Heavy Item" logic in view (subtotal over/under 300)
        $selectedShipping = ($subtotal < 300) ? 'standard' : 'whiteglove';
        $shippingCost = $shippingOptions[$selectedShipping]['cost'];
        
        // Tax (18% on discounted subtotal + shipping) as per view labels
        $tax = ($discountedSubtotal + $shippingCost) * 0.18;
        $grandTotal = $discountedSubtotal + $shippingCost + $tax;

        // Get any errors from session
        $checkoutMessage = $_SESSION['checkout_errors']['Global'] ?? '';
        unset($_SESSION['checkout_errors']);

        $view = new View_Product('order/checkout');
        $view->assign('title', 'Checkout')
             ->assign('cartItemsWithDetails', $cartItemsWithDetails) // Required for summary
             ->assign('items', $items)
             ->assign('isBuyNow', $isBuyNow)
             ->assign('directProduct', $directProduct)
             ->assign('directQuantity', $directQuantity)
             ->assign('shippingOptions', $shippingOptions)
             ->assign('selectedShipping', $selectedShipping)
             ->assign('subtotal', $subtotal)
             ->assign('discount', $discount)
             ->assign('promoDiscount', $promoDiscount) // Required for summary
             ->assign('appliedPromo', null) // Required for summary
             ->assign('shippingCost', $shippingCost) // Required for summary
             ->assign('tax', $tax) // Required for summary
             ->assign('total', $grandTotal)
             ->assign('checkoutMessage', $checkoutMessage) // For error alerts
             ->assign('user', getCurrentUser());
             
        echo $view->toHtml();
    }
    
    public function confirmation() {
        // Handle Actions (like Cancel Order)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $orderNumber = $_POST['order_id'] ?? $_GET['id'] ?? null;
            
            if ($action === 'cancel_order' && $orderNumber) {
                $orderModel = new Model_Order();
                $order = $orderModel->loadByIncrementId($orderNumber);
                if ($order && $order['status'] !== 'cancelled') {
                    $orderModel->cancelOrder($order['id']);
                    $_SESSION['cancellation_success'] = "Order #{$orderNumber} has been successfully cancelled.";
                }
            }
            $this->redirect('order-confirmation?id=' . $orderNumber);
            return;
        }

        $orderNumber = $_GET['id'] ?? null;
        if (!$orderNumber) {
            $this->redirect('');
            return;
        }

        $orderModel = new Model_Order();
        $order = $orderModel->loadByIncrementId($orderNumber);

        if (!$order) {
            $this->redirect('');
            return;
        }

        // Map database fields to view expectations
        $orderData = [
            'id' => $order['id'],
            'order_number' => $order['increment_id'],
            'subtotal' => (float)$order['subtotal'],
            'tax' => (float)$order['tax_amount'],
            'shipping_cost' => (float)$order['shipping_amount'],
            'discount' => (float)$order['discount_amount'],
            'total' => (float)$order['grand_total'],
            'status' => ucfirst($order['status']),
            'date' => $order['created_at'],
            'shipping_method_name' => ucfirst($order['shipping_method']),
            'customer' => [
                'first_name' => $order['shipping_address']['firstname'] ?? '',
                'last_name' => $order['shipping_address']['lastname'] ?? '',
                'email' => $order['shipping_address']['email'] ?? '',
                'phone' => $order['shipping_address']['telephone'] ?? '',
                'address' => $order['shipping_address']['street'] ?? '',
                'city' => $order['shipping_address']['city'] ?? '',
                'state' => $order['shipping_address']['region'] ?? '',
                'zip' => $order['shipping_address']['postcode'] ?? ''
            ],
            'items' => []
        ];

        foreach ($order['items'] as $item) {
            $orderData['items'][] = [
                'product' => [
                    'id' => $item['product_id'],
                    'sku' => $item['sku'],
                    'name' => $item['name'],
                    'price' => (float)$item['price'],
                    'emoji' => '📦' // Fallback
                ],
                'quantity' => (int)$item['qty_ordered'],
                'itemTotal' => (float)$item['row_total']
            ];
        }

        $cancellationMessage = $_SESSION['cancellation_success'] ?? '';
        unset($_SESSION['cancellation_success']);

        $view = new View_Product('order/confirmation');
        $view->assign('title', 'Order Confirmed')
             ->assign('order', $orderData)
             ->assign('cancellationMessage', $cancellationMessage);
        echo $view->toHtml();
    }
    
    private function placeOrder() {
        try {
            // Validate checkout form data using Core_Validator
            $validator = new Core_Validator();
            $validator->addRule('first_name', 'required|min:2|max:50', 'First Name')
                      ->addRule('last_name', 'required|min:2|max:50', 'Last Name')
                      ->addRule('email', 'required|email', 'Email')
                      ->addRule('phone', 'required|min:10', 'Phone Number')
                      ->addRule('address', 'required|min:5', 'Address')
                      ->addRule('city', 'required|min:2', 'City')
                      ->addRule('state', 'required', 'State/Region')
                      ->addRule('zip', 'required', 'Postal Code')
                      ->addRule('shipping_method', 'required', 'Shipping Method');
            
            if (!$validator->validate($_POST)) {
                $_SESSION['checkout_errors'] = $validator->getErrors();
                $this->redirect('checkout');
                return;
            }
            
            $userId = isLoggedIn() ? getCurrentUser()['id'] : null;
            $items = [];
            $subtotal = 0;
            $isBuyNow = !empty($_POST['product_id']);

            if ($isBuyNow) {
                // Direct Load Product for Order Items
                $productModel = new Model_Product();
                $product = $productModel->load((int)$_POST['product_id']);
                $qty = (int)($_POST['qty'] ?? 1);
                $items[] = [
                    'product' => $product,
                    'quantity' => $qty
                ];
                $subtotal = $product['price'] * $qty;
            } else {
                $cartModel = new Model_Cart();
                $items = $cartModel->load();
                $totals = $cartModel->getTotals();
                $subtotal = $totals['subtotal'];
            }

            // Calculate bulk discount: 1% discount per item (e.g. 2 items = 2%, 5 items = 5%)
            $discount = 0;
            foreach ($items as $item) {
                $qty = (int)$item['quantity'];
                if ($qty > 0) {
                    $itemTotal = $item['product']['price'] * $qty;
                    $discount += $itemTotal * ($qty / 100);
                }
            }

            // Dynamic shipping calculation matching new rules
            $shippingMethod = $_POST['shipping_method'] ?? 'standard';
            switch ($shippingMethod) {
                case 'express':
                    $shippingCost = min(80.00, $subtotal * 0.10);
                    break;
                case 'whiteglove':
                    $shippingCost = min(150.00, $subtotal * 0.05);
                    break;
                case 'freight':
                    $shippingCost = max(200.00, $subtotal * 0.03);
                    break;
                case 'standard':
                default:
                    $shippingCost = 40.00;
                    break;
            }
            
            // Tax (18% as per view)
            $tax = ($subtotal - $discount + $shippingCost) * 0.18;
            $grandTotal = ($subtotal - $discount) + $shippingCost + $tax;

            // Construct Order Data
            $orderData = [
                'order_number' => 'ORD-' . strtoupper(bin2hex(random_bytes(4))),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'total' => $grandTotal,
                'status' => 'pending',
                'shipping_method' => $shippingMethod,
                'customer_email' => $_POST['email'],
                'customer' => [
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['phone'],
                    'address' => $_POST['address'],
                    'city' => $_POST['city'],
                    'state' => $_POST['state'],
                    'zip' => $_POST['zip']
                ]
            ];
            
            $orderModel = new Model_Order();
            $orderId = $orderModel->createOrder($userId, $orderData, $items);
            
            if ($orderId) {
                if (!$isBuyNow) {
                    $cartModel = new Model_Cart();
                    $cartModel->clear();
                }
                $this->redirect('order-confirmation?id=' . $orderData['order_number']);
            } else {
                throw new Exception("Order creation failed");
            }
            
        } catch (Exception $e) {
             $_SESSION['checkout_errors'] = ['Global' => $e->getMessage()];
             $this->redirect('checkout');
        }
    }
    
    public function index() {
        if (!isLoggedIn()) {
            $this->redirect('signin');
            return;
        }
        $userId = getCurrentUser()['id'];
        $orderModel = new Model_Order();
        $ordersCollection = $orderModel->getOrdersByUserId($userId);
        
        $orders = [];
        foreach ($ordersCollection as $order) {
            // Load items for each order to show in history
            $orderWithItems = $orderModel->loadByIncrementId($order['increment_id']);
            
            $orders[] = [
                'order_number' => $order['increment_id'],
                'date' => $order['created_at'],
                'status' => ucfirst($order['status']),
                'subtotal' => (float)$order['subtotal'],
                'tax' => (float)$order['tax_amount'],
                'shipping' => (float)$order['shipping_amount'],
                'total' => (float)$order['grand_total'],
                'items' => array_map(function($item) {
                    return [
                        'product_id' => $item['product_id'],
                        'quantity' => (int)$item['qty_ordered'],
                        'itemTotal' => (float)$item['row_total'],
                        'product' => [
                            'name' => $item['name'],
                            'price' => (float)($item['price'] ?? 0),
                            'emoji' => '📦' // Fallback
                        ]
                    ];
                }, $orderWithItems['items'] ?? [])
            ];
        }

        $view = new View_Product('order/index');
        $view->assign('orders', $orders)->assign('title', 'My Orders');
        echo $view->toHtml();
    }

    public function track() {
        $id = $_GET['id'] ?? '';
        $orderModel = new Model_Order();
        $order = $orderModel->loadByIncrementId($id);
        $view = new View_Product('order/track');
        $view->assign('order', $order)->assign('title', 'Track Order');
        echo $view->toHtml();
    }
    
    public function invoice() {
        if (!isLoggedIn()) {
            $this->redirect('signin');
            return;
        }

        $id = $_GET['id'] ?? '';
        $latest = $_GET['latest'] ?? '';
        $orderModel = new Model_Order();
        
        if ($latest === 'true') {
            $userId = getCurrentUser()['id'];
            $order = $orderModel->getLatestOrderByUserId($userId);
        } else {
            $order = $orderModel->loadByIncrementId($id);
        }

        if (!$order) {
            die("Order not found");
        }

        // Security check: ensure order belongs to current user (unless admin)
        if (getCurrentUser()['role'] !== 'admin' && $order['user_id'] != getCurrentUser()['id']) {
            $this->redirect('profile');
            return;
        }

        // Map data for view compatibility
        $mappedOrder = [
            'order_number' => $order['increment_id'],
            'date' => $order['created_at'],
            'customer' => [
                'first_name' => $order['shipping_address']['firstname'] ?? '',
                'last_name' => $order['shipping_address']['lastname'] ?? '',
                'email' => $order['shipping_address']['email'] ?? '',
                'address' => $order['shipping_address']['street'] ?? '',
                'city' => $order['shipping_address']['city'] ?? '',
                'state' => $order['shipping_address']['region'] ?? '',
                'zip' => $order['shipping_address']['postcode'] ?? '',
                'phone' => $order['shipping_address']['telephone'] ?? ''
            ],
            'items' => array_map(function($item) {
                return [
                    'quantity' => $item['qty_ordered'],
                    'itemTotal' => $item['row_total'],
                    'product' => [
                        'name' => $item['name'],
                        'sku' => $item['sku'],
                        'price' => $item['price']
                    ]
                ];
            }, $order['items'] ?? []),
            'subtotal' => $order['subtotal'],
            'tax' => $order['tax_amount'],
            'shipping' => $order['shipping_amount'],
            'discount' => $order['discount_amount'],
            'total' => $order['grand_total']
        ];

        $view = new View_Product('order/invoice');
        $view->assign('order', $mappedOrder);
        echo $view->toHtml();
    }
}
