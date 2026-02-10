<?php

class Controller_Order extends Core_Controller {
    
    public function checkout() {
        // Enforce login logic here if strict
        // "Check email first... Existing email -> ask password"
        // This usually implies user identification step
        
        $step = $_GET['step'] ?? 'shipping';
        
        if (!isLoggedIn()) {
             // Redirect to Signin with redirect back to checkout?
             // Or allow guest checkout if email check passes?
             // Given requirement, we likely want to redirect to signin OR a dedicated checkout-login page.
             // For simplicity, redirect to signin
             $this->redirect('signin?redirect=checkout');
             return;
        }
        
        // Prepare Checkout View
        $cartModel = new Model_Cart();
        $items = $cartModel->load(); // Re-load to be sure
        
        if (empty($items)) {
            $this->redirect('cart');
            return;
        }
        
        $totals = $cartModel->getTotals();
        
        $view = new View_Product('order/checkout');
        $view->assign('title', 'Checkout')
             ->assign('cartItems', $items)
             ->assign('totals', $totals)
             ->assign('user', getCurrentUser()); // Helper for now
             
        echo $view->toHtml();
    }
    
    public function confirmation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle Place Order
            $this->placeOrder();
        } else {
             // Show success page if GET
             // e.g. after successful placement
             $view = new View_Product('order/success');
             echo $view->toHtml();
        }
    }
    
    private function placeOrder() {
        try {
            $userId = isLoggedIn() ? getCurrentUser()['id'] : null;
            $cartModel = new Model_Cart();
            $items = $cartModel->load();
            $totals = $cartModel->getTotals();
            
            // Construct Order Data from POST
            $orderData = [
                'order_number' => 'ORD-' . time() . '-' . rand(1000, 9999),
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'shipping_cost' => 5.00, // Fixed shipping for now
                'tax' => $totals['subtotal'] * 0.1, // 10% tax
                'total' => $totals['total'] + 5.00 + ($totals['subtotal'] * 0.1),
                'status' => 'pending',
                'shipping_method' => 'standard',
                'customer' => [
                    'first_name' => $_POST['firstname'],
                    'last_name' => $_POST['lastname'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['telephone'],
                    'address' => $_POST['street'],
                    'city' => $_POST['city'],
                    'state' => $_POST['region'],
                    'zip' => $_POST['postcode']
                ]
            ];
            
            $orderModel = new Model_Order();
            $orderId = $orderModel->createOrder($userId, $orderData, $items);
            
            if ($orderId) {
                // Clear Cart
                $cartModel->clear();
                
                // Redirect
                $this->redirect('order-confirmation?id=' . $orderData['order_number']);
            } else {
                throw new Exception("Order creation failed");
            }
            
        } catch (Exception $e) {
             // Handle error
             echo "Error: " . $e->getMessage();
        }
    }
    
    public function index() {
        if (!isLoggedIn()) {
            $this->redirect('signin');
            return;
        }
        
        $userId = getCurrentUser()['id'];
        $orderModel = new Model_Order();
        $orders = $orderModel->getOrdersByUserId($userId);
        
        $view = new View_Product('order/history'); // Assuming view exists
        $view->assign('orders', $orders)
             ->assign('title', 'My Orders');
        echo $view->toHtml();
    }

    public function track() {
         // Tracking logic
    }
    
    public function invoice() {
        // Invoice view
    }
}
