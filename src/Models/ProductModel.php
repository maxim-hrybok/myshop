<?php
//require_once __DIR__ . '/../config/database.php';
namespace App\Models;

class ProductModel {
    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

         /**
          * Finds all products in the database and returns them as an array of associative arrays.
          *
          * @return array The product data as an array of associative arrays.
          */
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

         /**
          * Creates a new product in the database with the provided details.
          *
          * @param string $name The name of the product.
          * @param float $price The price of the product.
          * @param int $discount The discount percentage for the product.
          * @param string $description The description of the product.
          * @param string $imageUrl The URL of the product's image.
          * @return bool True if the product was created successfully, false otherwise.
          */
    public function createProduct(string $name, float $price, int $discount, string $description,  string $imageUrl): bool{
        $stmt = $this->pdo->prepare(
            "INSERT INTO products (name, price, discount, description, image_url) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$name, $price, $discount, $description, $imageUrl]);
    }

        /**
         * Updates an existing product in the database with the provided details.
         *
         * @param int $id The ID of the product to update.
         * @param string $name The name of the product.
         * @param float $price The price of the product.
         * @param int $discount The discount percentage for the product.
         * @param string $description The description of the product.
         * @param string $imageUrl The URL of the product's image.
         * @return bool True if the product was updated successfully, false otherwise.
         */
    public function updateProduct(int $id, string $name, float $price, int $discount, string $description, string $imageUrl): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE products SET name = ?, price = ?, discount = ?, description = ?, image_url = ? WHERE id = ?"
        );
        return $stmt->execute([$name, $price, $discount, $description, $imageUrl, $id]);
    }

     /**
      * Finds a single product by its primary key (ID) and deletes it from the database.
      * @param int $id The ID of the product to find.
      * @return bool True if the product was deleted successfully, false otherwise.
      */
    public function deleteProduct(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
