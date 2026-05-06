<?php
namespace App\Models;

class CommentModel {
    private \PDO $pdo;

    public function __construct(\PDO $pdo) 
    {
        $this->pdo = $pdo;
    }

    public function addComment(int $productId, int $userId, string $content): bool {
        $stmt = $this->pdo->prepare("INSERT INTO comments (product_id, user_id, content, status) VALUES (?, ?, ?, 'pending')");
        return $stmt->execute([$productId, $userId, $content]);
    }

    public function getApprovedCommentsForProduct(int $productId): array {
        $sql = "SELECT c.*, u.first_name 
                FROM comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.product_id = ? AND c.status = 'approved' 
                ORDER BY c.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPendingComments(): array {
        $sql = "SELECT c.*, u.first_name, p.name as product_name 
                FROM comments c 
                JOIN users u ON c.user_id = u.id 
                JOIN products p ON c.product_id = p.id 
                WHERE c.status = 'pending' 
                ORDER BY c.created_at ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $commentId, string $status): bool {
        $stmt = $this->pdo->prepare("UPDATE comments SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $commentId]);
    }

    public function deleteComment(int $commentId): bool {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$commentId]);
    }
}