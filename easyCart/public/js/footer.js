// Footer functionality - Add to Cart

/**
 * Add product to cart with specified quantity
 */
function addToCart(productId, quantity = 1) {
    quantity = quantity || 1;
    
    // Create form to submit to cart.php
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'cart.php';
    
    const productInput = document.createElement('input');
    productInput.type = 'hidden';
    productInput.name = 'action';
    productInput.value = 'add';
    
    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'product_id';
    idInput.value = productId;
    
    const qtyInput = document.createElement('input');
    qtyInput.type = 'hidden';
    qtyInput.name = 'quantity';
    qtyInput.value = quantity;
    
    form.appendChild(productInput);
    form.appendChild(idInput);
    form.appendChild(qtyInput);
    document.body.appendChild(form);
    form.submit();
}

/**
 * Close modal when clicking outside
 */
window.onclick = function(event) {
    const modal = document.getElementById('accountModal');
    if (modal && event.target === modal) {
        closeAccountModal();
    }
}
