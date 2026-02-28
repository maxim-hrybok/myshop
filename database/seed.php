<?php
$servername = "localhost:3307";
$username = "root";
$password = "22833";

try {
    // Подключаемся к базе данных
    $conn = new PDO("mysql:host=$servername;dbname=project;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected successfully<br>";

    // --- ВСТАВКА ДАННЫХ ---

    // Пример категорий
    $categories = [
        ['id' => 1, 'name' => 'Electronics'],
        ['id' => 2, 'name' => 'Books'],
        ['id' => 3, 'name' => 'Clothing']
    ];

    $stmt = $conn->prepare("INSERT INTO categories (id, name) VALUES (:id, :name)");
    foreach ($categories as $cat) {
        $stmt->execute([
            ':id' => $cat['id'],
            ':name' => $cat['name']
        ]);
    }

    echo "✅ Categories inserted successfully<br>";

    // Пример продуктов
    $products = [
        ['id' => 1, 'name' => 'Smartphone', 'price' => 499.99, 'image_url' => 'images/smartphone.jpg', 'status' => 'available', 'discount' => 10.00, 'quantity' => 50],
        ['id' => 2, 'name' => 'Laptop', 'price' => 899.50, 'image_url' => 'images/laptop.jpg', 'status' => 'available', 'discount' => 5.00, 'quantity' => 25],
        ['id' => 3, 'name' => 'T-shirt', 'price' => 19.99, 'image_url' => 'images/tshirt.jpg', 'status' => 'available', 'discount' => 0.00, 'quantity' => 100],
        ['id' => 4, 'name' => 'Novel Book', 'price' => 12.50, 'image_url' => 'images/book.jpg', 'status' => 'available', 'discount' => 0.00, 'quantity' => 40],
        ['id' => 5, 'name' => 'Headphones', 'price' => 59.90, 'image_url' => 'images/headphones.jpg', 'status' => 'available', 'discount' => 15.00, 'quantity' => 30],
    ];

    $stmt = $conn->prepare("INSERT INTO products (id, name, price, image_url, status, discount, quantity)
                            VALUES (:id, :name, :price, :image_url, :status, :discount, :quantity)");
    foreach ($products as $prod) {
        $stmt->execute([
            ':id' => $prod['id'],
            ':name' => $prod['name'],
            ':price' => $prod['price'],
            ':image_url' => $prod['image_url'],
            ':status' => $prod['status'],
            ':discount' => $prod['discount'],
            ':quantity' => $prod['quantity']
        ]);
    }

    echo "✅ Products inserted successfully<br>";

    // Привязка продуктов к категориям (пример)
    $mappings = [
        ['product_id' => 1, 'category_id' => 1], // Smartphone → Electronics
        ['product_id' => 2, 'category_id' => 1], // Laptop → Electronics
        ['product_id' => 3, 'category_id' => 3], // T-shirt → Clothing
        ['product_id' => 4, 'category_id' => 2], // Book → Books
        ['product_id' => 5, 'category_id' => 1], // Headphones → Electronics
    ];

    $stmt = $conn->prepare("INSERT INTO product_category_map (product_id, category_id)
                            VALUES (:product_id, :category_id)");
    foreach ($mappings as $map) {
        $stmt->execute([
            ':product_id' => $map['product_id'],
            ':category_id' => $map['category_id']
        ]);
    }

    echo "✅ Product-category mappings inserted successfully<br>";

} catch (PDOException $e) {
    echo "❌ Connection or query failed: " . $e->getMessage();
}

// Закрываем соединение после всех операций
$conn = null;
?>
