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
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $userModel = new UserModel($this->pdo);
        $user = $userModel->findUserByEmail($email);

        // Verify user exists and the password is correct.
        if ($user && password_verify($password, $user['password_hash'])) {
            // Login success! Store user info in the session.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'];

            // Redirect to the homepage after login.
            header('Location: /');
            exit();
        } else {
            // Login failed. Show the login page again with an error.
            $this->smarty->assign('error', 'Invalid email or password.');
            $this->showLoginForm();
        }
    }

    // Handles the registration form submission
    public function handleRegister() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $firstName = $_POST['first_name'] ?? '';

        // !!!gonna add server-side validation here (e.g., check if fields are empty, if email is valid).

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