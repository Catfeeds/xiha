-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-09-07 07:39:54
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
-- 表的结构 `cs_time_config_template`
--

DROP TABLE IF EXISTS `cs_time_config_template`;
CREATE TABLE IF NOT EXISTS `cs_time_config_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `temp_id` int(11) NOT NULL COMMENT '模板ID',
  `start_time` int(11) NOT NULL COMMENT '（如：8:00）',
  `end_time` int(11) NOT NULL COMMENT '（如：9:30）',
  `start_hour` int(11) NOT NULL COMMENT '8',
  `start_minute` int(4) DEFAULT '0',
  `end_hour` int(11) NOT NULL COMMENT '9',
  `end_minute` int(4) DEFAULT '0' COMMENT '30',
  `lesson_time` int(11) NOT NULL COMMENT '课程时间（单位小时）',
  `lesson_name` varchar(20) NOT NULL COMMENT '科目二，科目三',
  `lesson_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `max_user_num` int(2) NOT NULL DEFAULT '1' COMMENT '最大可预约用户数（暂时不开放的 默认一个人）',
  `is_online` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否在线（1：是，2：否 ，3可休息）',
  `addtime` bigint(20) NOT NULL,
  `updatetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='模板列表表';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
