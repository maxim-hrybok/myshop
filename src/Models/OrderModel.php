<?php
namespace App\Models;

use Exception;

class OrderModel {
    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function createOrder(int $userId, float $totalPrice): int {
        $stmt = $this->pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'pending')");// почему изначально комплитид , потом хочу сделать вкладку в админке для подтверждения заказа пока ок
        $stmt->execute([$userId, $totalPrice]);
        return (int)$this->pdo->lastInsertId();
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

    public function getAllOrders(): array {
        $stmt = $this->pdo->query(
            "SELECT * FROM orders
             ORDER BY 
                CASE status
                    WHEN 'pending' THEN 1
                    WHEN 'completed' THEN 2
                    WHEN 'cancelled' THEN 3
                END,
                created_at DESC; 
        ");
        return $stmt-> fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getOrderById(int $orderId): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $order ?: null;
    }


    public function updateOrderStatus(int $orderId, string $newStatus): bool {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$newStatus, $orderId]);
    }

    public function deleteOrder(int $orderId): bool {
        try {
            $this->pdo->beginTransaction();

            // 1. Delete the children (order_items) FIRST
            $stmt1 = $this->pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt1->execute([$orderId]);

            // 2. Delete the parent (orders) SECOND
            $stmt2 = $this->pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmt2->execute([$orderId]);

            $this->pdo->commit();
            return true;

        } catch (\Exception $e) {
            // Roll back to previous state if anything fails
            $this->pdo->rollBack();
            return false; 
        }
    }     
}
?>