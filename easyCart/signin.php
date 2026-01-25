<?php
session_start();

require_once 'includes/auth.php';

$pageTitle = 'Sign In';

// If already logged in, redirect to home
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = false;

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    $result = loginUser($email, $password);
    
    if ($result['success']) {
        // Redirect to cart or intended page
        if (isset($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
        } else {
            header('Location: products.php');
        }
        exit;
    } else {
        $errors = $result['errors'];
    }
}
?>
<?php include 'includes/header.php'; ?>
    <script src="js/validation.js"></script>

    <!-- Sign In Page -->
    <section class="container" style="padding: 60px 20px;">
        <div style="max-width: 450px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 40px;">
                <div style="font-size: 48px; margin-bottom: 15px;">🔐</div>
                <h1 style="color: #2563eb; margin-bottom: 10px;">Welcome Back</h1>
                <p style="color: #666;">Sign in to your EasyCart account</p>
            </div>

            <!-- Display Errors -->
            <?php if (count($errors) > 0): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #f5c6cb;">
                    <?php foreach ($errors as $error): ?>
                        <p style="margin: 5px 0;">• <?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Sign In Form -->
            <form method="POST" action="" id="loginForm" onsubmit="return validateLoginForm()">
                <input type="hidden" name="action" value="login">

                <!-- Email Field -->
                <div style="margin-bottom: 20px;">
                    <label for="email" style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; transition: border-color 0.3s;">
                    <small id="emailError" style="color: #dc2626; display: none; margin-top: 5px; display: block;"></small>
                </div>

                <!-- Password Field -->
                <div style="margin-bottom: 25px;">
                    <label for="password" style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required 
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; transition: border-color 0.3s;">
                    <small id="passwordError" style="color: #dc2626; display: none; margin-top: 5px; display: block;"></small>
                    <small style="color: #999; display: block; margin-top: 5px;">
                        Demo: password123
                    </small>
                </div>

                <!-- Remember Me -->
                <div style="display: flex; align-items: center; margin-bottom: 25px;">
                    <input type="checkbox" id="remember" name="remember" style="margin-right: 8px; cursor: pointer;">
                    <label for="remember" style="margin: 0; cursor: pointer; color: #666;">Remember me</label>
                </div>

                <!-- Sign In Button -->
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 16px; cursor: pointer;">
                    Sign In
                </button>
            </form>

            <!-- Divider -->
            <div style="text-align: center; margin: 30px 0; color: #999;">
                <span style="background: white; padding: 0 10px;">or</span>
                <div style="border-top: 1px solid #ddd; position: relative; top: -15px;"></div>
            </div>

            <!-- Demo Info -->
            <div style="background: #e8f4f8; padding: 15px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #2563eb;">
                <p style="margin: 0 0 10px 0; font-weight: 500; color: #0c5460;">Demo Account Available</p>
                <p style="margin: 0; font-size: 14px; color: #0c5460;">
                    <strong>Email:</strong> demo@example.com<br>
                    <strong>Password:</strong> password123
                </p>
            </div>

            <!-- Sign Up Link -->
            <div style="text-align: center; padding: 20px 0; border-top: 1px solid #eee;">
                <p style="margin: 0; color: #666;">
                    Don't have an account?
                    <a href="signup.php" style="color: #2563eb; text-decoration: none; font-weight: 500;">Create one now</a>
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
