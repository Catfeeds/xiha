-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-09-07 08:23:29
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
-- 表的结构 `cs_coach_shifts`
--

DROP TABLE IF EXISTS `cs_coach_shifts`;
CREATE TABLE IF NOT EXISTS `cs_coach_shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coach_id` int(11) NOT NULL,
  `sh_title` varchar(200) NOT NULL,
  `sh_money` decimal(6,2) NOT NULL,
  `sh_original_money` decimal(10,2) NOT NULL,
  `sh_type` int(11) NOT NULL,
  `sh_tag` varchar(200) NOT NULL,
  `sh_tag_id` int(11) NOT NULL,
  `sh_description` varchar(200) NOT NULL COMMENT '描述(如：最快7天上车，4人/车 自行前往)',
  `is_promote` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否促销（1：是，2：否）',
  `coupon_id` int(11) NOT NULL,
  `sh_license_id` int(11) NOT NULL,
  `sh_license_name` varchar(200) NOT NULL,
  `order` bigint(20) NOT NULL,
  `addtime` bigint(20) NOT NULL DEFAULT '0',
  `updatetime` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='教练班制表（教练自主设置的招生班制表）';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
