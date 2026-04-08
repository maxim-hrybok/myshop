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
          * @return array The product data as an array of associative arrays.
          */  
    public function getAllProducts(): array {
        $stmt = $this->pdo->query("SELECT * FROM products");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

         /**
          * Retrieves products based on various filters such as pagination, search term, status, and category IDs.
          */       
    public function getFilteredProducts(int $page = 1, int $limit = 10, string $search = '', string $status = 'all', array $categoryIds =[]): array {
        // Validation for negative or zero pages
        if ($page < 1) $page = 1;
        if ($limit < 1) $limit = 10;
        
        // SELECT * FROM products WHERE status = 'available' AND name LIKE '%phone%' AND category_id IN (1, 2) sql that it Can make after all..
        $offset = ($page - 1) * $limit;
        $params = [];
        $whereClauses =[];//["p.status = ?", "p.name LIKE ?", "pcm.category_id IN (?, ?)"]
        $joinClause = "";

        // 1. Filter by Status
        if ($status !== 'all') {
            $whereClauses[] = "p.status = ?";
            $params[] = $status;
        }

        // 2. Filter by Search string
        if ($search !== '') {
            $whereClauses[] = "p.name LIKE ?";
            $params[] = "%$search%";
        }

        // 3. Filter by Categories (Many-to-Many logic)
        if (!empty($categoryIds)) {
            // Create placeholders exactly matching the number of IDs: "?, ?, ?"
            $inQuery = implode(',', array_fill(0, count($categoryIds), '?'));
            $joinClause = "JOIN product_category_map pcm ON p.id = pcm.product_id";
            $whereClauses[] = "pcm.category_id IN ($inQuery)";
            foreach ($categoryIds as $catId) {
                $params[] = (int)$catId;
            }
        }

        $whereSql = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : ""; //WHERE p.status = ? AND p.name LIKE ? AND p.category_id IN (?, ?)

        // First, count total items for pagination math
        $countSql = "SELECT COUNT(DISTINCT p.id) as total FROM products p $joinClause $whereSql";
        $stmtCount = $this->pdo->prepare($countSql);
        $stmtCount->execute($params);
        $totalItems = (int)$stmtCount->fetchColumn();
        $totalPages = ceil($totalItems / $limit);

        // Second, fetch the actual data
        $sql = "SELECT DISTINCT p.* FROM products p $joinClause $whereSql ORDER BY p.id DESC LIMIT $limit OFFSET $offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Fetch categories for each product to display in the list
        foreach ($products as &$product) {
            $product['categories'] = $this->getProductCategories($product['id']);
        }

        

        return[
            'products' => $products,
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
            'current_page' => $page
        ];
    }
         /**
          * finds a single product by its ID and returns it as an associative array, or null if not found.
          */
    public function findProductById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($product) {
            $product['categories'] = $this->getProductCategories($id);
        }

        return $product ?: null;
    }
         /**
          * gets the categories associated with a product by its ID.
          */    
    public function getProductCategories(int $productId): array {
        $sql = "SELECT c.id, c.name FROM categories c 
                JOIN product_category_map pcm ON c.id = pcm.category_id 
                WHERE pcm.product_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

         /**
          * Creates a new product in the database with the provided details.
          */
      public function createProduct(string $name, float $price, int $discount, int $stock, string $description, string $imageUrl, string $status, array $categoryIds): bool {
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO products (name, price, discount, stock, description, image_url, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $price, $discount, $stock, $description, $imageUrl, $status]);
            
            $productId = (int)$this->pdo->lastInsertId();
            $this->syncProductCategories($productId, $categoryIds);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function updateProduct(int $id, string $name, float $price, int $discount, int $stock, string $description, string $imageUrl, string $status, array $categoryIds): bool {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE products SET name = ?, price = ?, discount = ?, stock = ?, description = ?, image_url = ?, status = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $price, $discount, $stock, $description, $imageUrl, $status, $id]);

            $this->syncProductCategories($id, $categoryIds);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    private function syncProductCategories(int $productId, array $categoryIds): void {
        // Clear old relations
        $stmt = $this->pdo->prepare("DELETE FROM product_category_map WHERE product_id = ?");
        $stmt->execute([$productId]);

        // Insert new relations
        if (!empty($categoryIds)) {
            $stmtInsert = $this->pdo->prepare("INSERT INTO product_category_map (product_id, category_id) VALUES (?, ?)");
            foreach ($categoryIds as $catId) {
                $stmtInsert->execute([$productId, $catId]);
            }
        }
    }

    public function deleteProduct(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getStock(int $id): int {
        $stmt = $this->pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? (int)$result['stock'] : 0;
    }

    public function decreaseStock(int $id, int $quantity): bool {
        $stmt = $this->pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
        return $stmt->execute([$quantity, $id, $quantity]);
    }

    public function getProductWithCalculations(int $id): ?array {
        $product = $this->findProductById($id);
        if (!$product) return null;
        
        $product['final_price'] = $product['price'];
        if ($product['discount'] > 0) {
            $product['final_price'] = $product['price'] * (1 - $product['discount'] / 100);
        }
        $product['is_discounted'] = $product['discount'] > 0;
        
        return $product;
    }
}
?>
