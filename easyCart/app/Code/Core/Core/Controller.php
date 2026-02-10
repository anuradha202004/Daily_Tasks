<?php
// app/Code/Core/Core/Controller.php

class Core_Controller {
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
