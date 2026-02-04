<?php

namespace Services;

use Models\Product;
use Models\Category;

/**
 * Product Service
 * Handles product business logic
 */
class ProductService {
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }
    
    /**
     * Get all products
     * @return array
     */
    public function getAllProducts() {
        return $this->productModel->getAllWithAttributes();
    }
    
    /**
     * Get product by ID
     * @param int $id
     * @return array|null
     */
    public function getProduct($id) {
        return $this->productModel->getProductWithAttributes($id);
    }
    
    /**
     * Get products by category
     * @param int $categoryId
     * @return array
     */
    public function getProductsByCategory($categoryId) {
        return $this->productModel->getByCategory($categoryId);
    }
    
    /**
     * Search products
     * @param string $query
     * @return array
     */
    public function searchProducts($query) {
        if (empty(trim($query))) {
            return $this->getAllProducts();
        }
        return $this->productModel->search($query);
    }
    
    /**
     * Get all categories
     * @return array
     */
    public function getAllCategories() {
        return $this->categoryModel->getAllWithAttributes();
    }
    
    /**
     * Get category by ID
     * @param int $id
     * @return array|null
     */
    public function getCategory($id) {
        return $this->categoryModel->getCategoryWithAttributes($id);
    }
    
    /**
     * Get all brands
     * @return array
     */
    public function getAllBrands() {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT DISTINCT brand as name FROM catalog_product_attribute WHERE brand IS NOT NULL");
        $brands = [];
        $i = 1;
        
        while ($row = $stmt->fetch()) {
            $brands[$i] = ['id' => $i, 'name' => $row['name']];
            $i++;
        }
        
        return $brands;
    }
}
