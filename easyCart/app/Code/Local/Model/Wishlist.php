<?php

class Model_Wishlist extends Core_Model {

    public function load($userId) {
        $sql = "SELECT 
                    e.entity_id as id, e.sku, e.name, e.created_at,
                    COALESCE(a.price, 0) as price, 
                    a.description, a.color, a.size, a.brand, 
                    COALESCE(a.stock, 0) as stock, 
                    COALESCE(a.emoji, '📦') as emoji,
                    4.5 as rating, 10 as reviews,
                    (
                        SELECT image_path 
                        FROM catalog_product_image 
                        WHERE product_id = e.entity_id 
                        ORDER BY is_primary DESC, id ASC 
                        LIMIT 1
                    ) as image
                FROM wishlist w
                JOIN catalog_product_entity e ON w.product_id = e.entity_id
                LEFT JOIN catalog_product_attribute a ON e.entity_id = a.product_id
                WHERE w.user_id = :uid
                ORDER BY w.id DESC";
        return $this->db->fetchAll($sql, ['uid' => $userId]);
    }
    
    public function addItem($userId, $productId) {
        $sql = "INSERT INTO wishlist (user_id, product_id) VALUES (:uid, :pid) ON CONFLICT DO NOTHING";
        // Need to check DB type for "ON CONFLICT" or "INSERT IGNORE"
        // Assuming compatible SQL based on previous files. 
        // If MySQL: INSERT IGNORE INTO ...
        // If PGSQL: INSERT INTO ... ON CONFLICT (user_id, product_id) DO NOTHING
        
        // Use generic approach: Check existence first
        $check = $this->db->fetchOne("SELECT 1 FROM wishlist WHERE user_id = :uid AND product_id = :pid", ['uid' => $userId, 'pid' => $productId]);
        if (!$check) {
            $this->db->query("INSERT INTO wishlist (user_id, product_id) VALUES (:uid, :pid)", ['uid' => $userId, 'pid' => $productId]);
        }
    }
    
    public function removeItem($userId, $productId) {
        $sql = "DELETE FROM wishlist WHERE user_id = :uid AND product_id = :pid";
        $this->db->query($sql, ['uid' => $userId, 'pid' => $productId]);
    }
    
    /**
     * Get count of wishlist items for a user
     * @param int $userId
     * @return int
     */
    public function getCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM wishlist WHERE user_id = :uid";
        $result = $this->db->fetchOne($sql, ['uid' => $userId]);
        return (int)($result['count'] ?? 0);
    }
    
    /**
     * Get array of product IDs in user's wishlist
     * @param int $userId
     * @return array
     */
    public function getProductIds($userId) {
        $sql = "SELECT product_id FROM wishlist WHERE user_id = :uid";
        $results = $this->db->fetchAll($sql, ['uid' => $userId]);
        return array_map(function($row) {
            return (int)$row['product_id'];
        }, $results);
    }
}
