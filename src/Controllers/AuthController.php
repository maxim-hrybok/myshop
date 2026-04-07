<?php

namespace App\Controllers;

use App\Models\UserModel;
use Smarty\Smarty;

class AuthController {
    private \PDO $pdo;
    private Smarty $smarty;

    public function __construct(\PDO $pdo, Smarty $smarty) {
        $this->pdo = $pdo;
        $this->smarty = $smarty;
    }

    // Displays the login page
    public function showLoginForm() {
        $this->smarty->assign('pageTitle', 'Login');
        $this->smarty->display('auth/login.tpl');
    }

    // Displays the registration page
    public function showRegisterForm() {
        $this->smarty->assign('pageTitle', 'Register');
        $this->smarty->display('auth/register.tpl');
    }

    // Handles the login form submission
    public function handleLogin() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // 1. Get the user's IP Address
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userModel = new UserModel($this->pdo);       

        // 2. Brute force protection imp-
        $attemptsData = $userModel->getLoginAttempts($ipAddress);
        $maxAttempts = 3;
        $lockoutMinutes = 5;

        if ($attemptsData && $attemptsData['attempts'] >= $maxAttempts) {
            // Calculate how much time has passed since their last failed attempt
            $lastAttemptTime = strtotime($attemptsData['last_attempt']);
            $timePassedSeconds = time() - $lastAttemptTime;
            $lockoutSeconds = $lockoutMinutes * 60;
            
            if ($timePassedSeconds < $lockoutSeconds) {
                //Calculate remaining time for a helpful UX.
                $minutesLeft = ceil(($lockoutSeconds - $timePassedSeconds) / 60);
                
                // HTTP 429 REST standard for "Too Many Requests"
                http_response_code(429);
                $this->smarty->assign('error', "Too many failed attempts. Please try again in {$minutesLeft} minute(s).");
                $this->showLoginForm();
                return; // STOP execution 
            } else {
                // The 5 minutes  passed- clear record
                $userModel->clearLoginAttempts($ipAddress);
            }
        }


        #login validation check
         if (empty($email) || empty($password)) {
            $this->smarty->assign('error', 'Please enter both email and password.');
            $this->showLoginForm();
            return; // Stop execution
        }

        #email validation check
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->smarty->assign('error', 'Invalid email format.');

            # keep email in form  mb
            #$this->smarty->assign('old', ['email' => $email]);
            $this->showLoginForm();
            return;
        }

        $userModel = new UserModel($this->pdo);
        $user = $userModel->findUserByEmail($email);

        // Verify user exists and the password is correct.
        if ($user && password_verify($password, $user['password_hash'])) {
            // Login success - store user info in the session +++ CLEAR FAILD ATTEMPTS.
            $userModel->clearLoginAttempts($ipAddress);


            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'];

            # Admin flag (if you have an is_admin column in your users table you have access to product management features)
            $_SESSION['is_admin'] = $user['is_admin'] ?? 0;

            // Redirect to the homepage after login.
            header('Location: /');
            exit();
        } else {
            // Record failed login attemts for brute force protection.
            $userModel->recordFailedLogin($ipAddress);

            // Login failed. Show the login page again with an error.
            $this->smarty->assign('error', 'Invalid email or password.');
            $this->showLoginForm();
        }
    }

    // Handles the registration form submission
    public function handleRegister() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $firstName = trim($_POST['first_name'] ?? '');

   
        

        #validate required fields
        if (empty($email) || empty($password) || empty($firstName)) {
            $this->smarty->assign('error', 'All fields are required.');
            $this->showRegisterForm();
            return; // Exit early to prevent database queries
        }

        #validate name length
         $nameLen = mb_strlen($firstName);
        if ($nameLen < 4 || $nameLen > 50) {
            $this->smarty->assign('error', 'First name must be between 4 and 50 characters.');
            $this->showRegisterForm();
            return;
        }

        #validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->smarty->assign('error', 'Please provide a valid email address.');
            $this->showRegisterForm();
            return;
        }
        
        #validate email length
        if (strlen($email) > 255) {
            $this->smarty->assign('error', 'Email address is too long (maximum 255 characters).');
            $this->showRegisterForm();
            return;
        }
        
        #validate password length
        if (strlen($password) > 255) {
            $this->smarty->assign('error', 'Password is too long (maximum 255 characters).');
            $this->showRegisterForm();
            return;
        }

        $userModel = new UserModel($this->pdo);
        // Check if user already exists
        if ($userModel->findUserByEmail($email)) {
            $this->smarty->assign('error', 'A user with this email already exists.');
            $this->showRegisterForm();
            return;
        }

       

        // Create the new user
        $success = $userModel->createUser($email, $password, $firstName);
        if ($success) {
            // Automatically log the user in after successful registration

            $_POST['email'] = $email;
            $_POST['password'] = $password;
            
            $this->handleLogin();
        } else {
            $this->smarty->assign('error', 'An error occurred during registration. Please try again.');
            $this->showRegisterForm();
        }
    }

    // Logs the user out
    public function logout() {
        session_destroy();
        header('Location: /');
        exit();
    }
}