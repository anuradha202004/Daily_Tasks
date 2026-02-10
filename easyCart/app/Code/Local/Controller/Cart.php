<?php

class Controller_Cart extends Core_Controller {
    
    public function index() {
        $cartModel = new Model_Cart();

        // Handle POST actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? null;
            $this->handlePost($cartModel, $action);
            return;
        }

        // Prepare View
        $cartItems = $cartModel->load();
        $totals = $cartModel->getTotals();
        
        // Transform for View Compatibility (matching existing view structure)
        $viewItems = [];
        foreach ($cartItems as $item) {
            $viewItems[] = [
                'product' => $item['product'],
                'quantity' => $item['quantity'],
                'itemTotal' => $item['product']['price'] * $item['quantity']
            ];
        }

        $view = new View_Product('cart/index'); // Reusing View_Product or Generic View for now. 
        // Note: Cart view expects $data array with keys
        $view->assign('title', 'Shopping Cart')
             ->assign('cartItems', $viewItems)
             ->assign('subtotal', $totals['subtotal'])
             ->assign('discount', $totals['discount'])
             ->assign('total', $totals['total']);

        echo $view->toHtml();
    }
    
    private function handlePost($cartModel, $action) {
        $response = ['success' => false, 'message' => 'Invalid action'];
        
        try {
            if ($action === 'add') {
                $productId = (int)$_POST['product_id'];
                $qty = (int)($_POST['quantity'] ?? 1);
                $cartModel->addItem($productId, $qty);
                
                $product = (new Model_Product())->load($productId);
                $response = [
                    'success' => true, 
                    'message' => 'Product added to cart',
                    'productName' => $product['name'],
                    // 'cartCount' => count($cartModel->getItems()) // Force reload to get count
                ];
            } elseif ($action === 'update') {
                $productId = (int)$_POST['product_id'];
                $qty = (int)$_POST['quantity'];
                $cartModel->updateItem($productId, $qty);
                $response = ['success' => true, 'message' => 'Cart updated'];
                
            } elseif ($action === 'remove') {
                $productId = (int)$_POST['product_id'];
                $cartModel->removeItem($productId);
                $response = ['success' => true, 'message' => 'Item removed'];
                
            } elseif ($action === 'clear') {
                $cartModel->clear();
                $response = ['success' => true, 'cartCount' => 0];
            }
            
            // Get updated totals for AJAX response (match JS key expectations)
            $totals = $cartModel->getTotals();
            $response['formattedSubtotal'] = formatPrice($totals['subtotal']);
            $response['formattedDiscount'] = formatPrice($totals['discount']);
            $response['formattedTotal']    = formatPrice($totals['total']);
            
            // Get updated count (sum of quantities to match header logic)
            $items = $cartModel->load(); // Refresh
            $totalQty = 0;
            foreach ($items as $item) {
                $totalQty += $item['quantity'];
            }
            $response['cartCount'] = $totalQty;
            
            // Only return JSON if AJAX
            // if ($isAjax) ... 
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
            
        } catch (Exception $e) {
             header('Content-Type: application/json');
             echo json_encode(['success' => false, 'message' => $e->getMessage()]);
             exit;
        }
    }
}
