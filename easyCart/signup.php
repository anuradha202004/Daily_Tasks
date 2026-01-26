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

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'signup') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    $result = registerUser($email, $password, $name, $confirmPassword);
    
    if ($result['success']) {
        header('Location: products.php');
        exit;
    } else {
        $errors = $result['errors'];
    }
}
?>
<?php include 'includes/header.php'; ?>
    <script src="js/validation.js"></script>

    <section class="container" style="padding: 60px 0; display: flex; justify-content: center;">
        <div class="modal-content" style="display: block; position: static; transform: none; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 100%; max-width: 450px;">
            <h1 class="modal-title">Create Account</h1>
            
            <?php if (count($errors) > 0): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 14px;">
                    <?php foreach ($errors as $error): ?>
                        <p style="margin: 5px 0;">• <?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" onsubmit="return validateSignupForm()">
                <input type="hidden" name="action" value="signup">
                
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required placeholder="John Doe" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="you@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                    <p style="font-size: 12px; color: #666; margin-top: 5px;">At least 6 characters</p>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••">
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="agree" name="agree" required>
                    <label for="agree">I agree to the Terms & Conditions</label>
                </div>

                <button type="submit" class="form-submit">Create Account</button>
            </form>

            <div class="form-footer">
                <p>Already have an account? <a href="signin.php">Sign In</a></p>
                <p style="margin-top: 10px;"><a href="products.php" style="color: #999; font-weight: normal;">Continue as Guest</a></p>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
