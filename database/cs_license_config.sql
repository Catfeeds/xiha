-- MySQL dump 10.13  Distrib 5.7.15, for Linux (x86_64)
--
-- Host: localhost    Database: xihaxueche
-- ------------------------------------------------------
-- Server version	5.7.15-1

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
-- Table structure for table `cs_license_config`
--

DROP TABLE IF EXISTS `cs_license_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_license_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `license_id` int(11) NOT NULL COMMENT '驾驶证对应的id',
  `license_name` varchar(32) NOT NULL COMMENT '驾驶证名称',
  `license_class` varchar(128) NOT NULL DEFAULT '' COMMENT '大致同类别',
  `license_title` varchar(128) NOT NULL DEFAULT '' COMMENT '中文标识',
  `is_open` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否开启（1：开启，2：关闭）',
  `order` int(10) unsigned NOT NULL COMMENT '排序',
  `addtime` bigint(20) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cs_license_config`
--

LOCK TABLES `cs_license_config` WRITE;
/*!40000 ALTER TABLE `cs_license_config` DISABLE KEYS */;
INSERT INTO `cs_license_config` VALUES (1,1,'C1','C1/C2/C3','小车',1,1,1473139955),(2,2,'C2','C1/C2/C3','小车',1,2,1473139980),(3,3,'C3','C1/C2/C3','小车',1,3,1473140026),(4,4,'C4','','',1,4,1473140069),(5,5,'C5','','',1,5,1473140119),(6,6,'A1','A1/A3/B1','客车',1,6,1473140149),(7,7,'A2','A2/B2','货车',1,7,1473140219),(8,8,'A3','A1/A3/B1','客车',1,8,1473140226),(9,9,'B1','A1/A3/B1','客车',1,9,1473140275),(10,10,'B2','A2/B2','货车',1,10,1473140301),(11,11,'D','D/E/F','摩托车',1,11,1473140339),(12,12,'E','D/E/F','摩托车',1,12,1473140363),(13,13,'F','D/E/F','摩托车',1,13,1473140400),(14,14,'M','','',1,14,1473140423),(15,15,'N','','',1,15,1473140463),(16,16,'P','','',1,16,1473140471);
/*!40000 ALTER TABLE `cs_license_config` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-10-27  9:13:35
