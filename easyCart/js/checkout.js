// Checkout page - Dynamic quantity and price updates

/**
 * Format price to USD currency
 */
function formatPrice(amount) {
    return '$' + parseFloat(amount).toFixed(2);
}

/**
 * Sync quantity with session via AJAX
 */
function syncQuantityWithSession(productId, quantity) {
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('cart.php', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart badge if needed
                const badge = document.querySelector('.badge, #cart-badge');
                if (badge) badge.textContent = data.cartCount;
            }
        })
        .catch(err => console.error('Sync failed:', err));
}

/**
 * Increment quantity in checkout summary
 */
function incrementCheckoutQty(button) {
    const summaryItem = button.closest('.summary-item');
    const qtyInput = summaryItem.querySelector('.qty-input-small');
    const productId = summaryItem.dataset.productId;
    const maxStock = parseInt(summaryItem.dataset.stock);
    let currentQty = parseInt(qtyInput.value);

    if (currentQty < maxStock) {
        currentQty++;
        qtyInput.value = currentQty;
        updateCheckoutPrices();
        syncQuantityWithSession(productId, currentQty);
    }
}

/**
 * Decrement quantity in checkout summary
 */
function decrementCheckoutQty(button) {
    const summaryItem = button.closest('.summary-item');
    const qtyInput = summaryItem.querySelector('.qty-input-small');
    const productId = summaryItem.dataset.productId;
    let currentQty = parseInt(qtyInput.value);

    if (currentQty > 1) {
        currentQty--;
        qtyInput.value = currentQty;
        updateCheckoutPrices();
        syncQuantityWithSession(productId, currentQty);
    }
}

/**
 * Save shipping method to session via AJAX
 */
function saveShippingToSession(method) {
    const formData = new FormData();
    formData.append('action', 'save_shipping');
    formData.append('method', method);

    fetch('checkout.php', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) console.error('Failed to save shipping method');
        })
        .catch(err => console.error('Shipping save failed:', err));
}

/**
 * Update shipping cost based on selected method and subtotal
 */
function updateShippingCost() {
    updateCheckoutPrices();

    // Update selected class on shipping options
    const options = document.querySelectorAll('.shipping-option');
    let selectedMethod = 'standard';

    options.forEach(opt => {
        const input = opt.querySelector('input');
        if (input.checked) {
            opt.classList.add('selected');
            selectedMethod = input.value;
        } else {
            opt.classList.remove('selected');
        }
    });

    // Save to session
    saveShippingToSession(selectedMethod);
}

/**
 * Update all prices in checkout based on current quantities and shipping
 */
function updateCheckoutPrices() {
    const summaryItems = document.querySelectorAll('.summary-item');
    let subtotal = 0;
    let discount = 0;

    // Calculate new subtotal and update each item's price
    summaryItems.forEach(item => {
        const price = parseFloat(item.dataset.productPrice);
        const qty = parseInt(item.querySelector('.qty-input-small').value);
        const itemTotal = price * qty;

        // Calculate bulk discount for this item
        if (qty >= 10) {
            discount += itemTotal * 0.20;
        } else if (qty >= 5) {
            discount += itemTotal * 0.10;
        }

        // Update item total
        const itemPriceEl = item.querySelector('.item-price');
        if (itemPriceEl) itemPriceEl.textContent = formatPrice(itemTotal);

        // Add to subtotal
        subtotal += itemTotal;
    });

    // Get selected shipping method
    const selectedMethodInput = document.querySelector('input[name="shipping_method"]:checked');
    const selectedMethod = selectedMethodInput ? selectedMethodInput.value : 'standard';
    let shipping = 40.00;

    switch (selectedMethod) {
        case 'standard':
            shipping = 40.00;
            break;
        case 'express':
            shipping = Math.min(80.00, subtotal * 0.10);
            break;
        case 'whiteglove':
            shipping = Math.min(150.00, subtotal * 0.05);
            break;
        case 'freight':
            shipping = Math.max(200.00, subtotal * 0.03);
            break;
    }

    // Update shipping method name in summary
    const methodLabel = document.querySelector(`label[for="shipping_${selectedMethod}"] h4`);
    const methodName = methodLabel ? methodLabel.textContent : 'Standard Shipping';
    const methodNameElement = document.getElementById('shipping-method-name');
    if (methodNameElement) {
        methodNameElement.textContent = `(${methodName})`;
    }

    // Calculate tax (18% on Subtotal - Discount + Shipping)
    const taxableAmount = Math.max(0, subtotal - discount + shipping);
    const tax = taxableAmount * 0.18;

    // Calculate grand total
    const total = subtotal - discount + shipping + tax;

    // Update all totals in the DOM
    const subtotalEl = document.getElementById('checkout-subtotal');
    if (subtotalEl) subtotalEl.textContent = formatPrice(subtotal);

    const discountEl = document.getElementById('checkout-discount');
    if (discountEl) discountEl.textContent = '-' + formatPrice(discount);

    const shippingEl = document.getElementById('checkout-shipping');
    if (shippingEl) shippingEl.textContent = formatPrice(shipping);

    const taxEl = document.getElementById('checkout-tax');
    if (taxEl) taxEl.textContent = formatPrice(tax);

    const totalEl = document.getElementById('checkout-total');
    if (totalEl) totalEl.textContent = formatPrice(total);

    const btnTotalEl = document.getElementById('btn-total');
    if (btnTotalEl) btnTotalEl.textContent = formatPrice(total);

    // Update item count
    const totalQuantity = Array.from(summaryItems).reduce((sum, item) => {
        const qtyInput = item.querySelector('.qty-input-small');
        return sum + (qtyInput ? parseInt(qtyInput.value) : 0);
    }, 0);

    const itemCountElement = document.querySelector('.item-count');
    if (itemCountElement) {
        itemCountElement.textContent = totalQuantity + ' item' + (totalQuantity !== 1 ? 's' : '');
    }
}

/**
 * Initialize checkout page
 */
document.addEventListener('DOMContentLoaded', function () {
    // Initial calculation
    updateCheckoutPrices();
});
