<?php
session_start();

// Load Application Bootstrap
require_once 'app/bootstrap.php';

$pageTitle = 'Sign In';

// If already logged in, redirect to home
if (isLoggedIn()) {
    header('Location: index');
    exit;
}

// Capture redirect param if present
if (isset($_GET['redirect'])) {
    $_SESSION['redirect_after_login'] = $_GET['redirect'];
    
    // If redirecting to product-detail, capture ID too
    if ($_GET['redirect'] === 'product-detail' && isset($_GET['id'])) {
        $_SESSION['redirect_after_login'] .= '?id=' . $_GET['id'];
    }
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
        // Redirect based on role or intended page
        $user = getCurrentUser();
        if ($user && isset($user['role']) && $user['role'] === 'admin') {
             // Admin goes to dashboard
             unset($_SESSION['redirect_after_login']); // Clear any pending redirects
             header('Location: admin/dashboard');
        } elseif (isset($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
        } else {
            header('Location: products');
        }
        exit;
    } else {
        $errors = $result['errors'];
    }
}
?>
<?php include TEMPLATES_PATH . '/header.php'; ?>
    <script src="public/js/validation.js"></script>

    <!-- Sign In Page -->
    <section class="container auth-section">
        <div class="auth-wrapper">
            <div class="auth-header">
                <div class="auth-icon-lg">🔐</div>
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">
                    <?php if (isset($_GET['redirect']) || isset($_SESSION['redirect_after_login'])): ?>
                        Sign in to complete your checkout
                    <?php else: ?>
                        Sign in to your EasyCart account
                    <?php endif; ?>
                </p>
            </div>

            <!-- Display Errors -->
            <?php if (count($errors) > 0): ?>
                <div class="auth-alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p>• <?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Sign In Form -->
            <form method="POST" action="" id="loginForm" onsubmit="return validateLoginForm()">
                <input type="hidden" name="action" value="login">

                <!-- Email Field -->
                <div class="auth-form-group">
                    <label for="email" class="auth-label">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           class="auth-input">
                    <small id="emailError" class="auth-error-text"></small>
                </div>

                <!-- Password Field -->
                <div class="auth-form-group-mb25">
                    <label for="password" class="auth-label">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required 
                           class="auth-input">
                    <small id="passwordError" class="auth-error-text"></small>
                    <small class="auth-hint-text">
                        Demo: password123
                    </small>
                </div>

                <!-- Remember Me -->
                <div class="auth-remember-row">
                    <input type="checkbox" id="remember" name="remember" class="auth-checkbox">
                    <label for="remember" class="auth-remember-label">Remember me</label>
                </div>

                <!-- Sign In Button -->
                <button type="submit" class="btn btn-primary auth-btn-submit">
                    Sign In
                </button>
            </form>

            <!-- Divider -->
            <div class="auth-divider">
                <span>or</span>
                <div></div>
            </div>

            <!-- Demo Info -->
            <div class="auth-demo-box">
                <p class="auth-demo-title">Demo Account Available</p>
                <p class="auth-demo-content">
                    <strong>Email:</strong> demo@example.com<br>
                    <strong>Password:</strong> password123
                </p>
            </div>

            <!-- Sign Up Link -->
            <div class="auth-footer-links">
                <p class="auth-footer-text">
                    Don't have an account?
                    <a href="signup" class="auth-footer-link">Create one now</a>
                </p>
            </div>

            <!-- Continue as Guest -->
            <div class="auth-guest-link-box">
                <a href="products" class="auth-guest-link">
                    Continue browsing as guest →
                </a>
            </div>
        </div>
    </section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
