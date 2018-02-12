-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-09-07 05:31:36
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
-- 表的结构 `cs_transaction_records`
--

DROP TABLE IF EXISTS `cs_transaction_records`;
CREATE TABLE IF NOT EXISTS `cs_transaction_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_money` varchar(200) NOT NULL DEFAULT '0.00',
  `transaction_no` varchar(200) NOT NULL COMMENT '交易单号',
  `transaction_body` varchar(200) NOT NULL COMMENT '商品名称',
  `transaction_detail` varchar(200) NOT NULL COMMENT '商品详情',
  `transaction_mch_name` varchar(200) NOT NULL COMMENT '交易商户名称(如：从安徽嘻哈网络科技有限公司提现的)',
  `transaction_receiver_no` varchar(200) NOT NULL COMMENT '收款账户',
  `transaction_receiver_name` varchar(200) NOT NULL COMMENT '收款名称',
  `transaction_status` int(2) NOT NULL COMMENT '交易状态',
  `transaction_starttime` bigint(20) NOT NULL COMMENT '交易开始时间',
  `transaction_endtime` bigint(20) NOT NULL COMMENT '交易结束时间',
  `transaction_pay_type` int(2) NOT NULL COMMENT '支付方式',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='交易记录表';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
