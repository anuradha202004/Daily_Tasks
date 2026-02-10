<?php

class Controller_Profile extends Core_Controller {
    
    public function __construct() {
        if (!isLoggedIn()) {
            $this->redirect('signin');
            exit;
        }
    }

    public function index() {
        $user = getCurrentUser();
        
        // Fetch Orders using Model_Order
        $orderModel = new Model_Order();
        $orders = $orderModel->getOrdersByUserId($user['id']);
        
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
