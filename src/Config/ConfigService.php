<?php
namespace App\Config;

class ConfigService {
    private array $settings;
    public function __construct(array $env) {
      $this->settings = [
            'app' => [
                'env' => $env['APP_ENV'] ?? 'production',
            ],
            'db' => [
                'host'    => $env['DB_HOST'] ?? '127.0.0.1:3306',
                'name'    => $env['DB_NAME'] ?? '',
                'user'    => $env['DB_USER'] ?? 'root',
                'pass'    => $env['DB_PASS'] ?? '',
                'charset' => $env['DB_CHARSET'] ?? 'utf8mb4',
            ],
            'recaptcha' => [
                'site_key'   => $env['RECAPTCHA_SITE_KEY'] ?? '',
                'secret_key' => $env['RECAPTCHA_SECRET_KEY'] ?? '',
            ],
            'paypal' => [
                'client_id' => $env['PAYPAL_CLIENT_ID'] ?? '',
                'secret'    => $env['PAYPAL_SECRET'] ?? '',
                'mode'      => $env['PAYPAL_MODE'] ?? 'sandbox',
            ]
        ];
    }
     /**
     * Retrieve a configuration value using dot notation (e.g., 'db.host')
     */
    public function get(string $key, $default = null) {
        $keys = explode('.', $key);
        $value = $this->settings;

        foreach ($keys as $k) {
            if (is_array($value) && array_key_exists($k, $value)) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }
        
        return $value;
    }
}