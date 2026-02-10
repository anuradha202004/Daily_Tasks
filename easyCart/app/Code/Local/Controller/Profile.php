<?php

class Controller_Profile extends Core_Controller {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn()) {
            $this->redirect('signin');
            exit;
        }
    }

    public function index() {
        $user = getCurrentUser();
        
        // Fetch Orders using Model_Order
        $orderModel = new Model_Order();
        $rawOrders = $orderModel->getOrdersByUserId($user['id']);
        
        // Map order data to match view expectations
        $orders = array_map(function($order) {
            return [
                'order_number' => $order['increment_id'],
                'date' => $order['created_at'],
                'status' => ucfirst($order['status']),
                'subtotal' => (float)$order['subtotal'],
                'tax' => (float)($order['tax_amount'] ?? 0),
                'shipping' => (float)($order['shipping_amount'] ?? 0),
                'total' => (float)$order['grand_total'],
                'discount' => (float)($order['discount_amount'] ?? 0)
            ];
        }, $rawOrders);
        
        $view = new View_Product('profile/index');
        $view->assign('user', $user)
             ->assign('orders', $orders)
             ->assign('title', 'My Profile');
             
        echo $view->toHtml();
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             // Handle profile update logic
             // Update User Model (needs implementation in Model_Customer)
             // ...
             // Redirect back
             $this->redirect('profile');
        }
    }
}
