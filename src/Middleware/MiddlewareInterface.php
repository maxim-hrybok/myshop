<?php
namespace App\Middleware;

interface MiddlewareInterface {
    /**
     * Handle the request. 
     * Throws an exception or redirects/exits if the check fails.
     */
    public function handle(string $uri, string $method): void;
}