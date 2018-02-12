-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-09-07 08:02:29
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
-- 表的结构 `cs_coach_time_config_new`
--

DROP TABLE IF EXISTS `cs_coach_time_config_new`;
CREATE TABLE IF NOT EXISTS `cs_coach_time_config_new` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` varchar(20) NOT NULL COMMENT '8:00',
  `end_time` varchar(20) NOT NULL COMMENT '9:30',
  `start_hour` int(11) NOT NULL COMMENT '8',
  `start_minute` int(11) DEFAULT '0',
  `end_hour` int(11) NOT NULL COMMENT '9',
  `end_minute` int(11) DEFAULT '0' COMMENT '30',
  `lesson_time` varchar(20) NOT NULL COMMENT '课程时间（单位小时）',
  `lesson_name` varchar(200) NOT NULL COMMENT '科目二，科目三',
  `lesson_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `max_user_num` int(11) NOT NULL DEFAULT '1',
  `is_online` tinyint(2) NOT NULL COMMENT '是否在线（1：是，2：否）',
  `is_publish` tinyint(2) NOT NULL DEFAULT '1' COMMENT '最终发布时间配置（1 :未发布, 2:已发布）',
  `year` int(11) NOT NULL DEFAULT '0',
  `month` int(11) NOT NULL DEFAULT '0',
  `day` int(11) NOT NULL DEFAULT '0',
  `timestamp` bigint(20) NOT NULL DEFAULT '0' COMMENT '年月日的时间戳',
  `addtime` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='教练时间最终配置表（时间配置结果表）';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
