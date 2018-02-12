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
-- Table structure for table `mi_admin`
--

DROP TABLE IF EXISTS `mi_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mi_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '用户名称',
  `password` varchar(80) NOT NULL COMMENT '用户登录密码',
  `phone` varchar(20) DEFAULT NULL COMMENT '用户手机',
  `role_permission_id` int(14) NOT NULL DEFAULT '0' COMMENT '角色权限ID',
  `role_id` int(14) NOT NULL DEFAULT '0' COMMENT '角色ID',
  `parent_id` int(14) NOT NULL COMMENT '父级ID',
  `owner_id` int(14) NOT NULL DEFAULT '0' COMMENT '同roles表中的owner_id对应',
  `content` varchar(200) DEFAULT NULL COMMENT '登陆者中文名称',
  `is_close` int(4) NOT NULL DEFAULT '2' COMMENT '是否关闭（1：是 | 2：否）',
  `addtime` int(14) DEFAULT NULL COMMENT '添加时间',
  `updatetime` int(14) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mi_admin`
--

LOCK TABLES `mi_admin` WRITE;
/*!40000 ALTER TABLE `mi_admin` DISABLE KEYS */;
INSERT INTO `mi_admin` VALUES (1,'admin','89b2921b9d3fed530116cb458b6009eb','18756004209',1,1,1,0,'超级管理员',1,0,1500520989),(2,'ceshi','89b2921b9d3fed530116cb458b6009eb','18756004201',2,3,1,0,'ceshi',1,1500953195,NULL);
/*!40000 ALTER TABLE `mi_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mi_client_version`
--

DROP TABLE IF EXISTS `mi_client_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mi_client_version` (
  `id` int(14) NOT NULL AUTO_INCREMENT,
  `os_type` int(4) NOT NULL DEFAULT '1' COMMENT '系统类型（1：windows）',
  `client_name` varchar(20) DEFAULT NULL COMMENT '客户端名称',
  `client_type` int(4) NOT NULL DEFAULT '1' COMMENT '客户端类型（1、喵咪鼠标）',
  `version` varchar(10) NOT NULL DEFAULT '' COMMENT '版本号，版本号必须是增加的',
  `version_code` int(11) NOT NULL DEFAULT '0' COMMENT '版本code，跟version不同，辅助表示版本更新迭代信息',
  `update_log` varchar(1000) DEFAULT NULL COMMENT '更新日志（本版本更新了哪些内容）',
  `download_url` varchar(500) DEFAULT NULL COMMENT '下载地址',
  `addtime` int(14) DEFAULT NULL COMMENT '添加时间',
  `updatetime` int(14) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='客户端版本信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mi_client_version`
--

LOCK TABLES `mi_client_version` WRITE;
/*!40000 ALTER TABLE `mi_client_version` DISABLE KEYS */;
INSERT INTO `mi_client_version` VALUES (3,1,'喵咪鼠标',1,'1.0',1,'1、完成鼠标语音的最初识别<div>2、完成鼠标的基础功能</div><div>3、新增词库功能</div>','miaomi/3/20170719/1037479337a7a515076df4d2bf3a9c0b.txt',1500443928,1500443957);
/*!40000 ALTER TABLE `mi_client_version` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mi_menu`
--

DROP TABLE IF EXISTS `mi_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mi_menu` (
  `moduleid` int(14) NOT NULL AUTO_INCREMENT,
  `m_applicationid` int(11) NOT NULL COMMENT '应用ID可以实现不同管理系统公用一个菜单表',
  `m_parentid` int(11) NOT NULL COMMENT '菜单父级ID-对于的值未菜单ID 如果是一级菜单设置为0',
  `m_pagecode` varchar(6) NOT NULL COMMENT '菜单排序字段',
  `m_controller` varchar(50) DEFAULT NULL COMMENT '控制器url',
  `m_type` int(11) NOT NULL COMMENT '菜单类型（1：模块 2：操作{增删改查等}）',
  `m_cname` varchar(50) DEFAULT NULL COMMENT '菜单中文名',
  `m_description` varchar(255) NOT NULL COMMENT '菜单的描述l',
  `m_imageurl` varchar(255) NOT NULL COMMENT '菜单栏显示的图片路径',
  `i_order` int(11) DEFAULT '50' COMMENT '菜单排序',
  `m_close` int(4) NOT NULL DEFAULT '2' COMMENT '菜单开启状态（1：开启 | 2：不开启）',
  `is_top` int(4) NOT NULL DEFAULT '2' COMMENT '支持顶部展示（1：支持 | 2：不支持）',
  `addtime` int(14) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `updatetime` int(14) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`moduleid`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mi_menu`
--

LOCK TABLES `mi_menu` WRITE;
/*!40000 ALTER TABLE `mi_menu` DISABLE KEYS */;
INSERT INTO `mi_menu` VALUES (1,1,0,'976902','admin/manage',1,'权限管理','权限管理','',50,1,1,1500628763,1500976902),(2,1,1,'961225','admin/manage',1,'管理员列表','管理员管理','',50,1,2,0,1500961225),(7,1,2,'976721','admin/addmanage',2,'新增','添加管理员','',50,1,1,1500609241,1500976721),(8,1,2,'976684','admin/editmanage',2,'修改','修改管理员的信息','',50,1,2,1500609283,1500976684),(10,1,0,'960729','product/index',1,'产品管理','管理产品的上线','',1,1,2,1500627341,1500960729),(11,1,10,'627503','product/index',1,'更新记录','产品版本更新的记录','',50,1,2,1500627503,0),(12,1,11,'960658','product/add',2,'新增','新增产品记录','',50,1,2,1500627571,1500960658),(13,1,11,'627606','product/edit',2,'更新','更新产品记录','',50,1,2,1500627606,0),(14,1,11,'975283','product/delajax',2,'删除','删除产品更新记录','',50,1,2,1500627655,1500975283),(15,1,2,'976214','admin/delajax?type=\"manage\"',2,'删除','删除管理员信息','',50,1,2,1500627768,1500976214),(17,1,1,'961237','admin/roles',1,'角色列表','管理角色信息','',50,1,2,1500886706,1500961237),(18,1,17,'965086','admin/addrole',2,'新增','添加角色','',50,1,1,1500886750,1500965086),(19,1,17,'888694','admin/editrole',2,'编辑','修改角色信息','',50,1,2,1500888694,0),(20,1,1,'960473','admin/menu',1,'菜单列表','管理菜单信息','',50,1,2,1500960473,0),(21,1,20,'970748','admin/addmenu',2,'新增','添加菜单','',50,1,1,1500960547,1500970748),(22,1,20,'960594','admin/editmenu',2,'编辑','修改菜单信息','',50,1,2,1500960594,0),(23,1,17,'974480','admin/delajax?type=\"role\"',2,'删除','删除角色','',50,1,2,1500974480,0),(24,1,20,'974521','admin/delajax?type=\"menu\"',2,'删除','删除菜单','',50,1,2,1500974521,0);
/*!40000 ALTER TABLE `mi_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mi_rolepermission`
--

DROP TABLE IF EXISTS `mi_rolepermission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mi_rolepermission` (
  `l_rolepress_incode` int(14) NOT NULL AUTO_INCREMENT,
  `l_role_id` int(14) NOT NULL COMMENT '角色ID',
  `module_id` varchar(3000) DEFAULT NULL COMMENT '最小菜单功能单位的id',
  `addtime` int(14) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`l_rolepress_incode`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mi_rolepermission`
--

LOCK TABLES `mi_rolepermission` WRITE;
/*!40000 ALTER TABLE `mi_rolepermission` DISABLE KEYS */;
INSERT INTO `mi_rolepermission` VALUES (1,1,'7,8,12,13,14,15,16,18,19,21,22,2,11,17,20,1,10',1500965633),(2,3,'12,13,14,11,10',1500975534),(3,4,'8,13,15,18,2,11,17,1,10',1500963049);
/*!40000 ALTER TABLE `mi_rolepermission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mi_roles`
--

DROP TABLE IF EXISTS `mi_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mi_roles` (
  `l_role_id` int(14) NOT NULL AUTO_INCREMENT,
  `s_role_name` varchar(50) DEFAULT NULL COMMENT '角色名称',
  `s_description` varchar(100) NOT NULL COMMENT '角色描述',
  `owner_id` int(11) NOT NULL COMMENT '后台管理者ID',
  `owner_type` int(11) NOT NULL COMMENT '后台管理者类型(1:超管)',
  `addtime` int(14) DEFAULT '0' COMMENT '添加时间',
  `updatetime` int(14) DEFAULT '0' COMMENT '新增时间',
  PRIMARY KEY (`l_role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mi_roles`
--

LOCK TABLES `mi_roles` WRITE;
/*!40000 ALTER TABLE `mi_roles` DISABLE KEYS */;
INSERT INTO `mi_roles` VALUES (1,'超级管理员','最高等级的管理员，任何操作请慎重',0,1,0,1500965633),(3,'测试','测试',0,1,1500892315,1500975534),(4,'测试全选123','测试全选',0,1,1500892540,1500963049);
/*!40000 ALTER TABLE `mi_roles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-07-26 10:23:11
