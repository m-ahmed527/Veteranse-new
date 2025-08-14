-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2025 at 03:20 AM
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
-- Database: `veteranse`
--

-- --------------------------------------------------------

--
-- Table structure for table `add_ons`
--

CREATE TABLE `add_ons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Name of the add-on',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `add_ons`
--

INSERT INTO `add_ons` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Extra Time', '2025-06-10 17:12:17', '2025-06-10 17:12:17'),
(2, 'Premium Products', '2025-06-10 17:12:17', '2025-06-10 17:12:17'),
(3, 'Weekend Availability', '2025-06-10 17:12:17', '2025-06-10 17:12:17'),
(4, 'Senior Staff Only', '2025-06-10 17:12:17', '2025-06-10 17:12:17');

-- --------------------------------------------------------

--
-- Table structure for table `add_on_service`
--

CREATE TABLE `add_on_service` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `add_on_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Foreign key to the add_ons table, representing the add-on service',
  `service_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Foreign key to the services table, representing the service that the add-on is associated with',
  `add_on_name` varchar(255) DEFAULT NULL COMMENT 'Add on name',
  `add_on_price` double NOT NULL DEFAULT 0 COMMENT 'Price of the add-on service',
  `service_name` varchar(255) DEFAULT NULL COMMENT 'Name of the service',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `add_on_service`
--

INSERT INTO `add_on_service` (`id`, `add_on_id`, `service_id`, `add_on_name`, `add_on_price`, `service_name`, `created_at`, `updated_at`) VALUES
(3, 2, 7, 'Premium Products', 50, 'Hair and Beards', '2025-06-10 18:27:04', '2025-06-10 18:27:04'),
(4, 4, 7, 'Senior Staff Only', 60, 'Hair and Beards', '2025-06-10 18:27:04', '2025-06-10 18:27:04'),
(5, 3, 9, 'Weekend Availability', 10, 'Grooming', '2025-06-11 13:38:34', '2025-06-11 13:38:34'),
(6, 4, 9, 'Senior Staff Only', 25, 'Grooming', '2025-06-11 13:38:34', '2025-06-11 13:38:34'),
(7, 1, 6, 'Extra Time', 65, 'wqqw', '2025-06-11 16:54:25', '2025-06-11 16:54:25'),
(8, 2, 10, 'Premium Products', 1222, 'Service 01', '2025-07-23 22:02:19', '2025-07-23 22:02:19'),
(9, 3, 11, 'Weekend Availability', 10, 'Grooming', '2025-07-23 22:09:12', '2025-07-23 22:09:12'),
(10, 4, 11, 'Senior Staff Only', 25, 'Grooming', '2025-07-23 22:09:12', '2025-07-23 22:09:12'),
(11, 3, 12, 'Weekend Availability', 10, 'Grooming', '2025-07-23 22:09:18', '2025-07-23 22:09:18'),
(12, 4, 12, 'Senior Staff Only', 25, 'Grooming', '2025-07-23 22:09:18', '2025-07-23 22:09:18'),
(13, 3, 13, 'Weekend Availability', 10, 'Groomingggggggg', '2025-07-24 20:48:58', '2025-07-24 20:48:58'),
(14, 4, 13, 'Senior Staff Only', 25, 'Groomingggggggg', '2025-07-24 20:48:58', '2025-07-24 20:48:58'),
(15, 3, 14, 'Weekend Availability', 10, 'Groominggggggggwwwww', '2025-07-24 20:53:01', '2025-07-24 20:53:01'),
(16, 4, 14, 'Senior Staff Only', 25, 'Groominggggggggwwwww', '2025-07-24 20:53:01', '2025-07-24 20:53:01'),
(17, 3, 15, 'Weekend Availability', 10, 'Grooming', '2025-07-24 20:55:52', '2025-07-24 20:55:52'),
(18, 4, 15, 'Senior Staff Only', 25, 'Grooming', '2025-07-24 20:55:52', '2025-07-24 20:55:52'),
(19, 3, 16, 'Weekend Availability', 10, 'Groominggggggggwwwww', '2025-07-25 15:11:45', '2025-07-25 15:11:45'),
(20, 4, 16, 'Senior Staff Only', 25, 'Groominggggggggwwwww', '2025-07-25 15:11:45', '2025-07-25 15:11:45'),
(21, 3, 17, 'Weekend Availability', 10, 'Groominggggggggwwwww', '2025-08-12 15:33:44', '2025-08-12 15:33:44'),
(22, 4, 17, 'Senior Staff Only', 25, 'Groominggggggggwwwww', '2025-08-12 15:33:44', '2025-08-12 15:33:44');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID of the vendor providing the service',
  `service_name` varchar(255) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_time_from` time DEFAULT NULL,
  `booking_time_to` time DEFAULT NULL,
  `charge_hour` int(11) DEFAULT 1,
  `base_price` double NOT NULL DEFAULT 0,
  `tax_price` double NOT NULL DEFAULT 0 COMMENT 'amount that admin received\r\n',
  `vendor_cut` double NOT NULL DEFAULT 0 COMMENT 'amount will be sent to vendor',
  `total_price` double NOT NULL DEFAULT 0,
  `booking_status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_intent` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `service_id`, `vendor_id`, `service_name`, `booking_date`, `booking_time_from`, `booking_time_to`, `charge_hour`, `base_price`, `tax_price`, `vendor_cut`, `total_price`, `booking_status`, `payment_status`, `payment_method`, `payment_intent`, `address`, `city`, `state`, `country`, `phone`, `zip_code`, `created_at`, `updated_at`) VALUES
(24, 3, 7, 1, 'wqqw', '2025-08-20', '03:46:00', '04:00:00', 1, 100, 7.5, 0, 167.5, 'cancelled', 'succeeded', 'pm_1Rj6V3IKKEDUbdbtw8v2IN0c', 'pi_3Rj6V5IKKEDUbdbt1PgimuMV', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09 17:30:02', '2025-07-25 13:48:13'),
(25, 1, 7, 1, 'wqqw', '2025-07-20', '03:46:00', '04:00:00', 1, 100, 12, 148, 160, 'pending', 'pending', 'pm_1RjOmhIKKEDUbdbtfdE4R18o', 'pi_3RjOmsIKKEDUbdbt1q9zJgFr', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-10 13:01:37', '2025-07-10 13:01:38'),
(26, 3, 7, 1, 'wqqw', '2025-07-20', '03:46:00', '04:00:00', 1, 100, 12, 148, 160, 'confirmed', 'succeeded', 'pm_1RjOzQIKKEDUbdbt0yw3QJ2r', 'pi_3RjOzTIKKEDUbdbt0KWAyhs1', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-10 13:14:38', '2025-07-10 13:14:43'),
(27, 3, 9, 2, 'Grooming', '2025-07-21', '03:46:00', '04:00:00', 1, 270, 0, 305, 305, 'confirmed', 'succeeded', 'pm_1RjPQmIKKEDUbdbtIwvzwYhe', 'pi_3RjPQoIKKEDUbdbt02vZeYR0', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-10 13:42:53', '2025-07-10 13:42:57'),
(45, 3, 9, 2, 'Grooming', '2025-08-21', '03:45:00', '04:00:00', 1, 150, 0, 185, 185, 'pending', 'pending', 'pm_1RvSRZIKKEDUbdbthvT3Kvvy', 'pi_3RvSRbIKKEDUbdbt00RkijNu', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-12 19:21:31', '2025-08-12 19:21:31'),
(49, 3, 9, 2, 'Grooming', '2025-08-21', '03:45:00', '05:00:00', 2, 150, 0, 335, 335, 'pending', 'pending', 'pm_1RvSteIKKEDUbdbtAPMiaoCC', 'pi_3RvStgIKKEDUbdbt1Fu0QsXA', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-12 19:50:32', '2025-08-12 19:50:32'),
(50, 3, 9, 2, 'Grooming', '2025-08-21', '03:45:00', '06:00:00', 3, 150, 0, 485, 485, 'pending', 'pending', 'pm_1RvT2xIKKEDUbdbtqRyaeX2l', 'pi_3RvT2zIKKEDUbdbt08KwG3Rb', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-12 20:00:09', '2025-08-12 20:00:09'),
(51, 3, 9, 2, 'Grooming', '2025-08-21', '03:45:00', '06:00:00', 3, 150, 0, 485, 485, 'pending', 'pending', 'pm_1RvT3uIKKEDUbdbt43wN9mjP', 'pi_3RvT3vIKKEDUbdbt0tGJkx0r', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-12 20:01:08', '2025-08-12 20:01:08');

-- --------------------------------------------------------

--
-- Table structure for table `booking_add_on`
--

CREATE TABLE `booking_add_on` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `add_on_id` bigint(20) UNSIGNED NOT NULL,
  `add_on_name` varchar(255) DEFAULT NULL,
  `add_on_price` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_add_on`
--

INSERT INTO `booking_add_on` (`id`, `booking_id`, `add_on_id`, `add_on_name`, `add_on_price`, `created_at`, `updated_at`) VALUES
(12, 24, 4, 'Senior Staff Only', 60, '2025-07-09 17:30:02', '2025-07-09 17:30:02'),
(13, 25, 4, 'Senior Staff Only', 60, '2025-07-10 13:01:37', '2025-07-10 13:01:37'),
(14, 26, 4, 'Senior Staff Only', 60, '2025-07-10 13:14:38', '2025-07-10 13:14:38'),
(15, 27, 4, 'Senior Staff Only', 25, '2025-07-10 13:42:53', '2025-07-10 13:42:53'),
(16, 27, 3, 'Weekend Availability', 10, '2025-07-10 13:42:53', '2025-07-10 13:42:53'),
(45, 45, 4, 'Senior Staff Only', 25, '2025-08-12 19:21:31', '2025-08-12 19:21:31'),
(46, 45, 3, 'Weekend Availability', 10, '2025-08-12 19:21:31', '2025-08-12 19:21:31'),
(53, 49, 4, 'Senior Staff Only', 25, '2025-08-12 19:50:32', '2025-08-12 19:50:32'),
(54, 49, 3, 'Weekend Availability', 10, '2025-08-12 19:50:32', '2025-08-12 19:50:32'),
(55, 50, 4, 'Senior Staff Only', 25, '2025-08-12 20:00:09', '2025-08-12 20:00:09'),
(56, 50, 3, 'Weekend Availability', 10, '2025-08-12 20:00:09', '2025-08-12 20:00:09'),
(57, 51, 4, 'Senior Staff Only', 25, '2025-08-12 20:01:08', '2025-08-12 20:01:08'),
(58, 51, 3, 'Weekend Availability', 10, '2025-08-12 20:01:08', '2025-08-12 20:01:08');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cart_name` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `discount_type` varchar(255) DEFAULT NULL,
  `discount_value` double NOT NULL DEFAULT 0,
  `sub_total_amount` double NOT NULL DEFAULT 0,
  `total_amount` double NOT NULL DEFAULT 0,
  `total_items` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `cart_name`, `user_id`, `discount_type`, `discount_value`, `sub_total_amount`, `total_amount`, `total_items`, `created_at`, `updated_at`) VALUES
(2, 'Ahmed-Cart', 1, NULL, 0, 0, 0, 0, '2025-08-04 14:53:08', '2025-08-04 14:54:03');

-- --------------------------------------------------------

--
-- Table structure for table `cart_products`
--

CREATE TABLE `cart_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_quantity` int(11) NOT NULL DEFAULT 1,
  `product_total` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Grooming Services', '2025-06-10 20:45:40', '2025-06-10 20:45:40'),
(2, 'Hygiene Services', '2025-06-10 20:45:40', '2025-06-10 20:45:40'),
(3, 'Specialized Services', '2025-06-10 20:46:25', '2025-06-10 20:46:25');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_06_03_234729_create_personal_access_tokens_table', 1),
(15, '2025_06_09_215835_add_columns_to_users_table', 2),
(16, '2025_06_09_192940_create_categories_table', 3),
(19, '2025_06_10_194855_create_services_table', 4),
(20, '2025_06_10_215730_create_add_ons_table', 5),
(21, '2025_06_10_222037_create_add_on_service_table', 6),
(23, '2025_06_12_003135_create_products_table', 7),
(28, '2025_06_13_193104_create_carts_table', 8),
(29, '2025_06_13_193137_create_cart_products_table', 8),
(30, '2025_06_16_191941_create_wishlists_table', 9),
(32, '2025_06_17_211011_create_bookings_table', 10),
(33, '2025_06_17_222524_create_booking_add_on_table', 10),
(34, '2025_06_18_181623_create_taxes_table', 11),
(36, '2025_06_23_183537_create_plans_table', 12),
(38, '2025_06_23_185119_add_stripe_columns_to_users_table', 13),
(39, '2025_06_25_175145_create_wallet_transactions_table', 14),
(41, '2025_06_25_175844_add_wallet_balance_to_users_table', 15),
(42, '2025_07_01_204413_add_discounted_price_column_to_services_table', 16),
(43, '2025_07_01_204433_add_color_column_to_products_table', 16),
(45, '2025_07_02_164531_add_vendor_id_column_to_bookings_table', 17),
(46, '2025_07_02_173404_create_stripe_accounts_table', 18),
(47, '2025_07_03_002510_add_stripe_account_connected_column_to_users_table', 19),
(48, '2025_07_10_174953_add_vendor_cut_column_to_bookings_table', 20),
(49, '2025_07_10_221450_create_orders_table', 21),
(50, '2025_07_10_225248_create_order_product_table', 21),
(52, '2025_07_11_205337_add_tax_realted_column_to_order_product_table', 22),
(53, '2025_07_11_211530_add_payment_type_column_to_orders_table', 23),
(54, '2025_07_25_200310_add_location_columns_to_services_table', 24),
(55, '2025_08_07_171724_create_reviews_table', 25),
(56, '2025_08_12_203153_add_charge_type_column_to_services_table', 26),
(57, '2025_08_12_221915_add_soft_delete_coloumn_to_users_table', 27),
(58, '2025_08_13_005327_add_charge_hour_column_to_bookings_table', 28);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `total_amount` double NOT NULL DEFAULT 0,
  `order_status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_type` varchar(255) NOT NULL DEFAULT 'card' COMMENT 'Type of payment used for the order, e.g., card, wallet',
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_intent` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `order_status`, `payment_status`, `payment_type`, `payment_method`, `payment_intent`, `address`, `city`, `country`, `state`, `phone`, `zip_code`, `created_at`, `updated_at`) VALUES
(1, 1, 560, 'pending', 'pending', 'card', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-10 18:46:37', '2025-07-10 18:46:37'),
(2, 1, 560, 'pending', 'pending', 'card', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-10 18:56:58', '2025-07-10 18:56:58'),
(3, 1, 560, 'pending', 'pending', 'card', 'pm_1RjUejIKKEDUbdbtinxGw2h2', 'pi_3RjUeuIKKEDUbdbt1DATwbV4', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-10 19:17:45', '2025-07-10 19:17:46'),
(4, 1, 560, 'placed', 'succeeded', 'card', 'pm_1RjUlPIKKEDUbdbt6nYUb3KQ', 'pi_3RjUlRIKKEDUbdbt0jDwIX5p', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-10 19:24:30', '2025-07-10 19:24:34'),
(5, 1, 560, 'placed', 'succeeded', 'card', 'pm_1RjUz2IKKEDUbdbtLBAZeAw2', 'pi_3RjUz4IKKEDUbdbt00zRGBs4', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-10 19:38:35', '2025-07-10 19:38:38'),
(6, 1, 400, 'placed', 'succeeded', 'wallet', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-11 16:09:31', '2025-07-11 16:09:31'),
(7, 1, 400, 'placed', 'succeeded', 'wallet', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-11 16:22:17', '2025-07-11 16:22:17'),
(8, 1, 2880, 'pending', 'pending', 'card', 'pm_1RjpdDIKKEDUbdbtlIlBA3sj', 'pi_3RjpdKIKKEDUbdbt0D3jpWP6', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-11 17:41:32', '2025-07-11 17:41:33'),
(9, 1, 2800, 'placed', 'succeeded', 'card', 'pm_1RjpgvIKKEDUbdbt5PMDvjhi', 'pi_3RjpgxIKKEDUbdbt1e8671hP', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-11 17:45:17', '2025-07-11 17:45:21');

-- --------------------------------------------------------

--
-- Table structure for table `order_product`
--

CREATE TABLE `order_product` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID of vendor whose product is',
  `product_price` double NOT NULL DEFAULT 0,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_price` double NOT NULL DEFAULT 0,
  `tax_price` double NOT NULL DEFAULT 0 COMMENT 'amount admin will receive',
  `vendor_cut` double NOT NULL DEFAULT 0 COMMENT 'amount vendor will receive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_product`
--

INSERT INTO `order_product` (`id`, `order_id`, `product_id`, `vendor_id`, `product_price`, `quantity`, `total_price`, `tax_price`, `vendor_cut`, `created_at`, `updated_at`) VALUES
(1, 2, 1, NULL, 20, 4, 80, 0, 0, '2025-07-10 18:56:58', '2025-07-10 18:56:58'),
(2, 2, 3, NULL, 120, 4, 480, 0, 0, '2025-07-10 18:56:58', '2025-07-10 18:56:58'),
(3, 3, 1, NULL, 20, 4, 80, 0, 0, '2025-07-10 19:17:45', '2025-07-10 19:17:45'),
(4, 3, 3, NULL, 120, 4, 480, 0, 0, '2025-07-10 19:17:45', '2025-07-10 19:17:45'),
(5, 4, 1, NULL, 20, 4, 80, 0, 0, '2025-07-10 19:24:30', '2025-07-10 19:24:30'),
(6, 4, 3, NULL, 120, 4, 480, 0, 0, '2025-07-10 19:24:30', '2025-07-10 19:24:30'),
(7, 5, 1, NULL, 20, 4, 80, 0, 0, '2025-07-10 19:38:35', '2025-07-10 19:38:35'),
(8, 5, 3, NULL, 120, 4, 480, 0, 0, '2025-07-10 19:38:35', '2025-07-10 19:38:35'),
(9, 6, 1, 1, 20, 4, 80, 6, 74, '2025-07-11 16:09:31', '2025-07-11 16:09:31'),
(10, 6, 2, 1, 80, 4, 320, 24, 296, '2025-07-11 16:09:31', '2025-07-11 16:09:31'),
(11, 7, 1, 1, 20, 4, 80, 6, 74, '2025-07-11 16:22:17', '2025-07-11 16:22:17'),
(12, 7, 2, 1, 80, 4, 320, 24, 296, '2025-07-11 16:22:17', '2025-07-11 16:22:17'),
(13, 8, 1, 1, 20, 4, 80, 6, 74, '2025-07-11 17:41:32', '2025-07-11 17:41:32'),
(14, 8, 4, 1, 700, 4, 2800, 210, 2590, '2025-07-11 17:41:32', '2025-07-11 17:41:32'),
(15, 9, 4, 1, 700, 4, 2800, 210, 2590, '2025-07-11 17:45:17', '2025-07-11 17:45:17');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'API-token', '48d79ac6cd315e66cb0b47f81a79cc179b4635c5c6ed1fafdf8df8180ee44468', '[\"*\"]', '2025-06-05 19:31:24', NULL, '2025-06-05 18:15:08', '2025-06-05 19:31:24'),
(2, 'App\\Models\\User', 1, 'API-token', 'e4c0a00b6086c0adb660df73ab3984eae35c2d589bcd1cf5bcc9b3461e32d927', '[\"*\"]', '2025-06-06 12:00:54', NULL, '2025-06-06 11:51:49', '2025-06-06 12:00:54'),
(3, 'App\\Models\\User', 1, 'API-token', 'd1ad2511398767b76efc72aca9e81d40ef472d10829afc82f0cbde2c7b84e7ed', '[\"*\"]', '2025-08-13 13:07:24', NULL, '2025-06-09 13:16:07', '2025-08-13 13:07:24'),
(4, 'App\\Models\\User', 2, 'API-token', 'a179dcc0d8d052a5212377c2bfe0d9a4c073e16ecaa3b6a498301b0a504cdb4e', '[\"*\"]', '2025-06-24 14:49:01', NULL, '2025-06-24 13:46:08', '2025-06-24 14:49:01'),
(7, 'App\\Models\\User', 2, 'API-token', 'c88581e600f4b202a228f4b1f197922b24aed0f1aa59bcb755f9ecf5c9c9eeaf', '[\"*\"]', NULL, NULL, '2025-07-02 13:33:19', '2025-07-02 13:33:19'),
(8, 'App\\Models\\User', 2, 'API-token', '7f1b8f537c5af20a7d82100b12b4375af30cdcbb3ae50da8bf57d3719a15149e', '[\"*\"]', NULL, NULL, '2025-07-02 14:02:28', '2025-07-02 14:02:28'),
(15, 'App\\Models\\User', 2, 'API-token', '9eb7d1ef6deee5408447b78a13e98cb55db74b911b480d26237cb3601d087cd0', '[\"*\"]', '2025-07-02 17:31:22', NULL, '2025-07-02 17:28:34', '2025-07-02 17:31:22'),
(16, 'App\\Models\\User', 2, 'API-token', '642dedff2cd35b232c398b64c1791bb51ebef7ffac73a52f5d317fb906d2135d', '[\"*\"]', '2025-07-02 19:32:51', NULL, '2025-07-02 19:32:12', '2025-07-02 19:32:51'),
(17, 'App\\Models\\User', 2, 'API-token', '2feab998afabbe8fc390dc4e87e0a9de4fe00afd7bcb14dc030196e69cac64d3', '[\"*\"]', '2025-07-03 16:19:51', NULL, '2025-07-03 16:17:58', '2025-07-03 16:19:51'),
(19, 'App\\Models\\User', 2, 'API-token', '8fec3ac729e1930247686e592afc78c280f5132160a76f437ad3463aaa96b9b8', '[\"*\"]', '2025-07-09 19:40:43', NULL, '2025-07-09 18:07:07', '2025-07-09 19:40:43'),
(20, 'App\\Models\\User', 3, 'API-token', '9b882af502daa9e6e603c5aa230f7cb21d2ea871ed3fb4b508501da772763b80', '[\"*\"]', '2025-08-12 20:07:31', NULL, '2025-07-10 13:00:25', '2025-08-12 20:07:31'),
(21, 'App\\Models\\User', 4, 'API-token', '4ff4e3508522d891ae353e44393eb64fa5cf0b4ef586826d4e5117ed9ec3bada', '[\"*\"]', NULL, NULL, '2025-07-23 18:47:37', '2025-07-23 18:47:37'),
(22, 'App\\Models\\User', 5, 'API-token', '300901fc284e056a0b2b330263f9f1f6df14daaa461c9bdeebf116d779881bb6', '[\"*\"]', '2025-07-23 18:48:42', NULL, '2025-07-23 18:48:36', '2025-07-23 18:48:42'),
(23, 'App\\Models\\User', 5, 'API-token', '111c5cc58c0b97e407ab8fe415c965084373c897f529ee81ef7663f1e9f38307', '[\"*\"]', NULL, NULL, '2025-07-23 18:58:35', '2025-07-23 18:58:35'),
(24, 'App\\Models\\User', 4, 'API-token', 'a6c129bbd74d577335e1c5dd103c1607d7754f593a6f8d330e0cd6bbc13ba708', '[\"*\"]', NULL, NULL, '2025-07-23 19:59:12', '2025-07-23 19:59:12'),
(25, 'App\\Models\\User', 5, 'API-token', '17cc6e495eaa940f940837f6954101c8e82ebeca8a88f52fe0030720ebb2c230', '[\"*\"]', '2025-07-23 20:00:54', NULL, '2025-07-23 20:00:11', '2025-07-23 20:00:54'),
(26, 'App\\Models\\User', 4, 'API-token', 'c5d5740b88bb3a6df0ac6214372373ac458e0f8dc4c392248174a78281245866', '[\"*\"]', NULL, NULL, '2025-07-23 20:01:29', '2025-07-23 20:01:29'),
(27, 'App\\Models\\User', 4, 'API-token', '197fc5606cae65f2b54a1e1ff9b639dbad179d4a1de3f6cbf823a41b4492dda2', '[\"*\"]', NULL, NULL, '2025-07-23 20:12:54', '2025-07-23 20:12:54'),
(28, 'App\\Models\\User', 5, 'API-token', '6df1ec154cf5ac27e9f76b647451f2d372eb907cc01bec4dff2123e4a34d2c2b', '[\"*\"]', '2025-07-23 20:14:15', NULL, '2025-07-23 20:13:25', '2025-07-23 20:14:15'),
(29, 'App\\Models\\User', 5, 'API-token', '3f65cef780cb842239bfb90c95ec34530c0bb09c27627f140b451a0293aed092', '[\"*\"]', NULL, NULL, '2025-07-23 20:15:32', '2025-07-23 20:15:32'),
(30, 'App\\Models\\User', 5, 'API-token', '0d1d1ca3cda9c2dac5556ae37d371bdc13fc2730e82cff4ae16127b7c78f0d5a', '[\"*\"]', '2025-07-23 20:41:05', NULL, '2025-07-23 20:17:22', '2025-07-23 20:41:05'),
(31, 'App\\Models\\User', 5, 'API-token', '46077453408692568c84ac5996a708fd39b7e803960c086452cd1d03c9d9adc0', '[\"*\"]', '2025-07-24 20:44:00', NULL, '2025-07-23 20:51:59', '2025-07-24 20:44:00'),
(32, 'App\\Models\\User', 5, 'API-token', 'be2a82bcb85737c53cb40cecdea647aa9c860e6b3dadb1904e09c34c0873481b', '[\"*\"]', '2025-07-23 22:10:00', NULL, '2025-07-23 22:06:38', '2025-07-23 22:10:00'),
(33, 'App\\Models\\User', 1, 'API-token', '7a636c646261bd7f361e32ffd699130ee9f6114b53bf76409aeaf38555e0a88b', '[\"*\"]', '2025-07-24 23:57:01', NULL, '2025-07-24 20:52:58', '2025-07-24 23:57:01'),
(34, 'App\\Models\\User', 2, 'API-token', '38525032078d80f0dca13beb2ccdd4ce3cb4c3fdb18638c2b00ee58473bd6a06', '[\"*\"]', '2025-08-12 18:47:37', NULL, '2025-08-12 18:32:55', '2025-08-12 18:47:37'),
(35, 'App\\Models\\User', 3, 'API-token', 'c694a042b31e3c16034820424d5c95fbeee2eba3b8c01f5d6d579a80d936d64f', '[\"*\"]', '2025-08-13 13:10:23', NULL, '2025-08-13 13:09:13', '2025-08-13 13:10:23');

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `stripe_price_id` varchar(255) DEFAULT NULL COMMENT 'Stripe price ID for the plan',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(255) NOT NULL DEFAULT 'USD',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `name`, `slug`, `description`, `stripe_price_id`, `price`, `currency`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Gold Plan', 'gold-plan', 'This is a free plan with 7.5% tax.', 'price_1RdfRSIKKEDUbdbtrTA4yrKk', 0.00, 'USD', 1, '2025-06-23 13:44:55', '2025-06-23 13:44:55'),
(2, 'Titanium Plan', 'titanium-plan', 'This is a Titanium plan no tax.', 'price_1RdfSiIKKEDUbdbtCFjbYHcE', 50.00, 'USD', 1, '2025-06-23 13:44:55', '2025-06-23 13:44:55');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` double DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL COMMENT 'Background Color of the product',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`image`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `user_id`, `category_id`, `name`, `company`, `description`, `price`, `color`, `status`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'product3', 'companyname3', 'updateddecription', 20, NULL, 1, '[\"http:\\/\\/127.0.0.1:8000\\/vendor\\/products\\/images\\/1749689928_684a2648cde43.png\",\"http:\\/\\/127.0.0.1:8000\\/vendor\\/products\\/images\\/1749689928_684a2648ceafe.jpg\"]', '2025-06-11 19:58:49', '2025-06-12 12:49:38'),
(2, 1, 2, 'product2231', 'companyname2231', 'asdasdasdasdassdasd121312231', 80, NULL, 0, NULL, '2025-06-12 12:17:29', '2025-06-12 12:28:39'),
(3, 2, 3, 'product3', 'companyname3', 'updateddecription', 120, NULL, 1, '[\"http:\\/\\/127.0.0.1:8000\\/vendor\\/products\\/images\\/1749750709_684b13b52e62d.png\",\"http:\\/\\/127.0.0.1:8000\\/vendor\\/products\\/images\\/1749750709_684b13b52f551.png\"]', '2025-06-12 12:46:49', '2025-06-12 12:51:49'),
(4, 1, 3, 'product3fsdfd', 'companyname3sdfsd', 'asdasdasdasdassdasd3dsfdfsdfsdf', 700, '#fff', 1, '[\"http:\\/\\/127.0.0.1:8000\\/vendor\\/products\\/images\\/1751402956_686449cc4223a.png\"]', '2025-07-01 15:49:16', '2025-07-01 15:51:21');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `reviewable_type` varchar(255) NOT NULL,
  `reviewable_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `review` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `reviewable_type`, `reviewable_id`, `rating`, `review`, `created_at`, `updated_at`) VALUES
(6, 1, 'App\\Models\\Service', 6, 5, 'asdasdasddwerwerwerwerretrt', '2025-08-07 13:16:15', '2025-08-07 13:51:09'),
(7, 1, 'App\\Models\\Service', 6, 5, 'asdasdasddwerwerwerwerretrt', '2025-08-07 13:51:24', '2025-08-07 13:51:24'),
(11, 1, 'App\\Models\\Service', 6, 5, 'asdasdasddwerwerwerwerretrt', '2025-08-07 17:29:22', '2025-08-07 17:29:22'),
(12, 1, 'App\\Models\\Service', 6, 5, 'asdasdasddwerwerwerwerretrt', '2025-08-07 17:29:31', '2025-08-07 17:29:31'),
(13, 1, 'App\\Models\\Service', 6, 5, 'asdasdasddwerwerwerwerretrt', '2025-08-07 17:30:40', '2025-08-07 17:30:40'),
(15, 1, 'App\\Models\\Service', 6, 5, 'asdasdasddwerwerwerwerretrt', '2025-08-07 17:32:26', '2025-08-07 17:32:26'),
(16, 1, 'App\\Models\\Service', 6, 5, 'asdasdasddwerwerwerwerretrt', '2025-08-07 17:57:20', '2025-08-07 17:57:20'),
(17, 1, 'App\\Models\\Service', 6, 5, 'asdasdasddwerwerwerwerretrt', '2025-08-07 17:59:34', '2025-08-07 17:59:34'),
(18, 1, 'App\\Models\\Service', 6, 5, 'asdasdasddwerwerwerwerretrt1231232', '2025-08-07 18:00:37', '2025-08-07 18:00:37'),
(20, 1, 'App\\Models\\Service', 6, 5, 'asdasdasddwerwerwerwerretrt1231232526', '2025-08-07 18:02:59', '2025-08-07 18:02:59'),
(21, 1, 'App\\Models\\Service', 6, 5, 'asdasdasddwerwerwerwerretrt1231232526qwe', '2025-08-07 18:03:06', '2025-08-07 18:03:06'),
(22, 1, 'App\\Models\\Product', 1, 5, 'asdasdasddwerwerwerwerretrt1231232526qweproduct', '2025-08-07 18:03:23', '2025-08-07 18:03:23'),
(23, 1, 'App\\Models\\Product', 1, 5, 'asdasdasddwerwerwerwerretrt1231232526qweproductdsadasda', '2025-08-12 15:49:33', '2025-08-12 15:49:33'),
(24, 1, 'App\\Models\\Product', 1, 5, 'asdasdasddwerwerwerwerretrt1231232526qweproductdsadasda', '2025-08-12 15:50:35', '2025-08-12 15:50:35');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Foreign key to the users table, representing the vendor of the service',
  `category_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Foreign key to the categories table, representing the category of the service',
  `name` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` double NOT NULL DEFAULT 0,
  `discounted_price` double DEFAULT NULL COMMENT 'Discounted price of the service',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 for active, 0 for inactive',
  `charge_type` varchar(255) DEFAULT 'daily',
  `image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON array of image URLs for the service' CHECK (json_valid(`image`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `user_id`, `category_id`, `name`, `company`, `description`, `price`, `discounted_price`, `status`, `charge_type`, `image`, `created_at`, `updated_at`, `address`, `city`, `state`, `country`, `zip_code`) VALUES
(6, 1, 3, 'wqqw', 'casd', 'dsfadfsdafsdafsd', 123, 80, 0, 'daily', '[\"http:\\/\\/127.0.0.1:8000\\/vendor\\/services\\/images\\/1749678888_6849fb28396ba.jpg\",\"http:\\/\\/127.0.0.1:8000\\/vendor\\/services\\/images\\/1749678888_6849fb283bcd4.jpg\"]', '2025-06-10 18:23:47', '2025-06-12 13:23:16', NULL, NULL, NULL, NULL, NULL),
(7, 1, 1, 'wqqw', 'casd', 'dsfadfsdafsdafsd', 123, 100, 0, 'daily', '[\"http:\\/\\/127.0.0.1:8000\\/vendor\\/services\\/images\\/1751403794_68644d12cf5f8.png\",\"http:\\/\\/127.0.0.1:8000\\/vendor\\/services\\/images\\/1751403794_68644d12d090a.png\"]', '2025-06-10 18:27:04', '2025-07-01 16:03:14', NULL, NULL, NULL, NULL, NULL),
(9, 2, 3, 'Grooming', 'asdfkjfhaklsdjfhaklsd', 'test description for service test description for service', 270, 150, 1, 'daily', '[\"http:\\/\\/127.0.0.1:8000\\/vendor\\/services\\/images\\/1749667114_6849cd2acfc62.png\",\"http:\\/\\/127.0.0.1:8000\\/vendor\\/services\\/images\\/1749667114_6849cd2ad0c28.jpg\",\"http:\\/\\/127.0.0.1:8000\\/vendor\\/services\\/images\\/1749667114_6849cd2ad11bc.jpg\",\"http:\\/\\/127.0.0.1:8000\\/vendor\\/services\\/images\\/1749667114_6849cd2ad16fb.jpg\"]', '2025-06-11 13:38:34', '2025-06-11 13:38:34', NULL, NULL, NULL, NULL, NULL),
(10, 5, 2, 'Service 01', 'Locationnn', 'Descriptionnnn', 222, 200, 1, 'daily', '[\"https:\\/\\/testingdemolink.com\\/custom_live\\/veteranse\\/vendor\\/services\\/images\\/1753308139_68815beb7e267.JPG\",\"https:\\/\\/testingdemolink.com\\/custom_live\\/veteranse\\/vendor\\/services\\/images\\/1753308139_68815beb7e976.JPG\",\"https:\\/\\/testingdemolink.com\\/custom_live\\/veteranse\\/vendor\\/services\\/images\\/1753308139_68815beb7ec65.JPG\"]', '2025-07-23 22:02:19', '2025-07-23 22:02:19', NULL, NULL, NULL, NULL, NULL),
(11, 5, 3, 'Grooming', 'asdfkjfhaklsdjfhaklsd', 'test description for service test description for service', 270, 120, 1, 'daily', '[\"https:\\/\\/testingdemolink.com\\/custom_live\\/veteranse\\/vendor\\/services\\/images\\/1753308552_68815d8813ef0.png\",\"https:\\/\\/testingdemolink.com\\/custom_live\\/veteranse\\/vendor\\/services\\/images\\/1753308552_68815d881407b.png\",\"https:\\/\\/testingdemolink.com\\/custom_live\\/veteranse\\/vendor\\/services\\/images\\/1753308552_68815d8814140.png\"]', '2025-07-23 22:09:12', '2025-07-23 22:09:12', NULL, NULL, NULL, NULL, NULL),
(12, 5, 3, 'Grooming', 'asdfkjfhaklsdjfhaklsd', 'test description for service test description for service', 270, 130, 1, 'daily', '[\"https:\\/\\/testingdemolink.com\\/custom_live\\/veteranse\\/vendor\\/services\\/images\\/1753308558_68815d8e4ffcc.png\",\"https:\\/\\/testingdemolink.com\\/custom_live\\/veteranse\\/vendor\\/services\\/images\\/1753308558_68815d8e500fe.png\",\"https:\\/\\/testingdemolink.com\\/custom_live\\/veteranse\\/vendor\\/services\\/images\\/1753308558_68815d8e501bf.png\"]', '2025-07-23 22:09:18', '2025-07-23 22:09:18', NULL, NULL, NULL, NULL, NULL),
(13, 1, 3, 'Groomingggggggg', 'asdfkjfhaklsdjfhaklsd', 'test description for service test description for service', 270, 150, 1, 'daily', '[\"https:\\/\\/testingdemolink.com\\/custom_live\\/veteranse\\/vendor\\/services\\/images\\/1753390138_68829c3a7691e.jpg\"]', '2025-07-24 20:48:58', '2025-07-24 20:48:58', NULL, NULL, NULL, NULL, NULL),
(14, 1, 3, 'Groominggggggggwwwww', 'asdfkjfhaklsdjfhaklsd', 'test description for service test description for service', 270, 200, 1, 'daily', '[\"https:\\/\\/testingdemolink.com\\/custom_live\\/veteranse\\/vendor\\/services\\/images\\/1753390379_68829d2b05405.jpg\"]', '2025-07-24 20:52:59', '2025-07-24 20:52:59', NULL, NULL, NULL, NULL, NULL),
(15, 1, 3, 'Grooming', 'asdfkjfhaklsdjfhaklsd', 'test description for service test description for service', 270, 200, 1, 'daily', '[\"https:\\/\\/testingdemolink.com\\/custom_live\\/veteranse\\/vendor\\/services\\/images\\/1753390552_68829dd8a95fa.png\",\"https:\\/\\/testingdemolink.com\\/custom_live\\/veteranse\\/vendor\\/services\\/images\\/1753390552_68829dd8a976b.png\",\"https:\\/\\/testingdemolink.com\\/custom_live\\/veteranse\\/vendor\\/services\\/images\\/1753390552_68829dd8a981c.png\"]', '2025-07-24 20:55:52', '2025-07-24 20:55:52', NULL, NULL, NULL, NULL, NULL),
(16, 1, 3, 'Testing Location', 'casd', 'dsfadfsdafsdafsd', 123, 100, 1, 'daily', '[\"http:\\/\\/127.0.0.1:8000\\/vendor\\/services\\/images\\/1753474559_6883e5ffdaee8.jpg\"]', '2025-07-25 15:11:45', '2025-07-25 15:15:59', 'New york Times Squre', 'New York', 'NY', 'USA', '10036'),
(17, 1, 3, 'Testing Charge Type', 'casd', 'dsfadfsdafsdafsd', 123, 100, 1, 'daily', '[\"http:\\/\\/127.0.0.1:8000\\/vendor\\/services\\/images\\/1755031108_689ba6444dfab.jpg\"]', '2025-08-12 15:33:44', '2025-08-12 15:38:28', 'New york Times', 'New York', 'NY', 'USA', '10036');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stripe_accounts`
--

CREATE TABLE `stripe_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `stripe_account_id` varchar(255) NOT NULL COMMENT 'Unique identifier for the Stripe account',
  `charges_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stripe_accounts`
--

INSERT INTO `stripe_accounts` (`id`, `user_id`, `stripe_account_id`, `charges_enabled`, `created_at`, `updated_at`) VALUES
(3, 2, 'acct_1Rj750INsMV8CSKS', 0, '2025-07-09 18:07:11', '2025-07-09 18:07:11'),
(4, 5, 'acct_1Ro7iSErKid3rJ9F', 0, '2025-07-23 18:48:39', '2025-07-23 20:25:41'),
(5, 1, 'acct_1RoW8NEv4oAPSXtz', 0, '2025-07-24 20:53:01', '2025-07-24 20:53:01');

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Name of the tax',
  `rate` decimal(5,2) NOT NULL COMMENT 'Tax rate in percentage',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Indicates if the tax is currently active',
  `type` varchar(255) NOT NULL DEFAULT 'percentage' COMMENT 'Type of tax, e.g., percentage or fixed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taxes`
--

INSERT INTO `taxes` (`id`, `name`, `rate`, `is_active`, `type`, `created_at`, `updated_at`) VALUES
(1, 'plateform tax', 7.50, 1, 'percentage', '2025-06-18 13:32:19', '2025-06-18 13:32:19'),
(2, 'platefornm tax', 7.50, 1, 'percentage', '2025-07-07 19:05:33', '2025-07-07 19:05:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'this is for username',
  `email` varchar(255) NOT NULL,
  `wallet_balance` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'User wallet balance',
  `phone` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user' COMMENT 'this is for user role',
  `stripe_account_connected` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indicates if the user has connected their Stripe account',
  `vendor_store_title` varchar(255) DEFAULT NULL,
  `vendor_store_description` text DEFAULT NULL,
  `vendor_store_image` varchar(255) DEFAULT NULL,
  `vendor_store_gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vendor_store_gallery`)),
  `otp` varchar(255) DEFAULT NULL,
  `otp_verified_at` timestamp NULL DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `login_type` varchar(255) DEFAULT 'otp',
  `status` tinyint(1) DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `has_subscribed` tinyint(1) NOT NULL DEFAULT 0,
  `stripe_payment_intent` varchar(255) DEFAULT NULL COMMENT 'Stripe payement intent/method',
  `plan_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Stripe ID of the plan',
  `subscription_plan` varchar(255) DEFAULT NULL COMMENT 'Current subscription plan slug',
  `stripe_price` varchar(255) DEFAULT NULL COMMENT 'Stripe price id for the plan',
  `stripe_customer_id` varchar(255) DEFAULT NULL COMMENT 'Stripe customer ID',
  `stripe_subscription_id` varchar(255) DEFAULT NULL COMMENT 'Stripe subscription ID',
  `subscription_status` varchar(255) DEFAULT NULL COMMENT 'subscription status',
  `stripe_payment_status` varchar(255) DEFAULT NULL,
  `stripe_payment_type` varchar(255) DEFAULT NULL,
  `subscription_started_at` timestamp NULL DEFAULT NULL COMMENT 'Subscription start date',
  `subscription_renew_at` timestamp NULL DEFAULT NULL COMMENT 'renewal date of subscription\r\n',
  `subscription_ends_at` timestamp NULL DEFAULT NULL COMMENT 'Subscription end date after cancelation\r\n',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `avatar`, `name`, `email`, `wallet_balance`, `phone`, `gender`, `dob`, `role`, `stripe_account_connected`, `vendor_store_title`, `vendor_store_description`, `vendor_store_image`, `vendor_store_gallery`, `otp`, `otp_verified_at`, `otp_expires_at`, `login_type`, `status`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `has_subscribed`, `stripe_payment_intent`, `plan_id`, `subscription_plan`, `stripe_price`, `stripe_customer_id`, `stripe_subscription_id`, `subscription_status`, `stripe_payment_status`, `stripe_payment_type`, `subscription_started_at`, `subscription_renew_at`, `subscription_ends_at`, `deleted_at`) VALUES
(1, 'http://127.0.0.1:8000/avatars/1749169884.png', 'Ahmed', 'luisalfonsoweb99@gmail.com', 217.50, '+923242534131', 'male', '1997-05-07', 'vendor', 0, 'Barbar', 'qweqweqweqweqweqwe qweqweqweqweqweqwe qweqweqweqweqweqwe', 'http://127.0.0.1:8000/vendor/store/covers/1749513244.png', '[\"http:\\/\\/127.0.0.1:8000\\/vendor\\/store\\/gallery\\/1749513244_0.jpg\",\"http:\\/\\/127.0.0.1:8000\\/vendor\\/store\\/gallery\\/1749513244_1.jpg\"]', NULL, '2025-07-24 20:52:58', '2025-07-24 20:57:50', 'otp', 1, NULL, '$2y$12$yK.gCejhiMp30oGnB6G0..KT21wcG6fwwFLKxBv44h2rPIR.pLe7a', NULL, '2025-06-05 18:14:49', '2025-08-12 17:25:09', 1, 'pm_card_visa', 2, 'Titanium Plan', 'price_1RdfSiIKKEDUbdbtCFjbYHcE', 'cus_SYn90xTlFcltnD', 'sub_1RoW3RIKKEDUbdbtPjrJJeVa', 'active', 'paid', 'card', '2025-07-24 20:47:55', '2025-08-24 20:47:52', NULL, NULL),
(2, 'http://127.0.0.1:8000/avatars/1749170800.png', 'asdasdasdasd', 'test@webdesignglory.com', 0.00, NULL, NULL, NULL, 'vendor', 0, NULL, NULL, NULL, NULL, NULL, '2025-08-12 18:32:55', '2025-08-12 18:36:31', 'otp', 1, NULL, '$2y$12$9LHMe5W9Nh7LOWLQkMzmNugW3uo.MFY3eQBGVzDAmIvm9QbjkzEUK', NULL, '2025-06-05 19:46:41', '2025-08-12 18:32:55', 1, 'pm_card_visa', 2, 'Titanium Plan', 'price_1RdfSiIKKEDUbdbtCFjbYHcE', 'cus_SbmjS6vWtX9wgX', 'sub_1Rj8VAIKKEDUbdbtN2pXTu7O', 'canceled', 'paid', 'card', '2025-06-30 19:38:18', '2025-08-09 19:38:16', '2025-08-09 19:38:16', NULL),
(3, NULL, 'testing', 'test@webdesign.com', 167.50, NULL, NULL, NULL, 'vendor', 0, 'asdasd', 'erwerwerwe', 'http://127.0.0.1:8000/vendor/store/covers/1755108623.png', '[\"http:\\/\\/127.0.0.1:8000\\/vendor\\/store\\/gallery\\/1755108623_689cd50f72a33.jpg\",\"http:\\/\\/127.0.0.1:8000\\/vendor\\/store\\/gallery\\/1755108623_689cd50f72ecd.jpg\",\"http:\\/\\/127.0.0.1:8000\\/vendor\\/store\\/gallery\\/1755108623_689cd50f736e3.jpg\",\"http:\\/\\/127.0.0.1:8000\\/vendor\\/store\\/gallery\\/1755108623_689cd50f73bbd.png\"]', NULL, '2025-08-13 13:09:13', '2025-08-13 13:13:44', 'otp', 1, NULL, '$2y$12$PwZm.q7SAejDe46TT8i/ouXEM1a6wxrQvjY7LP0teB1IbyyA5Kicq', NULL, '2025-07-02 15:47:10', '2025-08-13 13:10:23', 0, NULL, NULL, NULL, NULL, 'cus_SeOnpIoJYaVZPs', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'https://testingdemolink.com/custom_live/veteranse/avatars/1753296442.JPG', 'Mine', 'mine@yopmail.com', 0.00, NULL, NULL, NULL, 'user', 0, NULL, NULL, NULL, NULL, 'WjgT', '2025-07-23 20:12:54', '2025-07-23 20:19:29', 'otp', 1, NULL, '$2y$12$ZlJEWfp7zY6qRBeNqPjMkeT5mnHQkG2g21vZAtbPByGcRlx0ceIsi', NULL, '2025-07-23 18:47:22', '2025-07-23 20:14:29', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'https://testingdemolink.com/custom_live/veteranse/avatars/1753296500.JPG', 'Mine', 'minee@yopmail.com', 50.00, NULL, NULL, NULL, 'user', 0, NULL, NULL, NULL, NULL, NULL, '2025-07-23 22:06:38', '2025-07-23 22:11:14', 'otp', 1, NULL, '$2y$12$oEjgIE40m3XeBowfcuRAyebgrGuTobsqpGDofslRPAHW8nCcw1GeO', NULL, '2025-07-23 18:48:20', '2025-07-23 22:06:38', 1, 'pm_1RoAdQIKKEDUbdbtN1hiWGkO', 2, 'Titanium Plan', 'price_1RdfSiIKKEDUbdbtCFjbYHcE', 'cus_SjdsnUTNSmJy75', 'sub_1RoAdRIKKEDUbdbtcrsdoors', 'active', 'paid', 'card', '2025-07-23 21:55:40', '2025-08-23 21:55:37', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallet_transactions`
--

INSERT INTO `wallet_transactions` (`id`, `user_id`, `amount`, `type`, `reason`, `created_at`, `updated_at`) VALUES
(1, 1, 50.00, 'credit', 'Refund for subscription cancellation (within 7 days)', '2025-06-25 13:11:34', '2025-06-25 13:11:34'),
(3, 1, 50.00, 'debit', 'Subscribed to Titanium Plan plan via wallet', '2025-06-25 14:33:06', '2025-06-25 14:33:06'),
(4, 1, 50.00, 'credit', 'Refund for subscription cancellation (within 7 days)', '2025-06-25 14:42:25', '2025-06-25 14:42:25'),
(5, 1, 50.00, 'debit', 'Subscribed to Titanium Plan plan via wallet', '2025-06-25 14:42:53', '2025-06-25 14:42:53'),
(6, 1, 50.00, 'credit', 'Refund for subscription cancellation (within 7 days)', '2025-07-02 17:24:13', '2025-07-02 17:24:13'),
(7, 1, 50.00, 'debit', 'Subscribed to Titanium Plan plan via wallet', '2025-07-02 17:26:18', '2025-07-02 17:26:18'),
(8, 2, 50.00, 'credit', 'Refund for subscription cancellation (within 7 days)', '2025-07-02 17:31:24', '2025-07-02 17:31:24'),
(9, 1, 50.00, 'credit', 'Refund for subscription cancellation (within 7 days)', '2025-07-03 16:03:45', '2025-07-03 16:03:45'),
(10, 2, 50.00, 'debit', 'Subscribed to Titanium Plan plan via wallet', '2025-07-09 18:07:47', '2025-07-09 18:07:47'),
(11, 2, 50.00, 'credit', 'Refund for subscription cancellation (within 7 days)', '2025-07-09 18:13:20', '2025-07-09 18:13:20'),
(12, 2, 50.00, 'credit', 'Refund for subscription cancellation (within 7 days)', '2025-07-09 18:19:51', '2025-07-09 18:19:51'),
(13, 2, 50.00, 'credit', 'Refund for subscription cancellation (within 7 days)', '2025-07-09 18:23:28', '2025-07-09 18:23:28'),
(14, 2, 50.00, 'credit', 'Refund for subscription cancellation (within 7 days)', '2025-07-09 18:27:06', '2025-07-09 18:27:06'),
(15, 2, 50.00, 'debit', 'Subscribed to Titanium Plan plan via wallet', '2025-07-09 18:28:38', '2025-07-09 18:28:38'),
(16, 1, 50.00, 'debit', 'Subscribed to Titanium Plan plan via wallet', '2025-07-11 12:46:48', '2025-07-11 12:46:48'),
(17, 1, 50.00, 'credit', 'Refund for subscription cancellation (within 7 days)', '2025-07-11 12:52:25', '2025-07-11 12:52:25'),
(18, 1, 50.00, 'debit', 'Subscribed to Titanium Plan plan via wallet', '2025-07-11 12:52:40', '2025-07-11 12:52:40'),
(19, 1, 400.00, 'debit', 'Purchased products from cart', '2025-07-11 16:09:31', '2025-07-11 16:09:31'),
(20, 1, 400.00, 'debit', 'Purchased products from cart', '2025-07-11 16:22:17', '2025-07-11 16:22:17'),
(21, 5, 50.00, 'credit', 'Refund for subscription cancellation (within 7 days)', '2025-07-23 21:55:04', '2025-07-23 21:55:04'),
(22, 1, 167.50, 'credit', 'Booking cancelled for : wqqw', '2025-07-25 12:18:39', '2025-07-25 12:18:39'),
(23, 3, 167.50, 'credit', 'Booking cancelled by vendor for : wqqw', '2025-07-25 13:48:13', '2025-07-25 13:48:13');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `wishlistable_type` varchar(255) NOT NULL,
  `wishlistable_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`id`, `user_id`, `wishlistable_type`, `wishlistable_id`, `created_at`, `updated_at`) VALUES
(8, 1, 'App\\Models\\Service', 7, '2025-06-16 15:41:23', '2025-06-16 15:41:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `add_ons`
--
ALTER TABLE `add_ons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `add_on_service`
--
ALTER TABLE `add_on_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `add_on_service_add_on_id_foreign` (`add_on_id`),
  ADD KEY `add_on_service_service_id_foreign` (`service_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_user_id_foreign` (`user_id`),
  ADD KEY `bookings_service_id_foreign` (`service_id`),
  ADD KEY `bookings_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `booking_add_on`
--
ALTER TABLE `booking_add_on`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_add_on_booking_id_foreign` (`booking_id`),
  ADD KEY `booking_add_on_add_on_id_foreign` (`add_on_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carts_user_id_foreign` (`user_id`);

--
-- Indexes for table `cart_products`
--
ALTER TABLE `cart_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cart_products_cart_id_product_id_unique` (`cart_id`,`product_id`),
  ADD KEY `cart_products_product_id_foreign` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indexes for table `order_product`
--
ALTER TABLE `order_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_product_order_id_foreign` (`order_id`),
  ADD KEY `order_product_product_id_foreign` (`product_id`),
  ADD KEY `order_product_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plans_name_unique` (`name`),
  ADD UNIQUE KEY `plans_slug_unique` (`slug`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_user_id_foreign` (`user_id`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_reviewable_type_reviewable_id_index` (`reviewable_type`,`reviewable_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `services_user_id_foreign` (`user_id`),
  ADD KEY `services_category_id_foreign` (`category_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stripe_accounts`
--
ALTER TABLE `stripe_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stripe_accounts_stripe_account_id_unique` (`stripe_account_id`),
  ADD KEY `stripe_accounts_user_id_foreign` (`user_id`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_plan_id_foreign` (`plan_id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallet_transactions_user_id_foreign` (`user_id`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wishlists_user_id_wishlistable_id_wishlistable_type_unique` (`user_id`,`wishlistable_id`,`wishlistable_type`),
  ADD KEY `wishlists_wishlistable_type_wishlistable_id_index` (`wishlistable_type`,`wishlistable_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `add_ons`
--
ALTER TABLE `add_ons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `add_on_service`
--
ALTER TABLE `add_on_service`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `booking_add_on`
--
ALTER TABLE `booking_add_on`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart_products`
--
ALTER TABLE `cart_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_product`
--
ALTER TABLE `order_product`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `stripe_accounts`
--
ALTER TABLE `stripe_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `add_on_service`
--
ALTER TABLE `add_on_service`
  ADD CONSTRAINT `add_on_service_add_on_id_foreign` FOREIGN KEY (`add_on_id`) REFERENCES `add_ons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `add_on_service_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `booking_add_on`
--
ALTER TABLE `booking_add_on`
  ADD CONSTRAINT `booking_add_on_add_on_id_foreign` FOREIGN KEY (`add_on_id`) REFERENCES `add_ons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_add_on_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_products`
--
ALTER TABLE `cart_products`
  ADD CONSTRAINT `cart_products_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_product`
--
ALTER TABLE `order_product`
  ADD CONSTRAINT `order_product_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_product_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `products_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `services_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stripe_accounts`
--
ALTER TABLE `stripe_accounts`
  ADD CONSTRAINT `stripe_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`);

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
