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
 * Apply Promo Code
 */
function applyPromoCode() {
    const input = document.getElementById('promo-code-input');
    const btn = document.getElementById('promo-btn');
    const msg = document.getElementById('promo-message');
    const code = input.value.trim();

    if (!code) return;

    btn.textContent = '...';
    btn.disabled = true;

    const formData = new FormData();
    formData.append('action', 'apply_promo');
    formData.append('code', code);

    fetch('checkout.php', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                input.disabled = true;
                btn.textContent = 'Remove';
                btn.setAttribute('onclick', 'removePromoCode()');
                msg.textContent = data.message;
                msg.style.color = '#10b981';

                // Set active promo based on code (matching server logic)
                if (code.toUpperCase() === 'SAVE10') {
                    window.activePromo = { type: 'percent', value: 10, code: 'SAVE10' };
                } else if (code.toUpperCase() === 'FLAT50') {
                    window.activePromo = { type: 'fixed', value: 50, code: 'FLAT50' };
                }
                updateCheckoutPrices();
            } else {
                btn.textContent = 'Apply';
                msg.textContent = data.message;
                msg.style.color = '#ef4444';
            }
        })
        .catch(err => {
            btn.textContent = 'Apply';
            btn.disabled = false;
            console.error(err);
        });
}

/**
 * Remove Promo Code
 */
function removePromoCode() {
    const input = document.getElementById('promo-code-input');
    const btn = document.getElementById('promo-btn');
    const msg = document.getElementById('promo-message');

    btn.textContent = '...';
    btn.disabled = true;

    const formData = new FormData();
    formData.append('action', 'remove_promo');

    fetch('checkout.php', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                input.disabled = false;
                input.value = '';
                btn.textContent = 'Apply';
                btn.setAttribute('onclick', 'applyPromoCode()');
                msg.textContent = '';
                window.activePromo = null;
                updateCheckoutPrices();
            }
        });
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

        // Calculate bulk discount: 1% discount per item (e.g. 2 items = 2%, 5 items = 5%)
        if (qty > 0) {
            discount += itemTotal * (qty / 100);
        }

        // Update item total
        const itemPriceEl = item.querySelector('.item-price');
        if (itemPriceEl) itemPriceEl.textContent = formatPrice(itemTotal);

        // Add to subtotal
        subtotal += itemTotal;
    });

    // Calculate Promo Discount
    let promoDiscount = 0;
    if (window.activePromo) {
        if (window.activePromo.type === 'percent') {
            promoDiscount = subtotal * (window.activePromo.value / 100);
        } else {
            promoDiscount = parseFloat(window.activePromo.value);
        }
        // Cap at subtotal - bulkDiscount
        if ((subtotal - discount - promoDiscount) < 0) {
            promoDiscount = Math.max(0, subtotal - discount);
        }
    }

    // Get selected shipping method
    const shippingOptions = document.querySelectorAll('.shipping-option');
    let autoSwitchNeeded = false;
    let selectedMethodInput = document.querySelector('input[name="shipping_method"]:checked');

    // Update shipping method availability based on subtotal
    shippingOptions.forEach(option => {
        const input = option.querySelector('input[type="radio"]');
        const method = input.value;
        let isDisabled = false;

        if (subtotal < 300) {
            if (method === 'whiteglove' || method === 'freight') isDisabled = true;
        } else {
            if (method === 'standard' || method === 'express') isDisabled = true;
        }

        input.disabled = isDisabled;
        if (isDisabled) {
            option.style.opacity = '0.5';
            option.style.pointerEvents = 'none';
            if (input.checked) {
                input.checked = false;
                option.classList.remove('selected');
                autoSwitchNeeded = true;
                selectedMethodInput = null;
            }
        } else {
            option.style.opacity = '1';
            option.style.pointerEvents = 'auto';
        }
    });

    if (autoSwitchNeeded || !selectedMethodInput) {
        // Select the first enabled option
        const enabledInput = document.querySelector('input[name="shipping_method"]:not([disabled])');
        if (enabledInput) {
            enabledInput.checked = true;
            enabledInput.closest('.shipping-option').classList.add('selected');
            selectedMethodInput = enabledInput;
        }
    }

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

    // Calculate Discounted Subtotal
    const discountedSubtotal = Math.max(0, subtotal - discount - promoDiscount);

    // Calculate tax (18% on Discounted Subtotal + Shipping)
    const taxableAmount = Math.max(0, discountedSubtotal + shipping);
    const tax = taxableAmount * 0.18;

    // Calculate grand total
    const total = discountedSubtotal + shipping + tax;

    // Update all totals in the DOM
    const subtotalEl = document.getElementById('checkout-subtotal');
    if (subtotalEl) subtotalEl.textContent = formatPrice(subtotal);

    const discountEl = document.getElementById('checkout-discount');
    if (discountEl) discountEl.textContent = '-' + formatPrice(discount);

    // Update Promo Row
    const promoRow = document.getElementById('promo-row');
    const promoVal = document.getElementById('checkout-promo');
    if (promoRow && promoVal) {
        if (promoDiscount > 0) {
            promoRow.style.display = 'flex';
            promoVal.textContent = '-' + formatPrice(promoDiscount);
        } else {
            promoRow.style.display = 'none';
        }
    }

    // Update Discounted Subtotal
    const discountedSubtotalEl = document.getElementById('checkout-discounted-subtotal');
    if (discountedSubtotalEl) discountedSubtotalEl.textContent = formatPrice(discountedSubtotal);

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
