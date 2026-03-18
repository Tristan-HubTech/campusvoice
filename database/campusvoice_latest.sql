
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `campusvoice` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `campusvoice`;
DROP TABLE IF EXISTS `admin_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `admin_user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(60) DEFAULT NULL,
  `target_id` bigint(20) unsigned DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `metadata` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `action_created_at` (`action`,`created_at`),
  KEY `admin_user_id` (`admin_user_id`),
  CONSTRAINT `admin_activity_logs_admin_user_id_foreign` FOREIGN KEY (`admin_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `admin_activity_logs` WRITE;
/*!40000 ALTER TABLE `admin_activity_logs` DISABLE KEYS */;
INSERT INTO `admin_activity_logs` VALUES (1,2,'auth.login','admin_user',2,'Admin logged into control panel.','{\"email\":\"admin@campusvoice.local\",\"role\":\"admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 17:22:53'),(2,2,'user.password_reset','user',4,'Force-reset password for user: Tya Suham (tyasuham@gmail.com)','{\"email_sent\":false}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 19:26:00'),(3,2,'auth.logout','admin_user',2,'Admin logged out from control panel.','{\"email\":\"admin@campusvoice.local\",\"role\":\"admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 19:57:15'),(4,2,'auth.login','admin_user',2,'Admin logged into control panel.','{\"email\":\"admin@campusvoice.local\",\"role\":\"admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 19:58:14'),(5,2,'user.deactivated','user',4,'Deactivated user account: Tya Suham (tyasuham@gmail.com)',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 19:58:23'),(6,2,'user.activated','user',4,'Activated user account: Tya Suham (tyasuham@gmail.com)',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 19:58:24'),(7,2,'auth.logout','admin_user',2,'Admin logged out from control panel.','{\"email\":\"admin@campusvoice.local\",\"role\":\"admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 19:59:16'),(8,2,'auth.login','admin_user',2,'Admin logged into control panel.','{\"email\":\"admin@campusvoice.local\",\"role\":\"admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 20:05:42');
/*!40000 ALTER TABLE `admin_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(180) NOT NULL,
  `body` text NOT NULL,
  `posted_by` bigint(20) unsigned DEFAULT NULL,
  `audience` varchar(20) NOT NULL DEFAULT 'all',
  `publish_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_posted_by_foreign` (`posted_by`),
  KEY `is_published_publish_at_expires_at` (`is_published`,`publish_at`,`expires_at`),
  CONSTRAINT `announcements_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `api_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(80) DEFAULT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `last_used_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_hash` (`token_hash`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `api_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `api_tokens` WRITE;
/*!40000 ALTER TABLE `api_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `api_tokens` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `feedback_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `feedback_categories` WRITE;
/*!40000 ALTER TABLE `feedback_categories` DISABLE KEYS */;
INSERT INTO `feedback_categories` VALUES (1,'Facility',NULL,1,'2026-03-18 16:42:41','2026-03-18 16:42:41'),(2,'Teacher',NULL,1,'2026-03-18 16:42:41','2026-03-18 16:42:41'),(3,'Service',NULL,1,'2026-03-18 16:42:41','2026-03-18 16:42:41'),(4,'Event',NULL,1,'2026-03-18 16:42:41','2026-03-18 16:42:41'),(5,'Academic',NULL,1,'2026-03-18 16:42:41','2026-03-18 16:42:41'),(6,'Security',NULL,1,'2026-03-18 16:42:41','2026-03-18 16:42:41'),(7,'Other',NULL,1,'2026-03-18 16:42:41','2026-03-18 16:42:41');
/*!40000 ALTER TABLE `feedback_categories` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `feedback_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback_replies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `feedback_id` bigint(20) unsigned NOT NULL,
  `admin_user_id` bigint(20) unsigned NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feedback_replies_admin_user_id_foreign` (`admin_user_id`),
  KEY `feedback_id` (`feedback_id`),
  CONSTRAINT `feedback_replies_admin_user_id_foreign` FOREIGN KEY (`admin_user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `feedback_replies_feedback_id_foreign` FOREIGN KEY (`feedback_id`) REFERENCES `feedbacks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `feedback_replies` WRITE;
/*!40000 ALTER TABLE `feedback_replies` DISABLE KEYS */;
/*!40000 ALTER TABLE `feedback_replies` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `feedbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedbacks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` int(11) unsigned NOT NULL,
  `type` varchar(20) NOT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'new',
  `submitted_at` datetime DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feedbacks_category_id_foreign` (`category_id`),
  KEY `user_id_category_id` (`user_id`,`category_id`),
  KEY `status_type` (`status`,`type`),
  CONSTRAINT `feedbacks_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `feedback_categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `feedbacks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `feedbacks` WRITE;
/*!40000 ALTER TABLE `feedbacks` DISABLE KEYS */;
/*!40000 ALTER TABLE `feedbacks` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026-03-19-000001','App\\Database\\Migrations\\CreateRolesTable','default','App',1773852161,1),(2,'2026-03-19-000002','App\\Database\\Migrations\\CreateUsersTable','default','App',1773852161,1),(3,'2026-03-19-000003','App\\Database\\Migrations\\CreateFeedbackCategoriesTable','default','App',1773852161,1),(4,'2026-03-19-000004','App\\Database\\Migrations\\CreateFeedbacksTable','default','App',1773852161,1),(5,'2026-03-19-000005','App\\Database\\Migrations\\CreateFeedbackRepliesTable','default','App',1773852161,1),(6,'2026-03-19-000006','App\\Database\\Migrations\\CreateAnnouncementsTable','default','App',1773852161,1),(7,'2026-03-19-000007','App\\Database\\Migrations\\CreateApiTokensTable','default','App',1773852161,1),(8,'2026-03-19-000008','App\\Database\\Migrations\\CreatePasswordOtpsTable','default','App',1773852161,1),(9,'2026-03-19-000009','App\\Database\\Migrations\\CreateAdminActivityLogsTable','default','App',1773854566,2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `password_otps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_otps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `purpose` varchar(30) NOT NULL DEFAULT 'password_reset',
  `otp_hash` varchar(255) NOT NULL,
  `attempts` tinyint(3) NOT NULL DEFAULT 0,
  `max_attempts` tinyint(3) NOT NULL DEFAULT 5,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_purpose_used_at_expires_at` (`email`,`purpose`,`used_at`,`expires_at`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_otps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `password_otps` WRITE;
/*!40000 ALTER TABLE `password_otps` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_otps` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `post_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `author_name` varchar(255) DEFAULT NULL,
  `author_email` varchar(255) DEFAULT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `body` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `post_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `voice_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `post_comments` WRITE;
/*!40000 ALTER TABLE `post_comments` DISABLE KEYS */;
INSERT INTO `post_comments` VALUES (1,2,'2026-03-18 11:15:48','Student','tyasuham@gmail.com',0,'awd'),(2,2,'2026-03-18 11:18:31','Student','tyasuham@gmail.com',0,'awd'),(3,2,'2026-03-18 11:18:33','Student','tyasuham@gmail.com',0,'ku');
/*!40000 ALTER TABLE `post_comments` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'system_admin','Full access to all modules','2026-03-18 16:42:41','2026-03-18 16:42:41'),(2,'admin','Manages feedback and content','2026-03-18 16:42:41','2026-03-18 16:42:41'),(3,'student','Submits and tracks feedback','2026-03-18 16:42:41','2026-03-18 16:42:41');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint(3) unsigned NOT NULL,
  `student_no` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `student_no` (`student_no`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,NULL,'System','Admin','sysadmin@campusvoice.local','$2y$10$/.mRpdIw0nzCBqyOQkYYNOV.N9ZebX9eeJlnZoEgu.Vgx1jsRGEha',NULL,1,NULL,'2026-03-18 16:42:41','2026-03-18 16:42:41',NULL),(2,2,NULL,'School','Admin','admin@campusvoice.local','$2y$10$2pdc92tJAXLUlQzD.Ug/AeB1Cv1Hxk9MC..aS3urDXNgRtygj28rO',NULL,1,'2026-03-18 20:05:42','2026-03-18 16:42:41','2026-03-18 20:05:42',NULL),(3,3,'DEMO-0001','Demo','Student','student@campusvoice.local','$2y$10$gAkqzvHfob/rjVvrsM0M8.QoQgeVJW0u79Ka02/vPoCMzDidDBrD.',NULL,1,NULL,'2026-03-18 16:42:41','2026-03-18 16:42:41',NULL),(4,3,'TMP-1001','Tya','Suham','tyasuham@gmail.com','$2y$10$.WAhIt0TLqZY5TRj0gFf4eRC6djbpKsE8zhjwgAWjMvrrWwaNzY3a',NULL,1,NULL,'2026-03-18 16:43:37','2026-03-18 19:58:24',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `voice_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voice_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `author_name` varchar(255) DEFAULT NULL,
  `author_email` varchar(255) DEFAULT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `image_url` text DEFAULT NULL,
  `author_role` varchar(50) DEFAULT 'student',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `voice_posts` WRITE;
/*!40000 ALTER TABLE `voice_posts` DISABLE KEYS */;
INSERT INTO `voice_posts` VALUES (1,'2026-03-18 10:58:10','awdawd','awdawd','Student','tyasuham@gmail.com',0,NULL,'student'),(2,'2026-03-18 11:15:35','Test Post','Testing author_role column','Test User','test@example.com',0,NULL,'teacher'),(3,'2026-03-18 19:49:58','dawdaw','wda',NULL,NULL,1,NULL,NULL);
/*!40000 ALTER TABLE `voice_posts` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

