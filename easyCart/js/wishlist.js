// Wishlist functionality - Toggle favorites across all pages

/**
 * Toggle product in/out of wishlist
 */
function toggleWishlist(event, productId) {
    event.preventDefault();
    event.stopPropagation();
    
    const heartIcon = event.currentTarget;
    const isLiked = heartIcon.textContent.includes('❤️');
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'wishlist.php';
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = isLiked ? 'remove' : 'add';
    
    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'product_id';
    idInput.value = productId;
    
    form.appendChild(actionInput);
    form.appendChild(idInput);
    document.body.appendChild(form);
    form.submit();
}

/**
 * Update the wishlist badge count
 */
function updateWishlistBadge() {
    // This can be called after AJAX updates
    // The badge will be auto-updated by the page reload or AJAX response
    const wishlistBadge = document.querySelector('.wishlist-icon .badge');
    if (wishlistBadge) {
        const currentCount = parseInt(wishlistBadge.textContent);
        wishlistBadge.textContent = currentCount;
    }
}
