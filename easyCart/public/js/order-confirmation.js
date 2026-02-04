// Order confirmation page functionality - Cancel order modal

/**
 * Open the cancel order confirmation modal
 */
function openCancelModal() {
    document.getElementById('cancelModal').style.display = 'block';
}

/**
 * Close the cancel order confirmation modal
 */
function closeCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
}

/**
 * Close modal when clicking outside of modal content
 */
document.getElementById('cancelModal')?.addEventListener('click', function(event) {
    if (event.target === this) {
        closeCancelModal();
    }
});

/**
 * Close modal when pressing Escape key
 */
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeCancelModal();
    }
});
