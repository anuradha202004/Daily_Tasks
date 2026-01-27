// Checkout page - Dynamic quantity and price updates

/**
 * Format price to USD currency
 */
function formatPrice(amount) {
    return '$' + parseFloat(amount).toFixed(2);
}

/**
 * Increment quantity in checkout summary
 */
function incrementCheckoutQty(button) {
    const summaryItem = button.closest('.summary-item');
    const qtyInput = summaryItem.querySelector('.qty-input-small');
    const maxStock = parseInt(summaryItem.dataset.stock);
    let currentQty = parseInt(qtyInput.value);

    if (currentQty < maxStock) {
        currentQty++;
        qtyInput.value = currentQty;
        updateCheckoutPrices();
    }
}

/**
 * Decrement quantity in checkout summary
 */
function decrementCheckoutQty(button) {
    const summaryItem = button.closest('.summary-item');
    const qtyInput = summaryItem.querySelector('.qty-input-small');
    let currentQty = parseInt(qtyInput.value);

    if (currentQty > 1) {
        currentQty--;
        qtyInput.value = currentQty;
        updateCheckoutPrices();
    }
}

/**
 * Update shipping cost based on selected method and subtotal
 */
function updateShippingCost() {
    updateCheckoutPrices();

    // Update selected class on shipping options
    const options = document.querySelectorAll('.shipping-option');
    options.forEach(opt => {
        const input = opt.querySelector('input');
        if (input.checked) {
            opt.classList.add('selected');
        } else {
            opt.classList.remove('selected');
        }
    });
}

/**
 * Update all prices in checkout based on current quantities and shipping
 */
function updateCheckoutPrices() {
    const summaryItems = document.querySelectorAll('.summary-item');
    let subtotal = 0;

    // Calculate new subtotal and update each item's price
    summaryItems.forEach(item => {
        const price = parseFloat(item.dataset.productPrice);
        const qty = parseInt(item.querySelector('.qty-input-small').value);
        const itemTotal = price * qty;

        // Update item total
        item.querySelector('.item-price').textContent = formatPrice(itemTotal);

        // Add to subtotal
        subtotal += itemTotal;
    });

    // Get selected shipping method
    const selectedMethod = document.querySelector('input[name="shipping_method"]:checked')?.value || 'standard';
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
    const methodName = document.querySelector(`label[for="shipping_${selectedMethod}"] h4`)?.textContent || 'Standard Shipping';
    const methodNameElement = document.getElementById('shipping-method-name');
    if (methodNameElement) {
        methodNameElement.textContent = `(${methodName})`;
    }

    // Calculate tax (18% on Subtotal + Shipping)
    const tax = (subtotal + shipping) * 0.18;

    // Calculate grand total
    const total = subtotal + shipping + tax;

    // Update all totals in the DOM
    document.getElementById('checkout-subtotal').textContent = formatPrice(subtotal);

    const shippingElement = document.getElementById('checkout-shipping');
    if (shippingElement) {
        shippingElement.textContent = formatPrice(shipping);
    }

    document.getElementById('checkout-tax').textContent = formatPrice(tax);
    document.getElementById('checkout-total').textContent = formatPrice(total);
    document.getElementById('btn-total').textContent = formatPrice(total);

    // Update item count
    const totalQuantity = Array.from(summaryItems).reduce((sum, item) => {
        return sum + parseInt(item.querySelector('.qty-input-small').value);
    }, 0);

    const itemCountElement = document.querySelector('.item-count');
    if (itemCountElement) {
        itemCountElement.textContent = totalQuantity + ' item' + (totalQuantity > 1 ? 's' : '');
    }
}

/**
 * Initialize checkout page
 */
document.addEventListener('DOMContentLoaded', function () {
    // Initial calculation
    updateCheckoutPrices();
});
