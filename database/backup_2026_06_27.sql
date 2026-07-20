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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project.categories: ~5 rows (approximately)
INSERT INTO `categories` (`id`, `name`) VALUES
	(1, 'Electronics'),
	(2, 'Books_2'),
	(3, 'Clothing'),
	(4, 'Dota2'),
	(6, '1');

-- Dumping structure for table project.comments
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_comment_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project.comments: ~2 rows (approximately)
INSERT INTO `comments` (`id`, `product_id`, `user_id`, `content`, `status`, `created_at`) VALUES
	(1, 15, 2, 'oh yee first comm xd', 'approved', '2026-05-04 18:47:28'),
	(2, 15, 2, 'asdf', 'approved', '2026-05-05 21:27:49');

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
  CONSTRAINT `fk_order_items_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `fk_order_items_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table project.order_items: ~31 rows (approximately)
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
	(24, 16, 13, 1, 3.84),
	(25, 17, 11, 1, 4.75),
	(26, 18, 12, 1, 5.64),
	(27, 19, 12, 1, 5.64),
	(28, 20, 12, 1, 5.64),
	(29, 21, 12, 1, 5.64),
	(30, 22, 12, 1, 5.64),
	(31, 23, 12, 1, 5.64),
	(32, 24, 7, 1, 20.90),
	(33, 25, 11, 1, 4.75),
	(34, 26, 3, 1, 19.99),
	(35, 27, 14, 1, 12.00),
	(36, 28, 14, 9, 12.00),
	(37, 29, 15, 1, 119.31),
	(38, 30, 15, 1, 119.31);

-- Dumping structure for table project.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_orders_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table project.orders: ~26 rows (approximately)
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
	(14, 2, 12.50, 'pending', '2026-04-07 12:16:11'),
	(15, 2, 173.16, 'completed', '2026-04-07 12:23:18'),
	(16, 2, 3.84, 'completed', '2026-04-07 12:28:42'),
	(17, 5, 4.75, 'pending', '2026-04-09 13:13:17'),
	(18, 5, 5.64, 'cancelled', '2026-04-09 13:13:25'),
	(19, 2, 5.64, 'pending', '2026-04-09 17:53:11'),
	(20, 2, 5.64, 'pending', '2026-04-09 17:53:13'),
	(21, 2, 5.64, 'pending', '2026-04-09 17:53:16'),
	(22, 2, 5.64, 'pending', '2026-04-09 17:53:19'),
	(23, 2, 5.64, 'pending', '2026-04-09 17:53:21'),
	(24, 2, 20.90, 'completed', '2026-04-09 17:53:24'),
	(25, 6, 4.75, 'pending', '2026-04-11 15:57:20'),
	(26, 6, 19.99, 'completed', '2026-04-11 15:57:25'),
	(27, 2, 12.00, 'pending', '2026-04-26 13:33:55'),
	(28, 2, 108.00, 'pending', '2026-04-26 13:34:24'),
	(29, 2, 119.31, 'pending', '2026-05-02 18:05:26'),
	(30, 2, 119.31, 'pending', '2026-05-05 21:27:43');

-- Dumping structure for table project.product_category_map
CREATE TABLE IF NOT EXISTS `product_category_map` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `product_category_map_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_category_map_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project.product_category_map: ~13 rows (approximately)
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
	(13, 6),
	(14, 4),
	(15, 2);

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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project.products: ~14 rows (approximately)
INSERT INTO `products` (`id`, `name`, `price`, `image_url`, `status`, `discount`, `description`, `stock`) VALUES
	(1, 'Smartphonexx', 500.11, '/public/assets/img/steam.png', 'available', 12.00, 'yes ', 112),
	(2, 'Laptop', 899.50, '/public/assets/img/steam.png', 'available', 5.00, 'asdfg', 117),
	(3, 'T-shirt', 19.99, '/public/assets/img/steam.png', 'available', 0.00, 'asdfgh', 119),
	(4, 'Novel Book', 12.50, '/public/assets/img/steam.png', 'available', 0.00, 'asdfghj', 19),
	(6, 'BOBA', 222.00, '/public/assets/img/steam.png', 'available', 22.00, 'MEGA BOBA asdf', 1),
	(7, 'Dota 3 ', 22.00, '/public/assets/img/steam.png', 'available', 5.00, 'Yest spam productssss', 29),
	(8, '2', 2.00, '/public/assets/img/steam.png', 'available', 2.00, '2', 2),
	(9, '3', 3.00, '/public/assets/img/steam.png', 'available', 3.00, '3', 3),
	(10, '4', 4.00, '/public/assets/img/steam.png', 'available', 4.00, '4', 4),
	(11, '5', 5.00, '/public/assets/img/steam.png', 'available', 5.00, '5', 3),
	(12, '6', 6.00, '/public/assets/img/steam.png', 'available', 5.00, '6', 0),
	(13, 'cccat baaaad', 4.00, 'prod_69ee16d79e8824.14542168.jpg', 'available', 0.00, 'worst cat...', 3),
	(14, 'bestCat', 12.00, 'prod_69ee0ea4669f76.32365850.jpg', 'available', 0.00, 'BEST CAT EVEERERERER', 4),
	(15, 'ddd', 123.00, 'prod_69ee19ba58a669.31622908.jpg', 'available', 3.00, 'asdf', 2);

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table project.users: ~5 rows (approximately)
INSERT INTO `users` (`id`, `email`, `password_hash`, `first_name`, `is_admin`, `created_at`) VALUES
	(1, 'haker@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$WjJ2dmI1aUV5Vm1PRjV2bQ$LWSO9X2zFP7vD+S0IZwHwhMu692goTeIsw9agpqt4GI', 'caster', 0, '2026-02-24 11:51:43'),
	(2, 'admin@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$eHJJZG5zLnRYdmVkYi93Sw$tKueDAb7TE7uHYJWps07IMJa5ZuBawN90xFM48x+BPo', 'adminka', 1, '2026-03-01 19:25:57'),
	(3, 'admin@gmail.c', 'a', 'adminka2', 0, '2026-03-31 12:30:44'),
	(5, 'admin2@gmail.com', '1', 'admin2@gmail.com', 0, '2026-04-09 13:13:11'),
	(6, 'admin3@gmail.com', '1', 'chykcha', 0, '2026-04-11 15:57:16');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
