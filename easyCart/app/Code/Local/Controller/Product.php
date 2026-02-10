<?php

class Controller_Product extends Core_Controller {
    
    public function index() {
        // Collect Filters
        $filters = [
            'category_id' => $_GET['category'] ?? ($_GET['categories'] ?? null),
            'sort' => $_GET['sort'] ?? 'newest',
            'min_price' => $_GET['min_price'] ?? 0,
            'max_price' => $_GET['max_price'] ?? 10000
        ];

        // 1. Get Product Collection
        $collection = new Model_Product_Collection();
        
        // Apply Filters
        if (!empty($filters['category_id'])) {
            // Support array or single value
            $catId = is_array($filters['category_id']) ? $filters['category_id'][0] : $filters['category_id'];
            $collection->addFilter('category_id', $catId);
        }
        
        // Apply Sorting
        switch ($filters['sort']) {
            case 'price_asc':
                $collection->setOrder('price', 'ASC');
                break;
            case 'price_desc':
                $collection->setOrder('price', 'DESC');
                break;
            case 'name_asc':
                $collection->setOrder('name', 'ASC');
                break;
            case 'newest':
            default:
                $collection->setOrder('entity_id', 'DESC');
                break;
        }

        $products = $collection->getData();

        // 2. Prepare View Data
        // We still need global categories/brands for the sidebar if we want to keep current UI
        // In a real MVC, we would have Block/Sidebar or similar, but for now we fetch via data.php helpers or models
        // Using existing global functions for now to avoid breaking too much logic at once until Category/Brand models are ready
        
        global $categories, $brands; // Still relying on data.php for these specific lists

        $view = new View_Product('product/index');
        $view->assign('title', 'Products')
             ->assign('products', $products)
             ->assign('categories', $categories ?? [])
             ->assign('brands', $brands ?? [])
             ->assign('filters', [
                 'categories' => is_array($filters['category_id']) ? $filters['category_id'] : [$filters['category_id']],
                 'brands' => [], // Needs implementation
                 'min_price' => $filters['min_price'],
                 'max_price' => $filters['max_price'],
                 'sort' => $filters['sort'],
                 'global_min' => 0,
                 'global_max' => 10000
             ]);

        echo $view->toHtml();
    }

    public function detail($slug = null) {
        $productId = null;
        
        if ($slug) {
             // Handle Slug (e.g. yeti-water-bottle)
             // Convert back to Name? Or search by LOWER(name)
             $searchName = str_replace('-', ' ', $slug);
             
             // Ideally use a dedicated method in Resource
             $resource = new Model_Product_Resource();
             // Since we don't have a getBySlug method, let's query directly or add one
             // Adding ad-hoc query here via DB instance for speed, but should be in Resource
             $db = Core_Database::getInstance();
             // Replace hyphens with spaces in both DB name and search term for robust matching
             $sql = "SELECT entity_id FROM catalog_product_entity WHERE REPLACE(LOWER(name), '-', ' ') = :name LIMIT 1";
             $searchName = str_replace('-', ' ', $slug);
             $row = $db->fetchOne($sql, ['name' => strtolower($searchName)]);
             $productId = $row ? $row['entity_id'] : null;
        } else {
             $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        }

        if (!$productId) {
            $this->redirect('products');
            return;
        }

        $model = new Model_Product();
        $product = $model->load($productId);

        if (!$product) {
            $this->redirect('products');
            return;
        }
        
        // ... (rest of logic: categories, related, view)
        
        $categoryModel = new Model_Category();
        $categories = $categoryModel->getCategories(); // For menu/breadcrumb

        // Related Products (same category)
        $collection = $model->getCollection();
        $collection->addFilter('category_id', $product['category_id']);
        $relatedProducts = $collection->getData();
        // Filter out current product
        $relatedProducts = array_filter($relatedProducts, function($p) use ($productId) {
            return $p['id'] != $productId;
        });
        $relatedProducts = array_slice($relatedProducts, 0, 4);

        // Derive current category from list
        $currentCategory = null;
        if (isset($product['category_id'])) {
            // Find category where id matches
            $found = array_filter($categories, function($c) use ($product) {
                return $c['id'] == $product['category_id'];
            });
            $currentCategory = !empty($found) ? reset($found) : ['name' => 'General', 'id' => 0];
        }

        $view = new View_Product('product/detail');
        $view->assign('title', $product['name'])
             ->assign('product', $product)
             ->assign('categories', $categories) // All categories for menu
             ->assign('category', $currentCategory) // Current product category for breadcrumb
             ->assign('related', $relatedProducts); // Legacy view expects 'related'
             
        echo $view->toHtml();
    }
    
    public function search() {
        $query = isset($_GET['q']) ? $_GET['q'] : '';
        // Implement search in Collection
        // $collection->addSearchFilter($query);
        // For now, use existing function or simple query
        global $products; // Fallback
        $results = searchProducts($query); 
        
        $view = new View_Product('product/search');
        $view->assign('products', $results)
             ->assign('query', $query)
             ->assign('title', 'Search Results');
             
        echo $view->toHtml();
    }
}
