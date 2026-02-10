<?php

class Model_Admin_Resource extends Core_Model {

    public function getDashboardStats() {
        $stats = [];
        
        $stats['sales'] = $this->db->fetchOne("SELECT SUM(grand_total) as total FROM sales_order WHERE status IN ('processing', 'completed')");
        $stats['orders'] = $this->db->fetchOne("SELECT COUNT(*) as count FROM sales_order");
        $stats['customers'] = $this->db->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
        $stats['products'] = $this->db->fetchOne("SELECT COUNT(*) as count FROM catalog_product_entity");
        $stats['low_stock'] = $this->db->fetchOne("SELECT COUNT(stock) as count FROM catalog_product_attribute WHERE stock < 5");
        
        // Ensure values
        $stats['sales']['total'] = $stats['sales']['total'] ?? 0;
        
        return $stats;
    }

    public function getRecentOrders($limit = 5) {
        $sql = "SELECT o.increment_id, o.created_at, o.customer_email, o.grand_total, o.status, u.name as customer_name 
                FROM sales_order o 
                LEFT JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC LIMIT :limit";
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
}
