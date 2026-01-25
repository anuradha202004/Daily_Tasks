<?php
session_start();

require_once 'includes/auth.php';

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
<?php include 'includes/header.php'; ?>
    <script src="js/validation.js"></script>

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

<?php include 'includes/footer.php'; ?>
