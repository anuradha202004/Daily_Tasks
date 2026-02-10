<?php

class Model_Admin extends Core_Model {
    protected $resource;

    public function __construct() {
        parent::__construct();
        $this->resource = new Model_Admin_Resource();
    }

    public function getDashboardData() {
        $stats = $this->resource->getDashboardStats();
        $recentOrders = $this->resource->getRecentOrders();
        
        // Format Currency
        $stats['sales_formatted'] = '$' . number_format($stats['sales']['total'], 2); // Using $ as per default, helper usage better
        
        return [
            'stats' => $stats,
            'recent_orders' => $recentOrders
        ];
    }
    
    // Import/Export Logic
    public function exportProducts() {
        // ... logic
    }
}
