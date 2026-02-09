// Product detail page functionality - Quantity, shipping, and wishlist


/**
 * Increment quantity in product detail
 */
function incrementQty(max) {
    const quantityInput = document.getElementById('quantity');
    let currentQty = parseInt(quantityInput.value);

    if (currentQty < max) {
        currentQty++;
        quantityInput.value = currentQty;
        updateQuantityHidden();
    }
}

/**
 * Decrement quantity in product detail
 */
function decrementQty() {
    const quantityInput = document.getElementById('quantity');
    let currentQty = parseInt(quantityInput.value);

    if (currentQty > 1) {
        currentQty--;
        quantityInput.value = currentQty;
        updateQuantityHidden();
    }
}

/**
 * Update hidden quantity input
 */
function updateQuantityHidden() {
    const quantity = document.getElementById('quantity').value;
    const hiddenInput = document.getElementById('quantity-hidden');
    if (hiddenInput) {
        hiddenInput.value = quantity;
    }
}

/*
 * Handle Buy Now button click
 */
function buyNow(productId, maxStock) {
    // Check if user is logged in by looking for user menu elements
    const isLoggedIn = document.querySelector('.user-menu') !== null || document.querySelector('.user-btn') !== null;

    if (!isLoggedIn) {
        // Redirect to login page with redirect back to product detail
        window.location.href = 'signin.php?redirect=product-detail&id=' + productId;
        return false;
    }

    const quantity = document.getElementById('quantity').value;

    if (quantity > maxStock) {
        alert('Quantity cannot exceed available stock');
        return false;
    }

    // Add to cart via AJAX first so it merges with existing cart
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('cart', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to standard checkout (full cart)
                window.location.href = 'checkout?reset_shipping=1';
            } else {
                alert(data.message || 'Error processing request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });

    return false;
}

/**
 * Handle form submission for add to cart
 */
document.addEventListener('DOMContentLoaded', function () {
    const addToCartForm = document.querySelector('.add-to-cart-form');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function () {
            updateQuantityHidden();
        });
    }

    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.addEventListener('change', updateQuantityHidden);
    }
});

/**
 * Toggle product in/out of wishlist (product detail page)
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
    const wishlistBadge = document.querySelector('.wishlist-icon .badge');
    if (wishlistBadge) {
        const currentCount = parseInt(wishlistBadge.textContent);
        wishlistBadge.textContent = currentCount;
    }
}
