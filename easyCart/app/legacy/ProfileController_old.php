<?php

class ProfileController extends Controller {

    public function index() {
        // Require login
        if (!isLoggedIn()) {
            $this->redirect('signin');
        }

        $currentUser = getCurrentUser();
        $orders = getUserOrders($currentUser['id']) ?? [];

        // Determine if we need to show the session order (immediate feedback after checkout)
        if (isset($_SESSION['last_order'])) {
            $lastOrderNum = $_SESSION['last_order']['order_number'];
            $exists = false;
            foreach ($orders as $o) {
                if (isset($o['order_number']) && $o['order_number'] === $lastOrderNum) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $sessionOrder = $_SESSION['last_order'];
                // Ensure consistent structure for the view
                $sessionOrder['id'] = $sessionOrder['id'] ?? 'session_' . time();
                $sessionOrder['subtotal'] = $sessionOrder['subtotal'] ?? 0;
                $sessionOrder['tax'] = $sessionOrder['tax'] ?? 0;
                $sessionOrder['shipping'] = $sessionOrder['shipping_cost'] ?? 0;
                array_unshift($orders, $sessionOrder);
            }
        }

        $data = [
            'title' => 'My Profile',
            'user' => $currentUser,
            'orders' => $orders
        ];

        $this->view('profile/index', $data);
    }
}
