CREATE DATABASE  IF NOT EXISTS `pixelstrike` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `pixelstrike`;
-- MySQL dump
--
-- Host: localhost    Database: pixelstrike
-- ------------------------------------------------------
-- Server version	5.5.50

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
-- Table structure for table `Bomb`
--

DROP TABLE IF EXISTS `Bomb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Bomb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lat` double NOT NULL,
  `lon` double NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `planted_by` varchar(45) DEFAULT NULL,
  `defused_by` varchar(45) DEFAULT NULL,
  `planted_on` datetime NOT NULL,
  `deactivated_on` datetime DEFAULT NULL,
  `exploded` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `set_by_idx` (`planted_by`),
  KEY `defused_by_idx` (`defused_by`),
  CONSTRAINT `planted_fk` FOREIGN KEY (`planted_by`) REFERENCES `Player` (`caller_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `defused_fk` FOREIGN KEY (`defused_by`) REFERENCES `Player` (`caller_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Player`
--

DROP TABLE IF EXISTS `Player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caller_id` varchar(45) DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `lon` double DEFAULT NULL,
  `t_ct` tinyint(1) DEFAULT NULL,
  `mac_address` varchar(45) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `lives_actioned` int(11) NOT NULL DEFAULT '0',
  `bombs_actioned` int(11) NOT NULL DEFAULT '0',
  `activation_code` varchar(4) NOT NULL,
  `last_ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mac_address_UNIQUE` (`mac_address`),
  UNIQUE KEY `activation_code_UNIQUE` (`activation_code`),
  UNIQUE KEY `caller_id_UNIQUE` (`caller_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2667 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-10-13  1:21:19
