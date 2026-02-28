<?php
//require_once __DIR__ . '/../config/database.php';
namespace App\Models;

class ProductModel {
    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAllProducts(): array {
        $stmt = $this->pdo->query("SELECT * FROM products");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    /**
     * Finds a single product by its primary key (ID).
     *
     * @param int $id The ID of the product to find.
     * @return array|null The product data as an associative array, or null if not found.
     */
    public function findProductById(int $id): ?array {
        // 1. Prepare the SQL query with a placeholder (?) to prevent SQL injection.
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");

        // 2. Execute the prepared statement, passing the actual ID in an array.
        // PDO safely binds the value, treating it as data, not as part of the SQL command.
        $stmt->execute([$id]);

        // 3. Fetch the result. fetch() returns the single row, or false if no row is found.
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);

        // 4. Return the product data or null if it wasn't found.
        return $product ?: null;
    }
}
?>
