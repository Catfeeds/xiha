-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2017-05-09 07:28:07
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
-- 表的结构 `cs_school_account`
--

CREATE TABLE `cs_school_account` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `account_user_name` varchar(32) NOT NULL COMMENT '账户名字（银行开户名）',
  `bank_name` varchar(64) NOT NULL COMMENT '银行名称（中国银行...）',
  `account_no` varchar(32) NOT NULL COMMENT '卡号',
  `account_phone` varchar(14) NOT NULL COMMENT '银行预留手机号',
  `account_identifyId` varchar(20) NOT NULL COMMENT '银行预留的身份证信息',
  `is_default` tinyint(2) NOT NULL COMMENT '是否默认',
  `addtime` bigint(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='驾校银行账号';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cs_school_account`
--
ALTER TABLE `cs_school_account`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `cs_school_account`
--
ALTER TABLE `cs_school_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
