// Common utility functions used across the application

/**
 * Format number as currency with $ symbol
 */
function formatCurrency(value) {
    return '$' + parseFloat(value).toFixed(2);
}

/**
 * Add event listener for DOM ready
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded and DOM ready');
    
    // Make entire product card clickable
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't navigate if clicking on wishlist heart or buttons
            if (e.target.closest('.heart-icon') || e.target.closest('.btn')) {
                return;
            }
            
            // Find the View Details link and navigate
            const viewDetailsLink = this.querySelector('a[href*="product-detail.php"]');
            if (viewDetailsLink) {
                window.location.href = viewDetailsLink.getAttribute('href');
            }
        });
        
        // Add cursor pointer to indicate clickability
        card.style.cursor = 'pointer';
    });
});
