-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               12.2.2-MariaDB - MariaDB Server
-- Server OS:                    Win64
-- HeidiSQL Version:             12.14.0.7165
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for project
CREATE DATABASE IF NOT EXISTS `project` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci */;
USE `project`;

-- Dumping structure for table project.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project.categories: ~3 rows (approximately)
INSERT INTO `categories` (`id`, `name`) VALUES
	(1, 'Electronics'),
	(2, 'Books'),
	(3, 'Clothing');

-- Dumping structure for table project.order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table project.order_items: ~16 rows (approximately)
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_at_purchase`) VALUES
	(1, 1, 1, 1, 440.10),
	(2, 2, 1, 1, 440.10),
	(3, 2, 3, 1, 19.99),
	(4, 3, 3, 1, 19.99),
	(5, 4, 1, 1, 440.10),
	(6, 5, 2, 1, 854.53),
	(7, 6, 2, 1, 854.53),
	(8, 6, 1, 1, 440.10),
	(9, 6, 4, 1, 12.50),
	(10, 7, 1, 1, 440.10),
	(11, 7, 2, 2, 854.53),
	(12, 8, 1, 1, 440.10),
	(13, 9, 1, 2, 440.10),
	(14, 9, 3, 1, 19.99),
	(15, 9, 4, 1, 12.50),
	(16, 9, 2, 1, 854.53),
	(17, 10, 1, 1, 440.10);

-- Dumping structure for table project.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table project.orders: ~9 rows (approximately)
INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `created_at`) VALUES
	(1, 2, 440.10, 'completed', '2026-03-03 20:40:46'),
	(2, 2, 460.09, 'completed', '2026-03-03 20:40:57'),
	(3, 2, 19.99, 'completed', '2026-03-03 20:41:12'),
	(4, 2, 440.10, 'completed', '2026-03-03 20:42:01'),
	(5, 2, 854.53, 'completed', '2026-03-03 20:42:54'),
	(6, 2, 1307.12, 'completed', '2026-03-03 20:53:49'),
	(7, 2, 2149.15, 'completed', '2026-03-03 20:54:00'),
	(8, 2, 440.10, 'completed', '2026-03-06 19:20:20'),
	(9, 2, 1767.21, 'completed', '2026-03-06 19:21:18'),
	(10, 2, 440.10, 'completed', '2026-03-06 19:38:45');

-- Dumping structure for table project.product_category_map
CREATE TABLE IF NOT EXISTS `product_category_map` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `product_category_map_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_category_map_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project.product_category_map: ~5 rows (approximately)
INSERT INTO `product_category_map` (`product_id`, `category_id`) VALUES
	(1, 1),
	(2, 1),
	(3, 3),
	(4, 2);

-- Dumping structure for table project.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `discount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `description` tinytext DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project.products: ~5 rows (approximately)
INSERT INTO `products` (`id`, `name`, `price`, `image_url`, `status`, `discount`, `description`, `stock`) VALUES
	(1, 'Smartphonexx', 500.11, '/public/assets/img/steam.png', 'available', 12.00, 'yes ', 114),
	(2, 'Laptop', 899.50, '/public/assets/img/steam.png', 'available', 5.00, 'asdfg', 118),
	(3, 'T-shirt', 19.99, '/public/assets/img/steam.png', 'available', 0.00, 'asdfgh', 120),
	(4, 'Novel Book', 12.50, '/public/assets/img/steam.png', 'available', 0.00, 'asdfghj', 21),
	(6, 'BOBA', 222.00, '/public/assets/img/steam.png', 'available', 22.00, 'MEGA BOBA asdf', 7);

-- Dumping structure for table project.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project.users: ~1 rows (approximately)
INSERT INTO `users` (`id`, `email`, `password_hash`, `first_name`, `is_admin`, `created_at`) VALUES
	(1, 'haker@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$WjJ2dmI1aUV5Vm1PRjV2bQ$LWSO9X2zFP7vD+S0IZwHwhMu692goTeIsw9agpqt4GI', 'caster', 0, '2026-02-24 11:51:43'),
	(2, 'admin@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$eHJJZG5zLnRYdmVkYi93Sw$tKueDAb7TE7uHYJWps07IMJa5ZuBawN90xFM48x+BPo', 'adminka', 1, '2026-03-01 19:25:57');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
