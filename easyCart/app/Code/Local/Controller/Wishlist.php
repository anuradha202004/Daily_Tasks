<?php

class Controller_Wishlist extends Core_Controller {
    
    public function __construct() {
        if (!isLoggedIn()) {
            $this->redirect('signin');
            exit;
        }
    }

    public function index() {
        $userId = getCurrentUser()['id'];
        $wishlistModel = new Model_Wishlist();
        $items = $wishlistModel->load($userId);
        
        // Transform items? Model returns product data directly via JOIN
        // Ensure image paths are correct via View/Helper
        
        $view = new View_Product('wishlist/index');
        $view->assign('wishlistItems', $items)
             ->assign('title', 'My Wishlist');
        echo $view->toHtml();
    }
    
    public function add() {
        // Logic for adding to wishlist
        $productId = $_GET['id'] ?? null;
        if ($productId) {
            $userId = getCurrentUser()['id'];
            $wishlistModel = new Model_Wishlist();
            $wishlistModel->addItem($userId, $productId);
            // Flash message?
        }
        $this->redirect('wishlist');
    }
    
    public function remove() {
        $productId = $_GET['id'] ?? null;
        if ($productId) {
            $userId = getCurrentUser()['id'];
            $wishlistModel = new Model_Wishlist();
            $wishlistModel->removeItem($userId, $productId);
        }
        $this->redirect('wishlist');
    }
}
