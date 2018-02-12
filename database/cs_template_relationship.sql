-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-09-07 07:23:46
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
-- 表的结构 `cs_template_relationship`
--

DROP TABLE IF EXISTS `cs_template_relationship`;
CREATE TABLE IF NOT EXISTS `cs_template_relationship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `temp_owner_id` int(11) NOT NULL COMMENT '模板所有者ID（驾校ID, 教练ID...）',
  `temp_type` tinyint(2) NOT NULL COMMENT '模板角色分类（1：教练，2：驾校...）',
  `weekday` int(11) NOT NULL COMMENT '星期几（1代表星期一 2，3，4，5，6，7）',
  `is_default` tinyint(2) NOT NULL COMMENT '是否针对于星期几的默认（1：是，2：否）',
  `is_online` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否这一天在线（1：是，2：否）',
  `addtime` bigint(20) NOT NULL,
  `updatetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='匹配不同角色不同星期的模板关联（驾校，教练，嘻哈...）';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
