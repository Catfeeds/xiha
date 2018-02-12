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
-- Table structure for table `cs_exam_history`
--

DROP TABLE IF EXISTS `cs_exam_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_exam_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` int(10) unsigned NOT NULL COMMENT '外键关联',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `site_id` int(10) unsigned NOT NULL COMMENT '场地id',
  `car_id` int(10) unsigned NOT NULL COMMENT '车辆id',
  `identity_no` varchar(28) NOT NULL COMMENT '身份证号码',
  `text_url` varchar(128) NOT NULL COMMENT '训练记录txt存放路径',
  `time_interval` decimal(20,6) NOT NULL COMMENT '时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='历史记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cs_exam_history`
--

LOCK TABLES `cs_exam_history` WRITE;
/*!40000 ALTER TABLE `cs_exam_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `cs_exam_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cs_exam_result_model`
--

DROP TABLE IF EXISTS `cs_exam_result_model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_exam_result_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` int(10) unsigned NOT NULL COMMENT 'exam_id外键关联',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `site_id` int(10) unsigned NOT NULL COMMENT '场地id',
  `car_id` int(10) unsigned NOT NULL COMMENT '车辆id',
  `item_content` varchar(512) NOT NULL COMMENT '扣分项',
  `point_penalty` varchar(128) NOT NULL COMMENT '扣分值',
  `time_interval` decimal(20,6) unsigned NOT NULL COMMENT '时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评判结果';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cs_exam_result_model`
--

LOCK TABLES `cs_exam_result_model` WRITE;
/*!40000 ALTER TABLE `cs_exam_result_model` DISABLE KEYS */;
/*!40000 ALTER TABLE `cs_exam_result_model` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cs_xwyd2_model`
--

DROP TABLE IF EXISTS `cs_xwyd2_model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_xwyd2_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `site_id` int(10) unsigned NOT NULL COMMENT '场地id',
  `car_id` int(10) unsigned NOT NULL COMMENT '车辆id',
  `advance_distance` decimal(20,6) NOT NULL COMMENT '前进距离',
  `back_distance` decimal(20,6) NOT NULL COMMENT '后退距离',
  `course_angle` decimal(20,6) NOT NULL COMMENT '航向角，度，与正北的夹角',
  `date` varchar(20) NOT NULL COMMENT '日期 20150320',
  `time` decimal(20,6) NOT NULL COMMENT '时间 092328',
  `east_distance` decimal(20,6) NOT NULL COMMENT '东向距离，米，以基站为原点',
  `north_distance` decimal(20,6) NOT NULL COMMENT '北向距离，米，以基站为原点',
  `pitch_angle` decimal(20,6) NOT NULL COMMENT '俯仰角，度',
  `roll_angle` decimal(20,6) NOT NULL COMMENT '横滚角，度',
  `sky_distance` decimal(20,6) NOT NULL COMMENT '天向距离，米，以基站为原点',
  `speed` decimal(20,6) NOT NULL COMMENT '速度，公里/时',
  `retain_position` varchar(100) NOT NULL,
  `gps_status` varchar(100) NOT NULL,
  `time_interval` decimal(20,6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='星网宇达数据';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cs_xwyd2_model`
--

LOCK TABLES `cs_xwyd2_model` WRITE;
/*!40000 ALTER TABLE `cs_xwyd2_model` DISABLE KEYS */;
/*!40000 ALTER TABLE `cs_xwyd2_model` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cs_xwcj_model`
--

DROP TABLE IF EXISTS `cs_xwcj_model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cs_xwcj_model` (
  `exam_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `site_id` int(10) unsigned NOT NULL COMMENT '场地id',
  `car_id` int(10) unsigned NOT NULL COMMENT '车辆id',
  `sig1` int(11) unsigned NOT NULL COMMENT '安全带信号',
  `sig2` int(11) unsigned NOT NULL COMMENT '车门灯',
  `sig3` int(11) unsigned NOT NULL COMMENT '喇叭',
  `sig4` int(11) unsigned NOT NULL COMMENT '雨刮',
  `sig5` int(11) unsigned NOT NULL COMMENT '警示灯',
  `sig6` int(11) unsigned NOT NULL COMMENT '离合器',
  `sig7` int(11) unsigned NOT NULL COMMENT '手制动',
  `sig8` int(11) unsigned NOT NULL COMMENT '档位信号',
  `sig9` int(11) unsigned NOT NULL COMMENT '发动机',
  `sig10` int(11) unsigned NOT NULL COMMENT '呼叫请求',
  `sig11` int(11) unsigned NOT NULL COMMENT '车型',
  `sig12` int(11) unsigned NOT NULL COMMENT '倒车灯',
  `sig13` int(11) unsigned NOT NULL COMMENT '副脚刹',
  `sig14` int(11) unsigned NOT NULL COMMENT '座位',
  `sig15` int(11) unsigned NOT NULL COMMENT '点火信号',
  `sig16` int(11) unsigned NOT NULL COMMENT '左转向灯信号',
  `sig17` int(11) unsigned NOT NULL COMMENT '右转向灯信号',
  `sig18` int(11) unsigned NOT NULL COMMENT '脚刹信号',
  `sig19` int(11) unsigned NOT NULL COMMENT '示宽灯信号',
  `sig20` int(11) unsigned NOT NULL COMMENT '远光信号',
  `sig21` int(11) unsigned NOT NULL COMMENT '近光信号',
  `sig22` int(11) unsigned NOT NULL COMMENT '雾灯信号',
  `sig23` int(11) unsigned NOT NULL COMMENT '车头',
  `sig24` int(11) unsigned NOT NULL COMMENT '车尾',
  `sig25` int(11) unsigned NOT NULL COMMENT '转速',
  `sig26` int(11) unsigned NOT NULL COMMENT '单边桥前',
  `sig27` int(11) unsigned NOT NULL COMMENT '单边桥后',
  `sig28` int(11) unsigned NOT NULL COMMENT '单边桥中',
  `sig29` int(11) unsigned NOT NULL COMMENT '预留',
  `timeInterval` decimal(20,6) NOT NULL,
  PRIMARY KEY (`exam_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='电子教练信号采集协议表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cs_xwcj_model`
--

LOCK TABLES `cs_xwcj_model` WRITE;
/*!40000 ALTER TABLE `cs_xwcj_model` DISABLE KEYS */;
/*!40000 ALTER TABLE `cs_xwcj_model` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-05-12 13:50:24
