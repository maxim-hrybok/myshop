<?php
namespace App\Services;

use App\Models\ProductModel;
use App\Models\OrderModel;

class CartService {
    private ProductModel $productModel;
    private OrderModel $orderModel;
    private \PDO $pdo; // Needed for transactions

    public function __construct(ProductModel $productModel, OrderModel $orderModel, \PDO $pdo) {
        $this->productModel = $productModel;
        $this->orderModel = $orderModel;
        $this->pdo = $pdo;

        // Ensure session exists
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    }

    // 1. Add item logic
    public function add(int $productId, int $qty): array {
        $stock = $this->productModel->getStock($productId);
        $currentQty = $_SESSION['cart'][$productId] ?? 0;

        if (($currentQty + $qty) > $stock) {
            return ['success' => false, 'message' => "Only $stock items left in stock."];
        }

        $_SESSION['cart'][$productId] = $currentQty + $qty;
        return ['success' => true];
    }

    // 2. Remove item logic
    public function remove(int $productId): void {
        unset($_SESSION['cart'][$productId]);
    }

    // 3. Get Cart Details (for View)
    public function getCartDetails(): array {
        $items = [];
        $total = 0;

        foreach ($_SESSION['cart'] as $id => $qty) {
            $product = $this->productModel->findProductById($id);
            if ($product) {
                $realPrice = $product['price'] * (1 - $product['discount'] / 100);
                
                $items[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'image_url' => $product['image_url'],
                    'price' => $realPrice,
                    'qty' => $qty,
                    'subtotal' => $realPrice * $qty
                ];
                $total += ($realPrice * $qty);
            }
        }
        return ['items' => $items, 'total' => $total];
    }

    // 4. Checkout Logic (Transaction)
    public function checkout(int $userId): array {
        if (empty($_SESSION['cart'])) {
            return ['success' => false, 'message' => 'Cart is empty'];
        }

        try {
            $this->pdo->beginTransaction();

            $cartData = $this->getCartDetails();
            $orderId = $this->orderModel->createOrder($userId, $cartData['total']);

            foreach ($cartData['items'] as $item) {
            // Attempt to decrease stock atomically FIRST. If it fails, another user bought the last item.
            $stockDecreased = $this->productModel->decreaseStock($item['id'], $item['qty']);
            
            if (!$stockDecreased) {
                throw new \Exception("Product '{$item['name']}' is out of stock or insufficient quantity available.");
            }

            // If stock decreased successfully, log the order item.
            $this->orderModel->addOrderItem($orderId, $item['id'], $item['qty'], $item['price']);
            }

            $this->pdo->commit();
            $_SESSION['cart'] = []; // Clear cart
            return ['success' => true, 'order_id' => $orderId];

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}