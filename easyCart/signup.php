<?php
session_start();

require_once 'auth.php';

$pageTitle = 'Sign Up';

// If already logged in, redirect to home
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = false;

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'signup') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    $result = registerUser($email, $password, $name, $confirmPassword);
    
    if ($result['success']) {
        // Redirect to products page after successful signup
        header('Location: products.php');
        exit;
    } else {
        $errors = $result['errors'];
    }
}
?>
<?php include 'header.php'; ?>

    <!-- Sign Up Page -->
    <section class="container" style="padding: 60px 20px;">
        <div style="max-width: 450px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 40px;">
                <div style="font-size: 48px; margin-bottom: 15px;">🎉</div>
                <h1 style="color: #2563eb; margin-bottom: 10px;">Create Account</h1>
                <p style="color: #666;">Join EasyCart and start shopping today</p>
            </div>

            <!-- Display Errors -->
            <?php if (count($errors) > 0): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #f5c6cb;">
                    <?php foreach ($errors as $error): ?>
                        <p style="margin: 5px 0;">• <?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Sign Up Form -->
            <form method="POST" action="" id="signupForm" onsubmit="return validateSignupForm()">
                <input type="hidden" name="action" value="signup">

                <!-- Name Field -->
                <div style="margin-bottom: 20px;">
                    <label for="name" style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" required 
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <small id="nameError" style="color: #dc2626; display: none; margin-top: 5px; display: block;"></small>
                </div>

                <!-- Email Field -->
                <div style="margin-bottom: 20px;">
                    <label for="email" style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <small id="emailError" style="color: #dc2626; display: none; margin-top: 5px; display: block;"></small>
                    <small style="color: #999; display: block; margin-top: 5px;">
                        We'll never share your email
                    </small>
                </div>

                <!-- Password Field -->
                <div style="margin-bottom: 20px;">
                    <label for="password" style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required 
                           onchange="validatePasswordStrength()"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <small id="passwordError" style="color: #dc2626; display: none; margin-top: 5px; display: block;"></small>
                    <div id="passwordStrength" style="margin-top: 8px; display: none;">
                        <span style="font-size: 12px; color: #666;">Password strength: </span>
                        <span id="strengthBar" style="display: inline-block; width: 60px; height: 4px; background: #e5e7eb; border-radius: 2px; position: relative; margin-left: 5px;">
                            <span id="strengthFill" style="display: block; height: 100%; width: 0%; border-radius: 2px; transition: width 0.3s, background-color 0.3s;"></span>
                        </span>
                    </div>
                    <small style="color: #999; display: block; margin-top: 5px;">
                        At least 6 characters
                    </small>
                </div>

                <!-- Confirm Password Field -->
                <div style="margin-bottom: 25px;">
                    <label for="confirm_password" style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required 
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <small id="confirmError" style="color: #dc2626; display: none; margin-top: 5px; display: block;"></small>
                </div>

                <!-- Terms & Conditions -->
                <div style="display: flex; align-items: flex-start; margin-bottom: 25px;">
                    <input type="checkbox" id="agree" name="agree" required style="margin-right: 8px; margin-top: 4px; cursor: pointer;">
                    <label for="agree" style="margin: 0; cursor: pointer; color: #666; font-size: 14px;">
                        I agree to the <a href="#" style="color: #2563eb; text-decoration: none;">Terms & Conditions</a> and <a href="#" style="color: #2563eb; text-decoration: none;">Privacy Policy</a>
                    </label>
                </div>

                <!-- Sign Up Button -->
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 16px; cursor: pointer;">
                    Create Account
                </button>
            </form>

            <script>
                function validateSignupForm() {
                    let isValid = true;
                    
                    // Clear previous errors
                    document.getElementById('nameError').style.display = 'none';
                    document.getElementById('emailError').style.display = 'none';
                    document.getElementById('passwordError').style.display = 'none';
                    document.getElementById('confirmError').style.display = 'none';
                    
                    // Name validation
                    const name = document.getElementById('name').value.trim();
                    if (!name) {
                        showError('nameError', 'Full name is required');
                        isValid = false;
                    } else if (name.length < 2) {
                        showError('nameError', 'Name must be at least 2 characters');
                        isValid = false;
                    }
                    
                    // Email validation
                    const email = document.getElementById('email').value.trim();
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    
                    if (!email) {
                        showError('emailError', 'Email address is required');
                        isValid = false;
                    } else if (!emailRegex.test(email)) {
                        showError('emailError', 'Please enter a valid email address');
                        isValid = false;
                    }
                    
                    // Password validation
                    const password = document.getElementById('password').value;
                    
                    if (!password) {
                        showError('passwordError', 'Password is required');
                        isValid = false;
                    } else if (password.length < 6) {
                        showError('passwordError', 'Password must be at least 6 characters');
                        isValid = false;
                    }
                    
                    // Confirm password validation
                    const confirmPassword = document.getElementById('confirm_password').value;
                    
                    if (!confirmPassword) {
                        showError('confirmError', 'Please confirm your password');
                        isValid = false;
                    } else if (password !== confirmPassword) {
                        showError('confirmError', 'Passwords do not match');
                        isValid = false;
                    }
                    
                    return isValid;
                }
                
                function validatePasswordStrength() {
                    const password = document.getElementById('password').value;
                    const strengthFill = document.getElementById('strengthFill');
                    const passwordStrength = document.getElementById('passwordStrength');
                    
                    if (password.length === 0) {
                        passwordStrength.style.display = 'none';
                        return;
                    }
                    
                    passwordStrength.style.display = 'inline-block';
                    
                    let strength = 0;
                    if (password.length >= 6) strength += 25;
                    if (password.length >= 10) strength += 25;
                    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
                    if (/\d/.test(password)) strength += 15;
                    if (/[^a-zA-Z\d]/.test(password)) strength += 10;
                    
                    strength = Math.min(strength, 100);
                    strengthFill.style.width = strength + '%';
                    
                    if (strength < 40) {
                        strengthFill.style.backgroundColor = '#dc2626';
                    } else if (strength < 70) {
                        strengthFill.style.backgroundColor = '#f59e0b';
                    } else {
                        strengthFill.style.backgroundColor = '#10b981';
                    }
                }
                
                function showError(elementId, message) {
                    const errorElement = document.getElementById(elementId);
                    errorElement.textContent = message;
                    errorElement.style.display = 'block';
                }
            </script>

            <!-- Divider -->
            <div style="text-align: center; margin: 30px 0; color: #999;">
                <span style="background: white; padding: 0 10px;">or</span>
                <div style="border-top: 1px solid #ddd; position: relative; top: -15px;"></div>
            </div>

            <!-- Benefits -->
            <div style="background: #f0f4f8; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
                <h4 style="margin-top: 0; margin-bottom: 15px; color: #2563eb;">Why Join EasyCart?</h4>
                <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #666;">
                    <li style="margin-bottom: 8px;">✓ Easy checkout process</li>
                    <li style="margin-bottom: 8px;">✓ Track your orders in real-time</li>
                    <li style="margin-bottom: 8px;">✓ Access to exclusive deals</li>
                    <li style="margin-bottom: 8px;">✓ Save your favorite products</li>
                    <li>✓ Fast and secure payments</li>
                </ul>
            </div>

            <!-- Sign In Link -->
            <div style="text-align: center; padding: 20px 0; border-top: 1px solid #eee;">
                <p style="margin: 0; color: #666;">
                    Already have an account?
                    <a href="signin.php" style="color: #2563eb; text-decoration: none; font-weight: 500;">Sign in here</a>
                </p>
            </div>

            <!-- Continue as Guest -->
            <div style="text-align: center; padding: 15px 0;">
                <a href="products.php" style="color: #666; text-decoration: none; font-size: 14px;">
                    Continue browsing as guest →
                </a>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>
