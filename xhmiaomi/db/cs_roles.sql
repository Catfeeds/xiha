-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2017-04-01 06:11:32
-- 服务器版本： 5.7.14
-- PHP Version: 7.0.10

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
-- 表的结构 `cs_roles`
--

CREATE TABLE `cs_roles` (
  `l_role_id` int(11) NOT NULL,
  `s_rolename` varchar(100) NOT NULL,
  `s_description` varchar(100) NOT NULL,
  `owner_id` int(11) NOT NULL COMMENT '后台管理者Id',
  `owner_type` int(11) NOT NULL COMMENT '管理者类型（1：驾校 2：代理商 3：嘻哈）'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='角色配置表';

--
-- 转存表中的数据 `cs_roles`
--

INSERT INTO `cs_roles` (`l_role_id`, `s_rolename`, `s_description`, `owner_id`, `owner_type`) VALUES
(1, '嘻哈超级管理员', '最高等级的管理员，任何操作请慎重', 0, 1),
(2, '驾校管理', '驾校综合管理', 0, 1),
(5, '嘻哈信息管理员', '信息管理与维护', 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cs_roles`
--
ALTER TABLE `cs_roles`
  ADD PRIMARY KEY (`l_role_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `cs_roles`
--
ALTER TABLE `cs_roles`
  MODIFY `l_role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
