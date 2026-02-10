<?php

class Model_Product extends Core_Model {
    protected $resource;

    public function __construct() {
        parent::__construct();
        $this->resource = new Model_Product_Resource();
    }

    public function load($id) {
        $data = $this->resource->getProductById($id);
        if ($data) {
            // Apply business logic / data normalization
            $data['price'] = (float)$data['price'];
            $data['stock'] = (int)$data['stock'];
            $data['rating'] = 4.5; // Mock data from previous implementation
            $data['reviews'] = 10;
            $data['emoji'] = $data['emoji'] ?? '📦';
            
            // Image fallback logic
            if (empty($data['image'])) {
                $data['image'] = 'img/products/laptop.png';
            }
            
            // Ensure no public/ prefix issue here too (defensive programming)
            if (strpos($data['image'], 'http') !== 0) {
                 $data['image'] = preg_replace('/^(\\/)?public\\//', '', $data['image']);
            }
            
            return $data;
        }
        return null;
    }

    public function getCollection() {
        return new Model_Product_Collection();
    }
}
