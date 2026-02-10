<?php

class Controller_Home extends Core_Controller {
    
    public function index() {
        // Fetch All Products
        $model = new Model_Product();
        $collection = $model->getCollection();
        $products = $collection->getData();
        
        // Simple "Featured" selection (first 4 items)
        $featured = array_slice($products, 0, 4); 
        
        // Fetch Categories
        $categoryModel = new Model_Category();
        $categories = $categoryModel->getCategories();
        
        $view = new View_Product('home/index');
        $view->assign('title', 'Welcome to EasyCart')
             ->assign('featuredProducts', $featured)
             ->assign('products', $products) 
             ->assign('categories', $categories);
             
        echo $view->toHtml();
    }
}
