// Wishlist functionality - Toggle favorites across all pages

/**
 * Toggle product in/out of wishlist using AJAX
 */
function toggleWishlist(event, productId) {
    event.preventDefault();
    event.stopPropagation();

    const heartIcon = event.currentTarget;
    const isLiked = heartIcon.textContent.includes('❤️');
    const action = isLiked ? 'remove' : 'add';

    // Create form data for AJAX request
    const formData = new FormData();
    formData.append('action', action);
    formData.append('product_id', productId);

    // Send AJAX request
    fetch('wishlist.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Toggle the heart icon
                if (action === 'add') {
                    heartIcon.textContent = '❤️';
                    heartIcon.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        heartIcon.style.transform = 'scale(1)';
                    }, 200);
                    showToast('❤️ Added to wishlist!', 'success', 3000);
                } else {
                    heartIcon.textContent = '🤍';
                    heartIcon.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        heartIcon.style.transform = 'scale(1)';
                    }, 200);
                    showToast('💔 Removed from wishlist', 'info', 3000);

                    // If on wishlist page, remove the product card
                    if (window.location.pathname.includes('wishlist.php')) {
                        const productCard = heartIcon.closest('.product-card');
                        if (productCard) {
                            productCard.style.opacity = '0';
                            productCard.style.transform = 'scale(0.8)';
                            setTimeout(() => {
                                productCard.remove();
                                updateWishlistCount();
                                // Check if wishlist is empty
                                const remainingCards = document.querySelectorAll('.product-card');
                                if (remainingCards.length === 0) {
                                    location.reload(); // Reload to show empty state
                                }
                            }, 300);
                        }
                    }
                }

                // Update wishlist badge count using the count from server
                if (data.count !== undefined) {
                    updateWishlistBadge(0, data.count);
                } else {
                    updateWishlistBadge(action === 'add' ? 1 : -1);
                }
            } else {
                showToast('❌ Error: ' + (data.message || 'Could not update wishlist'), 'error');
            }
        })
        .catch(error => {
            console.error('Wishlist error:', error);
            // If not logged in, redirect to signin
            if (error.message && error.message.includes('login')) {
                window.location.href = 'signin.php?redirect=1';
            } else {
                showToast('⚠️ Please login to use wishlist', 'error');
                setTimeout(() => {
                    window.location.href = 'signin.php?redirect=1';
                }, 1500);
            }
        });
}

/**
 * Update the wishlist badge count
 */
function updateWishlistBadge(change = 0, newCount = null) {
    const wishlistBadge = document.getElementById('wishlist-badge') || document.querySelector('.wishlist-badge');
    if (wishlistBadge) {
        let currentCount;
        if (newCount !== null) {
            currentCount = newCount;
        } else {
            currentCount = parseInt(wishlistBadge.textContent) || 0;
            currentCount += change;
        }
        if (currentCount < 0) currentCount = 0;
        wishlistBadge.textContent = currentCount;

        // Hide badge if count is 0
        if (currentCount === 0) {
            wishlistBadge.style.display = 'none';
        } else {
            wishlistBadge.style.display = 'flex';
            // Add animation
            wishlistBadge.style.transform = 'scale(1.3)';
            setTimeout(() => {
                wishlistBadge.style.transform = 'scale(1)';
            }, 200);
        }
    }
}

/**
 * Update wishlist item count on the page
 */
function updateWishlistCount() {
    const countElement = document.querySelector('.section-title + div, [style*="background: #ffe8ee"]');
    if (countElement) {
        const remainingCards = document.querySelectorAll('.product-card').length;
        countElement.textContent = `${remainingCards} item${remainingCards !== 1 ? 's' : ''}`;
    }
}

// showToast is now handled by the global public/js/toast.js

/**
 * Initialize wishlist icons on page load
 */
function initWishlistIcons() {
    // Fetch current wishlist and update all heart icons
    fetch('wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_wishlist'
    })
        .then(response => response.json())
        .then(data => {
            if (data.wishlist && Array.isArray(data.wishlist)) {
                // Update all heart icons on the page (only elements with heart-icon class)
                document.querySelectorAll('.heart-icon').forEach(icon => {
                    const productId = parseInt(icon.dataset.productId || icon.getAttribute('data-product-id'));
                    if (data.wishlist.includes(productId)) {
                        icon.textContent = '❤️';
                    } else {
                        icon.textContent = '🤍';
                    }
                });
            }
        })
        .catch(error => {
            // User might not be logged in, ignore silently
            console.log('Wishlist not available');
        });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', initWishlistIcons);
