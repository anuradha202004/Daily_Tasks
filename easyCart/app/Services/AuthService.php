<?php

namespace Services;

use Models\User;

/**
 * Authentication Service
 * Handles user authentication logic
 */
class AuthService {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Register a new user
     * @param array $data
     * @return array
     */
    public function register($email, $password, $name, $confirmPassword) {
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
        if ($this->userModel->findByEmail($email)) {
            $errors[] = 'Email already registered';
        }
        
        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }
        
        
        // Save guest cart items
        $guestCart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $guestWishlist = isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : [];
        
        // Create user with hashed password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $newUserId = $this->userModel->createUser([
            'email' => $email,
            'password' => $hashedPassword,
            'name' => $name
        ]);
        
        // Set session
        $_SESSION['user_id'] = $newUserId;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
        
        // Merge guest data
        $cartService = new CartService();
        $cartService->mergeGuestCart($guestCart);
        
        $wishlistService = new WishlistService();
        $wishlistService->mergeGuestWishlist($guestWishlist);
        
        return ['success' => true, 'message' => 'Account created successfully!'];
    }
    
    /**
     * Login user
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login($email, $password) {
        $errors = [];
        
        if (empty($email)) $errors[] = 'Email is required';
        if (empty($password)) $errors[] = 'Password is required';
        
        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $user = $this->userModel->verifyCredentials($email, $password);
        
        if (!$user) {
            return ['success' => false, 'errors' => ['Invalid email or password']];
        }
        
        // Save guest cart items
        $guestCart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $guestWishlist = isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : [];
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
        
        // Merge guest data
        $cartService = new CartService();
        $cartService->mergeGuestCart($guestCart);
        
        $wishlistService = new WishlistService();
        $wishlistService->mergeGuestWishlist($guestWishlist);
        
        return ['success' => true, 'message' => 'Logged in successfully!'];
    }
    
    /**
     * Logout user
     * @return bool
     */
    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        unset($_SESSION['login_time']);
        session_destroy();
        return true;
    }
}
