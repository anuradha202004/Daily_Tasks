<?php
/**
 * Authentication Management (Database Integrated)
 * Handles user login, signup, and session management via PostgreSQL
 */

require_once 'db.php';

if (!isset($pdo)) {
    $pdo = getDBConnection();
}

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
            'name' => $_SESSION['user_name'] ?? 'User',
            'role' => $_SESSION['user_role'] ?? 'customer'
        ];
    }
    return null;
}

/**
 * Verify if the logged-in user still exists in DB
 * Used for auto-logout if user is deleted
 */
function checkUserExists() {
    if (!isLoggedIn()) return true;
    
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT 1 FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        if (!$stmt->fetch()) {
            logoutUser();
            return false;
        }
        return true;
    } catch (PDOException $e) {
        return true; // Don't logout on DB error
    }
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
    
    // Insert new user with hashed password
    try {
        // Hash password using bcrypt
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $pdo->prepare("INSERT INTO users (email, password, name, created_at) VALUES (:email, :password, :name, NOW()) RETURNING id");
        $stmt->execute([
            ':email' => $email, 
            ':password' => $hashedPassword, 
            ':name' => $name
        ]);
        
        $newUserId = $stmt->fetchColumn();
        
        // Create session
        $_SESSION['user_id'] = $newUserId;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = 'customer'; // Default role
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
        
        // Load cart and wishlist
        require_once __DIR__ . '/data.php';
        $_SESSION['cart'] = loadUserCart($newUserId);
        $_SESSION['wishlist'] = loadUserWishlist($newUserId);
        
        // Merge guest cart items
        if (!empty($guestCart)) {
            foreach ($guestCart as $productId => $guestItem) {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['quantity'] += $guestItem['quantity'];
                } else {
                    $_SESSION['cart'][$productId] = $guestItem;
                }
            }
            saveUserCart($newUserId, $_SESSION['cart']);
        }
        
        // Merge guest wishlist items
        if (!empty($guestWishlist)) {
            foreach ($guestWishlist as $productId) {
                if (!in_array($productId, $_SESSION['wishlist'])) {
                    $_SESSION['wishlist'][] = $productId;
                }
            }
            saveUserWishlist($newUserId, $_SESSION['wishlist']);
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
            return ['success' => false, 'errors' => ['invalid email id']];
        }
        
        // Verify Password using password_verify()
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'errors' => ['Invalid password']];
        }
        
        // Valid Login
        $guestCart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $guestWishlist = isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : [];
        
        // Set Session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'] ?? 'customer'; // Use existing role or default
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
        
        // Load Cart/Wishlist
        require_once __DIR__ . '/data.php';
        $_SESSION['cart'] = loadUserCart($user['id']);
        $_SESSION['wishlist'] = loadUserWishlist($user['id']);
        
        // Merge guest items
        if (!empty($guestCart)) {
            foreach ($guestCart as $productId => $guestItem) {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['quantity'] += $guestItem['quantity'];
                } else {
                    $_SESSION['cart'][$productId] = $guestItem;
                }
            }
            saveUserCart($user['id'], $_SESSION['cart']);
            
            // CRITICAL: Clear guest cart from database so it doesn't reappear on logout
            // This removes rows associated with the guest_session_id cookie
            saveUserCart(null, []);
        }
        
        if (!empty($guestWishlist)) {
            foreach ($guestWishlist as $productId) {
                if (!in_array($productId, $_SESSION['wishlist'])) {
                    $_SESSION['wishlist'][] = $productId;
                }
            }
            saveUserWishlist($user['id'], $_SESSION['wishlist']);
            
            // Clear guest wishlist session variable just in case

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
    unset($_SESSION['user_role']);
    unset($_SESSION['login_time']);
    
    // Clear guest session cookie to ensure fresh start as guest after logout
    if (isset($_COOKIE['guest_session_id'])) {
        setcookie('guest_session_id', '', time() - 3600, "/");
        unset($_COOKIE['guest_session_id']);
    }
    
    session_destroy();
    return true;
}

/**
 * Check if page requires login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: signin?redirect=1');
        exit;
    }
}

/**
 * Check if user is admin
 */
function requireAdmin() {
    requireLogin(); // Ensure logged in first
    
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        // Redirect to homepage or error page if not admin
        $_SESSION['error_message'] = "Access Denied: You do not have permission to view this page.";
        // Determine redirect path relative to current script
        // Assuming this is used in admin/something.php which is 1 level deep
        header('Location: ../index'); 
        exit;
    }
}
?>
