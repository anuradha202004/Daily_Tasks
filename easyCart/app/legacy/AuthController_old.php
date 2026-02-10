<?php

class AuthController extends Controller {
    
    public function signin() {
        // If already logged in, redirect to home
        if (isLoggedIn()) {
            $this->redirect('index');
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

        // Handle login form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            
            // Use core function (refactor later to UserModel)
            $result = loginUser($email, $password);
            
            if ($result['success']) {
                $user = getCurrentUser();
                
                // Admin checks
                if ($user && isset($user['role']) && $user['role'] === 'admin') {
                     unset($_SESSION['redirect_after_login']);
                     $this->redirect('admin/dashboard');
                } elseif (isset($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    // Safety check for clean URLs
                    header('Location: ' . $redirect);
                    exit;
                } else {
                    $this->redirect('products');
                }
            } else {
                $errors = $result['errors'];
            }
        }
        
        $this->view('auth/signin', ['errors' => $errors, 'title' => 'Sign In']);
    }

    public function signup() {
        // If already logged in, redirect to home
        if (isLoggedIn()) {
            $this->redirect('index');
        }

        $errors = [];

        // Handle signup form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'signup') {
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            
            // Use core function
            $result = registerUser($email, $password, $name, $confirmPassword);
            
            if ($result['success']) {
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header('Location: ' . $redirect);
                    exit;
                } else {
                    $this->redirect('products');
                }
            } else {
                $errors = $result['errors'];
            }
        }

        $this->view('auth/signup', ['errors' => $errors, 'title' => 'Sign Up']);
    }

    public function logout() {
        logoutUser(); // Call core logout function
        $this->redirect('signin'); 
    }
}
