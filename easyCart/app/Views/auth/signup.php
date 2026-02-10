<?php include TEMPLATES_PATH . '/header.php'; ?>
<script src="<?php echo URL_ROOT; ?>/js/validation.js"></script>

<section class="container auth-container-center">
    <div class="modal-content auth-card-static">
        <h1 class="modal-title">Create Account</h1>
        
        <?php if (!empty($data['errors'])): ?>
            <div class="auth-alert-error-red">
                <?php foreach ($data['errors'] as $error): ?>
                    <p>• <?php echo htmlspecialchars($error); ?></p>
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
                <p class="auth-hint-text-small">At least 6 characters</p>
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
            <p>Already have an account? <a href="signin">Sign In</a></p>
            <p style="margin-top: 10px;"><a href="products" class="auth-guest-link-muted">Continue as Guest</a></p>
        </div>
    </div>
</section>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
