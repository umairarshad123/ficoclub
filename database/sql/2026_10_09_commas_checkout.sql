-- Commas checkout tables — raw-SQL fallback for phpMyAdmin when
-- `php artisan migrate` doesn't run on the cPanel host.
-- Mirrors:
--   2026_10_09_000001_create_checkout_orders_table
--   2026_10_09_000002_add_provider_columns
-- Run once. If a statement fails with "already exists" / "Duplicate column",
-- that part was already applied — skip it and run the rest.

CREATE TABLE `checkout_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `plan_key` varchar(255) NOT NULL,
  `plan_label` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `commas_product_id` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(10) NOT NULL,
  `zip` varchar(20) NOT NULL,
  `referral_code` varchar(50) DEFAULT NULL,
  `marketing_opt_in` tinyint(1) NOT NULL DEFAULT 0,
  `agreed_terms_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `commas_session_id` varchar(255) DEFAULT NULL,
  `client_transaction_ref` varchar(255) DEFAULT NULL,
  `commas_transaction_id` varchar(255) DEFAULT NULL,
  `commas_payment_id` varchar(255) DEFAULT NULL,
  `commas_buyer_id` varchar(255) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT NULL,
  `confirmed_via` varchar(20) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `mismatch_reason` text DEFAULT NULL,
  `subscription_id` bigint unsigned DEFAULT NULL,
  `notified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `checkout_orders_uuid_unique` (`uuid`),
  UNIQUE KEY `checkout_orders_invoice_number_unique` (`invoice_number`),
  UNIQUE KEY `checkout_orders_commas_transaction_id_unique` (`commas_transaction_id`),
  UNIQUE KEY `checkout_orders_commas_payment_id_unique` (`commas_payment_id`),
  KEY `checkout_orders_email_index` (`email`),
  KEY `checkout_orders_status_index` (`status`),
  KEY `checkout_orders_paid_at_index` (`paid_at`),
  KEY `checkout_orders_subscription_id_foreign` (`subscription_id`),
  CONSTRAINT `checkout_orders_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `subscriptions`  ADD COLUMN `provider` varchar(20) NOT NULL DEFAULT 'authorize_net', ADD INDEX `subscriptions_provider_index` (`provider`);
ALTER TABLE `payments`       ADD COLUMN `provider` varchar(20) NOT NULL DEFAULT 'authorize_net', ADD INDEX `payments_provider_index` (`provider`);
ALTER TABLE `webhook_events` ADD COLUMN `provider` varchar(20) NOT NULL DEFAULT 'authorize_net', ADD INDEX `webhook_events_provider_index` (`provider`);
ALTER TABLE `webhook_events` ADD COLUMN `processed_at` timestamp NULL DEFAULT NULL;

-- Tell Laravel both migrations are done so a later `migrate` won't re-run them.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_09_000001_create_checkout_orders_table', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_09_000002_add_provider_columns', MAX(`batch`) FROM `migrations`;
