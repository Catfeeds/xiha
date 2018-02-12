-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-09-07 05:15:32
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
-- 表的结构 `cs_pay_account_config`
--

DROP TABLE IF EXISTS `cs_pay_account_config`;
CREATE TABLE IF NOT EXISTS `cs_pay_account_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_name` varchar(200) NOT NULL COMMENT '账户名称 包括支付宝，微信等 不限于银行账户',
  `account_slug` varchar(200) NOT NULL COMMENT '英文名',
  `account_description` varchar(255) NOT NULL COMMENT '账户说明',
  `is_open` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否开启（1：是，2：否）',
  `is_bank` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否是银行账号（1：是，2：否）',
  `order` bigint(20) NOT NULL COMMENT '排序',
  `addtime` bigint(20) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='账户支持配置表';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
