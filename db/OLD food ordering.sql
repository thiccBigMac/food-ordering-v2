-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 07, 2025 at 07:58 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.10

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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `email`) VALUES
(1, 'admin', '$2a$12$9SyVecE/bp13JpaOcI/KSu./7kTKgKCYpDR8WmQX/AEFe1htM/ZaW', 'admin@gmail.com');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `codes`
--

CREATE TABLE `codes` (
  `id` int(11) NOT NULL,
  `code` text NOT NULL,
  `uses` int(11) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `discount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `codes`
--

INSERT INTO `codes` (`id`, `code`, `uses`, `expiry_date`, `discount`) VALUES
(14, 'MIshan', 1, '2025-08-08 22:00:00', 10);

-- --------------------------------------------------------

--
-- Table structure for table `code_usages`
--

CREATE TABLE `code_usages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code_id` int(11) NOT NULL,
  `used_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `user_id`, `message`) VALUES
(1, 3, 'This is awesome');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(6,2) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `image`) VALUES
(10, 'sdfaasd', 'fasdfasdf', '9999.99', 'menu_688a5508164de2.26030093.jpg'),
(11, 'asdfasdf', 'asdfasd', '1132.00', 'menu_688a550d8bb393.51833016.jpg'),
(12, 'asdfasd', 'fasdfasd', '9999.99', 'menu_688a55159289c0.58277258.jpg'),
(14, 'xfs', 'dfasdf', '9999.99', 'menu_688a6ab23196b0.74468976.png'),
(15, 'sadfasd', 'asdfasd', '9999.99', 'menu_688a6ab82dd8c7.69001489.jpg');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `shipping_address`, `payment_method`, `status`, `created_at`) VALUES
(1, 3, '584.00', 'Hattiban,lalitpur', 'cash_on_delivery', 'cancelled', '2025-07-30 18:30:25'),
(2, 3, '21225.98', 'fgsdf', 'cash_on_delivery', 'cancelled', '2025-07-30 19:10:37'),
(3, 3, '220.00', 'Hattiban', 'cash_on_delivery', 'cancelled', '2025-08-05 14:56:16'),
(4, 3, '220.00', 'hattiban', 'cash_on_delivery', 'cancelled', '2025-08-05 15:05:27'),
(5, 3, '1250.00', 'Hattiban', 'cash_on_delivery', 'completed', '2025-08-07 08:17:47'),
(6, 3, '250.00', 'sfdfsa', 'cash_on_delivery', 'pending', '2025-08-07 14:56:12'),
(7, 3, '250.00', 'asdfasdf', 'cash_on_delivery', 'pending', '2025-08-07 14:56:35'),
(8, 3, '77.50', 'Hattiban', 'cash_on_delivery', 'completed', '2025-08-07 15:59:11'),
(9, 3, '77.50', 'hattiban', 'cash_on_delivery', 'completed', '2025-08-07 16:00:07'),
(12, 3, '9999.99', 'hattiban', 'cash_on_delivery', 'pending', '2025-08-07 17:15:59'),
(13, 3, '9999.99', 'sd', 'cash_on_delivery', 'pending', '2025-08-07 17:16:44');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`) VALUES
(1, 1, 2, 1, '250.00'),
(2, 1, 3, 1, '240.00'),
(3, 1, 4, 1, '94.00'),
(4, 2, 4, 1, '94.00'),
(5, 2, 10, 1, '9999.99'),
(6, 2, 11, 1, '1132.00'),
(7, 2, 12, 1, '9999.99'),
(8, 3, 2, 1, '250.00'),
(9, 4, 2, 1, '250.00'),
(10, 5, 2, 5, '250.00'),
(11, 6, 2, 1, '250.00'),
(12, 7, 2, 1, '250.00'),
(13, 8, 2, 1, '250.00'),
(14, 9, 2, 1, '250.00'),
(15, 10, 2, 1, '250.00'),
(16, 11, 2, 1, '250.00'),
(17, 12, 10, 1, '9999.99'),
(18, 13, 12, 1, '9999.99');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(12, 'afsd adsfas', 'mishanshah222@gmail.com', '$2y$10$kxVh7XQC7ZNRMk5u6QXyJebIKpwRo0NFReejDgKtpQ.EaBiUj.fCO', '9812312322');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

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
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
