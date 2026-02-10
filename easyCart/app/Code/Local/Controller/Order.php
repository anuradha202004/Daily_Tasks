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

        // Tiered bulk discount calculation
        $discount = 0;
        if ($subtotal >= 1000) $discount = $subtotal * 0.15;
        elseif ($subtotal >= 500) $discount = $subtotal * 0.10;
        elseif ($subtotal >= 100) $discount = $subtotal * 0.05;
        
        $promoDiscount = 0; // Promo logic placeholder
        $discountedSubtotal = $subtotal - $discount - $promoDiscount;

        // Shipping Options matching view logic
        $shippingOptions = [
            'standard' => [
                'name' => 'Standard Shipping',
                'cost' => 15.00,
                'icon' => '📦',
                'description' => 'Delivery in 3-5 business days'
            ],
            'express' => [
                'name' => 'Express Shipping',
                'cost' => 35.00,
                'icon' => '🚀',
                'description' => 'Delivery in 1-2 business days'
            ],
            'whiteglove' => [
                'name' => 'White Glove Delivery',
                'cost' => 150.00,
                'icon' => '🤵',
                'description' => 'Professional setup and assembly'
            ],
            'freight' => [
                'name' => 'Freight Delivery',
                'cost' => 450.00,
                'icon' => '🚛',
                'description' => 'Specialized LTL shipping for bulky items'
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
            $orderNumber = $_GET['id'] ?? null;
            
            if ($action === 'cancel_order' && $orderNumber) {
                $orderModel = new Model_Order();
                $order = $orderModel->loadByIncrementId($orderNumber);
                if ($order) {
                    // Update order status in DB
                    $this->db->query("UPDATE sales_order SET status = 'cancelled' WHERE id = :id", ['id' => $order['id']]);
                    $_SESSION['cancellation_success'] = "Order has been successfully cancelled.";
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

            // Calculation Logic
            $discount = 0;
            if ($subtotal >= 1000) $discount = $subtotal * 0.15;
            elseif ($subtotal >= 500) $discount = $subtotal * 0.10;
            elseif ($subtotal >= 100) $discount = $subtotal * 0.05;

            // Get shipping cost from predefined map
            $shippingMap = ['standard' => 15.0, 'express' => 35.0, 'whiteglove' => 150.0, 'freight' => 450.0];
            $shippingMethod = $_POST['shipping_method'] ?? 'standard';
            $shippingCost = $shippingMap[$shippingMethod] ?? 15.00;
            
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
        $id = $_GET['id'] ?? '';
        $orderModel = new Model_Order();
        $order = $orderModel->loadByIncrementId($id);
        $view = new View_Product('order/invoice');
        $view->assign('order', $order);
        echo $view->toHtml();
    }
}
