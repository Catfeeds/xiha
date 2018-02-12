-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-06-13 09:30:12
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `xihaxueche`
--

-- --------------------------------------------------------

--
-- 表的结构 `cs_coach_users`
--

CREATE TABLE IF NOT EXISTS `cs_coach_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(20) DEFAULT NULL COMMENT '学员姓名',
  `user_phone` varchar(20) NOT NULL COMMENT '学员号码',
  `user_photo` varchar(50) NOT NULL COMMENT '学员头像',
  `household_property` varchar(20) DEFAULT ' ' COMMENT '户籍性质（本地 外地）',
  `i_stage` int(3) DEFAULT '1' COMMENT '学员所处阶段（1 待定，2 科目二，3 科目三，4 毕业）',
  `identity_id` varchar(20) NOT NULL COMMENT '身份证ID',
  `user_property` varchar(20) NOT NULL COMMENT '学员性质',
  `signup_school_name` varchar(32) DEFAULT NULL COMMENT '报考驾校名称',
  `signup_school_id` int(10) unsigned DEFAULT '0' COMMENT '报考驾校ID',
  `user_address` varchar(100) DEFAULT ' ' COMMENT '学员地址',
  `lesson2_learn_times` int(10) unsigned DEFAULT '0' COMMENT '科目二学时',
  `lesson3_learn_times` int(10) unsigned DEFAULT '0' COMMENT '科目三学时',
  `lesson1_exam_times` int(10) unsigned DEFAULT '0' COMMENT '科目一考试（补考）次数',
  `lesson2_exam_times` int(10) unsigned DEFAULT '0' COMMENT '科目二考试（补考）次数',
  `lesson3_exam_times` int(10) unsigned DEFAULT '0' COMMENT '科目三考试（补考）次数',
  `lesson4_exam_times` int(10) unsigned DEFAULT '0' COMMENT '科目四考试（补考）次数',
  `addtime` bigint(14) NOT NULL COMMENT '添加时间',
  `updatetime` bigint(14) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_phone` (`user_phone`,`i_stage`,`identity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='教练学员表' AUTO_INCREMENT=11 ;

--
-- 转存表中的数据 `cs_coach_users`
--

INSERT INTO `cs_coach_users` (`id`, `user_name`, `user_phone`, `user_photo`, `household_property`, `i_stage`, `identity_id`, `user_property`, `signup_school_name`, `signup_school_id`, `user_address`, `lesson2_learn_times`, `lesson3_learn_times`, `lesson1_exam_times`, `lesson2_exam_times`, `lesson3_exam_times`, `lesson4_exam_times`, `addtime`, `updatetime`) VALUES
(1, '陈曦', '18656999023', '', '外地', 1, '342423199104106297', '学生', '八一驾校', 1, ' 安徽省合肥市', 2, 3, 1, 1, 0, 0, 1455499715, 0),
(3, '高大成', '18656999022', '', '外地', 1, '342423199104106293', '学生', '八一驾校', 1, ' 安徽省合肥市', 2, 3, 1, 1, 0, 0, 1454139032, 0),
(4, '陈曦1', '18656999021', '', '外地', 1, '342423199104106292', '学生', '八一驾校', 1, ' 安徽省合肥市', 2, 3, 1, 1, 0, 0, 1454138411, 0),
(5, '陈曦2', '18656999025', '', '本地', 1, '342423199104106298', '社招', '八一驾校', 1, ' 安徽省合肥市', 2, 3, 1, 1, 0, 0, 1454138201, 0),
(8, '张三', '12345678901', '', '', 1, '', '', '', 0, '', 0, 0, 0, 0, 0, 0, 1464660505, 1464660505),
(9, '张三1', '12345678902', '', '', 1, '', '', '', 0, '', 0, 0, 0, 0, 0, 0, 1464661117, 1464661117),
(10, '陈曦2', '18656999125', '', '', 1, '', '', '', 0, '', 0, 0, 0, 0, 0, 0, 1464661359, 1464661359);

-- --------------------------------------------------------

--
-- 表的结构 `cs_coach_users_records`
--

CREATE TABLE IF NOT EXISTS `cs_coach_users_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `coach_users_id` int(11) NOT NULL COMMENT '关联的用户ID',
  `user_name` varchar(20) DEFAULT ' ' COMMENT '用户名',
  `coach_id` int(10) unsigned NOT NULL COMMENT '教练ID',
  `user_phone` varchar(20) NOT NULL COMMENT '学员手机号码',
  `start_time` decimal(4,2) DEFAULT '0.00' COMMENT '开始时间',
  `end_time` decimal(4,2) DEFAULT '0.00' COMMENT '结束世间',
  `year` int(10) unsigned DEFAULT '0' COMMENT '年',
  `month` int(10) unsigned DEFAULT '0' COMMENT '月',
  `day` int(10) unsigned DEFAULT '0' COMMENT '日',
  `timestamp` bigint(14) unsigned DEFAULT '0' COMMENT '年月日时间戳',
  `i_stage` int(3) NOT NULL COMMENT '学员所处阶段（1 待定，2 科目二，3 科目三，4 毕业）',
  `identity_id` varchar(20) DEFAULT '0' COMMENT '身份证',
  `i_status` int(11) NOT NULL DEFAULT '1' COMMENT '学员状态（1：待定 1001：休息中 1002：练车中 1003：考试中 4：毕业）',
  `lesson2_learn_times` int(10) unsigned DEFAULT '0' COMMENT '科目二学时',
  `lesson3_learn_times` int(10) unsigned DEFAULT '0' COMMENT '科目三学时',
  `lesson1_exam_times` int(10) unsigned DEFAULT '0' COMMENT '科目一考试（补考）次数',
  `lesson2_exam_times` int(10) unsigned DEFAULT '0' COMMENT '科目一考试（补考）次数',
  `lesson3_exam_times` int(10) unsigned DEFAULT '0' COMMENT '科目三考试（补考）次数',
  `lesson4_exam_times` int(10) unsigned DEFAULT '0' COMMENT '科目四考试（补考）次数',
  `is_bind` int(11) NOT NULL DEFAULT '1' COMMENT '是否绑定教练（1：绑定 2：不绑定）场景是学员主动与教练绑定时',
  `addtime` bigint(14) NOT NULL,
  `updatetime` bigint(14) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `coach_id` (`coach_id`),
  KEY `coach_users_id` (`coach_users_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='学员考试练车记录表' AUTO_INCREMENT=9 ;

--
-- 转存表中的数据 `cs_coach_users_records`
--

INSERT INTO `cs_coach_users_records` (`id`, `coach_users_id`, `user_name`, `coach_id`, `user_phone`, `start_time`, `end_time`, `year`, `month`, `day`, `timestamp`, `i_stage`, `identity_id`, `i_status`, `lesson2_learn_times`, `lesson3_learn_times`, `lesson1_exam_times`, `lesson2_exam_times`, `lesson3_exam_times`, `lesson4_exam_times`, `is_bind`, `addtime`, `updatetime`) VALUES
(1, 1, 'chenxi1', 26, '18656999031', '8.00', '9.00', 2016, 7, 8, 1467907200, 3, '342423199104106297', 1002, 0, 0, 0, 0, 0, 0, 1, 0, 1465373079),
(2, 3, ' ', 26, '18656999022', '8.00', '9.00', 2016, 5, 20, 1463673600, 2, '0', 1, 0, 0, 0, 0, 0, 0, 1, 0, 0),
(3, 5, ' ', 26, '18656999025', '8.00', '9.00', 2016, 5, 21, 1463760000, 3, '0', 1, 0, 0, 0, 0, 0, 0, 1, 0, 0),
(4, 4, ' ', 26, '18656999021', '8.00', '9.00', 2016, 5, 22, 1463846400, 2, '0', 1, 0, 0, 0, 0, 0, 0, 1, 0, 0),
(6, 8, ' ', 26, '12345678901', '0.00', '0.00', 2016, 5, 31, 1464624000, 1, '0', 1, 0, 0, 0, 0, 0, 0, 1, 1464660644, 0),
(7, 9, ' ', 26, '12345678902', '0.00', '0.00', 2016, 5, 31, 1464624000, 1, '0', 1, 0, 0, 0, 0, 0, 0, 1, 1464661315, 0),
(8, 10, ' ', 26, '18656999125', '0.00', '0.00', 2016, 5, 31, 1464624000, 1, '0', 1, 0, 0, 0, 0, 0, 0, 1, 1464661359, 0);

-- --------------------------------------------------------

--
-- 表的结构 `cs_coach_users_relation`
--

CREATE TABLE IF NOT EXISTS `cs_coach_users_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `coach_users_id` int(11) NOT NULL,
  `coach_id` int(11) DEFAULT '0' COMMENT '教练ID（教练与学员多对多关系）',
  PRIMARY KEY (`id`),
  KEY `coach_users_id` (`coach_users_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='教练关联学员与注册学员关联表' AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `cs_coach_users_relation`
--

INSERT INTO `cs_coach_users_relation` (`id`, `user_id`, `coach_users_id`, `coach_id`) VALUES
(1, 1, 1, 26),
(2, 1, 3, 26),
(3, 1, 3, 109);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
