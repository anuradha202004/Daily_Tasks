<?php
session_start();

// Load Application Bootstrap
require_once 'app/bootstrap.php';

// Get product ID from URL
$productId = isset($_GET['id']) ? intval($_GET['id']) : null;
$product = $productId ? getProductById($productId) : null;

// Redirect to products page if product not found
if (!$product) {
    header('Location: products.php');
    exit;
}

$pageTitle = htmlspecialchars($product['name']);
$category = getCategoryById($product['category_id']);
$brand = getBrandById($product['brand_id']);

// Check if user is logged in
$isLoggedIn = isLoggedIn();

// Handle add to cart logic (kept for functionality)
$addToCartMessage = '';
$addToCartError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if ($quantity > 0 && $quantity <= $product['stock']) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = ['product_id' => $productId, 'quantity' => $quantity];
        }
        
        if ($isLoggedIn && isset($_SESSION['user_email'])) {
            saveUserCart($_SESSION['user_email'], $_SESSION['cart']);
        }
        
        $addToCartMessage = 'Product added to cart successfully!';
    } else {
        $addToCartError = 'Invalid quantity. Please check stock availability.';
    }
}
?>
<?php include TEMPLATES_PATH . '/header.php'; ?>
    <script src="public/js/product-detail.js"></script>
    <script src="public/js/wishlist.js"></script>
    <script src="public/js/toast.js"></script>

    <?php if ($addToCartMessage): ?>
        <script>document.addEventListener('DOMContentLoaded', () => showToast('<?php echo htmlspecialchars($addToCartMessage); ?>', 'success', 3500));</script>
    <?php endif; ?>
    <?php if ($addToCartError): ?>
        <script>document.addEventListener('DOMContentLoaded', () => showToast('<?php echo htmlspecialchars($addToCartError); ?>', 'error', 4000));</script>
    <?php endif; ?>

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #10b981;
            --text-main: #111827;
            --text-sub: #6b7280;
            --bg-page: #f9fafb;
            --bg-card: #ffffff;
        }

        body { background: var(--bg-page); }

        .product-page-wrapper {
            padding: 20px 0; /* Reduced from 40px */
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Breadcrumb */
        .breadcrumb {
            margin-bottom: 15px; /* Reduced */
            font-size: 0.85rem;
            color: var(--text-sub);
        }
        .breadcrumb a { color: var(--text-sub); text-decoration: none; transition: 0.2s; }
        .breadcrumb a:hover { color: var(--primary); }

        /* Product Grid */
        .product-main-grid {
            display: grid;
            grid-template-columns: 45% 1fr; /* Image takes slightly less space */
            gap: 40px;
            background: var(--bg-card);
            padding: 25px; /* Reduced padding */
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 40px;
            align-items: start;
        }

        /* Image Section */
        .product-gallery {
            position: sticky;
            top: 20px;
        }
        .main-image-frame {
            background: #f3f4f6;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            aspect-ratio: 4/3; /* Shorter aspect ratio */
            max-height: 400px;
            margin-bottom: 0;
            position: relative;
        }
        .main-image-frame img {
            width: 85%;
            height: 85%;
            object-fit: contain;
            transition: transform 0.3s;
        }
        .main-image-frame:hover img { transform: scale(1.05); }

        /* Details Section */
        .product-details h1 {
            font-size: 1.8rem; /* Slightly smaller */
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 5px;
            line-height: 1.2;
        }

        .meta-row {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: var(--text-sub);
        }
        .rating-badge {
            background: #fef3c7;
            color: #d97706;
            padding: 2px 6px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .price-row {
            display: flex;
            align-items: baseline;
            gap: 15px;
            margin-bottom: 15px; /* Reduced */
        }
        .current-price {
            font-size: 2rem; /* Slightly smaller */
            font-weight: 700;
            color: var(--primary);
        }
        .stock-status {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }
        .in-stock { background: #d1fae5; color: #059669; }
        .out-stock { background: #fee2e2; color: #dc2626; }

        .description-text {
            color: var(--text-sub);
            line-height: 1.5;
            margin-bottom: 15px; /* Reduced */
            padding-bottom: 15px; /* Reduced */
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.95rem;
            display: -webkit-box;
            -webkit-line-clamp: 3; /* Limit visible lines */
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Options */
        .options-grid {
            margin-bottom: 20px; /* Reduced from 30px */
            display: flex;
            gap: 30px; /* Align color and qty horizontally if space permits */
            flex-wrap: wrap;
        }
        .option-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .option-label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text-main);
            font-size: 0.9rem;
        }
        
        .color-selector { display: flex; gap: 8px; }
        .color-dot {
            width: 28px; /* Smaller dots */
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .color-dot.active { border-color: var(--text-main); transform: scale(1.1); }

        .quantity-wrapper {
            display: flex;
            align-items: center;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            overflow: hidden;
            width: fit-content;
            height: 36px;
        }
        .qty-btn {
            background: #fff;
            border: none;
            padding: 0 12px;
            cursor: pointer;
            font-size: 1.1rem;
            color: var(--text-sub);
            transition: 0.2s;
            height: 100%;
            display: flex;
            align-items: center;
        }
        .qty-btn:hover { background: #f3f4f6; color: var(--primary); }
        .qty-input {
            width: 40px;
            text-align: center;
            border: none;
            font-weight: 600;
            font-size: 0.95rem;
            -moz-appearance: textfield;
            height: 100%;
        }

        /* Action Buttons */
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 5px; /* Reduced */
        }
        .btn-action {
            padding: 14px; /* Reduced padding */
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-cart {
            background: #2563eb;
            color: white;
            border: none;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }
        .btn-cart:hover { 
            background: #1d4ed8; 
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
        }
        
        .btn-buy {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 4px 6px rgba(245, 158, 11, 0.3);
        }
        .btn-buy:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(245, 158, 11, 0.4);
        }

        .features-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }
        .feature-box {
            text-align: center;
            padding: 10px;
            background: #f9fafb;
            border-radius: 8px;
        }
        .feature-icon { font-size: 1.5rem; margin-bottom: 5px; display: block; }
        .feature-text { font-size: 0.8rem; font-weight: 600; color: var(--text-sub); }

        /* Responsive */
        @media (max-width: 900px) {
            .product-main-grid { grid-template-columns: 1fr; gap: 30px; }
            .action-buttons { grid-template-columns: 1fr; }
        }
    </style>

    <div class="product-page-wrapper">
        <div class="container">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="products.php">Products</a> / 
                <a href="products.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a> / 
                <span style="color: var(--text-main); font-weight: 500;"><?php echo htmlspecialchars($product['name']); ?></span>
            </div>

            <div class="product-main-grid">
                <!-- Visuals -->
                <div class="product-gallery">
                    <div class="main-image-frame">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <div style="font-size: 6rem;"><?php echo $product['emoji']; ?></div>
                        <?php endif; ?>
                        
                        <?php if (isLoggedIn()): ?>
                        <!-- Wishlist Toggle -->
                        <div onclick="toggleWishlist(event, <?php echo $product['id']; ?>)" 
                             style="position: absolute; top: 15px; right: 15px; background: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.1); cursor: pointer; font-size: 1.2rem; transition: transform 0.2s;"
                             onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                             <?php echo (isset($_SESSION['wishlist']) && in_array($product['id'], $_SESSION['wishlist'])) ? '❤️' : '🤍'; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Content -->
                <div class="product-details">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    
                    <div class="meta-row">
                        <span class="rating-badge">★ <?php echo $product['rating']; ?></span>
                        <span><?php echo $product['reviews']; ?> Reviews</span>
                        <span style="color: #d1d5db;">|</span>
                        <span>Brand: <strong><?php echo htmlspecialchars($brand['name'] ?? 'Generic'); ?></strong></span>
                    </div>

                    <div class="price-row">
                        <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                        <?php if ($product['stock'] > 0): ?>
                            <span class="stock-status in-stock">In Stock (<?php echo $product['stock']; ?>)</span>
                        <?php else: ?>
                            <span class="stock-status out-stock">Out of Stock</span>
                        <?php endif; ?>
                    </div>

                    <p class="description-text"><?php echo htmlspecialchars($product['description']); ?></p>

                    <!-- Form -->
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="options-grid">
                            <div class="option-group">
                                <label class="option-label">Select Color</label>
                                <div class="color-selector">
                                    <div class="color-dot active" style="background: #111827;"></div>
                                    <div class="color-dot" style="background: #3b82f6;"></div>
                                    <div class="color-dot" style="background: #ef4444;"></div>
                                    <div class="color-dot" style="background: #10b981;"></div>
                                </div>
                            </div>

                            <div class="option-group">
                                <label class="option-label">Quantity</label>
                                <div class="quantity-wrapper">
                                    <button type="button" class="qty-btn" onclick="updateQty(-1)">−</button>
                                    <input type="number" id="qtyInput" name="quantity" class="qty-input" value="1" min="1" max="<?php echo $product['stock']; ?>" readonly>
                                    <button type="button" class="qty-btn" onclick="updateQty(1, <?php echo $product['stock']; ?>)">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <?php if ($product['stock'] > 0): ?>
                                <button type="button" class="btn-action btn-cart" onclick="addToCartAjax(event, <?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')">
                                    🛒 Add to Cart
                                </button>
                                <button type="button" class="btn-action btn-buy" onclick="buyNow(<?php echo $product['id']; ?>, <?php echo $product['stock']; ?>)">
                                    ⚡ Buy Now
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn-action btn-cart" style="grid-column: span 2; opacity: 0.5;" disabled>
                                    Currently Unavailable
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="features-list">
                        <div class="feature-box">
                            <span class="feature-icon">🚚</span>
                            <div class="feature-text">Fast Delivery</div>
                        </div>
                        <div class="feature-box">
                            <span class="feature-icon">🛡️</span>
                            <div class="feature-text">Secure Checkout</div>
                        </div>
                        <div class="feature-box">
                            <span class="feature-icon">↩️</span>
                            <div class="feature-text">30-Day Returns</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommended Products Reuse -->
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 25px;">You Might Also Like</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                <?php
                $related = array_slice(getProductsByCategory($product['category_id']), 0, 4);
                foreach ($related as $rel):
                    if ($rel['id'] == $product['id']) continue;
                ?>
                    <a href="product-detail.php?id=<?php echo $rel['id']; ?>" style="text-decoration: none; color: inherit; background: white; padding: 15px; border-radius: 12px; border: 1px solid #eee; transition: transform 0.2s; display: block;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="height: 150px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                            <?php if (!empty($rel['image'])): ?>
                                <img src="<?php echo $rel['image']; ?>" style="max-height: 100%; max-width: 100%;">
                            <?php else: ?>
                                <span style="font-size: 3rem;"><?php echo $rel['emoji']; ?></span>
                            <?php endif; ?>
                        </div>
                        <h4 style="font-size: 1rem; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($rel['name']); ?></h4>
                        <div style="color: var(--primary); font-weight: 700;"><?php echo formatPrice($rel['price']); ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function updateQty(change, max = 100) {
            const input = document.getElementById('qtyInput');
            let val = parseInt(input.value) + change;
            if (val < 1) val = 1;
            if (val > max) val = max;
            input.value = val;
        }

        function buyNow(id, stock) {
            const qty = document.getElementById('qtyInput').value;
            window.location.href = `checkout.php?product_id=${id}&qty=${qty}&reset_shipping=1`;
        }

        function addToCartAjax(e, id, name) {
            e.preventDefault();
            const qty = document.getElementById('qtyInput').value;
            const btn = e.currentTarget;
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '⏳ Adding...';
            btn.disabled = true;

            const fd = new FormData();
            fd.append('action', 'add');
            fd.append('product_id', id);
            fd.append('quantity', qty);

            fetch('cart.php', { method: 'POST', body: fd })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(`🛒 ${name} added to cart!`, 'success', 3500);
                        
                        // Update default header badge
                        const badges = document.querySelectorAll('.badge');
                        badges.forEach(b => {
                           if (!b.classList.contains('wishlist-badge')) {
                               b.textContent = data.cartCount;
                               b.style.display = 'flex';
                           }
                        });
                        
                        btn.innerHTML = '✓ Added';
                        setTimeout(() => {
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        }, 2000);
                    } else {
                        showToast(data.message || 'Error adding to cart', 'error');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    showToast('Network error', 'error');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        }

        // Color selection logic
        document.querySelectorAll('.color-dot').forEach(dot => {
            dot.addEventListener('click', function() {
                document.querySelectorAll('.color-dot').forEach(d => d.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
<?php include TEMPLATES_PATH . '/footer.php'; ?>
// Redirect to products page if product not found
if (!$product) {
    header('Location: products.php');
    exit;
}

$pageTitle = htmlspecialchars($product['name']);
$category = getCategoryById($product['category_id']);
$brand = getBrandById($product['brand_id']);

// Check if user is logged in
$isLoggedIn = isLoggedIn();

// Handle add to cart
$addToCartMessage = '';
$addToCartError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if ($quantity > 0 && $quantity <= $product['stock']) {
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Check if product already in cart
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity
            ];
        }
        
        // Save cart to file for logged-in users
        if ($isLoggedIn && isset($_SESSION['user_email'])) {
            saveUserCart($_SESSION['user_email'], $_SESSION['cart']);
        }
        
        $addToCartMessage = 'Product added to cart successfully!';
    } else {
        $addToCartError = 'Invalid quantity. Please check stock availability.';
    }
}
?>
<?php include TEMPLATES_PATH . '/header.php'; ?>
    <script src="public/js/product-detail.js"></script>
    <script src="public/js/wishlist.js"></script>
    <script src="public/js/toast.js"></script>

    <?php if ($addToCartMessage): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('<?php echo htmlspecialchars($addToCartMessage); ?>', 'success', 3500);
            });
        </script>
    <?php endif; ?>

    <?php if ($addToCartError): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('<?php echo htmlspecialchars($addToCartError); ?>', 'error', 4000);
            });
        </script>
    <?php endif; ?>

    <!-- Product Detail Page - Modern Design -->
    <section class="product-detail-section">
        <div class="container">
            <!-- Back Button -->
            <a href="products.php" class="back-link">← Back to Products</a>

            <!-- Success/Error Messages -->
            

            <div class="product-detail-grid">
                <!-- Left Column: Product Visual -->
                <div class="product-detail-left">
                    <div class="product-visual-container" style="position: relative;">


                        <div class="product-emoji-large" style="display: flex; align-items: center; justify-content: center; overflow: hidden; background: #fff;">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: contain;">
                            <?php else: ?>
                                <?php echo $product['emoji']; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Product Info Box -->
                    <div class="product-info-box">
                        <h3>Product Information</h3>
                        <div class="info-item">
                            <span class="label">Product ID</span>
                            <span class="value">#<?php echo $product['id']; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Brand</span>
                            <span class="value"><?php echo htmlspecialchars($brand['name'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Category</span>
                            <a href="products.php?category=<?php echo $product['category_id']; ?>" class="value-link">
                                <?php echo htmlspecialchars($category['name'] ?? 'N/A'); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Product Details & Actions -->
                <div class="product-detail-right">
                    <!-- Product Title & Rating -->
                    <div class="product-header">
                        <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                        <div class="rating-section">
                            <div class="stars"><?php echo renderStars($product['rating']); ?></div>
                            <span class="rating-value"><?php echo $product['rating']; ?>/5</span>
                            <span class="review-count">(<?php echo $product['reviews']; ?> reviews)</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>

                    <!-- Price & Availability -->
                    <div class="price-availability-section">
                        <div class="price-box">
                            <span class="price-label">Price</span>
                            <span class="price"><?php echo formatPrice($product['price']); ?></span>
                        </div>
                        
                        <div class="availability-box <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                            <span class="status-icon"><?php echo $product['stock'] > 0 ? '✓' : '✕'; ?></span>
                            <div>
                                <div class="status-label"><?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?></div>
                                <div class="status-detail"><?php echo $product['stock'] > 0 ? $product['stock'] . ' units available' : 'Currently unavailable'; ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Color Options -->
                    <div class="options-section">
                        <div class="option-group">
                            <label class="option-label">Colors Available</label>
                            <div class="color-options">
                                <button class="color-btn active" style="background: #e74c3c;" title="Red"></button>
                                <button class="color-btn" style="background: #3498db;" title="Blue"></button>
                                <button class="color-btn" style="background: #2ecc71;" title="Green"></button>
                                <button class="color-btn" style="background: #f39c12;" title="Orange"></button>
                                <button class="color-btn" style="background: #34495e;" title="Black"></button>
                            </div>
                        </div>

                        <!-- Quantity Selector -->
                        <div class="option-group">
                            <label for="quantity" class="option-label">Quantity</label>
                            <div class="quantity-selector">
                                <button class="qty-btn" onclick="decrementQty()">−</button>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" readonly>
                                <button class="qty-btn" onclick="incrementQty(<?php echo $product['stock']; ?>)">+</button>
                            </div>
                            <small class="qty-note">Max: <?php echo $product['stock']; ?> units available</small>
                        </div>
                    </div>

                    <!-- Add to Cart Form -->
                    <form method="POST" action="" class="add-to-cart-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" id="quantity-hidden" name="quantity" value="1">
                        <input type="hidden" id="buy-quantity" value="1">

                        <?php if ($product['stock'] > 0): ?>
                            <button type="submit" class="btn-add-to-cart">
                                <span class="btn-icon">🛒</span>
                                Add to Cart
                            </button>
                            <button type="button" class="btn-buy-now" onclick="buyNow(<?php echo $product['id']; ?>, <?php echo $product['stock']; ?>)">
                                <span class="btn-icon">💳</span>
                                Buy Now
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn-add-to-cart" disabled>
                                Out of Stock
                            </button>
                        <?php endif; ?>
                    </form>


                    <!-- Features -->
                    <div class="info-features" style="margin-top: 20px;">
                        <div class="feature-item">
                            <span class="feature-icon">↩️</span>
                            <div>
                                <h4>30-Day Returns</h4>
                                <p>Hassle-free returns</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">🔒</span>
                            <div>
                                <h4>Secure Payment</h4>
                                <p>100% encrypted</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✓</span>
                            <div>
                                <h4>Authentic</h4>
                                <p>Guaranteed original</p>
                            </div>
                        </div>
                    </div>

                    <script>
                        function selectShipping(element) {
                            // Remove selected state from all shipping options
                            document.querySelectorAll('.shipping-option').forEach(option => {
                                option.style.borderColor = '#ddd';
                                option.style.backgroundColor = 'white';
                            });
                            
                            // Add selected state to clicked option
                            element.style.borderColor = '#2563eb';
                            element.style.backgroundColor = '#eff6ff';
                            
                            // Store selected shipping in data attribute
                            const shippingCost = element.dataset.shippingCost;
                            document.body.dataset.selectedShipping = shippingCost;
                        }
                    </script>
                </div>
            </div>

            <!-- Recommended Products Section -->
            <section class="recommended-section">
                <h2 class="section-title">Recommended for You</h2>
                <p class="section-subtitle">Similar products you might like</p>
                <?php
                $relatedProducts = array_slice(
                    getProductsByCategory($product['category_id']),
                    0,
                    4,
                    true
                );
                ?>
                <div class="products-grid">
                    <?php foreach ($relatedProducts as $relatedProduct): ?>
                        <?php if ($relatedProduct['id'] !== $product['id']): ?>
                            <div class="product-card">
                                <div class="product-image"><?php echo $relatedProduct['emoji']; ?></div>
                                <h3 class="product-title"><?php echo htmlspecialchars($relatedProduct['name']); ?></h3>
                                <div class="product-rating"><?php echo renderStars($relatedProduct['rating']); ?> <?php echo $relatedProduct['rating']; ?></div>
                                <div class="product-price"><?php echo formatPrice($relatedProduct['price']); ?></div>
                                <div class="product-footer">
                                    <span class="stock-info">Stock: <?php echo $relatedProduct['stock']; ?> units</span>
                                    <a href="product-detail.php?id=<?php echo $relatedProduct['id']; ?>" class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </section>
                       

<?php include TEMPLATES_PATH . '/footer.php'; ?>