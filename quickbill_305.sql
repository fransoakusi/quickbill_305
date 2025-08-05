-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
<<<<<<< HEAD
-- Generation Time: Jul 11, 2025 at 03:55 AM
=======
-- Generation Time: Jul 22, 2025 at 11:29 AM
>>>>>>> c9ccaba (Initial commit)
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quickbill_305`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'User Login', '', NULL, NULL, '{\"user_id\":1,\"username\":\"admin\",\"ip_address\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-05 02:33:15'),
(2, 1, 'Password Changed', '', NULL, NULL, '{\"user_id\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-05 02:33:51'),
(3, 1, 'User Logout', '', NULL, NULL, '{\"user_id\":1,\"session_duration\":17002}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-05 07:16:37'),
(4, 1, 'User Login', '', NULL, NULL, '{\"user_id\":1,\"username\":\"admin\",\"ip_address\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-05 07:17:39'),
(5, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-05 17:15:56'),
(6, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-05 17:19:40'),
(7, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-09 16:22:51'),
(8, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-09 17:23:08'),
(9, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-09 17:23:26'),
(10, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-09 18:27:58'),
(11, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-09 18:28:33'),
(12, 1, 'CREATE_USER', 'users', 3, NULL, '{\"username\":\"Joojo\",\"email\":\"kwadwomegas@gmail.com\",\"role_id\":1,\"first_name\":\"Joojo\",\"last_name\":\"Megas\",\"is_active\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-09 19:03:22'),
(13, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-09 19:07:33'),
(14, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 03:10:46'),
(15, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 04:25:10'),
(16, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 04:25:28'),
(17, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 08:58:55'),
(18, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 10:10:41'),
(19, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 10:11:07'),
(20, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 10:12:05'),
(21, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 10:12:20'),
(22, 3, 'PASSWORD_CHANGED', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 10:13:32'),
(23, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 13:16:52'),
(24, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 13:17:40'),
(25, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 14:16:10'),
(26, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 14:16:24'),
(27, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-10 14:47:11'),
(28, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-10 15:49:42'),
(29, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-10 15:52:51'),
(30, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 17:44:32'),
(31, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 17:47:32'),
(32, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 17:47:46'),
(33, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 18:56:11'),
(34, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 18:57:12'),
(35, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 19:59:30'),
(36, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-10 20:00:07'),
(37, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-10 20:02:07'),
<<<<<<< HEAD
(38, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 01:03:06');
=======
(38, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 01:03:06'),
(39, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 02:07:26'),
(40, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 02:08:10'),
(41, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-11 02:30:18'),
(42, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 09:16:12'),
(43, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 10:16:41'),
(44, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 10:17:14'),
(45, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-11 11:04:42'),
(46, 1, 'BILL_ADJUSTED', 'bills', 4, '{\"field\":\"current_bill\",\"old_value\":1000,\"old_amount_payable\":1000}', '{\"field\":\"current_bill\",\"new_value\":1100,\"new_amount_payable\":1100,\"adjustment_method\":\"Fixed Amount\",\"adjustment_value\":100,\"reason\":\"Due to the operation of the business\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-11 11:06:22'),
(47, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 11:20:11'),
(48, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 11:36:34'),
(49, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 11:52:24'),
(50, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 11:52:39'),
(51, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 15:20:04'),
(52, 1, 'CREATE_USER', 'users', 4, NULL, '{\"username\":\"Kusi\",\"email\":\"kusi@gmail.com\",\"role_id\":5,\"first_name\":\"Kusi\",\"last_name\":\"France\",\"is_active\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 15:21:00'),
(53, 4, 'USER_LOGIN', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-11 15:21:40'),
(54, 4, 'PASSWORD_CHANGED', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-11 15:22:17'),
(55, 4, 'USER_LOGOUT', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-11 16:32:47'),
(56, 4, 'USER_LOGIN', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-11 16:33:07'),
(57, 4, 'USER_LOGOUT', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-11 17:38:12'),
(58, 4, 'USER_LOGIN', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-11 17:38:38'),
(59, 4, 'USER_LOGOUT', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-11 18:41:20'),
(60, 4, 'USER_LOGIN', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-11 18:41:53'),
(61, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 18:55:11'),
(62, 4, 'USER_LOGOUT', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-11 20:17:30'),
(63, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 05:11:33'),
(64, 1, 'CREATE_USER', 'users', 5, NULL, '{\"username\":\"Aseye\",\"email\":\"aseyeabledoo@gmail.com\",\"role_id\":4,\"first_name\":\"Aseye\",\"last_name\":\"Abledu\",\"is_active\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 05:12:52'),
(65, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 05:13:38'),
(66, 5, 'PASSWORD_CHANGED', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 05:14:00'),
(69, 5, 'USER_LOGOUT', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 06:15:06'),
(70, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 07:16:32'),
(71, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 07:17:32'),
(72, 1, 'CREATE_USER', 'users', 6, NULL, '{\"username\":\"David\",\"email\":\"kabtechconsulting@gmail.com\",\"role_id\":3,\"first_name\":\"David\",\"last_name\":\"Lomko\",\"is_active\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 07:18:33'),
(73, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 07:20:10'),
(74, 6, 'PASSWORD_CHANGED', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 07:20:34'),
(75, 6, 'USER_LOGOUT', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 08:21:22'),
(76, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 08:21:52'),
(77, 6, 'USER_LOGOUT', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 09:26:14'),
(78, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 09:26:46'),
(79, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 09:37:48'),
(80, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 09:38:07'),
(81, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"specific\",\"billing_year\":2025,\"business_bills\":1,\"property_bills\":0,\"skipped_records\":0,\"total_generated\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 09:39:12'),
(82, 6, 'USER_LOGOUT', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 10:35:57'),
(83, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 10:36:33'),
(84, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 10:40:12'),
(85, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 10:40:27'),
(86, 6, 'USER_LOGOUT', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 11:40:56'),
(87, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 11:41:20'),
(88, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 11:46:21'),
(89, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 13:20:39'),
(90, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 14:08:59'),
(91, 3, '3', 'LIFT_SYSTEM_RESTRICTION', 0, '2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 14:41:52'),
(92, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 14:57:49'),
(93, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 14:58:02'),
(94, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 15:40:15'),
(95, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 15:40:47'),
(96, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 15:42:07'),
(97, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 16:31:21'),
(98, 5, 'USER_LOGOUT', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 17:43:09'),
(99, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 17:43:22'),
(101, 4, 'USER_LOGIN', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-12 18:27:30'),
(102, 3, 'BACKUP_CREATED', 'backup_logs', 1, NULL, '{\"type\":\"Full\",\"size\":52476}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 18:32:25'),
(103, 3, 'BACKUP_DOWNLOADED', 'backup_logs', NULL, NULL, '{\"filename\":\"quickbill_305_backup_2025-07-12_18-32-25.sql\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 18:32:40'),
(104, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 18:43:30'),
(105, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 18:46:40'),
(107, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-12 19:45:01'),
(108, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-12 19:46:53'),
(109, 3, 'UPDATE', 'business_fee_structure', 3, '{\"business_type\":\"Restaurant\",\"category\":\"Large Scale\",\"fee_amount\":\"2000.00\",\"is_active\":1}', '{\"business_type\":\"Restaurant\",\"category\":\"Large Scale\",\"fee_amount\":2100,\"is_active\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-12 19:58:34'),
(110, 3, 'UPDATE', 'properties', 1, '{\"property_id\":1,\"property_number\":\"PROP000001\",\"owner_name\":\"Yaw Kusi\",\"telephone\":\"0545051428\",\"gender\":\"Male\",\"location\":\"GPS: 5.593020, -0.077100\",\"latitude\":\"5.59302000\",\"longitude\":\"-0.07710000\",\"structure\":\"Modern Building\",\"ownership_type\":\"Self\",\"property_type\":\"Modern\",\"number_of_rooms\":3,\"property_use\":\"Residential\",\"old_bill\":\"0.00\",\"previous_payments\":\"150.00\",\"arrears\":\"0.00\",\"current_bill\":\"225.00\",\"amount_payable\":\"75.00\",\"batch\":\"\",\"zone_id\":2,\"created_by\":1,\"created_at\":\"2025-07-10 04:07:51\",\"updated_at\":\"2025-07-12 05:46:12\"}', '{\"owner_name\":\"Yaw Kusi\",\"telephone\":\"0545051428\",\"gender\":\"Male\",\"location\":\"GPS: 5.593020, -0.077100\",\"latitude\":\"5.59302000\",\"longitude\":\"-0.07710000\",\"structure\":\"Modern Building\",\"ownership_type\":\"Corporate\",\"property_type\":\"Modern\",\"number_of_rooms\":3,\"property_use\":\"Residential\",\"old_bill\":0,\"previous_payments\":150,\"arrears\":0,\"current_bill\":225,\"batch\":\"\",\"zone_id\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-12 20:17:28'),
(111, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-13 04:31:06'),
(112, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-13 05:20:22'),
(113, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-13 05:20:39'),
(114, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-13 07:43:26'),
(115, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-13 07:43:46'),
(116, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-13 08:18:40'),
(117, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-13 08:44:02'),
(118, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-13 08:44:26'),
(119, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-13 09:23:11'),
(120, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-13 09:23:40'),
(121, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-13 09:45:24'),
(122, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-13 09:45:53'),
(123, 6, 'USER_LOGOUT', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-11-12 14:04:41'),
(124, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-11-12 14:04:48'),
(125, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-11-12 14:05:54'),
(126, 3, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"all\",\"billing_year\":2026,\"business_bills\":5,\"property_bills\":3,\"skipped_records\":0,\"total_generated\":8}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-11-12 14:07:00'),
(127, 3, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"all\",\"billing_year\":2026,\"business_bills\":0,\"property_bills\":1,\"skipped_records\":0,\"total_generated\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-13 13:07:24'),
(128, 4, 'USER_LOGIN', 'users', 4, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-13 13:29:16'),
(129, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 08:34:47'),
(130, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 09:38:56'),
(131, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 09:39:13'),
(132, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 10:39:43'),
(133, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 10:40:10'),
(134, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 10:40:18'),
(135, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 10:40:40'),
(136, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-14 10:50:47'),
(138, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"specific\",\"billing_year\":2025,\"business_bills\":1,\"property_bills\":0,\"skipped_records\":0,\"total_generated\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 11:25:58'),
(139, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 11:45:55'),
(140, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 11:46:09'),
(141, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2026-02-01 11:49:54'),
(142, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2026-02-01 11:50:12'),
(143, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-14 11:54:46'),
(144, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 12:13:26'),
(145, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-14 12:37:55'),
(147, 1, 'Account search attempt - Number: BIZ000001, Type: Business', '', NULL, NULL, '\"INFO\"', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 13:04:00'),
(148, 1, 'Business found: KabTech Consulting', '', NULL, NULL, '\"INFO\"', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 13:04:00'),
(149, 1, 'Existing bill found: BILL2025B000006', '', NULL, NULL, '\"INFO\"', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 13:04:00'),
(150, 1, 'Account search successful', '', NULL, NULL, '\"INFO\"', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 13:04:00'),
(151, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 13:15:25'),
(152, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 13:15:39'),
(153, 5, 'USER_LOGOUT', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-14 13:26:08'),
(154, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-14 13:26:30'),
(155, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-14 13:50:30'),
(156, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-14 13:50:47'),
(158, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-14 14:12:02'),
(159, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 15:04:55'),
(160, 5, 'USER_LOGOUT', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-14 15:11:56'),
(161, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-14 15:39:57'),
(162, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-14 15:40:33'),
(163, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-14 15:40:58'),
(164, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-15 08:29:19'),
(165, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-15 09:48:42'),
(166, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-15 09:48:58'),
(167, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-15 11:24:09'),
(168, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-15 11:24:22'),
(169, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-15 12:41:42'),
(170, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-15 12:42:00'),
(171, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-15 12:45:54'),
(173, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-15 13:30:43'),
(174, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-15 13:30:57'),
(175, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-15 13:38:37'),
(176, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-15 14:38:51'),
(177, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-15 14:39:46'),
(181, 4, 'USER_LOGIN', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-15 14:47:31'),
(182, 4, 'USER_LOGOUT', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-15 14:48:00'),
(183, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-15 14:48:14'),
(184, 1, 'HARD_DELETE_USER', 'users', 2, '{\"username\":\"abismark\",\"email\":\"kabslink@gmail.com\",\"first_name\":\"Afful\",\"last_name\":\"Bismark\"}', '{\"deleted\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-15 15:04:07'),
(185, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 08:39:54'),
(186, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 08:40:09'),
(187, 5, 'USER_LOGOUT', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-16 08:41:07'),
(188, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 09:45:17'),
(189, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 09:45:27'),
(190, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 10:02:38'),
(191, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 10:21:34'),
(192, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 11:19:48'),
(193, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-16 11:20:45'),
(194, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"specific\",\"billing_year\":2025,\"business_bills\":1,\"property_bills\":0,\"skipped_records\":0,\"total_generated\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 11:28:25'),
(195, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 12:30:02'),
(196, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 12:33:15'),
(197, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"all\",\"billing_year\":2026,\"business_bills\":2,\"property_bills\":0,\"skipped_records\":1,\"total_generated\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 12:33:43'),
(198, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"all\",\"billing_year\":2025,\"business_bills\":0,\"property_bills\":0,\"skipped_records\":3,\"total_generated\":0}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 12:33:47'),
(199, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"all\",\"billing_year\":2025,\"business_bills\":0,\"property_bills\":0,\"skipped_records\":3,\"total_generated\":0}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 12:33:50'),
(200, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"all\",\"billing_year\":2026,\"business_bills\":0,\"property_bills\":0,\"skipped_records\":3,\"total_generated\":0}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 12:33:57'),
(201, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"all\",\"billing_year\":2026,\"business_bills\":0,\"property_bills\":0,\"skipped_records\":3,\"total_generated\":0}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 12:40:01'),
(202, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"all\",\"billing_year\":2026,\"business_bills\":0,\"property_bills\":0,\"skipped_records\":3,\"total_generated\":0}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 12:40:34'),
(203, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"all\",\"billing_year\":2025,\"business_bills\":0,\"property_bills\":0,\"skipped_records\":3,\"total_generated\":0}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 12:40:37'),
(204, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"all\",\"billing_year\":2026,\"business_bills\":0,\"property_bills\":0,\"skipped_records\":3,\"total_generated\":0}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 12:41:00'),
(205, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"specific\",\"billing_year\":2025,\"business_bills\":1,\"property_bills\":0,\"skipped_records\":0,\"total_generated\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 12:42:11'),
(206, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 14:27:46'),
(207, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 14:27:57'),
(208, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-16 14:45:09'),
(209, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 15:53:14'),
(210, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 15:53:34'),
(211, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"all\",\"billing_year\":2025,\"business_bills\":0,\"property_bills\":0,\"skipped_records\":4,\"total_generated\":0}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 16:10:28'),
(212, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-16 16:13:52'),
(213, 5, 'USER_LOGOUT', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-16 16:18:56'),
(214, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-16 16:19:00'),
(215, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-16 17:51:18'),
(216, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 17:51:52'),
(217, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 17:52:11'),
(218, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-16 18:09:19'),
(219, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 08:45:37'),
(220, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-17 09:45:02'),
(221, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 10:50:24'),
(222, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 10:51:33'),
(223, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"all\",\"billing_year\":2025,\"business_bills\":1,\"property_bills\":0,\"skipped_records\":0,\"total_generated\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 10:52:40'),
(224, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"all\",\"billing_year\":2026,\"business_bills\":1,\"property_bills\":0,\"skipped_records\":0,\"total_generated\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 10:56:30'),
(225, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 12:51:19'),
(226, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 12:51:28'),
(227, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"specific\",\"billing_year\":2025,\"business_bills\":1,\"property_bills\":0,\"skipped_records\":0,\"total_generated\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 12:56:53'),
(228, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"businesses\",\"billing_year\":2026,\"business_bills\":1,\"property_bills\":0,\"skipped_records\":0,\"total_generated\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 13:00:31'),
(229, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 13:59:42'),
(230, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 14:08:02'),
(231, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"specific\",\"billing_year\":2025,\"business_bills\":1,\"property_bills\":0,\"skipped_records\":0,\"total_generated\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 14:09:17'),
(232, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"businesses\",\"billing_year\":2026,\"business_bills\":1,\"property_bills\":0,\"skipped_records\":0,\"total_generated\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 14:11:17'),
(233, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"specific\",\"billing_year\":2025,\"business_bills\":1,\"property_bills\":0,\"skipped_records\":0,\"total_generated\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 14:19:07'),
(234, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"businesses\",\"billing_year\":2026,\"business_bills\":1,\"property_bills\":0,\"skipped_records\":1,\"total_generated\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 14:21:04'),
(235, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-17 15:36:42'),
(236, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-18 09:40:20'),
(237, 4, 'USER_LOGIN', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-18 09:45:22');
INSERT INTO `audit_logs` (`log_id`, `user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(238, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-18 09:52:10'),
(239, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"specific\",\"billing_year\":2025,\"business_bills\":1,\"property_bills\":0,\"skipped_records\":0,\"total_generated\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-18 09:57:59'),
(240, 6, 'USER_LOGOUT', 'users', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-18 10:42:54'),
(241, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-18 10:43:09'),
(242, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-18 10:50:06'),
(243, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-18 10:50:18'),
(244, 4, 'USER_LOGOUT', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-18 10:50:42'),
(245, 4, 'USER_LOGIN', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-18 10:51:18'),
(246, 5, 'USER_LOGOUT', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-18 10:59:01'),
(247, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-18 11:00:00'),
(248, 6, 'USER_LOGOUT', 'users', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-18 16:01:58'),
(249, 4, 'USER_LOGIN', 'users', 4, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-18 16:02:33'),
(250, 4, 'USER_LOGOUT', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-18 16:04:23'),
(251, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-18 16:45:48'),
(252, 5, 'USER_LOGOUT', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-19 03:15:09'),
(253, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-19 03:15:41'),
(254, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-19 03:21:28'),
(255, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-19 03:39:34'),
(256, 4, 'USER_LOGOUT', 'users', 4, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-19 03:40:38'),
(257, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-19 03:41:06'),
(258, 6, 'USER_LOGOUT', 'users', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-19 03:41:56'),
(259, 4, 'USER_LOGIN', 'users', 4, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-19 03:42:35'),
(260, 5, 'USER_LOGOUT', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-19 04:22:15'),
(261, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-19 04:22:58'),
(262, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-19 04:42:16'),
(263, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-19 04:42:39'),
(264, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-19 08:28:55'),
(265, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-19 09:29:33'),
(266, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-19 09:29:58'),
(267, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-19 13:09:25'),
(268, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-19 13:09:37'),
(269, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-19 13:28:15'),
(270, 5, 'USER_LOGOUT', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-19 14:07:40'),
(271, 4, 'USER_LOGIN', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-19 14:07:57'),
(272, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-19 14:14:55'),
(273, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-19 14:15:06'),
(274, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 05:05:19'),
(275, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 05:45:17'),
(276, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 06:55:40'),
(277, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 06:55:54'),
(278, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 09:11:07'),
(279, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 09:11:20'),
(280, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-20 09:13:38'),
(281, 4, 'USER_LOGIN', 'users', 4, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-20 09:15:09'),
(282, 1, 'IMPORT_FEES', 'business_fee_structure', NULL, NULL, '{\"type\":\"business\",\"success\":1,\"failed\":0,\"duplicates\":2,\"total_records\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 09:55:37'),
(283, 1, 'IMPORT_FEES', 'business_fee_structure', NULL, NULL, '{\"type\":\"business\",\"success\":413,\"failed\":0,\"duplicates\":2,\"total_records\":415}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 10:07:34'),
(284, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 10:08:42'),
(285, 6, 'USER_LOGOUT', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-20 10:47:33'),
(286, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-20 10:48:21'),
(287, 6, 'USER_LOGOUT', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-20 13:33:11'),
(288, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-20 13:33:25'),
(289, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 13:50:36'),
(290, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 13:50:47'),
(291, 1, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"businesses\",\"billing_year\":2025,\"business_bills\":1,\"property_bills\":0,\"skipped_records\":5,\"total_generated\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 13:57:30'),
(292, 6, 'USER_LOGOUT', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-20 14:38:18'),
(293, 6, 'USER_LOGIN', 'users', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-20 14:38:41'),
(294, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 15:07:05'),
(295, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 15:07:14'),
(296, 1, 'USER_LOGOUT', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 18:27:41'),
(297, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 18:27:45'),
(298, 5, 'USER_LOGIN', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-20 18:29:08'),
(299, 5, 'USER_LOGOUT', 'users', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-20 18:30:07'),
(300, 4, 'USER_LOGIN', 'users', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-20 18:30:42'),
(301, 4, 'CREATE_BUSINESS', 'businesses', 15, NULL, '{\"business_name\":\"Agoo Tv\",\"owner_name\":\"Kofi Nti\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-20 18:34:54'),
(305, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-20 19:00:25'),
(306, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 08:45:14'),
(307, 3, 'EMERGENCY_LIFT_RESTRICTIONS', 'system_restrictions', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 09:40:38'),
(308, 3, 'SET_SYSTEM_RESTRICTION', 'system_restrictions', NULL, NULL, '{\"start_date\":\"2025-07-21\",\"end_date\":\"2025-07-28\",\"warning_days\":7}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 09:41:06'),
(309, 1, 'USER_LOGIN', 'users', 1, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', '2025-07-21 09:41:39'),
(310, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 09:50:40'),
(311, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 09:50:46'),
(312, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 11:42:59'),
(313, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 11:43:10'),
(314, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 12:25:03'),
(315, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 13:26:56'),
(316, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 13:27:05'),
(317, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 14:35:34'),
(318, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 14:35:44'),
(319, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 15:18:49'),
(320, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-22 03:48:23'),
(321, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-22 07:33:19'),
(322, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-22 07:34:23'),
(323, 3, 'BILLS_GENERATED', 'bills', NULL, NULL, '{\"generation_type\":\"businesses\",\"billing_year\":2025,\"business_bills\":3,\"property_bills\":0,\"skipped_records\":0,\"total_generated\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-22 07:36:52'),
(324, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-22 09:04:48'),
(325, 3, 'USER_LOGOUT', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-22 09:18:28'),
(326, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-22 09:20:22');
>>>>>>> c9ccaba (Initial commit)

-- --------------------------------------------------------

--
-- Table structure for table `backup_logs`
--

CREATE TABLE `backup_logs` (
  `backup_id` int(11) NOT NULL,
  `backup_type` enum('Full','Incremental') NOT NULL,
  `backup_path` varchar(255) NOT NULL,
  `backup_size` bigint(20) DEFAULT NULL,
  `status` enum('In Progress','Completed','Failed') DEFAULT 'In Progress',
  `started_by` int(11) NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

<<<<<<< HEAD
=======
--
-- Dumping data for table `backup_logs`
--

INSERT INTO `backup_logs` (`backup_id`, `backup_type`, `backup_path`, `backup_size`, `status`, `started_by`, `started_at`, `completed_at`, `error_message`) VALUES
(1, 'Full', 'C:\\xampp\\htdocs\\quickbill_305/storage/backups/quickbill_305_backup_2025-07-12_18-32-25.sql', 52476, 'Completed', 3, '2025-07-12 18:32:25', '2025-07-12 18:32:25', NULL),
(2, 'Incremental', '../../storage/backups/quickbill_305_backup_2025-07-12_18-47-22.sql', 5978, 'Completed', 1, '2025-07-12 18:47:22', '2025-07-12 18:47:22', NULL),
(3, 'Full', '../../storage/backups/quickbill_305_backup_2025-07-20_18-55-11.sql', 20805, 'Completed', 1, '2025-07-20 18:55:11', '2025-07-20 18:55:12', NULL);

>>>>>>> c9ccaba (Initial commit)
-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `bill_id` int(11) NOT NULL,
  `bill_number` varchar(20) NOT NULL,
  `bill_type` enum('Business','Property') NOT NULL,
  `reference_id` int(11) NOT NULL,
  `billing_year` year(4) NOT NULL,
  `old_bill` decimal(10,2) DEFAULT 0.00,
  `previous_payments` decimal(10,2) DEFAULT 0.00,
  `arrears` decimal(10,2) DEFAULT 0.00,
  `current_bill` decimal(10,2) NOT NULL,
  `amount_payable` decimal(10,2) NOT NULL,
  `qr_code` text DEFAULT NULL,
  `status` enum('Pending','Paid','Partially Paid','Overdue') DEFAULT 'Pending',
  `generated_by` int(11) DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`bill_id`, `bill_number`, `bill_type`, `reference_id`, `billing_year`, `old_bill`, `previous_payments`, `arrears`, `current_bill`, `amount_payable`, `qr_code`, `status`, `generated_by`, `generated_at`, `due_date`) VALUES
<<<<<<< HEAD
(1, 'BIL-BIZ2025-51A36E98', 'Business', 2, '2025', 0.00, 0.00, 0.00, 1000.00, 1000.00, NULL, 'Pending', 3, '2025-07-10 19:31:38', NULL),
(2, 'BILL2025B000001', 'Business', 1, '2025', 0.00, 0.00, 0.00, 1000.00, 1000.00, NULL, 'Pending', 3, '2025-07-11 01:20:30', NULL),
(3, 'BILL2025P000001', 'Property', 1, '2025', 0.00, 0.00, 0.00, 225.00, 225.00, NULL, 'Pending', 3, '2025-07-11 01:20:30', NULL),
(4, 'BILL2024B000001', 'Business', 1, '2024', 0.00, 0.00, 0.00, 1000.00, 1000.00, NULL, 'Pending', 3, '2025-07-11 01:53:19', NULL);
=======
(22, 'BILL2025B000009', 'Business', 9, '2025', 0.00, 0.00, 0.00, 500.00, 300.00, NULL, 'Partially Paid', 1, '2025-07-17 10:52:40', NULL),
(23, 'BILL2026B000009', 'Business', 9, '2026', 300.00, 200.00, 100.00, 500.00, 600.00, NULL, 'Pending', 1, '2025-07-17 10:56:30', NULL),
(24, 'BILL2025B000010', 'Business', 10, '2025', 0.00, 0.00, 0.00, 1200.00, 700.00, NULL, 'Partially Paid', 1, '2025-07-17 12:56:53', NULL),
(25, 'BILL2026B000010', 'Business', 10, '2026', 700.00, 500.00, 200.00, 1200.00, 1400.00, NULL, 'Pending', 1, '2025-07-17 13:00:31', NULL),
(26, 'BILL2025B000011', 'Business', 11, '2025', 0.00, 0.00, 0.00, 1000.00, 500.00, NULL, 'Partially Paid', 1, '2025-07-17 14:09:17', NULL),
(27, 'BILL2026B000011', 'Business', 11, '2026', 1000.00, 500.00, 500.00, 1000.00, 1500.00, NULL, 'Pending', 1, '2025-07-17 14:11:17', NULL),
(28, 'BILL2025B000012', 'Business', 12, '2025', 0.00, 0.00, 0.00, 2100.00, 1100.00, NULL, 'Partially Paid', 1, '2025-07-17 14:19:07', NULL),
(29, 'BILL2026B000012', 'Business', 12, '2026', 2100.00, 1000.00, 1100.00, 2100.00, 3200.00, NULL, 'Pending', 1, '2025-07-17 14:21:04', NULL),
(30, 'BILL2025B000013', 'Business', 13, '2025', 0.00, 0.00, 0.00, 2100.00, 1600.00, NULL, 'Partially Paid', 1, '2025-07-18 09:57:59', NULL),
(31, 'BILL2025B000014', 'Business', 14, '2025', 0.00, 0.00, 0.00, 500.00, 500.00, NULL, 'Pending', 1, '2025-07-20 13:57:30', NULL),
(32, 'BILL2025B000015', 'Business', 15, '2025', 0.00, 0.00, 0.00, 80.00, 80.00, NULL, 'Pending', 3, '2025-07-22 07:36:52', NULL),
(33, 'BILL2025B000016', 'Business', 16, '2025', 0.00, 0.00, 0.00, 116.00, 116.00, NULL, 'Pending', 3, '2025-07-22 07:36:52', NULL),
(34, 'BILL2025B000017', 'Business', 17, '2025', 0.00, 0.00, 0.00, 67.00, 67.00, NULL, 'Pending', 3, '2025-07-22 07:36:52', NULL);
>>>>>>> c9ccaba (Initial commit)

-- --------------------------------------------------------

--
-- Table structure for table `bill_adjustments`
--

CREATE TABLE `bill_adjustments` (
  `adjustment_id` int(11) NOT NULL,
  `adjustment_type` enum('Single','Bulk') NOT NULL,
  `target_type` enum('Business','Property') NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `criteria` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`criteria`)),
  `adjustment_method` enum('Fixed Amount','Percentage') NOT NULL,
  `adjustment_value` decimal(10,2) NOT NULL,
  `old_amount` decimal(10,2) DEFAULT NULL,
  `new_amount` decimal(10,2) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `applied_by` int(11) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `businesses`
--

CREATE TABLE `businesses` (
  `business_id` int(11) NOT NULL,
  `account_number` varchar(20) NOT NULL,
  `business_name` varchar(200) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `business_type` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `exact_location` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `old_bill` decimal(10,2) DEFAULT 0.00,
  `previous_payments` decimal(10,2) DEFAULT 0.00,
  `arrears` decimal(10,2) DEFAULT 0.00,
  `current_bill` decimal(10,2) DEFAULT 0.00,
  `amount_payable` decimal(10,2) DEFAULT 0.00,
  `batch` varchar(50) DEFAULT NULL,
  `status` enum('Active','Inactive','Suspended') DEFAULT 'Active',
  `zone_id` int(11) DEFAULT NULL,
  `sub_zone_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `businesses`
--

INSERT INTO `businesses` (`business_id`, `account_number`, `business_name`, `owner_name`, `business_type`, `category`, `telephone`, `exact_location`, `latitude`, `longitude`, `old_bill`, `previous_payments`, `arrears`, `current_bill`, `amount_payable`, `batch`, `status`, `zone_id`, `sub_zone_id`, `created_by`, `created_at`, `updated_at`) VALUES
<<<<<<< HEAD
(1, 'BIZ000001', 'KabTech Consulting', 'Afful Bismark', 'Restaurant', 'Medium Scale', '', 'GPS: 5.593020, -0.077100', 5.59302000, -0.07710000, 0.00, 0.00, 0.00, 1000.00, 1000.00, '', 'Active', 1, 2, 1, '2025-07-10 03:16:20', '2025-07-10 03:16:20'),
(2, 'BIZ000002', 'Kwabena Ewusi Enterprise', 'Zayne Ewusi', 'Restaurant', 'Medium Scale', '0567823456', 'GPS: 5.593020, -0.077100', 5.59302000, -0.07710000, 0.00, 0.00, 0.00, 1000.00, 1000.00, '', 'Active', 2, 3, 1, '2025-07-10 09:00:51', '2025-07-10 09:00:51');
=======
(9, 'BIZ000001', 'Media General', 'Asante Bismark', 'I.T firm', 'Small', '', 'GPS: 5.593020, -0.077100', 5.59302000, -0.07710000, 300.00, 200.00, 100.00, 500.00, 700.00, '', 'Active', 1, 2, 1, '2025-07-17 10:52:16', '2025-07-17 10:56:30'),
(10, 'BIZ000010', 'Bel Aqua', 'Joojo Megas', 'Shop', 'Large Scale', '', 'GPS: 6.046429, 0.400600', 6.04642900, 0.40060000, 700.00, 500.00, 200.00, 1200.00, 1600.00, '', 'Active', 1, 1, 1, '2025-07-17 12:56:22', '2025-07-17 13:00:31'),
(11, 'BIZ000011', 'KabTech Consulting', 'Kusi Francis', 'Restaurant', 'Medium Scale', '', 'GPS: 5.593020, -0.077100', 5.59302000, -0.07710000, 1000.00, 500.00, 500.00, 1000.00, 2000.00, '', 'Active', 3, 4, 1, '2025-07-17 14:08:53', '2025-07-17 14:11:17'),
(12, 'BIZ000012', 'NTDA', 'Beatrice Akueteh', 'Restaurant', 'Large Scale', '', 'GPS: 5.593020, -0.077100', NULL, NULL, 2100.00, 1000.00, 1100.00, 2100.00, 3200.00, '', 'Active', 3, 4, 1, '2025-07-17 14:18:55', '2025-07-17 14:21:04'),
(13, 'BIZ000013', 'FK Farms', 'Yaw Kusi', 'Restaurant', 'Large Scale', '', 'GPS: 5.593020, -0.077100', 5.59302000, -0.07710000, 0.00, 500.00, 0.00, 2100.00, 2100.00, '', 'Active', 1, 2, 1, '2025-07-18 09:54:10', '2025-07-18 10:02:29'),
(14, 'BIZ000014', 'TV 3', 'Kofi Ansah', 'Restaurant', 'Small Scale', '+233545041428', 'Nungua, Adwoa Waakye', 5.59302000, -0.07710000, 0.00, 0.00, 0.00, 500.00, 500.00, '', 'Active', 1, 2, 1, '2025-07-19 13:22:24', '2025-07-19 13:22:24'),
(15, 'BIZ000015', 'Agoo Tv', 'Kofi Nti', 'Akpeteshie Sellers Only', 'Akpeteshie Sellers Only', '0545041428', 'GPS: 5.593020, -0.077100\r\nHWVF+37C, Accra, Ghana', 5.59302000, -0.07710000, 0.00, 0.00, 0.00, 80.00, 80.00, '', 'Active', 2, 3, 4, '2025-07-20 18:34:54', '2025-07-20 18:34:54'),
(16, 'BIZ000016', 'Spare Parts Sales Outlets (Second-hand)', 'Dave', 'Aluminium Pot Dealers (‘Dadesen’)', 'CAT B - Distributors', '+233545041428', 'Battor, North Tongu District, Volta Region, Ghana', 6.04642523, 0.40060000, 0.00, 0.00, 0.00, 116.00, 116.00, '', 'Active', 2, 3, 3, '2025-07-21 12:26:20', '2025-07-21 12:26:20'),
(17, 'BIZ000017', 'GTUC', 'Maame Ama', 'Arc Welders', 'CAT B - Light vehicle repairs, Container Shops, and Iron Gates', '', 'GPS: 5.593020, -0.077100 (±130m)', 5.59302000, -0.07710000, 0.00, 0.00, 0.00, 67.00, 67.00, '', 'Active', 2, 3, 3, '2025-07-22 07:35:39', '2025-07-22 07:35:39');
>>>>>>> c9ccaba (Initial commit)

--
-- Triggers `businesses`
--
DELIMITER $$
CREATE TRIGGER `calculate_business_payable` BEFORE INSERT ON `businesses` FOR EACH ROW BEGIN
<<<<<<< HEAD
    SET NEW.amount_payable = NEW.old_bill + NEW.arrears + NEW.current_bill - NEW.previous_payments;
=======
    -- Calculate arrears: old_bill - previous_payments (minimum 0)
    SET NEW.arrears = GREATEST(0, NEW.old_bill - NEW.previous_payments);
    
    -- Calculate amount_payable: arrears + current_bill
    SET NEW.amount_payable = NEW.arrears + NEW.current_bill;
>>>>>>> c9ccaba (Initial commit)
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `generate_business_account_number` BEFORE INSERT ON `businesses` FOR EACH ROW BEGIN
    IF NEW.account_number IS NULL OR NEW.account_number = '' THEN
        SET NEW.account_number = CONCAT('BIZ', LPAD(COALESCE((SELECT MAX(business_id) FROM businesses), 0) + 1, 6, '0'));
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_business_payable` BEFORE UPDATE ON `businesses` FOR EACH ROW BEGIN
<<<<<<< HEAD
    SET NEW.amount_payable = NEW.old_bill + NEW.arrears + NEW.current_bill - NEW.previous_payments;
=======
    -- Calculate arrears: old_bill - previous_payments (minimum 0)
    SET NEW.arrears = GREATEST(0, NEW.old_bill - NEW.previous_payments);
    
    -- Calculate amount_payable: arrears + current_bill
    SET NEW.amount_payable = NEW.arrears + NEW.current_bill;
>>>>>>> c9ccaba (Initial commit)
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `business_fee_structure`
--

CREATE TABLE `business_fee_structure` (
  `fee_id` int(11) NOT NULL,
  `business_type` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `fee_amount` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_fee_structure`
--

INSERT INTO `business_fee_structure` (`fee_id`, `business_type`, `category`, `fee_amount`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Restaurant', 'Small Scale', 500.00, 1, 1, '2025-07-04 18:57:35', '2025-07-10 14:52:25'),
(2, 'Restaurant', 'Medium Scale', 1000.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
<<<<<<< HEAD
(3, 'Restaurant', 'Large Scale', 2000.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(4, 'Shop', 'Small Scale', 300.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(5, 'Shop', 'Medium Scale', 600.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(6, 'Shop', 'Large Scale', 1200.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(7, 'Saloon', 'Large', 100.00, 1, 3, '2025-07-10 14:51:40', '2025-07-10 14:51:40');
=======
(3, 'Restaurant', 'Large Scale', 2100.00, 1, 1, '2025-07-04 18:57:35', '2025-07-12 19:58:34'),
(4, 'Shop', 'Small Scale', 300.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(5, 'Shop', 'Medium Scale', 600.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(6, 'Shop', 'Large Scale', 1200.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(7, 'Saloon', 'Large', 100.00, 1, 3, '2025-07-10 14:51:40', '2025-07-10 14:51:40'),
(8, 'I.T firm', 'Small', 500.00, 1, 3, '2025-07-16 11:27:11', '2025-07-16 11:27:11'),
(9, 'Salon', 'Large Scale', 1200.00, 1, 1, '2025-07-20 09:55:37', '2025-07-20 09:55:37'),
(10, 'Abattoir (Private)', 'CAT F - Slaughter House (Small)', 146.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(11, 'Abattoir (Private)', 'CAT G - Others', 67.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(12, 'Adinkra Designers/Kente/Smock Weavers & Sellers', 'CAT H - Weavers Only (Small)', 40.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(13, 'Adinkra Designers/Kente/Smock Weavers & Sellers', 'CAT I - Sellers Only (Small)', 67.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(14, 'Agro Chemical/Farm Inputs Dealers', 'CAT H - Distributors', 160.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(15, 'Agro Chemical/Farm Inputs Dealers', 'CAT I - Retailers', 93.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(16, 'Agro Machine Dealers', 'CAT I - Retailers', 133.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(17, 'Akpeteshie (liquor) Dealers  Manufacturers (Distillers)', 'CAT B - Medium Scale', 133.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(18, 'Akpeteshie (liquor) Dealers  Manufacturers (Distillers)', 'CAT C - Small Scale', 67.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(19, 'Home Based/Farm Site', 'CAT A - Pito (daily brewing)', 40.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(20, 'Home Based/Farm Site', 'CAT B - Palm Wine Tappers', 40.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(21, 'Akpeteshie Sellers Only', 'Akpeteshie Sellers Only', 80.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(22, 'Aluminium Fabricators (Doors/Windows)', 'CAT B-Medium Scale', 385.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(23, 'Aluminium Fabricators (Doors/Windows)', 'CAT C - Small Scale', 160.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(24, 'Aluminium Fabricators (Doors/Windows)', 'CAT D - Others', 117.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(25, 'Aluminium Pot Dealers (‘Dadesen’)', 'CAT B - Distributors', 116.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(26, 'Aluminium Pot Dealers (‘Dadesen’)', 'CAT C - Retailers', 110.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(27, 'Aluminium Product Distributors', 'CAT B - Medium Scale', 220.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(28, 'Aluminium Product Distributors', 'CAT C - Small Scale', 146.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(29, 'Aluminium Product Retailers', 'Category B - Container/Kiosk (Medium)', 100.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(30, 'Aluminium Product Retailers', 'Category C - Table Top (Small)', 67.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(31, 'Ambulance Service Providers', 'CAT B - Medium Scale (3-5 Vehicles)', 133.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(32, 'Ambulance Service Providers', 'CAT C - Small Scale (1-2 Vehicles)', 61.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(33, 'Arts & Handicraft Dealers', 'CAT F - Retailers Only', 133.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(34, 'Arts & Handicraft Dealers', 'CAT G - Container/Kiosk', 102.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(35, 'Arts & Handicraft Dealers', 'CAT H - Table Top', 61.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(36, 'Artisans e.g., Masons, Carpenters, Plumbers, Electricians, Painters, Steel Benders, Tile Layers etc.', 'CAT \'A\'', 67.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(37, 'Artisans e.g., Masons, Carpenters, Plumbers, Electricians, Painters, Steel Benders, Tile Layers etc.', 'CAT \'B\'', 39.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(38, 'Air Condition Mechanics', 'CAT C - Informal Garage without Shop', 160.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(39, 'Air Condition Mechanics', 'CAT D - Others', 67.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(40, 'Arc Welders', 'CAT A - Fuel and Water Tankers; Truck Builders', 160.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(41, 'Arc Welders', 'CAT B - Light vehicle repairs, Container Shops, and Iron Gates', 67.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(42, 'Argon (Aluminium) Welders', 'Argon (Aluminium) Welders', 44.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(43, 'Auto Body Repairers', 'CAT C - Informal Garage without Shop', 160.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(44, 'Auto Body Repairers', 'CAT D - Others', 117.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(45, 'Auto Electricians', 'CAT A - With Battery Charging', 110.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(46, 'Auto Electricians', 'CAT B - Without Battery Charging', 41.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(47, 'Auto Mechanics', 'CAT B - Heavy Duty Trucks', 97.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(48, 'Auto Mechanics', 'CAT C - Light Duty Trucks (3 to 5 tonnes)', 89.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(49, 'Auto Mechanics', 'CAT D - Light Duty Vehicles (Below 3 tonnes)', 81.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(50, 'Auto Sprayers', 'CAT A - Spraying with Oven', 315.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(51, 'Auto Sprayers', 'CAT B - Spraying without Oven', 293.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(52, 'Auto Upholstery', 'CAT A - Moulding and Seat Cover Sewing', 110.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(53, 'Auto Upholstery', 'CAT B - Seat Cover Sewing', 59.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(54, 'Vulcanisers', 'CAT C - Wheel Balancing and Alignment', 106.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(55, 'Vulcanisers', 'CAT D - Tyre Repairs only', 66.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(56, 'Brake Specialist', 'Brake Specialist', 67.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(57, 'Carburettor Specialist', 'Carburettor Specialist', 67.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(58, 'Plastic Welders and Fabricators', 'Plastic Welders and Fabricators', 67.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(59, 'Radiator Specialist', 'Radiator Specialist', 67.00, 1, 1, '2025-07-20 10:07:31', '2025-07-20 10:07:31'),
(60, 'Multiple Service Providers for Auto Works', 'CAT B-Medium Scale', 110.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(61, 'Multiple Service Providers for Auto Works', 'CAT C - Small Scale', 99.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(62, 'Windscreen Repairers', 'CAT C- Small scale', 99.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(63, 'Bolt and Nut Dealers', 'CAT A - Shop', 93.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(64, 'Bolt and Nut Dealers', 'CAT B - Table Top', 67.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(65, 'Spare Parts Sales Outlets (Secondhand)', 'CAT A - Second-Hand Engine Shops', 532.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(66, 'Spare Parts Sales Outlets (Secondhand)', 'CAT B - Retailers (Large)', 308.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(67, 'Spare Parts Sales Outlets (Secondhand)', 'CAT C - Retailers (Medium)', 187.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(68, 'Spare Parts Sales Outlets (Secondhand)', 'CAT D - Retailers (Small)', 106.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(69, 'Spare Parts Sales Outlets (Secondhand)', 'CAT E - Kiosk/Tabletop', 77.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(70, 'Tyre/Battery Dealers – Used', 'CAT B - Retail (Large)', 160.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(71, 'Tyre/Battery Dealers – Used', 'CAT C - Retail (Medium)', 133.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(72, 'Tyre/Battery Dealers – Used', 'CAT D - Retail (Small)', 100.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(73, 'Barbering Shop (floor space and number of points)', 'CAT B - Medium Shop (3-5 points)', 133.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(74, 'Barbering Shop (floor space and number of points)', 'CAT C - Small Shop (1-2 points)', 70.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(75, 'Barbering Shop (floor space and number of points)', 'CAT D - Mobile Operators', 17.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(76, 'Barbering Shop (floor space and number of points)', 'CAT E - Barbering accessory shop', 44.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(77, 'Bakeries', 'CAT A - Large Scale (Industrial operations)', 319.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(78, 'Bakeries', 'CAT B - Medium Scale', 160.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(79, 'Bakeries', 'CAT C - Small Scale', 80.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(80, 'Beads Dealers', 'CAT B', 83.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(81, 'Beads Dealers', 'CAT C', 55.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(82, 'Beads Dealers', 'CAT D - Retailers (Tabletop)', 33.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(83, 'Alcoholic and Non-Alcoholic beverages', 'CAT D - Retail (Large)', 400.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(84, 'Alcoholic and Non-Alcoholic beverages', 'CAT E - Retail (Medium)', 133.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(85, 'Alcoholic and Non-Alcoholic beverages', 'CAT F - Retail (Small)', 70.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(86, 'Bet & Game Centres Sports Betting Operations', 'CAT A - Online Betting', 2200.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(87, 'Route Operations (Console/consul Games)', 'CAT ‘C’', 1198.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(88, 'Route Operations (Console/consul Games)', 'CAT D (Per Machine)', 266.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(89, 'Bicycles/Tricycles/Motorcycle Dealers (Second-hand)', 'CAT C - Small Scale', 133.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(90, 'Bicycles/Tricycles/Motorcycles Parts Sales', 'CAT C - Motorcycle/Tricycles', 152.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(91, 'Bicycles/Tricycles/Motorcycles Parts Sales', 'CAT D - Bicycle with Parts', 120.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(92, 'Bicycles/Tricycles/Motorcycles Parts Sales', 'CAT E - Bicycles', 67.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(93, 'Bicycle Tricycle/ Motorcycle Repairers', 'CAT A - Tricycle', 66.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(94, 'Bicycle Tricycle/ Motorcycle Repairers', 'CAT B - Motorcycle', 39.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(95, 'Bicycle Tricycle/ Motorcycle Repairers', 'CAT C - Bicycle', 13.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(96, 'Billboards/Outdoor Adverts (e.g., Road Arches, Unipole spectacular, LEDs, Building wrap or Wall Drap', 'CAT A - Class A1 (Along First Class Access)', 37.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(97, 'Billboards/Outdoor Adverts (e.g., Road Arches, Unipole spectacular, LEDs, Building wrap or Wall Drap', 'CAT B - Class A2 (Along Second Class Access)', 29.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(98, 'Billboards/Outdoor Adverts (e.g., Road Arches, Unipole spectacular, LEDs, Building wrap or Wall Drap', 'CAT C - Class A3 (Along Third Class Access)', 25.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(99, 'Other Adverts', 'Other Adverts', 100.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(100, 'Blacksmith', 'Blacksmith', 73.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(101, 'Blocks & Concrete Producers', 'Blocks & Concrete Producers', 74.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(102, 'CAT E - Small Scale (Manual design blocks/columns only)', 'CAT E - Small Scale (Manual design blocks/columns only)', 75.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(103, 'Book, Stationery, Office Equipment, Computer & Accessory, etc. Shops', 'CAT H - Office Equipment Only', 300.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(104, 'Book, Stationery, Office Equipment, Computer & Accessory, etc. Shops', 'CAT I - Stationery Only', 200.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(105, 'Book, Stationery, Office Equipment, Computer & Accessory, etc. Shops', 'CAT J - Books Only', 73.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(106, 'Book, Stationery, Office Equipment, Computer & Accessory, etc. Shops', 'CAT K - Table Top/Truck Pusher', 27.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(107, 'Book, Stationery, Office Equipment, Computer & Accessory, etc. Shops', 'CAT L - Mobile Vans', 44.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(108, 'Boutiques (Including African Wear)', 'CAT B - Small Scale', 400.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(109, 'Boutiques (Including African Wear)', 'CAT C - Small Scale', 242.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(110, 'Boutiques (Including African Wear)', 'CAT D - Branches of CAT A & B', 182.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(111, 'Boutiques (Including African Wear)', 'CAT E - Others', 85.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(112, 'Bridal Homes', 'CAT ‘B’', 399.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(113, 'Bridal Homes', 'CAT \'C\'', 200.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(114, 'Butcher\'s Licence', 'CAT B - Butcher', 88.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(115, 'Building Material Dealers Hardware (Distributor/ Wholesaler)', 'CAT A - Distributor/Wholesaler (Large)', 2782.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(116, 'Building Material Dealers Hardware (Distributor/ Wholesaler)', 'CAT B - Distributor/Wholesaler (Medium)', 1171.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(117, 'Building Material Dealers Hardware (Distributor/ Wholesaler)', 'CAT C - Wholesaler (Medium)', 732.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(118, 'Building Material Dealers Hardware (Distributor/ Wholesaler)', 'CAT D - Wholesaler (Small)', 395.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(119, 'Finishing/Retail', 'CAT A - Large Scale', 666.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(120, 'Finishing/Retail', 'CAT B - Medium Scale', 395.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(121, 'Finishing/Retail', 'CAT C - Small Scale', 293.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(122, 'Finishing/Retail', 'CAT D - Very Small Scale', 146.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(123, 'Roofing Material Dealers', 'CAT A - Manufacture/Sales', 666.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(124, 'Roofing Material Dealers', 'CAT B - Sales & Installation', 399.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(125, 'Business Centres', 'CAT D - Internet, Word Processing, Printing and Copying Services plus below 11 Workstations', 146.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(126, 'Business Centres', 'CAT E - Secretarial Services (Word Processing, Printing and Copying Services)', 102.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(127, 'Cane Product Weavers', 'CAT ‘B’', 41.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(128, 'Cane Product Weavers', 'CAT \'C\'', 32.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(129, 'Car Washing Bay', 'CAT C - Jet Washing Only', 117.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(130, 'Car Washing Bay', 'CAT E - Manual Washing with other Facilities', 73.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(131, 'Car Washing Bay', 'CAT F - Manual Washing', 44.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(132, 'Canopy Producers', 'CAT B - Metal Fabricators', 133.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(133, 'Canopy Producers', 'CAT C - Fabric Sewing', 53.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(134, 'Carpentry Workshops', 'CAT C - Furniture (plus upholstery - Medium)', 102.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(135, 'Carpentry Workshops', 'CAT D - Furniture (plus upholstery - Small)', 81.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(136, 'Carpentry Workshops', 'CAT E - Minor Works', 64.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(137, 'Casket & Coffin Dealers', 'CAT C - Industrial (Manufacture & Sale)', 363.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(138, 'Casket & Coffin Dealers', 'CAT C - Manual (Manufacture & Sale)', 303.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(139, 'Casket & Coffin Dealers', 'CAT D - Sales Outlets', 110.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(140, 'Casket & Coffin Dealers', 'CAT E - Manufacturers Only', 99.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(141, 'Contracted Caterers (e.g., School Feeding)', 'CAT B - Above 500 to 1,000 pupils', 161.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(142, 'Contracted Caterers (e.g., School Feeding)', 'CAT C - Up to 500 pupils', 122.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(143, 'Ceremonial Hiring Services', 'CAT C - Canopies, Chairs, Tables', 399.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(144, 'Ceremonial Hiring Services', 'CAT D - Single Item of above', 99.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(145, 'Ceremonial Hiring Services', 'CAT H - Spinners (Large)', 200.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(146, 'Ceremonial Hiring Services', 'CAT I - Spinners (Small)', 99.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(147, 'Ceremonial Hiring Services', 'CAT J - Live Band', 333.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(148, 'Ceremonial Hiring Services', 'CAT K - Musical/Dance Groups', 133.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(149, 'Cleaning Companies', 'CAT B - Household/Office (Medium)', 700.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(150, 'Cleaning Companies', 'CAT C - Household/Office (Small)', 600.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(151, 'Cleaning Companies', 'CAT D - Household/Office (Very Small)', 200.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(152, 'Cold Storage Faccilities Non-Importers with Containerised Cold Storage Facilities (Local)', 'CAT D - Fabricated facility (Retail Large)', 200.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(153, 'Cold Storage Faccilities Non-Importers with Containerised Cold Storage Facilities (Local)', 'CAT E - Fabricated facility (Retail Medium)', 110.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(154, 'Cold Storage Faccilities Non-Importers with Containerised Cold Storage Facilities (Local)', 'CAT F - Fabricated facility (Retail Small)', 53.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(155, 'Stores Commercial Houses/Departmental', 'CAT G - Neighbourhood Shops (Large Size)', 399.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(156, 'Stores Commercial Houses/Departmental', 'CAT H - Neighbourhood Shops (Medium Size)', 266.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(157, 'Stores Commercial Houses/Departmental', 'CAT I - Neighbourhood Shops (Small Size)', 99.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(158, 'Stores Commercial Houses/Departmental', 'CAT J - Neighbourhood Shops (Very Small Size)', 66.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(159, 'Commercial Houses/Departmental', 'CAT C - Branch Offices', 7700.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(160, 'Commercial Houses/Departmental', 'CAT D - District Offices', 4950.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(161, 'Commercial Houses/Departmental', 'CAT E - Local Offices', 2200.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(162, 'Commissioner of Oath/Letter Writers', 'CAT ‘A’', 99.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(163, 'Commissioner of Oath/Letter Writers', 'CAT ‘B’', 66.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(164, 'Communication Mast Operating License', 'CAT A - 1-10 Masts', 2420.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(165, 'Communication Mast Operating License', 'CAT B - 11-20 Masts', 1980.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(166, 'Communication Mast Operating License', 'CAT C - 21-30 Masts', 1650.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(167, 'Communication Mast Operating License', 'CAT D - 31-40 Masts', 1100.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(168, 'Communication Mast Operating License', 'CAT E - Above 40 Masts', 792.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(169, 'Cooking/Household Utensil Sales', 'CAT ‘A’', 75.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(170, 'Cooking/Household Utensil Sales', 'CAT ‘B’', 54.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(171, 'Cooking/Household Utensil Sales', 'CAT ‘C’', 35.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(172, 'Cosmetic/Personal Care/Hair Product sale', 'CAT C - Retail', 121.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(173, 'Cosmetic/Personal Care/Hair Product sale', 'CAT D - Table top', 40.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(174, 'Curtains/Carpets etc. Sales', 'CAT ‘A’', 133.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(175, 'Curtains/Carpets etc. Sales', 'CAT ‘B’', 93.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(176, 'Curtains/Carpets etc. Sales', 'CAT ‘C’', 67.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(177, 'Disposable Products Dealers', 'CAT A - Wholesalers/Retailers', 200.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(178, 'Disposable Products Dealers', 'CAT B - Retailers (Medium)', 133.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(179, 'Disposable Products Dealers', 'CAT C - Retailers (Small)', 80.00, 1, 1, '2025-07-20 10:07:32', '2025-07-20 10:07:32'),
(180, 'Dressmakers/Tailors (Non-Industrial)', 'CAT A - Large Scale', 96.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(181, 'Dressmakers/Tailors (Non-Industrial)', 'CAT B - Medium Scale', 80.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(182, 'Dressmakers/Tailors (Non-Industrial)', 'CAT C - Small scale', 64.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(183, 'Dressmakers/Tailors Services', 'CAT A - Knitting, Haberdashery and Embroidery', 93.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(184, 'Dressmakers/Tailors Services', 'CAT B - Knitting and Embroidery', 67.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(185, 'Dressmakers/Tailors Services', 'CAT C - Embroidery only', 40.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(186, 'Dressmakers/Tailors Services', 'CAT D - Knitting only', 27.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(187, 'Dressmakers/Tailors Services', 'CAT E - Haberdashery Only (sewingitems -    large)', 133.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(188, 'Dressmakers/Tailors Services', 'CAT F - Haberdashery Only (Small)', 67.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(189, 'Driving Schools', 'CAT A - Above 6 Vehicles', 182.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(190, 'Driving Schools', 'CAT B - 4 - 6 Vehicles', 146.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(191, 'Driving Schools', 'CAT C - 1 - 3 Vehicles', 106.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(192, 'Educational Institutions – Private Day Care Centres (Early Childhood Development Centres)', 'CAT A - Grade A', 666.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(193, 'Educational Institutions – Private Day Care Centres (Early Childhood Development Centres)', 'CAT B - Grade B', 395.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(194, 'Educational Institutions – Private Day Care Centres (Early Childhood Development Centres)', 'CAT C - Grade C', 133.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(195, 'Pre-Tertiary Schools Basic School (KG/Primary/Junior High Schools) National Curriculum Operators', 'CAT B - KG/Primary/Junior High Schools (Medium)', 1210.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(196, 'Pre-Tertiary Schools Basic School (KG/Primary/Junior High Schools) National Curriculum Operators', 'CAT C - KG/Primary/Junior High Schools (Small)', 545.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(197, 'Pre-Tertiary Schools Basic School (KG/Primary/Junior High Schools) National Curriculum Operators', 'CAT D - KG/Primary (Large)', 424.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(198, 'Pre-Tertiary Schools Basic School (KG/Primary/Junior High Schools) National Curriculum Operators', 'CAT E - KG/Primary (Medium)', 242.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(199, 'Pre-Tertiary Schools Basic School (KG/Primary/Junior High Schools) National Curriculum Operators', 'CAT F - KG/Primary (Small)', 121.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(200, 'Secondary Level (Senior) High/Technical/ Vocational Schools)', 'CAT A - Large', 878.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(201, 'Secondary Level (Senior) High/Technical/ Vocational Schools)', 'CAT B - Medium', 586.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(202, 'Secondary Level (Senior) High/Technical/ Vocational Schools)', 'CAT C - Small', 439.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(203, 'Basic to Secondary School', 'CAT A - Large', 1210.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(204, 'Basic to Secondary School', 'CAT B - Medium', 847.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(205, 'Specialised Schools (Remedial School)', 'CAT \'A\'', 1065.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(206, 'Specialised Schools (Remedial School)', 'CAT \'B\'', 399.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(207, 'Training & Vocational Institutions (Media, Construction, Fashion, Floral, Catering, Cosmetology & We', 'CAT \'A\'', 293.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(208, 'Training & Vocational Institutions (Media, Construction, Fashion, Floral, Catering, Cosmetology & We', 'CAT \'B\'', 293.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(209, 'Training & Vocational Institutions (Media, Construction, Fashion, Floral, Catering, Cosmetology & We', 'CAT \'C\'', 200.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(210, 'Training & Vocational Institutions (Media, Construction, Fashion, Floral, Catering, Cosmetology & We', 'CAT \'D\'', 200.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(211, 'Egg Dealers', 'CAT A - Wholesale', 110.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(212, 'Egg Dealers', 'CAT B - Retail Shops', 73.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(213, 'Egg Dealers', 'CAT C - Mobile Retail', 44.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(214, 'Electrical Appliances (New & Secondhand)', 'CAT B - Wholesalers', 303.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(215, 'Electrical Appliances (New & Secondhand)', 'CAT C - Retailers (Large)', 182.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(216, 'Electrical Appliances (New & Secondhand)', 'CAT D - Retailers - (Small)', 133.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(217, 'Electrical Appliances (New & Secondhand)', 'CAT E - Table Top', 80.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(218, 'Electronic/Home Appliances/Shops (New\n& Second Hand)', 'CAT H - Retailers (Tabletop - Medium)', 266.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(219, 'Electronic/Home Appliances/Shops (New\n& Second Hand)', 'CAT I - Retailers (Tabletop - Small)', 153.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(220, 'Fabric Dealers – Sales', 'CAT C - Wholesale', 184.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(221, 'Fabric Dealers – Sales', 'CAT D - Retail', 108.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(222, 'Fabric Dealers – Sales', 'CAT E - Table Top', 67.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(223, 'Feed Sellers (Poultry, Pets, Fish etc.)', 'CAT A - Wholesalers', 220.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(224, 'Feed Sellers (Poultry, Pets, Fish etc.)', 'CAT B - Distributors', 143.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(225, 'Feed Sellers (Poultry, Pets, Fish etc.)', 'CAT C - Retailers', 99.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(226, 'Financial Institutions (Banking) Other Financial Institutions Microfinance Companies (Deposit Taking', 'CAT A - Head Office', 1452.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(227, 'Financial Institutions (Banking) Other Financial Institutions Microfinance Companies (Deposit Taking', 'CAT B - Branch', 605.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(228, 'Microcredit/Money Lenders/Credit Union', 'CAT A - Head Office', 1452.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(229, 'Microcredit/Money Lenders/Credit Union', 'CAT B - Branch', 605.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(230, 'Microcredit/Money Lenders/Credit Union', 'CAT C - Tier 4 Operators', 242.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(231, 'Rural & Community Banks', 'CAT A - Head Office', 2750.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(232, 'Rural & Community Banks', 'CAT B - Branch', 448.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(233, 'Rural & Community Banks', 'CAT C - Agency', 303.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(234, 'Rural & Community Banks', 'CAT D - Mobilisation Centre', 145.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(235, 'Financial Technology Companies (FINTECH)', 'CAT B Mobile Money Vendor Large', 1210.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(236, 'Financial Technology Companies (FINTECH)', 'CAT C Mobile Money Vendor Medium', 303.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(237, 'Financial Technology Companies (FINTECH)', 'CAT D Mobile Money Vendor Small', 182.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(238, 'Stand Alone ATMs', 'Stand Alone ATMs', 500.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(239, 'Insurance Companies Non-Life Insurance', 'Cat-C-Agency', 700.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(240, 'Life Insurance Life Insurance', 'Cat- C-Agency', 605.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(241, 'Fish Farming Companies', 'CAT C- International Small Scale', 6050.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(242, 'Fish Farming Companies', 'CAT D - Local Large Scale', 266.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(243, 'Fish Farming Companies', 'CAT E - Local Medium Scale', 146.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(244, 'Fish Farming Companies', 'CAT F - Local Small Scale', 67.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(245, 'Fishing Nets and Accessories Dealers', 'CAT B - Wholesalers', 363.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(246, 'Fishing Nets and Accessories Dealers', 'CAT C - Retailers', 133.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(247, 'Fishing Nets and Accessories Dealers', 'CAT D - Table Top', 44.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(248, 'Footwear Sales - New', 'CAT ‘A’', 133.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(249, 'Footwear Sales - New', 'CAT ‘B’', 93.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(250, 'Footwear Sales - New', 'CAT ‘C’', 67.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(251, 'Footwear Sales - Used', 'CAT ‘A’', 100.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(252, 'Footwear Sales - Used', 'CAT ‘B’', 67.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(253, 'Footwear Sales - Used', 'CAT ‘C’', 36.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(254, 'Footwear Repairers (Cobblers)', 'CAT ‘A’', 47.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(255, 'Footwear Repairers (Cobblers)', 'CAT ‘B’', 29.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(256, 'Funeral – Undertaker’s Licence Hearse Service Providers', 'CAT A - Large Scale (Above 5 Vehicles)', 440.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(257, 'Funeral – Undertaker’s Licence Hearse Service Providers', 'CAT B - Medium Scale (3-5 Vehicles)', 220.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(258, 'Funeral – Undertaker’s Licence Hearse Service Providers', 'CAT C - Small Scale (1-2 Vehicles)', 110.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(259, 'Furniture Showroom', 'CAT -C', 605.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(260, 'Furniture Showroom', 'CAT-D', 363.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(261, 'Game Viewing/Commercial TV Viewing Centres', 'CAT B - Medium Scale', 133.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(262, 'Game Viewing/Commercial TV Viewing Centres', 'CAT C - Small Scale', 87.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(263, 'Gas Cylinder/ Stoves & Accessory Dealers', 'CAT A - Large Scale', 160.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(264, 'Gas Cylinder/ Stoves & Accessory Dealers', 'CAT B - Medium Scale', 100.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(265, 'Gas Cylinder/ Stoves & Accessory Dealers', 'CAT C - Small Scale', 67.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(266, 'General Goods - Sales (e.g., Generator, Water pump, Chain saw, etc.)', 'CAT C - Small Scale', 322.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(267, 'General Goods - Sales (e.g., Generator, Water pump, Chain saw, etc.)', 'CAT D - Individuals', 293.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(268, 'Gift Shops', 'CAT B-Medium Scale', 120.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(269, 'Gift Shops', 'CAT C - Small Scale', 59.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(270, 'Glass Sellers (Tinted /Plain)', 'CAT A - Large Scale', 666.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(271, 'Glass Sellers (Tinted /Plain)', 'CAT B - Medium Scale', 586.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(272, 'Glass Sellers (Tinted /Plain)', 'CAT C - Small Scale', 293.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(273, 'GRAPHIC DESIGN COMPANIES', 'CAT B Medium Scale', 605.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(274, 'GRAPHIC DESIGN COMPANIES', 'CAT C Small Scale', 440.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(275, 'GRAPHIC DESIGN COMPANIES', 'CAT D Individuals', 275.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(276, 'Hair & Beauty Service Providers', 'CAT D - Big Salon', 133.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(277, 'Hair & Beauty Service Providers', 'CAT E - Small Salon', 73.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(278, 'Hair & Beauty Service Providers', 'CAT F - Braiding and Weaving Only', 146.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(279, 'Hair & Beauty Service Providers', 'CAT G - Braiding Only', 73.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(280, 'Hair & Beauty Service Providers', 'CAT H - Pedicure & Manicure Only', 40.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(281, 'Health Facilities – Private Dental Clinics', 'Dental Clinics', 293.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(282, 'Health Facilities – Private Eye Clinics', 'CAT A - Ophthalmologist Clinics', 293.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(283, 'Health Facilities – Private Eye Clinics', 'CAT B - Opticians/Optometrist', 293.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(284, 'Health Facilities – Private General Clinics', 'CAT A - Specialty Clinics', 666.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(285, 'Health Facilities – Private General Clinics', 'CAT B - Primary Health Care', 532.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(286, 'General Hospitals', 'Primary', 666.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(287, 'Maternity Homes', 'CAT A - Expanded Services', 306.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(288, 'Maternity Homes', 'CAT B - General Services', 121.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(289, 'Medical Diagnostic Services', 'CAT A - Secondary/Tertiary', 230.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(290, 'Medical Diagnostic Services', 'CAT B - Basic/ Primary', 108.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(291, 'Licenced Herbal Medicine Units', 'CAT E - Medicine Producers Only (Local)', 200.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(292, 'Licenced Herbal Medicine Units', 'CAT F - Herbal Shops (Local)', 146.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(293, 'Hire Purchase Trading Enterprises', 'CAT A - Large Scale', 306.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(294, 'Hire Purchase Trading Enterprises', 'CAT B - Medium Scale', 266.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(295, 'Hire Purchase Trading Enterprises', 'CAT C - Small Scale', 200.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(296, 'Ice Cream/Yoghurt Dealers', 'CAT E - Retail', 133.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(297, 'Interior/Event Decorators', 'CAT C - Individuals', 146.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(298, 'Jewelry Repairers (watches/bracelets, etc.)', 'CAT ‘A’', 53.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(299, 'Jewelry Repairers (watches/bracelets, etc.)', 'CAT ‘B’', 40.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(300, 'Key Technicians/Cutters', 'CAT \'A\'', 133.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(301, 'Key Technicians/Cutters', 'CAT \'B\'', 66.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(302, 'Laundry Services', 'CAT B - Medium Scale', 230.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(303, 'Laundry Services', 'CAT C - Small Scale', 77.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(304, 'Leather Works Dealers (Other Nonfootwear products)', 'CAT C - Producers (Handmade Medium)', 133.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(305, 'Leather Works Dealers (Other Nonfootwear products)', 'CAT D - Sales/Others', 80.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(306, 'Livestock Farms', 'CAT C - Medium-sized', 399.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(307, 'Livestock Farms', 'CAT D - Small-sized', 200.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(308, 'Livestock Farms', 'CAT E - Very Small-sized', 106.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(309, 'Lottery Business Operators', 'CAT B - National Marketing Companies/Agents', 1997.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(310, 'Lottery Business Operators', 'CAT C - Private Operators', 605.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(311, 'Lottery Business Operators', 'CAT D - Lotto Receiver', 399.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(312, 'Lumber Business (Including Sawmill)', 'CAT ‘B’', 1997.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(313, 'Lumber Business (Including Sawmill)', 'CAT \'C\'', 932.00, 1, 1, '2025-07-20 10:07:33', '2025-07-20 10:07:33'),
(314, 'Lumber Business (Including Sawmill)', 'CAT \'D\'', 532.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(315, 'Machine Sharpening Operators', 'CAT A - Stationed with shops', 47.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(316, 'Machine Sharpening Operators', 'CAT B - Mobile', 7.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(317, 'Markets & Other Facilities’ Management Companies', 'CAT C - Small Scale', 1597.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(318, 'Mattress/Foam Products Dealers', 'CAT B - Wholesalers', 532.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(319, 'Mattress/Foam Products Dealers', 'CAT C - Retailers (Medium Scale)', 306.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(320, 'Mattress/Foam Products Dealers', 'CAT D - Retailers (Small Scale)', 153.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(321, 'Mattress Makers/Repairers', 'Mattress Makers/Repairers', 55.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(322, 'Media Houses Electronic Media (Radio) Operators', 'CAT-C District', 1870.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(323, 'Media Houses Electronic Media (Radio) Operators', 'CAT D - Community', 799.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(324, 'Media Houses Electronic Media (Radio) Operators', 'CAT-E -Recording Studio-Large', 700.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(325, 'Media Houses Electronic Media (Radio) Operators', 'CAT-F-Recording Studio-medium', 300.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(326, 'Media Houses Electronic Media (Radio) Operators', 'CAT-H-Information Centre (Urban)', 200.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(327, 'Media Houses Electronic Media (Radio) Operators', 'CAT-I-Information Centre (Rural)', 150.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(328, 'Printing Houses', 'CAT E - Others', 133.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(329, 'Printing Houses', 'CAT F - Newspaper/Periodicals Vendors', 67.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(330, 'Metal Dealers Metal Fabricators', 'CAT C - Domestic Milling Machines', 932.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(331, 'Metal Dealers Metal Fabricators', 'CAT D - Canopies and Scaffolding', 673.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(332, 'Metal Dealers Metal Fabricators', 'CAT E - Pot, Coal pots and Sheet moulders', 306.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(333, 'Metal Dealers Metal Fabricators', 'CAT F - Chairs and beds, etc.', 184.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(334, 'Milling Businesses (For Food)', 'CAT A - Above 3 Machines', 133.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(335, 'Milling Businesses (For Food)', 'CAT B - 2-3 Machines', 99.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(336, 'Milling Businesses (For Food)', 'CAT C - 1 Machine Only', 44.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(337, 'Mineral Water Producers', 'CAT C - Sachet Water', 586.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(338, 'Mineral Water Distribution/Sales', 'CAT C - Retail (Large)', 200.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(339, 'Mineral Water Distribution/Sales', 'CAT D - Retail (Medium)', 93.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(340, 'Mineral Water Distribution/Sales', 'CAT E - Retail (Small)', 40.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(341, 'Mobile Phone & Accessories Sales/Assembling/Repairs', 'CAT C - Retail/Repairs', 220.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(342, 'Mobile Phone & Accessories Sales/Assembling/Repairs', 'CAT D - Retail Only', 165.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(343, 'Mobile Phone & Accessories Sales/Assembling/Repairs', 'CAT E - Repairs Only', 165.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(344, 'Mobile Phone & Accessories Sales/Assembling/Repairs', 'CAT F - Repairs Only (Small)', 110.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(345, 'Mother Care Shops Retail', 'CAT A - Large Scale', 399.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(346, 'Mother Care Shops Retail', 'CAT B - Medium Scale', 200.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(347, 'Musical Equipment Musical Instrument Sales', 'CAT B - Retail (Medium)', 126.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(348, 'Musical Equipment Musical Instrument Sales', 'CAT C - Retail (Small)', 93.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(349, 'Musical Speaker Manufacturers &Sales', 'Musical Speaker Manufacturers &Sales', 93.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(350, 'Non-Governmental Institutions (Renewal)', 'CAT A - International NGOs/CSOs /FBOs', 187.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(351, 'Non-Governmental Institutions (Renewal)', 'CAT B - Local NGOs/CSOs/ FBOs', 110.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(352, 'Non-Governmental Institutions (Renewal)', 'CAT C - Community NGOs/CSOs /FBOs', 77.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(353, 'Pharmaceutical Companies Pharmacies', 'CAT D - Retail', 439.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(354, 'Over the Counter Medicine Sellers (OTCMs) (Licenced Chemical Shops)', 'Over the Counter Medicine Sellers (OTCMs) (Licenced Chemical Shops)', 231.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(355, 'Photographers / Video Operators', 'CAT D - Photo Shops/Studio', 200.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(356, 'Photographers / Video Operators', 'CAT E - Individual video and photography operators', 77.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(357, 'Plastic Product Sales (Including Water tanks)', 'CAT A - Wholesale (Large)', 385.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(358, 'Plastic Product Sales (Including Water tanks)', 'CAT B - Wholesale (Small)', 200.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(359, 'Plastic Product Sales (Including Water tanks)', 'CAT C - Retail (Large)', 93.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(360, 'Plastic Product Sales (Including Water tanks)', 'CAT D - Retail (Medium)', 80.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(361, 'Plastic Product Sales (Including Water tanks)', 'CAT E - Retail (Small)', 53.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(362, 'Plastic Product Sales (Including Water tanks)', 'CAT F - Polyethylene Sellers', 40.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(363, 'Poultry Farms', 'CAT A - Above 2000 Birds', 466.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(364, 'Poultry Farms', 'CAT B - Up to 2000 Birds', 333.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(365, 'Poultry Farms', 'CAT C - Up to 1000 Birds', 186.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(366, 'Poultry Farms', 'CAT D - Up to 500 Birds', 160.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(367, 'Professional Firms/Individuals (Architectural, Auditing, Accounting, Engineering, Legal Firms etc.)', 'CAT G - Others', 399.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(368, 'Draughtsmanship Business', 'CAT ‘A’', 385.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(369, 'Draughtsmanship Business', 'CAT ‘B’', 187.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(370, 'Publishing Houses', 'CAT D - Publishing Only (Small)', 466.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(371, 'Refrigerator/Air Condition Mechanics', 'CAT ‘A’', 306.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(372, 'Refrigerator/Air Condition Mechanics', 'CAT ‘B’', 230.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(373, 'Straw Basket Weavers and Sales', 'CAT \'C\'', 93.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(374, 'Scrap Metal Dealers', 'CAT B - Medium Scale (Depot)', 399.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(375, 'Scrap Metal Dealers', 'CAT C - Small Scale (Collection Points)', 266.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(376, 'Service/Filling Stations', 'CAT E - Fuel Only', 1100.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(377, 'Service/Filling Stations', 'CAT F - Surface Tank Points', 400.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(378, 'Service/Filling Stations', 'CAT G - Sale of Lubricants', 133.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(379, 'Service/Filling Stations', 'CAT H - Kerosene', 133.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(380, 'Service/Filling Stations', 'CAT I - LPG Retail Points (Large)', 600.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(381, 'Service/Filling Stations', 'CAT J - LPG Retail Points (Medium)', 466.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(382, 'Service/Filling Stations', 'CAT K - LPG Retail Points Only (Small)', 399.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(383, 'Straw Basket Weavers and Sales', 'Straw Basket Weavers and Sales', 40.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(384, 'Timber Products Retail Outlets', 'CAT A - Large scale', 202.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34');
INSERT INTO `business_fee_structure` (`fee_id`, `business_type`, `category`, `fee_amount`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(385, 'Timber Products Retail Outlets', 'CAT B - Medium scale', 92.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(386, 'Timber Products Retail Outlets', 'CAT C - Small scale', 61.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(387, 'Tourism Licenced Facilities Accommodation Facilities Hotels/ Beach Resorts/ Motels/ Apartments', 'CAT A- Five Star', 5000.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(388, 'Tourism Licenced Facilities Accommodation Facilities Hotels/ Beach Resorts/ Motels/ Apartments', 'CAT B- Four Star', 4000.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(389, 'Tourism Licenced Facilities Accommodation Facilities Hotels/ Beach Resorts/ Motels/ Apartments', 'CAT C - Three Star', 3328.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(390, 'Tourism Licenced Facilities Accommodation Facilities Hotels/ Beach Resorts/ Motels/ Apartments', 'CAT D - Two Star', 900.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(391, 'Tourism Licenced Facilities Accommodation Facilities Hotels/ Beach Resorts/ Motels/ Apartments', 'CAT E - One Star', 750.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(392, 'Guest Houses (4-9 Rooms)', 'Guest Houses (4-9 Rooms)', 500.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(393, 'Budget Hotels', 'Budget Hotels', 400.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(394, 'Hostels (Private):', 'CAT A - Above 50 Beds', 759.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(395, 'Hostels (Private):', 'CAT B - 21-50 Beds', 626.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(396, 'Hostels (Private):', 'CAT C - 11-20 Beds', 506.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(397, 'Hostels (Private):', 'CAT D - Up to 10 Beds', 383.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(398, 'Body Building Gyms', 'CAT-A- Large scale settings', 440.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(399, 'Body Building Gyms', 'CAT-B- Medium Scale', 308.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(400, 'Body Building Gyms', 'CAT-C- Small', 154.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(401, 'Food & Beverage (Eatery / Catering Houses) Formal Catering Services Restaurants.', 'CAT-A- Large', 1430.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(402, 'Food & Beverage (Eatery / Catering Houses) Formal Catering Services Restaurants.', 'CAT-B- Medium Scale', 935.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(403, 'Food & Beverage (Eatery / Catering Houses) Formal Catering Services Restaurants.', 'CAT-C- Small', 660.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(404, 'Fast Food', 'CAT A - Grade 1', 799.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(405, 'Fast Food', 'CAT B - Grade 2', 532.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(406, 'Fast Food', 'CAT C - Grade 3', 399.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(407, 'Local Restaurant (Chop Bar)', 'CAT-A', 220.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(408, 'Local Restaurant (Chop Bar)', 'CAT-B', 165.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(409, 'Local Restaurant (Chop Bar)', 'CAT-C', 110.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(410, 'Transport Charges - Commercial Transport Unions (Lorry Park Operations)', 'CAT ‘C’ (GPRTU/ CO-ORPERATIVE)', 303.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(411, 'Transport Charges - Commercial Transport Unions (Lorry Park Operations)', 'CAT \'D\' (PROTOA)', 266.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(412, 'TV & Radio Repairers', 'TV & Radio Repairers', 94.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(413, 'Upholstery Dealers', 'CAT A - Production & Sales', 399.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(414, 'Upholstery Dealers', 'CAT B - Sales', 266.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(415, 'Upholstery Dealers', 'CAT C - Production', 133.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(416, 'Used Clothing Sales (\'Second Hand\')', 'CAT D - Retailers (Containers/Kiosks/Tabletops)', 87.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(417, 'Used Clothing Sales (\'Second Hand\')', 'CAT E - Retailers (Tabletops)', 67.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(418, 'Wood Fuel', 'CAT A - Charcoal Producers', 46.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(419, 'Wood Fuel', 'CAT B - Firewood Sellers (Large)', 106.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(420, 'Wood Fuel', 'CAT C - Firewood Sellers (Medium)', 77.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(421, 'Wood Fuel', 'CAT D - Firewood Sellers (Small)', 46.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34'),
(422, 'Wood Fuel', 'CAT E - Charcoal Sellers', 46.00, 1, 1, '2025-07-20 10:07:34', '2025-07-20 10:07:34');
>>>>>>> c9ccaba (Initial commit)

-- --------------------------------------------------------

--
-- Stand-in structure for view `business_summary`
-- (See below for the actual view)
--
CREATE TABLE `business_summary` (
`business_id` int(11)
,`account_number` varchar(20)
,`business_name` varchar(200)
,`owner_name` varchar(100)
,`business_type` varchar(100)
,`category` varchar(100)
,`telephone` varchar(20)
,`exact_location` text
,`amount_payable` decimal(10,2)
,`status` enum('Active','Inactive','Suspended')
,`zone_name` varchar(100)
,`sub_zone_name` varchar(100)
,`payment_status` varchar(10)
);

-- --------------------------------------------------------

--
<<<<<<< HEAD
=======
-- Table structure for table `message_templates`
--

CREATE TABLE `message_templates` (
  `template_id` int(11) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `template_type` enum('SMS','Email','System') NOT NULL DEFAULT 'SMS',
  `template_content` text NOT NULL,
  `variables` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
>>>>>>> c9ccaba (Initial commit)
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `recipient_type` enum('User','Business','Property') NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `notification_type` enum('SMS','System','Email') NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('Pending','Sent','Failed','Read') DEFAULT 'Pending',
  `sent_by` int(11) DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

<<<<<<< HEAD
=======
--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `recipient_type`, `recipient_id`, `notification_type`, `subject`, `message`, `status`, `sent_by`, `sent_at`, `created_at`) VALUES
(1, 'User', 0, 'SMS', 'SMS Notification', 'Hi', 'Read', 1, '2025-07-13 08:36:56', '2025-07-13 08:36:56');

>>>>>>> c9ccaba (Initial commit)
-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `payment_reference` varchar(50) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_method` enum('Mobile Money','Cash','Bank Transfer','Online') NOT NULL,
  `payment_channel` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `paystack_reference` varchar(100) DEFAULT NULL,
  `payment_status` enum('Pending','Successful','Failed','Cancelled') DEFAULT 'Pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `receipt_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

<<<<<<< HEAD
=======
--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `payment_reference`, `bill_id`, `amount_paid`, `payment_method`, `payment_channel`, `transaction_id`, `paystack_reference`, `payment_status`, `payment_date`, `processed_by`, `notes`, `receipt_url`) VALUES
(28, 'PAY20250717TXWM2F', 22, 200.00, 'Cash', '', '', NULL, 'Successful', '2025-07-17 10:53:44', 1, '', NULL),
(29, 'PAY202507178TFPGI', 24, 500.00, 'Cash', '', '', NULL, 'Successful', '2025-07-17 12:58:32', 1, '', NULL),
(30, 'PAY20250717ZHKL18', 26, 500.00, 'Cash', '', '', NULL, 'Successful', '2025-07-17 14:10:03', 1, '', NULL),
(31, 'PAY202507177AUH4Q', 28, 1000.00, 'Cash', '', '', NULL, 'Successful', '2025-07-17 14:19:57', 1, '', NULL),
(32, 'PAY20250718X4MN07', 30, 500.00, 'Cash', '', '', NULL, 'Successful', '2025-07-18 10:02:29', 1, '', NULL);

>>>>>>> c9ccaba (Initial commit)
-- --------------------------------------------------------

--
-- Stand-in structure for view `payment_summary`
-- (See below for the actual view)
--
CREATE TABLE `payment_summary` (
`payment_id` int(11)
,`payment_reference` varchar(50)
,`amount_paid` decimal(10,2)
,`payment_method` enum('Mobile Money','Cash','Bank Transfer','Online')
,`payment_status` enum('Pending','Successful','Failed','Cancelled')
,`payment_date` timestamp
,`bill_number` varchar(20)
,`bill_type` enum('Business','Property')
,`payer_name` varchar(200)
);

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL,
  `property_number` varchar(20) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `location` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `structure` varchar(100) NOT NULL,
  `ownership_type` enum('Self','Family','Corporate','Others') DEFAULT 'Self',
  `property_type` enum('Modern','Traditional') DEFAULT 'Modern',
  `number_of_rooms` int(11) NOT NULL,
  `property_use` enum('Commercial','Residential') NOT NULL,
  `old_bill` decimal(10,2) DEFAULT 0.00,
  `previous_payments` decimal(10,2) DEFAULT 0.00,
  `arrears` decimal(10,2) DEFAULT 0.00,
  `current_bill` decimal(10,2) DEFAULT 0.00,
  `amount_payable` decimal(10,2) DEFAULT 0.00,
  `batch` varchar(50) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
<<<<<<< HEAD
=======
  `sub_zone_id` int(11) DEFAULT NULL,
>>>>>>> c9ccaba (Initial commit)
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

<<<<<<< HEAD
INSERT INTO `properties` (`property_id`, `property_number`, `owner_name`, `telephone`, `gender`, `location`, `latitude`, `longitude`, `structure`, `ownership_type`, `property_type`, `number_of_rooms`, `property_use`, `old_bill`, `previous_payments`, `arrears`, `current_bill`, `amount_payable`, `batch`, `zone_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'PROP000001', 'Yaw Kusi', '0545051428', 'Male', 'GPS: 5.593020, -0.077100', 5.59302000, -0.07710000, 'Modern Building', 'Self', 'Modern', 3, 'Residential', 0.00, 0.00, 0.00, 225.00, 225.00, '', 2, 1, '2025-07-10 04:07:51', '2025-07-10 04:07:51');
=======
INSERT INTO `properties` (`property_id`, `property_number`, `owner_name`, `telephone`, `gender`, `location`, `latitude`, `longitude`, `structure`, `ownership_type`, `property_type`, `number_of_rooms`, `property_use`, `old_bill`, `previous_payments`, `arrears`, `current_bill`, `amount_payable`, `batch`, `zone_id`, `sub_zone_id`, `created_by`, `created_at`, `updated_at`) VALUES
(5, 'PROP000001', 'Beatrice Akueteh', '0545051428', 'Female', 'GPS: 5.636096, -0.216269\r\nDokua Plaza, Accra, Ghana', 5.63609600, -0.21626880, 'Concrete Block', 'Self', 'Modern', 5, 'Commercial', 0.00, 0.00, 0.00, 500.00, 500.00, '', 1, 2, NULL, '2025-07-18 16:12:29', '2025-07-18 16:12:29');
>>>>>>> c9ccaba (Initial commit)

--
-- Triggers `properties`
--
DELIMITER $$
CREATE TRIGGER `calculate_property_payable` BEFORE INSERT ON `properties` FOR EACH ROW BEGIN
<<<<<<< HEAD
    SET NEW.amount_payable = NEW.old_bill + NEW.arrears + NEW.current_bill - NEW.previous_payments;
=======
    -- Calculate arrears: old_bill - previous_payments (minimum 0)
    SET NEW.arrears = GREATEST(0, NEW.old_bill - NEW.previous_payments);
    
    -- Calculate amount_payable: arrears + current_bill
    SET NEW.amount_payable = NEW.arrears + NEW.current_bill;
>>>>>>> c9ccaba (Initial commit)
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `generate_property_number` BEFORE INSERT ON `properties` FOR EACH ROW BEGIN
    IF NEW.property_number IS NULL OR NEW.property_number = '' THEN
        SET NEW.property_number = CONCAT('PROP', LPAD(COALESCE((SELECT MAX(property_id) FROM properties), 0) + 1, 6, '0'));
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_property_payable` BEFORE UPDATE ON `properties` FOR EACH ROW BEGIN
<<<<<<< HEAD
    SET NEW.amount_payable = NEW.old_bill + NEW.arrears + NEW.current_bill - NEW.previous_payments;
=======
    -- Calculate arrears: old_bill - previous_payments (minimum 0)
    SET NEW.arrears = GREATEST(0, NEW.old_bill - NEW.previous_payments);
    
    -- Calculate amount_payable: arrears + current_bill
    SET NEW.amount_payable = NEW.arrears + NEW.current_bill;
>>>>>>> c9ccaba (Initial commit)
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `property_fee_structure`
--

CREATE TABLE `property_fee_structure` (
  `fee_id` int(11) NOT NULL,
  `structure` varchar(100) NOT NULL,
  `property_use` enum('Commercial','Residential') NOT NULL,
  `fee_per_room` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_fee_structure`
--

INSERT INTO `property_fee_structure` (`fee_id`, `structure`, `property_use`, `fee_per_room`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Concrete Block', 'Residential', 50.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(2, 'Concrete Block', 'Commercial', 100.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(3, 'Mud Block', 'Residential', 25.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(4, 'Mud Block', 'Commercial', 50.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(5, 'Modern Building', 'Residential', 75.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(6, 'Modern Building', 'Commercial', 150.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35');

-- --------------------------------------------------------

--
-- Stand-in structure for view `property_summary`
-- (See below for the actual view)
--
CREATE TABLE `property_summary` (
`property_id` int(11)
,`property_number` varchar(20)
,`owner_name` varchar(100)
,`telephone` varchar(20)
,`location` text
,`structure` varchar(100)
,`property_use` enum('Commercial','Residential')
,`number_of_rooms` int(11)
,`amount_payable` decimal(10,2)
,`zone_name` varchar(100)
<<<<<<< HEAD
=======
,`sub_zone_name` varchar(100)
>>>>>>> c9ccaba (Initial commit)
,`payment_status` varchar(10)
);

-- --------------------------------------------------------

--
-- Table structure for table `public_sessions`
--

CREATE TABLE `public_sessions` (
  `session_id` varchar(64) NOT NULL,
  `account_number` varchar(20) DEFAULT NULL,
  `session_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`session_data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT (current_timestamp() + interval 1 hour)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_zones`
--

CREATE TABLE `sub_zones` (
  `sub_zone_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `sub_zone_name` varchar(100) NOT NULL,
  `sub_zone_code` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_zones`
--

INSERT INTO `sub_zones` (`sub_zone_id`, `zone_id`, `sub_zone_name`, `sub_zone_code`, `description`, `created_by`, `created_at`) VALUES
(1, 1, 'Market Area', 'MA01', 'Main market area', 1, '2025-07-04 18:57:35'),
(2, 1, 'Government Area', 'GA02', 'Government offices area', 1, '2025-07-04 18:57:35'),
(3, 2, 'Residential A', 'RA01', 'High-end residential', 1, '2025-07-04 18:57:35'),
(4, 3, 'Industrial Area', 'IA01', 'Industrial zone', 1, '2025-07-04 18:57:35');

-- --------------------------------------------------------

--
-- Table structure for table `system_restrictions`
--

CREATE TABLE `system_restrictions` (
  `restriction_id` int(11) NOT NULL,
  `restriction_start_date` date NOT NULL,
  `restriction_end_date` date NOT NULL,
  `warning_days` int(11) DEFAULT 7,
  `is_active` tinyint(1) DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','date','json') DEFAULT 'text',
  `description` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_by`, `updated_at`) VALUES
<<<<<<< HEAD
(1, 'assembly_name', 'Municipal Assembly', 'text', 'Name to appear on bills and reports', NULL, '2025-07-04 18:57:35'),
(2, 'billing_start_date', '2024-11-01', 'date', 'Annual billing start date', NULL, '2025-07-04 18:57:35'),
(3, 'restriction_period_months', '3', 'number', 'System restriction period in months', NULL, '2025-07-04 18:57:35'),
(4, 'restriction_start_date', NULL, 'date', 'Restriction countdown start date', NULL, '2025-07-04 18:57:35'),
(5, 'system_restricted', 'false', 'boolean', 'System restriction status', NULL, '2025-07-04 18:57:35'),
(6, 'sms_enabled', 'true', 'boolean', 'SMS notifications enabled', NULL, '2025-07-04 18:57:35'),
(7, 'auto_bill_generation', 'true', 'boolean', 'Automatic bill generation on Nov 1st', NULL, '2025-07-04 18:57:35');
=======
(1, 'assembly_name', 'Anloga District Assembly', 'text', 'Name to appear on bills and reports', 3, '2025-07-17 10:13:16'),
(2, 'billing_start_date', '2024-11-01', 'date', 'Annual billing start date', NULL, '2025-07-04 18:57:35'),
(3, 'restriction_period_months', '3', 'number', 'System restriction period in months', NULL, '2025-07-04 18:57:35'),
(4, 'restriction_start_date', '2025-07-21', 'date', 'Restriction countdown start date', 3, '2025-07-21 09:41:06'),
(5, 'system_restricted', 'true', 'boolean', 'System restriction status', 3, '2025-07-21 09:41:06'),
(6, 'sms_enabled', 'true', 'boolean', 'SMS notifications enabled', 1, '2025-07-12 18:55:11'),
(7, 'auto_bill_generation', 'true', 'boolean', 'Automatic bill generation on Nov 1st', NULL, '2025-07-04 18:57:35'),
(8, 'twilio_sid', '831JD7BHZAHE9M7EWNW1FCUB', 'text', 'Twilio Sid', 3, '2025-07-12 20:23:05'),
(9, 'twilio_token', 'ZQHijuboaimCs7Ali3X9aRzizbjztN8a', 'text', 'Twilio Token', 3, '2025-07-12 20:23:05'),
(10, 'twilio_phone', '0545041428', 'text', 'Twilio Phone', 3, '2025-07-12 20:23:05');
>>>>>>> c9ccaba (Initial commit)

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `first_login` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `role_id`, `first_name`, `last_name`, `phone`, `is_active`, `first_login`, `last_login`, `created_at`, `updated_at`) VALUES
<<<<<<< HEAD
(1, 'admin', 'admin@quickbill305.com', '$2y$10$e4YGmKebT13JFeJVTNJTr.oWNFXUzfTYqhmQEco1/VF/hVOSPCdYS', 2, 'System', 'Administrator', '+233000000000', 1, 0, '2025-07-10 20:00:07', '2025-07-04 18:57:35', '2025-07-10 20:00:07'),
(2, 'abismark', 'kabslink@gmail.com', '$2y$10$JUEO.SZbvFTCgI6p.QYfyO3zVd6hcKqyp8FJcr/.ido7RApNtGXlW', 2, 'Afful', 'Bismark', '+233545041428', 1, 1, NULL, '2025-07-05 17:21:43', '2025-07-05 17:21:43'),
(3, 'Joojo', 'kwadwomegas@gmail.com', '$2y$10$JSLvWE7gM/FUgiTqv9v1qOU9L4U3udx6crIBivD6KIP9.q2NMuTDq', 1, 'Joojo', 'Megas', '0545041428', 1, 0, '2025-07-11 01:03:06', '2025-07-09 19:03:22', '2025-07-11 01:03:06');
=======
(1, 'admin', 'admin@quickbill305.com', '$2y$10$e4YGmKebT13JFeJVTNJTr.oWNFXUzfTYqhmQEco1/VF/hVOSPCdYS', 2, 'System', 'Administrator', '+233000000000', 1, 0, '2025-07-21 09:41:39', '2025-07-04 18:57:35', '2025-07-21 09:41:39'),
(3, 'Joojo', 'kwadwomegas@gmail.com', '$2y$10$JSLvWE7gM/FUgiTqv9v1qOU9L4U3udx6crIBivD6KIP9.q2NMuTDq', 1, 'Joojo', 'Megas', '0545041428', 1, 0, '2025-07-22 09:20:22', '2025-07-09 19:03:22', '2025-07-22 09:20:22'),
(4, 'Kusi', 'kusi@gmail.com', '$2y$10$xXAtNw3GQSVKPNRPnaIacOX9XWegyGQT47fAkuZ22b1J9swsJllge', 5, 'Kusi', 'France', '+233543258791', 1, 0, '2025-07-20 18:30:42', '2025-07-11 15:21:00', '2025-07-20 18:30:42'),
(5, 'Aseye', 'aseyeabledoo@gmail.com', '$2y$10$I8aBJT72RTKJ8bMgiWOwP.831BvSerUvhqQCLft82TbkDyTDJgIZO', 4, 'Aseye', 'Abledu', '', 1, 0, '2025-07-20 18:29:08', '2025-07-12 05:12:52', '2025-07-20 18:29:08'),
(6, 'David', 'kabtechconsulting@gmail.com', '$2y$10$Sn1Ex9uZx3GlCdAwsKkOcuow7anUlJI9FJBSaRgjyDDUeQg4S0XjW', 3, 'David', 'Lomko', '', 1, 0, '2025-07-20 14:38:41', '2025-07-12 07:18:33', '2025-07-20 14:38:41');
>>>>>>> c9ccaba (Initial commit)

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`role_id`, `role_name`, `description`, `created_at`) VALUES
(1, 'Super Admin', 'Full system access with restriction controls', '2025-07-04 18:57:34'),
(2, 'Admin', 'Full system access excluding restrictions', '2025-07-04 18:57:34'),
(3, 'Officer', 'Register businesses/properties, record payments, generate bills', '2025-07-04 18:57:34'),
(4, 'Revenue Officer', 'Record payments and view maps', '2025-07-04 18:57:34'),
(5, 'Data Collector', 'Register businesses/properties and view profiles', '2025-07-04 18:57:34');

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `zone_id` int(11) NOT NULL,
  `zone_name` varchar(100) NOT NULL,
  `zone_code` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`zone_id`, `zone_name`, `zone_code`, `description`, `created_by`, `created_at`) VALUES
(1, 'Central Zone', 'CZ01', 'Central business district', 1, '2025-07-04 18:57:35'),
(2, 'North Zone', 'NZ02', 'Northern residential area', 1, '2025-07-04 18:57:35'),
(3, 'South Zone', 'SZ03', 'Southern commercial area', 1, '2025-07-04 18:57:35'),
(4, 'Eastern Zone', 'EZ', NULL, 1, '2025-07-10 09:20:38');

-- --------------------------------------------------------

--
-- Structure for view `business_summary`
--
DROP TABLE IF EXISTS `business_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `business_summary`  AS SELECT `b`.`business_id` AS `business_id`, `b`.`account_number` AS `account_number`, `b`.`business_name` AS `business_name`, `b`.`owner_name` AS `owner_name`, `b`.`business_type` AS `business_type`, `b`.`category` AS `category`, `b`.`telephone` AS `telephone`, `b`.`exact_location` AS `exact_location`, `b`.`amount_payable` AS `amount_payable`, `b`.`status` AS `status`, `z`.`zone_name` AS `zone_name`, `sz`.`sub_zone_name` AS `sub_zone_name`, CASE WHEN `b`.`amount_payable` > 0 THEN 'Defaulter' ELSE 'Up to Date' END AS `payment_status` FROM ((`businesses` `b` left join `zones` `z` on(`b`.`zone_id` = `z`.`zone_id`)) left join `sub_zones` `sz` on(`b`.`sub_zone_id` = `sz`.`sub_zone_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `payment_summary`
--
DROP TABLE IF EXISTS `payment_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `payment_summary`  AS SELECT `p`.`payment_id` AS `payment_id`, `p`.`payment_reference` AS `payment_reference`, `p`.`amount_paid` AS `amount_paid`, `p`.`payment_method` AS `payment_method`, `p`.`payment_status` AS `payment_status`, `p`.`payment_date` AS `payment_date`, `b`.`bill_number` AS `bill_number`, `b`.`bill_type` AS `bill_type`, CASE WHEN `b`.`bill_type` = 'Business' THEN `bs`.`business_name` WHEN `b`.`bill_type` = 'Property' THEN `pr`.`owner_name` END AS `payer_name` FROM (((`payments` `p` join `bills` `b` on(`p`.`bill_id` = `b`.`bill_id`)) left join `businesses` `bs` on(`b`.`bill_type` = 'Business' and `b`.`reference_id` = `bs`.`business_id`)) left join `properties` `pr` on(`b`.`bill_type` = 'Property' and `b`.`reference_id` = `pr`.`property_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `property_summary`
--
DROP TABLE IF EXISTS `property_summary`;

<<<<<<< HEAD
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `property_summary`  AS SELECT `p`.`property_id` AS `property_id`, `p`.`property_number` AS `property_number`, `p`.`owner_name` AS `owner_name`, `p`.`telephone` AS `telephone`, `p`.`location` AS `location`, `p`.`structure` AS `structure`, `p`.`property_use` AS `property_use`, `p`.`number_of_rooms` AS `number_of_rooms`, `p`.`amount_payable` AS `amount_payable`, `z`.`zone_name` AS `zone_name`, CASE WHEN `p`.`amount_payable` > 0 THEN 'Defaulter' ELSE 'Up to Date' END AS `payment_status` FROM (`properties` `p` left join `zones` `z` on(`p`.`zone_id` = `z`.`zone_id`)) ;
=======
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `property_summary`  AS SELECT `p`.`property_id` AS `property_id`, `p`.`property_number` AS `property_number`, `p`.`owner_name` AS `owner_name`, `p`.`telephone` AS `telephone`, `p`.`location` AS `location`, `p`.`structure` AS `structure`, `p`.`property_use` AS `property_use`, `p`.`number_of_rooms` AS `number_of_rooms`, `p`.`amount_payable` AS `amount_payable`, `z`.`zone_name` AS `zone_name`, `sz`.`sub_zone_name` AS `sub_zone_name`, CASE WHEN `p`.`amount_payable` > 0 THEN 'Defaulter' ELSE 'Up to Date' END AS `payment_status` FROM ((`properties` `p` left join `zones` `z` on(`p`.`zone_id` = `z`.`zone_id`)) left join `sub_zones` `sz` on(`p`.`sub_zone_id` = `sz`.`sub_zone_id`)) ;
>>>>>>> c9ccaba (Initial commit)

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_table_name` (`table_name`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_audit_logs_user_date` (`user_id`,`created_at`);

--
-- Indexes for table `backup_logs`
--
ALTER TABLE `backup_logs`
  ADD PRIMARY KEY (`backup_id`),
  ADD KEY `started_by` (`started_by`),
  ADD KEY `idx_backup_type` (`backup_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_started_at` (`started_at`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`bill_id`),
  ADD UNIQUE KEY `bill_number` (`bill_number`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `idx_bill_number` (`bill_number`),
  ADD KEY `idx_bill_type_ref` (`bill_type`,`reference_id`),
  ADD KEY `idx_billing_year` (`billing_year`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_bills_due_date` (`due_date`);

--
-- Indexes for table `bill_adjustments`
--
ALTER TABLE `bill_adjustments`
  ADD PRIMARY KEY (`adjustment_id`),
  ADD KEY `applied_by` (`applied_by`),
  ADD KEY `idx_adjustment_type` (`adjustment_type`),
  ADD KEY `idx_target` (`target_type`,`target_id`),
  ADD KEY `idx_applied_at` (`applied_at`);

--
-- Indexes for table `businesses`
--
ALTER TABLE `businesses`
  ADD PRIMARY KEY (`business_id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `sub_zone_id` (`sub_zone_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_account_number` (`account_number`),
  ADD KEY `idx_business_type` (`business_type`),
  ADD KEY `idx_zone` (`zone_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_businesses_payable` (`amount_payable`);

--
-- Indexes for table `business_fee_structure`
--
ALTER TABLE `business_fee_structure`
  ADD PRIMARY KEY (`fee_id`),
  ADD UNIQUE KEY `unique_business_type_category` (`business_type`,`category`),
  ADD KEY `created_by` (`created_by`);

--
<<<<<<< HEAD
=======
-- Indexes for table `message_templates`
--
ALTER TABLE `message_templates`
  ADD PRIMARY KEY (`template_id`),
  ADD KEY `created_by` (`created_by`);

--
>>>>>>> c9ccaba (Initial commit)
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `sent_by` (`sent_by`),
  ADD KEY `idx_recipient` (`recipient_type`,`recipient_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_sent_at` (`sent_at`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `payment_reference` (`payment_reference`),
  ADD KEY `processed_by` (`processed_by`),
  ADD KEY `idx_payment_ref` (`payment_reference`),
  ADD KEY `idx_bill_id` (`bill_id`),
  ADD KEY `idx_transaction_id` (`transaction_id`),
  ADD KEY `idx_payment_date` (`payment_date`),
  ADD KEY `idx_payments_date_status` (`payment_date`,`payment_status`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`),
  ADD UNIQUE KEY `property_number` (`property_number`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_property_number` (`property_number`),
  ADD KEY `idx_structure` (`structure`),
  ADD KEY `idx_zone` (`zone_id`),
<<<<<<< HEAD
  ADD KEY `idx_properties_payable` (`amount_payable`);
=======
  ADD KEY `idx_properties_payable` (`amount_payable`),
  ADD KEY `idx_sub_zone` (`sub_zone_id`);
>>>>>>> c9ccaba (Initial commit)

--
-- Indexes for table `property_fee_structure`
--
ALTER TABLE `property_fee_structure`
  ADD PRIMARY KEY (`fee_id`),
  ADD UNIQUE KEY `unique_structure_use` (`structure`,`property_use`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `public_sessions`
--
ALTER TABLE `public_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_account_number` (`account_number`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `sub_zones`
--
ALTER TABLE `sub_zones`
  ADD PRIMARY KEY (`sub_zone_id`),
  ADD UNIQUE KEY `sub_zone_code` (`sub_zone_code`),
  ADD KEY `zone_id` (`zone_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `system_restrictions`
--
ALTER TABLE `system_restrictions`
  ADD PRIMARY KEY (`restriction_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`zone_id`),
  ADD UNIQUE KEY `zone_code` (`zone_code`),
  ADD KEY `created_by` (`created_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
<<<<<<< HEAD
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
=======
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=327;
>>>>>>> c9ccaba (Initial commit)

--
-- AUTO_INCREMENT for table `backup_logs`
--
ALTER TABLE `backup_logs`
<<<<<<< HEAD
  MODIFY `backup_id` int(11) NOT NULL AUTO_INCREMENT;
=======
  MODIFY `backup_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
>>>>>>> c9ccaba (Initial commit)

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
<<<<<<< HEAD
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
=======
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
>>>>>>> c9ccaba (Initial commit)

--
-- AUTO_INCREMENT for table `bill_adjustments`
--
ALTER TABLE `bill_adjustments`
<<<<<<< HEAD
  MODIFY `adjustment_id` int(11) NOT NULL AUTO_INCREMENT;
=======
  MODIFY `adjustment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
>>>>>>> c9ccaba (Initial commit)

--
-- AUTO_INCREMENT for table `businesses`
--
ALTER TABLE `businesses`
<<<<<<< HEAD
  MODIFY `business_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
=======
  MODIFY `business_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
>>>>>>> c9ccaba (Initial commit)

--
-- AUTO_INCREMENT for table `business_fee_structure`
--
ALTER TABLE `business_fee_structure`
<<<<<<< HEAD
  MODIFY `fee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
=======
  MODIFY `fee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=423;

--
-- AUTO_INCREMENT for table `message_templates`
--
ALTER TABLE `message_templates`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT;
>>>>>>> c9ccaba (Initial commit)

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
<<<<<<< HEAD
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;
=======
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
>>>>>>> c9ccaba (Initial commit)

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
<<<<<<< HEAD
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;
=======
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
>>>>>>> c9ccaba (Initial commit)

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
<<<<<<< HEAD
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
=======
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
>>>>>>> c9ccaba (Initial commit)

--
-- AUTO_INCREMENT for table `property_fee_structure`
--
ALTER TABLE `property_fee_structure`
  MODIFY `fee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sub_zones`
--
ALTER TABLE `sub_zones`
  MODIFY `sub_zone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `system_restrictions`
--
ALTER TABLE `system_restrictions`
<<<<<<< HEAD
  MODIFY `restriction_id` int(11) NOT NULL AUTO_INCREMENT;
=======
  MODIFY `restriction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
>>>>>>> c9ccaba (Initial commit)

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
<<<<<<< HEAD
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
=======
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
>>>>>>> c9ccaba (Initial commit)

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
<<<<<<< HEAD
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
=======
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
>>>>>>> c9ccaba (Initial commit)

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `zone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `backup_logs`
--
ALTER TABLE `backup_logs`
  ADD CONSTRAINT `backup_logs_ibfk_1` FOREIGN KEY (`started_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_ibfk_1` FOREIGN KEY (`generated_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `bill_adjustments`
--
ALTER TABLE `bill_adjustments`
  ADD CONSTRAINT `bill_adjustments_ibfk_1` FOREIGN KEY (`applied_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `businesses`
--
ALTER TABLE `businesses`
  ADD CONSTRAINT `businesses_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`zone_id`),
  ADD CONSTRAINT `businesses_ibfk_2` FOREIGN KEY (`sub_zone_id`) REFERENCES `sub_zones` (`sub_zone_id`),
  ADD CONSTRAINT `businesses_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `business_fee_structure`
--
ALTER TABLE `business_fee_structure`
  ADD CONSTRAINT `business_fee_structure_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
<<<<<<< HEAD
=======
-- Constraints for table `message_templates`
--
ALTER TABLE `message_templates`
  ADD CONSTRAINT `message_templates_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
>>>>>>> c9ccaba (Initial commit)
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`sent_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`bill_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`zone_id`),
<<<<<<< HEAD
  ADD CONSTRAINT `properties_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);
=======
  ADD CONSTRAINT `properties_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `properties_ibfk_3` FOREIGN KEY (`sub_zone_id`) REFERENCES `sub_zones` (`sub_zone_id`);
>>>>>>> c9ccaba (Initial commit)

--
-- Constraints for table `property_fee_structure`
--
ALTER TABLE `property_fee_structure`
  ADD CONSTRAINT `property_fee_structure_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `sub_zones`
--
ALTER TABLE `sub_zones`
  ADD CONSTRAINT `sub_zones_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`zone_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sub_zones_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `system_restrictions`
--
ALTER TABLE `system_restrictions`
  ADD CONSTRAINT `system_restrictions_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`role_id`);

--
-- Constraints for table `zones`
--
ALTER TABLE `zones`
  ADD CONSTRAINT `zones_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
