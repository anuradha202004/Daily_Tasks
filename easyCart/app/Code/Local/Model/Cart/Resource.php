<?php

class Model_Cart_Resource extends Core_Model {
    protected $tableName = 'checkout_cart';

    public function loadUserCart($userId, $sessionId) {
        if ($userId) {
            $sql = "SELECT product_id, quantity FROM checkout_cart WHERE user_id = :uid ORDER BY id DESC";
            $params = ['uid' => $userId];
        } else {
            $sql = "SELECT product_id, quantity FROM checkout_cart WHERE session_id = :sid ORDER BY id DESC";
            $params = ['sid' => $sessionId];
        }
        return $this->db->fetchAll($sql, $params);
    }

    public function addItem($userId, $sessionId, $productId, $qty) {
        // Check if item exists
        if ($userId) {
            $checkSql = "SELECT 1 FROM checkout_cart WHERE user_id = :uid AND product_id = :pid";
            $params = ['uid' => $userId, 'pid' => $productId];
        } else {
            $checkSql = "SELECT 1 FROM checkout_cart WHERE session_id = :sid AND product_id = :pid";
            $params = ['sid' => $sessionId, 'pid' => $productId];
        }
        
        $exists = $this->db->fetchOne($checkSql, $params);
        
        if ($exists) {
            // Update Existing
            if ($userId) {
                $sql = "UPDATE checkout_cart SET quantity = quantity + :qty, updated_at = NOW() WHERE user_id = :uid AND product_id = :pid";
                $params = ['qty' => $qty, 'uid' => $userId, 'pid' => $productId];
            } else {
                $sql = "UPDATE checkout_cart SET quantity = quantity + :qty, updated_at = NOW() WHERE session_id = :sid AND product_id = :pid";
                $params = ['qty' => $qty, 'sid' => $sessionId, 'pid' => $productId];
            }
        } else {
            // Insert New
            if ($userId) {
                $sql = "INSERT INTO checkout_cart (user_id, product_id, quantity, updated_at) VALUES (:uid, :pid, :qty, NOW())";
                $params = ['uid' => $userId, 'pid' => $productId, 'qty' => $qty];
            } else {
                $sql = "INSERT INTO checkout_cart (session_id, product_id, quantity, updated_at) VALUES (:sid, :pid, :qty, NOW())";
                $params = ['sid' => $sessionId, 'pid' => $productId, 'qty' => $qty];
            }
        }
        $this->db->query($sql, $params);
    }
    
    // Updates quantity directly (not increment)
    public function updateItem($userId, $sessionId, $productId, $qty) {
        if ($userId) {
            $sql = "UPDATE checkout_cart SET quantity = :qty, updated_at = NOW() WHERE user_id = :uid AND product_id = :pid";
            $params = ['qty' => $qty, 'uid' => $userId, 'pid' => $productId];
        } else {
            $sql = "UPDATE checkout_cart SET quantity = :qty, updated_at = NOW() WHERE session_id = :sid AND product_id = :pid";
            $params = ['qty' => $qty, 'sid' => $sessionId, 'pid' => $productId];
        }
        $this->db->query($sql, $params);
    }

    public function removeItem($userId, $sessionId, $productId) {
        if ($userId) {
            $sql = "DELETE FROM checkout_cart WHERE user_id = :uid AND product_id = :pid";
            $params = ['uid' => $userId, 'pid' => $productId];
        } else {
            $sql = "DELETE FROM checkout_cart WHERE session_id = :sid AND product_id = :pid";
            $params = ['sid' => $sessionId, 'pid' => $productId];
        }
        $this->db->query($sql, $params);
    }

    public function clearCart($userId, $sessionId) {
        if ($userId) {
            $sql = "DELETE FROM checkout_cart WHERE user_id = :uid";
            $params = ['uid' => $userId];
        } else {
            $sql = "DELETE FROM checkout_cart WHERE session_id = :sid";
            $params = ['sid' => $sessionId];
        }
        $this->db->query($sql, $params);
    }
}
