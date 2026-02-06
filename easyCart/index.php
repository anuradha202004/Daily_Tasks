<?php
session_start();

// Fallback Router: If server sends requests for 'product-detail' (no ext) to index.php, redirect to product-detail.php
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'product-detail') !== false && strpos($_SERVER['REQUEST_URI'], '.php') === false) {
    if (isset($_GET['id'])) {
        header("Location: product-detail.php?id=" . $_GET['id']);
        exit;
    }
}

// die("<pre>" . json_encode($_SESSION, 128) . "</pre>");

// echo "<pre>";
// print_r($_SESSION);
// // echo "</pre>";

// echo "<pre>" . json_encode(json_decode(file_get_contents('data/users.json')), 128) . "</pre>";
// die;
// Include data and auth
// Load Application Bootstrap
require_once 'app/bootstrap.php';

// Load cart and wishlist from file if user is logged in
if (isLoggedIn()) {
    if (!isset($_SESSION['cart'])) {
        initializeCartFromFile();
    }
    if (!isset($_SESSION['wishlist'])) {
        initializeWishlistFromFile();
    }
}

$pageTitle = 'Home';

// Get featured products (first 4 products)
$featuredProducts = array_slice($products, 0, 4, true);
?>
<?php include TEMPLATES_PATH . '/header.php'; ?>

    <script src="public/js/wishlist.js"></script>
    <script src="public/js/cart.js"></script>
    <script src="public/js/toast.js"></script>

    <!-- Modern Hero Section - Split Design with Glassmorphism -->
    <!-- Modern Interactive Ad Hero Section -->
    <!-- Modern Clean Hero Section -->
    <section class="hero-section" style="background: #ffffff; padding: 20px 0 40px; position: relative; overflow: hidden;">
        <div class="container" style="display: flex; align-items: center; gap: 40px; min-height: 450px;">
            
            <!-- Left: Image (Person) -->
            <div class="hero-image" style="flex: 1; text-align: center; position: relative;">
                <!-- Using hero_model.png as placeholder for the person image -->
                <img src="public/img/hero_model.png" alt="Happy Shopper" style="
                    position: relative;
                    z-index: 1;
                    max-width: 100%;
                    height: auto;
                ">
            </div>

            <!-- Right: Content -->
            <div class="hero-content" style="flex: 1; padding-left: 20px;">
                <span style="
                    display: inline-block;
                    padding: 8px 16px;
                    background: #ebf5ff;
                    color: #2563eb;
                    border-radius: 30px;
                    font-weight: 700;
                    font-size: 0.9rem;
                    margin-bottom: 24px;
                ">
                    ✨ NEW COLLECTION 2026
                </span>
                
                <h1 style="
                    font-size: 4.5rem;
                    line-height: 1.1;
                    font-weight: 800;
                    color: #111827;
                    margin-bottom: 24px;
                    letter-spacing: -0.02em;
                ">
                    Discover Your <br>
                    <span style="color: #2563eb;">Style Today.</span>
                </h1>
                
                <p style="
                    font-size: 1.25rem;
                    line-height: 1.7;
                    color: #6b7280;
                    margin-bottom: 40px;
                    max-width: 500px;
                ">
                    Explore our latest arrivals and find the perfect look for any occasion. Premium quality, best prices.
                </p>
                
                <div style="display: flex; gap: 20px; align-items: center;">
                    <button onclick="window.location.href='products.php'" style="
                        cursor: pointer;
                        padding: 18px 40px;
                        background: #2563eb;
                        color: white;
                        border: none;
                        border-radius: 50px;
                        font-size: 1.1rem;
                        font-weight: 600;
                        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
                        transition: transform 0.2s, box-shadow 0.2s;
                    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 30px rgba(37, 99, 235, 0.4)'"
                      onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(37, 99, 235, 0.3)'">
                        Shop Now
                    </button>
                    
                    <a href="#about" style="
                        color: #4b5563;
                        text-decoration: none;
                        font-weight: 500;
                        font-size: 1.1rem;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                        transition: color 0.2s;
                    " onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#4b5563'">
                        <span>Learn More</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div style="margin-top: 60px; display: flex; gap: 40px; border-top: 1px solid #f3f4f6; padding-top: 1px;">
                    <div>
                        <strong style="display: block; font-size: 1.5rem; color: #111827;">50k+</strong>
                        <span style="color: #6b7280; font-size: 0.95rem;">Happy Customers</span>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 1.5rem; color: #111827;">2k+</strong>
                        <span style="color: #6b7280; font-size: 0.95rem;">Top Products</span>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 1.5rem; color: #111827;">24/7</strong>
                        <span style="color: #6b7280; font-size: 0.95rem;">Customer Support</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Access / Services Section -->
    <section style="padding: 0 0 80px; background: #ffffff;">
        <div class="container">
            <div style="
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 30px;
                background: #f8fafc;
                padding: 40px;
                border-radius: 20px;
            ">
                <div style="text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 15px;">🚚</div>
                    <h3 style="margin-bottom: 10px; color: #1e293b;">Fast Shipping</h3>
                    <p style="color: #64748b; font-size: 0.95rem;">Free delivery on orders over $50</p>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 15px;">🛡️</div>
                    <h3 style="margin-bottom: 10px; color: #1e293b;">Secure Payment</h3>
                    <p style="color: #64748b; font-size: 0.95rem;">100% secure payment processing</p>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 15px;">↩️</div>
                    <h3 style="margin-bottom: 10px; color: #1e293b;">Easy Returns</h3>
                    <p style="color: #64748b; font-size: 0.95rem;">Hassle-free 30-day return policy</p>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 15px;">💬</div>
                    <h3 style="margin-bottom: 10px; color: #1e293b;">24/7 Support</h3>
                    <p style="color: #64748b; font-size: 0.95rem;">Dedicated support anytime</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Featured Products Section -->
    <section class="container">
        <h2 class="section-title">Featured Products</h2>
        <div class="products-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <?php $isWishlisted = isset($_SESSION['wishlist']) && in_array($product['id'], $_SESSION['wishlist']); ?>
                
                <div class="product-card" onclick="window.location.href='product-detail?id=<?php echo $product['id']; ?>'">
                    
                    <?php if (isLoggedIn()): ?>
                        <div class="card-wishlist-btn" 
                             onclick="event.stopPropagation(); toggleWishlist(event, <?php echo $product['id']; ?>)"
                             data-product-id="<?php echo $product['id']; ?>">
                            <?php echo $isWishlisted ? '❤️' : '🤍'; ?>
                        </div>
                    <?php endif; ?>

                    <span class="badge-new">NEW</span>

                    <div class="product-image-container">
                        <div class="product-image-content">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <span style="font-size: 60px;"><?php echo $product['emoji']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-info">
                        <div class="product-category"><?php echo htmlspecialchars($product['brand'] ?? 'General'); ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        
                        <div class="product-price-row">
                            <div class="price-current"><?php echo formatPrice($product['price']); ?></div>
                            <div class="rating-block">
                                ⭐ <?php echo $product['rating']; ?> <span style="color: #9ca3af; font-weight: 400;">(<?php echo $product['reviews']; ?>)</span>
                            </div>
                        </div>

                        <div class="product-footer">
                            <?php 
                                $stockClass = 'in-stock';
                                $stockText = 'In Stock';
                                if ($product['stock'] == 0) {
                                    $stockClass = 'out-stock';
                                    $stockText = 'Out of Stock';
                                } elseif ($product['stock'] < 10) {
                                    $stockClass = 'low-stock';
                                    $stockText = 'Running Low';
                                }
                            ?>
                            <div class="stock-indicator <?php echo $stockClass; ?>">
                                <span class="stock-dot"></span> <?php echo $stockText; ?>
                            </div>

                            <div class="action-buttons" onclick="event.stopPropagation();">
                                <?php if ($product['stock'] > 0): ?>
                                    <button onclick="(function(e, id, name){ e.preventDefault(); e.stopPropagation(); var fd = new FormData(); fd.append('action', 'add'); fd.append('product_id', id); fd.append('quantity', 1); fetch('cart.php', {method: 'POST', body: fd}).then(res => res.json()).then(data => { if(data.success) { showToast('🛒 ' + name + ' added to cart!', 'success', 3500); var badge = document.querySelector('.badge'); if(badge){ badge.textContent = data.cartCount || (parseInt(badge.textContent) + 1); badge.style.display = 'flex'; } } else if(data.alreadyInCart) { showToast('ℹ️ ' + name + ' is already in your cart!', 'info', 3500); } else { showToast('❌ ' + (data.message || 'Error adding to cart'), 'error', 3000); } }).catch(() => showToast('❌ Error adding to cart', 'error', 3000)); return false; })(event, <?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')" 
                                            class="btn-modern btn-outline-cart">
                                        Add to Cart 
                                    </button>
                                    <a href="checkout.php?product_id=<?php echo $product['id']; ?>&qty=1&reset_shipping=1" class="btn-modern btn-gradient-buy">
                                        Buy Now
                                    </a>
                                <?php else: ?>
                                    <button class="btn-modern btn-outline-cart" style="opacity: 0.5; cursor: not-allowed;" disabled>Out of Stock</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- All Products Section -->
    <section id="products" class="container">
        <h2 class="section-title">Our Products</h2>
        
        <div class="category-filters">
            <a href="products.php" class="filter-btn active">All Products</a>
            <?php foreach ($categories as $category): ?>
                <a href="products.php?category=<?php echo $category['id']; ?>" class="filter-btn">
                    <?php echo htmlspecialchars($category['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="products-grid">
            <?php foreach (array_slice($products, 0, 8, true) as $index => $product): ?>
                <?php $isWishlisted = isset($_SESSION['wishlist']) && in_array($product['id'], $_SESSION['wishlist']); ?>
                
                <div class="product-card" onclick="window.location.href='product-detail?id=<?php echo $product['id']; ?>'">
                    
                    <?php if (isLoggedIn()): ?>
                        <div class="card-wishlist-btn" 
                             onclick="event.stopPropagation(); toggleWishlist(event, <?php echo $product['id']; ?>)"
                             data-product-id="<?php echo $product['id']; ?>">
                            <?php echo $isWishlisted ? '❤️' : '🤍'; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($index < 3): ?>
                        <span class="badge-new">NEW</span>
                    <?php endif; ?>

                    <div class="product-image-container">
                        <div class="product-image-content">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <span style="font-size: 60px;"><?php echo $product['emoji']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-info">
                        <div class="product-category"><?php echo htmlspecialchars($product['brand'] ?? 'General'); ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        
                        <div class="product-price-row">
                            <div class="price-current"><?php echo formatPrice($product['price']); ?></div>
                            <div class="rating-block">
                                ⭐ <?php echo $product['rating']; ?> <span style="color: #9ca3af; font-weight: 400;">(<?php echo $product['reviews']; ?>)</span>
                            </div>
                        </div>

                        <div class="product-footer">
                            <?php 
                                $stockClass = 'in-stock';
                                $stockText = 'In Stock';
                                if ($product['stock'] == 0) {
                                    $stockClass = 'out-stock';
                                    $stockText = 'Out of Stock';
                                } elseif ($product['stock'] < 10) {
                                    $stockClass = 'low-stock';
                                    $stockText = 'Running Low';
                                }
                            ?>
                            <div class="stock-indicator <?php echo $stockClass; ?>">
                                <span class="stock-dot"></span> <?php echo $stockText; ?>
                            </div>

                            <div class="action-buttons" onclick="event.stopPropagation();">
                                <?php if ($product['stock'] > 0): ?>
                                    <button onclick="(function(e, id, name){ e.preventDefault(); e.stopPropagation(); var fd = new FormData(); fd.append('action', 'add'); fd.append('product_id', id); fd.append('quantity', 1); fetch('cart.php', {method: 'POST', body: fd}).then(res => res.json()).then(data => { if(data.success) { showToast('🛒 ' + name + ' added to cart!', 'success', 3500); var badge = document.querySelector('.badge'); if(badge){ badge.textContent = data.cartCount || (parseInt(badge.textContent) + 1); badge.style.display = 'flex'; } } else if(data.alreadyInCart) { showToast('ℹ️ ' + name + ' is already in your cart!', 'info', 3500); } else { showToast('❌ ' + (data.message || 'Error adding to cart'), 'error', 3000); } }).catch(() => showToast('❌ Error adding to cart', 'error', 3000)); return false; })(event, <?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')" 
                                            class="btn-modern btn-outline-cart">
                                        Add to Cart
                                    </button>
                                    <a href="checkout.php?product_id=<?php echo $product['id']; ?>&qty=1&reset_shipping=1" class="btn-modern btn-gradient-buy">
                                        Buy Now
                                    </a>
                                <?php else: ?>
                                    <button class="btn-modern btn-outline-cart" style="opacity: 0.5; cursor: not-allowed;" disabled>Out of Stock</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- About Section (Home Page) -->
    <section id="about" class="container" style="padding: 60px 20px;">
        <h2 class="section-title">About EasyCart</h2>
        <div class="about-section" style="background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;">
                <div>
                    <h3 style="margin-bottom: 20px; font-size: 1.5rem;">Our Mission</h3>
                    <p style="color: #4b5563; line-height: 1.7; margin-bottom: 20px;">
                        EasyCart is a professional e-commerce platform designed to provide seamless online shopping experiences. 
                        Started in 2024, we built this on a simple premise: online shopping shouldn't be complicated.
                    </p>
                    <div style="display: flex; gap: 20px;">
                        <div style="text-align: center;">
                            <strong style="display: block; font-size: 1.5rem; color: #4f46e5;">50K+</strong>
                            <span style="font-size: 0.9rem; color: #6b7280;">Customers</span>
                        </div>
                        <div style="text-align: center;">
                            <strong style="display: block; font-size: 1.5rem; color: #4f46e5;">100+</strong>
                            <span style="font-size: 0.9rem; color: #6b7280;">Brands</span>
                        </div>
                    </div>
                </div>
                <div style="background: #f3f4f6; padding: 40px; border-radius: 20px; text-align: center; font-size: 5rem;">
                    🛍️
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section (Home Page) -->
    <section id="contact" class="container" style="padding: 60px 20px;">
        <h2 class="section-title">Contact Us</h2>
        <div class="contact-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div class="contact-card" style="background: #4f46e5; color: white; padding: 40px; border-radius: 16px;">
                <h3 style="margin-bottom: 20px; font-size: 1.5rem;">Get in Touch</h3>
                <div style="margin-bottom: 20px;">
                    <strong>📍 Address</strong>
                    <p style="opacity: 0.9;">123 Commerce St, Tech City</p>
                </div>
                <div style="margin-bottom: 20px;">
                    <strong>📧 Email</strong>
                    <p style="opacity: 0.9;">support@easycart.com</p>
                </div>
                <div>
                    <strong>📞 Phone</strong>
                    <p style="opacity: 0.9;">+1 (555) 123-4567</p>
                </div>
            </div>

            <div class="contact-card" style="background: white; padding: 40px; border-radius: 16px; border: 1px solid #e5e7eb;">
                <h3 style="margin-bottom: 20px; font-size: 1.5rem; color: #111827;">Send Message</h3>
                <form onsubmit="event.preventDefault(); showToast('✅ Message sent!', 'success'); this.reset();">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #374151;">Name</label>
                        <input type="text" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #374151;">Message</label>
                        <textarea required rows="3" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Send</button>
                </form>
            </div>
        </div>
    </section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
