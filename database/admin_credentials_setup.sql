-- Create admin_credentials table
CREATE TABLE IF NOT EXISTS `admin_credentials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_password_hash` varchar(255) NOT NULL,
  `last_password_changed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Insert initial master password (password: "admin")
INSERT INTO `admin_credentials` (`master_password_hash`, `last_password_changed_at`, `created_at`, `updated_at`) 
VALUES ('$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/GOK', NOW(), NOW(), NOW());
