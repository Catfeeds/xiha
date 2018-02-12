-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2017-01-06 05:46:52
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
-- 表的结构 `cs_action`
--

DROP TABLE IF EXISTS `cs_action`;
CREATE TABLE IF NOT EXISTS `cs_action` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '行为唯一标识',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '行为说明',
  `remark` char(140) NOT NULL DEFAULT '' COMMENT '行为描述',
  `rule` text NOT NULL COMMENT '行为规则',
  `log` text NOT NULL COMMENT '日志规则',
  `type` tinyint(2) UNSIGNED NOT NULL DEFAULT '1' COMMENT '类型',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `add_time` bigint(20) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=159 DEFAULT CHARSET=utf8 COMMENT='系统行为表' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `cs_action`
--

INSERT INTO `cs_action` (`id`, `name`, `title`, `remark`, `rule`, `log`, `type`, `status`, `add_time`, `update_time`) VALUES
(1, 'user_login', '用户登录', '积分+10，每天一次', 'table:member|field:score|condition:uid={$self} AND status>-1|rule:score+10|cycle:24|max:1;', '[user|getOperator]在[time|time_format]登录了后台', 1, 1, 0, 1387181220),
(2, 'add_article', '发布文章', '积分+5，每天上限5次', 'table:member|field:score|condition:uid={$self}|rule:score+5|cycle:24|max:5', '', 2, 1, 0, 1380173180),
(3, 'review', '评论', '评论积分+1，无限制', 'table:member|field:score|condition:uid={$self}|rule:score+1', '', 2, 1, 0, 1383285646),
(4, 'add_document', '发表文档', '积分+10，每天上限5次', 'table:member|field:score|condition:uid={$self}|rule:score+10|cycle:24|max:5', '[user|get_nickname]在[time|time_format]发表了一篇文章。\n表[model]，记录编号[record]。', 2, 1, 0, 1386139726),
(5, 'add_document_topic', '发表讨论', '积分+5，每天上限10次', 'table:member|field:score|condition:uid={$self}|rule:score+5|cycle:24|max:10', '', 2, 1, 0, 1383285551),
(6, 'update_config', '更新配置', '新增或修改或删除配置', '', '', 1, 1, 0, 1383294988),
(7, 'update_model', '更新模型', '新增或修改模型', '', '', 1, 1, 0, 1383295057),
(8, 'update_attribute', '更新属性', '新增或更新或删除属性', '', '', 1, 1, 0, 1383295963),
(9, 'update_channel', '更新导航', '新增或修改或删除导航', '', '', 1, 1, 0, 1383296301),
(10, 'update_menu', '更新菜单', '新增或修改或删除菜单', '', '', 1, 1, 0, 1383296392),
(11, 'update_category', '更新分类', '新增或修改或删除分类', '', '', 1, 1, 0, 1383296765),
(12, 'del_stucomment_coach', '删除学员评价教练的相关信息', '删除学员评价教练的相关信息属性', '', '[user|getOperator]在[time|time_format]删除学员评价教练的相关信息', 1, 1, 0, 1480504599),
(13, 'del_student', '删除学员及其信息', '删除学员', '', '[user|getOperator]在[time|time_format]删除学员及其相关信息', 1, 1, 1479295235, 1479350063),
(14, 'del_coach_commentstu', '删除教练评价学员的相关信息', '删除教练评价学员的相关信息', '', '[user|getOperator]在[time|time_format]删除了教练评价学员的相关信息', 1, 1, 1479349767, 1480562232),
(15, 'add_manager', '添加管理员', '添加管理员属性', '', '[user|getOperator]在[time|time_format]添加管理员操作', 1, 1, 1479350468, 0),
(16, 'edit_manager', '编辑管理员信息', '编辑管理员信息属性', '', '[user|getOperator]在[time|time_format]编辑管理员信息操作', 1, 1, 1479350640, 1479350719),
(17, 'del_manager', '删除管理员', '删除管理员的属性', '', '[user|getOperator]在[time|time_format]删除管理员的操作', 1, 1, 1479350686, 0),
(19, 'change_menu_order', '改变菜单的排序', '改变菜单的排序属性', '', '[user|getOperator]在[time|time_format]改变菜单的排序状态', 1, 1, 1479350892, 0),
(18, 'set_manager_status', '设置管理员的开启状态', '设置管理员的开启状态的属性', '', '[user|getOperator]在[time|time_format]设置管理员的开启状态', 1, 1, 1479351596, 0),
(21, 'add_menu', '添加新的菜单', '添加新的菜单属性', '', '[user|getOperator]在[time|time_format]添加新的菜单', 1, 1, 1479351770, 0),
(22, 'edit_menu', '编辑菜单信息', '编辑菜单信息属性', '', '[user|getOperator]在[time|time_format]编辑菜单的相关信息', 1, 1, 1479351835, 0),
(23, 'del_menu', '删除菜单及其信息', '删除菜单属性', '', '[user|getOperator]在[time|time_format]删除菜单及其信息', 1, 1, 1479351890, 0),
(24, 'set_menu_status', '菜单开启状态的设置', '设置菜单开启状态的属性', '', '[user|getOperator]在[time|time_format]设置菜单的开启状态', 1, 1, 1479351981, 1479352016),
(25, 'add_system_action', '添加新的系统行为', '添加新的系统行为属性', '', '[user|getOperator]在[time|time_format]添加新的系统行为', 1, 1, 1479352673, 0),
(26, 'edit_system_action', '编辑新的系统行为', '编辑新的系统行为属性', '', '[user|getOperator]在[time|time_format]编辑系统行为', 1, 1, 1479352758, 0),
(27, 'set_action_status', '设置系统行为的状态', '设置系统行为的状态属性', '', '[user|getOperator]在[time|time_format]设置系统行为的状态（开启或关闭）', 1, 1, 1479352910, 0),
(28, 'del_system_action', '删除系统行为（只可单条删除）', '删除系统行为属性', '', '[user|getOperator]在[time|time_format]删除系统行为（只单条删除）', 1, 1, 1479353057, 0),
(29, 'del_system_actions', '批量删除系统行为（可多条删除）', '批量删除系统行为（可多条删除）属性', '', '[user|getOperator]在[time|time_format]批量删除系统行为（可多条删除）属性', 1, 1, 1479353220, 0),
(30, 'recover_system_actions', '批量恢复系统行为', '批量恢复系统行为的属性', '', '[user|getOperator]在[time|time_format]批量恢复系统行为（可多条恢复）属性', 1, 1, 1479353314, 0),
(31, 'set_actionlog_status', '设置行为日志的状态', '设置行为日志的状态属性', '', '[user|getOperator]在[time|time_format]设置系统行为的状态', 1, 1, 1479353442, 0),
(32, 'del_actionlog', '删除行为日志（只单条删除）', '删除行为日志（只单条删除）属性', '', '[user|getOperator]在[time|time_format]删除行为日志（只单条删除）', 1, 1, 1479354014, 0),
(33, 'del_actionlogs', '批量删除行为日志（可选择性的删除）', '批量删除行为日志属性', '', '[user|getOperator]在[time|time_format]批量删除行为日志', 1, 1, 1479354094, 0),
(34, 'add_tag_config', '添加新的系统标签', '添加新的系统标签属性', '', '[user|getOperator]在[time|time_format]添加新的系统标签', 1, 1, 1479354268, 1479354404),
(35, 'edit_tag_config', '编辑系统标签', '编辑系统标签属性', '', '[user|getOperator]在[time|time_format]修改系统标签信息', 1, 1, 1479354378, 0),
(36, 'del_tag_config', '删除系统标签', '删除系统标签属性', '', '[user|getOperator]在[time|time_format]删除系统标签及其相关信息', 1, 1, 1479354504, 0),
(37, 'set_tag_order', '设置系统标签的排序状态', '设置系统标签的排序状态属性', '', '[user|getOperator]在[time|time_format]设置系统标签的排序状态', 1, 1, 1479354627, 0),
(38, 'del_user_tag', '删除用户自定义标签', '删除用户自定义标签属性', '', '[user|getOperator]在[time|time_format]删除用户自定义标签', 1, 1, 1479354882, 0),
(39, 'set_usertag_order', '设置用户自定义标签的排序状态', '设置用户自定义标签的排序状态属性', '', '[user|getOperator]在[time|time_format]设置用户自定义标签的排序状态', 1, 1, 1479354989, 0),
(40, 'add_pay_account', '添加新的用户账户配置', '添加新的用户账户配置属性', '', '[user|getOperator]在[time|time_format]添加新的用户账户配置信息', 1, 1, 1479355265, 0),
(41, 'edit_pay_account', '编辑用户账户配置信息', '编辑用户账户配置信息属性', '', '[user|getOperator]在[time|time_format]修改用户账户配置信息', 1, 1, 1479355350, 0),
(42, 'del_pay_account', '删除用户账户配置信息', '删除用户账户配置属性', '', '[user|getOperator]在[time|time_format]删除用户账户配置信息', 1, 1, 1479355446, 0),
(43, 'set_payaccount_status', '设置用户账户配置的状态', '设置用户账户配置的状态属性', '', '[user|getOperator]在[time|time_format]设置用户账户配置的状态', 1, 1, 1479355546, 0),
(44, 'set_payaccount_order', '设置用户账户配置的排序', '设置用户账户配置的排序属性', '', '[user|getOperator]在[time|time_format]设置用户账户配置的排序', 1, 1, 1479359514, 0),
(45, 'edit_student', '编辑学员信息', '编辑学员信息属性', '', '[user|getOperator]在[time|time_format]编辑学员信息', 1, 1, 1479360667, 0),
(46, 'add_student', '添加新的学员', '添加新的学员属性', '', '[user|getOperator]在[time|time_format]添加新的学员及其相关信息', 1, 1, 1479361117, 0),
(47, 'recover_del_student', '恢复被删除的学员', '恢复被删除的学员属性', '', '[user|getOperator]在[time|time_format]恢复被删除的学员及其相关信息', 1, 1, 1479361195, 0),
(48, 'del_users_wallet', '删除用户银行账户', '删除用户银行账户信息', '', '[user|getOperator]在[time|time_format]删除用户银行账户', 1, 1, 1479361379, 0),
(49, 'add_schoolsite', '添加新的驾校场地', '添加新的驾校场地属性', '', '[user|getOperator]在[time|time_format]添加新的驾校场地及其信息', 1, 1, 1479361883, 0),
(50, 'edit_schoolsite', '编辑驾校场地', '编辑驾校场地属性', '', '[user|getOperator]在[time|time_format]修改驾校场地信息', 1, 1, 1479361963, 0),
(51, 'del_schoolsite', '删除驾校场地', '删除驾校场地属性', '', '[user|getOperator]在[time|time_format]删除驾校场地及其相关信息', 1, 1, 1479362072, 0),
(52, 'set_site_status', '设置驾校场地的开放状态', '设置驾校场地的开放状态', '', '[user|getOperator]在[time|time_format]设置驾校场地的开放状态', 1, 1, 1479362280, 0),
(53, 'add_schoolshifts', '添加新的驾校班制', '添加新的驾校班制', '', '[user|getOperator]在[time|time_format]添加新的驾校班制', 1, 1, 1479362574, 0),
(54, 'edit_schoolshifts', '编辑驾校班制', '编辑驾校班制属性', '', '[user|getOperator]在[time|time_format]修改驾校班制', 1, 1, 1479362676, 0),
(55, 'del_schoolshifts', '删除驾校的班制', '删除驾校的班制属性', '', '[user|getOperator]在[time|time_format]删除驾校的班制及其相关信息', 1, 1, 1479362799, 0),
(56, 'set_schoolshifts_status', '设置推荐驾校班制的状态', '设置推荐驾校班制的状态', '', '[user|getOperator]在[time|time_format]设置推荐驾校班制的状态', 1, 1, 1479362908, 0),
(57, 'set_shiftsdeleted_status', '设置驾校班制的删除状态', '设置驾校班制的删除状态属性', '', '[user|getOperator]在[time|time_format]设置驾校班制的删除状态', 1, 1, 1479363046, 0),
(58, 'set_schoolshifts_order', '设置驾校班制的排序状态', '设置驾校班制的排序状态属性', '', '[user|getOperator]在[time|time_format]设置驾校班制的排序状态', 1, 1, 1479363126, 0),
(59, 'add_train_location', '添加新的驾校报名点', '添加新的驾校报名点属性', '', '[user|getOperator]在[time|time_format]添加新的驾校报名点', 1, 1, 1479363272, 0),
(60, 'edit_train_location', '编辑驾校的报名点', '编辑驾校的报名点属性', '', '[user|getOperator]在[time|time_format]编辑驾校的报名点信息', 1, 1, 1479363520, 0),
(61, 'del_train_location', '删除驾校报名点', '删除驾校报名点属性', '', '[user|getOperator]在[time|time_format]删除驾校报名点', 1, 1, 1479363653, 0),
(62, 'add_school_banner', '添加驾校新的轮播图', '添加驾校新的轮播图属性', '', '[user|getOperator]在[time|time_format]添加驾校新的轮播图', 1, 1, 1479363904, 0),
(63, 'del_school_banner', '删除驾校的轮播图', '删除驾校的轮播图属性', '', '[user|getOperator]在[time|time_format]删除驾校的轮播图', 1, 1, 1479364018, 0),
(64, 'add_school_order', '添加驾校订单', '添加驾校订单属性', '', '[user|getOperator]在[time|time_format]添加驾校订单', 1, 1, 1479364179, 0),
(65, 'edit_school_order', '编辑驾校订单', '编辑驾校订单', '', '[user|getOperator]在[time|time_format]修改驾校订单信息', 1, 1, 1479364288, 0),
(66, 'del_school_order', '删除（逻辑）驾校订单', '删除（逻辑）驾校订单属性', '', '[user|getOperator]在[time|time_format]删除（逻辑）驾校订单', 1, 1, 1479364385, 0),
(67, 'del_study_orders', '删除（逻辑）预约学车信息', '删除（逻辑）预约学车信息属性', '', '[user|getOperator]在[time|time_format]删除（逻辑）预约学车信息', 1, 1, 1479364851, 0),
(68, 'del_train_records', '删除模拟记录', '删除模拟记录属性', '', '[user|getOperator]在[time|time_format]删除模拟记录信息', 1, 1, 1479365787, 0),
(69, 'add_coupon_category', '添加新的优惠券种类', '添加新的优惠券种类属性', '', '[user|getOperator]在[time|time_format]添加新的优惠券种类', 1, 1, 1479366114, 0),
(70, 'edit_coupon_category', '编辑优惠券种类信息', '编辑优惠券种类信息属性', '', '[user|getOperator]在[time|time_format]编辑优惠券种类信息', 1, 1, 1479366278, 0),
(71, 'del_coupon_category', '删除优惠券种类', '删除优惠券种类', '', '[user|getOperator]在[time|time_format]删除优惠券种类及其相关信息', 1, 1, 1479366359, 0),
(72, 'set_coupon_status', '设置优惠券的开启状态', '设置优惠券的开启状态属性', '', '[user|getOperator]在[time|time_format]设置优惠券的开启状态', 1, 1, 1479366474, 0),
(73, 'add_coupon', '添加新的优惠券', '添加新的优惠券', '', '[user|getOperator]在[time|time_format]添加新的优惠券', 1, 1, 1479366659, 0),
(74, 'edit_coupon', '编辑优惠券信息', '编辑优惠券信息属性', '', '[user|getOperator]在[time|time_format]编辑优惠券信息', 1, 1, 1479366755, 0),
(75, 'del_coupon', '删除优惠券及其信息', '删除优惠券及其信息属性', '', '[user|getOperator]在[time|time_format]删除优惠券及其信息', 1, 1, 1479366868, 0),
(76, 'set_coupon_order', '设置优惠券的排序状态', '设置优惠券的排序状态属性', '', '[user|getOperator]在[time|time_format]设置优惠券的排序状态', 1, 1, 1479366939, 0),
(77, 'del_user_coupon', '删除学车券及其信息', '删除学车券及其信息属性', '', '[user|getOperator]在[time|time_format]删除学车券及其信息', 1, 1, 1479367032, 0),
(78, 'add_coin_goods', '添加新的商品', '添加新的商品属性', '', '[user|getOperator]在[time|time_format]添加新的商品及其相关信息', 1, 1, 1479367155, 0),
(79, 'edit_coin_goods', '编辑商品信息', '编辑商品信息属性', '', '[user|getOperator]在[time|time_format]修改商品信息', 1, 1, 1479367288, 0),
(80, 'del_coin_goods', '删除商品信息', '删除商品信息属性', '', '[user|getOperator]在[time|time_format]删除商品信息', 1, 1, 1479367354, 0),
(81, 'set_coingoods_order', '设置商品的排序状态', '设置商品的排序状态', '', '[user|getOperator]在[time|time_format]设置商品的排序状态', 1, 1, 1479367425, 0),
(82, 'set_coingoods_hot', '设置商品的热销状态', '设置商品的热销状态', '', '[user|getOperator]在[time|time_format]设置商品的热销状态', 1, 1, 1479367599, 0),
(83, 'set_coingoods_recommend', '设置商品的推荐状态', '设置商品的推荐状态', '', '[user|getOperator]在[time|time_format]设置商品的推荐状态', 1, 1, 1479367725, 0),
(84, 'set_coingoods_promote', '设置商品的促销状态', '设置商品的促销状态', '', '[user|getOperator]在[time|time_format]设置商品的促销状态', 1, 1, 1479367796, 0),
(85, 'set_coingoods_publish', '设置商品的发布状态', '设置商品的发布状态', '', '[user|getOperator]在[time|time_format]设置商品的发布状态', 1, 1, 1479367920, 0),
(86, 'add_coin_category', '添加新的商品分类信息', '添加新的商品分类信息', '', '[user|getOperator]在[time|time_format]添加新的商品分类信息', 1, 1, 1479368135, 0),
(87, 'edit_coin_category', '编辑商品信息', '编辑商品信息', '', '[user|getOperator]在[time|time_format]编辑商品信息', 1, 1, 1479368192, 0),
(88, 'del_coin_category', '删除商品分类信息', '删除商品分类信息', '', '[user|getOperator]在[time|time_format]删除商品分类信息', 1, 1, 1479368464, 0),
(89, 'set_coincate_order', '设置商品分类的排序状态', '设置商品分类的排序状态', '', '[user|getOperator]在[time|time_format]设置商品分类的排序状态', 1, 1, 1479368532, 0),
(90, 'add_coin_rule', '添加新的金币规则', '添加新的金币规则', '', '[user|getOperator]在[time|time_format]添加新的金币规则', 1, 1, 1479368619, 0),
(91, 'edit_coin_rule', '编辑金币规则信息', '编辑金币规则信息', '', '[user|getOperator]在[time|time_format]编辑金币规则信息', 1, 1, 1479368696, 0),
(92, 'del_coin_rule', '删除金币规则', '删除金币规则', '', '[user|getOperator]在[time|time_format]删除金币规则', 1, 1, 1479368737, 0),
(93, 'del_exchange_order', '删除金币兑换记录', '删除金币兑换记录', '', '[user|getOperator]在[time|time_format]删除金币兑换记录', 1, 1, 1479368851, 0),
(94, 'add_coingoods_banner', '添加商品轮播图', '添加商品轮播图', '', '[user|getOperator]在[time|time_format]添加商品轮播图', 1, 1, 1479368949, 1479369062),
(95, 'del_coingoods_banner', '删除商品轮播图', '删除商品轮播图', '', '[user|getOperator]在[time|time_format]删除商品轮播图', 1, 1, 1479369219, 0),
(96, 'set_coach_status', '设置教练的在线状态', '设置教练的在线状态', '', '[user|getOperator]在[time|time_format]设置教练的在线状态', 1, 1, 1479369321, 0),
(97, 'del_coach', '删除教练', '删除教练', '', '[user|getOperator]在[time|time_format]删除教练及其信息', 1, 1, 1479369370, 0),
(98, 'add_coach', '添加新的教练', '添加新的教练', '', '[user|getOperator]在[time|time_format]添加新的教练', 1, 1, 1479369470, 0),
(99, 'edit_coach', '编辑教练信息', '编辑教练信息', '', '[user|getOperator]在[time|time_format]修改教练信息', 1, 1, 1479369554, 0),
(100, 'add_coach_shifts', '添加新的教练班制', '添加新的教练班制', '', '[user|getOperator]在[time|time_format]添加新的教练班制', 1, 1, 1479369838, 0),
(101, 'edit_coach_shifts', '编辑教练的班制信息', '编辑教练的班制信息', '', '[user|getOperator]在[time|time_format]修改教练的班制信息', 1, 1, 1479369900, 0),
(102, 'set_coachshifts_order', '设置教练班制的排序状态', '设置教练班制的排序状态', '', '[user|getOperator]在[time|time_format]设置教练班制的排序状态', 1, 1, 1479369966, 0),
(103, 'set_coachshifts_status', '设置教练班制的推荐状态', '设置教练班制的推荐状态', '', '[user|getOperator]在[time|time_format]设置教练班制的推荐状态', 1, 1, 1479370030, 0),
(104, 'del_coach_shifts', '删除教练班制信息', '删除教练班制信息', '', '[user|getOperator]在[time|time_format]删除教练班制信息', 1, 1, 1479370080, 0),
(105, 'add_coach_temprelation', '添加教练时间模板关联模型', '添加教练时间模板关联模型', '', '[user|getOperator]在[time|time_format]添加教练时间模板关联模型', 1, 1, 1479370179, 0),
(106, 'edit_coach_temprelation', '编辑教练时间模板关联模型', '编辑教练时间模板关联模型', '', '[user|getOperator]在[time|time_format]修改教练时间模板关联模型', 1, 1, 1479370349, 0),
(107, 'del_coach_temprelation', '删除教练时间模板关联模型', '删除教练时间模板关联模型', '', '[user|getOperator]在[time|time_format]删除教练时间模板关联模型', 1, 1, 1479370480, 0),
(108, 'set_coachtemprelation_default', '设置教练时间模板关联模型的默认状态', '设置教练时间模板关联模型的默认状态', '', '[user|getOperator]在[time|time_format]设置教练时间模板关联模型的默认状态', 1, 1, 1479370581, 0),
(109, 'set_coachtemprelation_online', '设置教练时间模板的关联模型的在线状态', '设置教练时间模板的关联模型的在线状态', '', '[user|getOperator]在[time|time_format]设置教练时间模板的关联模型的在线状态', 1, 1, 1479370688, 0),
(110, 'add_timeconfigtemp', '添加新的时间模板', '添加新的时间模板', '', '[user|getOperator]在[time|time_format]添加新的时间模板', 1, 1, 1479370781, 0),
(111, 'edit_timeconfigtemp', '编辑时间模板信息', '编辑时间模板信息', '', '[user|getOperator]在[time|time_format]修改时间模板信息', 1, 1, 1479370828, 0),
(112, 'del_timeconfigtemp', '删除时间模板信息', '删除时间模板信息', '', '[user|getOperator]在[time|time_format]删除时间模板信息', 1, 1, 1479370878, 0),
(113, 'set_timeconfigtemp_online', '设置时间模板的在线状态', '设置时间模板的在线状态', '', '[user|getOperator]在[time|time_format]设置时间模板的在线状态', 1, 1, 1479370933, 0),
(114, 'add_car', '添加新的车辆', '添加新的车辆', '', '[user|getOperator]在[time|time_format]添加新的车辆', 1, 1, 1479371158, 0),
(115, 'edit_car', '编辑车辆信息', '编辑车辆信息', '', '[user|getOperator]在[time|time_format]修改车辆信息', 1, 1, 1479371267, 0),
(116, 'del_car', '删除车辆及其信息', '删除车辆及其信息', '', '[user|getOperator]在[time|time_format]删除车辆及其信息', 1, 1, 1479371337, 0),
(117, 'add_cars_category', '添加新的车辆型号信息', '添加新的车辆型号信息', '', '[user|getOperator]在[time|time_format]添加新的车辆型号信息', 1, 1, 1479371457, 0),
(118, 'edit_cars_category', '编辑车辆型号信息', '编辑车辆型号信息', '', '[user|getOperator]在[time|time_format]修改车辆型号信息', 1, 1, 1479371515, 0),
(119, 'del_cars_category', '删除车辆型号信息', '删除车辆型号信息', '', '[user|getOperator]在[time|time_format]删除车辆型号信息', 1, 1, 1479371558, 0),
(120, 'add_learn_video', '添加新的学车视频', '添加新的学车视频', '', '[user|getOperator]在[time|time_format]添加新的学车视频', 1, 1, 1479371651, 0),
(121, 'edit_learn_video', '编辑学车视频信息', '编辑学车视频信息', '', '[user|getOperator]在[time|time_format]修改学车视频信息', 1, 1, 1479371694, 0),
(122, 'set_learnvideo_status', '设置学车视频的开启状态', '设置学车视频的开启状态', '', '[user|getOperator]在[time|time_format]设置学车视频的开启状态', 1, 1, 1479371756, 0),
(123, 'del_learn_video', '删除学车视频及其信息', '删除学车视频及其信息', '', '[user|getOperator]在[time|time_format]删除学车视频及其信息', 1, 1, 1479371800, 0),
(124, 'del_app', '删除app版本信息', '删除app版本信息', '', '[user|getOperator]在[time|time_format]删除app版本信息', 1, 1, 1479371915, 0),
(125, 'add_app', '添加新版本app', '添加新版本app', '', '[user|getOperator]在[time|time_format]添加新版本app', 1, 1, 1479371963, 0),
(126, 'edit_app', '编辑app的版本信息', '编辑app的版本信息', '', '[user|getOperator]在[time|time_format]编辑app的版本信息', 1, 1, 1479372106, 0),
(127, 'del_qppfeedback', '删除app反馈信息', '删除app反馈信息', '', '[user|getOperator]在[time|time_format]删除app反馈信息', 1, 1, 1479372373, 0),
(128, 'add_school', '添加新的驾校', '添加新的驾校', '', '[user|getOperator]在[time|time_format]添加新的驾校', 1, 1, 1479372626, 0),
(129, 'edit_school', '编辑驾校信息', '编辑驾校信息', '', '[user|getOperator]在[time|time_format]修改驾校信息', 1, 1, 1479372657, 0),
(130, 'del_school', '删除驾校', '删除驾校', '', '[user|getOperator]在[time|time_format]删除驾校', 1, 1, 1479372761, 0),
(131, 'set_school_status', '设置驾校的展示状态', '设置驾校的展示状态', '', '[user|getOperator]在[time|time_format]设置驾校的展示状态', 1, 1, 1479372816, 0),
(132, 'del_adsmanage', '删除广告操作', '删除广告操作', '', '[user|getOperator]在[time|time_format]删除广告及其相关信息', 1, 1, 1479372913, 0),
(133, 'add_adsmanage', '添加新的广告', '添加新的广告', '', '[user|getOperator]在[time|time_format]添加新的广告', 1, 1, 1479372956, 0),
(134, 'edit_adsmanage', '编辑广告信息', '编辑广告信息', '', '[user|getOperator]在[time|time_format]编辑广告信息', 1, 1, 1479372997, 0),
(135, 'set_adsmanage_order', '设置广告的排序状态', '设置广告的排序状态', '', '[user|getOperator]在[time|time_format]设置广告的排序状态', 1, 1, 1479373081, 0),
(136, 'add_ads_position', '添加广告的寻访位置', '添加广告的寻访位置', '', '[user|getOperator]在[time|time_format]添加广告的寻访位置', 1, 1, 1479373224, 0),
(137, 'edit_ads_position', '编辑广告存放的位置', '编辑广告存放的位置', '', '[user|getOperator]在[time|time_format]编辑广告存放的位置', 1, 1, 1479373299, 0),
(138, 'del_ads_position', '删除广告的位置', '删除广告的位置', '', '[user|getOperator]在[time|time_format]删除广告的位置', 1, 1, 1479373348, 0),
(139, 'add_ads_level', '添加广告的等级', '添加广告的等级', '', '[user|getOperator]在[time|time_format]添加广告的等级', 1, 1, 1479373431, 0),
(140, 'del_ads_level', '删除广告等级', '删除广告等级', '', '[user|getOperator]在[time|time_format]删除广告等级', 1, 1, 1479373487, 0),
(141, 'update_ads_level', '更新广告等级', '更新广告等级', '', '[user|getOperator]在[time|time_format]更新广告等级', 1, 1, 1479373544, 0),
(142, 'set_student_status', '设置学员的删除状态', '设置学员的删除状态属性', '', '[user|getOperator]在[time|time_format]设置了学员的删除状态', 1, 1, 1480063305, 0),
(143, 'set_coachdel_status', '设置教练的删除状态', '设置教练的删除状态', '', '[user|getOperator]在[time|time_format]设置了教练的删除状态', 1, 1, 1480323378, 0),
(144, 'del_stucomment_school', '删除学员评价驾校的相关信息', '删除学员评价驾校的相关信息属性', '', '[user|getOperator]在[time|time_format]删除了学员评价驾校的相关信息', 1, 1, 1480555529, 0),
(145, 'set_coach_order', '设置教练的排序', '设置教练的排序属性', '', '[user|getOperator]在[time|time_format]设置了教练的排序', 1, 1, 1481263393, 0),
(146, 'set_coupon_support_status', '设置优惠券的支持状态', '设置优惠券的支持状态', '', '[user|getOperator]在[time|time_format]设置了优惠券的支持状态', 1, 1, 1481264960, 0),
(147, 'set_schooltrain_order', '设置驾校报名点的排序', '设置驾校报名点的排序属性', '', '[user|getOperator]在[time|time_format]设置了驾校报名点的排序', 1, 1, 1481699182, 0),
(148, 'set_coach_bindstatus', '设置教练的绑定状态', '设置教练的绑定状态属性', '', '[user|getOperator]在[time|time_format]设置了教练的绑定状态', 1, 1, 1481799176, 0),
(149, 'set_coach_hotstatus', '设置教练的热门状态', '设置教练的热门状态属性', '', '[user|getOperator]在[time|time_format]设置了教练的热门状态', 1, 1, 1482297310, 0),
(150, 'set_schoolorder_status', '设置报名驾校订单的状态', '设置报名驾校订单的状态属性', '', '[user|getOperator]在[time|time_format]设置了报名驾校订单的状态', 1, 1, 1482308798, 0),
(151, 'set_studyorder_status', '设置预约学车的订单状态', '设置预约学车的订单状态行为', '', '[user|getOperator]在[time|time_format]设置了预约学车的订单状态', 1, 1, 1482310686, 0),
(152, 'set_hot_city', '设置热门城市', '设置热门城市属性', '', '[user|getOperator]在[time|time_format]设置了热门城市', 1, 1, 1483429810, 0),
(153, 'add_city', '添加城市', '添加城市属性', '', '[user|getOperator]在[time|time_format]添加了城市', 1, 1, 1483439331, 0),
(154, 'edit_city', '编辑城市信息', '编辑城市信息', '', '[user|getOperator]在[time|time_format]编辑了城市信息', 1, 1, 1483439359, 0),
(155, 'del_city', '删除城市', '删除城市属性', '', '[user|getOperator]在[time|time_format]删除了城市', 1, 1, 1483493050, 0),
(157, 'set_coach_bind', '设置教练与学员的绑定状态', '设置教练与学员的绑定状态属性', '', '[user|getOperator]在[time|time_format]设置教练与学员的绑定状态', 1, 1, 1483517413, 0),
(158, 'set_coach_certification', '设置教练认证状态', '设置教练认证状态', '', '[user|getOperator]在[time|time_format]设置了教练认证状态', 1, 1, 1483607223, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
