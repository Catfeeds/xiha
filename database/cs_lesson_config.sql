-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-09-06 05:22:06
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
-- 表的结构 `cs_lesson_config`
--

DROP TABLE IF EXISTS `cs_lesson_config`;
CREATE TABLE IF NOT EXISTS `cs_lesson_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL COMMENT '科目对应的id',
  `lesson_name` varchar(32) NOT NULL COMMENT '科目名称',
  `is_open` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否启用（1：是，2：否）',
  `order` int(10) UNSIGNED NOT NULL COMMENT '排序',
  `addtime` bigint(20) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `cs_lesson_config`
--

INSERT INTO `cs_lesson_config` (`id`, `lesson_id`, `lesson_name`, `is_open`, `order`, `addtime`) VALUES
(1, 1, '科目一', 1, 1, 1473139188),
(2, 2, '科目二', 1, 2, 1473139215),
(3, 3, '科目三', 1, 3, 1473139258),
(4, 4, '科目四', 1, 4, 1473139281);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
