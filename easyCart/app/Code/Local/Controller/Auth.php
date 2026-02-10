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
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            
            // Using existing global logic for now
            $result = registerUser($email, $password, $name, $confirm);
            
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
