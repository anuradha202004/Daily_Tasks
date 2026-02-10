<?php

class Controller_Wishlist extends Core_Controller {
    
    public function __construct() {
        parent::__construct(); // Call parent for deactivation check
        
        if (!isLoggedIn()) {
            // If AJAX request, return JSON error
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Please login']);
                exit;
            }
            // Otherwise redirect
            $this->redirect('signin');
            exit;
        }
    }

    public function index() {
        // Handle AJAX POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAjax();
            return;
        }
        
        // Regular page view
        $userId = getCurrentUser()['id'];
        $wishlistModel = new Model_Wishlist();
        $items = $wishlistModel->load($userId);
        
        $view = new View_Product('wishlist/index');
        $view->assign('wishlistItems', $items)
             ->assign('title', 'My Wishlist');
        echo $view->toHtml();
    }
    
    protected function handleAjax() {
        header('Content-Type: application/json');
        
        try {
            $action = $_POST['action'] ?? '';
            $userId = getCurrentUser()['id'];
            $wishlistModel = new Model_Wishlist();
            
            switch ($action) {
                case 'add':
                    $productId = (int)($_POST['product_id'] ?? 0);
                    if ($productId) {
                        $wishlistModel->addItem($userId, $productId);
                        // Sync session
                        if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
                        if (!in_array($productId, $_SESSION['wishlist'])) {
                            $_SESSION['wishlist'][] = $productId;
                        }
                        $count = $wishlistModel->getCount($userId);
                        echo json_encode(['success' => true, 'count' => $count]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
                    }
                    break;
                    
                case 'remove':
                    $productId = (int)($_POST['product_id'] ?? 0);
                    if ($productId) {
                        $wishlistModel->removeItem($userId, $productId);
                        // Sync session
                        if (isset($_SESSION['wishlist'])) {
                            $_SESSION['wishlist'] = array_values(array_diff($_SESSION['wishlist'], [$productId]));
                        }
                        $count = $wishlistModel->getCount($userId);
                        echo json_encode(['success' => true, 'count' => $count]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
                    }
                    break;
                    
                case 'get_wishlist':
                    $items = $wishlistModel->getProductIds($userId);
                    echo json_encode(['success' => true, 'wishlist' => $items]);
                    break;
                    
                default:
                    echo json_encode(['success' => false, 'message' => 'Invalid action']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
