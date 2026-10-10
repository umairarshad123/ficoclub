-- Commas transactions mirror — raw-SQL fallback for phpMyAdmin when
-- `php artisan migrate` doesn't run on the cPanel host.
-- Mirrors: 2026_10_10_000001_create_commas_transactions_table
-- Run once. If it fails with "already exists", it was already applied.

CREATE TABLE `commas_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `commas_id` varchar(255) NOT NULL,
  `transaction_date` timestamp NULL DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(40) DEFAULT NULL,
  `product_id` varchar(40) DEFAULT NULL,
  `product_title` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fee_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `refunded_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `refund_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `refund_count` smallint unsigned NOT NULL DEFAULT 0,
  `payment_type` varchar(40) DEFAULT NULL,
  `fund_release_on` timestamp NULL DEFAULT NULL,
  `fund_released` tinyint(1) NOT NULL DEFAULT 0,
  `raw` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commas_transactions_commas_id_unique` (`commas_id`),
  KEY `commas_transactions_transaction_date_index` (`transaction_date`),
  KEY `commas_transactions_customer_email_index` (`customer_email`),
  KEY `commas_transactions_product_id_index` (`product_id`),
  KEY `commas_transactions_fund_released_index` (`fund_released`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_10_000001_create_commas_transactions_table', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
