-- MySQL dump 10.13  Distrib 5.7.16, for Linux (x86_64)
--
-- Host: localhost    Database: xihaxueche
-- ------------------------------------------------------
-- Server version	5.7.16-1

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
-- Table structure for table `cs_coach_user_relation`
--

DROP TABLE IF EXISTS `cs_coach_user_relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_coach_user_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '学员id',
  `coach_id` int(11) unsigned NOT NULL COMMENT '教练ID',
  `bind_status` int(11) unsigned DEFAULT NULL COMMENT '学员与教练之间的绑定状态（1 已绑定 2 解除绑定 3 学员申请绑定教练 4 教练申请绑定学员 5 学员申请解绑教练 6 教练申请解绑学员）',
  `coach_user_id` int(10) unsigned DEFAULT NULL COMMENT '教练导入的学员的用户id',
  `lesson_id` int(11) DEFAULT NULL COMMENT '科目id',
  `lesson_name` int(11) DEFAULT NULL COMMENT '科目名称',
  `license_id` int(11) DEFAULT NULL COMMENT '牌照id',
  `license_name` int(11) DEFAULT NULL COMMENT '牌照名称',
  `license_title` varchar(100) DEFAULT '' COMMENT '牌照标题',
  `addtime` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户对应所属教练表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cs_coach_user_relation`
--

LOCK TABLES `cs_coach_user_relation` WRITE;
/*!40000 ALTER TABLE `cs_coach_user_relation` DISABLE KEYS */;
/*!40000 ALTER TABLE `cs_coach_user_relation` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-11-24 15:01:32
