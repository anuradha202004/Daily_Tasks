<?php

class Model_Wishlist extends Core_Model {

    public function load($userId) {
        $sql = "SELECT p.*, w.user_id 
                FROM wishlist w
                JOIN catalog_product_entity p ON w.product_id = p.entity_id
                WHERE w.user_id = :uid";
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
}
