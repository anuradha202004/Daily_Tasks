<?php

class Model_Cart extends Core_Model {
    protected $resource;
    protected $items = [];

    public function __construct() {
        parent::__construct();
        $this->resource = new Model_Cart_Resource();
    }

    public function load($id = null) {
        $userId = isLoggedIn() ? getCurrentUser()['id'] : null;
        $sessionId = getGuestSessionId();
        
        $usageItems = $this->resource->loadUserCart($userId, $sessionId);
        
        // Re-hydrate full product data
        $this->items = [];
        $modelProduct = new Model_Product();
        
        foreach ($usageItems as $item) {
            $product = $modelProduct->load($item['product_id']);
            if ($product) {
                // Attach quantity relevant to cart
                $product['cart_qty'] = $item['quantity'];
                
                $this->items[$item['product_id']] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'product' => $product
                ];
            }
        }
        return $this->items;
    }

    public function addItem($productId, $qty) {
        // Validation: user active state
        if (isLoggedIn()) {
             $user = getCurrentUser(); // Assuming this returns array with 'is_active' or similar
             // Implement specific check if 'is_active' field exists
             // user details could be part of session
        }

        $userId = isLoggedIn() ? getCurrentUser()['id'] : null;
        $sessionId = getGuestSessionId();
        
        // Check stock via Product Model
        $modelProduct = new Model_Product();
        $product = $modelProduct->load($productId);
        
        if (!$product || $product['stock'] < $qty) {
            throw new Exception("Product out of stock or requested quantity unavailable.");
        }
        
        // Add to DB
        $this->resource->addItem($userId, $sessionId, $productId, $qty);
    }
    
    public function updateItem($productId, $qty) {
        $userId = isLoggedIn() ? getCurrentUser()['id'] : null;
        $sessionId = getGuestSessionId();
        
        if ($qty <= 0) {
            $this->removeItem($productId);
        } else {
             // Check stock
            $modelProduct = new Model_Product();
            $product = $modelProduct->load($productId);
            if (!$product || $product['stock'] < $qty) {
                throw new Exception("Not enough stock.");
            }
            $this->resource->updateItem($userId, $sessionId, $productId, $qty);
        }
    }

    public function removeItem($productId) {
        $userId = isLoggedIn() ? getCurrentUser()['id'] : null;
        $sessionId = getGuestSessionId();
        $this->resource->removeItem($userId, $sessionId, $productId);
    }

    public function clear() {
        $userId = isLoggedIn() ? getCurrentUser()['id'] : null;
        $sessionId = getGuestSessionId();
        $this->resource->clearCart($userId, $sessionId);
    }
    
    public function getItems() {
        if (empty($this->items)) {
            $this->load();
        }
        return $this->items;
    }

    public function getTotals() {
        $items = $this->getItems();
        $subtotal = 0;

        foreach ($items as $item) {
            $price = $item['product']['price'];
            $qty = $item['quantity'];
            $itemTotal = $price * $qty;
            $subtotal += $itemTotal;
        }
        
        // Tiered bulk discount based on order subtotal
        $discount = 0;
        if ($subtotal >= 1000) {
            $discount = $subtotal * 0.15; // 15% off for orders $1000+
        } elseif ($subtotal >= 500) {
            $discount = $subtotal * 0.10; // 10% off for orders $500+
        } elseif ($subtotal >= 100) {
            $discount = $subtotal * 0.05; // 5% off for orders $100+
        }
        
        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $subtotal - $discount
        ];
    }
}
