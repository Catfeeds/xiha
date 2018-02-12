-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-09-07 03:52:29
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
-- 表的结构 `cs_coupon`
--

DROP TABLE IF EXISTS `cs_coupon`;
CREATE TABLE IF NOT EXISTS `cs_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_type` int(2) NOT NULL DEFAULT '1' COMMENT '角色类别（1：教练，2：驾校）',
  `owner_id` int(11) NOT NULL COMMENT '券的所有者id',
  `owner_name` varchar(32) NOT NULL COMMENT '驾校名称或者教练名称',
  `coupon_total_num` int(10) UNSIGNED NOT NULL COMMENT '券的总数量',
  `coupon_get_num` int(10) UNSIGNED NOT NULL COMMENT '券被领取数目',
  `addtime` bigint(20) NOT NULL COMMENT '添加时间',
  `expiretime` bigint(20) NOT NULL COMMENT '过期时间',
  `coupon_value` varchar(32) NOT NULL COMMENT '优惠面值（100元，95折...）',
  `coupon_category` int(2) NOT NULL COMMENT '券的种类（1、现金券，2、打折券...）',
  `coupon_code` varchar(32) NOT NULL COMMENT '兑换码',
  `coupon_limit_num` int(11) NOT NULL COMMENT '领取数量限制',
  `province_id` int(11) NOT NULL DEFAULT '0' COMMENT '如果是全国的话 默认值就是0',
  `city_id` int(11) NOT NULL DEFAULT '0',
  `area_id` int(11) NOT NULL DEFAULT '0',
  `order` bigint(20) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='优惠券表（用于不同角色所设置的券， 优惠券，学车券，活动券）';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
