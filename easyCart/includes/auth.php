<?php
/**
 * Authentication Management (Database Integrated)
 * Handles user login, signup, and session management via PostgreSQL
 */

require_once 'db.php';

if (!isset($pdo)) {
    $pdo = getDBConnection();
}

// Remove JSON file logic as we are now fully DB-driven

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

/**
 * Get current logged-in user
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'name' => $_SESSION['user_name'] ?? 'User'
        ];
    }
    return null;
}

/**
 * Register new user
 */
function registerUser($email, $password, $name, $confirmPassword) {
    global $pdo;

    $errors = [];
    
    // Validate input
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($name)) {
        $errors[] = 'Full name is required';
    }
    
    // Check if email already exists in DB
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already registered';
        }
    } catch (PDOException $e) {
        $errors[] = 'Database error: ' . $e->getMessage();
    }
    
    if (count($errors) > 0) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Save guest cart items before clearing session
    $guestCart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $guestWishlist = isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : [];
    
    // Insert new user
    try {
        $stmt = $pdo->prepare("INSERT INTO users (email, password, name, created_at) VALUES (:email, :password, :name, NOW())");
        // Store plaintext password for compatibility with migrated data, or use password_hash() if preferred for new users
        // For consistency with setup_database.php and existing login, we stick to plaintext (or update login to check both)
        // Let's stick to what the user had: plaintext for this phase.
        $stmt->execute([
            ':email' => $email, 
            ':password' => $password, 
            ':name' => $name
        ]);
        
        // Get limits ID
        $newUserId = $pdo->lastInsertId(); // Should work for SERIAL with PDO Postgres if sequence is right, or query fetch
        
        // If lastInsertId fails (sometimes on Pg), query it
        if (!$newUserId) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $newUserId = $stmt->fetchColumn();
        }
        
        // Create session
        $_SESSION['user_id'] = $newUserId; // INTEGER ID
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
        
        // Load cart and wishlist (file based, using Email as identifier)
        require_once __DIR__ . '/data.php';
        $_SESSION['cart'] = loadUserCart($email);
        $_SESSION['wishlist'] = loadUserWishlist($email);
        
        // Merge guest cart items
        if (!empty($guestCart)) {
            foreach ($guestCart as $productId => $guestItem) {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['quantity'] += $guestItem['quantity'];
                } else {
                    $_SESSION['cart'][$productId] = $guestItem;
                }
            }
            saveUserCart($email, $_SESSION['cart']);
        }
        
        // Merge guest wishlist items
        if (!empty($guestWishlist)) {
            foreach ($guestWishlist as $productId => $guestWishItem) {
                if (!isset($_SESSION['wishlist'][$productId])) {
                    $_SESSION['wishlist'][$productId] = $guestWishItem;
                }
            }
            saveUserWishlist($email, $_SESSION['wishlist']);
        }
        
        return ['success' => true, 'message' => 'Account created successfully!'];

    } catch (PDOException $e) {
        return ['success' => false, 'errors' => ['Registration failed: ' . $e->getMessage()]];
    }
}

/**
 * Login user
 */
function loginUser($email, $password) {
    global $pdo;

    $errors = [];
    
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($password)) $errors[] = 'Password is required';
    
    if (count($errors) > 0) return ['success' => false, 'errors' => $errors];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'errors' => ['Email not found']];
        }
        
        // Verify Password (Plaintext check for now as per migration)
        if ($password !== $user['password']) {
            return ['success' => false, 'errors' => ['Invalid password']];
        }
        
        // Valid Login
        // Save guest cart items
        $guestCart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $guestWishlist = isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : [];
        
        // Set Session
        $_SESSION['user_id'] = $user['id']; // INTEGER ID
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
        
        // Load Cart/Wishlist
        require_once __DIR__ . '/data.php';
        $_SESSION['cart'] = loadUserCart($email);
        $_SESSION['wishlist'] = loadUserWishlist($email);
        
        // Merge guest items
        if (!empty($guestCart)) {
            foreach ($guestCart as $productId => $guestItem) {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['quantity'] += $guestItem['quantity'];
                } else {
                    $_SESSION['cart'][$productId] = $guestItem;
                }
            }
            saveUserCart($email, $_SESSION['cart']);
        }
        
        if (!empty($guestWishlist)) {
            foreach ($guestWishlist as $productId => $guestWishItem) {
                if (!isset($_SESSION['wishlist'][$productId])) {
                    $_SESSION['wishlist'][$productId] = $guestWishItem;
                }
            }
            saveUserWishlist($email, $_SESSION['wishlist']);
        }
        
        return ['success' => true, 'message' => 'Logged in successfully!'];
        
    } catch (PDOException $e) {
        return ['success' => false, 'errors' => ['Login error: ' . $e->getMessage()]];
    }
}

/**
 * Logout user
 */
function logoutUser() {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    unset($_SESSION['login_time']);
    session_destroy();
    return true;
}

/**
 * Check if page requires login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: signin.php?redirect=1');
        exit;
    }
}
?>
