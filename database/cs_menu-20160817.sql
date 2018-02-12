-- MySQL dump 10.13  Distrib 5.6.30, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: xihaxueche
-- ------------------------------------------------------
-- Server version	5.6.30-1

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
-- Table structure for table `cs_menu`
--

DROP TABLE IF EXISTS `cs_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_menu` (
  `moduleid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `m_applicationid` int(11) NOT NULL DEFAULT '0',
  `m_parentid` int(11) NOT NULL DEFAULT '0',
  `m_pagecode` varchar(6) NOT NULL DEFAULT '0',
  `m_controller` varchar(50) NOT NULL DEFAULT '' COMMENT '控制器url',
  `m_type` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '菜单类型（1：模块 2：操作{增删改查等}）',
  `m_cname` varchar(50) NOT NULL DEFAULT '',
  `m_directory` varchar(255) NOT NULL DEFAULT '',
  `m_imageurl` varchar(255) NOT NULL DEFAULT '',
  `m_close` int(4) NOT NULL DEFAULT '1' COMMENT '是否关闭（1：关闭 2：不关闭）',
  PRIMARY KEY (`moduleid`),
  UNIQUE KEY `cs_menu` (`m_applicationid`,`m_pagecode`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cs_menu`
--

LOCK TABLES `cs_menu` WRITE;
/*!40000 ALTER TABLE `cs_menu` DISABLE KEYS */;
INSERT INTO `cs_menu` VALUES (3,1,0,'395163','Cars/index',1,'车辆管理','车辆管理模块','',1),(4,1,3,'395322','Cars/addCarsCategory',2,'添加车辆类型','添加车辆类型，主要包含打点图资源文件','',1),(5,1,0,'395914','School/index',1,'驾校管理','驾校管理模块','',1),(6,1,5,'395967','School/siteAdmin',2,'场地管理','管理驾校下面的场地','',1),(7,1,3,'396067','Cars/delCarsCategory',2,'删除车辆类型','对无用的车辆类型进行删除操作','',1);
/*!40000 ALTER TABLE `cs_menu` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-17 18:35:41
