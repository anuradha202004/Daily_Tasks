// Toast Notification System - Modern Design

/**
 * Show a toast notification
 * @param {string} message - Message to display
 * @param {string} type - Type of toast: 'success', 'error', 'info', 'warning'
 * @param {number} duration - Duration in milliseconds (default: 3000)
 */
function showToast(message, type = 'success', duration = 3000) {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;

    // Get icon based on type
    const icons = {
        success: '✓',
        error: '✕',
        warning: '⚠',
        info: 'ℹ'
    };

    const icon = icons[type] || icons.success;

    // Create toast content
    toast.innerHTML = `
        <div class="toast-icon">${icon}</div>
        <div class="toast-content">
            <p class="toast-message">${message}</p>
        </div>
        <button class="toast-close" onclick="closeToast(this)">×</button>
        <div class="toast-progress"></div>
    `;

    // Add to container
    toastContainer.appendChild(toast);

    // Trigger animation
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    // Start progress bar animation
    const progressBar = toast.querySelector('.toast-progress');
    setTimeout(() => {
        progressBar.style.transition = `width ${duration}ms linear`;
        progressBar.style.width = '0%';
    }, 10);

    // Auto remove after duration
    const timeoutId = setTimeout(() => {
        removeToast(toast);
    }, duration);

    // Store timeout ID for manual close
    toast.dataset.timeoutId = timeoutId;
}

/**
 * Close a toast notification
 * @param {HTMLElement} button - Close button element
 */
function closeToast(button) {
    const toast = button.closest('.toast');
    if (toast && toast.dataset.timeoutId) {
        clearTimeout(parseInt(toast.dataset.timeoutId));
        removeToast(toast);
    }
}

/**
 * Remove toast with animation
 * @param {HTMLElement} toast - Toast element to remove
 */
function removeToast(toast) {
    toast.classList.add('hide');
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

/**
 * Show success toast for add to cart
 * @param {string} productName - Name of the product added
 */
function showAddToCartSuccess(productName) {
    showToast(`🛒 ${productName} added to cart!`, 'success', 3500);
}

/**
 * Show error toast
 * @param {string} errorMessage - Error message to display
 */
function showErrorToast(errorMessage) {
    showToast(errorMessage, 'error', 4000);
}

/**
 * Show info toast
 * @param {string} infoMessage - Info message to display
 */
function showInfoToast(infoMessage) {
    showToast(infoMessage, 'info', 3000);
}

/**
 * Show warning toast
 * @param {string} warningMessage - Warning message to display
 */
function showWarningToast(warningMessage) {
    showToast(warningMessage, 'warning', 3500);
}
