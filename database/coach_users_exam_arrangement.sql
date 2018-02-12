-- MySQL dump 10.16  Distrib 10.1.21-MariaDB, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: localhost
-- ------------------------------------------------------
-- Server version	10.1.21-MariaDB-5+b1

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
-- Table structure for table `cs_coach_users_exam_arrangement`
--

DROP TABLE IF EXISTS `cs_coach_users_exam_arrangement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_coach_users_exam_arrangement` (
  `id` int(14) NOT NULL AUTO_INCREMENT,
  `coach_users_id` varchar(500) DEFAULT NULL COMMENT '关联的用户ID',
  `coach_id` int(11) DEFAULT '0' COMMENT '教练ID',
  `exam_lesson` int(4) DEFAULT '0' COMMENT '考试科目（2：科目二 | 3：科目三）',
  `exam_year` int(11) DEFAULT '0' COMMENT '年（考试时间）',
  `exam_month` int(11) DEFAULT '0' COMMENT '月（考试时间）',
  `exam_day` int(11) DEFAULT '0' COMMENT '日（考试时间）',
  `exam_timestamp` int(14) DEFAULT '0' COMMENT '考试时间戳',
  `exam_site` varchar(30) DEFAULT NULL COMMENT '考试场地',
  `exam_beizhu` varchar(60) DEFAULT NULL COMMENT '考试备注',
  `user_beizhu` varchar(500) DEFAULT NULL COMMENT '用户（学员）备注',
  `remind_timestamp` int(14) DEFAULT '0' COMMENT '指定时间提醒教练',
  `deleted` int(4) NOT NULL DEFAULT '1' COMMENT '删除状态（1：未删除 | 2：已删除）',
  `addtime` int(14) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `updatetime` int(14) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='学员考试安排表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cs_coach_users_exam_arrangement`
--

LOCK TABLES `cs_coach_users_exam_arrangement` WRITE;
/*!40000 ALTER TABLE `cs_coach_users_exam_arrangement` DISABLE KEYS */;
/*!40000 ALTER TABLE `cs_coach_users_exam_arrangement` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-04-26 16:02:51
