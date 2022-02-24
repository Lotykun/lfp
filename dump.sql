-- MySQL dump 10.13  Distrib 8.0.28, for Linux (x86_64)
--
-- Host: localhost    Database: symfony_db
-- ------------------------------------------------------
-- Server version	8.0.28

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `symfony_db`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `symfony_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `symfony_db`;

--
-- Table structure for table `contract`
--

DROP TABLE IF EXISTS `contract`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract` (
  `id` int NOT NULL AUTO_INCREMENT,
  `team_id` int NOT NULL,
  `player_id` int DEFAULT NULL,
  `trainer_id` int DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  `amount` double NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E98F2859296CD8AE` (`team_id`),
  KEY `IDX_E98F285999E6F5DF` (`player_id`),
  KEY `IDX_E98F2859FB08EDF6` (`trainer_id`),
  CONSTRAINT `contract_FK` FOREIGN KEY (`trainer_id`) REFERENCES `trainer` (`id`),
  CONSTRAINT `FK_E98F2859296CD8AE` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`),
  CONSTRAINT `FK_E98F285999E6F5DF` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contract`
--

LOCK TABLES `contract` WRITE;
/*!40000 ALTER TABLE `contract` DISABLE KEYS */;
INSERT INTO `contract` VALUES (1,2,12,NULL,'2022-02-21 19:52:45','2022-06-30 00:00:00',1,40.56,'2022-02-21 19:52:59','2022-02-21 19:52:59'),(3,1,NULL,1,'2022-02-23 15:45:42','2022-06-30 00:00:00',1,40.56,'2022-02-23 15:45:42','2022-02-23 15:45:42'),(4,1,17,NULL,'2022-02-24 00:05:32','2022-02-24 00:54:00',0,40.56,'2022-02-24 00:05:32','2022-02-24 00:54:00'),(5,1,18,NULL,'2022-02-24 00:11:14','2022-02-24 00:58:14',0,40.56,'2022-02-24 00:11:14','2022-02-24 00:58:14'),(6,1,19,NULL,'2022-02-24 00:11:28','2022-02-24 00:59:55',0,40.56,'2022-02-24 00:11:28','2022-02-24 00:59:55'),(7,1,17,NULL,'2022-02-24 01:00:37','2022-06-30 00:00:00',1,40.56,'2022-02-24 01:00:37','2022-02-24 01:00:37'),(8,1,18,NULL,'2022-02-24 01:00:49','2022-02-24 01:10:50',0,40.56,'2022-02-24 01:00:49','2022-02-24 01:10:50'),(9,1,19,NULL,'2022-02-24 01:01:06','2022-02-24 01:09:30',0,40.56,'2022-02-24 01:01:06','2022-02-24 01:09:30'),(10,1,19,NULL,'2022-02-24 01:11:16','2022-06-30 00:00:00',1,40.56,'2022-02-24 01:11:16','2022-02-24 01:11:16'),(11,1,18,NULL,'2022-02-24 01:13:55','2022-02-24 01:14:19',0,40.56,'2022-02-24 01:13:55','2022-02-24 01:14:19');
/*!40000 ALTER TABLE `contract` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20220219111212','2022-02-19 13:22:43',48),('DoctrineMigrations\\Version20220221151005','2022-02-21 15:17:56',86),('DoctrineMigrations\\Version20220221155221','2022-02-21 17:18:33',32),('DoctrineMigrations\\Version20220221171454','2022-02-21 17:21:13',35);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player`
--

DROP TABLE IF EXISTS `player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `player` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player`
--

LOCK TABLES `player` WRITE;
/*!40000 ALTER TABLE `player` DISABLE KEYS */;
INSERT INTO `player` VALUES (1,'Diego Armando Maradona',30,'2022-02-21 17:47:45','2022-02-21 17:48:46'),(2,'Sergio Leonel Aguero del Castillo',29,'2022-02-21 17:49:35','2022-02-21 17:50:09'),(3,'Fernando Torres Sanz',29,'2022-02-21 17:51:05','2022-02-21 17:51:05'),(12,'Diego Forlan Corazzo',29,'2022-02-21 17:50:27','2022-02-21 17:50:27'),(13,'Carlos Aguilera Martin',30,'2022-02-21 16:01:57','2022-02-21 16:01:57'),(14,'Igancio Monreal Eraso',27,'2022-02-23 15:03:46','2022-02-23 15:03:46'),(15,'Ander Guevara Lajo',27,'2022-02-23 15:04:11','2022-02-23 15:04:11'),(17,'Yannick Ferreira Carrasco',27,'2022-02-23 15:18:58','2022-02-23 15:18:58'),(18,'Rodrigo Javier De paul',27,'2022-02-23 15:19:13','2022-02-23 15:19:13'),(19,'Hextor Miguel Herrera Lopez',27,'2022-02-23 15:19:36','2022-02-23 15:19:36');
/*!40000 ALTER TABLE `player` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `team` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `budget` double NOT NULL,
  `year` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team`
--

LOCK TABLES `team` WRITE;
/*!40000 ALTER TABLE `team` DISABLE KEYS */;
INSERT INTO `team` VALUES (1,'Club Atlético de Madrid S. A. D.',765000000,NULL,'2022-02-21 17:26:20','2022-02-24 00:28:05'),(2,'Fútbol Club Barcelona',765,1899,'2022-02-21 17:28:23','2022-02-21 18:33:20'),(3,'Real Trampas Club de Futbol',822.1,NULL,'2022-02-23 14:59:55','2022-02-23 14:59:55'),(4,'Real Sociedad de Futbol S.A.D.',110,NULL,'2022-02-23 15:02:50','2022-02-23 15:02:50');
/*!40000 ALTER TABLE `team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainer`
--

DROP TABLE IF EXISTS `trainer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trainer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trainer`
--

LOCK TABLES `trainer` WRITE;
/*!40000 ALTER TABLE `trainer` DISABLE KEYS */;
INSERT INTO `trainer` VALUES (1,'Diego Pablo Simeone',50,'2022-02-21 18:24:23','2022-02-21 18:25:55'),(7,'Alvaro Cervera',51,'2022-02-24 15:31:21','2022-02-24 15:31:21');
/*!40000 ALTER TABLE `trainer` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-02-24 16:20:05
