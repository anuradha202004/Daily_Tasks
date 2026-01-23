<?php
session_start();

// Include data and auth
require_once 'data.php';
require_once 'auth.php';

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
<?php include 'header.php'; ?>

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
                    <div class="ad-slide active" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="ad-content">
                            <div class="ad-badge">✨ NEW COLLECTION</div>
                            <h2 class="ad-title">Summer Collection 2026</h2>
                            <p class="ad-description">Discover the latest trending products</p>
                            <a href="products.php?category=1" class="ad-cta">Shop Collection →</a>
                        </div>
                        <div class="ad-visual">👕</div>
                    </div>

                    <!-- Ad 2 -->
                    <div class="ad-slide" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="ad-content">
                            <div class="ad-badge">🔥 HOT DEALS</div>
                            <h2 class="ad-title">Up to 70% OFF</h2>
                            <p class="ad-description">Limited time exclusive offers</p>
                            <a href="products.php" class="ad-cta">Grab Deals →</a>
                        </div>
                        <div class="ad-visual">🎁</div>
                    </div>

                    <!-- Ad 3 -->
                    <div class="ad-slide" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="ad-content">
                            <div class="ad-badge">⭐ PREMIUM QUALITY</div>
                            <h2 class="ad-title">Best Sellers</h2>
                            <p class="ad-description">Top-rated products at best prices</p>
                            <a href="products.php" class="ad-cta">View Best Sellers →</a>
                        </div>
                        <div class="ad-visual">🏆</div>
                    </div>

                    <!-- Ad 4 -->
                    <div class="ad-slide" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <div class="ad-content">
                            <div class="ad-badge">💎 EXCLUSIVE</div>
                            <h2 class="ad-title">VIP Member Benefits</h2>
                            <p class="ad-description">Unlock premium rewards today</p>
                            <a href="products.php" class="ad-cta">Join VIP →</a>
                        </div>
                        <div class="ad-visual">👑</div>
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
                <div class="product-card" style="position: relative;">
                    <!-- Wishlist Heart Icon (only for logged in users) -->
                    <?php if (isLoggedIn()): ?>
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
                            <?php echo $isWishlisted ? '❤️' : '🤍'; ?>
                        </div>
                    <?php endif; ?>
                    <div class="product-image"><?php echo $product['emoji']; ?></div>
                    <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="product-rating"><?php echo renderStars($product['rating']); ?> <?php echo $product['rating']; ?> (<?php echo $product['reviews']; ?> reviews)</div>
                    <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                    <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                    <div class="product-footer">
                        <span class="stock-info">Stock: <?php echo $product['stock']; ?> units</span>
                        <div class="product-actions">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                            <?php if ($product['stock'] > 0): ?>
                                <a href="checkout.php?product_id=<?php echo $product['id']; ?>&qty=1" class="btn btn-buy-now">Buy Now</a>
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
                <div class="product-card" style="position: relative;">
                    <!-- Wishlist Heart Icon (only for logged in users) -->
                    <?php if (isLoggedIn()): ?>
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
                            <?php echo $isWishlisted ? '❤️' : '🤍'; ?>
                        </div>
                    <?php endif; ?>
                    <div class="product-image"><?php echo $product['emoji']; ?></div>
                    <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="product-rating"><?php echo renderStars($product['rating']); ?> <?php echo $product['rating']; ?> (<?php echo $product['reviews']; ?> reviews)</div>
                    <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                    <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                    <div class="product-footer">
                        <span class="stock-info">Stock: <?php echo $product['stock']; ?> units</span>
                        <div class="product-actions">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                            <?php if ($product['stock'] > 0): ?>
                                <a href="checkout.php?product_id=<?php echo $product['id']; ?>&qty=1" class="btn btn-buy-now">Buy Now</a>
                            <?php else: ?>
                                <button class="btn btn-disabled" disabled>Out of Stock</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="container">
        <h2 class="section-title">About EasyCart</h2>
        <div class="about-section">
            <p>EasyCart is a professional e-commerce platform designed to provide seamless online shopping experiences for businesses and customers alike. Built with modern web technologies, our platform offers a comprehensive solution for digital commerce.</p>
            
            <h3>Our Mission</h3>
            <p>To empower businesses with cutting-edge e-commerce tools while delivering exceptional shopping experiences to customers through innovative technology and user-centric design.</p>
            
            <h3>Key Features</h3>
            <ul>
                <li>Intuitive product browsing with advanced filtering and search capabilities</li>
                <li>Seamless shopping cart and wishlist management</li>
                <li>Secure checkout process with multiple payment options</li>
                <li>Responsive design optimized for all devices</li>
                <li>Real-time inventory tracking and management</li>
                <li>Customer reviews and ratings system</li>
                <li>Fast and reliable delivery services</li>
                <li>24/7 customer support</li>
            </ul>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="container">
        <h2 class="section-title">Contact Us</h2>
        <div class="contact-grid">
            <div class="contact-card">
                <h3>Get in Touch</h3>
                <form>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
                </form>
            </div>

            <div class="contact-card">
                <h3>Contact Information</h3>
                <div class="info-item">
                    <h4>Address</h4>
                    <p>123 Commerce Street<br>Business District<br>City, State 12345</p>
                </div>
                <div class="info-item">
                    <h4>Phone</h4>
                    <p>+1 (555) 123-4567</p>
                </div>
                <div class="info-item">
                    <h4>Email</h4>
                    <p>support@easycart.com</p>
                </div>
                <div class="info-item">
                    <h4>Business Hours</h4>
                    <p>Monday - Friday: 9:00 AM - 6:00 PM<br>
                    Saturday: 10:00 AM - 4:00 PM<br>
                    Sunday: Closed</p>
                </div>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>

    <!-- Interactive Ad Carousel Script -->
    <script>
        let currentAdIndex = 0;
        const totalAds = 4;
        let autoplayInterval;

        function showAd(index) {
            // Get all slides and dots
            const slides = document.querySelectorAll('.ad-slide');
            const dots = document.querySelectorAll('.carousel-dots .dot');

            // Remove active class from all
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            // Add active class to current
            slides[index].classList.add('active');
            dots[index].classList.add('active');

            // Update progress bar
            const progress = ((index + 1) / totalAds) * 100;
            document.querySelector('.progress-fill').style.width = progress + '%';
        }

        function goToAd(index) {
            currentAdIndex = index;
            showAd(currentAdIndex);
            resetAutoplay();
        }

        function nextAd() {
            currentAdIndex = (currentAdIndex + 1) % totalAds;
            showAd(currentAdIndex);
            resetAutoplay();
        }

        function prevAd() {
            currentAdIndex = (currentAdIndex - 1 + totalAds) % totalAds;
            showAd(currentAdIndex);
            resetAutoplay();
        }

        function resetAutoplay() {
            clearInterval(autoplayInterval);
            startAutoplay();
        }

        function startAutoplay() {
            autoplayInterval = setInterval(() => {
                nextAd();
            }, 5000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            startAutoplay();
        });

        // Wishlist toggle functionality
        function toggleWishlist(event, productId) {
            event.preventDefault();
            event.stopPropagation();
            
            const heartIcon = event.currentTarget;
            const isLiked = heartIcon.textContent.includes('❤️');
            
            const formData = new FormData();
            formData.append('action', isLiked ? 'remove' : 'add');
            formData.append('product_id', productId);
            
            fetch('wishlist.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    heartIcon.textContent = isLiked ? '🤍' : '❤️';
                    updateWishlistBadge();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update wishlist');
            });
        }
        
        function updateWishlistBadge() {
            const badge = document.querySelector('.wishlist-icon .badge');
            if (badge) {
                const formData = new FormData();
                formData.append('action', 'get_count');
                
                fetch('wishlist.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.count !== undefined) {
                        badge.textContent = data.count;
                    }
                })
                .catch(error => console.error('Error updating badge:', error));
            }
        }
    </script>
