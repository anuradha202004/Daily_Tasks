<?php

class WishlistController extends Controller {

    public function index() {
        // Require login for page view
        if (!isLoggedIn()) {
             $this->redirect('signin'); // Or handle gracefully
        }
        
        $userId = getCurrentUser()['id'];

         // Handle AJAX requests
         if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $this->handleAjaxRequests($userId);
            return;
        }

        // --- Page View Logic ---
        
        // Initialize if not set
        if (!isset($_SESSION['wishlist'])) {
            $_SESSION['wishlist'] = loadUserWishlist($userId);
        }
         if (!is_array($_SESSION['wishlist'])) {
            $_SESSION['wishlist'] = [];
        }

        $wishlistItems = [];
        if (!empty($_SESSION['wishlist'])) {
            $itemsToShow = array_reverse($_SESSION['wishlist']); // Newest first
            foreach ($itemsToShow as $productId) {
                $product = getProductById($productId);
                if ($product) {
                    $wishlistItems[] = $product;
                }
            }
        }

        $data = [
            'title' => 'My Wishlist',
            'wishlistItems' => $wishlistItems
        ];

        $this->view('wishlist/index', $data);
    }

    private function handleAjaxRequests($userId) {
        $action = $_POST['action'];
        $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        
        // Ensure wishlist is loaded
        if (!isset($_SESSION['wishlist'])) {
            $_SESSION['wishlist'] = loadUserWishlist($userId);
        }
         if (!is_array($_SESSION['wishlist'])) {
            $_SESSION['wishlist'] = [];
        }

        $response = ['success' => false];

        if ($action === 'add' && $productId > 0) {
            if (!in_array($productId, $_SESSION['wishlist'])) {
                $_SESSION['wishlist'][] = $productId;
            }
            saveUserWishlist($userId, $_SESSION['wishlist']);
            $response = ['success' => true, 'message' => 'Added to wishlist', 'count' => count($_SESSION['wishlist'])];

        } elseif ($action === 'remove' && $productId > 0) {
            $_SESSION['wishlist'] = array_filter($_SESSION['wishlist'], function($id) use ($productId) {
                return $id !== $productId;
            });
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']); // Re-index
            saveUserWishlist($userId, $_SESSION['wishlist']);
            $response = ['success' => true, 'message' => 'Removed from wishlist', 'count' => count($_SESSION['wishlist'])];

        } elseif ($action === 'get_wishlist') {
            $response = ['success' => true, 'wishlist' => $_SESSION['wishlist']];

        } elseif ($action === 'get_count') {
            $response = ['success' => true, 'count' => count($_SESSION['wishlist'])];
            
        } else {
            $response = ['success' => false, 'message' => 'Invalid action'];
        }

        $this->sendJson($response);
    }
    
    private function sendJson($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
