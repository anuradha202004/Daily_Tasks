<?php

class HomeController extends Controller {
    public function index() {
        // Access global variables from data.php for compatibility
        global $products, $categories;
        
        // Logic from old index.php
        $pageTitle = 'Home';
        
        // Get featured products (first 4 products)
        // Ensure $products is not null
        $productsList = $products ?? [];
        $featuredProducts = array_slice($productsList, 0, 4, true);

        // Prepare data for the view
        $data = [
            'pageTitle' => $pageTitle,
            'featuredProducts' => $featuredProducts,
            'products' => $productsList,
            'categories' => $categories ?? []
        ];
        
        // Load the view
        $this->view('home/index', $data);
    }
}
