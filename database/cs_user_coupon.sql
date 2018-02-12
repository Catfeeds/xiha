-- phpMyAdmin SQL Dump
-- version 4.5.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-12-18 16:12:28
-- 服务器版本： 5.6.28-log
-- PHP Version: 5.6.19

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
-- 表的结构 `cs_user_coupon`
--

CREATE TABLE `cs_user_coupon` (
  `id` int(11) NOT NULL,
  `user_name` varchar(15) NOT NULL COMMENT '领取人的姓名',
  `user_phone` bigint(20) NOT NULL COMMENT '领取人的手机号码',
  `coupon_name` varchar(32) NOT NULL COMMENT '标题',
  `coupon_desc` varchar(64) NOT NULL COMMENT '描述',
  `coupon_code` varchar(32) NOT NULL COMMENT '兑换码',
  `coupon_value` varchar(32) NOT NULL COMMENT '优惠面值',
  `coupon_category_id` int(10) NOT NULL COMMENT '优惠券的类别（1：现金券，2：打折券等等）',
  `coupon_sender_owner_id` int(11) NOT NULL COMMENT '发放学车券的角色（教练，嘻哈自营，汽车厂家...）',
  `coupon_sender_owner_type` int(11) NOT NULL COMMENT '角色类别（1：教练，2：驾校，3：嘻哈）',
  `coupon_status` int(2) NOT NULL DEFAULT '1' COMMENT '学车券状态1： 未使用，2： 已使用，:3：已过期 ，4：已删除',
  `coupon_type` int(2) NOT NULL DEFAULT '1' COMMENT '用于区别自己领取还是系统推送的， 后台设置随机发放，学员自己领取（1：自己领取，2：系统推送）',
  `province_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `city_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `area_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `coupon_scope` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `addtime` bigint(20) NOT NULL COMMENT '领取时间',
  `expiretime` bigint(20) NOT NULL COMMENT '过期时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学车领取券表';

--
-- 转存表中的数据 `cs_user_coupon`
--

INSERT INTO `cs_user_coupon` (`id`, `user_name`, `user_phone`, `coupon_name`, `coupon_desc`, `coupon_code`, `coupon_value`, `coupon_category_id`, `coupon_sender_owner_id`, `coupon_sender_owner_type`, `coupon_status`, `coupon_type`, `province_id`, `city_id`, `area_id`, `coupon_scope`, `addtime`, `expiretime`) VALUES
(1, '嘻哈学员9023', 18656999023, '学车优惠券', '学车优惠券', 'abc123', '100', 1, 26, 1, 1, 1, 340000, 340100, 0, 2, 1479434786, 12312312312),
(2, '嘻哈学员7615', 18756907615, '学车优惠券', '学车优惠券', 'abc123', '100', 1, 26, 1, 1, 1, 340000, 340100, 0, 2, 1479440226, 12312312312),
(4, '嘻哈学员', 18756907614, '学车优惠券', '学车优惠券', 'abc123', '100', 1, 26, 1, 1, 1, 340100, 340100, 0, 2, 1479697202, 12312312312),
(5, '嘻哈学员7614', 18756907614, '学车优惠券', '学车优惠券', 'abc123', '100', 1, 26, 1, 1, 1, 340000, 340100, 0, 2, 1479715523, 12312312312),
(6, 'tttt', 18655132037, '学车优惠券', '学车优惠券', 'abc123', '100', 1, 26, 1, 1, 1, 340000, 340100, 0, 2, 1480644674, 12312312312),
(7, 'tttt', 18655132037, '学车优惠券', '学车优惠券', 'abc123', '100', 1, 26, 1, 1, 1, 340000, 340100, 0, 2, 1480650367, 12312312312),
(8, '嘻哈学员7225', 18326897225, '学车优惠券', '学车优惠券', 'abc123', '100', 1, 26, 1, 1, 1, 340000, 340100, 0, 2, 1480661783, 12312312312),
(9, '小宇(测学)', 17355100855, '学车优惠券', '学车优惠券', 'abc123', '100', 1, 26, 1, 1, 1, 340000, 340100, 0, 2, 1480665647, 12312312312),
(10, '小宇(测学)', 17355100855, '学车优惠券', '学车优惠券', 'abc123', '100', 1, 26, 1, 1, 1, 340000, 340100, 0, 2, 1481082142, 12312312312),
(11, '王玲', 18756004209, '学车优惠券', '学车优惠券', 'abc123', '100', 1, 26, 1, 1, 1, 0, 340100, 0, 2, 1481879507, 12312312312),
(12, '王玲', 18756004209, '学车优惠券', '学车优惠券', 'abc123', '100', 1, 26, 1, 1, 1, 0, 340100, 0, 2, 1481879517, 12312312312);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cs_user_coupon`
--
ALTER TABLE `cs_user_coupon`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `cs_user_coupon`
--
ALTER TABLE `cs_user_coupon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
