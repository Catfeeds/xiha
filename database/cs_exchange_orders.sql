-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-09-07 06:04:11
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
-- 表的结构 `cs_exchange_orders`
--

DROP TABLE IF EXISTS `cs_exchange_orders`;
CREATE TABLE IF NOT EXISTS `cs_exchange_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_name` varchar(200) NOT NULL COMMENT '商品名称',
  `mch_name` varchar(200) NOT NULL COMMENT '商户名称',
  `goods_original_price` decimal(10,2) NOT NULL COMMENT '商品原来金币价格',
  `goods_final_price` decimal(10,2) NOT NULL COMMENT '商品最终金币价格',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `exchange_no` varchar(20) NOT NULL COMMENT '兑换订单号',
  `pay_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '支付状态（1、金币支付 1、支付成功 2、支付失败）',
  `exchange_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '兑换状态 （1、兑换成功，2、正在发货中，3、未抽中奖品，4、兑换失败）',
  `exchange_num` int(11) NOT NULL COMMENT '兑换数量',
  `addtime` bigint(20) NOT NULL COMMENT '兑换时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=' 兑换记录表';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
