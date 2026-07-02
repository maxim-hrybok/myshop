<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Config\ConfigService;
use Exception;

class AuthService {
    private UserRepository $userRepo;
    private ConfigService $config;

    public function __construct(UserRepository $userRepo, ConfigService $config) {
        $this->userRepo = $userRepo;
        $this->config = $config;
    }

    public function attemptLogin(string $email, string $password, string $ip, string $recaptchaResponse, bool $skipCaptcha = false): array {
        if (!$skipCaptcha && !$this->verifyRecaptcha($recaptchaResponse)) {
            throw new Exception('Please complete the CAPTCHA verification.');
        }

        $attemptsData = $this->userRepo->getLoginAttempts($ip);
        if ($attemptsData && $attemptsData['attempts'] >= 3) {
            $timePassed = time() - strtotime($attemptsData['last_attempt']);
            if ($timePassed < 300) { // 5 minutes
                $minutesLeft = ceil((300 - $timePassed) / 60);
                throw new Exception("Too many failed attempts. Please try again in {$minutesLeft} minute(s).", 429);
            } else {
                $this->userRepo->clearLoginAttempts($ip);
            }
        }

        if (empty($email) || empty($password)) {
            throw new Exception('Please enter both email and password.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format.');
        }

        $user = $this->userRepo->findUserByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            $this->userRepo->clearLoginAttempts($ip);
            return $user; // Success
        }

        $this->userRepo->recordFailedLogin($ip);
        throw new Exception('Invalid email or password.');
    }

    public function register(string $email, string $password, string $firstName, string $recaptchaResponse): void {
        if (!$this->verifyRecaptcha($recaptchaResponse)) {
            throw new Exception('Please complete the CAPTCHA verification.');
        }

        if (empty($email) || empty($password) || empty($firstName)) {
            throw new Exception('All fields are required.');
        }

        $nameLen = mb_strlen($firstName);
        if ($nameLen < 4 || $nameLen > 50) {
            throw new Exception('First name must be between 4 and 50 characters.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255 || strlen($password) > 255) {
            throw new Exception('Invalid input formats or lengths.');
        }

        if ($this->userRepo->findUserByEmail($email)) {
            throw new Exception('A user with this email already exists.');
        }

        if (!$this->userRepo->createUser($email, $password, $firstName)) {
            throw new Exception('An error occurred during registration.');
        }
    }

    private function verifyRecaptcha(string $recaptchaResponse): bool {
        if (empty($recaptchaResponse)) return false;

        $secret = $this->config->get('recaptcha.secret_key');
        $verifyUrl = "https://www.google.com/recaptcha/api/siteverify";
        $postData = http_build_query(['secret' => $secret, 'response' => $recaptchaResponse]);
        
        $isDevelopment = $this->config->get('app.env') === 'development';
        $response = false;

        if (function_exists('curl_init')) {
            $ch = \curl_init();
            \curl_setopt($ch, CURLOPT_URL, $verifyUrl);
            \curl_setopt($ch, CURLOPT_POST, 1);
            \curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            if ($isDevelopment) \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = \curl_exec($ch);
            //\curl_close($ch);
        } else {
            $options = [
                'http' => ['header' => "Content-type: application/x-www-form-urlencoded\r\n", 'method' => 'POST', 'content' => $postData],
                'ssl' => ['verify_peer' => !$isDevelopment, 'verify_peer_name' => !$isDevelopment]
            ];
            $context  = stream_context_create($options);
            $response = @file_get_contents($verifyUrl, false, $context);
        }

        if (!$response) return false;
        $responseData = json_decode($response);
        return isset($responseData->success) && $responseData->success === true;
    }
}