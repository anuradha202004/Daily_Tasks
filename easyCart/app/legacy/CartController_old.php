<?php

class CartController extends Controller {
    public function index() {
        // Load cart from session/DB
        $this->syncCart();

        // Handle POST Requests (Add, Remove, Update)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = isset($_POST['action']) ? $_POST['action'] : null;
            $this->handleActions($action);
            return; // Stop execution after handling AJAX/Form
        }
        
        // Prepare Data for View
        $cartItems = $_SESSION['cart'] ?? [];
        $cartItemsWithDetails = [];
        $subtotal = 0;
        $discount = 0;

        foreach ($cartItems as $productId => $cartItem) {
            $product = getProductById($productId);
            if ($product) {
                $itemTotal = $product['price'] * $cartItem['quantity'];
                $subtotal += $itemTotal;
                $discount += calculateBulkDiscount($product['price'], $cartItem['quantity']);
                
                $cartItemsWithDetails[] = [
                    'product' => $product,
                    'quantity' => $cartItem['quantity'],
                    'itemTotal' => $itemTotal
                ];
            }
        }
        
        $total = $subtotal - $discount;

        $data = [
            'title' => 'Shopping Cart',
            'cartItems' => $cartItemsWithDetails,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total
        ];
        
        $this->view('cart/index', $data);
    }
    
    private function syncCart() {
        if (!isset($_SESSION['cart'])) {
            $userId = isLoggedIn() ? getCurrentUser()['id'] : null;
            $dbCart = loadUserCart($userId);
            $_SESSION['cart'] = $dbCart;
        }
    }

    private function getUserId() {
        return isLoggedIn() ? getCurrentUser()['id'] : null;
    }

    private function handleActions($action) {
        $userId = $this->getUserId();
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
        
        if ($action === 'add' && isset($_POST['product_id'])) {
            $this->addToCart($userId);
        } elseif ($action === 'remove' && isset($_POST['product_id'])) {
            $this->removeFromCart($userId, $isAjax);
        } elseif ($action === 'update' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
            $this->updateCart($userId, $isAjax);
        } elseif ($action === 'clear') {
            $this->clearCart($userId, $isAjax);
        } elseif ($action === 'get_count') {
            $this->sendJson(['success' => true, 'cartCount' => count($_SESSION['cart'])]);
        }
    }

    private function addToCart($userId) {
        $productId = intval($_POST['product_id']);
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        $product = getProductById($productId);
        
        if (!$product || $product['stock'] <= 0) {
            $this->sendJson(['success' => false, 'message' => 'Product not available']);
        }
        
        if (isset($_SESSION['cart'][$productId])) {
             $this->sendJson([
                'success' => false, 
                'alreadyInCart' => true,
                'message' => 'Product already in cart',
                'productName' => $product['name']
            ]);
        }
        
        $_SESSION['cart'][$productId] = [
            'product_id' => $productId,
            'quantity' => min($quantity, $product['stock'])
        ];
        
        saveUserCart($userId, $_SESSION['cart']);
        
        $this->sendJson([
            'success' => true,
            'message' => 'Product added to cart',
            'productName' => $product['name'],
            'cartCount' => count($_SESSION['cart'])
        ]);
    }

    private function removeFromCart($userId, $isAjax) {
        $productId = intval($_POST['product_id']);
        unset($_SESSION['cart'][$productId]);
        saveUserCart($userId, $_SESSION['cart']);
        
        if ($isAjax) {
            $summary = calculateCartSummary();
            $this->sendJson(array_merge(['success' => true, 'cartCount' => count($_SESSION['cart'])], $summary));
        } else {
             $this->redirect('cart');
        }
    }

    private function updateCart($userId, $isAjax) {
        $productId = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $product = getProductById($productId);
            if ($product && $quantity <= $product['stock']) {
                if (!isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId] = ['product_id' => $productId];
                }
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            }
        }
        saveUserCart($userId, $_SESSION['cart']);
        
        if ($isAjax) {
            $summary = calculateCartSummary();
            $this->sendJson(array_merge(['success' => true, 'cartCount' => count($_SESSION['cart'])], $summary));
        } else {
            $this->redirect('cart');
        }
    }

    private function clearCart($userId, $isAjax) {
        $_SESSION['cart'] = [];
        saveUserCart($userId, $_SESSION['cart']);
        if ($isAjax) {
            $this->sendJson(['success' => true, 'cartCount' => 0]);
        } else {
            $this->redirect('cart');
        }
    }

    private function sendJson($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
