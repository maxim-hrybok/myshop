<?php
namespace App\Controllers\Api;

use App\Services\Contracts\PaymentServiceInterface;
use App\Services\CartService;
use App\Repositories\OrderRepository;

class PayPalController {
    private PaymentServiceInterface $paymentService;
    private CartService $cartService;
    private OrderRepository $orderRepo;

    public function __construct(PaymentServiceInterface $paymentService, CartService $cartService, OrderRepository $orderRepo) {
        $this->paymentService = $paymentService;
        $this->cartService = $cartService;
        $this->orderRepo = $orderRepo;
    }

    // Called by JS to generate the popup
    public function createOrder() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        try {
            $cartDetails = $this->cartService->getCartDetails();
            if ($cartDetails['total'] <= 0) throw new \Exception("Cart is empty");

            $paypalOrderId = $this->paymentService->createOrder($cartDetails['total'], 'USD');
            
            header('Content-Type: application/json');
            echo json_encode(['id' => $paypalOrderId]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Called by JS after user approves payment
    public function captureOrder() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $data = json_decode(file_get_contents('php://input'), true);
        $paypalOrderId = $data['orderID'] ?? '';

        try {
            if ($this->paymentService->capturePayment($paypalOrderId)) {
                // Payment success -> Move cart to database and clear session
                $result = $this->cartService->checkout($_SESSION['user_id']);
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'order_id' => $result['order_id']]);
            } else {
                throw new \Exception("Payment Capture Failed");
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    
    public function webhook() {
        $payload = file_get_contents('php://input');
        $event = json_decode($payload, true);

        if ($event && $event['event_type'] === 'PAYMENT.CAPTURE.COMPLETED') {
            //would verify the webhook signature here. ##################################################
        
            error_log("PayPal Webhook: Payment captured successfully for resource: " . $event['resource']['id']);
            
            // system captures synchronously in captureOrder(), 
            // so database updates are already done. This webhook acts as a fallback/log.
        }

       
        http_response_code(200);
    }
}