-- phpMyAdmin SQL Dump
-- version 4.4.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 18, 2016 at 09:32 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ghzsk`
--

-- --------------------------------------------------------

--
-- Table structure for table `gh_category`
--

CREATE TABLE IF NOT EXISTS `gh_category` (
  `id` int(11) unsigned NOT NULL,
  `cate_name` varchar(100) NOT NULL DEFAULT '' COMMENT '分类名',
  `slug` varchar(100) NOT NULL DEFAULT '' COMMENT '分类别名，英文表示',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父分类id',
  `cate_desc` varchar(500) NOT NULL DEFAULT '' COMMENT '分类描述',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gh_category`
--

INSERT INTO `gh_category` (`id`, `cate_name`, `slug`, `parent_id`, `cate_desc`, `created`) VALUES
(3, '考试系统中心', 'examsystemcenter', 0, '考试系统中心', 1471430860),
(4, '新考试系统车载', 'newexamsystemload', 0, '新考试系统车载', 1471430860),
(5, '电子教练', 'ecoach', 0, '电子教练', 1471430860),
(6, '旧考试系统车载', 'oldexamsystemload', 0, '旧考试系统车载', 1471430860),
(7, '模拟考试系统', 'emulatedexamsystem', 0, '模拟考试系统', 1471430860),
(8, '三维地图', '3dmap', 0, '三维地图', 1471430860),
(9, '星网宇达GPS', 'xwydgps', 0, '星网宇达GPS', 1471430860),
(10, '司南GPS', 'sinangps', 0, '司南GPS', 1471430860),
(11, '天宝GPS', 'tianbaogps', 0, '天宝GPS', 1471430860),
(12, '天际通（硕德网络）', 'tianjitong', 0, '天际通（硕德网络）', 1471430860),
(13, '斯普莱网络', 'splnetwork', 0, '斯普莱网络', 1471430860),
(14, '4G路由器', '4grouter', 0, '4G路由器', 1471430860),
(15, '视频监控', 'videomonitor', 0, '视频监控', 1471430860),
(16, '车辆模型', 'carmodel', 0, '车辆模型', 1471430860),
(17, '车辆信号', 'carsignal', 0, '车辆信号', 1471430860);

-- --------------------------------------------------------

--
-- Table structure for table `gh_group`
--

CREATE TABLE IF NOT EXISTS `gh_group` (
  `id` int(11) unsigned NOT NULL COMMENT '组id',
  `group_name` varchar(100) NOT NULL COMMENT '组名',
  `group_comment` varchar(500) NOT NULL DEFAULT '' COMMENT '组注解，说明此组具有的权限',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户的所属组权限模型表';

--
-- Dumping data for table `gh_group`
--

INSERT INTO `gh_group` (`id`, `group_name`, `group_comment`, `add_time`) VALUES
(1, '管理员', '系统用户，超级权限', 1471225825),
(2, '普通用户', '管理方案', 1471225695);

-- --------------------------------------------------------

--
-- Table structure for table `gh_post`
--

CREATE TABLE IF NOT EXISTS `gh_post` (
  `id` int(11) unsigned NOT NULL,
  `author` varchar(100) NOT NULL DEFAULT '' COMMENT '作者名',
  `author_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '作者id',
  `title` varchar(300) NOT NULL DEFAULT '' COMMENT '主题，标题',
  `short_desc` varchar(1000) NOT NULL DEFAULT '' COMMENT '摘要',
  `content` text COMMENT '主体内容',
  `created` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `attachment` varchar(200) NOT NULL DEFAULT '' COMMENT '附件',
  `cate_id` int(11) unsigned DEFAULT NULL COMMENT '分类id',
  `system_type` int(11) unsigned NOT NULL COMMENT '1 旧系统 2 新系统'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gh_post`
--

INSERT INTO `gh_post` (`id`, `author`, `author_id`, `title`, `short_desc`, `content`, `created`, `modified`, `attachment`, `cate_id`, `system_type`) VALUES
(2, '超级管理员2', 11, '业余时间多充电！', '', '&lt;p&gt;CAD绘图知识多学习点&lt;/p&gt;&lt;p&gt;CAD绘图用不到，主要是在mapinfo里面编辑科目三的地图。&lt;/p&gt;&lt;p&gt;用CadSurvey只是用来将打点的txt文件转换成.dwf格式的文件；为了下一步制作三维电子地图 和 科目三考试系统地图区域可编辑的.tab文件的一个中间临时文件。&lt;/p&gt;', 1471317852, 1471319937, '../uploadGhzsk/zsk/2/20160816/zsk_57b28f55a1e8a.jpg', 0, 1),
(3, '梅彬', 11, 'YYSJ多学习', '', '&lt;p&gt;业余时间多看点数据结构的书，这样才能编出惊天地泣鬼神的程序出来。&lt;/p&gt;', 1471319489, 1471419972, '../upload/Ghzsk/zsk/3/20160817/zsk_57b41644b11af.png', 0, 2),
(4, '王浩', 13, '打点软件使用说明（南瓜头）', '', '&lt;p&gt;&lt;span style=&quot;font-family:;&quot;&gt;&amp;nbsp;关于南瓜头软件的大致说明：&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;font-family:;&quot;&gt;&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;/p&gt;', 1471433197, 1471433547, '../upload/Ghzsk/zsk/4/20160817/zsk_57b44b4b55a7f.pdf', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `gh_secretkey`
--

CREATE TABLE IF NOT EXISTS `gh_secretkey` (
  `id` int(11) NOT NULL,
  `applicant` varchar(15) NOT NULL DEFAULT '' COMMENT '密钥申请人，手动输入',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '申请时间',
  `system_type` int(2) NOT NULL DEFAULT '1' COMMENT '系统类型 1 新系统 2 旧系统',
  `school_name` varchar(200) NOT NULL DEFAULT '' COMMENT '驾校名称',
  `school_address` varchar(500) DEFAULT '' COMMENT '驾校地址',
  `school_phone` varchar(50) NOT NULL DEFAULT '' COMMENT '驾校联系电话',
  `machine_code` varchar(100) NOT NULL DEFAULT '' COMMENT '机器码，数字和字母',
  `register_code` varchar(200) NOT NULL DEFAULT '' COMMENT '注册码',
  `register_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `expire_time` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
  `register_type` int(3) NOT NULL DEFAULT '1' COMMENT '注册类型 1 车载 2 调度中心 3 接口 4 管理 软件'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='车载注册';

--
-- Dumping data for table `gh_secretkey`
--

INSERT INTO `gh_secretkey` (`id`, `applicant`, `addtime`, `system_type`, `school_name`, `school_address`, `school_phone`, `machine_code`, `register_code`, `register_time`, `expire_time`, `register_type`) VALUES
(1, '李婷婷', 2016, 1, '宿州安通驾校', '未知', '346456456', 'dgdfgdgdsgf', 'gdfsgsgsdg', 2016, 2017, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gh_user`
--

CREATE TABLE IF NOT EXISTS `gh_user` (
  `id` int(11) unsigned NOT NULL COMMENT '用户id',
  `user_account` varchar(100) NOT NULL COMMENT '登陆账号，默认用手机号',
  `user_password` varchar(32) NOT NULL COMMENT '密码，最少6字符，最长20字符',
  `user_name` varchar(100) NOT NULL DEFAULT '' COMMENT '用户名，展示，相当于昵称',
  `user_phone` varchar(30) NOT NULL DEFAULT '' COMMENT '手机号，作为账号登陆',
  `group_id` int(11) unsigned DEFAULT NULL COMMENT '用户组id，相当于权限组',
  `first_login` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '首次登陆时间',
  `last_login` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最近登陆时间',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gh_user`
--

INSERT INTO `gh_user` (`id`, `user_account`, `user_password`, `user_name`, `user_phone`, `group_id`, `first_login`, `last_login`, `add_time`) VALUES
(1, '13955174065', '4d56ac4f4ffabefbf7013c10146f8d11', '徐竹青', '13955174065', 1, 0, 0, 1471311344),
(9, '18656451828', '4d56ac4f4ffabefbf7013c10146f8d11', '超级管理员2', '18656451828', 1, 0, 0, 1471316272),
(10, '18656639679', '4d56ac4f4ffabefbf7013c10146f8d11', '孟良', '18656639679', 1, 0, 0, 1471318265),
(11, 'meibin', 'b20f7703718b9a230340a6aabae9422c', '梅彬', '18055666807', 2, 0, 0, 1471318693),
(12, '13866727233', '21218cca77804d2ba1922c33e0151105', '张总', '13866727233', 1, 0, 0, 1471318833),
(13, 'wanghao1514', '4d56ac4f4ffabefbf7013c10146f8d11', '王浩', '17712113665', 1, 0, 0, 1471353335),
(14, 'chenmaoyu', 'e807f1fcf82d132f9bb018ca6738a19f', '陈毛宇', '15555411990', 2, 0, 0, 1471353289),
(15, '1163335089', '1eb5f0fe678e4b27ab19f770a202355a', '李森', '18356533108', 2, 0, 0, 1471353508),
(16, 'wangling', 'b20f7703718b9a230340a6aabae9422c', '王琳', '18756004209', 2, 0, 0, 1471399947),
(17, '13485715990', '4d56ac4f4ffabefbf7013c10146f8d11', '鲍庆翠', '13485715990', 1, 0, 0, 1471401328);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gh_category`
--
ALTER TABLE `gh_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gh_group`
--
ALTER TABLE `gh_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gh_post`
--
ALTER TABLE `gh_post`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gh_secretkey`
--
ALTER TABLE `gh_secretkey`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gh_user`
--
ALTER TABLE `gh_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `user_phone` (`user_phone`),
  ADD KEY `user_account` (`user_account`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gh_category`
--
ALTER TABLE `gh_category`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `gh_group`
--
ALTER TABLE `gh_group`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '组id',AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `gh_post`
--
ALTER TABLE `gh_post`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `gh_secretkey`
--
ALTER TABLE `gh_secretkey`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `gh_user`
--
ALTER TABLE `gh_user`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',AUTO_INCREMENT=19;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
