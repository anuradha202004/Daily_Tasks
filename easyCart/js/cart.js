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

    fetch('cart.php', {
        method: 'POST',
        body: formData
        
    })
        .then(response => {
            // Show alert message FIRST
            alert('Product added successfully');

            // Update cart badge manually
            const cartBadge = document.getElementById('cart-badge') || document.querySelector('.badge');
            if (cartBadge) {
                const currentCount = parseInt(cartBadge.textContent) || 0;
                cartBadge.textContent = currentCount + 1;
                cartBadge.style.display = 'flex';
                // Add animation
                cartBadge.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    cartBadge.style.transform = 'scale(1)';
                }, 200);
            }

            // Reset button
            if (btn) {
                btn.innerHTML = '✓ Added!';
                btn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = '';
                    btn.disabled = false;
                }, 1500);
            }
        })
        .catch(error => {
            console.error('Cart error:', error);
            alert('Error adding to cart');
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
    const cartBadge = document.getElementById('cart-badge') || document.querySelector('.cart-badge');
    if (cartBadge) {
        cartBadge.textContent = count;
        if (count > 0) {
            cartBadge.style.display = 'flex';
            // Add animation
            cartBadge.style.transform = 'scale(1.3)';
            setTimeout(() => {
                cartBadge.style.transform = 'scale(1)';
            }, 200);
        } else {
            cartBadge.style.display = 'none';
        }
    }
}

/**
 * Show toast notification for cart
 */
function showCartToast(message, type = 'info') {
    // Remove existing toast
    const existingToast = document.querySelector('.cart-toast');
    if (existingToast) {
        existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.className = 'cart-toast';
    toast.style.cssText = `
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        z-index: 10000;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        gap: 10px;
    `;

    if (type === 'success') {
        toast.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        toast.style.color = 'white';
        toast.innerHTML = `<span>🛒</span> ${message}`;
    } else if (type === 'error') {
        toast.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
        toast.style.color = 'white';
        toast.innerHTML = `<span>⚠️</span> ${message}`;
    } else {
        toast.style.background = 'linear-gradient(135deg, #3b82f6, #2563eb)';
        toast.style.color = 'white';
        toast.innerHTML = `<span>ℹ️</span> ${message}`;
    }

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transform = 'translateX(-50%) translateY(0)';
    }, 10);

    setTimeout(() => {
        toast.style.transform = 'translateX(-50%) translateY(100px)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

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
        updateCartItemTotal(cartItem);
        updateOrderSummary();
        updateQuantityAndSummary(quantityInput);
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
        updateCartItemTotal(cartItem);
        updateOrderSummary();
        updateQuantityAndSummary(quantityInput);
    }
}

/**
 * Update quantity and cart summary when quantity input changes
 */
function updateQuantityAndSummary(quantityInput) {
    const cartItem = quantityInput.closest('.cart-item');

    if (quantityInput.value <= 0) {
        quantityInput.value = 1;
    }

    const maxStock = parseInt(quantityInput.max);
    if (parseInt(quantityInput.value) > maxStock) {
        quantityInput.value = maxStock;
    }

    updateCartItemTotal(cartItem);
    updateOrderSummary();

    // Auto-submit to save quantity
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '';

    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'update';

    const productInput = document.createElement('input');
    productInput.type = 'hidden';
    productInput.name = 'product_id';
    productInput.value = cartItem.dataset.productId;

    const quantityVal = document.createElement('input');
    quantityVal.type = 'hidden';
    quantityVal.name = 'quantity';
    quantityVal.value = quantityInput.value;

    form.appendChild(actionInput);
    form.appendChild(productInput);
    form.appendChild(quantityVal);
    document.body.appendChild(form);
    form.submit();
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
        totalElement.textContent = formatCurrency(itemTotal);
    }
}

/**
 * Remove an item from the cart
 */
function removeCartItem(btn) {
    const cartItem = btn.closest('.cart-item');
    const productId = cartItem.dataset.productId;

    if (confirm('Are you sure you want to remove this item from your cart?')) {
        // Create and submit hidden form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'remove';

        const productInput = document.createElement('input');
        productInput.type = 'hidden';
        productInput.name = 'product_id';
        productInput.value = productId;

        form.appendChild(actionInput);
        form.appendChild(productInput);
        document.body.appendChild(form);
        form.submit();
    }
}

/**
 * Update the order summary with current cart totals
 */
function updateOrderSummary() {
    // Calculate new subtotal from all cart items
    let newSubtotal = 0;
    document.querySelectorAll('.cart-item').forEach(item => {
        const price = parseFloat(item.dataset.productPrice);
        const quantity = parseInt(item.querySelector('.quantity-input').value);
        newSubtotal += price * quantity;
    });

    const newTax = newSubtotal * 0.10;
    const shippingCost = newSubtotal > 50 ? 0 : 9.99;
    const newTotal = newSubtotal + newTax + shippingCost;

    // Update summary display
    document.getElementById('summary-subtotal').textContent = formatCurrency(newSubtotal);
    document.getElementById('summary-tax').textContent = formatCurrency(newTax);
    document.getElementById('summary-shipping').textContent = shippingCost === 0 ? 'Free' : formatCurrency(shippingCost);
    document.getElementById('summary-total').textContent = formatCurrency(newTotal);
}
