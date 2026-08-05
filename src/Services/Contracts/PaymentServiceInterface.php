<?php
namespace App\Services\Contracts;

interface PaymentServiceInterface {
    public function createOrder(float $amount, string $currency): string;
    public function capturePayment(string $transactionId): bool;
}