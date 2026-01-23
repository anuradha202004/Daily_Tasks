<?php
/**
 * Wishlist Management
 * Handles adding/removing products from user's wishlist
 */

session_start();
require_once 'data.php';
require_once 'auth.php';

$pageTitle = 'My Wishlist';

// Require login
requireLogin();

// Initialize wishlist in session if not exists
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// Handle add/remove wishlist actions (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    
    if ($action === 'add' && $productId > 0) {
        if (!in_array($productId, $_SESSION['wishlist'])) {
            $_SESSION['wishlist'][] = $productId;
        }
        echo json_encode(['success' => true, 'message' => 'Added to wishlist']);
    } elseif ($action === 'remove' && $productId > 0) {
        $_SESSION['wishlist'] = array_filter($_SESSION['wishlist'], function($id) use ($productId) {
            return $id !== $productId;
        });
        $_SESSION['wishlist'] = array_values($_SESSION['wishlist']); // Re-index array
        echo json_encode(['success' => true, 'message' => 'Removed from wishlist']);
    } elseif ($action === 'get_wishlist') {
        echo json_encode(['wishlist' => $_SESSION['wishlist']]);
    }
    exit;
}

// Get wishlist items with product details
$wishlistItems = [];
if (!empty($_SESSION['wishlist'])) {
    foreach ($_SESSION['wishlist'] as $productId) {
        $product = getProductById($productId);
        if ($product) {
            $wishlistItems[] = $product;
        }
    }
}
?>
<?php include 'header.php'; ?>

    <!-- My Wishlist Page -->
    <section class="container" style="padding: 40px 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 class="section-title">❤️ My Wishlist</h1>
            <div style="background: #ffe8ee; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; color: #dc2626;">
                <?php echo count($wishlistItems); ?> item<?php echo count($wishlistItems) !== 1 ? 's' : ''; ?>
            </div>
        </div>

        <?php if (count($wishlistItems) > 0): ?>
            <div class="products-grid" style="margin-top: 30px;">
                <?php foreach ($wishlistItems as $product): ?>
                    <div class="product-card" style="position: relative;">
                        <!-- Heart Icon -->
                        <div onclick="toggleWishlist(event, <?php echo $product['id']; ?>)" 
                             style="
                                position: absolute;
                                top: 10px;
                                right: 10px;
                                font-size: 24px;
                                cursor: pointer;
                                z-index: 10;
                                background: white;
                                width: 40px;
                                height: 40px;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                                transition: all 0.3s ease;
                             "
                             onmouseover="this.style.transform='scale(1.1)'"
                             onmouseout="this.style.transform='scale(1)'"
                             class="heart-icon"
                             data-product-id="<?php echo $product['id']; ?>">
                            ❤️
                        </div>

                        <div class="product-image"><?php echo $product['emoji']; ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-rating"><?php echo renderStars($product['rating']); ?> <?php echo $product['rating']; ?> (<?php echo $product['reviews']; ?> reviews)</div>
                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                        <div class="product-footer">
                            <span class="stock-info">Stock: <?php echo $product['stock']; ?> units</span>
                            <?php if ($product['stock'] > 0): ?>
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn-view-details" style="flex: 1; text-align: center; padding: 8px 12px; background: #2563eb; color: white; border-radius: 4px; text-decoration: none; font-size: 14px;">
                                    View Details
                                </a>
                            <?php else: ?>
                                <button style="flex: 1; background: #ccc; color: #666; border: none; padding: 8px 12px; border-radius: 4px; cursor: not-allowed;">
                                    Out of Stock
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <!-- Empty Wishlist Message -->
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 60px; margin-bottom: 20px;">🤍</div>
                <h2 style="color: #666; margin-bottom: 10px;">Your Wishlist is Empty</h2>
                <p style="color: #999; margin-bottom: 30px;">Start adding products to your wishlist to save them for later!</p>
                <a href="products.php" class="btn btn-primary" style="display: inline-block; padding: 12px 30px; text-decoration: none;">
                    Explore Products
                </a>
            </div>
        <?php endif; ?>
    </section>

    <script>
        function toggleWishlist(event, productId) {
            event.preventDefault();
            event.stopPropagation();
            
            const heartIcon = event.currentTarget;
            const isLiked = heartIcon.textContent.includes('❤️');
            
            const formData = new FormData();
            formData.append('action', isLiked ? 'remove' : 'add');
            formData.append('product_id', productId);
            
            fetch('<?php echo basename(__FILE__); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (isLiked) {
                        heartIcon.textContent = '🤍';
                        // Reload page to update wishlist
                        setTimeout(() => location.reload(), 300);
                    } else {
                        heartIcon.textContent = '❤️';
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>

<?php include 'footer.php'; ?>
