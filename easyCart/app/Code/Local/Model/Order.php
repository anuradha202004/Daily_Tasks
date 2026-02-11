<?php

class Model_Order extends Core_Model {

    public function createOrder($userId, $orderData, $items) {
        // Reuse logic from data.php but within a model context
        // This should probably be in Model_Order_Service or Resource
        // For now, implementing directly here using DB helper for transactions
        
        $pdo = $this->db->getConnection();
        
        try {
            $pdo->beginTransaction();
            
            // 1. Insert Sales Order
            // Use query builder ideally, but simple SQL for now
            $sql = "INSERT INTO sales_order 
                    (increment_id, user_id, subtotal, tax_amount, shipping_amount, discount_amount, grand_total, status, shipping_method, customer_email, created_at)
                    VALUES 
                    (:increment_id, :user_id, :subtotal, :tax_amount, :shipping_amount, :discount_amount, :grand_total, :status, :shipping_method, :customer_email, NOW())";
            
            // Note: RETURNING id is nice but not all DBs support it equally (MySQL vs PG). Assuming easyCart uses PGSQL (based on previous files)
            // But if MySQL, we need lastInsertId()
            // The existing data.php used RETURNING id which suggests PGSQL
            $sql .= " RETURNING id"; 
            
            $stmt = $pdo->prepare($sql);
            
            // Get user email if not provided in orderData
            if ($userId && empty($orderData['customer_email'])) {
                $userModel = new Model_Customer();
                $userData = $userModel->load($userId);
                $orderData['customer_email'] = $userData['email'];
            }
            
            $stmt->execute([
                ':increment_id' => $orderData['order_number'],
                ':user_id' => $userId,
                ':subtotal' => $orderData['subtotal'],
                ':tax_amount' => $orderData['tax'],
                ':shipping_amount' => $orderData['shipping_cost'],
                ':discount_amount' => $orderData['discount'],
                ':grand_total' => $orderData['total'],
                ':status' => $orderData['status'],
                ':shipping_method' => $orderData['shipping_method'],
                ':customer_email' => $orderData['customer_email']
            ]);
            
            $orderId = $stmt->fetchColumn();
            
            // 2. Insert Items
            $itemSql = "INSERT INTO sales_order_products (order_id, product_id, sku, name, price, qty_ordered, row_total)
                        VALUES (:order_id, :product_id, :sku, :name, :price, :qty_ordered, :row_total)";
            $itemStmt = $pdo->prepare($itemSql);
            
            foreach ($items as $item) {
                 $itemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product']['id'],
                    ':sku' => $item['product']['sku'],
                    ':name' => $item['product']['name'],
                    ':price' => $item['product']['price'],
                    ':qty_ordered' => $item['quantity'],
                    ':row_total' => $item['product']['price'] * $item['quantity']
                ]);
                
                // Update Stock logic (omitted or handled by trigger/another call)
                // $this->updateStock($item['product']['id'], $item['quantity']);
            }
            
            // 3. Addresses (Shipping/Billing)
            // Similar logic...
            if (isset($orderData['customer'])) {
                $cust = $orderData['customer'];
                $addrSql = "INSERT INTO sales_order_address (parent_id, address_type, firstname, lastname, email, telephone, street, city, region, postcode)
                            VALUES (:pid, 'shipping', :fn, :ln, :email, :tel, :str, :city, :reg, :zip)";
                $stmt = $pdo->prepare($addrSql);
                $stmt->execute([
                    ':pid' => $orderId,
                    ':fn' => $cust['first_name'],
                    ':ln' => $cust['last_name'],
                    ':email' => $cust['email'],
                    ':tel' => $cust['phone'],
                    ':str' => $cust['address'],
                    ':city' => $cust['city'],
                    ':reg' => $cust['state'],
                    ':zip' => $cust['zip']
                ]);
            }
            
            $pdo->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function loadByIncrementId($incrementId) {
        $sql = "SELECT * FROM sales_order WHERE increment_id = :id";
        $order = $this->db->fetchOne($sql, ['id' => $incrementId]);
        
        if ($order) {
            // Load Items
            $itemSql = "SELECT * FROM sales_order_products WHERE order_id = :oid";
            $order['items'] = $this->db->fetchAll($itemSql, ['oid' => $order['id']]);
            
            // Load Address (Shipping)
            $addrSql = "SELECT * FROM sales_order_address WHERE parent_id = :oid AND address_type = 'shipping'";
            $order['shipping_address'] = $this->db->fetchOne($addrSql, ['oid' => $order['id']]);
        }
        return $order;
    }
    
    public function getLatestOrderByUserId($userId) {
        $sql = "SELECT increment_id FROM sales_order WHERE user_id = :uid ORDER BY created_at DESC LIMIT 1";
        $row = $this->db->fetchOne($sql, ['uid' => $userId]);
        if ($row) {
            return $this->loadByIncrementId($row['increment_id']);
        }
        return null;
    }
    
    public function getCollection() {
        // Return collection object for orders
        // return new Model_Order_Collection();
    }
    
    public function getOrdersByUserId($userId) {
        $sql = "SELECT * FROM sales_order WHERE user_id = :uid ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, ['uid' => $userId]);
    }

    public function cancelOrder($orderId) {
        $sql = "UPDATE sales_order SET status = 'cancelled' WHERE id = :id";
        return $this->db->query($sql, ['id' => $orderId]);
    }
}
