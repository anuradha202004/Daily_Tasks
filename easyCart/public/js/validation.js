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
    const phone = document.getElementById('phone').value.trim();
    const address = document.getElementById('address').value.trim();
    const city = document.getElementById('city').value.trim();
    const state = document.getElementById('state').value.trim();
    const zip = document.getElementById('zip').value.trim();
    const cardNumber = document.getElementById('cardNumber').value.trim();
    const expiry = document.getElementById('expiry').value.trim();
    const cvv = document.getElementById('cvv').value.trim();
    const shippingSelected = document.querySelector('input[name="shipping_method"]:checked');

    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

    let isValid = true;

    // Personal Info
    if (!firstName) {
        showError('firstNameError', 'First name is required');
        isValid = false;
    }

    if (!lastName) {
        showError('lastNameError', 'Last name is required');
        isValid = false;
    }

    // Email Validation: Pattern Check
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!email) {
        showError('emailError', 'Email is required');
        isValid = false;
    } else if (!emailPattern.test(email)) {
        showError('emailError', 'Enter a valid email (e.g., user@example.com)');
        isValid = false;
    }

    // Phone Validation: Generic digits check, 10-15 digits
    const phonePattern = /^\d{10,15}$/;
    if (!phone) {
        showError('phoneError', 'Phone number is required');
        isValid = false;
    } else if (!phonePattern.test(phone.replace(/[\s\-\(\)\+]/g, ''))) {
        showError('phoneError', 'Invalid phone number (enter 10-15 digits)');
        isValid = false;
    }

    // Address
    if (!address) {
        showError('addressError', 'Address is required');
        isValid = false;
    }

    if (!city) {
        showError('cityError', 'City is required');
        isValid = false;
    }

    if (!state) {
        showError('stateError', 'State is required');
        isValid = false;
    }

    if (!zip || !/^(\d{5}(-\d{4})?|\d{6})$/.test(zip)) {
        showError('zipError', 'Valid ZIP/PIN code is required (5 or 6 digits)');
        isValid = false;
    }

    // Payment
    if (!cardNumber || !/^\d{13,19}$/.test(cardNumber.replace(/\s/g, ''))) {
        showError('cardError', 'Valid card number is required (13-19 digits)');
        isValid = false;
    }

    const expiryPattern = /^(0[1-9]|1[0-2])\/\d{2}$/;
    if (!expiry) {
        showError('expiryError', 'Expiry date is required');
        isValid = false;
    } else if (!expiryPattern.test(expiry)) {
        showError('expiryError', 'Invalid format (MM/YY)');
        isValid = false;
    }

    const cvvPattern = /^\d{3,4}$/;
    if (!cvv) {
        showError('cvvError', 'CVV is required');
        isValid = false;
    } else if (!cvvPattern.test(cvv)) {
        showError('cvvError', 'Invalid CVV (3-4 digits)');
        isValid = false;
    }

    if (!shippingSelected) {
        showError('shippingError', 'Please select a shipping option');
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
