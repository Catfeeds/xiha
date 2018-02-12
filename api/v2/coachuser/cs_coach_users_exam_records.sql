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
-- Table structure for table `cs_coach_users_exam_records`
--

DROP TABLE IF EXISTS `cs_coach_users_exam_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_coach_users_exam_records` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `coach_users_id` int(11) unsigned NOT NULL COMMENT '关联的用户id',
  `coach_id` int(11) unsigned NOT NULL COMMENT '教练id',
  `exam_timestamp` int(11) unsigned NOT NULL COMMENT '考试时间戳',
  `addtime` int(11) unsigned NOT NULL COMMENT '添加时间',
  `lesson` int(11) NOT NULL COMMENT '考试科目，2科目二，3科目三',
  `exam_score` int(11) NOT NULL COMMENT '考试分数',
  `update_time` int(11) unsigned NOT NULL COMMENT '编辑更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学员的考试记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cs_coach_users_exam_records`
--

LOCK TABLES `cs_coach_users_exam_records` WRITE;
/*!40000 ALTER TABLE `cs_coach_users_exam_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `cs_coach_users_exam_records` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-07-11 16:11:10
