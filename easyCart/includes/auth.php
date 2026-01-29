<?php
/**
 * Authentication Management for EasyCart
 * Handles user login, signup, and session management
 */

// Path to users data file
$usersDataFile = __DIR__ . '/../data/users.json';

// Ensure data directory exists
if (!is_dir(__DIR__ . '/../data')) {
    mkdir(__DIR__ . '/../data', 0755, true);
}

// Load registered users from file
function loadRegisteredUsers() {
    global $usersDataFile;
    
    $defaultUsers = [
        'demo@example.com' => [
            'email' => 'demo@example.com',
            'password' => 'password123',
            'name' => 'Demo User',
            'created' => '2026-01-01'
        ]
    ];
    
    // If file doesn't exist or is empty, use default users
    if (!file_exists($usersDataFile) || filesize($usersDataFile) == 0) {
        return $defaultUsers;
    }
    
    // Load users from file
    $fileContent = file_get_contents($usersDataFile);
    $users = json_decode($fileContent, true);
    
    // Merge with default users (to ensure demo user always exists)
    if (is_array($users)) {
        return array_merge($defaultUsers, $users);
    }
    
    return $defaultUsers;
}

// Save registered users to file
function saveRegisteredUsers($users) {
    global $usersDataFile;
    
    // Write users to file
    $jsonData = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents($usersDataFile, $jsonData);
}

// Load users at the start
$registeredUsers = loadRegisteredUsers();

// ADD THIS LINE BELOW:
if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION['all_users_from_json'] = $registeredUsers;
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
            'name' => $_SESSION['user_name'] ?? 'User'
        ];
    }
    return null;
}

/**
 * Register new user
 */
function registerUser($email, $password, $name, $confirmPassword) {
    global $registeredUsers;
    
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
    
    // Check if email already exists
    if (isset($registeredUsers[$email])) {
        $errors[] = 'Email already registered';
    }
    
    if (count($errors) > 0) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Save guest cart items before clearing session
    $guestCart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $guestWishlist = isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : [];
    
    // Register user
    $registeredUsers[$email] = [
        'email' => $email,
        'password' => $password, // In production: password_hash($password, PASSWORD_BCRYPT)
        'name' => $name,
        'created' => date('Y-m-d H:i:s')
    ];
    
    // Save users to file
    saveRegisteredUsers($registeredUsers);
    
    // Create session
    $_SESSION['user_id'] = md5($email);
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $name;
    $_SESSION['login_time'] = date('Y-m-d H:i:s');
    
    // Load cart and wishlist from files for this user
    require_once __DIR__ . '/data.php';
    $_SESSION['cart'] = loadUserCart($email);
    $_SESSION['wishlist'] = loadUserWishlist($email);
    
    // Merge guest cart items into user cart
    if (!empty($guestCart)) {
        foreach ($guestCart as $productId => $guestItem) {
            if (isset($_SESSION['cart'][$productId])) {
                // If product already exists in user cart, add quantities
                $_SESSION['cart'][$productId]['quantity'] += $guestItem['quantity'];
            } else {
                // If product doesn't exist, add it from guest cart
                $_SESSION['cart'][$productId] = $guestItem;
            }
        }
        // Save merged cart to file
        saveUserCart($email, $_SESSION['cart']);
    }
    
    // Merge guest wishlist items into user wishlist
    if (!empty($guestWishlist)) {
        foreach ($guestWishlist as $productId => $guestWishItem) {
            if (!isset($_SESSION['wishlist'][$productId])) {
                $_SESSION['wishlist'][$productId] = $guestWishItem;
            }
        }
        // Save merged wishlist to file
        saveUserWishlist($email, $_SESSION['wishlist']);
    }
    
    return ['success' => true, 'message' => 'Account created successfully!'];
}

/**
 * Login user
 */
function loginUser($email, $password) {
    global $registeredUsers;
    
    $errors = [];
    
    // Validate input
    if (empty($email)) {
        $errors[] = 'Email is required';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    if (count($errors) > 0) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Check credentials
    if (!isset($registeredUsers[$email])) {
        return ['success' => false, 'errors' => ['Email not found']];
    }
    
    $user = $registeredUsers[$email];
    
    // In production: password_verify($password, $user['password'])
    if ($password !== $user['password']) {
        return ['success' => false, 'errors' => ['Invalid password']];
    }
    
    // Save guest cart items before clearing session
    $guestCart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $guestWishlist = isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : [];
    
    // Create session
    $_SESSION['user_id'] = md5($email);
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['login_time'] = date('Y-m-d H:i:s');
    
    // Load cart and wishlist from files for this user
    require_once __DIR__ . '/data.php';
    $_SESSION['cart'] = loadUserCart($email);
    $_SESSION['wishlist'] = loadUserWishlist($email);
    
    // Merge guest cart items into user cart
    if (!empty($guestCart)) {
        foreach ($guestCart as $productId => $guestItem) {
            if (isset($_SESSION['cart'][$productId])) {
                // If product already exists in user cart, add quantities
                $_SESSION['cart'][$productId]['quantity'] += $guestItem['quantity'];
            } else {
                // If product doesn't exist, add it from guest cart
                $_SESSION['cart'][$productId] = $guestItem;
            }
        }
        // Save merged cart to file
        saveUserCart($email, $_SESSION['cart']);
    }
    
    // Merge guest wishlist items into user wishlist
    if (!empty($guestWishlist)) {
        foreach ($guestWishlist as $productId => $guestWishItem) {
            if (!isset($_SESSION['wishlist'][$productId])) {
                $_SESSION['wishlist'][$productId] = $guestWishItem;
            }
        }
        // Save merged wishlist to file
        saveUserWishlist($email, $_SESSION['wishlist']);
    }
    
    return ['success' => true, 'message' => 'Logged in successfully!'];
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
