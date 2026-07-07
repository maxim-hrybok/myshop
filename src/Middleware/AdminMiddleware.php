<?php
namespace App\Middleware;

class AdminMiddleware implements MiddlewareInterface {
    public function handle(string $uri, string $method): void {
        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header('Location: /login');
            exit();
        }
    }
}