-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-08-23 04:39:23
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `xiha`
--

-- --------------------------------------------------------

--
-- 表的结构 `cs_menu`
--

CREATE TABLE IF NOT EXISTS `cs_menu` (
  `moduleid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `m_applicationid` int(11) NOT NULL COMMENT '应用ID可以实现不同管理系统公用一个菜单表',
  `m_parentid` int(11) NOT NULL COMMENT '菜单父级ID-对于的值未菜单ID 如果是一级菜单设置为0',
  `m_pagecode` varchar(6) NOT NULL COMMENT '菜单排序字段',
  `m_controller` varchar(50) DEFAULT NULL COMMENT '控制器url',
  `m_type` int(11) NOT NULL COMMENT '菜单类型（1：模块 2：操作{增删改查等}）',
  `m_cname` varchar(50) NOT NULL COMMENT '菜单中文名',
  `m_directory` varchar(255) NOT NULL COMMENT '菜单对于的url',
  `m_imageurl` varchar(255) NOT NULL COMMENT '菜单栏显示的图片路径',
  `m_close` int(4) NOT NULL COMMENT '是否开放（1：开放 2：不开放）',
  PRIMARY KEY (`moduleid`),
  UNIQUE KEY `cs_menu` (`m_applicationid`,`m_pagecode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- 转存表中的数据 `cs_menu`
--

INSERT INTO `cs_menu` (`moduleid`, `m_applicationid`, `m_parentid`, `m_pagecode`, `m_controller`, `m_type`, `m_cname`, `m_directory`, `m_imageurl`, `m_close`) VALUES
(1, 1, 0, '029851', 'manager/addMenu', 1, '评价管理 ', '评价管理菜单描述', 'upload/menu/20160407/menu_5706499b965b6.png', 1),
(2, 1, 1, '029901', 'manager/addMenu', 1, '学员评价教练', '评价管理菜单描述', 'upload/menu/20160407/menu_570649cddddc3.png', 1),
(3, 1, 1, '029921', 'manager/addMenu', 1, '学员评价驾校', '评价管理菜单描述', 'upload/menu/20160407/menu_570649e12d209.png', 1),
(4, 1, 1, '029942', 'manager/addMenu', 1, '教练评价学员', '评价管理菜单描述', 'upload/menu/20160407/menu_570649f65d84e.jpg', 1),
(5, 1, 2, '029982', 'manager/addMenu', 2, '新增', '评价管理菜单描述', 'upload/menu/20160407/menu_57064a1e1f15f.jpg', 1),
(6, 1, 2, '030021', 'manager/addMenu', 2, '删除', '评价管理菜单描述', 'upload/menu/20160407/menu_57064a45dae5a.jpg', 1),
(7, 1, 2, '030037', 'manager/addMenu', 2, '修改', '评价管理菜单描述', 'upload/menu/20160407/menu_57064a55e2337.jpg', 1),
(9, 1, 3, '030151', 'manager/addMenu', 2, '新增', '评价管理菜单描述', 'upload/menu/20160407/menu_57064ac758617.JPG', 1),
(10, 1, 3, '030162', 'manager/addMenu', 2, '删除', '评价管理菜单描述', 'upload/menu/20160407/menu_57064ad208b21.JPG', 1),
(11, 1, 3, '030183', 'manager/addMenu', 2, '修改', '评价管理菜单描述', 'upload/menu/20160407/menu_57064ae76e502.jpg', 1),
(13, 1, 4, '030219', 'manager/addMenu', 2, '新增', '评价管理菜单描述', 'upload/menu/20160407/menu_57064b0b2f947.png', 1),
(14, 1, 4, '030230', 'manager/addMenu', 2, '删除', '评价管理菜单描述', 'upload/menu/20160407/menu_57064b160966b.jpg', 1),
(15, 1, 4, '030241', 'manager/addMenu', 2, '修改', '评价管理菜单描述', 'upload/menu/20160407/menu_57064b2110e12.png', 1),
(16, 1, 0, '097885', 'manager/addMenu', 1, '学员管理', '学员管理菜单秒杀', 'upload/menu/20160408/menu_5707535dac584.jpg', 1),
(17, 1, 16, '101969', 'manager/addMenu', 1, '学员列表', '学员列表详细描述', 'upload/menu/20160408/menu_5707635174308.jpg', 1),
(18, 1, 17, '101984', 'manager/addMenu', 2, '新增', '学员列表详细描述', 'upload/menu/20160408/menu_57076360a9de6.jpg', 1),
(19, 1, 17, '114423', 'manager/addMenu', 2, '删除', '学员列表详细描述', 'upload/menu/20160408/menu_570793f702cce.JPG', 1),
(20, 1, 17, '114439', 'manager/addMenu', 2, '修改', '评价管理菜单描述', 'upload/menu/20160408/menu_570794075b29e.jpg', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
