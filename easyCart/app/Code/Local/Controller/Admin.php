<?php

class Controller_Admin extends Core_Controller {
    
    public function __construct() {
        // Enforce Admin Access
        if (!isLoggedIn() || getCurrentUser()['role'] !== 'admin') {
             $this->redirect('');
             exit;
        }
    }

    public function dashboard() {
        $adminModel = new Model_Admin();
        $data = $adminModel->getDashboardData();
        
        $view = new View_Product('admin/dashboard');
        $view->assign('stats', $data['stats'])
             ->assign('recentOrders', $data['recent_orders'])
             ->assign('title', 'Admin Dashboard');
             
        echo $view->toHtml();
    }
    
    public function importExport() {
        $view = new View_Product('admin/import_export');
        $view->assign('title', 'Import / Export');
        echo $view->toHtml();
    }
    
    // Process Import
    public function processImport() {
        // ...
    }
}
