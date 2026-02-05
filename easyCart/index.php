<?php
session_start();

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
    <script src="public/js/carousel.js"></script>
    <script src="public/js/wishlist.js"></script>
    <script src="public/js/cart.js"></script>
    <script src="public/js/toast.js"></script>

    <!-- Modern Hero Section - Split Design with Glassmorphism -->
    <!-- Modern Interactive Ad Hero Section -->
    <section id="home" class="interactive-hero">
        <!-- Animated Background -->
        <div class="hero-bg-animated">
            <div class="animated-shape shape-1"></div>
            <div class="animated-shape shape-2"></div>
            <div class="animated-shape shape-3"></div>
        </div>

        <div class="hero-wrapper">
            <!-- Main Ad Display -->
            <div class="ads-carousel-container">
                <div class="ads-carousel">
                    <!-- Ad 1 -->
                    <div class="ad-slide active" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('public/img/hero_shopping.png') center/cover no-repeat;">
                        <div class="ad-content">
                            <div class="ad-badge">✨ NEW COLLECTION</div>
                            <h2 class="ad-title">Summer Collection 2026</h2>
                            <p class="ad-description">Discover the latest trending products</p>
                            <a href="products.php?category=1" class="ad-cta">Shop Collection →</a>
                        </div>
                        <div class="ad-visual"></div>
                    </div>

                    <!-- Ad 2 -->
                    <div class="ad-slide" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('public/img/hero_laptop.png') center/cover no-repeat;">
                        <div class="ad-content">
                            <div class="ad-badge">🔥 HOT DEALS</div>
                            <h2 class="ad-title">Up to 70% OFF</h2>
                            <p class="ad-description">Limited time exclusive offers</p>
                            <a href="products.php" class="ad-cta">Grab Deals →</a>
                        </div>
                        <div class="ad-visual"></div>
                    </div>

                    <!-- Ad 3 -->
                    <div class="ad-slide" style="background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('public/img/hero_music.png') center/cover no-repeat;">
                        <div class="ad-content">
                            <div class="ad-badge">⭐ PREMIUM QUALITY</div>
                            <h2 class="ad-title">Best Sellers</h2>
                            <p class="ad-description">Top-rated products at best prices</p>
                            <a href="products.php" class="ad-cta">View Best Sellers →</a>
                        </div>
                        <div class="ad-visual"></div>
                    </div>

                    <!-- Ad 4 -->
                    <div class="ad-slide" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('public/img/hero_vip.png') center/cover no-repeat;">
                        <div class="ad-content">
                            <div class="ad-badge">💎 EXCLUSIVE</div>
                            <h2 class="ad-title">VIP Member Benefits</h2>
                            <p class="ad-description">Unlock premium rewards today</p>
                            <a href="products.php" class="ad-cta">Join VIP →</a>
                        </div>
                        <div class="ad-visual"></div>
                    </div>
                </div>

                <!-- Navigation Controls -->
                <div class="carousel-controls">
                    <button class="nav-btn prev-btn" onclick="prevAd()">❮</button>
                    <button class="nav-btn next-btn" onclick="nextAd()">❯</button>
                </div>

                <!-- Interactive Dots -->
                <div class="carousel-dots">
                    <span class="dot active" onclick="goToAd(0)"></span>
                    <span class="dot" onclick="goToAd(1)"></span>
                    <span class="dot" onclick="goToAd(2)"></span>
                    <span class="dot" onclick="goToAd(3)"></span>
                </div>

                <!-- Progress Bar -->
                <div class="progress-bar"><div class="progress-fill"></div></div>
            </div>

            <!-- Quick Access Section -->
            <div class="quick-access">
                <div class="access-item">
                    <div class="access-icon">🚚</div>
                    <h3>Fast Shipping</h3>
                    <p>Free on orders over $50</p>
                </div>
                <div class="access-item">
                    <div class="access-icon">🛡️</div>
                    <h3>Secure Payment</h3>
                    <p>100% protected checkout</p>
                </div>
                <div class="access-item">
                    <div class="access-icon">↩️</div>
                    <h3>Easy Returns</h3>
                    <p>30-day return policy</p>
                </div>
                <div class="access-item">
                    <div class="access-icon">💬</div>
                    <h3>24/7 Support</h3>
                    <p>Always here to help</p>
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
                <div class="product-card" style="position: relative; cursor: pointer;" onclick="window.location.href='product-detail.php?id=<?php echo $product['id']; ?>'">
                    <?php if (isLoggedIn()): ?>
                    <div onclick="event.stopPropagation(); toggleWishlist(event, <?php echo $product['id']; ?>)" 
                         style="position: absolute; top: 10px; right: 10px; font-size: 24px; cursor: pointer; z-index: 10; background: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: all 0.3s ease;"
                         onmouseover="this.style.transform='scale(1.1)'"
                         onmouseout="this.style.transform='scale(1)'"
                         class="heart-icon"
                         data-product-id="<?php echo $product['id']; ?>">
                        <?php echo $isWishlisted ? '❤️' : '🤍'; ?>
                    </div>
                    <?php endif; ?>
                    <div class="product-image" style="overflow: hidden; display: flex; align-items: center; justify-content: center; background: #fff;">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: contain; transition: transform 0.3s ease;">
                        <?php else: ?>
                            <?php echo $product['emoji']; ?>
                        <?php endif; ?>
                    </div>
                    <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="product-rating"><?php echo renderStars($product['rating']); ?> <?php echo $product['rating']; ?> (<?php echo $product['reviews']; ?> reviews)</div>
                    <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                    <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                    <div class="product-footer">
                        <span class="stock-info">Stock: <?php echo $product['stock']; ?> units</span>
                        <div class="product-actions" onclick="event.stopPropagation();">
                            <?php if ($product['stock'] > 0): ?>
                                <button type="button" onclick="(function(e, id, name){ e.preventDefault(); e.stopPropagation(); var fd = new FormData(); fd.append('action', 'add'); fd.append('product_id', id); fd.append('quantity', 1); fetch('cart.php', {method: 'POST', body: fd}).then(res => res.json()).then(data => { if(data.success) { showToast('🛒 ' + name + ' added to cart!', 'success', 3500); var badge = document.querySelector('.badge'); if(badge){ badge.textContent = data.cartCount || (parseInt(badge.textContent) + 1); badge.style.display = 'flex'; } } else if(data.alreadyInCart) { showToast('ℹ️ ' + name + ' is already in your cart!', 'info', 3500); } else { showToast('❌ ' + (data.message || 'Error adding to cart'), 'error', 3000); } }).catch(() => showToast('❌ Error adding to cart', 'error', 3000)); return false; })(event, <?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')" class="btn btn-primary btn-add-cart" data-product-id="<?php echo $product['id']; ?>">
                                    Add to Cart
                                </button>
                                <a href="checkout?product_id=<?php echo $product['id']; ?>&qty=1&reset_shipping=1" class="btn btn-buy-now">Buy Now</a>
                            <?php else: ?>
                                <button class="btn btn-disabled" disabled>Out of Stock</button>
                            <?php endif; ?>
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
            <?php foreach (array_slice($products, 0, 8, true) as $product): ?>
                <?php $isWishlisted = isset($_SESSION['wishlist']) && in_array($product['id'], $_SESSION['wishlist']); ?>
                <div class="product-card" style="position: relative; cursor: pointer;" onclick="window.location.href='product-detail.php?id=<?php echo $product['id']; ?>'">
                    <?php if (isLoggedIn()): ?>
                    <div onclick="event.stopPropagation(); toggleWishlist(event, <?php echo $product['id']; ?>)" 
                         style="position: absolute; top: 10px; right: 10px; font-size: 24px; cursor: pointer; z-index: 10; background: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: all 0.3s ease;"
                         onmouseover="this.style.transform='scale(1.1)'"
                         onmouseout="this.style.transform='scale(1)'"
                         class="heart-icon"
                         data-product-id="<?php echo $product['id']; ?>">
                        <?php echo $isWishlisted ? '❤️' : '🤍'; ?>
                    </div>
                    <?php endif; ?>
                    <div class="product-image" style="overflow: hidden; display: flex; align-items: center; justify-content: center; background: #fff;">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: contain; transition: transform 0.3s ease;">
                        <?php else: ?>
                            <?php echo $product['emoji']; ?>
                        <?php endif; ?>
                    </div>
                    <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="product-rating"><?php echo renderStars($product['rating']); ?> <?php echo $product['rating']; ?> (<?php echo $product['reviews']; ?> reviews)</div>
                    <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                    <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                    <div class="product-footer">
                        <span class="stock-info">Stock: <?php echo $product['stock']; ?> units</span>
                        <div class="product-actions" onclick="event.stopPropagation();">
                            <?php if ($product['stock'] > 0): ?>
                                <button type="button" onclick="(function(e, id, name){ e.preventDefault(); e.stopPropagation(); var fd = new FormData(); fd.append('action', 'add'); fd.append('product_id', id); fd.append('quantity', 1); fetch('cart.php', {method: 'POST', body: fd}).then(res => res.json()).then(data => { if(data.success) { showToast('🛒 ' + name + ' added to cart!', 'success', 3500); var badge = document.querySelector('.badge'); if(badge){ badge.textContent = data.cartCount || (parseInt(badge.textContent) + 1); badge.style.display = 'flex'; } } else if(data.alreadyInCart) { showToast('ℹ️ ' + name + ' is already in your cart!', 'info', 3500); } else { showToast('❌ ' + (data.message || 'Error adding to cart'), 'error', 3000); } }).catch(() => showToast('❌ Error adding to cart', 'error', 3000)); return false; })(event, <?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')" class="btn btn-primary btn-add-cart" data-product-id="<?php echo $product['id']; ?>">
                                     Add to Cart
                                </button>
                                <a href="checkout?product_id=<?php echo $product['id']; ?>&qty=1&reset_shipping=1" class="btn btn-buy-now">Buy Now</a>
                            <?php else: ?>
                                <button class="btn btn-disabled" disabled>Out of Stock</button>
                            <?php endif; ?>
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
