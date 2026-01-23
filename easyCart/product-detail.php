<?php
session_start();

// Include data and auth
require_once 'data.php';
require_once 'auth.php';

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

// Handle add to cart
$addToCartMessage = '';
$addToCartError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    // Check if user is logged in
    if (!$isLoggedIn) {
        header('Location: signin.php?login_required=1');
        exit;
    }
    
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
        
        $addToCartMessage = 'Product added to cart successfully!';
    } else {
        $addToCartError = 'Invalid quantity. Please check stock availability.';
    }
}
?>
<?php include 'header.php'; ?>

    <!-- Product Detail Page - Modern Design -->
    <section class="product-detail-section">
        <div class="container">
            <!-- Back Button -->
            <a href="products.php" class="back-link">← Back to Products</a>

            <!-- Success/Error Messages -->
            <?php if ($addToCartMessage): ?>
                <div class="alert alert-success">
                    <span class="alert-icon">✓</span>
                    <?php echo htmlspecialchars($addToCartMessage); ?>
                </div>
            <?php endif; ?>

            <?php if ($addToCartError): ?>
                <div class="alert alert-error">
                    <span class="alert-icon">✕</span>
                    <?php echo htmlspecialchars($addToCartError); ?>
                </div>
            <?php endif; ?>

            <div class="product-detail-grid">
                <!-- Left Column: Product Visual -->
                <div class="product-detail-left">
                    <div class="product-visual-container">
                        <div class="product-emoji-large"><?php echo $product['emoji']; ?></div>
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
                            <?php if ($isLoggedIn): ?>
                                <button type="submit" class="btn-add-to-cart">
                                    <span class="btn-icon">🛒</span>
                                    Add to Cart
                                </button>
                                <button type="button" class="btn-buy-now" onclick="buyNow(<?php echo $product['id']; ?>, <?php echo $product['stock']; ?>)">
                                    <span class="btn-icon">💳</span>
                                    Buy Now
                                </button>
                            <?php else: ?>
                                <div class="login-prompt">
                                    <p>Sign in to add items to your cart</p>
                                    <div class="login-buttons">
                                        <a href="signin.php" class="btn-signin">Sign In</a>
                                        <a href="signup.php" class="btn-signup">Create Account</a>
                                    </div>
                                </div>
                                <button type="button" class="btn-add-to-cart" disabled>
                                    <span class="btn-icon">🛒</span>
                                    Add to Cart (Sign in required)
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <button type="button" class="btn-add-to-cart" disabled>
                                Out of Stock
                            </button>
                        <?php endif; ?>
                    </form>

                    <!-- Shipping & Returns Info -->
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px;">
                        <h4 style="margin-top: 0; margin-bottom: 15px;">Shipping Options</h4>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <div class="shipping-option" onclick="selectShipping(this)" style="
                                padding: 12px;
                                border: 2px solid #ddd;
                                border-radius: 6px;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                background: white;
                            " data-shipping-cost="0">
                                <div style="font-weight: 600; color: #333;">Standard Shipping</div>
                                <div style="font-size: 12px; color: #666;">5-7 business days</div>
                                <div style="font-weight: 700; color: #10b981; margin-top: 5px;">FREE</div>
                            </div>
                            <div class="shipping-option" onclick="selectShipping(this)" style="
                                padding: 12px;
                                border: 2px solid #ddd;
                                border-radius: 6px;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                background: white;
                            " data-shipping-cost="5.99">
                                <div style="font-weight: 600; color: #333;">Express Shipping</div>
                                <div style="font-size: 12px; color: #666;">2-3 business days</div>
                                <div style="font-weight: 700; color: #2563eb; margin-top: 5px;">$5.99</div>
                            </div>
                            <div class="shipping-option" onclick="selectShipping(this)" style="
                                padding: 12px;
                                border: 2px solid #ddd;
                                border-radius: 6px;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                background: white;
                            " data-shipping-cost="14.99">
                                <div style="font-weight: 600; color: #333;">Overnight Shipping</div>
                                <div style="font-size: 12px; color: #666;">Next business day</div>
                                <div style="font-weight: 700; color: #f59e0b; margin-top: 5px;">$14.99</div>
                            </div>
                        </div>
                    </div>

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
                       

<?php include 'footer.php'; ?>
<script>
    // Color selector functionality
    document.querySelectorAll('.color-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Quantity increment/decrement
    function incrementQty(max) {
        const qtyInput = document.getElementById('quantity');
        let currentVal = parseInt(qtyInput.value);
        if (currentVal < max) {
            qtyInput.value = currentVal + 1;
            updateQuantityHidden();
        }
    }

    function decrementQty() {
        const qtyInput = document.getElementById('quantity');
        let currentVal = parseInt(qtyInput.value);
        if (currentVal > 1) {
            qtyInput.value = currentVal - 1;
            updateQuantityHidden();
        }
    }

    function updateQuantityHidden() {
        const qtyInput = document.getElementById('quantity');
        const qtyHidden = document.getElementById('quantity-hidden');
        qtyHidden.value = qtyInput.value;
    }

    function buyNow(productId, maxStock) {
        const qtyInput = document.getElementById('quantity');
        const qty = parseInt(qtyInput.value);
        
        if (qty > 0 && qty <= maxStock) {
            window.location.href = 'checkout.php?product_id=' + productId + '&qty=' + qty;
        } else {
            alert('Please select a valid quantity');
        }
    }

    // Update hidden quantity when input changes
    document.getElementById('quantity').addEventListener('change', updateQuantityHidden);

    // Update hidden quantity on form submit
    document.querySelector('.add-to-cart-form')?.addEventListener('submit', function() {
        updateQuantityHidden();
    });
</script>