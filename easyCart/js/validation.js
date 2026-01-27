// Form validation functions for Sign In, Sign Up, and Checkout pages

/**
 * Validate sign-in form
 */
function validateLoginForm() {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    
    // Clear previous errors
    document.getElementById('emailError').textContent = '';
    document.getElementById('passwordError').textContent = '';
    
    let isValid = true;
    
    // Validate email
    if (!email) {
        showError('emailError', 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showError('emailError', 'Please enter a valid email address');
        isValid = false;
    }
    
    // Validate password
    if (!password) {
        showError('passwordError', 'Password is required');
        isValid = false;
    } else if (password.length < 6) {
        showError('passwordError', 'Password must be at least 6 characters');
        isValid = false;
    }
    
    return isValid;
}

/**
 * Validate sign-up form
 */
function validateSignupForm() {
    const fullName = document.getElementById('fullName').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Clear previous errors
    document.getElementById('nameError').textContent = '';
    document.getElementById('emailError').textContent = '';
    document.getElementById('passwordError').textContent = '';
    document.getElementById('confirmError').textContent = '';
    
    let isValid = true;
    
    // Validate name
    if (!fullName) {
        showError('nameError', 'Full name is required');
        isValid = false;
    } else if (fullName.length < 3) {
        showError('nameError', 'Name must be at least 3 characters');
        isValid = false;
    }
    
    // Validate email
    if (!email) {
        showError('emailError', 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showError('emailError', 'Please enter a valid email address');
        isValid = false;
    }
    
    // Validate password
    if (!password) {
        showError('passwordError', 'Password is required');
        isValid = false;
    } else if (password.length < 6) {
        showError('passwordError', 'Password must be at least 6 characters');
        isValid = false;
    }
    
    // Validate confirm password
    if (!confirmPassword) {
        showError('confirmError', 'Please confirm your password');
        isValid = false;
    } else if (password !== confirmPassword) {
        showError('confirmError', 'Passwords do not match');
        isValid = false;
    }
    return isValid;
}
 
/**
 * Validate password strength
 */
function validatePasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthMeter = document.getElementById('strengthMeter');
    const strengthText = document.getElementById('strengthText');
    
    if (!strengthMeter || !strengthText) return;
    
    let strength = 0;
    
    // Check length
    if (password.length >= 6) strength++;
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    
    // Check for variety
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    // Update meter display
    const percentage = (strength / 7) * 100;
    strengthMeter.style.width = percentage + '%';
    
    if (strength < 2) {
        strengthMeter.style.background = '#d32f2f';
        strengthText.textContent = 'Weak';
        strengthText.style.color = '#d32f2f';
    } else if (strength < 4) {
        strengthMeter.style.background = '#ff9800';
        strengthText.textContent = 'Fair';
        strengthText.style.color = '#ff9800';
    } else if (strength < 6) {
        strengthMeter.style.background = '#fbc02d';
        strengthText.textContent = 'Good';
        strengthText.style.color = '#fbc02d';
    } else {
        strengthMeter.style.background = '#388e3c';
        strengthText.textContent = 'Strong';
        strengthText.style.color = '#388e3c';
    }
}

/**
 * Validate checkout form
 */
function validateCheckoutForm() {
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();
    const address = document.getElementById('address').value.trim();
    const city = document.getElementById('city').value.trim();
    const state = document.getElementById('state').value.trim();
    const zip = document.getElementById('zip').value.trim();
    const cardNumber = document.getElementById('cardNumber').value.trim();
    const shippingSelected = document.querySelector('input[name="shipping"]:checked');
    
    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    
    let isValid = true;
    
    if (!firstName) {
        document.getElementById('firstNameError').textContent = 'First name is required';
        isValid = false;
    }
    
    if (!lastName) {
        document.getElementById('lastNameError').textContent = 'Last name is required';
        isValid = false;
    }
    
    if (!email || !isValidEmail(email)) {
        document.getElementById('emailError').textContent = 'Valid email is required';
        isValid = false;
    }
    
    if (!address) {
        document.getElementById('addressError').textContent = 'Address is required';
        isValid = false;
    }
    
    if (!city) {
        document.getElementById('cityError').textContent = 'City is required';
        isValid = false;
    }
    
    if (!state) {
        document.getElementById('stateError').textContent = 'State is required';
        isValid = false;
    }
    
    if (!zip || !/^\d{5}(-\d{4})?$/.test(zip)) {
        document.getElementById('zipError').textContent = 'Valid ZIP code is required';
        isValid = false;
    }
    
    if (!cardNumber || !/^\d{13,19}$/.test(cardNumber.replace(/\s/g, ''))) {
        document.getElementById('cardError').textContent = 'Valid card number is required';
        isValid = false;
    }
    
    if (!shippingSelected) {
        document.getElementById('shippingError').textContent = 'Please select a shipping option';
        isValid = false;
    }
    
    return isValid;
}

/**
 * Show error message for a form field
 */
function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = message;
    }
}

/**
 * Validate email format using regex
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}
