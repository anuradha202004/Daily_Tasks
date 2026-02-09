// Cart functionality - Quantity controls, cart management, and AJAX add to cart

/**
 * Add product to cart via AJAX
 */
function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    // Show loading state on button
    const btn = document.querySelector(`[data-product-id="${productId}"].btn-add-cart`);
    const originalText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.innerHTML = '⏳ Adding...';
        btn.disabled = true;
    }

    fetch('cart', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                updateCartBadge(data.cartCount);
                if (btn) {
                    btn.innerHTML = '✓ Added!';
                    btn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.style.background = '';
                        btn.disabled = false;
                    }, 1500);
                }
            } else {
                showToast(data.message || 'Error adding to cart', 'error');
                if (btn) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            }
        })
        .catch(error => {
            console.error('Cart error:', error);
            showToast('Error adding to cart', 'error');
            if (btn) {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
}

/**
 * Update cart badge count
 */
function updateCartBadge(count) {
    console.log('Updating cart badge count:', count);

    // Specific selector for cart badge only
    const badgeElements = document.querySelectorAll('.cart-badge, #cart-badge');
    console.log('Found badge elements:', badgeElements.length);

    badgeElements.forEach(cartBadge => {
        if (cartBadge) {
            cartBadge.textContent = count;
            cartBadge.setAttribute('data-count', count); // Helper for debugging

            if (count > 0) {
                cartBadge.classList.remove('d-none');
                cartBadge.classList.add('d-flex');
                cartBadge.style.display = 'flex'; // Force display inline style
                cartBadge.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    cartBadge.style.transform = 'scale(1)';
                }, 200);
            } else {
                cartBadge.classList.remove('d-flex');
                cartBadge.classList.add('d-none');
                cartBadge.style.display = 'none';
            }
        }
    });
}

// showCartToast replaced by global showToast from toast.js

/**
 * Increase quantity for a cart item
 */
function increaseQuantity(btn) {
    const cartItem = btn.closest('.cart-item');
    const quantityInput = cartItem.querySelector('.quantity-input');
    const maxStock = parseInt(quantityInput.max);
    const currentQty = parseInt(quantityInput.value);

    if (currentQty < maxStock) {
        quantityInput.value = currentQty + 1;
        updateQuantityAjax(cartItem, quantityInput.value);
    }
}

/**
 * Decrease quantity for a cart item
 */
function decreaseQuantity(btn) {
    const cartItem = btn.closest('.cart-item');
    const quantityInput = cartItem.querySelector('.quantity-input');
    const currentQty = parseInt(quantityInput.value);

    if (currentQty > 1) {
        quantityInput.value = currentQty - 1;
        updateQuantityAjax(cartItem, quantityInput.value);
    }
}

/**
 * Update quantity and cart summary via AJAX
 */
function updateQuantityAjax(cartItem, quantity) {
    const productId = cartItem.dataset.productId;
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    // Show loading state
    cartItem.style.opacity = '0.7';

    fetch('cart', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            cartItem.style.opacity = '1';
            if (data.success) {
                updateCartItemTotal(cartItem);
                updateSummaryDisplay(data);
                updateCartBadge(data.cartCount);
            }
        })
        .catch(error => {
            cartItem.style.opacity = '1';
            console.error('Update error:', error);
        });
}

function updateQuantityAndSummary(input) {
    const cartItem = input.closest('.cart-item');
    const val = parseInt(input.value);
    const max = parseInt(input.max);

    if (isNaN(val) || val < 1) input.value = 1;
    if (val > max) input.value = max;

    updateQuantityAjax(cartItem, input.value);
}

/**
 * Update the total price for a single cart item
 */
function updateCartItemTotal(cartItem) {
    const price = parseFloat(cartItem.dataset.productPrice);
    const quantity = parseInt(cartItem.querySelector('.quantity-input').value);
    const itemTotal = price * quantity;

    const totalElement = cartItem.querySelector('.item-total');
    if (totalElement) {
        totalElement.textContent = '$' + itemTotal.toFixed(2);
    }
}

/**
 * Remove an item from the cart via AJAX
 */
function removeCartItem(btn) {
    const cartItem = btn.closest('.cart-item');
    const productId = cartItem.dataset.productId;

    if (confirm('Are you sure you want to remove this item?')) {
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('product_id', productId);

        cartItem.style.transform = 'translateX(100px)';
        cartItem.style.opacity = '0';

        fetch('cart', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI immediately (don't wait for animation)
                    if (data.cartCount > 0) {
                        updateSummaryDisplay(data);
                        updateCartBadge(data.cartCount);

                        // Update cart page specific header
                        const pageCountEl = document.getElementById('cart-page-count');
                        if (pageCountEl) {
                            pageCountEl.textContent = data.cartCount + ' Item' + (data.cartCount !== 1 ? 's' : '') + ' in Cart';
                        }
                    }

                    // Remove element after animation
                    setTimeout(() => {
                        cartItem.remove();
                        if (data.cartCount === 0) {
                            location.reload(); // Reload to show empty cart message
                        }
                    }, 300);
                } else {
                    cartItem.style.transform = 'none';
                    cartItem.style.opacity = '1';
                }
            })
            .catch(error => {
                cartItem.style.transform = 'none';
                cartItem.style.opacity = '1';
                console.error('Remove error:', error);
            });
    }
}

/**
 * Update the order summary display with data from AJAX
 */
function updateSummaryDisplay(data) {
    const subtotalEl = document.getElementById('summary-subtotal');
    const discountEl = document.getElementById('summary-discount');
    const taxEl = document.getElementById('summary-tax');
    const shippingEl = document.getElementById('summary-shipping');
    const totalEl = document.getElementById('summary-total');

    if (subtotalEl) subtotalEl.textContent = data.formattedSubtotal;
    if (discountEl) discountEl.textContent = data.formattedDiscount;
    if (taxEl) taxEl.textContent = data.formattedTax;
    if (shippingEl) shippingEl.textContent = data.formattedShipping;
    if (totalEl) totalEl.textContent = data.formattedTotal;
}
