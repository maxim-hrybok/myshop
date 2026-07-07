<?php
namespace App\Middleware;

class AuthMiddleware implements MiddlewareInterface {
    public function handle(string $uri, string $method): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
    }
}