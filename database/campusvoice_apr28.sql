-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: campusvoice
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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

--
-- Table structure for table `admin_activity_logs`
--

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
  KEY `idx_logs_created` (`created_at`),
  CONSTRAINT `admin_activity_logs_admin_user_id_foreign` FOREIGN KEY (`admin_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_activity_logs`
--

LOCK TABLES `admin_activity_logs` WRITE;
/*!40000 ALTER TABLE `admin_activity_logs` DISABLE KEYS */;
INSERT INTO `admin_activity_logs` VALUES (1,2,'auth.login','admin_user',2,'Admin logged into control panel.','{\"email\":\"admin@campusvoice.local\",\"role\":\"admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 17:22:53'),(2,2,'user.password_reset','user',4,'Force-reset password for user: Tya Suham (tyasuham@gmail.com)','{\"email_sent\":false}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 19:26:00'),(3,2,'auth.logout','admin_user',2,'Admin logged out from control panel.','{\"email\":\"admin@campusvoice.local\",\"role\":\"admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 19:57:15'),(4,2,'auth.login','admin_user',2,'Admin logged into control panel.','{\"email\":\"admin@campusvoice.local\",\"role\":\"admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 19:58:14'),(5,2,'user.deactivated','user',4,'Deactivated user account: Tya Suham (tyasuham@gmail.com)',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 19:58:23'),(6,2,'user.activated','user',4,'Activated user account: Tya Suham (tyasuham@gmail.com)',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 19:58:24'),(7,2,'auth.logout','admin_user',2,'Admin logged out from control panel.','{\"email\":\"admin@campusvoice.local\",\"role\":\"admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 19:59:16'),(8,2,'auth.login','admin_user',2,'Admin logged into control panel.','{\"email\":\"admin@campusvoice.local\",\"role\":\"admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','2026-03-18 20:05:42'),(9,1,'auth.login','admin_user',1,'Admin logged into control panel.','{\"email\":\"sysadmin@campusvoice.local\",\"role\":\"system_admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 06:14:39'),(10,1,'announcement.created','announcement',1,'Created an announcement.','{\"title\":\"Announcement\",\"audience\":\"all\",\"published\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-19 06:32:24'),(11,1,'auth.login','admin_user',1,'Admin logged into control panel via master password.','{\"email\":\"sysadmin@campusvoice.local\",\"role\":\"system_admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-24 08:07:50'),(12,1,'auth.logout','admin_user',1,'Admin logged out from control panel.','{\"email\":\"sysadmin@campusvoice.local\",\"role\":\"system_admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-24 08:17:51'),(13,1,'auth.login','admin_user',1,'Admin logged into control panel via master password.','{\"email\":\"sysadmin@campusvoice.local\",\"role\":\"system_admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-27 05:53:11'),(14,1,'auth.logout','admin_user',1,'Admin logged out from control panel.','{\"email\":\"sysadmin@campusvoice.local\",\"role\":\"system_admin\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','2026-03-27 06:45:36');
/*!40000 ALTER TABLE `admin_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_credentials`
--

DROP TABLE IF EXISTS `admin_credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_credentials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_password_hash` varchar(255) NOT NULL,
  `last_password_changed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_credentials`
--

LOCK TABLES `admin_credentials` WRITE;
/*!40000 ALTER TABLE `admin_credentials` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_credentials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(180) NOT NULL,
  `body` text NOT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `posted_by` bigint(20) unsigned DEFAULT NULL,
  `audience` varchar(20) NOT NULL DEFAULT 'all',
  `publish_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `pinned` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_posted_by_foreign` (`posted_by`),
  KEY `is_published_publish_at_expires_at` (`is_published`,`publish_at`,`expires_at`),
  CONSTRAINT `announcements_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (1,'Announcement','Roger is bayot!!!!!!!!',NULL,1,'all',NULL,NULL,1,0,'2026-03-19 06:32:24','2026-03-19 06:32:24');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_tokens`
--

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

--
-- Dumping data for table `api_tokens`
--

LOCK TABLES `api_tokens` WRITE;
/*!40000 ALTER TABLE `api_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `api_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment_reactions`
--

DROP TABLE IF EXISTS `comment_reactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment_reactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `reaction_type` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `comment_user_unique` (`comment_id`,`user_id`),
  KEY `idx_comment_reactions_user` (`user_id`),
  CONSTRAINT `comment_reactions_comment_id_foreign` FOREIGN KEY (`comment_id`) REFERENCES `social_post_comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comment_reactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment_reactions`
--

LOCK TABLES `comment_reactions` WRITE;
/*!40000 ALTER TABLE `comment_reactions` DISABLE KEYS */;
INSERT INTO `comment_reactions` VALUES (1,30,5,'like','2026-04-17 03:27:49','2026-04-17 03:38:49'),(5,30,6,'like','2026-04-17 03:42:07','2026-04-17 03:47:43'),(6,31,6,'love','2026-04-17 03:47:46','2026-04-17 03:47:46'),(7,31,5,'haha','2026-04-17 03:47:56','2026-04-17 03:47:56'),(9,33,5,'haha','2026-04-17 04:11:39','2026-04-17 04:32:58'),(10,35,5,'like','2026-04-17 04:32:56','2026-04-17 04:32:56'),(11,36,6,'haha','2026-04-17 04:33:49','2026-04-17 04:33:49'),(12,36,5,'haha','2026-04-17 04:33:59','2026-04-17 04:33:59'),(13,38,5,'wow','2026-04-17 04:41:12','2026-04-17 04:41:12'),(14,39,5,'sad','2026-04-17 04:46:08','2026-04-17 05:03:01');
/*!40000 ALTER TABLE `comment_reactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback_categories`
--

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

--
-- Dumping data for table `feedback_categories`
--

LOCK TABLES `feedback_categories` WRITE;
/*!40000 ALTER TABLE `feedback_categories` DISABLE KEYS */;
INSERT INTO `feedback_categories` VALUES (1,'Facility',NULL,1,'2026-03-18 16:42:41','2026-03-18 16:42:41'),(2,'Teacher',NULL,1,'2026-03-18 16:42:41','2026-03-18 16:42:41'),(3,'Service',NULL,1,'2026-03-18 16:42:41','2026-03-18 16:42:41'),(4,'Event',NULL,1,'2026-03-18 16:42:41','2026-03-18 16:42:41'),(5,'Academic',NULL,1,'2026-03-18 16:42:41','2026-03-18 16:42:41'),(6,'Security',NULL,1,'2026-03-18 16:42:41','2026-03-18 16:42:41'),(7,'Other',NULL,1,'2026-03-18 16:42:41','2026-03-18 16:42:41');
/*!40000 ALTER TABLE `feedback_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback_replies`
--

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

--
-- Dumping data for table `feedback_replies`
--

LOCK TABLES `feedback_replies` WRITE;
/*!40000 ALTER TABLE `feedback_replies` DISABLE KEYS */;
/*!40000 ALTER TABLE `feedback_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedbacks`
--

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
  `image_path` varchar(500) DEFAULT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'new',
  `submitted_at` datetime DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feedbacks_category_id_foreign` (`category_id`),
  KEY `user_id_category_id` (`user_id`,`category_id`),
  KEY `status_type` (`status`,`type`),
  KEY `idx_feedbacks_created` (`created_at`),
  CONSTRAINT `feedbacks_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `feedback_categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `feedbacks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedbacks`
--

LOCK TABLES `feedbacks` WRITE;
/*!40000 ALTER TABLE `feedbacks` DISABLE KEYS */;
INSERT INTO `feedbacks` VALUES (1,5,7,'suggestion','DDDDD','Gwapo si jason',NULL,1,'new','2026-03-28 11:38:13',NULL,NULL,NULL,NULL,NULL,'2026-03-28 11:38:13','2026-03-28 11:47:27','2026-03-28 11:47:27'),(2,5,5,'complaint','DDDDD','asdawdawwwwwwwww',NULL,0,'new','2026-03-28 11:49:42',NULL,NULL,NULL,NULL,NULL,'2026-03-28 11:49:42','2026-03-28 11:55:28','2026-03-28 11:55:28'),(3,5,5,'suggestion','wwwwwwww','asdddddddddddddddddddddddd',NULL,0,'new','2026-03-28 11:55:36',NULL,NULL,NULL,NULL,NULL,'2026-03-28 11:55:36','2026-03-28 16:18:52','2026-03-28 16:18:52'),(4,5,4,'complaint','wwwwwwwwaw','dwwwwwwwwwwwwwwwww',NULL,0,'new','2026-03-28 16:23:47',NULL,NULL,NULL,NULL,NULL,'2026-03-28 16:23:47','2026-03-28 16:29:28','2026-03-28 16:29:28'),(5,5,4,'praise','wwwwwwwwa','awwwwwwwwwwwwwwwwwwwwww',NULL,0,'new','2026-03-28 16:29:39',NULL,NULL,NULL,NULL,NULL,'2026-03-28 16:29:39','2026-03-28 16:53:18','2026-03-28 16:53:18'),(6,5,5,'suggestion','dasdddd','ddddddddqweeeeeeeeeee',NULL,1,'new','2026-03-28 16:41:36',NULL,NULL,NULL,NULL,NULL,'2026-03-28 16:41:36','2026-03-28 16:53:15','2026-03-28 16:53:15'),(7,5,3,'suggestion','wwwwwwwwaw','GUFJVHBKJNLKJHKGJFHCVBNM',NULL,1,'new','2026-03-28 17:08:04',NULL,NULL,NULL,NULL,NULL,'2026-03-28 17:08:04','2026-03-30 05:12:51','2026-03-30 05:12:51'),(8,5,4,'suggestion','wwwwwwwwa','dawwwwwawwwwwwwww',NULL,0,'new','2026-03-30 05:13:08',NULL,NULL,NULL,NULL,NULL,'2026-03-30 05:13:08','2026-03-30 06:26:21','2026-03-30 06:26:21'),(9,6,4,'praise','wwwwwwwwaw','2111111111111111111111',NULL,0,'new','2026-03-30 06:24:56',NULL,NULL,NULL,NULL,NULL,'2026-03-30 06:24:56','2026-03-30 06:24:56',NULL),(10,5,5,'complaint','wwwwwwwwa','2333333333333333',NULL,0,'new','2026-03-30 06:33:04',NULL,NULL,NULL,NULL,NULL,'2026-03-30 06:33:04','2026-03-30 06:35:27','2026-03-30 06:35:27'),(11,5,1,'complaint','assssssssssss','asssssssssssssssss',NULL,0,'new','2026-03-30 06:35:40',NULL,NULL,NULL,NULL,NULL,'2026-03-30 06:35:40','2026-03-30 06:40:01','2026-03-30 06:40:01'),(12,5,4,'praise','wwwwwwwwaw','2111111111111111111111',NULL,0,'new','2026-03-30 06:40:13',NULL,NULL,NULL,NULL,NULL,'2026-03-30 06:40:13','2026-03-30 06:43:10','2026-03-30 06:43:10'),(13,5,4,'complaint','DDDDDas','asssssssssssssssssssssss',NULL,0,'new','2026-03-30 06:43:22',NULL,NULL,NULL,NULL,NULL,'2026-03-30 06:43:22','2026-03-30 06:47:47','2026-03-30 06:47:47'),(14,5,4,'suggestion','asdasd','asdasdasdasdasdasdasdasdasd',NULL,0,'new','2026-04-17 02:28:36',NULL,NULL,NULL,NULL,NULL,'2026-04-17 02:28:36','2026-04-17 02:28:46','2026-04-17 02:28:46'),(15,5,5,'complaint','asdasd','asdasdasdasdasdasdasdasdasd',NULL,0,'new','2026-04-17 02:42:02',NULL,NULL,NULL,NULL,NULL,'2026-04-17 02:42:02','2026-04-17 02:51:03','2026-04-17 02:51:03'),(16,5,5,'praise','Praise: nICE JOB PLA KAY OKOTYY','nICE JOB PLA KAY OKOTYY',NULL,1,'new','2026-04-17 02:51:35',NULL,NULL,NULL,NULL,NULL,'2026-04-17 02:51:35','2026-04-17 02:52:58','2026-04-17 02:52:58'),(17,5,4,'complaint','Complaint: ASDDDDDDDDDDDDDDDDDDDDDDDDD','ASDDDDDDDDDDDDDDDDDDDDDDDDD',NULL,0,'new','2026-04-17 02:52:52',NULL,NULL,NULL,NULL,NULL,'2026-04-17 02:52:52','2026-04-17 02:52:55','2026-04-17 02:52:55'),(18,5,5,'complaint','Complaint: ASDDDDDDDDDDDD','ASDDDDDDDDDDDD',NULL,0,'new','2026-04-17 02:58:17',NULL,NULL,NULL,NULL,NULL,'2026-04-17 02:58:17','2026-04-17 02:58:30','2026-04-17 02:58:30'),(19,5,4,'complaint','Complaint: ASDDDDDDDDDDDDDDDD','ASDDDDDDDDDDDDDDDD',NULL,0,'new','2026-04-17 02:58:37',NULL,NULL,NULL,NULL,NULL,'2026-04-17 02:58:37','2026-04-17 02:58:42','2026-04-17 02:58:42'),(20,5,4,'suggestion','Suggestion: ASDDDDDDDDDDDDDDDDDDDDDDDDDD','ASDDDDDDDDDDDDDDDDDDDDDDDDDD',NULL,0,'new','2026-04-17 03:00:57',NULL,NULL,NULL,NULL,NULL,'2026-04-17 03:00:57','2026-04-17 03:04:31','2026-04-17 03:04:31'),(21,5,4,'suggestion','Suggestion: ASDDDDDDDDDDDDDDDDDDDDDDDDDDDD','ASDDDDDDDDDDDDDDDDDDDDDDDDDDDD',NULL,0,'new','2026-04-17 03:04:38',NULL,NULL,NULL,NULL,NULL,'2026-04-17 03:04:38','2026-04-17 04:33:03','2026-04-17 04:33:03'),(22,5,5,'suggestion','Suggestion: ASDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD','ASDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD',NULL,0,'new','2026-04-17 04:33:25',NULL,NULL,NULL,NULL,NULL,'2026-04-17 04:33:25','2026-04-17 04:34:06','2026-04-17 04:34:06'),(23,5,4,'suggestion','Suggestion: ASDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD','ASDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD',NULL,0,'new','2026-04-17 04:34:12',NULL,NULL,NULL,NULL,NULL,'2026-04-17 04:34:12','2026-04-17 04:40:30','2026-04-17 04:40:30'),(24,5,4,'suggestion','Suggestion: ASDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD','ASDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD',NULL,0,'new','2026-04-17 04:40:38',NULL,NULL,NULL,NULL,NULL,'2026-04-17 04:40:38','2026-04-17 04:45:55','2026-04-17 04:45:55'),(25,5,5,'complaint','Complaint: adssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss','adssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss',NULL,0,'new','2026-04-17 04:46:03',NULL,NULL,NULL,NULL,NULL,'2026-04-17 04:46:03','2026-04-17 05:03:37','2026-04-17 05:03:37');
/*!40000 ALTER TABLE `feedbacks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026-03-19-000001','App\\Database\\Migrations\\CreateRolesTable','default','App',1773852161,1),(2,'2026-03-19-000002','App\\Database\\Migrations\\CreateUsersTable','default','App',1773852161,1),(3,'2026-03-19-000003','App\\Database\\Migrations\\CreateFeedbackCategoriesTable','default','App',1773852161,1),(4,'2026-03-19-000004','App\\Database\\Migrations\\CreateFeedbacksTable','default','App',1773852161,1),(5,'2026-03-19-000005','App\\Database\\Migrations\\CreateFeedbackRepliesTable','default','App',1773852161,1),(6,'2026-03-19-000006','App\\Database\\Migrations\\CreateAnnouncementsTable','default','App',1773852161,1),(7,'2026-03-19-000007','App\\Database\\Migrations\\CreateApiTokensTable','default','App',1773852161,1),(8,'2026-03-19-000008','App\\Database\\Migrations\\CreatePasswordOtpsTable','default','App',1773852161,1),(9,'2026-03-19-000009','App\\Database\\Migrations\\CreateAdminActivityLogsTable','default','App',1773854566,2),(10,'2026-03-19-000010','App\\Database\\Migrations\\CreateSocialProfilesTable','default','App',1773910757,3),(11,'2026-03-19-000011','App\\Database\\Migrations\\CreateSocialPostsTable','default','App',1773910757,3),(12,'2026-03-19-000012','App\\Database\\Migrations\\CreateSocialPostReactionsTable','default','App',1773910757,3),(13,'2026-03-19-000013','App\\Database\\Migrations\\CreateSocialPostCommentsTable','default','App',1773910757,3),(14,'2026-03-19-000014','App\\Database\\Migrations\\CreateSocialPostSharesTable','default','App',1773910757,3),(15,'2026-03-20-000015','App\\Database\\Migrations\\CreateAdminCredentialsTable','default','App',1774339301,4),(16,'2026-03-27-000015','App\\Database\\Migrations\\AddAnonymousToSocialTables','default','App',1774673404,5),(17,'2026-04-17-000001','App\\Database\\Migrations\\CreateCommentReactionsTable','default','App',1776396185,6),(18,'2026-04-17-000002','App\\Database\\Migrations\\DropLegacyTables','default','App',1776402920,7),(19,'2026-04-17-000003','App\\Database\\Migrations\\AddFeedbackIdToSocialPosts','default','App',1776402920,7),(20,'2026-04-17-000004','App\\Database\\Migrations\\AddPerformanceIndexes','default','App',1776402920,7),(21,'2026-04-17-000005','App\\Database\\Migrations\\UnifyReactionTypes','default','App',1776402920,7),(22,'2026-04-23-000001','App\\Database\\Migrations\\AddImagePathToFeedbacks','default','App',1714500000,8),(23,'2026-04-17-100000','App\\Database\\Migrations\\AddParentIdToSocialPostComments','default','App',1777358577,9),(24,'2026-04-24-100000','App\\Database\\Migrations\\AddImagePathToSocialPostComments','default','App',1777358577,9),(25,'2026-04-27-000001','App\\Database\\Migrations\\AddImagePathToAnnouncements','default','App',1777358577,9),(26,'2026-04-28-000001','App\\Database\\Migrations\\AddPinnedToAnnouncements','default','App',1777358577,9),(27,'2026-04-28-000002','App\\Database\\Migrations\\AddMissingFeedbackColumns','default','App',1777358577,9);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_otps`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_otps`
--

LOCK TABLES `password_otps` WRITE;
/*!40000 ALTER TABLE `password_otps` DISABLE KEYS */;
INSERT INTO `password_otps` VALUES (7,NULL,'yasuhamtristan1@gmail.com','register','$2y$10$6jZAy.xkI8xlm2px2o6qn.B6FB3laFcCn/23oA.b547mjTJajJNZq',0,5,'2026-03-24 08:54:01',NULL,'2026-03-24 08:44:01','2026-03-24 08:44:01'),(8,NULL,'lilflickone12@gmail.com','register','$2y$10$n9Q1MWfavGBBzVINeMQg8.UDEz.G4HPRcTJnbPLkS2CoeZ17WXCYy',0,5,'2026-03-24 08:54:53','2026-03-24 08:45:23','2026-03-24 08:44:53','2026-03-24 08:45:23'),(9,NULL,'lilflickone@g2mail.com','register','$2y$10$nlj/60mIwO/KuWSkye3/peKv/F.4cZKF3iyyVJ0eI/evIsWALOzKC',0,5,'2026-03-24 09:19:36',NULL,'2026-03-24 09:09:36','2026-03-24 09:09:36'),(10,NULL,'lilflickon^gyygyg#ygyg@gmail.com','register','$2y$10$nqfN8GXAIvJfTPfR7Mdz6OoEmdM.VposwBmKgLkK2MJ7l2iGpM2SK',0,5,'2026-03-24 09:20:59',NULL,'2026-03-24 09:10:59','2026-03-24 09:10:59'),(11,NULL,'123222323@gmail.com333322','register','$2y$10$58iNLxZ0jLRhrANAkSYLf.vgirRz1a4lHfg./fMSk/j7F8ATznsPG',0,5,'2026-03-24 09:23:44',NULL,'2026-03-24 09:13:45','2026-03-24 09:13:45'),(12,NULL,'gmail@gmail.com','register','$2y$10$0K4cPvmBTkgqIsGs7Qe9KeTt1tUHf1LXUAd66NCjucm3v3TKm0CNi',0,5,'2026-03-24 09:27:49',NULL,'2026-03-24 09:17:49','2026-03-24 09:17:49'),(13,5,'lilflickone12@gmail.com','reset','$2y$10$6fWeB.X4NE.74Bi3nF07tupwl7RzFF3y3B.EJkCualRChgBZzk2O6',1,5,'2026-03-24 10:00:16',NULL,'2026-03-24 09:50:16','2026-03-24 09:51:05'),(14,5,'lilflickone12@gmail.com','reset','$2y$10$pUz6hBQARKBWJRlTlxvDReUNe2LI/GEvY3lL157yztYsQpI/H6FBu',1,5,'2026-03-24 10:05:09','2026-03-24 09:55:40','2026-03-24 09:55:09','2026-03-24 09:55:40'),(16,NULL,'firetest45651@gmail.com','register','$2y$10$NGO62t6bgDBF4PHqptzRFOPYb8bEcTB28ryiC.nWa1qVeXfzjMdle',1,5,'2026-03-30 05:46:35',NULL,'2026-03-30 05:36:35','2026-03-30 05:37:19'),(17,NULL,'firetest45651@gmail.com','register','$2y$10$WrzCVl7QY3H3wpXDGqblMe2ROPTWtj2OAwo5Yupmft8Yhwijqjhbu',1,5,'2026-03-30 05:48:23',NULL,'2026-03-30 05:38:23','2026-03-30 05:38:49'),(18,NULL,'firetest45651@gmail.com','register','$2y$10$/af3Ununy3xNQbANZvCVUeC4fgKgXWkULvbA5juTCJUyhyZywyY3C',1,5,'2026-03-30 05:58:35',NULL,'2026-03-30 05:48:35','2026-03-30 05:48:56'),(19,NULL,'firetest45651@gmail.com','register','$2y$10$2R7gGH2XaVUNh6AAdn2gTe60l1sVct6Rk4hzHsIZ3Vqzh1fSu1oLO',0,5,'2026-03-30 06:02:11',NULL,'2026-03-30 05:52:11','2026-03-30 05:52:11'),(20,NULL,'firetest45651@gmail.com','register','$2y$10$M0v6Jbb0XFpItu90hByOpeHe6OwTMoPBOz2FjTPvqKdGTerZY1Hay',0,5,'2026-03-30 06:04:50',NULL,'2026-03-30 05:54:50','2026-03-30 05:54:50'),(21,NULL,'asdadasdasd@gmail.com','register','$2y$10$I3e6OIBTQ.F6AlfjUzQnxetIi.zBCEl0IQbKanuejpgGk/jSiCQdi',0,5,'2026-03-30 06:05:49',NULL,'2026-03-30 05:55:49','2026-03-30 05:55:49'),(22,NULL,'asdasdas@gmail.com','register','$2y$10$/zH9hN5wSR8rkdoM2c1gZO1czX8rakUmxngDOA0olfP1/b3Mm75Ve',1,5,'2026-03-30 06:10:30',NULL,'2026-03-30 06:00:30','2026-03-30 06:00:39'),(23,NULL,'dddddddddddd@gmail.com','register','$2y$10$haf9YT.8nGDLYUgK64UmfuSf5PsoNjTUVsv6xrxkFqsHiDXEPUiTG',0,5,'2026-03-30 06:13:38',NULL,'2026-03-30 06:03:38','2026-03-30 06:03:38'),(24,NULL,'firetest45651@gmail.com','register','$2y$10$Hgr3823czSQu7ORKLFYm3.gtY8Rixbh0KYyOurtn7.7tfZlRlLobS',0,5,'2026-03-30 06:18:45','2026-03-30 06:09:08','2026-03-30 06:08:45','2026-03-30 06:09:08'),(25,5,'lilflickone12@gmail.com','reset','$2y$10$.puzi6Qj8DRdsuV5MHBTfeGUJZslIdXvdO5abjUC7Aii935OHs1Vu',0,5,'2026-03-30 06:35:24','2026-03-30 06:25:48','2026-03-30 06:25:24','2026-03-30 06:25:48'),(26,6,'firetest45651@gmail.com','reset','$2y$10$mPZEr6gsbzj27jjKKSJFr.UBEE1sUqVUTW76Gc5fAv8PytTo9znvG',0,5,'2026-04-17 03:38:47','2026-04-17 03:29:13','2026-04-17 03:28:47','2026-04-17 03:29:13'),(27,5,'lilflickone12@gmail.com','password_change','$2y$10$G7fSI0FZGNjf7E.bP/gx8O6F9QajCLL7Tuue9/4n1SLNuVgK0zvIa',0,5,'2026-04-17 05:07:55',NULL,'2026-04-17 04:57:55','2026-04-17 04:57:55'),(28,5,'lilflickone12@gmail.com','password_change','$2y$10$jmryMa6a3Qs.hSGVyGTiMe.FpO/ayRSuT3i3i00i1G7RWeT8HCd/K',0,5,'2026-04-17 05:10:35','2026-04-17 05:01:07','2026-04-17 05:00:35','2026-04-17 05:01:07');
/*!40000 ALTER TABLE `password_otps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

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

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'system_admin','Full access to all modules','2026-03-18 16:42:41','2026-03-18 16:42:41'),(2,'admin','Manages feedback and content','2026-03-18 16:42:41','2026-03-18 16:42:41'),(3,'student','Submits and tracks feedback','2026-03-18 16:42:41','2026-03-18 16:42:41');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_post_comments`
--

DROP TABLE IF EXISTS `social_post_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_post_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `body` text NOT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_post_comments_listing` (`post_id`,`deleted_at`,`created_at`),
  KEY `idx_parent_id` (`parent_id`),
  CONSTRAINT `fk_comment_parent` FOREIGN KEY (`parent_id`) REFERENCES `social_post_comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `social_post_comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `social_posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `social_post_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_post_comments`
--

LOCK TABLES `social_post_comments` WRITE;
/*!40000 ALTER TABLE `social_post_comments` DISABLE KEYS */;
INSERT INTO `social_post_comments` VALUES (26,20,NULL,5,'ASDASDASDASDASDAS',NULL,1,'2026-04-17 03:01:05','2026-04-17 03:01:05',NULL),(27,20,NULL,5,'ASDASDASDASDASDAS',NULL,1,'2026-04-17 03:03:01','2026-04-17 03:03:01',NULL),(28,20,NULL,5,'ASDASDASD',NULL,1,'2026-04-17 03:04:24','2026-04-17 03:04:24',NULL),(29,20,NULL,5,'ASDASDASDASD',NULL,0,'2026-04-17 03:04:26','2026-04-17 03:04:26',NULL),(30,21,NULL,5,'ASDASDASDASDASDASDASDASDASD',NULL,1,'2026-04-17 03:04:44','2026-04-17 03:04:44',NULL),(31,21,NULL,5,'ASDASDAS',NULL,1,'2026-04-17 03:05:44','2026-04-17 03:05:44',NULL),(32,21,NULL,5,'ASDASDASD',NULL,1,'2026-04-17 03:05:46','2026-04-17 03:05:46',NULL),(33,21,NULL,5,'ASDASD',NULL,0,'2026-04-17 03:05:48','2026-04-17 03:05:48',NULL),(34,21,NULL,5,'ASDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD',NULL,0,'2026-04-17 03:09:27','2026-04-17 03:09:27',NULL),(35,21,NULL,5,'ASDDDDDDDDDDDDDDDDDDDASDDDDDDDDDDDDDDDDDDDASDDDDDDDDDDDDDDDDDDDASDDDDDDDDDDDDDDDDDDDASDDDDDDDDDDDDDDDDDDDASDDDDDDDDDDDDDDDDDDDASDDDDDDDDDDDDDDDDDDDASDDDDDDDDDDDDDDDDDDDASDDDDDDDDDDDDDDDDDDDASDDDDDDDDDDDDDDDDDDD',NULL,0,'2026-04-17 03:11:16','2026-04-17 03:11:16',NULL),(36,22,NULL,5,'ASDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD',NULL,1,'2026-04-17 04:33:33','2026-04-17 04:33:33',NULL),(37,23,NULL,5,'ASDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD',NULL,1,'2026-04-17 04:34:18','2026-04-17 04:34:18',NULL),(38,24,NULL,5,'ADSSSSSSSSSSSSSSSSSSSSSS',NULL,1,'2026-04-17 04:40:42','2026-04-17 04:40:42',NULL),(39,25,NULL,5,'asdddddddddd',NULL,1,'2026-04-17 04:46:07','2026-04-17 04:46:07',NULL);
/*!40000 ALTER TABLE `social_post_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_post_reactions`
--

DROP TABLE IF EXISTS `social_post_reactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_post_reactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `reaction_type` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_id_user_id` (`post_id`,`user_id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `social_post_reactions_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `social_posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `social_post_reactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_post_reactions`
--

LOCK TABLES `social_post_reactions` WRITE;
/*!40000 ALTER TABLE `social_post_reactions` DISABLE KEYS */;
INSERT INTO `social_post_reactions` VALUES (35,20,5,'wow','2026-04-17 03:01:01','2026-04-17 03:01:01'),(36,21,6,'sad','2026-04-17 03:29:27','2026-04-17 03:38:56'),(37,21,5,'sad','2026-04-17 03:38:44','2026-04-17 03:38:47'),(38,22,5,'love','2026-04-17 04:33:36','2026-04-17 04:33:36'),(39,22,6,'love','2026-04-17 04:33:47','2026-04-17 04:33:47'),(40,25,5,'love','2026-04-17 05:03:11','2026-04-17 05:03:11');
/*!40000 ALTER TABLE `social_post_reactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_post_shares`
--

DROP TABLE IF EXISTS `social_post_shares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_post_shares` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_id_user_id` (`post_id`,`user_id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `social_post_shares_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `social_posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `social_post_shares_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_post_shares`
--

LOCK TABLES `social_post_shares` WRITE;
/*!40000 ALTER TABLE `social_post_shares` DISABLE KEYS */;
/*!40000 ALTER TABLE `social_post_shares` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_posts`
--

DROP TABLE IF EXISTS `social_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `feedback_id` bigint(20) unsigned DEFAULT NULL,
  `body` text NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_feedback_id` (`feedback_id`),
  KEY `idx_feed` (`is_public`,`deleted_at`,`created_at`),
  CONSTRAINT `social_posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_posts`
--

LOCK TABLES `social_posts` WRITE;
/*!40000 ALTER TABLE `social_posts` DISABLE KEYS */;
INSERT INTO `social_posts` VALUES (16,5,NULL,'Praise\n\nnICE JOB PLA KAY OKOTYY',1,1,'2026-04-17 02:51:35','2026-04-17 02:58:27','2026-04-17 02:58:27'),(17,5,NULL,'Complaint\n\nASDDDDDDDDDDDDDDDDDDDDDDDDD',1,0,'2026-04-17 02:52:52','2026-04-17 02:58:25','2026-04-17 02:58:25'),(18,5,NULL,'Complaint\n\nASDDDDDDDDDDDD',1,0,'2026-04-17 02:58:17','2026-04-17 02:58:23','2026-04-17 02:58:23'),(19,5,NULL,'Complaint\n\nASDDDDDDDDDDDDDDDD',1,0,'2026-04-17 02:58:37','2026-04-17 02:58:42','2026-04-17 02:58:42'),(20,5,NULL,'Suggestion\n\nASDDDDDDDDDDDDDDDDDDDDDDDDDD',1,0,'2026-04-17 03:00:57','2026-04-17 03:04:31','2026-04-17 03:04:31'),(21,5,NULL,'Suggestion\n\nASDDDDDDDDDDDDDDDDDDDDDDDDDDDD',1,0,'2026-04-17 03:04:38','2026-04-17 04:33:03','2026-04-17 04:33:03'),(22,5,NULL,'Suggestion\n\nASDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD',1,0,'2026-04-17 04:33:25','2026-04-17 04:34:06','2026-04-17 04:34:06'),(23,5,NULL,'Suggestion\n\nASDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD',1,0,'2026-04-17 04:34:12','2026-04-17 04:40:30','2026-04-17 04:40:30'),(24,5,NULL,'Suggestion\n\nASDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD',1,0,'2026-04-17 04:40:38','2026-04-17 04:45:55','2026-04-17 04:45:55'),(25,5,NULL,'Complaint\n\nadssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss',1,0,'2026-04-17 04:46:03','2026-04-17 05:03:37','2026-04-17 05:03:37');
/*!40000 ALTER TABLE `social_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_profiles`
--

DROP TABLE IF EXISTS `social_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `bio` text DEFAULT NULL,
  `avatar_color` varchar(30) NOT NULL DEFAULT 'blue',
  `is_anonymous` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `social_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_profiles`
--

LOCK TABLES `social_profiles` WRITE;
/*!40000 ALTER TABLE `social_profiles` DISABLE KEYS */;
INSERT INTO `social_profiles` VALUES (1,1,'Keeping the campus conversation organized and visible.','violet',0,'2026-03-19 09:01:08','2026-03-19 09:01:08'),(2,2,'Tracking updates and making sure community issues are seen.','amber',0,'2026-03-19 09:01:08','2026-03-19 09:01:08'),(3,3,'Sharing ideas, reporting issues, and following campus updates.','blue',0,'2026-03-19 09:01:08','2026-03-19 09:01:08'),(4,4,'CampusVoice community member.','blue',0,'2026-03-19 09:01:08','2026-03-19 09:01:08'),(5,5,NULL,'blue',0,'2026-03-27 08:04:34','2026-04-17 05:03:28'),(6,6,NULL,'blue',0,'2026-03-30 06:24:23','2026-04-17 03:29:35');
/*!40000 ALTER TABLE `social_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

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
  KEY `idx_active` (`is_active`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,NULL,'System','Admin','sysadmin@campusvoice.local','$2y$10$sqOcaC9FCzu62rl6uXciouIeQEQdVvXjQDkG8QtvHV.4OBYHqhLim',NULL,1,'2026-03-19 06:14:39','2026-03-18 16:42:41','2026-03-19 06:14:39',NULL),(2,2,NULL,'School','Admin','admin@campusvoice.local','$2y$10$uUf8o15s2ybbp4IW0KovfO.LxZCjVavL4jHfhIrQ4.5EPeYc2PJSW',NULL,1,'2026-03-18 20:05:42','2026-03-18 16:42:41','2026-03-18 20:05:42',NULL),(3,3,'DEMO-0001','Demo','Student','student@campusvoice.local','$2y$10$1/clDWNS65Lvp5A1WO9Lhuw.Wm5cBylEP4pNI/xrnoYwHnQEGHM7u',NULL,1,'2026-03-19 11:43:22','2026-03-18 16:42:41','2026-03-19 11:43:22',NULL),(4,3,'TMP-1001','Tya','Suham','tyasuham@gmail.com','$2y$10$.WAhIt0TLqZY5TRj0gFf4eRC6djbpKsE8zhjwgAWjMvrrWwaNzY3a',NULL,1,NULL,'2026-03-18 16:43:37','2026-03-18 19:58:24',NULL),(5,3,NULL,'Jason','Dungog','lilflickone12@gmail.com','$2y$10$tzluuVZsVSNo3wvhmN7KPuP05HY7bPAIOm0dPPY5G3QQ28jHhaF3G',NULL,1,'2026-04-17 05:01:17','2026-03-24 08:45:23','2026-04-17 05:03:28',NULL),(6,3,NULL,'Kaloy','DDDD','firetest45651@gmail.com','$2y$10$MMITzxeGJQ.92.m2U2tw1OEPCrFdZ9qyKH9zlBVqKiObA0L8tp66q',NULL,1,'2026-04-17 04:33:42','2026-03-30 06:09:08','2026-04-17 04:33:42',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-28 14:44:50
