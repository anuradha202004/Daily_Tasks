<?php

namespace Services;

use Models\Order as OrderModel;
use Models\Product;
use Models\Cart as CartModel;

/**
 * Order Service
 * Handles order business logic
 */
class OrderService {
    private $orderModel;
    private $productModel;
    private $cartModel;
    
    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->productModel = new Product();
        $this->cartModel = new CartModel();
    }
    
    /**
     * Create a new order
     * @param int|null $userId
     * @param array $orderData
     * @param array $items
     * @param bool $deactivateCart
     * @return bool
     */
    public function createOrder($userId, $orderData, $items, $deactivateCart = true) {
        try {
            $this->orderModel->beginTransaction();
            
            // Get user email
            $userEmail = $userId ? $this->getUserEmail($userId) : ($orderData['customer_email'] ?? null);
            
            // Insert sales order
            $orderId = $this->orderModel->createOrder([
                ':increment_id' => $orderData['order_number'],
                ':user_id' => $userId,
                ':subtotal' => $orderData['subtotal'],
                ':tax_amount' => $orderData['tax'],
                ':shipping_amount' => $orderData['shipping_cost'],
                ':discount_amount' => $orderData['discount'],
                ':grand_total' => $orderData['total'],
                ':status' => $orderData['status'],
                ':shipping_method' => $orderData['shipping_method'],
                ':customer_email' => $userEmail
            ]);
            
            // Insert order items
            foreach ($items as $item) {
                $this->orderModel->addOrderItem($orderId, [
                    'product_id' => $item['product']['id'],
                    'sku' => $item['product']['sku'],
                    'name' => $item['product']['name'],
                    'price' => $item['product']['price'],
                    'qty_ordered' => $item['quantity'],
                    'row_total' => $item['itemTotal']
                ]);
                
                // Update stock
                $this->productModel->updateStock($item['product']['id'], $item['quantity']);
            }
            
            // Insert order address
            if (isset($orderData['customer'])) {
                $this->orderModel->addOrderAddress($orderId, $orderData['customer']);
            }
            
            // Deactivate cart
            if ($deactivateCart && isset($_SESSION['cart_id'])) {
                $this->cartModel->deactivateCart($_SESSION['cart_id']);
                unset($_SESSION['cart_id']);
            }
            
            $this->orderModel->commit();
            return true;
        } catch (\Exception $e) {
            $this->orderModel->rollback();
            error_log("Order Creation Failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user email
     * @param int $userId
     * @return string|null
     */
    private function getUserEmail($userId) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }
    
    /**
     * Get user orders
     * @param int $userId
     * @return array
     */
    public function getUserOrders($userId) {
        $orders = $this->orderModel->getOrdersByUser($userId);
        
        // Format orders for display
        $formattedOrders = [];
        foreach ($orders as $order) {
            // Get order items
            $stmt = $this->orderModel->query("SELECT * FROM sales_order_products WHERE order_id = ?", [$order['id']]);
            $items = [];
            foreach ($stmt as $item) {
                $items[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty_ordered'],
                    'price' => $item['price']
                ];
            }
            
            $formattedOrders[] = [
                'id' => $order['id'],
                'order_number' => $order['increment_id'],
                'date' => $order['created_at'],
                'status' => $order['status'] ?? 'Processing',
                'subtotal' => $order['subtotal'],
                'tax' => $order['tax_amount'],
                'shipping' => $order['shipping_amount'],
                'shipping_method_name' => $order['shipping_method'] ?? 'Standard',
                'total' => $order['grand_total'],
                'items' => $items,
                'customer' => []
            ];
        }
        
        return $formattedOrders;
    }
}
