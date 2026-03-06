<?php
namespace App\Models;

class OrderModel {
    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function createOrder(int $userId, float $totalPrice): int {
        $stmt = $this->pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'completed')");// почему изначально комплитид , потом хочу сделать вкладку в админке для подтверждения заказа пока ок
        $stmt->execute([$userId, $totalPrice]);
        return (int)$this->pdo->lastInsertId();// зачем почему и как !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    }

    // Add individual items to the order
    public function addOrderItem(int $orderId, int $productId, int $qty, float $price): bool {
        $stmt = $this->pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$orderId, $productId, $qty, $price]);
    }

    // Get all orders for a specific user (History)
    public function getOrdersByUserId(int $userId): array {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Get items for a specific order (For detail view)
    public function getOrderItems(int $orderId): array {
        $sql = "SELECT oi.*, p.name, p.image_url 
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}