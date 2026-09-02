-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: glasswall
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
-- Current Database: `glasswall`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `glasswall` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `glasswall`;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Residential','residential',1),(2,'Commercial & Hospitality','commercial',2),(3,'International','international',3);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_scope`
--

DROP TABLE IF EXISTS `project_scope`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_scope` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `scope_text` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_scope_project` (`project_id`),
  CONSTRAINT `fk_scope_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_scope`
--

LOCK TABLES `project_scope` WRITE;
/*!40000 ALTER TABLE `project_scope` DISABLE KEYS */;
INSERT INTO `project_scope` VALUES (1,1,'Faster workflows and optimized processes.',1),(2,1,'Growth-ready systems for future needs.',2),(3,1,'Better experience and higher engagement.',3),(4,1,'Creative and modern problem-solving.',4);
/*!40000 ALTER TABLE `project_scope` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `thumb_image` varchar(255) NOT NULL,
  `thumb_height` int(11) NOT NULL DEFAULT 340,
  `banner_image` varchar(255) NOT NULL DEFAULT 'assets/images/banner/5650.webp',
  `main_image` varchar(255) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `client` varchar(200) DEFAULT NULL,
  `architect` varchar(200) DEFAULT NULL,
  `consultant` varchar(200) DEFAULT NULL,
  `project_type` varchar(120) DEFAULT NULL,
  `project_area` varchar(120) DEFAULT NULL,
  `floors` varchar(60) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_category` (`category_id`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `fk_project_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,1,'Godrej Platinum','godrej-platinum','assets/images/projects/Residential/Godrej-Platinum.webp',420,'assets/images/banner/5650.webp','https://www.glasswallsystems.in/wp-content/uploads/2024/05/Godrej-Platinum.webp','Mumbai','Godrej & Boyce MFG. Co. Ltd.','T. Khareghat & Associates','Saicone','Residential','60,000 Sqm','28',1,1,'2026-08-04 11:00:43'),(2,1,'Avana','avana','assets/images/projects/Residential/Avana.webp',280,'assets/images/banner/5650.webp',NULL,'Mumbai',NULL,NULL,NULL,'Residential',NULL,NULL,2,1,'2026-08-04 11:00:43'),(3,1,'Indiabulls Sky','indiabulls-sky','assets/images/projects/Residential/Indiabulls-Sky.webp',340,'assets/images/banner/5650.webp',NULL,'Mumbai',NULL,NULL,NULL,'Residential',NULL,NULL,3,1,'2026-08-04 11:00:43'),(4,1,'The 42','the-42','assets/images/projects/Residential/7.One-Avighna-Park.webp',260,'assets/images/banner/5650.webp',NULL,'Kolkata',NULL,NULL,NULL,'Residential',NULL,NULL,4,1,'2026-08-04 11:00:43'),(5,1,'Embassy Boulevard','embassy-boulevard','assets/images/projects/Residential/10.Embassy-Boulevard.webp',400,'assets/images/banner/5650.webp',NULL,'Bengaluru',NULL,NULL,NULL,'Residential',NULL,NULL,5,1,'2026-08-04 11:00:43'),(6,1,'Kingfisher Tower','kingfisher-tower','assets/images/projects/Residential/9.Kingfisher-Tower.webp',320,'assets/images/banner/5650.webp',NULL,'Bengaluru',NULL,NULL,NULL,'Residential',NULL,NULL,6,1,'2026-08-04 11:00:43'),(7,1,'Atmosphere','atmosphere','assets/images/projects/Residential/11.The-Atmosphere.webp',270,'assets/images/banner/5650.webp',NULL,'Mumbai',NULL,NULL,NULL,'Residential',NULL,NULL,7,1,'2026-08-04 11:00:43'),(8,1,'Indiabulls Blu','indiabulls-blu','assets/images/projects/Residential/3.-Indiabulls-Blu-1.webp',410,'assets/images/banner/5650.webp',NULL,'Mumbai',NULL,NULL,NULL,'Residential',NULL,NULL,8,1,'2026-08-04 11:00:43'),(9,1,'Embassy Lake Terrace','embassy-lake-terrace','assets/images/projects/Residential/12.Embassy-Lake-Terrace-1.webp',330,'assets/images/banner/5650.webp',NULL,'Bengaluru',NULL,NULL,NULL,'Residential',NULL,NULL,9,1,'2026-08-04 11:00:43'),(10,1,'Indiabulls Skyforest','indiabulls-skyforest','assets/images/projects/Residential/15.Indiabulls-forest-scaled.webp',410,'assets/images/banner/5650.webp',NULL,'Mumbai',NULL,NULL,NULL,'Residential',NULL,NULL,10,1,'2026-08-04 11:00:43'),(11,1,'Lodha World One','lodha-world-one','assets/images/projects/Residential/Lodha-World-One-1.webp',410,'assets/images/banner/5650.webp',NULL,'Mumbai',NULL,NULL,NULL,'Residential',NULL,NULL,11,1,'2026-08-04 11:00:43'),(12,1,'Gulita','gulita','assets/images/projects/Residential/4.Gulita-1.webp',410,'assets/images/banner/5650.webp',NULL,'Mumbai',NULL,NULL,NULL,'Residential',NULL,NULL,12,1,'2026-08-04 11:00:43'),(13,1,'Artesia','artesia','assets/images/projects/Residential/Artesia.webp',410,'assets/images/banner/5650.webp',NULL,'Mumbai',NULL,NULL,NULL,'Residential',NULL,NULL,13,1,'2026-08-04 11:00:43');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'glasswall'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-04 16:58:03
