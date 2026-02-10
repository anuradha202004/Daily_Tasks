<?php include TEMPLATES_PATH . '/header.php'; ?>

<script src="<?php echo baseUrl('js/wishlist.js'); ?>"></script>
<script src="<?php echo baseUrl('js/cart.js'); ?>"></script>
<script src="<?php echo baseUrl('js/toast.js'); ?>"></script>

<!-- Modern Hero Section - Split Design with Glassmorphism -->
<section class="hero-section">
    <div class="container hero-container">
        
        <!-- Left: Image (Person) -->
        <div class="hero-image">
            <img src="<?php echo baseUrl('img/hero_model.png'); ?>" alt="Happy Shopper">
        </div>

        <!-- Right: Content -->
        <div class="hero-content">
            <span class="hero-badge">
                ✨ NEW COLLECTION 2026
            </span>
            
            <h1 class="hero-title">
                Discover Your <br>
                <span class="highlight">Style Today.</span>
            </h1>
            
            <p class="hero-description">
                Explore our latest arrivals and find the perfect look for any occasion. Premium quality, best prices.
            </p>
            
            <div class="hero-actions">
                <button onclick="window.location.href='products'" class="btn-hero-primary">
                    Shop Now
                </button>
                
                <a href="#about" class="btn-hero-secondary">
                    <span>Learn More</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Trust Indicators -->
            <div class="hero-trust-indicators">
                <div class="trust-item">
                    <strong>50k+</strong>
                    <span>Happy Customers</span>
                </div>
                <div class="trust-item">
                    <strong>2k+</strong>
                    <span>Top Products</span>
                </div>
                <div class="trust-item">
                    <strong>24/7</strong>
                    <span>Customer Support</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Access / Services Section -->
<section class="services-section">
    <div class="container">
        <div class="services-grid">
            <div class="service-item">
                <div class="service-icon">🚚</div>
                <h3 class="service-title">Fast Shipping</h3>
                <p class="service-desc">Free delivery on orders over $50</p>
            </div>
            <div class="service-item">
                <div class="service-icon">🛡️</div>
                <h3 class="service-title">Secure Payment</h3>
                <p class="service-desc">100% secure payment processing</p>
            </div>
            <div class="service-item">
                <div class="service-icon">↩️</div>
                <h3 class="service-title">Easy Returns</h3>
                <p class="service-desc">Hassle-free 30-day return policy</p>
            </div>
            <div class="service-item">
                <div class="service-icon">💬</div>
                <h3 class="service-title">24/7 Support</h3>
                <p class="service-desc">Dedicated support anytime</p>
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
            
            <?php 
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product['name'])));
                $productUrl = baseUrl('product/' . $slug);
            ?>
            <div class="product-card" onclick="window.location.href='<?php echo $productUrl; ?>'">
                
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
                            <?php 
                                $imgSrc = $product['image'];
                                if (strpos($imgSrc, 'http') === 0) {
                                    // External URL, use as is
                                } else {
                                    // Local file: normalize path
                                    // If it starts with public/ or /public/, remove it
                                    $imgSrc = preg_replace('/^(\/)?public\//', '', $imgSrc);
                                    $imgSrc = baseUrl($imgSrc);
                                }
                            ?>
                            <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
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
                                <button onclick="event.preventDefault(); event.stopPropagation(); addToCart(<?php echo $product['id']; ?>, 1);" 
                                        class="btn-modern btn-outline-cart btn-add-cart" 
                                        data-product-id="<?php echo $product['id']; ?>">
                                    Add to Cart 
                                </button>
                                <a href="checkout?product_id=<?php echo $product['id']; ?>&qty=1&reset_shipping=1" class="btn-modern btn-gradient-buy">
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
        <a href="products" class="filter-btn active">All Products</a>
        <?php foreach ($categories as $category): ?>
            <a href="products?category=<?php echo $category['id']; ?>" class="filter-btn">
                <?php echo htmlspecialchars($category['name']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="products-grid">
        <?php foreach (array_slice($products, 0, 8, true) as $index => $product): ?>
            <?php $isWishlisted = isset($_SESSION['wishlist']) && in_array($product['id'], $_SESSION['wishlist']); ?>
            
            <?php 
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product['name'])));
                $productUrl = baseUrl('product/' . $slug);
            ?>
            <div class="product-card" onclick="window.location.href='<?php echo $productUrl; ?>'">
                
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
                            <?php 
                                $imgSrc = $product['image'];
                                if (strpos($imgSrc, 'http') === 0) {
                                    // External URL, use as is
                                } else {
                                    // Local file: normalize path
                                    // If it starts with public/ or /public/, remove it
                                    $imgSrc = preg_replace('/^(\/)?public\//', '', $imgSrc);
                                    $imgSrc = baseUrl($imgSrc);
                                }
                            ?>
                            <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
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
                                <button onclick="event.preventDefault(); event.stopPropagation(); addToCart(<?php echo $product['id']; ?>, 1);" 
                                        class="btn-modern btn-outline-cart btn-add-cart" 
                                        data-product-id="<?php echo $product['id']; ?>">
                                    Add to Cart
                                </button>
                                <a href="checkout?product_id=<?php echo $product['id']; ?>&qty=1&reset_shipping=1" class="btn-modern btn-gradient-buy">
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
    <div class="about-section-container">
        <div class="about-grid">
            <div>
                <h3 style="margin-bottom: 20px; font-size: 1.5rem;">Our Mission</h3>
                <p style="color: #4b5563; line-height: 1.7; margin-bottom: 20px;">
                    EasyCart is a professional e-commerce platform designed to provide seamless online shopping experiences. 
                    Started in 2024, we built this on a simple premise: online shopping shouldn't be complicated.
                </p>
                <div class="about-stats">
                    <div class="about-stat-item">
                        <strong class="about-stat-number">50K+</strong>
                        <span class="about-stat-label">Customers</span>
                    </div>
                    <div class="about-stat-item">
                        <strong class="about-stat-number">100+</strong>
                        <span class="about-stat-label">Brands</span>
                    </div>
                </div>
            </div>
            <div class="about-image-placeholder">
                🛍️
            </div>
        </div>
    </div>
</section>

<!-- Contact Section (Home Page) -->
<section id="contact" class="container" style="padding: 60px 20px;">
    <h2 class="section-title">Contact Us</h2>
    <div class="contact-grid">
        <div class="contact-info-card">
            <h3 style="margin-bottom: 20px; font-size: 1.5rem;">Get in Touch</h3>
            <div class="contact-info-item">
                <strong>📍 Address</strong>
                <p>123 Commerce St, Tech City</p>
            </div>
            <div class="contact-info-item">
                <strong>📧 Email</strong>
                <p>support@easycart.com</p>
            </div>
            <div>
                <strong>📞 Phone</strong>
                <p>+1 (555) 123-4567</p>
            </div>
        </div>

        <div class="contact-form-card">
            <h3 style="margin-bottom: 20px; font-size: 1.5rem; color: #111827;">Send Message</h3>
            <form id="contactForm" onsubmit="event.preventDefault(); submitContactForm(this);">
                <div class="contact-form-group">
                    <label class="contact-label">Name</label>
                    <input type="text" name="name" required class="contact-input">
                </div>
                <div class="contact-form-group">
                    <label class="contact-label">Email</label>
                    <input type="email" name="email" required class="contact-input">
                </div>
                <div class="contact-form-group">
                    <label class="contact-label">Message</label>
                    <textarea name="message" required rows="3" class="contact-input"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
            </form>
            
            <script>
            function submitContactForm(form) {
                const formData = new FormData(form);
                const btn = form.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;
                
                btn.innerHTML = 'Sending...';
                btn.disabled = true;
                
                fetch('contact_process', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        form.reset();
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred. Please try again.', 'error');
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            }
            </script>
        </div>
    </div>
</section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
