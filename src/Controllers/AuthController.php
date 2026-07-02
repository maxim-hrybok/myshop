<?php
namespace App\Controllers;

use App\Services\AuthService;
use Smarty\Smarty;
use Exception;

class AuthController {
    private AuthService $authService;
    private Smarty $smarty;

    public function __construct(AuthService $authService, Smarty $smarty) {
        $this->authService = $authService;
        $this->smarty = $smarty;
    }

    public function showLoginForm(array $vars = []) {
        $this->smarty->assign('pageTitle', 'Login');
        $this->smarty->display('auth/login.tpl');
    }

    public function showRegisterForm(array $vars = []) {
        $this->smarty->assign('pageTitle', 'Register');
        $this->smarty->display('auth/register.tpl');
    }

    public function handleLogin(array $vars = []) {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'];

        try {
            // Normal login requires CAPTCHA verification (skipCaptcha = false)
            $user = $this->authService->attemptLogin($email, $password, $ip, $recaptchaResponse, false);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'];
            $_SESSION['is_admin'] = $user['is_admin'] ?? 0;
            
            header('Location: /');
            exit();

        } catch (Exception $e) {
            if ($e->getCode() === 429) {
                http_response_code(429);
            }
            $this->smarty->assign('error', $e->getMessage());
            $this->showLoginForm();
        }
    }

    public function handleRegister(array $vars = []) {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $firstName = trim($_POST['first_name'] ?? '');
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'];

        try {
            // 1. Register the user
            $this->authService->register($email, $password, $firstName, $recaptchaResponse);
            
            // 2. Auto-login upon success using the Service directly (skipCaptcha = true)
            $user = $this->authService->attemptLogin($email, $password, $ip, '', true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'];
            $_SESSION['is_admin'] = $user['is_admin'] ?? 0;
            
            header('Location: /');
            exit();

        } catch (Exception $e) {
            $this->smarty->assign('error', $e->getMessage());
            $this->showRegisterForm();
        }
    }

    public function logout(array $vars = []) {
        session_destroy();
        header('Location: /');
        exit();
    }
}