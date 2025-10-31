-- MySQL dump 10.16  Distrib 10.1.48-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: db
-- ------------------------------------------------------
-- Server version	10.1.48-MariaDB-0+deb9u2

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
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attachments` (
  `id` varchar(0) DEFAULT NULL,
  `grievance_id` varchar(0) DEFAULT NULL,
  `file_name` varchar(0) DEFAULT NULL,
  `file_path` varchar(0) DEFAULT NULL,
  `file_type` varchar(0) DEFAULT NULL,
  `file_size` varchar(0) DEFAULT NULL,
  `created_at` varchar(0) DEFAULT NULL,
  `updated_at` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachments`
--

LOCK TABLES `attachments` WRITE;
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` tinyint(4) DEFAULT NULL,
  `user_id` tinyint(4) DEFAULT NULL,
  `action` varchar(7) DEFAULT NULL,
  `message` varchar(37) DEFAULT NULL,
  `auditable_type` varchar(20) DEFAULT NULL,
  `auditable_id` tinyint(4) DEFAULT NULL,
  `old_values` text,
  `new_values` varchar(54) DEFAULT NULL,
  `created_at` varchar(0) DEFAULT NULL,
  `updated_at` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'deleted','Grievance #B645QKJH has been deleted.','App\\Models\\Grievance',3,'{\"id\":3,\"reference_id\":\"B645QKJH\",\"user_id\":6,\"title\":\"Need Counseling Support\",\"description\":\"I would like to schedule regular counseling sessions to discuss some personal challenges.\",\"priority\":\"high\",\"status\":\"closed\",\"is_anonymous\":0,\"chat_disabled\":0,\"assigned_to\":5,\"resolved_at\":null,\"created_at\":\"2025-07-26T09:05:38.000000Z\",\"updated_at\":\"2025-08-02T12:48:56.000000Z\",\"deleted_at\":\"2025-08-02T12:48:56.000000Z\"}','','',''),(2,1,'updated','User was updated.','App\\Models\\User',1,'{\"id\":1,\"student_id\":null,\"employee_id\":null,\"name\":\"Test Admin\",\"email\":\"test@test.com\",\"email_verified_at\":\"2025-08-02T09:05:31.000000Z\",\"password\":\"[REDACTED]\",\"role\":\"admin\",\"remember_token\":\"[REDACTED]\",\"created_at\":\"2025-08-02T09:05:31.000000Z\",\"updated_at\":\"2025-08-02T09:05:31.000000Z\"}','{\"remember_token\":\"[REDACTED]\"}','',''),(3,1,'updated','Grievance #V9PFH1ID has been updated.','App\\Models\\Grievance',6,'{\"id\":6,\"reference_id\":\"V9PFH1ID\",\"user_id\":10,\"title\":\"Request for Assignment Extension\",\"description\":\"Due to medical reasons, I need an extension for my upcoming assignments.\",\"priority\":\"medium\",\"status\":\"pending\",\"is_anonymous\":0,\"chat_disabled\":0,\"assigned_to\":null,\"resolved_at\":null,\"created_at\":\"2025-07-29T09:05:38.000000Z\",\"updated_at\":\"2025-08-02T09:05:38.000000Z\",\"deleted_at\":null}','{\"assigned_to\":\"2\",\"updated_at\":\"2025-08-02 20:50:42\"}','',''),(4,2,'updated','User was updated.','App\\Models\\User',2,'{\"id\":2,\"student_id\":null,\"employee_id\":null,\"name\":\"Test Guidance\",\"email\":\"guidance@test.com\",\"email_verified_at\":\"2025-08-02T09:05:32.000000Z\",\"password\":\"[REDACTED]\",\"role\":\"guidance\",\"remember_token\":\"[REDACTED]\",\"created_at\":\"2025-08-02T09:05:32.000000Z\",\"updated_at\":\"2025-08-02T09:05:32.000000Z\"}','{\"remember_token\":\"[REDACTED]\"}','',''),(5,6,'updated','User was updated.','App\\Models\\User',6,'{\"id\":6,\"student_id\":\"STU000001\",\"employee_id\":null,\"name\":\"Student 1\",\"email\":\"student1@example.com\",\"email_verified_at\":\"2025-08-02T09:05:35.000000Z\",\"password\":\"[REDACTED]\",\"role\":\"student\",\"remember_token\":\"[REDACTED]\",\"created_at\":\"2025-08-02T09:05:35.000000Z\",\"updated_at\":\"2025-08-02T09:05:35.000000Z\"}','{\"remember_token\":\"[REDACTED]\"}','',''),(6,1,'updated','User was updated.','App\\Models\\User',1,'{\"id\":1,\"student_id\":null,\"employee_id\":null,\"name\":\"Test Admin\",\"email\":\"test@test.com\",\"email_verified_at\":\"2025-08-02T09:05:31.000000Z\",\"password\":\"[REDACTED]\",\"role\":\"admin\",\"remember_token\":\"[REDACTED]\",\"created_at\":\"2025-08-02T09:05:31.000000Z\",\"updated_at\":\"2025-08-02T09:05:31.000000Z\"}','{\"remember_token\":\"[REDACTED]\"}','','');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(0) DEFAULT NULL,
  `value` varchar(0) DEFAULT NULL,
  `expiration` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(0) DEFAULT NULL,
  `owner` varchar(0) DEFAULT NULL,
  `expiration` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chats`
--

DROP TABLE IF EXISTS `chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chats` (
  `id` varchar(0) DEFAULT NULL,
  `sender_id` varchar(0) DEFAULT NULL,
  `receiver_id` varchar(0) DEFAULT NULL,
  `grievance_id` varchar(0) DEFAULT NULL,
  `message` varchar(0) DEFAULT NULL,
  `is_read` varchar(0) DEFAULT NULL,
  `created_at` varchar(0) DEFAULT NULL,
  `updated_at` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chats`
--

LOCK TABLES `chats` WRITE;
/*!40000 ALTER TABLE `chats` DISABLE KEYS */;
/*!40000 ALTER TABLE `chats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` tinyint(4) DEFAULT NULL,
  `user_id` tinyint(4) DEFAULT NULL,
  `title` varchar(10) DEFAULT NULL,
  `description` varchar(20) DEFAULT NULL,
  `start` varchar(0) DEFAULT NULL,
  `end` varchar(0) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `allDay` tinyint(4) DEFAULT NULL,
  `status` varchar(9) DEFAULT NULL,
  `created_at` varchar(0) DEFAULT NULL,
  `updated_at` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,2,'Test Event','This is a test event','','','#e90c0c',0,'scheduled','','');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` varchar(0) DEFAULT NULL,
  `uuid` varchar(0) DEFAULT NULL,
  `connection` varchar(0) DEFAULT NULL,
  `queue` varchar(0) DEFAULT NULL,
  `payload` varchar(0) DEFAULT NULL,
  `exception` varchar(0) DEFAULT NULL,
  `failed_at` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grievance_tag`
--

DROP TABLE IF EXISTS `grievance_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grievance_tag` (
  `id` tinyint(4) DEFAULT NULL,
  `grievance_id` tinyint(4) DEFAULT NULL,
  `tag_id` tinyint(4) DEFAULT NULL,
  `created_at` varchar(0) DEFAULT NULL,
  `updated_at` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grievance_tag`
--

LOCK TABLES `grievance_tag` WRITE;
/*!40000 ALTER TABLE `grievance_tag` DISABLE KEYS */;
INSERT INTO `grievance_tag` VALUES (1,1,1,'',''),(2,1,4,'',''),(3,2,1,'',''),(4,2,6,'',''),(5,3,2,'',''),(6,3,7,'',''),(7,4,3,'',''),(8,5,4,'',''),(9,6,1,'',''),(10,6,7,'',''),(11,7,1,'',''),(12,7,5,'','');
/*!40000 ALTER TABLE `grievance_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grievances`
--

DROP TABLE IF EXISTS `grievances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grievances` (
  `id` tinyint(4) DEFAULT NULL,
  `reference_id` varchar(8) DEFAULT NULL,
  `user_id` varchar(2) DEFAULT NULL,
  `title` varchar(37) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `priority` varchar(6) DEFAULT NULL,
  `status` varchar(11) DEFAULT NULL,
  `is_anonymous` tinyint(4) DEFAULT NULL,
  `chat_disabled` tinyint(4) DEFAULT NULL,
  `assigned_to` varchar(1) DEFAULT NULL,
  `resolved_at` varchar(0) DEFAULT NULL,
  `created_at` varchar(0) DEFAULT NULL,
  `updated_at` varchar(0) DEFAULT NULL,
  `deleted_at` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grievances`
--

LOCK TABLES `grievances` WRITE;
/*!40000 ALTER TABLE `grievances` DISABLE KEYS */;
INSERT INTO `grievances` VALUES (1,'PMATJVQ3','7','Difficulty Accessing Online Resources','I have been experiencing issues accessing the online library resources. The system keeps timing out.','medium','resolved',0,0,'5','','','',''),(2,'6KWED0Z7','6','Concerns About Course Schedule','There are several scheduling conflicts in my current course load that need to be addressed.','high','resolved',0,0,'5','','','',''),(3,'B645QKJH','6','Need Counseling Support','I would like to schedule regular counseling sessions to discuss some personal challenges.','high','closed',0,0,'5','','','',''),(4,'AQ2HK4LW','','Report of Cyberbullying','I want to report instances of cyberbullying that I have experienced recently.','high','resolved',1,0,'5','','','',''),(5,'UB4NCMTG','7','Classroom Ventilation Issues','The ventilation in Room 301 is not working properly, making it difficult to concentrate.','medium','closed',0,0,'5','','','',''),(6,'V9PFH1ID','10','Request for Assignment Extension','Due to medical reasons, I need an extension for my upcoming assignments.','medium','pending',0,0,'2','','','',''),(7,'AQTMSIE0','6','Feedback on Teaching Methods','I would like to provide constructive feedback about certain teaching methods.','low','in_progress',0,0,'','','','','');
/*!40000 ALTER TABLE `grievances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(0) DEFAULT NULL,
  `name` varchar(0) DEFAULT NULL,
  `total_jobs` varchar(0) DEFAULT NULL,
  `pending_jobs` varchar(0) DEFAULT NULL,
  `failed_jobs` varchar(0) DEFAULT NULL,
  `failed_job_ids` varchar(0) DEFAULT NULL,
  `options` varchar(0) DEFAULT NULL,
  `cancelled_at` varchar(0) DEFAULT NULL,
  `created_at` varchar(0) DEFAULT NULL,
  `finished_at` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` varchar(0) DEFAULT NULL,
  `queue` varchar(0) DEFAULT NULL,
  `payload` varchar(0) DEFAULT NULL,
  `attempts` varchar(0) DEFAULT NULL,
  `reserved_at` varchar(0) DEFAULT NULL,
  `available_at` varchar(0) DEFAULT NULL,
  `created_at` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` tinyint(4) DEFAULT NULL,
  `migration` varchar(53) DEFAULT NULL,
  `batch` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_07_21_181442_create_grievances_table',1),(5,'2025_07_24_142710_create_tags_table',1),(6,'2025_07_24_155528_create_attachments_table',1),(7,'2025_07_25_151927_create_chats_table',1),(8,'2025_07_25_162406_create_personal_access_tokens_table',1),(9,'2025_07_31_143419_create_audit_logs_table',1),(10,'2025_08_01_174713_create_events_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(0) DEFAULT NULL,
  `token` varchar(0) DEFAULT NULL,
  `created_at` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` varchar(0) DEFAULT NULL,
  `tokenable_type` varchar(0) DEFAULT NULL,
  `tokenable_id` varchar(0) DEFAULT NULL,
  `name` varchar(0) DEFAULT NULL,
  `token` varchar(0) DEFAULT NULL,
  `abilities` varchar(0) DEFAULT NULL,
  `last_used_at` varchar(0) DEFAULT NULL,
  `expires_at` varchar(0) DEFAULT NULL,
  `created_at` varchar(0) DEFAULT NULL,
  `updated_at` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(40) DEFAULT NULL,
  `user_id` varchar(1) DEFAULT NULL,
  `ip_address` varchar(9) DEFAULT NULL,
  `user_agent` varchar(125) DEFAULT NULL,
  `payload` text,
  `last_activity` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('ZdkrqLzNET4F15I0f9B7ThfaoPKLOp7UGNy4Ts0v','1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQkIzU2lwZ1JxUEpRR0MzR2xBQjJDYlZoMXBZZFpkUU5Cd1NzTEFZcSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvY2hhdD91c2VyX2lkPTIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1754139137),('rPJq0Cm5utnxKxaYqWZ8HKN7835sHcCmdWX6l3NF','','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoialpuaG5UNmRXeDRlNjg2ZU82TEpGeFFzTWtsUWJYVWd3YzRLblJDSSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fX0=',1754140412),('qB2G7shxUXquAMA32eTtfK4gIoIREym5h7bOgGsv','','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 Edg/139.0.0.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRE8zQmZJd0VLQWNoNmhrNmRsbXBpa0tWajMzb01Rdmx0MjFLdGJhQSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fX0=',1755252967);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sqlite_sequence`
--

DROP TABLE IF EXISTS `sqlite_sequence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sqlite_sequence` (
  `name` varchar(13) DEFAULT NULL,
  `seq` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sqlite_sequence`
--

LOCK TABLES `sqlite_sequence` WRITE;
/*!40000 ALTER TABLE `sqlite_sequence` DISABLE KEYS */;
INSERT INTO `sqlite_sequence` VALUES ('migrations',10),('users',10),('tags',8),('grievances',7),('grievance_tag',12),('audit_logs',6),('events',1);
/*!40000 ALTER TABLE `sqlite_sequence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `id` tinyint(4) DEFAULT NULL,
  `name` varchar(14) DEFAULT NULL,
  `description` varchar(37) DEFAULT NULL,
  `created_at` varchar(0) DEFAULT NULL,
  `updated_at` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (1,'Academic','Issues related to academic matters','',''),(2,'Mental Health','Mental health and well-being concerns','',''),(3,'Bullying','Reports of bullying or harassment','',''),(4,'Facilities','Issues with school facilities','',''),(5,'Faculty','Concerns about faculty members','',''),(6,'Administrative','Administrative and procedural issues','',''),(7,'Personal','Personal or private matters','',''),(8,'Other','Other miscellaneous concerns','','');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` tinyint(4) DEFAULT NULL,
  `student_id` varchar(9) DEFAULT NULL,
  `employee_id` varchar(0) DEFAULT NULL,
  `name` varchar(18) DEFAULT NULL,
  `email` varchar(20) DEFAULT NULL,
  `email_verified_at` varchar(0) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `role` varchar(8) DEFAULT NULL,
  `remember_token` varchar(60) DEFAULT NULL,
  `created_at` varchar(0) DEFAULT NULL,
  `updated_at` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'','','Test Admin','test@test.com','','$2y$12$Tb7gtJjjoTkZ3uzkpeAr1ev2tpmt6nydH/OZ9iSt/OxNF8LILxnIS','admin','n84DuDGV48OjMaVXCEBZML45I45RCi7STSDvZqcY0wJpbLzaRfj6S6dvn4lk','',''),(2,'','','Test Guidance','guidance@test.com','','$2y$12$iyngKYjXQ2SV72H8KK0DV.yZduKcTHUzLxbdhGjIM/9/OgC45fjQG','guidance','gkA4znnqP777fNXF1BTgePe3SrQmA5dZ40pj1c30qJG7VQHmzl25pGWAh4qV','',''),(3,'STU345678','','Test Student','student@test.com','','$2y$12$.nrL.eyjed.0jFJt5qs/iOES4eEEYFDc8scGRkfwh/UryM8tySk8y','student','09LK17BC7u','',''),(4,'','','Admin User','admin@example.com','','$2y$12$F3bdNxBc63V1POq/3zeoTeO46b6emwK5Q3U1qy.XjQfGfJaaRZoS.','admin','','',''),(5,'','','Guidance Counselor','guidance@example.com','','$2y$12$17p5TVq5nw7vueYM8rp8m.AQvqdkT0./gDTZ/ZFKhAE9Udz1b1ojC','guidance','','',''),(6,'STU000001','','Student 1','student1@example.com','','$2y$12$sCXUHPgLIdihKNhkb7yWjuiQoOxiHz9U9zPywP4BGJqg0PgLKhdXS','student','kvK803iUBZJXCgmTOU0N9TdNXpFa3aDGPBnuEjtYk9bw5M4LonNalUl7USMq','',''),(7,'STU000002','','Student 2','student2@example.com','','$2y$12$QoWcwpsY3uN6HE6AGZyv9O4BDcn73mAko/ru.ya44GnGe9EDkN1lS','student','BQBFYaK7dL','',''),(8,'STU000003','','Student 3','student3@example.com','','$2y$12$Gj0D7Kq.56zA9Gk1mTYe9utyDVUtCmlYU4myZKrSiBDrYXH8hN.W2','student','cytg6KwwZj','',''),(9,'STU000004','','Student 4','student4@example.com','','$2y$12$h/.z2gbu8T2GmU.2jlgQFuF0lI.lz6FufNqSi1CTPQSWwO2DDDzmW','student','CBLcGdwH30','',''),(10,'STU000005','','Student 5','student5@example.com','','$2y$12$nzWPBe.jT0qR0m4175HRROHVniOeZkSXUGGJDmsL2cyiwGfRZm7eq','student','CGYooLVSKf','','');
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

-- Dump completed on 2025-03-21 19:23:39
