-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-09-07 07:17:18
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
-- 表的结构 `cs_user_tag`
--

DROP TABLE IF EXISTS `cs_user_tag`;
CREATE TABLE IF NOT EXISTS `cs_user_tag` (
  `id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL COMMENT '系统的标签配置id（教练不选择系统标签的话为0）',
  `tag_name` varchar(200) NOT NULL,
  `tag_slug` varchar(200) NOT NULL,
  `user_type` tinyint(2) NOT NULL COMMENT '用户类型（0:学员,1:教练...）',
  `user_id` int(11) NOT NULL COMMENT '用户id（0:学员,1:教练...）',
  `is_system` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否系统标签（1:是,2:否；用户不能修改）',
  `order` bigint(20) UNSIGNED NOT NULL,
  `addtime` bigint(20) NOT NULL,
  `apdatetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户自定义标签表（用户包括教练，学员...）用户不可修改 只能选择或不选择';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
