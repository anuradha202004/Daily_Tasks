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
 * Update all prices in checkout based on current quantities
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

    // Calculate tax (10%)
    const tax = subtotal * 0.10;

    // Calculate shipping (free if subtotal > 50, otherwise $9.99)
    const shipping = subtotal > 50 ? 0 : 9.99;

    // Calculate grand total
    const total = subtotal + tax + shipping;

    // Update all totals in the DOM
    document.getElementById('checkout-subtotal').textContent = formatPrice(subtotal);
    document.getElementById('checkout-tax').textContent = formatPrice(tax);

    const shippingElement = document.getElementById('checkout-shipping');
    if (shipping === 0) {
        shippingElement.textContent = '✓ Free';
        shippingElement.classList.add('free-shipping');
    } else {
        shippingElement.textContent = formatPrice(shipping);
        shippingElement.classList.remove('free-shipping');
    }

    document.getElementById('checkout-total').textContent = formatPrice(total);
    document.getElementById('btn-total').textContent = formatPrice(total);

    // Update item count
    const totalItems = Array.from(summaryItems).reduce((sum, item) => {
        return sum + parseInt(item.querySelector('.qty-input-small').value);
    }, 0);

    const itemCountElement = document.querySelector('.item-count');
    if (itemCountElement) {
        itemCountElement.textContent = totalItems + ' item' + (totalItems > 1 ? 's' : '');
    }
}

/**
 * Initialize checkout page
 */
document.addEventListener('DOMContentLoaded', function () {
    // Add smooth transitions
    const summaryItems = document.querySelectorAll('.summary-item');
    summaryItems.forEach(item => {
        item.style.transition = 'all 0.3s ease';
    });

    // Initial calculation
    updateCheckoutPrices();
});
