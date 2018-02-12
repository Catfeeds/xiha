-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 23, 2017 at 11:25 AM
-- Server version: 5.6.28-log
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
-- Table structure for table `cs_test_account`
--

CREATE TABLE `cs_test_account` (
  `accid` int(11) UNSIGNED NOT NULL,
  `field` char(64) NOT NULL DEFAULT '' COMMENT '字段名',
  `value` char(32) NOT NULL DEFAULT '' COMMENT '存储值',
  `beizhu` char(255) NOT NULL DEFAULT '' COMMENT '备注',
  `addtime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cs_test_account`
--

INSERT INTO `cs_test_account` (`accid`, `field`, `value`, `beizhu`, `addtime`) VALUES
(1, 'stu_phone', '18656999023', '陈曦学员', 0),
(2, 'stu_phone', '17355100855', '高大成学员', 0),
(3, 'stu_phone', '18326605314', '魏姣学员', 0),
(4, 'stu_phone', '13205602383', '梅海龙学员', 0),
(5, 'stu_phone', '18756004209', '王玲学员', 0),
(6, 'stu_phone', '18655132037', '朱清学员', 0),
(7, 'stu_phone', '18655132037', '吴红星学员', 0),
(8, 'stu_phone', '15056032300', '陈蜂学员', 0),
(9, 'stu_phone', '18681801214', '陈蜂学员', 0),
(10, 'stu_phone', '14776768787', '未知', 0),
(11, 'stu_phone', '13285652603', '唐地', 0),
(12, 'stu_phone', '18756907614', '未知', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cs_test_account`
--
ALTER TABLE `cs_test_account`
  ADD PRIMARY KEY (`accid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cs_test_account`
--
ALTER TABLE `cs_test_account`
  MODIFY `accid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
