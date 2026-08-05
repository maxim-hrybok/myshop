<?php
namespace App\Services;

use App\Services\Contracts\PaymentServiceInterface;
use App\Config\ConfigService;
use Exception;

class PayPalService implements PaymentServiceInterface {
    private string $clientId;
    private string $secret;
    private string $baseUrl;
    private bool $isDevelopment;

    public function __construct(ConfigService $config) {
        $this->clientId = $config->get('paypal.client_id');
        $this->secret = $config->get('paypal.secret');
        $this->baseUrl = $config->get('paypal.mode') === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';


        $this->isDevelopment = $config->get('app.env') === 'development';
    }

    private function applySslFix(\CurlHandle $ch): void {
        if ($this->isDevelopment) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
    }
    
    private function getAccessToken(): string {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$this->baseUrl}/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->clientId}:{$this->secret}");

        $this->applySslFix($ch); 

        $result = curl_exec($ch);
        if (curl_errno($ch)) throw new Exception("PayPal Auth Error: " . curl_error($ch));
        //curl_close($ch);

        $json = json_decode($result, true);
        return $json['access_token'] ?? throw new Exception("Failed to get PayPal Access Token");
    }

    public function createOrder(float $amount, string $currency = 'USD'): string {
        $accessToken = $this->getAccessToken();
        
        $payload = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => $currency,
                    "value" => number_format($amount, 2, '.', '')
                ]
            ]]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$this->baseUrl}/v2/checkout/orders");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer {$accessToken}"
        ]);

        $this->applySslFix($ch);
        
        $result = curl_exec($ch);
        //curl_close($ch);

        $json = json_decode($result, true);
        return $json['id'] ?? throw new Exception("Failed to create PayPal Order");
    }

    public function capturePayment(string $transactionId): bool {
        $accessToken = $this->getAccessToken();
        
    
        // PayPal won't charge the customer twice.
        $idempotencyKey = "capture_{$transactionId}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$this->baseUrl}/v2/checkout/orders/{$transactionId}/capture");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer {$accessToken}",
            "PayPal-Request-Id: {$idempotencyKey}" 
        ]);

        $this->applySslFix($ch);

        $result = curl_exec($ch);
        //curl_close($ch);

        $json = json_decode($result, true);
        return isset($json['status']) && $json['status'] === 'COMPLETED';
    }
}