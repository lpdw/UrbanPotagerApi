CREATE DATABASE  IF NOT EXISTS `urbanpotager` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `urbanpotager`;
-- MySQL dump 10.13  Distrib 5.6.13, for osx10.6 (i386)
--
-- Host: 127.0.0.1    Database: urbanpotager
-- ------------------------------------------------------
-- Server version	5.6.27

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ext_translations`
--

DROP TABLE IF EXISTS `ext_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ext_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `locale` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `object_class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `foreign_key` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lookup_unique_idx` (`locale`,`object_class`,`field`,`foreign_key`),
  KEY `translations_lookup_idx` (`locale`,`object_class`,`foreign_key`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ext_translations`
--

LOCK TABLES `ext_translations` WRITE;
/*!40000 ALTER TABLE `ext_translations` DISABLE KEYS */;
INSERT INTO `ext_translations` VALUES (6,'fr','CoreBundle\\Entity\\Type','name','6','Température air'),(7,'fr','CoreBundle\\Entity\\Type','description','6','Température de l\'air dans le potager'),(8,'fr','CoreBundle\\Entity\\Type','name','7','Température de l’eau'),(9,'fr','CoreBundle\\Entity\\Type','description','7','Température de l\'eau dans le potager'),(10,'fr','CoreBundle\\Entity\\Type','name','8','Niveau d\'humidité dans l\'air'),(11,'fr','CoreBundle\\Entity\\Type','description','8','Pourcentage d\'humidité dans le potager'),(12,'fr','CoreBundle\\Entity\\Type','name','9','Niveau d’ensoleillement'),(13,'fr','CoreBundle\\Entity\\Type','description','9','Pourcentage lumière dans le potager'),(14,'fr','CoreBundle\\Entity\\Type','name','10','Niveau du réservoir d\'eau'),(15,'fr','CoreBundle\\Entity\\Type','description','10','Pourcentage d\'eau restant dans le réservoir');
/*!40000 ALTER TABLE `ext_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type`
--

DROP TABLE IF EXISTS `type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `min` decimal(6,2) NOT NULL,
  `max` decimal(6,2) NOT NULL,
  `type` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8CDE5729989D9B62` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type`
--

LOCK TABLES `type` WRITE;
/*!40000 ALTER TABLE `type` DISABLE KEYS */;
INSERT INTO `type` VALUES (6,'Air temperature','air-temperature','Air temperature in the vegetable garden',-20.00,100.00,1,'2016-06-06 21:36:42','2016-06-06 21:39:31'),(7,'Water temperature','water-temperature','Water temperature in the vegetable garden',-10.00,110.00,1,'2016-06-06 21:37:17','2016-06-06 21:42:20'),(8,'Humidity air','humidity-air','Percentage of humidity in the vegetable garden',0.00,100.00,1,'2016-06-06 21:38:10','2016-06-06 21:43:02'),(9,'Daylight level','daylight-level','Percentage of light in the vegetable garden',0.00,100.00,1,'2016-06-06 21:38:32','2016-06-06 21:43:27'),(10,'Water level','water-level','Percentage of water remaining in the vegetable garden',0.00,100.00,1,'2016-06-06 21:38:49','2016-06-06 21:44:02');
/*!40000 ALTER TABLE `type` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-06-21 11:59:26
