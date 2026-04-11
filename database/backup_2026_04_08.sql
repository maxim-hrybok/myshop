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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project.categories: ~4 rows (approximately)
INSERT INTO `categories` (`id`, `name`) VALUES
	(1, 'Electronics'),
	(2, 'Books_2'),
	(3, 'Clothing'),
	(4, 'Dota2');

-- Dumping structure for table project.login_attempts
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 1,
  `last_attempt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_address` (`ip_address`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project.login_attempts: ~0 rows (approximately)

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table project.order_items: ~24 rows (approximately)
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
	(17, 10, 1, 1, 440.10),
	(18, 11, 1, 1, 440.10),
	(19, 12, 4, 1, 12.50),
	(20, 13, 2, 1, 854.53),
	(21, 13, 1, 1, 440.10),
	(22, 14, 4, 1, 12.50),
	(23, 15, 6, 1, 173.16),
	(24, 16, 13, 1, 3.84);

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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table project.orders: ~16 rows (approximately)
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
	(10, 2, 440.10, 'completed', '2026-03-06 19:38:45'),
	(11, 2, 440.10, 'completed', '2026-03-19 10:25:34'),
	(12, 2, 12.50, 'completed', '2026-03-31 12:05:36'),
	(13, 2, 1294.62, 'completed', '2026-04-03 11:59:15'),
	(14, 2, 12.50, 'completed', '2026-04-07 12:16:11'),
	(15, 2, 173.16, 'completed', '2026-04-07 12:23:18'),
	(16, 2, 3.84, 'completed', '2026-04-07 12:28:42');

-- Dumping structure for table project.product_category_map
CREATE TABLE IF NOT EXISTS `product_category_map` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `product_category_map_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_category_map_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project.product_category_map: ~12 rows (approximately)
INSERT INTO `product_category_map` (`product_id`, `category_id`) VALUES
	(1, 1),
	(2, 1),
	(3, 3),
	(4, 2),
	(6, 4),
	(7, 4),
	(8, 3),
	(9, 2),
	(10, 2),
	(11, 2),
	(12, 1),
	(12, 2);

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project.products: ~12 rows (approximately)
INSERT INTO `products` (`id`, `name`, `price`, `image_url`, `status`, `discount`, `description`, `stock`) VALUES
	(1, 'Smartphonexx', 500.11, '/public/assets/img/steam.png', 'available', 12.00, 'yes ', 112),
	(2, 'Laptop', 899.50, '/public/assets/img/steam.png', 'available', 5.00, 'asdfg', 117),
	(3, 'T-shirt', 19.99, '/public/assets/img/steam.png', 'available', 0.00, 'asdfgh', 120),
	(4, 'Novel Book', 12.50, '/public/assets/img/steam.png', 'available', 0.00, 'asdfghj', 19),
	(6, 'BOBA', 222.00, '/public/assets/img/steam.png', 'available', 22.00, 'MEGA BOBA asdf', 1),
	(7, 'Dota 3 ', 22.00, '/public/assets/img/steam.png', 'available', 5.00, 'Yest spam productssss', 30),
	(8, '2', 2.00, '/public/assets/img/steam.png', 'available', 2.00, '2', 2),
	(9, '3', 3.00, '/public/assets/img/steam.png', 'available', 3.00, '3', 3),
	(10, '4', 4.00, '/public/assets/img/steam.png', 'available', 4.00, '4', 4),
	(11, '5', 5.00, '/public/assets/img/steam.png', 'available', 5.00, '5', 5),
	(12, '6', 6.00, '/public/assets/img/steam.png', 'available', 6.00, '6', 6),
	(13, 'to dich', 4.00, '/public/assets/img/steam.png', 'unavailable', 4.00, 'ydalil categoria', 3);

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project.users: ~4 rows (approximately)
INSERT INTO `users` (`id`, `email`, `password_hash`, `first_name`, `is_admin`, `created_at`) VALUES
	(1, 'haker@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$WjJ2dmI1aUV5Vm1PRjV2bQ$LWSO9X2zFP7vD+S0IZwHwhMu692goTeIsw9agpqt4GI', 'caster', 0, '2026-02-24 11:51:43'),
	(2, 'admin@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$eHJJZG5zLnRYdmVkYi93Sw$tKueDAb7TE7uHYJWps07IMJa5ZuBawN90xFM48x+BPo', 'adminka', 1, '2026-03-01 19:25:57'),
	(3, 'admin@gmail.c', '$argon2id$v=19$m=65536,t=4,p=1$OVZKVGExbWxBdWpLOS5oVQ$fKGQ4fOzG/iHgYGiXo8OBTbRNHGL2Tz1pgoEG5km3gY', 'adminka2', 0, '2026-03-31 12:30:44'),
	(4, 'admin@gmaa.a', '$argon2id$v=19$m=65536,t=4,p=1$T0J2MGl1QXR5Nkt0cXdEag$0BxDgJ6NNGARcF9A1yuvkRg1wOb4TbVvLTyP+sjqZVM', 'admin', 0, '2026-03-31 12:31:26');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
