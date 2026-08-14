-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 09, 2026 at 07:05 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travelease`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `full_name`, `email`, `password`, `phone`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@travelease.com', '$2y$10$sPxmK1eZniAvJA9I7GvLKOZZyvUSgYo744Felji97.bZzm8pjEGuu', NULL, 'active', '2026-04-10 00:33:22', '2025-12-15 06:08:24', '2026-04-09 19:03:22'),
(2, 'mahavirsingh', 'ms@travelease.com', '$2y$10$MIZ02e9DJjuyArHAL/kYjuuvzYZFKf21zYkoyBVzkfixZyoAuucxm', '7201807642', 'active', '2026-02-19 21:10:15', '2026-02-19 07:11:23', '2026-02-19 15:40:15');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `booking_type` enum('flight','hotel','train','bus','cruise','holiday') COLLATE utf8mb4_general_ci NOT NULL,
  `booking_reference` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `item_id` int NOT NULL,
  `details` text COLLATE utf8mb4_general_ci,
  `booking_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `travel_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `quantity` int DEFAULT '1',
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `payment_status` enum('paid','pending','failed') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `payment_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_reference` (`booking_reference`)
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `booking_type`, `booking_reference`, `item_id`, `details`, `booking_date`, `travel_date`, `return_date`, `check_in_date`, `check_out_date`, `quantity`, `total_amount`, `status`, `payment_status`, `payment_date`, `created_at`, `updated_at`) VALUES
(3, 7, 'flight', 'BOOK83191765448618', 0, 'Mumbai to Goa Flight', '2025-12-10 23:23:38', '2025-12-18', NULL, NULL, NULL, 1, 8500.00, 'confirmed', 'paid', NULL, '2025-12-12 06:37:54', '2025-12-12 06:37:54'),
(5, 5, 'flight', 'BOOK69831765529161', 0, 'Mumbai to Goa Flight', '2025-12-11 21:46:01', '2025-12-19', NULL, NULL, NULL, 1, 8500.00, 'confirmed', 'paid', NULL, '2025-12-12 06:37:54', '2025-12-12 06:37:54'),
(6, 5, 'hotel', 'BOOK16101765529161', 0, 'Goa Beach Resort - 3 Nights', '2025-12-11 21:46:01', '2025-12-19', NULL, NULL, NULL, 1, 12000.00, 'confirmed', 'paid', NULL, '2025-12-12 06:37:54', '2025-12-12 06:37:54'),
(7, 5, 'bus', 'BUS58761765561098', 2, 'Luxury Seater (LS205) - Delhi to Agra', '2025-12-12 06:38:18', '2026-01-01', NULL, NULL, NULL, 1, 500.00, 'confirmed', 'paid', NULL, '2025-12-12 06:38:18', '2025-12-12 06:38:26'),
(8, 5, 'train', 'TRAIN64761765561169', 1, 'Rajdhani Express (12951) - Mumbai Central to New Delhi', '2025-12-12 06:39:29', '2026-01-05', NULL, NULL, NULL, 1, 2500.00, 'confirmed', 'paid', NULL, '2025-12-12 06:39:29', '2025-12-12 06:39:32'),
(10, 5, 'flight', 'FLIGHT86551765561216', 1, 'Air India AI101 - Mumbai to Delhi', '2025-12-12 06:40:16', '2025-12-13', NULL, NULL, NULL, 2, 17000.00, 'confirmed', 'paid', NULL, '2025-12-12 06:40:16', '2025-12-12 06:40:20'),
(11, 5, 'hotel', 'HOTEL56201765561666', 2, 'Beach Resort Goa - Goa', '2025-12-12 06:47:46', '2025-12-12', NULL, '2025-12-13', '2025-12-15', 1, 7000.00, 'confirmed', 'paid', NULL, '2025-12-12 06:47:46', '2025-12-12 06:47:50'),
(12, 5, 'cruise', 'CRUISE50271765561823', 1, 'Royal Caribbean - Symphony of the Seas', '2025-12-12 06:50:23', '2026-02-05', NULL, NULL, NULL, 1, 45000.00, 'confirmed', 'paid', NULL, '2025-12-12 06:50:23', '2025-12-12 06:50:27'),
(13, 5, 'holiday', 'HOLIDAY1921176556350', 1, 'Goa Beach Paradise - Goa', '2025-12-12 07:18:20', '2026-11-10', NULL, NULL, NULL, 1, 15000.00, 'cancelled', '', NULL, '2025-12-12 07:18:20', '2025-12-15 04:15:56'),
(16, 8, 'holiday', 'HOLIDAY4326176556868', 24, 'Polish History & Culture - Poland', '2025-12-12 08:44:41', '2026-11-11', NULL, NULL, NULL, 5, 4999.95, 'confirmed', 'paid', NULL, '2025-12-12 08:44:41', '2025-12-12 08:44:48'),
(17, 8, 'holiday', 'HOLIDAY8561176556956', 12, 'Cherry Blossom Special - Japan', '2025-12-12 08:59:27', '2026-01-01', NULL, NULL, NULL, 10, 24999.90, 'confirmed', 'paid', NULL, '2025-12-12 08:59:27', '2025-12-12 08:59:34'),
(18, 7, 'cruise', 'CRUISE94941765617615', 2, 'Carnival Cruise - Mardi Gras', '2025-12-12 22:20:15', '2026-01-01', NULL, NULL, NULL, 1, 35000.00, 'confirmed', 'paid', NULL, '2025-12-12 22:20:15', '2025-12-12 22:20:34'),
(19, 7, 'holiday', 'HOLIDAY1485176562732', 30, 'Mumbai Darshan - City Tour Package - India', '2025-12-13 01:02:04', '2026-11-11', NULL, NULL, NULL, 5, 14995.00, 'confirmed', 'paid', NULL, '2025-12-13 01:02:04', '2025-12-13 01:02:07'),
(20, 5, 'holiday', 'HOLIDAY4142176571291', 29, 'Moscow & St. Petersburg Imperial Tour - Russia', '2025-12-14 06:18:34', '2027-11-11', NULL, NULL, NULL, 2, 4399.98, 'confirmed', 'paid', NULL, '2025-12-14 06:18:34', '2025-12-14 06:18:37'),
(21, 5, 'hotel', 'HOTEL39511765712972', 3, 'Heritage Palace Jaipur - Jaipur', '2025-12-14 06:19:32', '2025-12-14', NULL, '2025-12-15', '2025-12-16', 1, 4000.00, 'cancelled', '', NULL, '2025-12-14 06:19:32', '2025-12-14 06:52:01'),
(22, 5, 'hotel', 'HOTEL17571765715623', 2, 'Beach Resort Goa - Goa', '2025-12-14 07:03:43', '2025-12-14', NULL, '2025-12-15', '2025-12-16', 1, 3500.00, 'cancelled', '', NULL, '2025-12-14 07:03:43', '2025-12-14 07:04:40'),
(23, 5, 'hotel', 'HOTEL32441765715631', 2, 'Beach Resort Goa - Goa', '2025-12-14 07:03:51', '2025-12-14', NULL, '2025-12-15', '2025-12-16', 5, 17500.00, 'cancelled', '', NULL, '2025-12-14 07:03:51', '2025-12-14 07:04:28'),
(24, 5, 'holiday', 'HOLIDAY6878176580012', 26, 'K-Pop & Korean Culture - South Korea', '2025-12-15 06:32:06', '2027-01-01', NULL, NULL, NULL, 8, 13599.92, 'confirmed', 'paid', NULL, '2025-12-15 06:32:06', '2025-12-15 06:32:10'),
(25, 5, 'holiday', 'HOLIDAY5339176580678', 20, 'Dubai Luxury Experience - United Arab Emirates', '2025-12-15 08:23:09', '2026-05-05', NULL, NULL, NULL, 3, 5399.97, 'confirmed', 'paid', NULL, '2025-12-15 08:23:09', '2025-12-15 08:23:20'),
(26, 5, 'holiday', 'HOLIDAY3186176580969', 30, 'Mumbai Darshan - City Tour Package - India', '2025-12-15 09:11:35', '2026-01-01', NULL, NULL, NULL, 1, 2999.00, 'confirmed', 'paid', '2025-12-15 20:17:53', '2025-12-15 09:11:35', '2025-12-15 09:17:53'),
(27, 5, 'holiday', 'HOLIDAY7632176581008', 28, 'Egyptian Pyramids & Nile Cruise - Egypt', '2025-12-15 09:18:07', '2027-01-01', NULL, NULL, NULL, 1, 1799.99, 'confirmed', 'paid', '2025-12-15 20:18:26', '2025-12-15 09:18:07', '2025-12-15 09:18:26'),
(28, 5, 'holiday', 'HOLIDAY5853176581012', 28, 'Egyptian Pyramids & Nile Cruise - Egypt', '2025-12-15 09:18:45', '2027-01-01', NULL, NULL, NULL, 1, 1799.99, 'pending', 'pending', NULL, '2025-12-15 09:18:45', '2025-12-15 09:18:45'),
(29, 5, 'cruise', 'CRUISE40171765811429', 2, 'Carnival Cruise - Mardi Gras', '2025-12-15 09:40:29', '2026-01-01', NULL, NULL, NULL, 1, 35000.00, 'confirmed', 'paid', '2025-12-15 20:42:09', '2025-12-15 09:40:29', '2025-12-15 09:42:09'),
(30, 5, 'cruise', 'CRUISE32571765811564', 3, 'Norwegian Cruise - Wonder of the Seas', '2025-12-15 09:42:44', '2026-01-01', NULL, NULL, NULL, 1, 75000.00, 'confirmed', 'paid', '2025-12-15 20:43:03', '2025-12-15 09:42:44', '2025-12-15 09:43:03'),
(31, 5, 'cruise', 'CRUISE26861765811598', 1, 'Royal Caribbean - Symphony of the Seas', '2025-12-15 09:43:18', '2026-01-01', NULL, NULL, NULL, 1, 45000.00, 'confirmed', 'paid', '2025-12-15 20:43:25', '2025-12-15 09:43:18', '2025-12-15 09:43:25'),
(32, 5, 'cruise', 'CRUISE93641765811622', 1, 'Royal Caribbean - Symphony of the Seas', '2025-12-15 09:43:42', '2026-01-01', NULL, NULL, NULL, 1, 45000.00, 'confirmed', 'paid', '2025-12-15 20:44:06', '2025-12-15 09:43:42', '2025-12-15 09:44:06'),
(33, 5, 'holiday', 'HOLIDAY6167176581169', 25, 'Vietnam Highlights Tour - Vietnam', '2025-12-15 09:44:50', '2026-02-02', NULL, NULL, NULL, 1, 1499.99, 'confirmed', 'paid', '2025-12-15 20:49:04', '2025-12-15 09:44:50', '2025-12-15 09:49:04'),
(34, 5, 'hotel', 'HOTEL89481765811967', 2, 'Beach Resort Goa - Goa', '2025-12-15 09:49:27', '2025-12-15', NULL, '2025-12-16', '2025-12-17', 1, 7500.00, 'confirmed', 'paid', '2025-12-15 20:50:39', '2025-12-15 09:49:27', '2025-12-15 09:50:39'),
(35, 5, 'holiday', 'HOLIDAY4986176581239', 16, 'Hong Kong City Lights - Hong Kong (Special Administrative Region)', '2025-12-15 09:56:32', '2026-01-01', NULL, NULL, NULL, 3, 3899.97, 'confirmed', 'paid', '2025-12-15 20:57:49', '2025-12-15 09:56:32', '2025-12-15 09:57:49'),
(36, 5, 'holiday', 'HOLIDAY7726176581253', 11, 'German Castles & Beer Tour - Germany', '2025-12-15 09:58:50', '2025-12-30', NULL, NULL, NULL, 1, 1399.99, 'confirmed', 'paid', '2025-12-15 20:59:54', '2025-12-15 09:58:50', '2025-12-15 09:59:54'),
(37, 5, 'hotel', 'HOTEL57781765812659', 3, 'Heritage Palace Jaipur - Jaipur', '2025-12-15 10:00:59', '2025-12-15', NULL, '2025-12-16', '2025-12-17', 5, 20000.00, 'confirmed', 'paid', '2025-12-15 21:01:18', '2025-12-15 10:00:59', '2025-12-15 10:01:18'),
(39, 8, 'hotel', 'HOTEL24411765813216', 2, 'Beach Resort Goa - Goa', '2025-12-15 10:10:16', '2025-12-15', NULL, '2025-12-16', '2025-12-17', 2, 15000.00, 'confirmed', 'paid', '2025-12-15 21:10:37', '2025-12-15 10:10:16', '2025-12-15 10:10:37'),
(40, 8, 'holiday', 'HOLIDAY1889176581329', 29, 'Moscow & St. Petersburg Imperial Tour - Russia', '2025-12-15 10:11:32', '2026-02-01', NULL, NULL, NULL, 2, 498000.00, 'confirmed', 'paid', '2025-12-15 21:12:11', '2025-12-15 10:11:32', '2025-12-15 10:12:11'),
(52, 8, 'holiday', 'HOLIDAY4717176581907', 5, 'Spanish Fiesta Tour - Spain', '2025-12-15 11:47:56', '2026-03-01', NULL, NULL, NULL, 3, 4799.97, 'confirmed', 'paid', '2025-12-15 22:48:25', '2025-12-15 11:47:56', '2025-12-15 11:48:25'),
(53, 8, 'hotel', 'HOTEL93471765819169', 1, 'Grand Hotel Mumbai - Mumbai', '2025-12-15 11:49:29', '2025-12-15', NULL, '2025-12-16', '2025-12-17', 2, 10000.00, 'confirmed', 'paid', '2025-12-15 22:50:08', '2025-12-15 11:49:29', '2025-12-15 11:50:08'),
(54, 8, 'cruise', 'CRUISE55631765819234', 2, 'Carnival Cruise - Mardi Gras', '2025-12-15 11:50:34', '2026-02-05', NULL, NULL, NULL, 2, 70000.00, 'confirmed', 'paid', '2025-12-15 22:50:48', '2025-12-15 11:50:34', '2025-12-15 11:50:48'),
(55, 8, 'holiday', 'HOLIDAY8416176581927', 17, 'Saudi Heritage Experience - Saudi Arabia', '2025-12-15 11:51:17', '2026-01-05', NULL, NULL, NULL, 2, 3199.98, 'confirmed', 'paid', '2025-12-15 22:52:07', '2025-12-15 11:51:17', '2025-12-15 11:52:07'),
(56, 8, 'hotel', 'HOTEL10881765819344', 3, 'Heritage Palace Jaipur - Jaipur', '2025-12-15 11:52:24', '2025-12-15', NULL, '2025-12-16', '2025-12-17', 4, 16000.00, 'confirmed', 'paid', '2025-12-15 22:52:50', '2025-12-15 11:52:24', '2025-12-15 11:52:50'),
(57, 8, 'holiday', 'HOLIDAY9417176581944', 12, 'Cherry Blossom Special - Japan', '2025-12-15 11:54:05', '2026-03-05', NULL, NULL, NULL, 2, 4999.98, 'confirmed', 'paid', '2025-12-15 22:54:46', '2025-12-15 11:54:05', '2025-12-15 11:54:46'),
(58, 8, 'holiday', 'HOLIDAY9083176582020', 13, 'Greek Island Hopping - Greece', '2025-12-15 12:06:49', '2026-05-01', NULL, NULL, NULL, 4, 6799.96, 'confirmed', 'paid', '2025-12-15 23:07:15', '2025-12-15 12:06:49', '2025-12-15 12:07:15'),
(59, 8, 'holiday', 'HOLIDAY6833176582025', 26, 'K-Pop & Korean Culture - South Korea', '2025-12-15 12:07:38', '2026-05-05', NULL, NULL, NULL, 1, 1699.99, 'confirmed', 'paid', '2025-12-15 23:07:54', '2025-12-15 12:07:38', '2025-12-15 12:07:54'),
(60, 8, 'flight', 'FLIGHT88821765820293', 4, 'Emirates EM125 - Abu Dhabi to Kolkata', '2025-12-15 12:08:13', '2026-03-02', NULL, NULL, NULL, 2, 43000.00, 'confirmed', 'paid', '2025-12-15 23:08:39', '2025-12-15 12:08:13', '2025-12-15 12:08:39'),
(61, 5, 'train', 'TRAIN50261765823107', 3, 'Duronto Express (12259) - Mumbai to Bangalore', '2025-12-15 12:55:07', '2026-08-02', NULL, NULL, NULL, 2, 3600.00, 'confirmed', 'paid', '2025-12-15 23:55:36', '2025-12-15 12:55:07', '2025-12-15 12:55:36'),
(62, 5, 'holiday', 'HOLIDAY3200176582319', 12, 'Cherry Blossom Special - Japan', '2025-12-15 12:56:36', '2026-08-05', NULL, NULL, NULL, 5, 12499.95, 'confirmed', 'paid', '2025-12-15 23:56:56', '2025-12-15 12:56:36', '2025-12-15 12:56:56'),
(63, 5, 'holiday', 'HOLIDAY1792176582325', 21, 'Australian Alpine Retreat - Australia', '2025-12-15 12:57:38', '2026-03-05', NULL, NULL, NULL, 3, 4499.97, 'confirmed', 'paid', '2025-12-15 23:57:47', '2025-12-15 12:57:38', '2025-12-15 12:57:47'),
(64, 5, 'holiday', 'HOLIDAY5830176582415', 24, 'Singapore Tech & Culture - Singapore', '2025-12-15 13:12:33', '2026-05-01', NULL, NULL, NULL, 2, 1999.98, 'confirmed', 'paid', '2025-12-16 00:13:02', '2025-12-15 13:12:33', '2025-12-15 13:13:02'),
(65, 5, 'holiday', 'HOLIDAY2347176582558', 4, 'Romantic Paris Getaway - France', '2025-12-15 13:36:21', '2026-03-01', NULL, NULL, NULL, 2, 3799.98, 'confirmed', 'paid', '2025-12-16 00:36:55', '2025-12-15 13:36:21', '2025-12-15 13:36:55'),
(66, 5, 'cruise', 'CRUISE49251765825801', 3, 'Norwegian Cruise - Wonder of the Seas', '2025-12-15 13:40:01', '2027-01-01', NULL, NULL, NULL, 1, 75000.00, 'confirmed', 'paid', '2025-12-16 00:40:26', '2025-12-15 13:40:01', '2025-12-15 13:40:26'),
(67, 1, 'holiday', 'HOLIDAY2135176588350', 29, 'Moscow & St. Petersburg Imperial Tour - Russia', '2025-12-16 11:11:48', '2026-02-05', NULL, NULL, NULL, 1, 249000.00, 'confirmed', 'paid', '2025-12-16 16:42:00', '2025-12-16 11:11:48', '2025-12-16 11:12:00'),
(68, 1, 'flight', 'FLIGHT94881765964624', 4, 'Emirates EM125 - Abu Dhabi to Kolkata', '2025-12-17 09:43:44', '2026-01-01', NULL, NULL, NULL, 1, 21500.00, 'confirmed', 'paid', '2025-12-17 15:14:06', '2025-12-17 09:43:44', '2025-12-17 09:44:06'),
(69, 1, 'holiday', 'HOLIDAY3157176598780', 3, 'Himachal Hill Station - India', '2025-12-17 16:10:05', '2026-01-11', NULL, NULL, NULL, 2, 44000.00, 'confirmed', 'paid', '2025-12-17 21:40:23', '2025-12-17 16:10:05', '2025-12-17 16:10:23'),
(70, 1, 'train', 'TRAIN84421765991969', 3, 'Duronto Express (12259) - Mumbai to Bangalore', '2025-12-17 17:19:29', '2026-02-01', NULL, NULL, NULL, 1, 1800.00, 'confirmed', 'paid', '2025-12-17 22:49:38', '2025-12-17 17:19:29', '2025-12-17 17:19:38'),
(71, 1, 'hotel', 'HOTEL83921765992574', 1, 'Grand Hotel Mumbai - Mumbai', '2025-12-17 17:29:34', '2025-12-17', NULL, '2025-12-18', '2025-12-21', 1, 15000.00, 'confirmed', 'paid', '2025-12-17 22:59:43', '2025-12-17 17:29:34', '2025-12-17 17:29:43'),
(72, 5, 'flight', 'FLIGHT59961766841053', 6, 'Vistara UK111 - Mumbai to Bangalore', '2025-12-27 13:10:53', '2026-03-02', NULL, NULL, NULL, 1, 5800.00, 'cancelled', '', '2025-12-27 18:41:12', '2025-12-27 13:10:53', '2025-12-27 13:11:45'),
(73, 5, 'hotel', 'HOTEL22571766841253', 6, 'Sabarmati Palace - Ahmedabad', '2025-12-27 13:14:13', '2025-12-27', NULL, '2025-12-28', '2025-12-30', 1, 9600.00, 'pending', 'pending', NULL, '2025-12-27 13:14:13', '2025-12-27 13:14:13'),
(74, 5, 'flight', 'FLIGHT97811766842730', 4, 'Emirates EM125 - Abu Dhabi to Kolkata', '2025-12-27 13:38:50', '2026-02-01', NULL, NULL, NULL, 1, 21500.00, 'pending', 'pending', NULL, '2025-12-27 13:38:50', '2025-12-27 13:38:50'),
(75, 5, 'flight', 'FLIGHT54721766912534', 2, 'IndiGo 6E205 - Mumbai to Goa', '2025-12-28 09:02:14', '2026-01-01', NULL, NULL, NULL, 1, 4500.00, 'confirmed', 'paid', '2025-12-28 14:32:28', '2025-12-28 09:02:14', '2025-12-28 09:02:28'),
(76, 5, 'cruise', 'CRUISE79301766914429', 2, 'Carnival Cruise - Mardi Gras', '2025-12-28 09:33:49', '2026-01-02', NULL, NULL, NULL, 2, 70000.00, 'confirmed', 'paid', '2025-12-28 15:03:57', '2025-12-28 09:33:49', '2025-12-28 09:33:57'),
(77, 5, 'cruise', 'CRUISE30191766914482', 3, 'Norwegian Cruise - Wonder of the Seas', '2025-12-28 09:34:42', '2026-01-11', NULL, NULL, NULL, 3, 225000.00, 'pending', 'pending', NULL, '2025-12-28 09:34:42', '2025-12-28 09:34:42'),
(78, 5, 'bus', 'BUS84481766914496', 3, 'Semi Sleeper AC (SS301) - Bangalore to Mysore', '2025-12-28 09:34:56', '2025-12-19', NULL, NULL, NULL, 1, 600.00, 'pending', 'pending', NULL, '2025-12-28 09:34:56', '2025-12-28 09:34:56'),
(79, 5, 'bus', 'BUS78071766914502', 3, 'Semi Sleeper AC (SS301) - Bangalore to Mysore', '2025-12-28 09:35:02', '2025-12-19', NULL, NULL, NULL, 11, 6600.00, 'pending', 'pending', NULL, '2025-12-28 09:35:02', '2025-12-28 09:35:02'),
(80, 5, 'bus', 'BUS60761766914505', 3, 'Semi Sleeper AC (SS301) - Bangalore to Mysore', '2025-12-28 09:35:05', '2025-12-19', NULL, NULL, NULL, 11, 6600.00, 'pending', 'pending', NULL, '2025-12-28 09:35:05', '2025-12-28 09:35:05'),
(81, 5, 'hotel', 'HOTEL82071766914524', 7, 'Gujarat Elegance - Ahmedabad', '2025-12-28 09:35:24', '2025-12-28', NULL, '2025-12-29', '2025-12-31', 2, 28800.00, 'pending', 'pending', NULL, '2025-12-28 09:35:24', '2025-12-28 09:35:24'),
(82, 5, 'train', 'TRAIN53491766914670', 1, 'Rajdhani Express (12951) - Mumbai Central to New Delhi', '2025-12-28 09:37:50', '2025-12-19', NULL, NULL, NULL, 1, 2500.00, 'pending', 'pending', NULL, '2025-12-28 09:37:50', '2025-12-28 09:37:50'),
(83, 5, 'flight', 'FLIGHT36941766915279', 3, 'SpiceJet SG301 - Delhi to Bangalore', '2025-12-28 09:47:59', '2026-01-03', NULL, NULL, NULL, 1, 7000.00, 'pending', 'pending', NULL, '2025-12-28 09:47:59', '2025-12-28 09:47:59'),
(84, 5, 'flight', 'FLIGHT10941766915287', 3, 'SpiceJet SG301 - Delhi to Bangalore', '2025-12-28 09:48:07', '2026-01-03', NULL, NULL, NULL, 1, 7000.00, 'confirmed', 'paid', '2025-12-28 15:19:07', '2025-12-28 09:48:07', '2025-12-28 09:49:07'),
(85, 5, 'flight', 'FLIGHT54061766916925', 1, 'Air India AI101 - Mumbai to Delhi', '2025-12-28 10:15:25', '2026-01-06', NULL, NULL, NULL, 2, 19000.00, 'pending', 'pending', NULL, '2025-12-28 10:15:25', '2025-12-28 10:15:25'),
(86, 5, 'train', 'TRAIN64361766918002', 3, 'Duronto Express (12259) - Mumbai to Bangalore', '2025-12-28 10:33:22', '2025-12-19', NULL, NULL, NULL, 1, 1800.00, 'confirmed', 'paid', '2025-12-28 16:03:32', '2025-12-28 10:33:22', '2025-12-28 10:33:32'),
(87, 5, 'holiday', 'HOLIDAY5272176691953', 29, 'Moscow & St. Petersburg Imperial Tour - Russia', '2025-12-28 10:58:57', '2026-02-01', NULL, NULL, NULL, 1, 249000.00, 'pending', 'pending', NULL, '2025-12-28 10:58:57', '2025-12-28 10:58:57'),
(88, 5, 'holiday', 'HOLIDAY9136176692000', 30, 'Mumbai Darshan - City Tour Package - India', '2025-12-28 11:06:41', '2026-12-29', NULL, NULL, NULL, 1, 2999.00, 'confirmed', 'paid', '2025-12-28 16:36:51', '2025-12-28 11:06:41', '2025-12-28 11:06:51'),
(89, 5, 'holiday', 'HOLIDAY8737176692020', 20, 'Dubai Luxury Experience - United Arab Emirates', '2025-12-28 11:10:05', '2026-01-02', NULL, NULL, NULL, 1, 1799.99, 'confirmed', 'paid', '2025-12-28 16:40:14', '2025-12-28 11:10:05', '2025-12-28 11:10:14'),
(90, 5, 'holiday', 'HOLIDAY6978176692036', 21, 'Australian Picnics & Barbeques - Australia', '2025-12-28 11:12:45', '2026-02-01', NULL, NULL, NULL, 1, 1499.99, 'confirmed', 'paid', '2025-12-28 16:43:07', '2025-12-28 11:12:45', '2025-12-28 11:13:07'),
(91, 5, 'hotel', 'HOTEL64871766920403', 7, 'Gujarat Elegance - Ahmedabad', '2025-12-28 11:13:23', '2025-12-28', NULL, '2025-12-29', '2025-12-30', 1, 7200.00, 'confirmed', 'paid', '2025-12-28 16:45:41', '2025-12-28 11:13:23', '2025-12-28 11:15:41'),
(92, 5, 'holiday', 'HOLIDAY1464176692065', 30, 'Mumbai Darshan - City Tour Package - India', '2025-12-28 11:17:35', '2026-03-24', NULL, NULL, NULL, 1, 2999.00, 'pending', 'pending', NULL, '2025-12-28 11:17:35', '2025-12-28 11:17:35'),
(93, 5, 'holiday', 'HOLIDAY9851176692781', 30, 'Mumbai Darshan - City Tour Package - India', '2025-12-28 13:16:54', '2026-02-02', '2026-02-04', NULL, NULL, 1, 2999.00, 'confirmed', 'paid', '2025-12-28 18:47:23', '2025-12-28 13:16:54', '2025-12-28 13:17:23'),
(94, 5, 'holiday', 'HOLIDAY2794176692816', 29, 'Moscow & St. Petersburg Imperial Tour - Russia', '2025-12-28 13:22:42', '2026-08-05', '2026-08-14', NULL, NULL, 1, 249000.00, 'pending', 'pending', NULL, '2025-12-28 13:22:42', '2025-12-28 13:22:42'),
(95, 5, 'flight', 'FLIGHT24831766928547', 12, 'Vistara UK312 - Kolkata to Bangalore', '2025-12-28 13:29:07', '2025-12-29', NULL, NULL, NULL, 1, 6100.00, 'confirmed', 'paid', '2025-12-28 18:59:16', '2025-12-28 13:29:07', '2025-12-28 13:29:16'),
(96, 5, 'hotel', 'HOTEL71691766928610', 6, 'Sabarmati Palace - Ahmedabad', '2025-12-28 13:30:10', '2025-12-28', NULL, '2025-12-29', '2025-12-31', 1, 9600.00, 'confirmed', 'paid', '2025-12-28 19:00:19', '2025-12-28 13:30:10', '2025-12-28 13:30:19'),
(97, 5, 'train', 'TRAIN83731766928640', 3, 'Duronto Express (12259) - Mumbai to Bangalore', '2025-12-28 13:30:40', '2025-12-19', NULL, NULL, NULL, 1, 1800.00, 'confirmed', 'paid', '2025-12-28 19:00:46', '2025-12-28 13:30:40', '2025-12-28 13:30:46'),
(98, 5, 'bus', 'BUS38231766928671', 4, 'Ahmedabad Express (AE101) - Ahmedabad to Mumbai', '2025-12-28 13:31:11', '2025-12-20', NULL, NULL, NULL, 2, 1400.00, 'confirmed', 'paid', '2025-12-28 19:01:18', '2025-12-28 13:31:11', '2025-12-28 13:31:18'),
(99, 5, 'cruise', 'CRUISE47151766928710', 3, 'Norwegian Cruise - Wonder of the Seas', '2025-12-28 13:31:50', '2026-01-11', '2026-01-21', NULL, NULL, 1, 75000.00, 'cancelled', '', '2025-12-28 19:02:01', '2025-12-28 13:31:50', '2025-12-30 15:54:02'),
(100, 5, 'holiday', 'HOLIDAY7936176692875', 14, 'Great Wall & Forbidden City - China', '2025-12-28 13:32:37', '2026-01-30', '2026-02-08', NULL, NULL, 1, 1899.99, 'cancelled', '', '2025-12-28 19:02:56', '2025-12-28 13:32:37', '2025-12-29 17:34:42'),
(101, 5, 'holiday', 'HOLIDAY9265176692920', 23, 'Dutch Tulip Festival - Netherlands', '2025-12-28 13:40:03', '2026-02-01', '2026-02-07', NULL, NULL, 1, 1299.99, 'cancelled', '', '2025-12-28 19:10:14', '2025-12-28 13:40:03', '2025-12-29 14:30:26'),
(102, 5, 'hotel', 'HOTEL16441767027826', 12, 'Bangalore Royale - Bangalore', '2025-12-29 17:03:46', '2025-12-29', NULL, '2025-12-30', '2025-12-31', 1, 7200.00, 'cancelled', '', '2025-12-29 22:33:55', '2025-12-29 17:03:46', '2025-12-29 17:32:56'),
(103, 5, 'flight', 'FLIGHT34251767029920', 7, 'IndiGo 6E210 - Delhi to Mumbai', '2025-12-29 17:38:40', '2025-12-29', NULL, NULL, NULL, 1, 5400.00, 'cancelled', '', '2025-12-29 23:08:57', '2025-12-29 17:38:40', '2025-12-30 14:00:43'),
(104, 5, 'hotel', 'HOTEL45791767102959', 12, 'Bangalore Royale - Bangalore', '2025-12-30 13:55:59', '2025-12-30', NULL, '2025-12-31', '2026-02-01', 1, 230400.00, 'cancelled', 'pending', NULL, '2025-12-30 13:55:59', '2025-12-30 14:37:19'),
(105, 5, 'hotel', 'HOTEL89161767102974', 12, 'Bangalore Royale - Bangalore', '2025-12-30 13:56:14', '2025-12-30', NULL, '2025-12-31', '2026-01-02', 1, 14400.00, 'cancelled', '', '2025-12-30 19:26:33', '2025-12-30 13:56:14', '2025-12-30 13:59:56'),
(106, 5, 'holiday', 'HOLIDAY3582176710340', 27, 'Moroccan Desert Adventure - Morocco', '2025-12-30 14:03:20', '2026-01-02', '2026-01-10', NULL, NULL, 1, 1399.99, 'cancelled', '', '2025-12-30 19:33:30', '2025-12-30 14:03:20', '2025-12-30 14:21:11'),
(107, 5, 'hotel', 'HOTEL74211767104924', 7, 'Gujarat Elegance - Ahmedabad', '2025-12-30 14:28:44', '2025-12-30', NULL, '2025-12-31', '2026-01-03', 1, 21600.00, 'cancelled', '', '2025-12-30 19:58:51', '2025-12-30 14:28:44', '2025-12-30 14:31:54'),
(108, 5, 'holiday', 'HOLIDAY9136176710843', 19, 'Canadian Rockies Adventure - Canada', '2025-12-30 15:27:13', '2026-02-02', '2026-02-10', NULL, NULL, 1, 1999.99, 'cancelled', '', '2025-12-30 20:57:19', '2025-12-30 15:27:13', '2025-12-30 15:32:16'),
(109, 5, 'cruise', 'CRUISE12391767109078', 2, 'Carnival Cruise - Mardi Gras', '2025-12-30 15:37:58', '2026-01-02', '2026-01-07', NULL, NULL, 1, 35000.00, 'confirmed', 'paid', '2025-12-30 21:08:04', '2025-12-30 15:37:58', '2025-12-30 15:38:04'),
(110, 5, 'train', 'TRAIN11661767110390', 1, 'Rajdhani Express (12951) - Mumbai Central to New Delhi', '2025-12-30 15:59:50', '2025-12-19', NULL, NULL, NULL, 1, 2500.00, 'pending', 'pending', NULL, '2025-12-30 15:59:50', '2025-12-30 15:59:50'),
(111, 5, 'train', 'TRAIN31991767110425', 2, 'Shatabdi Express (12009) - Mumbai Central to Ahmedabad', '2025-12-30 16:00:25', '2025-12-19', NULL, NULL, NULL, 1, 1500.00, 'pending', 'pending', NULL, '2025-12-30 16:00:25', '2025-12-30 16:00:25'),
(112, 5, 'cruise', 'CRUISE86641767111149', 1, 'Royal Caribbean - Symphony of the Seas', '2025-12-30 16:12:29', '2025-12-26', '2026-01-02', NULL, NULL, 1, 45000.00, 'cancelled', '', '2025-12-30 21:42:35', '2025-12-30 16:12:29', '2026-01-22 15:48:54'),
(113, 5, 'cruise', 'CRUISE27901767111239', 3, 'Norwegian Cruise - Wonder of the Seas', '2025-12-30 16:13:59', '2026-01-11', '2026-01-21', NULL, NULL, 1, 75000.00, 'cancelled', 'pending', NULL, '2025-12-30 16:13:59', '2025-12-30 16:14:58'),
(114, 5, 'cruise', 'CRUISE85891767111282', 1, 'Royal Caribbean - Symphony of the Seas', '2025-12-30 16:14:42', '2025-12-26', '2026-01-02', NULL, NULL, 1, 45000.00, 'cancelled', '', '2025-12-30 21:44:47', '2025-12-30 16:14:42', '2025-12-30 16:28:37'),
(115, 5, 'flight', 'FLIGHT86151767111518', 14, 'IndiGo 6E410 - Bangalore to Mumbai', '2025-12-30 16:18:38', '2025-12-29', NULL, NULL, NULL, 1, 5200.00, 'confirmed', 'paid', '2025-12-30 21:48:44', '2025-12-30 16:18:38', '2025-12-30 16:18:44'),
(116, 5, 'flight', 'FLIGHT81491767111649', 5, 'Air India AI110 - Mumbai to Kolkata', '2025-12-30 16:20:49', '2025-12-29', NULL, NULL, NULL, 1, 6200.00, 'confirmed', 'paid', '2025-12-30 21:50:55', '2025-12-30 16:20:49', '2025-12-30 16:20:55'),
(117, 5, 'cruise', 'CRUISE59041767111963', 3, 'Norwegian Cruise - Wonder of the Seas', '2025-12-30 16:26:03', '2026-01-11', '2026-01-21', NULL, NULL, 1, 75000.00, 'cancelled', 'pending', NULL, '2025-12-30 16:26:03', '2025-12-30 16:40:38'),
(118, 5, 'cruise', 'CRUISE41911767113125', 1, 'Royal Caribbean - Symphony of the Seas', '2025-12-30 16:45:25', '2025-12-26', '2026-01-02', NULL, NULL, 1, 45000.00, 'cancelled', '', '2025-12-30 22:15:35', '2025-12-30 16:45:25', '2025-12-30 16:46:06'),
(119, 5, 'bus', 'BUS44971767113213', 2, 'Luxury Seater (LS205) - Delhi to Agra', '2025-12-30 16:46:53', '2025-12-19', NULL, NULL, NULL, 1, 500.00, 'confirmed', 'paid', '2025-12-30 22:17:00', '2025-12-30 16:46:53', '2025-12-30 16:47:00'),
(120, 5, 'flight', 'FLIGHT55501767204412', 5, 'Air India AI110 - Mumbai to Kolkata', '2025-12-31 18:06:52', '2025-12-29', NULL, NULL, NULL, 1, 6200.00, 'confirmed', 'paid', '2025-12-31 23:36:59', '2025-12-31 18:06:52', '2025-12-31 18:06:59'),
(121, 5, 'bus', 'BUS62341767204621', 1, 'Volvo AC Sleeper (VH001) - Mumbai to Pune', '2025-12-31 18:10:21', '2025-12-19', NULL, NULL, NULL, 1, 800.00, 'cancelled', '', '2025-12-31 23:40:27', '2025-12-31 18:10:21', '2026-01-22 12:49:58'),
(122, 5, 'holiday', 'HOLIDAY4383176720832', 13, 'Greek Island Hopping - Greece', '2025-12-31 19:12:06', '2026-01-05', '2026-01-13', NULL, NULL, 1, 1699.99, 'confirmed', 'paid', '2026-01-01 00:42:12', '2025-12-31 19:12:06', '2025-12-31 19:12:12'),
(123, 9, 'holiday', 'HOLIDAY7283176898465', 30, 'Mumbai Darshan - City Tour Package - India', '2026-01-21 08:37:35', '2026-02-26', '2026-02-28', NULL, NULL, 1, 7999.00, 'cancelled', '', '2026-01-21 14:07:41', '2026-01-21 08:37:35', '2026-01-21 08:38:50'),
(124, 11, 'holiday', 'HOLIDAY6210176898549', 30, 'Mumbai Darshan - City Tour Package - India', '2026-01-21 08:51:32', '2026-03-27', '2026-03-29', NULL, NULL, 1, 7999.00, 'cancelled', '', '2026-01-21 14:21:41', '2026-01-21 08:51:32', '2026-01-21 08:52:47'),
(125, 11, 'holiday', 'HOLIDAY8519176899297', 26, 'K-Pop & Korean Culture - South Korea', '2026-01-21 10:56:12', '2026-03-03', '2026-03-10', NULL, NULL, 1, 249000.00, 'confirmed', 'paid', '2026-01-21 16:26:18', '2026-01-21 10:56:12', '2026-01-21 10:56:18'),
(126, 1, 'holiday', 'HOLIDAY6242176907498', 30, 'Mumbai Darshan - City Tour Package - India', '2026-01-22 09:43:05', '2026-03-01', '2026-03-03', NULL, NULL, 1, 7999.00, 'cancelled', '', '2026-01-22 15:13:13', '2026-01-22 09:43:05', '2026-01-22 12:47:21'),
(127, 5, 'flight', 'FLIGHT67431769083935', 6, 'Vistara UK111 - Mumbai to Bangalore', '2026-01-22 12:12:15', '2026-01-31', NULL, NULL, NULL, 1, 5800.00, 'cancelled', '', '2026-01-22 17:45:40', '2026-01-22 12:12:15', '2026-01-22 12:46:29'),
(128, 5, 'hotel', 'HOTEL75671769084010', 7, 'Gujarat Elegance - Ahmedabad', '2026-01-22 12:13:30', '2026-01-22', NULL, '2026-01-22', '2026-01-24', 1, 14400.00, 'cancelled', 'pending', NULL, '2026-01-22 12:13:30', '2026-01-22 12:21:54'),
(129, 5, 'holiday', 'HOLIDAY6536176908654', 17, 'Saudi Heritage Experience - Saudi Arabia', '2026-01-22 12:55:43', '2026-02-02', '2026-02-08', NULL, NULL, 1, 149000.00, 'cancelled', '', '2026-01-22 18:26:42', '2026-01-22 12:55:43', '2026-01-22 13:04:11'),
(130, 5, 'bus', 'BUS51671769088372', 4, 'Ahmedabad Express (AE101) - Ahmedabad to Mumbai', '2026-01-22 13:26:12', '2026-01-31', NULL, NULL, NULL, 1, 700.00, 'confirmed', 'paid', '2026-01-22 18:56:18', '2026-01-22 13:26:12', '2026-01-22 13:26:18'),
(131, 11, 'flight', 'FLIGHT65561769096585', 14, 'IndiGo 6E410 - Bangalore to Mumbai', '2026-01-22 15:43:05', '2026-01-31', NULL, NULL, NULL, 1, 5200.00, 'cancelled', '', '2026-01-22 21:13:11', '2026-01-22 15:43:05', '2026-01-22 15:44:37'),
(132, 11, 'bus', 'BUS82661769097487', 17, 'Indore AC Seater (IS701) - Indore to Mumbai', '2026-01-22 15:58:07', '2026-01-31', NULL, NULL, NULL, 1, 800.00, 'cancelled', '', '2026-01-22 21:28:21', '2026-01-22 15:58:07', '2026-01-22 15:59:20'),
(133, 5, 'hotel', 'HOTEL14381769711183', 14, 'Chennai Serenity - Chennai', '2026-01-29 18:26:23', '2026-01-29', NULL, '2026-02-01', '2026-02-02', 1, 5600.00, 'pending', 'pending', NULL, '2026-01-29 18:26:23', '2026-01-29 18:26:23'),
(134, 5, 'hotel', 'HOTEL67371769711226', 14, 'Chennai Serenity - Chennai', '2026-01-29 18:27:06', '2026-01-29', NULL, '2026-02-01', '2026-02-02', 1, 5600.00, 'confirmed', 'paid', '2026-01-29 23:57:15', '2026-01-29 18:27:06', '2026-01-29 18:27:15'),
(135, 5, 'holiday', 'HOLIDAY3597177148474', 12, 'Cherry Blossom Special - Japan', '2026-02-19 07:05:45', '2026-02-23', '2026-03-05', NULL, NULL, 1, 349000.00, 'confirmed', 'paid', '2026-02-19 12:35:53', '2026-02-19 07:05:45', '2026-02-19 07:05:53'),
(136, 5, 'holiday', 'HOLIDAY5815177148549', 30, 'Mumbai Darshan - City Tour Package - India', '2026-02-19 07:18:13', '2026-04-01', '2026-04-03', NULL, NULL, 1, 7999.00, 'cancelled', '', '2026-02-19 12:48:20', '2026-02-19 07:18:13', '2026-02-19 07:20:18'),
(137, 12, 'flight', 'FLIGHT77541771515818', 6, 'Vistara UK111 - Mumbai to Bangalore', '2026-02-19 15:43:38', '2026-01-31', NULL, NULL, NULL, 1, 5800.00, 'confirmed', 'paid', '2026-02-19 21:14:02', '2026-02-19 15:43:38', '2026-02-19 15:44:02'),
(138, 1, 'hotel', 'HOTEL62741772532219', 24, 'Royal Heritage Mumbai - Mumbai', '2026-03-03 10:03:39', '2026-03-03', NULL, '2026-03-04', '2026-03-05', 1, 8500.00, 'confirmed', 'paid', '2026-03-03 15:33:47', '2026-03-03 10:03:39', '2026-03-03 10:03:47'),
(139, 13, 'holiday', 'HOLIDAY4617177574542', 30, 'Mumbai Darshan - City Tour Package - India', '2026-04-09 14:37:07', '2026-04-10', '2026-04-12', NULL, NULL, 2, 15998.00, 'confirmed', 'paid', '2026-04-09 20:08:19', '2026-04-09 14:37:07', '2026-04-09 14:38:19'),
(140, 13, 'cruise', 'CRUISE68571775745587', 3, 'Norwegian Cruise - Wonder of the Seas', '2026-04-09 14:39:47', '2026-04-30', '2026-05-10', NULL, NULL, 1, 275000.00, 'pending', 'pending', NULL, '2026-04-09 14:39:47', '2026-04-09 14:39:47'),
(141, 13, 'cruise', 'CRUISE63431775745700', 3, 'Norwegian Cruise - Wonder of the Seas', '2026-04-09 14:41:40', '2026-04-30', '2026-05-10', NULL, NULL, 1, 275000.00, 'pending', 'pending', NULL, '2026-04-09 14:41:40', '2026-04-09 14:41:40'),
(142, 13, 'flight', 'FLIGHT74911775746438', 1, 'Air India AI101 - Mumbai to Delhi', '2026-04-09 14:53:58', '2026-04-11', NULL, NULL, NULL, 1, 9500.00, 'confirmed', 'paid', '2026-04-09 20:24:41', '2026-04-09 14:53:58', '2026-04-09 14:54:41');

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

DROP TABLE IF EXISTS `buses`;
CREATE TABLE IF NOT EXISTS `buses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bus_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `bus_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `departure_city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `arrival_city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `departure_date` date NOT NULL,
  `arrival_date` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `available_seats` int DEFAULT '40',
  `total_seats` int DEFAULT '40',
  `bus_type` enum('sleeper','semi_sleeper','seater','ac','non_ac') COLLATE utf8mb4_general_ci DEFAULT 'seater',
  `status` enum('active','inactive','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`id`, `bus_name`, `bus_number`, `departure_city`, `arrival_city`, `departure_time`, `arrival_time`, `departure_date`, `arrival_date`, `price`, `available_seats`, `total_seats`, `bus_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Volvo AC Sleeper', 'VH001', 'Mumbai', 'Pune', '22:00:00', '02:00:00', '2026-04-11', '2026-04-11', 800.00, 30, 40, 'sleeper', 'active', '2025-12-12 04:08:05', '2026-04-09 14:51:45'),
(2, 'Luxury Seater', 'LS205', 'Delhi', 'Agra', '08:00:00', '12:00:00', '2026-04-11', '2026-04-11', 500.00, 33, 40, 'seater', 'active', '2025-12-12 04:08:05', '2026-04-09 14:51:45'),
(3, 'Semi Sleeper AC', 'SS301', 'Bangalore', 'Mysore', '10:00:00', '14:30:00', '2026-04-11', '2026-04-11', 600.00, 2, 40, 'semi_sleeper', 'active', '2025-12-12 04:08:05', '2026-04-09 14:51:45'),
(4, 'Ahmedabad Express', 'AE101', 'Ahmedabad', 'Mumbai', '06:00:00', '14:00:00', '2026-04-11', '2026-04-11', 700.00, 37, 40, 'seater', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(5, 'Rajkot AC Seater', 'RS102', 'Ahmedabad', 'Rajkot', '07:30:00', '12:00:00', '2026-04-11', '2026-04-11', 550.00, 40, 40, 'ac', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(6, 'Ahmedabad Sleeper', 'AS103', 'Ahmedabad', 'Jaipur', '18:00:00', '06:00:00', '2026-04-11', '2026-04-11', 900.00, 40, 40, 'sleeper', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(7, 'Rajkot Seater', 'RS201', 'Rajkot', 'Surat', '08:00:00', '14:00:00', '2026-04-11', '2026-04-11', 650.00, 40, 40, 'seater', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(8, 'Rajkot AC Sleeper', 'RS202', 'Rajkot', 'Mumbai', '20:00:00', '06:00:00', '2026-04-11', '2026-04-11', 1200.00, 40, 40, 'ac', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(9, 'Surat Sleeper', 'SS301', 'Surat', 'Mumbai', '22:00:00', '06:00:00', '2026-04-11', '2026-04-11', 850.00, 40, 40, 'sleeper', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(10, 'Surat Seater', 'SS302', 'Surat', 'Ahmedabad', '07:00:00', '12:30:00', '2026-04-11', '2026-04-11', 550.00, 40, 40, 'seater', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(11, 'Jaipur AC Seater', 'JS401', 'Jaipur', 'New Delhi', '06:30:00', '12:00:00', '2026-04-11', '2026-04-11', 650.00, 40, 40, 'ac', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(12, 'Jaipur Sleeper', 'JS402', 'Jaipur', 'Mumbai', '20:00:00', '08:00:00', '2026-04-11', '2026-04-11', 1000.00, 40, 40, 'sleeper', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(13, 'Delhi Seater', 'DS501', 'New Delhi', 'Patna', '05:00:00', '15:00:00', '2026-04-11', '2026-04-11', 900.00, 40, 40, 'seater', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(14, 'Delhi AC Sleeper', 'DS502', 'New Delhi', 'Jaipur', '21:00:00', '03:00:00', '2026-04-11', '2026-04-11', 1100.00, 40, 40, 'ac', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(15, 'Patna Sleeper', 'PS601', 'Patna', 'New Delhi', '20:00:00', '06:00:00', '2026-04-11', '2026-04-11', 950.00, 40, 40, 'sleeper', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(16, 'Patna Seater', 'PS602', 'Patna', 'Indore', '07:00:00', '19:00:00', '2026-04-11', '2026-04-11', 1200.00, 40, 40, 'seater', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(17, 'Indore AC Seater', 'IS701', 'Indore', 'Mumbai', '06:00:00', '14:00:00', '2026-04-11', '2026-04-11', 800.00, 40, 40, 'ac', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(18, 'Indore Sleeper', 'IS702', 'Indore', 'Jaipur', '20:00:00', '06:00:00', '2026-04-11', '2026-04-11', 950.00, 40, 40, 'sleeper', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(19, 'Goa AC Sleeper', 'GS801', 'Goa Panaji', 'Mumbai', '22:00:00', '08:00:00', '2026-04-11', '2026-04-11', 1200.00, 40, 40, 'ac', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(20, 'Goa Seater', 'GS802', 'Goa Panaji', 'Bangalore', '06:00:00', '18:00:00', '2026-04-11', '2026-04-11', 1100.00, 40, 40, 'seater', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(21, 'Hyderabad Sleeper', 'HS901', 'Hyderabad', 'Bangalore', '20:00:00', '06:00:00', '2026-04-11', '2026-04-11', 950.00, 40, 40, 'sleeper', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(22, 'Hyderabad AC Seater', 'HS902', 'Hyderabad', 'Chennai', '07:00:00', '17:00:00', '2026-04-11', '2026-04-11', 1100.00, 40, 40, 'ac', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(23, 'Vizag Seater', 'VS1001', 'Vishakhapatnam', 'Chennai', '06:00:00', '18:00:00', '2026-04-11', '2026-04-11', 1050.00, 40, 40, 'seater', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(24, 'Vizag Sleeper', 'VS1002', 'Vishakhapatnam', 'Hyderabad', '20:00:00', '06:00:00', '2026-04-11', '2026-04-11', 950.00, 40, 40, 'sleeper', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(25, 'Bangalore AC Sleeper', 'BS1101', 'Bangalore', 'Chennai', '21:00:00', '06:00:00', '2026-04-11', '2026-04-11', 1000.00, 40, 40, 'ac', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45'),
(26, 'Chennai Seater', 'CS1201', 'Chennai', 'Bangalore', '07:00:00', '19:00:00', '2026-04-11', '2026-04-11', 1050.00, 40, 40, 'seater', 'active', '2025-12-27 12:36:21', '2026-04-09 14:51:45');

-- --------------------------------------------------------

--
-- Table structure for table `cruises`
--

DROP TABLE IF EXISTS `cruises`;
CREATE TABLE IF NOT EXISTS `cruises` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cruise_line` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ship_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `departure_port` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `itinerary_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `departure_date` date NOT NULL,
  `duration_nights` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `available_cabins` int DEFAULT '20',
  `total_cabins` int DEFAULT '20',
  `cabin_type` enum('inside','ocean_view','balcony','suite') COLLATE utf8mb4_general_ci DEFAULT 'inside',
  `amenities` text COLLATE utf8mb4_general_ci,
  `description` text COLLATE utf8mb4_general_ci,
  `image_url` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('active','inactive','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cruises`
--

INSERT INTO `cruises` (`id`, `cruise_line`, `ship_name`, `departure_port`, `itinerary_type`, `departure_date`, `duration_nights`, `price`, `available_cabins`, `total_cabins`, `cabin_type`, `amenities`, `description`, `image_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Royal Caribbean', 'Symphony of the Seas', 'Mumbai', 'Caribbean', '2026-07-07', 7, 345000.00, 12, 20, 'balcony', 'Pool, Spa, Multiple Restaurants, Entertainment', 'Luxury Caribbean cruise experience', './tour_images/royalc.jpg', 'active', '2025-12-12 04:08:05', '2026-03-03 10:08:50'),
(2, 'Carnival Cruise', 'Mardi Gras', 'Goa', 'Mediterranean', '2026-05-01', 5, 350000.00, 11, 20, 'ocean_view', 'Pool, Casino, Shows, Kids Club', 'Family-friendly Mediterranean cruise', './tour_images/carnivalc.jpeg', 'active', '2025-12-12 04:08:05', '2026-03-03 10:08:11'),
(3, 'Norwegian Cruise', 'Wonder of the Seas', 'Mumbai', 'Alaska', '2026-04-30', 10, 275000.00, 5, 20, 'suite', 'Spa, Fine Dining, Excursions, Balcony', 'Premium Alaska adventure cruise', './tour_images/norwayc.jpg', 'active', '2025-12-12 04:08:05', '2026-04-09 14:41:40');

-- --------------------------------------------------------

--
-- Table structure for table `flights`
--

DROP TABLE IF EXISTS `flights`;
CREATE TABLE IF NOT EXISTS `flights` (
  `id` int NOT NULL AUTO_INCREMENT,
  `airline` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `flight_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `departure_city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `arrival_city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `departure_date` date NOT NULL,
  `arrival_date` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `available_seats` int DEFAULT '180',
  `total_seats` int DEFAULT '180',
  `class_type` enum('economy','business','first') COLLATE utf8mb4_general_ci DEFAULT 'economy',
  `status` enum('active','inactive','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flights`
--

INSERT INTO `flights` (`id`, `airline`, `flight_number`, `departure_city`, `arrival_city`, `departure_time`, `arrival_time`, `departure_date`, `arrival_date`, `price`, `available_seats`, `total_seats`, `class_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Air India', 'AI101', 'Mumbai', 'Delhi', '10:15:00', '12:45:00', '2026-04-11', '2026-04-11', 9500.00, 144, 180, 'economy', 'active', '2025-12-12 04:08:04', '2026-04-09 14:53:58'),
(2, 'IndiGo', '6E205', 'Mumbai', 'Goa', '15:15:00', '16:30:00', '2026-04-11', '2026-04-11', 4500.00, 119, 180, 'economy', 'active', '2025-12-12 04:08:04', '2026-04-09 14:51:45'),
(3, 'SpiceJet', 'SG301', 'Delhi', 'Bangalore', '19:15:00', '22:00:00', '2026-04-11', '2026-04-11', 7000.00, 98, 180, 'economy', 'active', '2025-12-12 04:08:04', '2026-04-09 14:51:45'),
(4, 'Emirates', 'EM125', 'Abu Dhabi', 'Kolkata', '22:30:00', '23:30:00', '2026-04-11', '2026-04-11', 21500.00, 176, 180, 'business', 'active', '2025-12-15 06:25:13', '2026-04-09 14:51:45'),
(5, 'Air India', 'AI110', 'Mumbai', 'Kolkata', '08:00:00', '10:45:00', '2026-04-11', '2026-04-11', 6200.00, 178, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(6, 'Vistara', 'UK111', 'Mumbai', 'Bangalore', '11:00:00', '13:00:00', '2026-04-11', '2026-04-11', 5800.00, 179, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(7, 'IndiGo', '6E210', 'Delhi', 'Mumbai', '06:30:00', '08:45:00', '2026-04-11', '2026-04-11', 5400.00, 180, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(8, 'Air India', 'AI211', 'Delhi', 'Kolkata', '09:30:00', '11:45:00', '2026-04-11', '2026-04-11', 5600.00, 180, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(9, 'SpiceJet', 'SG212', 'Delhi', 'Goa', '14:00:00', '16:30:00', '2026-04-11', '2026-04-11', 6300.00, 180, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(10, 'IndiGo', '6E310', 'Kolkata', 'Mumbai', '07:00:00', '09:45:00', '2026-04-11', '2026-04-11', 6000.00, 180, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(11, 'Air India', 'AI311', 'Kolkata', 'Delhi', '12:00:00', '14:15:00', '2026-04-11', '2026-04-11', 5500.00, 180, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(12, 'Vistara', 'UK312', 'Kolkata', 'Bangalore', '16:30:00', '19:00:00', '2026-04-11', '2026-04-11', 6100.00, 179, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(13, 'IndiGo', '6E313', 'Kolkata', 'Goa', '18:00:00', '20:45:00', '2026-04-11', '2026-04-11', 6600.00, 180, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(14, 'IndiGo', '6E410', 'Bangalore', 'Mumbai', '06:00:00', '08:00:00', '2026-04-11', '2026-04-11', 5200.00, 179, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(15, 'Air India', 'AI411', 'Bangalore', 'Delhi', '09:00:00', '11:45:00', '2026-04-11', '2026-04-11', 6400.00, 180, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(16, 'Vistara', 'UK412', 'Bangalore', 'Kolkata', '13:30:00', '16:00:00', '2026-04-11', '2026-04-11', 6000.00, 180, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(17, 'SpiceJet', 'SG413', 'Bangalore', 'Goa', '17:00:00', '18:10:00', '2026-04-11', '2026-04-11', 3800.00, 180, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(18, 'IndiGo', '6E510', 'Goa', 'Mumbai', '07:30:00', '08:45:00', '2026-04-11', '2026-04-11', 3600.00, 180, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(19, 'Air India', 'AI511', 'Goa', 'Delhi', '10:30:00', '13:10:00', '2026-04-11', '2026-04-11', 6200.00, 180, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(20, 'Vistara', 'UK512', 'Goa', 'Bangalore', '14:00:00', '15:10:00', '2026-04-11', '2026-04-11', 3900.00, 180, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45'),
(21, 'IndiGo', '6E513', 'Goa', 'Kolkata', '18:00:00', '20:45:00', '2026-04-11', '2026-04-11', 6700.00, 180, 180, 'economy', 'active', '2025-12-27 10:33:56', '2026-04-09 14:51:45');

-- --------------------------------------------------------

--
-- Table structure for table `holiday_packages`
--

DROP TABLE IF EXISTS `holiday_packages`;
CREATE TABLE IF NOT EXISTS `holiday_packages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `package_name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `destination` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `duration_days` int NOT NULL,
  `duration_nights` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `hotel_category` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `meal_plan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `package_type` enum('honeymoon','family','adventure','beach','hill_station','international') COLLATE utf8mb4_general_ci DEFAULT 'family',
  `includes_flights` tinyint(1) DEFAULT '0',
  `description` text COLLATE utf8mb4_general_ci,
  `itinerary` text COLLATE utf8mb4_general_ci,
  `image_url` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `available_slots` int DEFAULT '10',
  `total_slots` int DEFAULT '10',
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `holiday_packages`
--

INSERT INTO `holiday_packages` (`id`, `package_name`, `destination`, `duration_days`, `duration_nights`, `price`, `hotel_category`, `meal_plan`, `package_type`, `includes_flights`, `description`, `itinerary`, `image_url`, `available_slots`, `total_slots`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Goa Beach Paradise', 'India', 4, 3, 8999.00, 'luxury', 'breakfast', 'beach', 1, 'Perfect beach holiday with luxury resort stay', 'Day 1: Arrival & Beach, Day 2: Water Sports, Day 3: Sightseeing, Day 4: Departure', './tour_images/goa.jpg', 8, 10, 'active', '2025-12-12 04:08:05', '2026-01-21 08:28:50'),
(2, 'Rajasthan Royal Tour', 'India', 7, 6, 15999.00, 'standard', 'half-board', 'family', 1, 'Explore the royal heritage of Rajasthan', 'Visit Jaipur, Udaipur, Jodhpur with heritage hotels', './tour_images/rajasthan.jpeg', 5, 10, 'active', '2025-12-12 04:08:05', '2026-01-21 08:28:22'),
(3, 'Himachal Hill Station', 'India', 5, 4, 9999.00, 'standard', 'breakfast', 'hill_station', 1, 'Mountain adventure with scenic views', 'Manali, Shimla tour with mountain activities', './tour_images/himachal.jpeg', 8, 10, 'active', '2025-12-12 04:08:05', '2026-01-21 08:29:18'),
(4, 'Romantic Paris Getaway', 'France', 7, 6, 249000.00, '4-star', 'Breakfast & Dinner', 'honeymoon', 1, 'Experience the city of love with Eiffel Tower visits, Seine river cruise, and romantic dinners.', 'Day 1: Arrival & Welcome Dinner\nDay 2: Eiffel Tower & Louvre Museum\nDay 3: Seine River Cruise\nDay 4: Versailles Palace\nDay 5: Montmartre & Sacré-Cœur\nDay 6: Free Day Shopping\nDay 7: Departure', './tour_images/france.jpg', 6, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(5, 'Spanish Fiesta Tour', 'Spain', 8, 7, 249000.00, '', 'All Inclusive', 'family', 1, 'Explore Madrid, Barcelona, and experience authentic Spanish culture and cuisine.', 'Day 1: Madrid Arrival\r\nDay 2: Prado Museum & Royal Palace\r\nDay 3: Train to Barcelona\r\nDay 4: Sagrada Familia\r\nDay 5: Park Güell & Gothic Quarter\r\nDay 6: Beach Day\r\nDay 7: Montserrat Day Trip\r\nDay 8: Departure', './tour_images/spain.jpg', 5, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(6, 'American Dream Tour', 'United States', 10, 9, 349000.00, '', 'Breakfast Only', 'adventure', 1, 'New York City, Las Vegas, and Los Angeles - experience the best of America.', 'Day 1: NYC Arrival\r\nDay 2: Statue of Liberty\r\nDay 3: Broadway Show\r\nDay 4: Fly to Las Vegas\r\nDay 5: Grand Canyon Tour\r\nDay 6: Fly to LA\r\nDay 7: Hollywood & Beverly Hills\r\nDay 8: Disneyland\r\nDay 9: Santa Monica Beach\r\nDay 10: Departure', './tour_images/us.jpg', 5, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(7, 'Cappadocia Hot Air Balloon Experience', 'Turkey', 6, 5, 349000.00, '5-star', 'Half Board', 'adventure', 1, 'Magical hot air balloon rides, cave hotels, and ancient historical sites.', 'Day 1: Istanbul Arrival\nDay 2: Hagia Sophia & Blue Mosque\nDay 3: Fly to Cappadocia\nDay 4: Hot Air Balloon Ride\nDay 5: Underground City Tour\nDay 6: Departure', './tour_images/turkey.jpeg', 5, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(8, 'Italian Renaissance Tour', 'Italy', 9, 8, 249000.00, '4-star', 'Breakfast & Dinner', 'honeymoon', 1, 'Rome, Florence, and Venice - experience art, history, and romance.', 'Day 1: Rome Arrival\nDay 2: Colosseum & Roman Forum\nDay 3: Vatican City\nDay 4: Train to Florence\nDay 5: Uffizi Gallery\nDay 6: Tuscany Wine Tour\nDay 7: Train to Venice\nDay 8: Gondola Ride\nDay 9: Departure', './tour_images/Italy.jpg', 9, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(9, 'Mayan Riviera Adventure', 'Mexico', 7, 6, 149000.00, '5-star', 'All Inclusive', 'beach', 1, 'Beautiful beaches, ancient ruins, and vibrant Mexican culture.', 'Day 1: Cancun Arrival\nDay 2: Chichen Itza Tour\nDay 3: Tulum Ruins\nDay 4: Beach Day\nDay 5: Xcaret Park\nDay 6: Isla Mujeres\nDay 7: Departure', './tour_images/mexico.jpg', 8, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(10, 'British Heritage Tour', 'United Kingdom', 8, 7, 349000.00, '4-star', 'Bed & Breakfast', 'family', 1, 'London, Stonehenge, and Edinburgh - explore British history and culture.', 'Day 1: London Arrival\nDay 2: Buckingham Palace\nDay 3: British Museum\nDay 4: Stonehenge & Bath\nDay 5: Train to Edinburgh\nDay 6: Edinburgh Castle\nDay 7: Scottish Highlands\nDay 8: Departure', './tour_images/uk.jpeg', 6, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(11, 'German Castles & Beer Tour', 'Germany', 7, 6, 249000.00, '3-star', 'Breakfast Only', 'adventure', 1, 'Explore Neuschwanstein Castle, Berlin Wall, and experience Oktoberfest culture.', 'Day 1: Berlin Arrival\nDay 2: Berlin Wall & Brandenburg Gate\nDay 3: Train to Munich\nDay 4: Neuschwanstein Castle\nDay 5: Munich City Tour\nDay 6: Nuremberg Day Trip\nDay 7: Departure', './tour_images/germany.jpg', 9, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(12, 'Cherry Blossom Special', 'Japan', 10, 9, 349000.00, '', '', 'international', 1, 'Tokyo, Kyoto, and Mount Fuji during cherry blossom season.', 'Day 1: Tokyo Arrival\r\nDay 2: Shibuya & Shinjuku\r\nDay 3: Tokyo Disneyland\r\nDay 4: Bullet Train to Kyoto\r\nDay 5: Fushimi Inari Shrine\r\nDay 6: Osaka Day Trip\r\nDay 7: Mount Fuji Tour\r\nDay 8: Hakone Onsen\r\nDay 9: Free Day\r\nDay 10: Departure', './tour_images/japan.jpg', 4, 10, 'active', '2025-12-12 08:01:48', '2026-02-19 07:05:45'),
(13, 'Greek Island Hopping', 'Greece', 8, 7, 249000.00, '4-star', 'Breakfast Only', 'beach', 1, 'Santorini, Mykonos, and Athens - beautiful sunsets and ancient ruins.', 'Day 1: Athens Arrival\nDay 2: Acropolis Tour\nDay 3: Ferry to Mykonos\nDay 4: Mykonos Beaches\nDay 5: Ferry to Santorini\nDay 6: Oia Sunset\nDay 7: Volcano Tour\nDay 8: Departure', './tour_images/greece.jpg', 2, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(14, 'Great Wall & Forbidden City', 'China', 9, 8, 249000.00, '4-star', 'Full Board', 'international', 1, 'Beijing, Shanghai, and the Great Wall of China.', 'Day 1: Beijing Arrival\nDay 2: Forbidden City\nDay 3: Great Wall Tour\nDay 4: Summer Palace\nDay 5: Fly to Shanghai\nDay 6: The Bund & Yu Garden\nDay 7: Disneyland Shanghai\nDay 8: Zhujiajiao Water Town\nDay 9: Departure', './tour_images/china.jpeg', 5, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(15, 'Thai Beach Paradise', 'Thailand', 7, 6, 149000.00, '5-star', 'All Inclusive', 'beach', 1, 'Phuket, Phi Phi Islands, and Bangkok - tropical paradise experience.', 'Day 1: Bangkok Arrival\nDay 2: Grand Palace & Temples\nDay 3: Fly to Phuket\nDay 4: Phi Phi Islands Tour\nDay 5: James Bond Island\nDay 6: Beach Relaxation\nDay 7: Departure', './tour_images/thailand.jpg', 3, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(16, 'Hong Kong City Lights', 'Hong Kong (Special Administrative Region)', 5, 4, 249000.00, '4-star', 'Bed & Breakfast', 'family', 1, 'Victoria Peak, Disneyland, and vibrant street markets.', 'Day 1: Arrival\nDay 2: Victoria Peak & Star Ferry\nDay 3: Disneyland Hong Kong\nDay 4: Lantau Island & Big Buddha\nDay 5: Departure', './tour_images/hong kong.jpg', 5, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(17, 'Saudi Heritage Experience', 'Saudi Arabia', 6, 5, 149000.00, '5-star', 'Half Board', 'adventure', 1, 'Modern cities and ancient historical sites in Riyadh and AlUla.', 'Day 1: Riyadh Arrival\nDay 2: Diriyah & National Museum\nDay 3: Fly to AlUla\nDay 4: Hegra Archaeological Site\nDay 5: Elephant Rock & Old Town\nDay 6: Departure', './tour_images/saudiarabia.jpg', 8, 10, 'active', '2025-12-12 08:01:48', '2026-01-22 13:00:37'),
(18, 'Malaysian Twin City Tour', 'Malaysia', 7, 6, 349000.00, '4-star', 'Breakfast Only', 'family', 1, 'Kuala Lumpur city lights and Langkawi island paradise.', 'Day 1: Kuala Lumpur Arrival\nDay 2: Petronas Towers\nDay 3: Batu Caves\nDay 4: Fly to Langkawi\nDay 5: Island Hopping\nDay 6: Cable Car & Sky Bridge\nDay 7: Departure', './tour_images/malaysia.jpg', 4, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(19, 'Canadian Rockies Adventure', 'Canada', 8, 7, 149000.00, '4-star', 'Breakfast Only', 'adventure', 1, 'Banff, Lake Louise, and Vancouver - breathtaking mountain scenery.', 'Day 1: Vancouver Arrival\nDay 2: Stanley Park & Capilano\nDay 3: Fly to Calgary\nDay 4: Banff National Park\nDay 5: Lake Louise & Moraine Lake\nDay 6: Columbia Icefield\nDay 7: Jasper National Park\nDay 8: Departure', './tour_images/canada.jpg', 6, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(20, 'Dubai Luxury Experience', 'United Arab Emirates', 5, 4, 349000.00, '5-star', 'All Inclusive', 'family', 1, 'Burj Khalifa, desert safari, and luxury shopping in Dubai.', 'Day 1: Dubai Arrival\nDay 2: Burj Khalifa & Dubai Mall\nDay 3: Desert Safari\nDay 4: Palm Jumeirah & Atlantis\nDay 5: Departure', './tour_images/uae.jpg', 1, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(21, 'Australian Picnics & Barbeques', 'Australia', 7, 6, 149000.00, '', '', 'hill_station', 1, 'Unique island continent, the world\'s sixth-largest country, known for its vast, arid Outback, unique wildlife (kangaroos, platypuses), stunning coastlines (Great Barrier Reef).', 'Day 1: Arrival in Sydney\r\nDay 2: Sydney City Tour\r\nDay 3: Blue Mountains Day Trip\r\nDay 4: Flight to Melbourne\r\nDay 5: Great Ocean Road Tour\r\nDay 6: Gold Coast Leisure Day\r\nDay 7: Departure', './tour_images/australia.jpg', 1, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(22, 'Portuguese Coastal Tour', 'Portugal', 7, 6, 149000.00, '4-star', 'Breakfast Only', 'beach', 1, 'Lisbon, Porto, and Algarve - stunning coastlines and historic cities.', 'Day 1: Lisbon Arrival\nDay 2: Belém Tower & Jerónimos\nDay 3: Sintra Day Trip\nDay 4: Train to Porto\nDay 5: Port Wine Tasting\nDay 6: Douro Valley\nDay 7: Departure', './tour_images/portugal.jpg', 7, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(23, 'Dutch Tulip Festival', 'Netherlands', 6, 5, 249000.00, '3-star', 'Bed & Breakfast', 'family', 1, 'Keukenhof gardens, Amsterdam canals, and windmills.', 'Day 1: Amsterdam Arrival\nDay 2: Canal Cruise & Rijksmuseum\nDay 3: Keukenhof Gardens\nDay 4: Zaanse Schans Windmills\nDay 5: Volendam & Marken\nDay 6: Departure', './tour_images/natherlands.jpg', 4, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(24, 'Singapore Tech & Culture', 'Singapore', 6, 5, 149000.00, '', 'Breakfast Only', 'family', 1, 'Known for its futuristic skyline (Marina Bay Sands, Gardens by the Bay), world-class food scene (hawker centres, Singapore Sling), incredible cleanliness, efficient infrastructure, multicultural diversity (Chinatown, Little India), and status as a global financial hub, seamlessly blending lush greenery with cutting-edge modernity.  ', 'Day 1: Singapore Arrival & Marina Bay\r\nDay 2: Sentosa Island Adventure\r\nDay 3: Cultural Exploration & Clarke Quay\r\nDay 4: Gardens by the Bay & Shopping\r\nDay 5: Wildlife Encounters & Singapore Flyer\r\nDay 6: Jewel Changi Airport & Departure', './tour_images/singapore.jpg', 3, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(25, 'Vietnam Highlights Tour', 'Vietnam', 9, 8, 349000.00, '4-star', 'Breakfast & Dinner', 'adventure', 1, 'Halong Bay cruise, Hanoi, and Ho Chi Minh City.', 'Day 1: Hanoi Arrival\nDay 2: Hanoi City Tour\nDay 3: Halong Bay Cruise\nDay 4: Halong Bay\nDay 5: Fly to Hoi An\nDay 6: Hoi An Ancient Town\nDay 7: Fly to Ho Chi Minh\nDay 8: Mekong Delta\nDay 9: Departure', './tour_images/vietnam.jpeg', 7, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(26, 'K-Pop & Korean Culture', 'South Korea', 7, 6, 249000.00, '', 'Breakfast Only', 'international', 1, 'Seoul city tour, Korean food, and cultural experiences.', 'Day 1: Seoul Arrival\r\nDay 2: Gyeongbokgung Palace\r\nDay 3: DMZ Tour\r\nDay 4: N Seoul Tower & Myeongdong\r\nDay 5: Korean Cooking Class\r\nDay 6: Everland Theme Park\r\nDay 7: Departure', './tour_images/southkorea.jpg', 6, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 10:56:12'),
(27, 'Moroccan Desert Adventure', 'Morocco', 8, 7, 149000.00, '4-star', 'Half Board', 'adventure', 1, 'Marrakech, Sahara Desert, and Atlas Mountains.', 'Day 1: Marrakech Arrival\nDay 2: Jemaa el-Fnaa & Souks\nDay 3: Atlas Mountains Tour\nDay 4: Ait Benhaddou\nDay 5: Sahara Desert Camp\nDay 6: Camel Trek & Berber Villages\nDay 7: Return to Marrakech\nDay 8: Departure', './tour_images/morocco.jpg', 7, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(28, 'Egyptian Pyramids & Nile Cruise', 'Egypt', 8, 7, 349000.00, '5-star', 'All Inclusive', 'adventure', 1, 'Pyramids of Giza, Luxor temples, and Nile River cruise.', 'Day 1: Cairo Arrival\nDay 2: Pyramids & Sphinx\nDay 3: Egyptian Museum\nDay 4: Fly to Luxor\nDay 5: Valley of the Kings\nDay 6: Nile Cruise\nDay 7: Edfu & Kom Ombo\nDay 8: Departure', './tour_images/egypt.jpg', 4, 10, 'active', '2025-12-12 08:01:48', '2026-01-21 06:33:07'),
(29, 'Moscow & St. Petersburg Imperial Tour', 'Russia', 9, 8, 249000.00, '5-star', 'Breakfast & Dinner', 'family', 1, 'Explore two of Russia\'s most iconic cities - the capital Moscow and cultural capital St. Petersburg with their magnificent palaces, cathedrals, and rich history.', 'Day 1: Moscow Arrival & Red Square\r\nDay 2: Kremlin & Armory Museum\r\nDay 3: St. Basil\'s Cathedral & GUM\r\nDay 4: High-speed train to St. Petersburg\r\nDay 5: Hermitage Museum & Winter Palace\r\nDay 6: Peterhof Palace & Fountain Park\r\nDay 7: Catherine Palace & Amber Room\r\nDay 8: Church of Savior on Blood & Canal Cruise\r\nDay 9: Departure from St. Petersburg', './tour_images/russia.jpg', 3, 10, 'active', '2025-12-12 23:51:47', '2026-01-21 10:18:54'),
(30, 'Mumbai Darshan - City Tour Package', 'India', 2, 1, 7999.00, '3-star', 'Breakfast & Dinner', 'family', 1, 'Explore the vibrant city of Mumbai with visits to Gateway of India, Marine Drive, Siddhivinayak Temple, and other iconic landmarks. Perfect weekend getaway to experience Maximum City.', 'Day 1: Arrival & Check-in, Gateway of India, Taj Mahal Palace, Colaba Causeway\nDay 2: Marine Drive, Chowpatty Beach, Haji Ali Dargah, Siddhivinayak Temple, Bandra-Worli Sea Link, Departure', './tour_images/mumbai.jpg', 4, 20, 'active', '2025-12-12 23:55:42', '2026-04-09 14:37:07');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

DROP TABLE IF EXISTS `hotels`;
CREATE TABLE IF NOT EXISTS `hotels` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `star_rating` int DEFAULT '3',
  `price_per_night` decimal(10,2) NOT NULL,
  `available_rooms` int DEFAULT '10',
  `total_rooms` int DEFAULT '10',
  `amenities` text COLLATE utf8mb4_general_ci,
  `description` text COLLATE utf8mb4_general_ci,
  `image_url` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `city`, `address`, `star_rating`, `price_per_night`, `available_rooms`, `total_rooms`, `amenities`, `description`, `image_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Grand Hotel Mumbai', 'Mumbai', 'Marine Drive, Mumbai', 5, 5000.00, 22, 10, 'WiFi, Pool, Spa, Restaurant', 'Luxury hotel with ocean view', './hotel_img/mumbaiht.jpg', 'active', '2025-12-12 04:08:05', '2025-12-29 14:00:15'),
(2, 'Beach Resort Goa', 'Goa', 'Calangute Beach, Goa', 4, 7500.00, 26, 10, 'WiFi, Pool, Beach Access, Bar', 'Beachfront resort with modern amenities', './hotel_img/goaht.jpg', 'active', '2025-12-12 04:08:05', '2025-12-29 14:00:45'),
(3, 'Heritage Palace Jaipur', 'Jaipur', 'City Center, Jaipur', 4, 4000.00, 11, 10, 'WiFi, Restaurant, Spa, Heritage Tours', 'Traditional Rajasthani architecture', './hotel_img/jaipurht.jpg', 'active', '2025-12-12 04:08:05', '2025-12-29 14:01:01'),
(4, 'Imperial Residency', 'New Delhi', 'Connaught Place, New Delhi', 5, 7500.00, 20, 20, 'WiFi, Pool, Spa, Restaurant', 'Luxury hotel in the heart of Delhi with premium amenities', './hotel_img/imperial_residency.jpg', 'active', '2025-12-27 12:45:24', '2025-12-29 13:47:36'),
(5, 'Lotus Grand', 'New Delhi', 'India Gate Road, New Delhi', 4, 5500.00, 15, 15, 'WiFi, Gym, Bar, Rooftop Lounge', 'Elegant boutique hotel with stunning city views', './hotel_img/lotus_grand.jpg', 'active', '2025-12-27 12:45:24', '2025-12-29 13:47:36'),
(6, 'Sabarmati Palace', 'Ahmedabad', 'Paldi, Ahmedabad', 4, 4800.00, 16, 18, 'WiFi, Pool, Restaurant, Spa', 'Modern hotel inspired by traditional architecture', './hotel_img/sabarmati_palace.jpg', 'active', '2025-12-27 12:45:24', '2025-12-29 13:47:36'),
(7, 'Gujarat Elegance', 'Ahmedabad', 'Navrangpura, Ahmedabad', 3, 7200.00, 9, 12, 'WiFi, Gym, Bar, Pool', 'Premium hotel with luxurious rooms and services', './hotel_img/gujarat_elegance.jpg', 'active', '2025-12-27 12:45:24', '2026-01-22 12:21:54'),
(8, 'Rajkot Royal', 'Rajkot', 'Rajkot City Center', 4, 4000.00, 14, 14, 'WiFi, Restaurant, Spa', 'Comfortable hotel blending modern and royal decor', './hotel_img/rajkot_royal.jpg', 'active', '2025-12-27 12:45:24', '2025-12-29 13:47:36'),
(9, 'Diamond Inn Surat', 'Surat', 'Ring Road, Surat', 3, 3500.00, 20, 20, 'WiFi, Parking, Restaurant', 'Affordable yet stylish hotel near commercial hubs', './hotel_img/diamond_inn_surat.jpg', 'active', '2025-12-27 12:45:24', '2025-12-29 13:47:36'),
(10, 'Pune Horizon', 'Pune', 'FC Road, Pune', 4, 5000.00, 16, 16, 'WiFi, Pool, Gym, Bar', 'Contemporary hotel with rooftop lounge', './hotel_img/pune_horizon.jpg', 'active', '2025-12-27 12:45:24', '2025-12-29 13:47:36'),
(11, 'Marigold Suites', 'Pune', 'Koregaon Park, Pune', 5, 6800.00, 12, 12, 'WiFi, Spa, Pool, Restaurant', 'Luxury suites with modern amenities and serene ambiance', './hotel_img/marigold_suites.jpg', 'active', '2025-12-27 12:45:24', '2025-12-29 13:47:36'),
(12, 'Bangalore Royale', 'Bangalore', 'MG Road, Bangalore', 5, 7200.00, 15, 15, 'WiFi, Pool, Spa, Gym', 'Elegant hotel in central Bangalore with luxurious rooms', './hotel_img/bangalore_royale.jpg', 'active', '2025-12-27 12:45:24', '2025-12-30 14:37:19'),
(13, 'Gardenia Hotel', 'Bangalore', 'Whitefield, Bangalore', 4, 5200.00, 18, 18, 'WiFi, Restaurant, Gym, Bar', 'Modern hotel with scenic garden views', './hotel_img/gardenia_hotel.jpg', 'active', '2025-12-27 12:45:24', '2025-12-29 13:47:36'),
(14, 'Chennai Serenity', 'Chennai', 'Marina Beach Road, Chennai', 4, 5600.00, 18, 20, 'WiFi, Pool, Spa, Restaurant', 'Elegant beachfront hotel with contemporary design', './hotel_img/chennai_serenity.jpg', 'active', '2025-12-27 12:45:24', '2026-01-29 18:27:06'),
(15, 'Bayview Palace', 'Chennai', 'Anna Salai, Chennai', 3, 7800.00, 12, 12, 'WiFi, Gym, Pool, Bar', 'Luxury hotel with ocean-view suites and premium services', './hotel_img/bayview_palace.jpg', 'active', '2025-12-27 12:45:24', '2025-12-29 13:47:36'),
(16, 'Pearl Comfort', 'Hyderabad', 'Banjara Hills, Hyderabad', 4, 5400.00, 18, 18, 'WiFi, Spa, Gym, Restaurant', 'Modern hotel with elegant interiors and premium amenities', './hotel_img/pearl_comfort.jpg', 'active', '2025-12-27 12:45:24', '2025-12-29 13:47:36'),
(17, 'Nizam Heritage', 'Hyderabad', 'Hitech City, Hyderabad', 3, 8000.00, 14, 14, 'WiFi, Pool, Spa, Bar', 'Luxury heritage-inspired hotel with state-of-the-art facilities', './hotel_img/nizam_heritage.jpg', 'active', '2025-12-27 12:45:24', '2025-12-29 13:47:36'),
(18, 'Ganges View Inn', 'Patna', 'Patna City Center', 4, 4200.00, 15, 15, 'WiFi, Restaurant, Gym', 'Comfortable hotel with river views and modern amenities', './hotel_img/ganges_view_inn.jpg', 'active', '2025-12-27 12:45:24', '2025-12-29 13:47:36'),
(19, 'Indore Grand', 'Indore', 'MG Road, Indore', 4, 4800.00, 18, 18, 'WiFi, Pool, Spa, Restaurant', 'Elegant hotel with premium rooms and city-center location', './hotel_img/indore_grand.jpg', 'active', '2025-12-27 12:45:24', '2025-12-29 13:47:36'),
(20, 'Vizag Bay Resort', 'Vishakhapatnam', 'Beach Road, Vishakhapatnam', 5, 7500.00, 12, 12, 'WiFi, Pool, Spa, Beach Access, Bar', 'Luxury beachfront resort with modern amenities', './hotel_img/vizag_bay_resort.jpg', 'active', '2025-12-27 12:45:24', '2025-12-29 13:47:36'),
(21, 'Victoria Grande', 'Kolkata', 'Park Street, Kolkata', 5, 7800.00, 15, 15, 'WiFi, Pool, Spa, Restaurant, Bar', 'Luxury hotel in central Kolkata with heritage-inspired architecture', './hotel_img/victoria_grande.jpg', 'active', '2025-12-27 12:45:42', '2025-12-29 13:47:36'),
(22, 'Bengal Elegance', 'Kolkata', 'Esplanade, Kolkata', 4, 5200.00, 20, 20, 'WiFi, Gym, Restaurant, Rooftop Lounge', 'Elegant boutique hotel with modern amenities', './hotel_img/bengal_elegance.jpg', 'active', '2025-12-27 12:45:42', '2025-12-29 13:47:36'),
(23, 'Seaside Retreat', 'Goa', 'Baga Beach, Goa', 3, 8000.00, 12, 12, 'WiFi, Pool, Beach Access, Spa, Bar', 'Luxury beachfront resort with stunning sunset views', './hotel_img/seaside_retreat.jpg', 'active', '2025-12-27 12:45:42', '2025-12-29 13:47:36'),
(24, 'Royal Heritage Mumbai', 'Mumbai', 'Colaba, Mumbai', 5, 8500.00, 14, 15, 'WiFi, Pool, Spa, Restaurant', 'Luxury heritage-inspired hotel near the Gateway of India', './hotel_img/royal_heritage_mumbai.jpg', 'active', '2025-12-27 12:45:42', '2026-03-03 10:03:39'),
(25, 'Jaipur Royal Suites', 'Jaipur', 'Amber Road, Jaipur', 5, 7500.00, 12, 12, 'WiFi, Pool, Spa, Restaurant, Heritage Tours', 'Luxury suites blending modern comfort and Rajasthani heritage', './hotel_img/jaipur_royal_suites.jpg', 'active', '2025-12-27 12:45:42', '2025-12-29 13:47:36');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `user_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('credit_card','debit_card','net_banking','upi','wallet') COLLATE utf8mb4_general_ci DEFAULT 'credit_card',
  `transaction_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_status` enum('pending','success','failed','refunded') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `payment_details` text COLLATE utf8mb4_general_ci,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_id` (`transaction_id`),
  KEY `booking_id` (`booking_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `user_id`, `amount`, `payment_method`, `transaction_id`, `payment_status`, `payment_details`, `payment_date`, `created_at`) VALUES
(1, 7, 5, 500.00, 'debit_card', 'TXN17655611068426', 'success', NULL, '2025-12-12 06:38:26', '2025-12-15 09:17:45'),
(2, 7, 5, 500.00, 'upi', 'TXN17655611387506', 'success', NULL, '2025-12-12 06:38:58', '2025-12-15 09:17:45'),
(3, 8, 5, 2500.00, 'credit_card', 'TXN17655611724651', 'success', NULL, '2025-12-12 06:39:32', '2025-12-15 09:17:45'),
(4, 10, 5, 17000.00, 'credit_card', 'TXN17655612209351', 'success', NULL, '2025-12-12 06:40:20', '2025-12-15 09:17:45'),
(5, 11, 5, 7000.00, 'credit_card', 'TXN17655616704363', 'success', NULL, '2025-12-12 06:47:50', '2025-12-15 09:17:45'),
(6, 12, 5, 45000.00, 'net_banking', 'TXN17655618271230', 'success', NULL, '2025-12-12 06:50:27', '2025-12-15 09:17:45'),
(7, 13, 5, 15000.00, 'debit_card', 'TXN17655635038805', 'refunded', NULL, '2025-12-12 07:18:23', '2025-12-15 09:17:45'),
(12, 20, 5, 4399.98, 'debit_card', 'TXN17657129179589', 'refunded', NULL, '2025-12-14 06:18:37', '2025-12-15 09:17:45'),
(13, 21, 5, 4000.00, 'debit_card', 'TXN17657129751307', 'refunded', NULL, '2025-12-14 06:19:35', '2025-12-15 09:17:45'),
(14, 23, 5, 17500.00, 'debit_card', 'TXN17657156345961', 'refunded', NULL, '2025-12-14 07:03:54', '2025-12-15 09:17:45'),
(15, 24, 5, 13599.92, 'credit_card', 'TXN17658001304451', 'refunded', NULL, '2025-12-15 06:32:10', '2025-12-15 09:17:45'),
(16, 25, 5, 5399.97, 'debit_card', 'TXN17658068001371', 'success', NULL, '2025-12-15 08:23:20', '2025-12-15 09:17:45'),
(17, 26, 5, 2999.00, 'wallet', 'TXN17658100732100', 'success', '{\"payment_method\":\"wallet\",\"card_number\":\"\",\"card_holder\":\"\",\"expiry_month\":\"\",\"expiry_year\":\"\",\"cvv\":\"\",\"upi_id\":\"\",\"bank_name\":\"\"}', '2025-12-15 09:17:53', '2025-12-15 09:17:53'),
(18, 27, 5, 1799.99, 'wallet', 'TXN17658101063402', 'success', '{\"payment_method\":\"wallet\",\"card_number\":\"\",\"card_holder\":\"\",\"expiry_month\":\"\",\"expiry_year\":\"\",\"cvv\":\"\",\"upi_id\":\"\",\"bank_name\":\"\"}', '2025-12-15 09:18:26', '2025-12-15 09:18:26'),
(19, 29, 5, 35000.00, 'debit_card', 'TXN17658115298530', 'success', '{\"method\":\"debit_card\",\"masked_card\":\"6351\",\"upi_id\":\"\",\"bank\":\"\"}', '2025-12-15 09:42:09', '2025-12-15 09:42:09'),
(20, 30, 5, 75000.00, 'upi', 'TXN17658115838394', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"jhbjbsa@ociii\",\"bank\":\"\"}', '2025-12-15 09:43:03', '2025-12-15 09:43:03'),
(21, 31, 5, 45000.00, 'wallet', 'TXN17658116053553', 'success', '{\"method\":\"wallet\",\"masked_card\":null,\"upi_id\":\"\",\"bank\":\"\"}', '2025-12-15 09:43:25', '2025-12-15 09:43:25'),
(22, 32, 5, 45000.00, 'credit_card', 'TXN17658116469624', 'success', '{\"method\":\"credit_card\",\"masked_card\":\"2616\",\"upi_id\":\"\",\"bank\":\"\"}', '2025-12-15 09:44:06', '2025-12-15 09:44:06'),
(23, 33, 5, 1499.99, 'debit_card', 'TXN17658119445850', 'success', '{\"method\":\"debit_card\",\"masked_card\":\"4444\",\"upi_id\":\"\",\"bank\":\"\"}', '2025-12-15 09:49:04', '2025-12-15 09:49:04'),
(24, 34, 5, 7500.00, 'debit_card', 'TXN17658120395757', 'success', '{\"method\":\"debit_card\",\"masked_card\":\"4444\",\"upi_id\":\"\",\"bank\":\"\"}', '2025-12-15 09:50:39', '2025-12-15 09:50:39'),
(25, 35, 5, 3899.97, 'debit_card', 'TXN17658124691123', 'success', '{\"method\":\"debit_card\",\"masked_card\":\"4444\",\"upi_id\":\"\",\"bank\":\"\"}', '2025-12-15 09:57:49', '2025-12-15 09:57:49'),
(26, 36, 5, 1399.99, 'upi', 'TXN17658125949341', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"asd@asd\",\"bank\":\"\"}', '2025-12-15 09:59:54', '2025-12-15 09:59:54'),
(27, 37, 5, 20000.00, 'wallet', 'TXN17658126782104', 'success', '{\"method\":\"wallet\",\"masked_card\":null,\"upi_id\":\"\",\"bank\":\"\"}', '2025-12-15 10:01:18', '2025-12-15 10:01:18'),
(44, 61, 5, 3600.00, 'debit_card', 'TXN17658231364504', 'success', '{\"method\":\"debit_card\",\"masked_card\":\"6315\",\"upi_id\":null}', '2025-12-15 12:55:36', '2025-12-15 12:55:36'),
(45, 62, 5, 12499.95, 'credit_card', 'TXN17658232162666', 'success', '{\"method\":\"credit_card\",\"masked_card\":\"6232\",\"upi_id\":null}', '2025-12-15 12:56:56', '2025-12-15 12:56:56'),
(46, 63, 5, 4499.97, 'upi', 'TXN17658232679487', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"maha@okic\"}', '2025-12-15 12:57:47', '2025-12-15 12:57:47'),
(47, 64, 5, 1999.98, 'debit_card', 'TXN17658241828145', 'success', '{\"method\":\"debit_card\",\"masked_card\":\"5959\",\"upi_id\":null}', '2025-12-15 13:13:02', '2025-12-15 13:13:02'),
(48, 65, 5, 3799.98, 'upi', 'TXN17658256154419', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"mhavir@kae\"}', '2025-12-15 13:36:55', '2025-12-15 13:36:55'),
(49, 66, 5, 75000.00, 'credit_card', 'TXN17658258261261', 'success', '{\"method\":\"credit_card\",\"masked_card\":\"1651\",\"upi_id\":null}', '2025-12-15 13:40:26', '2025-12-15 13:40:26'),
(55, 72, 5, 5800.00, 'upi', 'TXN17668410724639', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"sdsi@isi\"}', '2025-12-27 13:11:12', '2025-12-27 13:11:12'),
(56, 75, 5, 4500.00, 'upi', 'TXN17669125481767', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"ggyuuh@jsd\"}', '2025-12-28 09:02:28', '2025-12-28 09:02:28'),
(57, 76, 5, 70000.00, 'upi', 'TXN17669144373736', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"ef@dfi\"}', '2025-12-28 09:33:57', '2025-12-28 09:33:57'),
(58, 84, 5, 7000.00, 'credit_card', 'TXN17669153479155', 'success', '{\"method\":\"credit_card\",\"masked_card\":\"6161\",\"upi_id\":null}', '2025-12-28 09:49:07', '2025-12-28 09:49:07'),
(59, 86, 5, 1800.00, 'upi', 'TXN17669180124482', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"hggf@hhi\"}', '2025-12-28 10:33:32', '2025-12-28 10:33:32'),
(60, 88, 5, 2999.00, 'upi', 'TXN17669200116321', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"as@eds\"}', '2025-12-28 11:06:51', '2025-12-28 11:06:51'),
(61, 89, 5, 1799.99, 'upi', 'TXN17669202146931', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"wed@dsdf\"}', '2025-12-28 11:10:14', '2025-12-28 11:10:14'),
(62, 90, 5, 1499.99, 'upi', 'TXN17669203877807', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"sd@sdf\"}', '2025-12-28 11:13:07', '2025-12-28 11:13:07'),
(63, 91, 5, 7200.00, 'upi', 'TXN17669205418581', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"ds@sds\"}', '2025-12-28 11:15:41', '2025-12-28 11:15:41'),
(64, 93, 5, 2999.00, 'upi', 'TXN17669278431948', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"khjg@iij\"}', '2025-12-28 13:17:23', '2025-12-28 13:17:23'),
(65, 95, 5, 6100.00, 'upi', 'TXN17669285567351', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"sd@suu\"}', '2025-12-28 13:29:16', '2025-12-28 13:29:16'),
(66, 96, 5, 9600.00, 'upi', 'TXN17669286197121', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"hg@gg\"}', '2025-12-28 13:30:19', '2025-12-28 13:30:19'),
(67, 97, 5, 1800.00, 'upi', 'TXN17669286462832', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"hg@yyy\"}', '2025-12-28 13:30:46', '2025-12-28 13:30:46'),
(68, 98, 5, 1400.00, 'upi', 'TXN17669286787180', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"ggfty@rr\"}', '2025-12-28 13:31:18', '2025-12-28 13:31:18'),
(69, 99, 5, 75000.00, 'upi', 'TXN17669287212518', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"hgh@hhh\"}', '2025-12-28 13:32:01', '2025-12-28 13:32:01'),
(70, 100, 5, 1899.99, 'upi', 'TXN17669287769854', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"gfdgf@rrr\"}', '2025-12-28 13:32:56', '2025-12-28 13:32:56'),
(71, 101, 5, 1299.99, 'upi', 'TXN17669292147108', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"sd@dfdsf\"}', '2025-12-28 13:40:14', '2025-12-28 13:40:14'),
(72, 102, 5, 7200.00, 'upi', 'TXN17670278355355', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"asd@ds\"}', '2025-12-29 17:03:55', '2025-12-29 17:03:55'),
(73, 103, 5, 5400.00, 'upi', 'TXN17670299371943', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"sd@sdf\"}', '2025-12-29 17:38:57', '2025-12-29 17:38:57'),
(74, 105, 5, 14400.00, 'credit_card', 'TXN17671029935786', 'refunded', '{\"method\":\"credit_card\",\"masked_card\":\"1616\",\"upi_id\":null}', '2025-12-30 13:56:33', '2025-12-30 13:56:33'),
(75, 106, 5, 1399.99, 'upi', 'TXN17671034105777', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"jhg@hjj\"}', '2025-12-30 14:03:30', '2025-12-30 14:03:30'),
(76, 107, 5, 21600.00, 'upi', 'TXN17671049318152', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"asd@sds\"}', '2025-12-30 14:28:51', '2025-12-30 14:28:51'),
(77, 108, 5, 1999.99, 'upi', 'TXN17671084391607', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"jgv@yyh\"}', '2025-12-30 15:27:19', '2025-12-30 15:27:19'),
(78, 109, 5, 35000.00, 'upi', 'TXN17671090842319', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"hg@tggg\"}', '2025-12-30 15:38:04', '2025-12-30 15:38:04'),
(79, 112, 5, 45000.00, 'upi', 'TXN17671111553094', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"dasdz@dsfas\"}', '2025-12-30 16:12:35', '2025-12-30 16:12:35'),
(80, 114, 5, 45000.00, 'upi', 'TXN17671112876575', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"sad@sd\"}', '2025-12-30 16:14:47', '2025-12-30 16:14:47'),
(81, 115, 5, 5200.00, 'upi', 'TXN17671115241482', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"asd@jjj\"}', '2025-12-30 16:18:44', '2025-12-30 16:18:44'),
(82, 116, 5, 6200.00, 'upi', 'TXN17671116553624', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"asda@jjj\"}', '2025-12-30 16:20:55', '2025-12-30 16:20:55'),
(83, 118, 5, 45000.00, 'upi', 'TXN17671131357126', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"hg@hhh\"}', '2025-12-30 16:45:35', '2025-12-30 16:45:35'),
(84, 119, 5, 500.00, 'upi', 'TXN17671132203801', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"jhjg@gg\"}', '2025-12-30 16:47:00', '2025-12-30 16:47:00'),
(85, 120, 5, 6200.00, 'upi', 'TXN17672044197021', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"asdasd@sss\"}', '2025-12-31 18:06:59', '2025-12-31 18:06:59'),
(86, 121, 5, 800.00, 'upi', 'TXN17672046279278', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"gfg@gg\"}', '2025-12-31 18:10:27', '2025-12-31 18:10:27'),
(87, 122, 5, 1699.99, 'upi', 'TXN17672083326830', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"sd@ssf\"}', '2025-12-31 19:12:12', '2025-12-31 19:12:12'),
(89, 124, 11, 7999.00, 'upi', 'TXN17689855014199', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"asd@asji\"}', '2026-01-21 08:51:41', '2026-01-21 08:51:41'),
(90, 125, 11, 249000.00, 'upi', 'TXN17689929783085', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"asd@dfd\"}', '2026-01-21 10:56:18', '2026-01-21 10:56:18'),
(92, 127, 5, 5800.00, 'upi', 'TXN17690841402510', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"sdas@dfsf\"}', '2026-01-22 12:15:40', '2026-01-22 12:15:40'),
(93, 129, 5, 149000.00, 'upi', 'TXN17690866023421', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"asd@sdf\"}', '2026-01-22 12:56:42', '2026-01-22 12:56:42'),
(94, 130, 5, 700.00, 'upi', 'TXN17690883787726', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"hvb@gg\"}', '2026-01-22 13:26:18', '2026-01-22 13:26:18'),
(95, 131, 11, 5200.00, 'upi', 'TXN17690965914743', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"asd@jjj\"}', '2026-01-22 15:43:11', '2026-01-22 15:43:11'),
(96, 132, 11, 800.00, 'upi', 'TXN17690975016193', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"sad@aasfd\"}', '2026-01-22 15:58:21', '2026-01-22 15:58:21'),
(97, 134, 5, 5600.00, 'upi', 'TXN17697112351618', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"ass@jss\"}', '2026-01-29 18:27:15', '2026-01-29 18:27:15'),
(98, 135, 5, 349000.00, 'upi', 'TXN17714847537988', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"sdsd@sd\"}', '2026-02-19 07:05:53', '2026-02-19 07:05:53'),
(99, 136, 5, 7999.00, 'upi', 'TXN17714855001072', 'refunded', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"asd@dfsdaf\"}', '2026-02-19 07:18:20', '2026-02-19 07:18:20'),
(100, 137, 12, 5800.00, 'credit_card', 'TXN17715158429688', 'success', '{\"method\":\"credit_card\",\"masked_card\":\"1235\",\"upi_id\":null}', '2026-02-19 15:44:02', '2026-02-19 15:44:02'),
(102, 139, 13, 15998.00, 'upi', 'TXN17757454994054', 'success', '{\"method\":\"upi\",\"masked_card\":null,\"upi_id\":\"virat@okicici\"}', '2026-04-09 14:38:19', '2026-04-09 14:38:19'),
(103, 142, 13, 9500.00, 'credit_card', 'TXN17757464813773', 'success', '{\"method\":\"credit_card\",\"masked_card\":\"7229\",\"upi_id\":null}', '2026-04-09 14:54:41', '2026-04-09 14:54:41');

-- --------------------------------------------------------

--
-- Table structure for table `trains`
--

DROP TABLE IF EXISTS `trains`;
CREATE TABLE IF NOT EXISTS `trains` (
  `id` int NOT NULL AUTO_INCREMENT,
  `train_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `train_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `departure_station` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `arrival_station` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `departure_date` date NOT NULL,
  `arrival_date` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `available_seats` int DEFAULT '50',
  `total_seats` int DEFAULT '50',
  `class_type` enum('sleeper','3AC','2AC','1AC','chair_car') COLLATE utf8mb4_general_ci DEFAULT 'sleeper',
  `status` enum('active','inactive','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trains`
--

INSERT INTO `trains` (`id`, `train_name`, `train_number`, `departure_station`, `arrival_station`, `departure_time`, `arrival_time`, `departure_date`, `arrival_date`, `price`, `available_seats`, `total_seats`, `class_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Rajdhani Express', '12951', 'Mumbai Central', 'New Delhi', '16:25:00', '08:15:00', '2026-04-11', '2026-04-11', 2500.00, 37, 50, '3AC', 'active', '2025-12-12 04:08:05', '2026-04-09 14:51:45'),
(2, 'Shatabdi Express', '12009', 'Mumbai Central', 'Ahmedabad', '06:10:00', '12:25:00', '2026-04-11', '2026-04-11', 1500.00, 34, 50, 'chair_car', 'active', '2025-12-12 04:08:05', '2026-04-09 14:51:45'),
(3, 'Duronto Express', '12259', 'Mumbai', 'Bangalore', '23:05:00', '14:30:00', '2026-04-11', '2026-04-11', 1800.00, 40, 50, 'sleeper', 'active', '2025-12-12 04:08:05', '2026-04-09 14:51:45'),
(4, 'Konkan Express', '10111', 'Mumbai Central', 'Pune Junction', '07:00:00', '10:30:00', '2026-04-11', '2026-04-11', 850.00, 50, 50, 'chair_car', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(5, 'Mumbai Mail', '11021', 'Mumbai Central', 'Chennai Central', '20:30:00', '18:00:00', '2026-04-11', '2026-04-11', 2200.00, 50, 50, '3AC', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(6, 'Howrah Express', '12809', 'Mumbai Central', 'Howrah Junction', '15:00:00', '22:30:00', '2026-04-11', '2026-04-11', 2600.00, 50, 50, '3AC', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(7, 'Punjab Mail', '12137', 'New Delhi', 'Mumbai Central', '19:40:00', '06:25:00', '2026-04-11', '2026-04-11', 2400.00, 50, 50, '3AC', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(8, 'Capital Express', '12309', 'New Delhi', 'Howrah Junction', '16:55:00', '10:05:00', '2026-04-11', '2026-04-11', 2300.00, 50, 50, '3AC', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(9, 'Dakshin Express', '12625', 'New Delhi', 'Chennai Central', '22:30:00', '20:10:00', '2026-04-11', '2026-04-11', 2800.00, 50, 50, '2AC', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(10, 'Ashram Express', '12915', 'New Delhi', 'Ahmedabad Junction', '15:20:00', '07:10:00', '2026-04-11', '2026-04-11', 1900.00, 50, 50, '3AC', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(11, 'Deccan Queen', '12123', 'Pune Junction', 'Mumbai Central', '17:10:00', '20:25:00', '2026-04-11', '2026-04-11', 900.00, 50, 50, 'chair_car', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(12, 'Pune Howrah SF', '12129', 'Pune Junction', 'Howrah Junction', '22:00:00', '05:30:00', '2026-04-11', '2026-04-11', 2700.00, 50, 50, '3AC', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(13, 'Pune Chennai Exp', '11041', 'Pune Junction', 'Chennai Central', '18:45:00', '11:30:00', '2026-04-11', '2026-04-11', 2100.00, 50, 50, '3AC', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(14, 'Karnataka Express', '12627', 'Bangalore City', 'New Delhi', '19:20:00', '21:10:00', '2026-04-11', '2026-04-11', 2900.00, 50, 50, '2AC', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(15, 'Bangalore Mail', '12657', 'Bangalore City', 'Chennai Central', '22:40:00', '06:30:00', '2026-04-11', '2026-04-11', 750.00, 50, 50, 'sleeper', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(16, 'Bangalore Howrah Exp', '12863', 'Bangalore City', 'Howrah Junction', '10:15:00', '13:20:00', '2026-04-11', '2026-04-11', 2600.00, 50, 50, '3AC', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(17, 'Sabarmati Express', '19165', 'Ahmedabad Junction', 'Mumbai Central', '21:50:00', '07:30:00', '2026-04-11', '2026-04-11', 1700.00, 50, 50, '3AC', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(18, 'Ahmedabad Howrah Exp', '12834', 'Ahmedabad Junction', 'Howrah Junction', '23:10:00', '07:00:00', '2026-04-11', '2026-04-11', 2500.00, 50, 50, '3AC', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(19, 'Coromandel Express', '12841', 'Chennai Central', 'Howrah Junction', '08:45:00', '10:50:00', '2026-04-11', '2026-04-11', 2400.00, 50, 50, '3AC', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45'),
(20, 'Chennai Mail', '12163', 'Chennai Central', 'Mumbai Central', '18:20:00', '20:00:00', '2026-04-11', '2026-04-11', 2600.00, 50, 50, '3AC', 'active', '2025-12-27 12:23:30', '2026-04-09 14:51:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `role` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  `reset_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `created_at`, `role`, `reset_token`, `token_expiry`) VALUES
(5, 'mahavirsinh', 'ms@ms.com', '917201807642', '$2y$10$11UOOq7j9oBg9xKaMEa6IOQzbim1MkI7ih5b9L/lq8Ik.zlSu101W', '2025-12-10 20:02:27', 'user', 'db1e3c93917051f4844d0ae62debb4def316e7582fa003081d76630b2de65df0', '2025-12-16 11:10:58'),
(10, 'mahavirsinh sodha', 'mahavirsinh2302@gmail.com', '7201807642', '$2y$10$cYl3qP6gZmMTPw.IJQ8p8epFK4svRWs1Mu55d.CBRBv2zbXrvUdkm', '2025-12-16 10:57:54', 'user', '5598676b631d074f655874faa941779ffb1a0b92ae857b82751cb0ceffb4f413', '2025-12-16 11:15:12'),
(11, 'maulik raval', 'maulik@gmail.com', '1212121212', '$2y$10$BnGPg6OOx8kTBM5ST2XLL.26YhCC2gpaHQCLDsPMFf3HVmX4lZYAa', '2026-01-21 08:40:39', 'user', NULL, NULL),
(12, 'mscorp', 'mscorp7@ms.com', '0123456789', '$2y$10$stGlMpl0jdKgFAwMfOkQ2O.3eVgd3cBHSvXWGIODUhJIbgVvY.JvW', '2026-02-19 08:38:05', 'user', NULL, NULL),
(13, 'virat kohli', 'virat@oneeight.com', '9918455547', '$2y$10$p34ZQzeIhclRWFfsgONtI.i4fRKVVKEuTx.5bfiXdSOQYRvpqrdYm', '2026-04-09 14:34:23', 'user', NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
