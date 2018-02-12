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
-- Table structure for table `cs_video_detail`
--

DROP TABLE IF EXISTS `cs_video_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_video_detail` (
  `id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(520) NOT NULL COMMENT '评论内容|回复内容',
  `video_id` int(14) unsigned NOT NULL COMMENT '对应学车视频的主键ID',
  `user_id` int(14) unsigned NOT NULL COMMENT '用户ID',
  `user_phone` varchar(20) NOT NULL COMMENT '用户手机',
  `user_name` varchar(20) NOT NULL COMMENT '用户名称',
  `parent_id` int(14) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
  `votes` int(14) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `addtime` int(14) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='学车视频详情表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cs_video_detail`
--

LOCK TABLES `cs_video_detail` WRITE;
/*!40000 ALTER TABLE `cs_video_detail` DISABLE KEYS */;
INSERT INTO `cs_video_detail` VALUES (1,'科二考了四五次都没过，又得重头来了',1,36720,'4294967295','陈曦哈哈',0,3,1504508594),(2,'科二不错，一把过',1,36720,'4294967295','陈曦哈哈',0,2,1504508628),(3,'个人认为最难的一项，学了一个多月才能顺应的手',1,36720,'4294967295','陈曦哈哈',0,1,1504512580),(4,'个人认为最难的一项，也是我学习最好的一项',1,36720,'4294967295','陈曦哈哈',0,1,1504512620),(5,'测试下',1,36720,'4294967295','陈曦哈哈',0,1,1504513221),(6,'测试下哦',1,36720,'4294967295','陈曦哈哈',0,0,1504516392);
/*!40000 ALTER TABLE `cs_video_detail` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-09-05  4:59:58
