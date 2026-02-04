<?php

namespace Services;

use Models\Cart as CartModel;
use Models\Product;

/**
 * Cart Service
 * Handles cart business logic
 */
class CartService {
    private $cartModel;
    private $productModel;
    
    public function __construct() {
        $this->cartModel = new CartModel();
        $this->productModel = new Product();
    }
    
    /**
     * Sync cart to database
     * @param string $sessionId
     * @param int|null $userId
     * @param array $cartItems
     */
    public function syncCartToDb($sessionId, $userId, $cartItems) {
        try {
            $cartId = isset($_SESSION['cart_id']) ? $_SESSION['cart_id'] : null;
            
            if (!$cartId) {
                $cart = $this->cartModel->getActiveCart($sessionId, $userId);
                $cartId = $cart ? $cart['id'] : null;
            }
            
            if (!$cartId) {
                if (empty($cartItems)) return;
                $cartId = $this->cartModel->createCart($sessionId, $userId);
                $_SESSION['cart_id'] = $cartId;
            } else {
                $_SESSION['cart_id'] = $cartId;
                if ($userId) {
                    $this->cartModel->updateCartUser($cartId, $userId);
                }
            }
            
            $this->cartModel->beginTransaction();
            $this->cartModel->clearCartItems($cartId);
            
            if (!empty($cartItems)) {
                foreach ($cartItems as $item) {
                    $this->cartModel->addItem($cartId, $item['product_id'], $item['quantity']);
                }
            }
            
            $this->cartModel->commit();
        } catch (\Exception $e) {
            $this->cartModel->rollback();
            error_log("Cart Sync Failed: " . $e->getMessage());
        }
    }
    
    /**
     * Load cart from database
     * @param string $sessionId
     * @param int|null $userId
     * @return array
     */
    public function loadCartFromDb($sessionId, $userId = null) {
        try {
            $cart = $this->cartModel->getActiveCart($sessionId, $userId);
            
            if ($cart) {
                $_SESSION['cart_id'] = $cart['id'];
                $items = $this->cartModel->getCartItems($cart['id']);
                
                $cartArray = [];
                foreach ($items as $item) {
                    $cartArray[$item['product_id']] = [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity']
                    ];
                }
                
                return $cartArray;
            }
        } catch (\Exception $e) {
            error_log("Cart Load Failed: " . $e->getMessage());
        }
        
        return [];
    }
    
    /**
     * Merge guest cart with user cart
     * @param array $guestCart
     */
    public function mergeGuestCart($guestCart) {
        if (!empty($guestCart)) {
            $sessionCart = $this->loadCartFromDb(session_id(), $_SESSION['user_id']);
            
            foreach ($guestCart as $productId => $guestItem) {
                if (isset($sessionCart[$productId])) {
                    $sessionCart[$productId]['quantity'] += $guestItem['quantity'];
                } else {
                    $sessionCart[$productId] = $guestItem;
                }
            }
            
            $_SESSION['cart'] = $sessionCart;
            $this->syncCartToDb(session_id(), $_SESSION['user_id'], $sessionCart);
        } else {
            $_SESSION['cart'] = $this->loadCartFromDb(session_id(), $_SESSION['user_id']);
        }
    }
    
    /**
     * Calculate cart summary
     * @param array $cart
     * @return array
     */
    public function calculateCartSummary($cart) {
        $subtotal = 0;
        $discount = 0;
        $itemsWithDetails = [];
        
        foreach ($cart as $productId => $item) {
            $product = $this->productModel->getProductWithAttributes($productId);
            
            if ($product) {
                $quantity = $item['quantity'];
                $itemTotal = $product['price'] * $quantity;
                $itemDiscount = $this->calculateBulkDiscount($product['price'], $quantity);
                
                $subtotal += $itemTotal;
                $discount += $itemDiscount;
                
                $itemsWithDetails[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'itemTotal' => $itemTotal,
                    'itemDiscount' => $itemDiscount
                ];
            }
        }
        
        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'items' => $itemsWithDetails
        ];
    }
    
    /**
     * Calculate bulk discount
     * @param float $price
     * @param int $quantity
     * @return float
     */
    private function calculateBulkDiscount($price, $quantity) {
        if ($quantity > 0) {
            $itemTotal = $price * $quantity;
            return $itemTotal * ($quantity / 100);
        }
        return 0;
    }
}
