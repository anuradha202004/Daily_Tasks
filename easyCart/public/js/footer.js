// Footer functionality - Add to Cart

/**
 * Add product to cart with specified quantity
 */
// Footer functionality - Cleaned up to remove conflicting addToCart
// Modal functionality remains


/**
 * Close modal when clicking outside
 */
window.onclick = function (event) {
    const modal = document.getElementById('accountModal');
    if (modal && event.target === modal) {
        closeAccountModal();
    }
}
