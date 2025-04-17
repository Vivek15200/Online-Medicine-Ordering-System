-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 16, 2025 at 07:24 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medicine_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$N0AayFJPTk9g2mrQQKicw.xz6iipNdIjRzWRU4k7Y1tpx02s1qN5W', '2025-04-16 13:56:40');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(1, 1, 1, 2, '2025-04-16 04:48:37'),
(2, 2, 3, 1, '2025-04-16 04:48:37'),
(21, 8, 3, 1, '2025-04-16 16:34:58');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Pain Relievers', 'Medicines for pain relief like headache, body ache, etc.'),
(2, 'Antibiotics', 'Used to treat bacterial infections.'),
(3, 'Cough & Cold', 'Relief for cough, cold, and flu symptoms.'),
(4, 'Vitamins & Supplements', 'Boost immunity and support daily nutrition.'),
(5, 'Skin Care', 'Creams, ointments, and lotions for skin conditions.'),
(6, 'Digestive Health', 'Medicines for acidity, indigestion, etc.');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Confirmed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `quantity`, `total_price`, `order_date`, `status`) VALUES
(1, 4, 4, 1, 80.00, '2025-04-16 09:42:41', 'Pending'),
(2, 4, 1, 1, 50.00, '2025-04-16 09:43:34', 'Pending'),
(3, 4, 1, 1, 50.00, '2025-04-16 09:45:06', 'Pending'),
(4, 4, 1, 1, 50.00, '2025-04-16 11:17:19', 'Pending'),
(5, 4, 5, 1, 120.00, '2025-04-16 11:17:19', 'Pending'),
(6, 8, 1, 2, 100.00, '2025-04-16 12:01:17', 'Pending'),
(7, 8, 1, 1, 50.00, '2025-04-16 12:07:56', 'Delivered'),
(8, 8, 2, 1, 30.00, '2025-04-16 12:09:23', 'Delivered');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock` int(11) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `created_at`, `stock`) VALUES
(1, 'Paracetamol', 'Used for fever and pain relief', 50.00, 'paracetamol.jpg', '2025-04-16 04:48:37', 10),
(2, 'Vitamin C', 'Boosts immune system', 30.00, 'vitamin_c.jpg', '2025-04-16 04:48:37', 10),
(3, 'Amoxicillin', 'Antibiotic for bacterial infections', 120.00, 'amoxicillin.jpg', '2025-04-16 04:48:37', 10),
(4, 'Cough Syrup', 'Relieves cough and throat irritation', 80.00, 'cough_syrup.jpg', '2025-04-16 04:48:37', 10),
(5, 'Boost', 'Energy drink for strength and stamina.', 120.00, 'boost.jpg', '2025-04-16 10:33:14', 50),
(7, 'Metformin', 'Used to manage type 2 diabetes.', 150.00, 'metformin.jpg', '2025-04-16 10:53:22', 120),
(8, 'Cofsils', NULL, 150.00, 'medicated_cough.jpg', '2025-04-16 17:22:53', 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `address`, `password`, `created_at`) VALUES
(1, 'Alice Sharma', 'alice@example.com', NULL, '$2y$10$examplehashvalue1234567890abcd', '2025-04-16 04:48:37'),
(2, 'Bob Singh', 'bob@example.com', NULL, '$2y$10$examplehashvalue0987654321efgh', '2025-04-16 04:48:37'),
(3, 'Vivek', 'vivekrathod1520@gmail.com', 'Saswad', '$2y$10$KFArx3sQcRUY168NToXnQ.NOYKSftBTISDLNXiYh1SGAq5cdjuoJK', '2025-04-16 05:05:02'),
(4, 'Shubham', 'shubh0603@gmail.com', 'Majari BK', '$2y$10$PoQ4jyQqkkPNl73GkeRTNOduxLn/u7gbiiAYnaV.ThIlYYF3WRC5K', '2025-04-16 10:06:53'),
(8, 'tata', 'tata@gmail.com', 'wedfgh', '$2y$10$/WbHZnpv9QGQ2BuEBjN7JeaI8iflbR4PGUyYJfy7BluvttshnVaSm', '2025-04-16 15:02:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
