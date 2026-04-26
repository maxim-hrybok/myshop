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
public function getFilteredProducts(int $page = 1, int $limit = 10, string $search = '', string $status = 'all', array $categoryIds = []): array {
    // --- Input sanitization ---
    if ($page < 1) $page = 1;
    if ($limit < 1) $limit = 10;

    $offset = ($page - 1) * $limit;
    $params = [];
    $whereClauses = [];

    // Separation the special "uncategorized" (0) from real ids
    // e.g. [0, 3, 5] → $wantsUncategorized = true, $realIds = [3, 5]
    $wantsUncategorized = in_array('0', $categoryIds) || in_array(0, $categoryIds);
    $realCategoryIds    = array_filter($categoryIds, fn($id) => (int)$id !== 0);

    // -----------------------------------------------------------------------
    //  • No category filter at all  → no JOIN needed
    //  • Only real IDs [1,2]        → INNER JOIN  (only products IN those categories)
    //  • Only uncategorized [0]     → LEFT JOIN   (we need NULLs to find orphans)
    //  • Mixed [0,1,2]              → LEFT JOIN   (need both orphans and matched rows)
    // -----------------------------------------------------------------------
    if (!empty($categoryIds)) {

        $joinClause = "LEFT JOIN product_category_map pcm ON p.id = pcm.product_id";

        $categoryConditions = []; // will be OR-ed together inside one AND block

        // Real category IDs → standard IN() check
        if (!empty($realCategoryIds)) {
            $placeholders = implode(',', array_fill(0, count($realCategoryIds), '?'));
            $categoryConditions[] = "pcm.category_id IN ($placeholders)";
            foreach ($realCategoryIds as $catId) {
                $params[] = (int)$catId;
            }
        }

        // "Uncategorized" → product has NO row in product_category_map at all
        if ($wantsUncategorized) {
            $categoryConditions[] = "pcm.product_id IS NULL";
        }

        // Combine (pcm.category_id IN (1,2) OR pcm.product_id IS NULL)
        $whereClauses[] = '(' . implode(' OR ', $categoryConditions) . ')';

    } else {
        $joinClause = ""; // no category filter → no join needed
    }

   
    // filters (status, search) 
    
    if ($status !== 'all') {
        $whereClauses[] = "p.status = ?";
        $params[] = $status;
    }

    if ($search !== '') {
        $whereClauses[] = "p.name LIKE ?";
        $params[] = "%$search%";
    }

    $whereSql = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";
    //  Count total (for pagination) then fetch the page
    $countSql = "SELECT COUNT(DISTINCT p.id) as total FROM products p $joinClause $whereSql";
    $stmtCount = $this->pdo->prepare($countSql);
    $stmtCount->execute($params);
    $totalItems = (int)$stmtCount->fetchColumn();
    $totalPages  = (int)ceil($totalItems / $limit);

    $sql = "SELECT DISTINCT p.* FROM products p $joinClause $whereSql ORDER BY p.id DESC LIMIT $limit OFFSET $offset";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    if (empty($products)) {
        return [
            'products'     => [],
            'total_pages'  => $totalPages,
            'current_page' => $page,
        ];
    }

    // --- EAGER LOADING CATEGORIES (The Fix) ---
    $productIds = array_column($products, 'id');
    $inQuery = implode(',', array_fill(0, count($productIds), '?'));
    
    $catSql = "SELECT pcm.product_id, c.id, c.name 
               FROM categories c 
               JOIN product_category_map pcm ON c.id = pcm.category_id 
               WHERE pcm.product_id IN ($inQuery)";
    $catStmt = $this->pdo->prepare($catSql);
    $catStmt->execute($productIds);
    $categoryRows = $catStmt->fetchAll(\PDO::FETCH_ASSOC);

    $categoriesByProduct =[];
    foreach ($categoryRows as $row) {
        $categoriesByProduct[$row['product_id']][] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }

    foreach ($products as &$product) {
        $product['categories'] = $categoriesByProduct[$product['id']] ?? [];
    }

    return[
        'products'     => $products,
        'total_pages'  => $totalPages,
        'current_page' => $page,
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
        $stmt->execute([$quantity, $id, $quantity]);
    // Must check rowCount to ensure the atomic condition (stock >= ?) was met
        return $stmt->rowCount() > 0;
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
