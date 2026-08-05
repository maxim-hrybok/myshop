<?php
namespace App\Middleware;

class CsrfMiddleware implements MiddlewareInterface {
    
    // Webhooks and APIs that do NOT require a CSRF session token
    private array $exemptRoutes = [
        '/api/paypal/webhook'
    ];

    public function handle(string $uri, string $method): void {
        // Only check POST requests and ignore exempt routes
        if ($method === 'POST' && !in_array($uri, $this->exemptRoutes)) {
            
            // 1. Ищем токен в стандартном POST (для обычных форм)
            // 2. Ищем токен в заголовке X-CSRF-Token (для JSON API / fetch)
            $submittedToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            $sessionToken = $_SESSION['csrf_token'] ?? '';
            
            if (empty($sessionToken) || !hash_equals($sessionToken, $submittedToken)) {
                http_response_code(403);
                die("403 Forbidden: Invalid or missing CSRF Token. Please refresh and try again.");
            }
        }
    }
}