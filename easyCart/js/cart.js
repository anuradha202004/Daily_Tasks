// Cart page functionality - Quantity controls and cart management

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
