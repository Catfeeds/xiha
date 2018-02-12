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
-- Table structure for table `cs_video`
--

DROP TABLE IF EXISTS `cs_video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_video` (
  `id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL COMMENT '视频名称',
  `pic_url` varchar(100) NOT NULL COMMENT '图片地址',
  `video_url` varchar(100) NOT NULL COMMENT '视频地址',
  `car_type` varchar(20) NOT NULL COMMENT '牌照类型（如：car，bus，truk，moto）',
  `course` varchar(20) NOT NULL COMMENT '科目（kemu2，kemu3）',
  `video_time` int(14) NOT NULL COMMENT '视频时间（单位秒）',
  `skill_intro` varchar(20) NOT NULL COMMENT '简短介绍或技巧介绍',
  `video_desc` varchar(255) DEFAULT NULL COMMENT '视频的描述',
  `views` int(14) unsigned NOT NULL DEFAULT '0' COMMENT '视频被浏览的次数',
  `v_order` int(14) NOT NULL DEFAULT '50' COMMENT '排序',
  `is_open` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否开启（0：未开启|1：开启）',
  `addtime` int(14) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `updatetime` int(14) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='视频学习表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cs_video`
--

LOCK TABLES `cs_video` WRITE;
/*!40000 ALTER TABLE `cs_video` DISABLE KEYS */;
INSERT INTO `cs_video` VALUES (1,'倒车入库','video/subjects/lesson_2/i6223561274278543874.png','video/subjects/lesson_2/download/i6223561274278543874.mp4','car','kemu2',282,'整个考场为一个“凸”字型','倒车入库是驾驶员考试中的一个考核点，即操作车辆从两侧正确到入车库。考核驾驶员是否能正确判断车辆倒车轨迹，操控车辆完成入库',15,50,1,0,0),(2,'曲线行驶','video/subjects/lesson_2/i6223609442529706498.png','video/subjects/lesson_2/download/i6223609442529706498.mp4','car','kemu2',174,'在规定宽度的S型路面上行驶','考试要求是车辆在规定宽度的S型路面上行驶，不得挤压路边缘边，方向运用自如。目的是为了培养机动车驾驶人转向的运用及车轮的把控',1,50,1,0,0),(3,'直角转弯','video/subjects/lesson_2/i6223610324461814273.png','video/subjects/lesson_2/download/i6223610324461814273.mp4','car','kemu2',153,'小车通过“直角”路段转弯过程','考核驾驶员在运动中正确操作转向装置，准确判断内外轮差的能力，按规定的线路行驶，由左向右或由右向左直接转弯，一次通过，中途不得停车，车轮不得碰轧车道边线。\n',0,50,1,0,0),(4,'坡道定点停车和起步','video/subjects/lesson_2/i6223604730757644801.png','video/subjects/lesson_2/download/i6223604730757644801.mp4','car','kemu2',180,'上坡前，使汽车靠向道路右侧','考核驾驶员上坡路段驾驭车辆的能力，能正确地在固定地点靠边停稳车辆，准确使用档位和离合器，再平稳起步',0,50,1,0,0),(5,'侧方停车','video/subjects/lesson_2/i6223554707994968577.png','video/subjects/lesson_2/download/i6223554707994968577.mp4','car','kemu2',225,'一进一退方式将车停库区','考核驾驶员将车辆正确停入道路右侧车位的能力。机动车驾驶人驾驶车辆在车轮不轧碰车道边线、库位边线的情况下，通过一进一退的方式，将车辆停入右侧车位中',0,50,1,0,0);
/*!40000 ALTER TABLE `cs_video` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-09-05  5:00:18
