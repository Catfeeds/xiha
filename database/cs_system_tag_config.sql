-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-09-07 07:02:22
-- 服务器版本： 5.7.9
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xihaxueche`
--

-- --------------------------------------------------------

--
-- 表的结构 `cs_system_tag_config`
--

DROP TABLE IF EXISTS `cs_system_tag_config`;
CREATE TABLE IF NOT EXISTS `cs_system_tag_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(200) NOT NULL COMMENT '标签中文名',
  `tag_slug` varchar(200) NOT NULL COMMENT '标签英文名',
  `user_type` int(11) NOT NULL COMMENT '标签类别（适用于0:学员,1,教练，2,驾校...）',
  `order` bigint(20) UNSIGNED NOT NULL,
  `addtime` bigint(20) NOT NULL,
  `updatetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统的标签配置表（学员和教练不同角色共用）';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
