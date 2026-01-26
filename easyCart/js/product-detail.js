// Product detail page functionality - Quantity, shipping, and wishlist

/**
 * Select shipping option
 */
function selectShipping(element) {
    // Remove active class from all options
    document.querySelectorAll('.shipping-option').forEach(option => {
        option.classList.remove('active');
    });

    // Add active class to selected option
    element.classList.add('active');

    // Update hidden input
    const shippingInput = document.querySelector('input[name="shipping"]');
    if (shippingInput) {
        shippingInput.value = element.dataset.value;
    }
}

/**
 * Initialize shipping option selection
 */
document.addEventListener('DOMContentLoaded', function () {
    const defaultShipping = document.querySelector('.shipping-option');
    if (defaultShipping) {
        defaultShipping.addEventListener('click', function () {
            selectShipping(this);
        });
    }
});

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
    const hiddenInput = document.querySelector('input[name="quantity"]');
    if (hiddenInput) {
        hiddenInput.value = quantity;
    }
}

/**
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

    // Navigate to checkout page
    window.location.href = `checkout.php?product_id=${productId}&qty=${quantity}`;
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
