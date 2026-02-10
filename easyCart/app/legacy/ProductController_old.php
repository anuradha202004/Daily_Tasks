<?php

class ProductController extends Controller {
    public function index() {
        // Access global variables from data.php
        global $products, $categories, $brands;

        // Ensure variables are available
        $productsList = $products ?? [];
        
        // 1. Get Global Price Range for Slider Limits (from logic)
        $allPrices = array_column($productsList, 'price');
        $globalMinPrice = !empty($allPrices) ? floor(min($allPrices)) : 0;
        $globalMaxPrice = !empty($allPrices) ? ceil(max($allPrices)) : 1000;

        // 2. Get Filter Parameters from $_GET
        $selectedCategories = isset($_GET['categories']) ? (is_array($_GET['categories']) ? $_GET['categories'] : [$_GET['categories']]) : [];
        $selectedBrands = isset($_GET['brands']) ? (is_array($_GET['brands']) ? $_GET['brands'] : [$_GET['brands']]) : [];
        
        // Handle single category filter from query string (e.g. ?category=1)
        if (isset($_GET['category']) && empty($selectedCategories)) {
             $selectedCategories = [$_GET['category']];
        }

        $minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : $globalMinPrice;
        $maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : $globalMaxPrice;
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

        // 3. Apply Filters
        $displayProducts = array_filter($productsList, function($product) use ($selectedCategories, $selectedBrands, $minPrice, $maxPrice) {
            // Category Filter
            if (!empty($selectedCategories)) {
                if (!in_array($product['category_id'], $selectedCategories)) {
                    return false;
                }
            }

            // Brand Filter
            if (!empty($selectedBrands)) {
                if (!isset($product['brand_id']) || !in_array($product['brand_id'], $selectedBrands)) {
                    return false;
                }
            }
            
            // Price Filter
            if ($product['price'] < $minPrice || $product['price'] > $maxPrice) {
                return false;
            }
            
            return true;
        });

        // 4. Apply Sorting
        switch ($sortBy) {
            case 'price_asc':
                usort($displayProducts, function($a, $b) { return $a['price'] <=> $b['price']; });
                break;
            case 'price_desc':
                usort($displayProducts, function($a, $b) { return $b['price'] <=> $a['price']; });
                break;
            case 'name_asc':
                usort($displayProducts, function($a, $b) { return strcasecmp($a['name'], $b['name']); });
                break;
            case 'popular':
                // Mock popularity based on reviews
                usort($displayProducts, function($a, $b) { return $b['reviews'] <=> $a['reviews']; });
                break;
            case 'newest':
            default:
                // Already sorted by ID/Created usually, but let's reverse ID for "newest"
                usort($displayProducts, function($a, $b) { return $b['id'] <=> $a['id']; });
                break;
        }

        // Pass data to View
        $data = [
            'title' => 'Products',
            'products' => $displayProducts,
            'categories' => $categories ?? [],
            'brands' => $brands ?? [],
            'filters' => [
                'categories' => $selectedCategories,
                'brands' => $selectedBrands,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'sort' => $sortBy,
                'global_min' => $globalMinPrice,
                'global_max' => $globalMaxPrice
            ]
        ];

        $this->view('product/index', $data);
    }

    public function detail() {
        global $products; // Ensure we access global products
        
        // Get ID from query string usually: ?id=1 or router param
        // For simple GET param:
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        
        // Use helper from data.php
        $product = $id ? getProductById($id) : null;
        
        if ($product) {
            $category = getCategoryById($product['category_id']);
            $brand = getBrandById($product['brand_id']);
            
            // Get related products
            $related = array_slice(getProductsByCategory($product['category_id']), 0, 4);
            
            // Pass data to View
            $data = [
                'title' => $product['name'],
                'product' => $product,
                'category' => $category,
                'brand' => $brand,
                'related' => $related
            ];
            
            $this->view('product/detail', $data);
        } else {
            $this->redirect('products');
        }
    }
    
    public function search() {
        // Handle search
        $query = isset($_GET['q']) ? $_GET['q'] : '';
        // Use global search function from data.php
        $results = searchProducts($query); 
        
        $this->view('product/search', ['products' => $results, 'query' => $query, 'title' => 'Search Results']);
    }
}
