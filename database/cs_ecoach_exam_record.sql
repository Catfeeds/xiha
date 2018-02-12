-- MySQL dump 10.16  Distrib 10.1.23-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: xihaxueche
-- ------------------------------------------------------
-- Server version	10.1.23-MariaDB-9+deb9u1

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
-- Table structure for table `cs_ecoach_exam_record`
--

DROP TABLE IF EXISTS `cs_ecoach_exam_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_ecoach_exam_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录id自增',
  `name` char(64) NOT NULL COMMENT '学员姓名',
  `idcard` char(64) NOT NULL COMMENT '学员身份身份证件号',
  `trainsub` char(32) NOT NULL DEFAULT '科目二' COMMENT '训练科目：科目二，科目三',
  `date` int(10) unsigned NOT NULL COMMENT '训练时间戳',
  `date_fmt` date DEFAULT NULL COMMENT '日期，方便查看',
  `begin` char(16) NOT NULL COMMENT '训练开始时间 HH:MM',
  `end` char(16) NOT NULL COMMENT '训练结束时间 HH:MM',
  `total` smallint(6) NOT NULL COMMENT '考试总次数',
  `pass` smallint(6) NOT NULL COMMENT '考试合格次数',
  `avspeed` decimal(10,2) NOT NULL COMMENT '平均速度  单位：km/h',
  `distance` decimal(10,2) NOT NULL COMMENT '学习里程数   单位：km',
  `failstat` text COMMENT '出错统计结果',
  `trainstat` text NOT NULL COMMENT '训练数据统计',
  `traindetail` text COMMENT '训练记录详情',
  `addtime` int(10) unsigned DEFAULT NULL COMMENT '添加时间',
  `uptime` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idcard` (`idcard`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='电子教练模拟考试记录';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-08-04 14:40:06
