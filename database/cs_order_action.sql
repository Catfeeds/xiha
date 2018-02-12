-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2017-04-28 10:37:59
-- 服务器版本： 5.7.14
-- PHP Version: 5.6.25

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
-- 表的结构 `cs_order_action`
--

CREATE TABLE `cs_order_action` (
  `action_id` mediumint(8) UNSIGNED NOT NULL,
  `order_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `order_no` varchar(32) NOT NULL COMMENT '订单号',
  `order_type` tinyint(2) NOT NULL COMMENT '订单类型 1：报名班制 2：预约计时',
  `action_user` varchar(30) NOT NULL DEFAULT '' COMMENT '操作员名称',
  `order_status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '订单状态',
  `pay_status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '支付状态',
  `action_note` varchar(255) NOT NULL DEFAULT '' COMMENT '操作日志',
  `addtime` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单操作表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cs_order_action`
--
ALTER TABLE `cs_order_action`
  ADD PRIMARY KEY (`action_id`),
  ADD KEY `order_id` (`order_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `cs_order_action`
--
ALTER TABLE `cs_order_action`
  MODIFY `action_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
