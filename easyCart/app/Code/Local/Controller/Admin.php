<?php

class Controller_Admin extends Core_Controller {
    
    public function __construct() {
        parent::__construct();
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
             ->assign('user', getCurrentUser())
             ->assign('title', 'Admin Dashboard');
             
        echo $view->toHtml();
    }
    
    public function importExport() {
        $message = null;
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_csv'])) {
            if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['csv_file']['tmp_name'];
                
                $adminModel = new Model_Admin();
                $result = $adminModel->importProducts($fileTmpPath);
                
                if ($result['success']) {
                    $message = $result['message'];
                } else {
                    $error = $result['message'];
                }
            } else {
                $error = "Please upload a valid CSV file.";
            }
        }

        // Handle Export
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_csv'])) {
            $adminModel = new Model_Admin();
            $products = $adminModel->exportProducts();
            
            if (!empty($products)) {
                $filename = "products_export_" . date('Y-m-d_His') . ".csv";
                
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');
                
                // Header row
                fputcsv($output, array_keys($products[0]));
                
                // Data rows
                foreach ($products as $product) {
                    fputcsv($output, $product);
                }
                
                fclose($output);
                exit;
            } else {
                $error = "No products found to export.";
            }
        }

        $view = new View_Product('admin/import_export');
        $view->assign('title', 'Import / Export')
             ->assign('message', $message)
             ->assign('error', $error);
        echo $view->toHtml();
    }
    
    // Process Import
    public function processImport() {
        // ...
    }
}
