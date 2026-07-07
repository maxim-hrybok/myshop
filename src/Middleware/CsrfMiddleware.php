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
            $submittedToken = $_POST['csrf_token'] ?? '';
            $sessionToken = $_SESSION['csrf_token'] ?? '';
            
            if (empty($sessionToken) || !hash_equals($sessionToken, $submittedToken)) {
                http_response_code(403);
                die("403 Forbidden: Invalid or missing CSRF Token. Please refresh and try again.");
            }
        }
    }
}