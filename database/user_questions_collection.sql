-- MySQL dump 10.16  Distrib 10.1.26-MariaDB, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: xihaxueche
-- ------------------------------------------------------
-- Server version	10.1.26-MariaDB-0+deb9u1

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
-- Table structure for table `cs_user_questions_collection`
--

DROP TABLE IF EXISTS `cs_user_questions_collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_user_questions_collection` (
  `id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(14) unsigned NOT NULL COMMENT '用户ID',
  `questions_id` int(14) unsigned NOT NULL COMMENT '题目详情表中的主键ID',
  `chapter_id` int(14) unsigned NOT NULL COMMENT '题目的章节ID',
  `is_show` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否在收藏题里（0：否，1：是）',
  `addtime` int(14) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='用户的题目收藏表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cs_user_questions_collection`
--

LOCK TABLES `cs_user_questions_collection` WRITE;
/*!40000 ALTER TABLE `cs_user_questions_collection` DISABLE KEYS */;
INSERT INTO `cs_user_questions_collection` VALUES (1,36720,6221,121,1,1504236201),(2,36720,6223,121,1,1504236206),(4,36720,6224,121,1,1504248405),(5,36720,6225,121,1,1504248791),(6,36720,6226,121,1,1504256260);
/*!40000 ALTER TABLE `cs_user_questions_collection` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-09-05  4:59:02
