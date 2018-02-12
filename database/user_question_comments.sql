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
-- Table structure for table `cs_user_question_comments`
--

DROP TABLE IF EXISTS `cs_user_question_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_user_question_comments` (
  `id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(512) NOT NULL COMMENT '评论内容',
  `question_id` int(14) unsigned NOT NULL COMMENT '评论的题目ID',
  `user_id` int(14) unsigned NOT NULL COMMENT '评论者ID',
  `user_name` varchar(512) NOT NULL COMMENT '评论者名',
  `user_phone` varchar(20) DEFAULT NULL COMMENT '评论者手机号',
  `parent_id` int(14) unsigned NOT NULL DEFAULT '0' COMMENT '评论内容的父级ID',
  `votes` int(14) unsigned NOT NULL DEFAULT '0' COMMENT '被点赞数',
  `addtime` int(14) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='题目评论详情表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cs_user_question_comments`
--

LOCK TABLES `cs_user_question_comments` WRITE;
/*!40000 ALTER TABLE `cs_user_question_comments` DISABLE KEYS */;
INSERT INTO `cs_user_question_comments` VALUES (1,'瞟了一眼就选了，有的题目是只要看选项就知道是哪些答案了',6221,36720,'陈曦哈哈','18756004209',0,1,1504534861),(2,'瞟了一眼就选了',6221,36720,'陈曦哈哈','18756004209',0,0,1504543492),(3,'哈哈，简单',6221,36720,'陈曦哈哈','18756004209',0,0,1504545842);
/*!40000 ALTER TABLE `cs_user_question_comments` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-09-05  5:00:49
