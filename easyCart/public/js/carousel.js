// Carousel/Slideshow functionality for home page banner

let currentAdIndex = 0;
let autoplayInterval;

/**
 * Show advertisement at specific index
 */
function showAd(index) {
    const slides = document.querySelectorAll('.ad-slide');
    const dots = document.querySelectorAll('.dot');
    
    if (index >= slides.length) {
        currentAdIndex = 0;
    } else if (index < 0) {
        currentAdIndex = slides.length - 1;
    } else {
        currentAdIndex = index;
    }
    
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    if (slides[currentAdIndex]) slides[currentAdIndex].classList.add('active');
    if (dots[currentAdIndex]) dots[currentAdIndex].classList.add('active');
}

/**
 * Navigate to ad by clicking dot
 */
function goToAd(index) {
    resetAutoplay();
    showAd(index);
}

/**
 * Show next advertisement
 */
function nextAd() {
    resetAutoplay();
    showAd(currentAdIndex + 1);
}

/**
 * Show previous advertisement
 */
function prevAd() {
    resetAutoplay();
    showAd(currentAdIndex - 1);
}

/**
 * Reset autoplay timer
 */
function resetAutoplay() {
    if (autoplayInterval) {
        clearInterval(autoplayInterval);
    }
    startAutoplay();
}

/**
 * Start automatic carousel rotation
 */
function startAutoplay() {
    autoplayInterval = setInterval(function() {
        showAd(currentAdIndex + 1);
    }, 5000); // Change slide every 5 seconds
}

/**
 * Initialize carousel on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    showAd(0);
    startAutoplay();
});
