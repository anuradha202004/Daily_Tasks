<?php

class Controller_Auth extends Core_Controller {
    
    public function signin() {
        if (isLoggedIn()) {
            $this->redirect('');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Replaced manual logic with Model_Customer logic
            $customer = new Model_Customer();
            try {
                $user = $customer->authenticate($email, $password);
                if ($user) {
                     // Need to mimic loginUser() behavior from auth.php (Session set, Cart merge)
                     // Since loginUser() is global, we can use it OR reimplement it in Model_Auth or similar
                     // For pure MVC, this logic ideally sits in Model_Customer or Model_Session
                     
                     // Using existing global functions for now to maintain session state structure
                     // until session management is fully refactored
                     $result = loginUser($email, $password); 
                     
                     if ($result['success']) {
                         $redirect = $_GET['redirect'] ?? ''; // or session redirect
                         $this->redirect($redirect ?: '');
                     } else {
                         $this->view('auth/signin', ['error' => $result['errors'][0], 'email' => $email]);
                     }
                } else {
                     $this->view('auth/signin', ['error' => 'Invalid email or password', 'email' => $email]);
                }
            } catch (Exception $e) {
                 $this->view('auth/signin', ['error' => $e->getMessage(), 'email' => $email]);
            }
        } else {
             $redirect = $_GET['redirect'] ?? '';
             $this->view('auth/signin', ['redirect' => $redirect]);
        }
    }

    public function signup() {
        if (isLoggedIn()) {
            $this->redirect('');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Use Core_Validator for validation
            $validator = new Core_Validator();
            $validator->addRule('name', 'required|min:2|max:100', 'Full Name')
                      ->addRule('email', 'required|email|unique:users,email', 'Email')
                      ->addRule('password', 'required|min:8', 'Password')
                      ->addRule('confirm_password', 'required|match:password', 'Password Confirmation');
            
            if (!$validator->validate($_POST)) {
                $this->view('auth/signup', [
                    'errors' => $validator->getErrors(),
                    'values' => $_POST
                ]);
                return;
            }
            
            // Validation passed, use existing registration function
            $result = registerUser($_POST['email'], $_POST['password'], $_POST['name'], $_POST['confirm_password']);
            
            if ($result['success']) {
                $this->redirect('');
            } else {
                $this->view('auth/signup', ['errors' => $result['errors'], 'values' => $_POST]);
            }
        } else {
            $this->view('auth/signup');
        }
    }

    public function logout() {
        logoutUser();
        $this->redirect('signin');
    }
}
