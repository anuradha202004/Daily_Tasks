<?php
// app/Code/Core/Core/Controller.php

class Core_Controller {
    
    /**
     * Constructor - runs on every controller instantiation
     * Checks if logged-in user is still active, logs out if deactivated
     */
    public function __construct() {
        $this->checkUserStatus();
    }
    
    /**
     * Check if current user is active, logout if deactivated
     */
    protected function checkUserStatus() {
        if (isLoggedIn()) {
            $user = getCurrentUser();
            
            // Check if user is deactivated
            if (isset($user['is_active']) && !$user['is_active']) {
                // Log out the deactivated user
                logoutUser();
                
                // Redirect to signin with message
                $this->redirect('signin?message=account_deactivated');
            }
        }
    }
    
    protected function view($viewPath, $data = []) {
        // Updated to support new View class structure if needed, 
        // but keeping compatibility with existing simple file-based views for now
        // or we can implement the View object requirement here.
        
        // For now, let's stick to the existing view rendering to keep the site running
        // but we will eventually replace this with View_Product->toHtml()
        
        // Extract data for the view
        extract($data);
        
        $viewFile = __DIR__ . '/../../../../app/Views/' . $viewPath . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            // Fallback or error
            echo "View not found: $viewFile";
        }
    }

    protected function redirect($url) {
        header("Location: " . URL_ROOT . '/' . ltrim($url, '/'));
        exit;
    }
}
