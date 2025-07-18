-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 11, 2025 at 03:55 AM
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
(38, 3, 'USER_LOGIN', 'users', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-11 01:03:06');

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
(1, 'BIL-BIZ2025-51A36E98', 'Business', 2, '2025', 0.00, 0.00, 0.00, 1000.00, 1000.00, NULL, 'Pending', 3, '2025-07-10 19:31:38', NULL),
(2, 'BILL2025B000001', 'Business', 1, '2025', 0.00, 0.00, 0.00, 1000.00, 1000.00, NULL, 'Pending', 3, '2025-07-11 01:20:30', NULL),
(3, 'BILL2025P000001', 'Property', 1, '2025', 0.00, 0.00, 0.00, 225.00, 225.00, NULL, 'Pending', 3, '2025-07-11 01:20:30', NULL),
(4, 'BILL2024B000001', 'Business', 1, '2024', 0.00, 0.00, 0.00, 1000.00, 1000.00, NULL, 'Pending', 3, '2025-07-11 01:53:19', NULL);

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
(1, 'BIZ000001', 'KabTech Consulting', 'Afful Bismark', 'Restaurant', 'Medium Scale', '', 'GPS: 5.593020, -0.077100', 5.59302000, -0.07710000, 0.00, 0.00, 0.00, 1000.00, 1000.00, '', 'Active', 1, 2, 1, '2025-07-10 03:16:20', '2025-07-10 03:16:20'),
(2, 'BIZ000002', 'Kwabena Ewusi Enterprise', 'Zayne Ewusi', 'Restaurant', 'Medium Scale', '0567823456', 'GPS: 5.593020, -0.077100', 5.59302000, -0.07710000, 0.00, 0.00, 0.00, 1000.00, 1000.00, '', 'Active', 2, 3, 1, '2025-07-10 09:00:51', '2025-07-10 09:00:51');

--
-- Triggers `businesses`
--
DELIMITER $$
CREATE TRIGGER `calculate_business_payable` BEFORE INSERT ON `businesses` FOR EACH ROW BEGIN
    SET NEW.amount_payable = NEW.old_bill + NEW.arrears + NEW.current_bill - NEW.previous_payments;
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
    SET NEW.amount_payable = NEW.old_bill + NEW.arrears + NEW.current_bill - NEW.previous_payments;
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
(3, 'Restaurant', 'Large Scale', 2000.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(4, 'Shop', 'Small Scale', 300.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(5, 'Shop', 'Medium Scale', 600.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(6, 'Shop', 'Large Scale', 1200.00, 1, 1, '2025-07-04 18:57:35', '2025-07-04 18:57:35'),
(7, 'Saloon', 'Large', 100.00, 1, 3, '2025-07-10 14:51:40', '2025-07-10 14:51:40');

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
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `property_number`, `owner_name`, `telephone`, `gender`, `location`, `latitude`, `longitude`, `structure`, `ownership_type`, `property_type`, `number_of_rooms`, `property_use`, `old_bill`, `previous_payments`, `arrears`, `current_bill`, `amount_payable`, `batch`, `zone_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'PROP000001', 'Yaw Kusi', '0545051428', 'Male', 'GPS: 5.593020, -0.077100', 5.59302000, -0.07710000, 'Modern Building', 'Self', 'Modern', 3, 'Residential', 0.00, 0.00, 0.00, 225.00, 225.00, '', 2, 1, '2025-07-10 04:07:51', '2025-07-10 04:07:51');

--
-- Triggers `properties`
--
DELIMITER $$
CREATE TRIGGER `calculate_property_payable` BEFORE INSERT ON `properties` FOR EACH ROW BEGIN
    SET NEW.amount_payable = NEW.old_bill + NEW.arrears + NEW.current_bill - NEW.previous_payments;
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
    SET NEW.amount_payable = NEW.old_bill + NEW.arrears + NEW.current_bill - NEW.previous_payments;
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
(1, 'assembly_name', 'Municipal Assembly', 'text', 'Name to appear on bills and reports', NULL, '2025-07-04 18:57:35'),
(2, 'billing_start_date', '2024-11-01', 'date', 'Annual billing start date', NULL, '2025-07-04 18:57:35'),
(3, 'restriction_period_months', '3', 'number', 'System restriction period in months', NULL, '2025-07-04 18:57:35'),
(4, 'restriction_start_date', NULL, 'date', 'Restriction countdown start date', NULL, '2025-07-04 18:57:35'),
(5, 'system_restricted', 'false', 'boolean', 'System restriction status', NULL, '2025-07-04 18:57:35'),
(6, 'sms_enabled', 'true', 'boolean', 'SMS notifications enabled', NULL, '2025-07-04 18:57:35'),
(7, 'auto_bill_generation', 'true', 'boolean', 'Automatic bill generation on Nov 1st', NULL, '2025-07-04 18:57:35');

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
(1, 'admin', 'admin@quickbill305.com', '$2y$10$e4YGmKebT13JFeJVTNJTr.oWNFXUzfTYqhmQEco1/VF/hVOSPCdYS', 2, 'System', 'Administrator', '+233000000000', 1, 0, '2025-07-10 20:00:07', '2025-07-04 18:57:35', '2025-07-10 20:00:07'),
(2, 'abismark', 'kabslink@gmail.com', '$2y$10$JUEO.SZbvFTCgI6p.QYfyO3zVd6hcKqyp8FJcr/.ido7RApNtGXlW', 2, 'Afful', 'Bismark', '+233545041428', 1, 1, NULL, '2025-07-05 17:21:43', '2025-07-05 17:21:43'),
(3, 'Joojo', 'kwadwomegas@gmail.com', '$2y$10$JSLvWE7gM/FUgiTqv9v1qOU9L4U3udx6crIBivD6KIP9.q2NMuTDq', 1, 'Joojo', 'Megas', '0545041428', 1, 0, '2025-07-11 01:03:06', '2025-07-09 19:03:22', '2025-07-11 01:03:06');

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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `property_summary`  AS SELECT `p`.`property_id` AS `property_id`, `p`.`property_number` AS `property_number`, `p`.`owner_name` AS `owner_name`, `p`.`telephone` AS `telephone`, `p`.`location` AS `location`, `p`.`structure` AS `structure`, `p`.`property_use` AS `property_use`, `p`.`number_of_rooms` AS `number_of_rooms`, `p`.`amount_payable` AS `amount_payable`, `z`.`zone_name` AS `zone_name`, CASE WHEN `p`.`amount_payable` > 0 THEN 'Defaulter' ELSE 'Up to Date' END AS `payment_status` FROM (`properties` `p` left join `zones` `z` on(`p`.`zone_id` = `z`.`zone_id`)) ;

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
  ADD KEY `idx_properties_payable` (`amount_payable`);

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `backup_logs`
--
ALTER TABLE `backup_logs`
  MODIFY `backup_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bill_adjustments`
--
ALTER TABLE `bill_adjustments`
  MODIFY `adjustment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `businesses`
--
ALTER TABLE `businesses`
  MODIFY `business_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `business_fee_structure`
--
ALTER TABLE `business_fee_structure`
  MODIFY `fee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `restriction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  ADD CONSTRAINT `properties_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

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
