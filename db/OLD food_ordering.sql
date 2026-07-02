-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 21, 2026 at 07:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `food_ordering`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `menu_id` int(255) NOT NULL,
  `quantity` int(255) NOT NULL,
  `checkout` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `codes`
--

CREATE TABLE `codes` (
  `id` int(11) NOT NULL,
  `code` text NOT NULL,
  `uses` int(11) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `discount` int(11) NOT NULL,
  `assigned_user_id` int(11) DEFAULT NULL,
  `restaurant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `codes`
--

INSERT INTO `codes` (`id`, `code`, `uses`, `expiry_date`, `discount`, `assigned_user_id`, `restaurant_id`) VALUES
(14, 'MIshan', 1, '2025-08-08 22:00:00', 10, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `code_usages`
--

CREATE TABLE `code_usages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code_id` int(11) NOT NULL,
  `used_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `code_usages`
--

INSERT INTO `code_usages` (`id`, `user_id`, `code_id`, `used_at`) VALUES
(2, 3, 14, '2025-08-07 22:01:00');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `user_id`, `message`) VALUES
(1, 3, 'This is awesome');

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_rules`
--

CREATE TABLE `loyalty_rules` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `type` enum('amount','orders') NOT NULL,
  `threshold` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loyalty_rules`
--

INSERT INTO `loyalty_rules` (`id`, `restaurant_id`, `type`, `threshold`, `discount`, `active`) VALUES
(1, 4, 'amount', 4000, 15, 1);

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(6,2) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `image`) VALUES
(16, 4, 'pizza', 'cheese pizza', 1000.00, 'menu_6a378aef90fdf0.03139678.jpg'),
(17, 5, 'burger', 'cheese', 500.00, 'menu_6a378f1cbfd839.06381868.png');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_address` text NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `restaurant_id` int(11) NOT NULL DEFAULT 1,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `distance_km` decimal(6,2) DEFAULT NULL,
  `estimated_minutes` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `shipping_address`, `payment_method`, `status`, `created_at`, `restaurant_id`, `latitude`, `longitude`, `distance_km`, `estimated_minutes`) VALUES
(14, 13, 5000.00, 'asdasd', 'cash_on_delivery', 'pending', '2026-06-21 06:57:41', 1, NULL, NULL, NULL, NULL),
(19, 13, 1500.00, 'asd', 'cash_on_delivery', 'pending', '2026-06-21 08:46:01', 5, NULL, NULL, NULL, NULL),
(20, 13, 1000.00, 'fast', 'cash_on_delivery', 'completed', '2026-06-21 14:12:25', 4, NULL, NULL, NULL, NULL),
(21, 13, 2000.00, 'asd', 'cash_on_delivery', 'completed', '2026-06-21 14:50:46', 4, NULL, NULL, NULL, NULL),
(22, 13, 500.00, 'sdf', 'cash_on_delivery', 'pending', '2026-06-21 14:58:42', 5, NULL, NULL, NULL, NULL),
(23, 13, 1500.00, 'chadai', 'cash_on_delivery', 'pending', '2026-06-21 15:02:06', 5, NULL, NULL, NULL, NULL),
(24, 13, 5000.00, 'asd', 'cash_on_delivery', 'pending', '2026-06-21 16:19:09', 4, NULL, NULL, NULL, NULL),
(25, 13, 4250.00, 'just the road', 'cash_on_delivery', 'pending', '2026-06-21 16:46:58', 4, NULL, NULL, NULL, NULL),
(26, 13, 1000.00, 'just around the corner', 'cash_on_delivery', 'pending', '2026-06-21 16:54:12', 4, 27.70707310, 85.31985164, 0.59, 12),
(27, 14, 6800.00, 'here', 'cash_on_delivery', 'completed', '2026-06-21 17:03:25', 4, 27.67794537, 85.37280917, 5.82, 28);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`) VALUES
(1, 1, 2, 1, 250.00),
(2, 1, 3, 1, 240.00),
(3, 1, 4, 1, 94.00),
(4, 2, 4, 1, 94.00),
(5, 2, 10, 1, 9999.99),
(6, 2, 11, 1, 1132.00),
(7, 2, 12, 1, 9999.99),
(8, 3, 2, 1, 250.00),
(9, 4, 2, 1, 250.00),
(10, 5, 2, 5, 250.00),
(11, 6, 2, 1, 250.00),
(12, 7, 2, 1, 250.00),
(13, 8, 2, 1, 250.00),
(14, 9, 2, 1, 250.00),
(15, 10, 2, 1, 250.00),
(16, 11, 2, 1, 250.00),
(17, 12, 10, 1, 9999.99),
(18, 13, 12, 1, 9999.99),
(19, 14, 16, 5, 1000.00),
(20, 15, 16, 1, 1000.00),
(21, 16, 16, 1, 1000.00),
(22, 17, 16, 4, 1000.00),
(23, 18, 16, 3, 1000.00),
(24, 19, 17, 3, 500.00),
(25, 20, 16, 1, 1000.00),
(26, 21, 16, 2, 1000.00),
(27, 22, 17, 1, 500.00),
(28, 23, 17, 3, 500.00),
(29, 24, 16, 5, 1000.00),
(30, 25, 16, 5, 1000.00),
(31, 26, 16, 1, 1000.00),
(32, 27, 16, 8, 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `username`, `password`, `email`, `image`, `latitude`, `longitude`) VALUES
(4, 'Pizza Palace', '$2y$10$PyEChHswH6fiPlmDA5NUQutgZfGSIKEq3WlZLzAMlb9nZWRMXLMnq', 'pizza@gmail.com', 'restaurant_6a37f0c7ef7001.97815277.jpg', 27.70915684, 85.32535592),
(5, 'Burger house', '$2y$10$o65YwxRcmmSsNkhk4XplleUL.Q8qp4SR98dmuUmVSrRDvVb9pQXzq', 'burger@gmail.com', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_admins`
--

CREATE TABLE `system_admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_admins`
--

INSERT INTO `system_admins` (`id`, `username`, `email`, `password`) VALUES
(1, 'superadmin', 'admin@gmail.com', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `contact`) VALUES
(3, 'Mishan Shah', 'mishanshah2@gmail.com', '$2y$10$NvHrQ5UAbEOkPYZuqU.vfu6WzTE5A8MfSGo.AtE2yXqxAe7UXE8/.', '9861359341'),
(4, 'Aman Bajracharya', 'amanbajracharya@gmail.com', '$2y$10$KYQ8X1ou6Rlg2Z8trHkNy.dA5SuA8Rgx2sxevkNSqFMDJV/B3AIaW', '9812345678'),
(5, 'Prince  Bikram Shah`', 'princebikram@2066gmail.com', '$2y$10$2G.DgELbJ7lK/UD95bjh0.L775TRljVbkTHKuxKElJi1eNYBSA6Yy', '9812345678'),
(6, 'John  Karki', 'johnkarki@gmail.com', '$2y$10$CaQaixTKMmZnlpgc1EQZ0e4lWpRhfSxqMqQT.BdgkrrigZUG8HU8e', '9812345678'),
(7, 'John  Karki', 'karkijohn@gmail.com', '$2y$10$PbQhGWCm5fxQx8Jb93E/BuU7fjlS0FFj5utq3WxrN5RunWeHUVdRC', '9845872153'),
(8, 'Mishan Shah', 'mishanshah12@gmail.com', '$2y$10$dCONxFLTGl6Je2UleAPK2.YlVPS8I/uSZJsBGC9Rl5bb7r3L0T236', '9812345678'),
(9, 'Mishan Shah', 'mishanshah112@gmail.com', '$2y$10$S346DqiVUJwW2EkZANY6Uuqujq7IqR95qIkclJ7CaxDBa56A4iRWe', '9812345678'),
(10, 'Mishan Shah', 'mishanshah1112@gmail.com', '$2y$10$6dC1yAEQNIoYGIGFM2IgnuIHcO.kQuFa2IDb03k3qLGMFWvqDjmkC', '9812345678'),
(11, 'sdfa asdf', 'dasfa2@gmail.com', '$2y$10$Co7u/JKNmhOlwClaiY9Pmu2tUG8u/XI6o33b.MbzKqaPCQ/Hp80p2', '9861359341'),
(12, 'afsd adsfas', 'mishanshah222@gmail.com', '$2y$10$kxVh7XQC7ZNRMk5u6QXyJebIKpwRo0NFReejDgKtpQ.EaBiUj.fCO', '9812312322'),
(13, 'suju suju', 'suju@gmail.com', '$2y$10$dMTMfsJchsxnK0idulisLOWWwKHhcDutKRJEIX0SWtVD8anFysn6K', '9841209997'),
(14, 'aman maharjan', 'amanmaharjan@gmail.com', '$2y$10$EZGbzfDCVTwEsQ2kP1TsdOB9oLuBjR4Cr9888u8bYiZH4dgLW.5qy', '9863222789');

-- --------------------------------------------------------

--
-- Table structure for table `user_loyalty_progress`
--

CREATE TABLE `user_loyalty_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `total_spent_since_last_reward` decimal(10,2) NOT NULL DEFAULT 0.00,
  `orders_since_last_reward` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `codes`
--
ALTER TABLE `codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `code_usages`
--
ALTER TABLE `code_usages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`code_id`),
  ADD KEY `code_id` (`code_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loyalty_rules`
--
ALTER TABLE `loyalty_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_restaurant2` (`restaurant_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_admins`
--
ALTER TABLE `system_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_loyalty_progress`
--
ALTER TABLE `user_loyalty_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_restaurant` (`user_id`,`restaurant_id`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `codes`
--
ALTER TABLE `codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `code_usages`
--
ALTER TABLE `code_usages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `loyalty_rules`
--
ALTER TABLE `loyalty_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `system_admins`
--
ALTER TABLE `system_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_loyalty_progress`
--
ALTER TABLE `user_loyalty_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `menu_id` FOREIGN KEY (`menu_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `code_usages`
--
ALTER TABLE `code_usages`
  ADD CONSTRAINT `code_usages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `code_usages_ibfk_2` FOREIGN KEY (`code_id`) REFERENCES `codes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `fk_restaurant2` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
