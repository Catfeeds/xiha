-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2017-05-10 02:52:56
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
-- 表的结构 `cs_bank_config`
--

DROP TABLE IF EXISTS `cs_bank_config` ;

CREATE TABLE `cs_bank_config` (
  `id` int(11) NOT NULL,
  `bank_no` varchar(20) NOT NULL,
  `bank_name` varchar(32) NOT NULL,
  `bank_code` varchar(20) NOT NULL,
  `card_type` varchar(10) NOT NULL COMMENT '卡类型缩写 CC 信用卡 DC储蓄卡（借记卡）',
  `addtime` bigint(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='银行配置表';

--
-- 转存表中的数据 `cs_bank_config`
--

INSERT INTO `cs_bank_config` (`id`, `bank_no`, `bank_name`, `bank_code`, `card_type`, `addtime`) VALUES
(1, 'ICBC', '工商银行(借记卡)', 'ICBC_DEBIT', 'DC', 11231231),
(2, 'ICBC', '工商银行(信用卡)', 'ICBC_CREDIT', 'CC', 11231232),
(3, 'ABC', '农业银行(借记卡)', 'ABC_DEBIT', 'DC', 11231233),
(4, 'ABC', '农业银行(信用卡)', 'ABC_CREDIT', 'CC', 11231234),
(5, 'PSBC', '邮政储蓄银行(借记卡)', 'PSBC_DEBIT', 'DC', 11231235),
(6, 'PSBC', '邮政储蓄银行(信用卡)', 'PSBC_CREDIT', 'CC', 11231236),
(7, 'CCB', '建设银行(借记卡)', 'CCB_DEBIT', 'DC', 11231237),
(8, 'CCB', '建设银行(信用卡)', 'CCB_CREDIT', 'CC', 11231238),
(9, 'CMB', '招商银行(借记卡)', 'CMB_DEBIT', 'DC', 11231239),
(10, 'CMB', '招商银行(信用卡)', 'CMB_CREDIT', 'CC', 11231240),
(11, 'BOC', '中国银行(借记卡)', 'BOC_DEBIT', 'DC', 11231241),
(12, 'BOC', '中国银行(信用卡)', 'BOC_CREDIT', 'CC', 11231242),
(13, 'COMM', '交通银行(借记卡)', 'COMM_DEBIT', 'DC', 11231243),
(14, 'COMM', '交通银行(信用卡)', 'COMM_CREDIT', 'CC', 11231244),
(15, 'SPDB', '浦发银行(借记卡)', 'SPDB_DEBIT', 'DC', 11231245),
(16, 'SPDB', '浦发银行(信用卡)', 'SPDB_CREDIT', 'CC', 11231246),
(17, 'GDB', '广发银行(借记卡)', 'GDB_DEBIT', 'DC', 11231247),
(18, 'GDB', '广发银行(信用卡)', 'GDB_CREDIT', 'CC', 11231248),
(19, 'CMBC', '民生银行(借记卡)', 'CMBC_DEBIT', 'DC', 11231249),
(20, 'CMBC', '民生银行(信用卡)', 'CMBC_CREDIT', 'CC', 11231250),
(21, 'PAB', '平安银行(借记卡)', 'PAB_DEBIT', 'DC', 11231251),
(22, 'PAB', '平安银行(信用卡)', 'PAB_CREDIT', 'CC', 11231252),
(23, 'CEB', '光大银行(借记卡)', 'CEB_DEBIT', 'DC', 11231253),
(24, 'CEB', '光大银行(信用卡)', 'CEB_CREDIT', 'CC', 11231254),
(25, 'CIB', '兴业银行(借记卡)', 'CIB_DEBIT', 'DC', 11231255),
(26, 'CIB', '兴业银行(信用卡)', 'CIB_CREDIT', 'CC', 11231256),
(27, 'CITIC', '中信银行(借记卡)', 'CITIC_DEBIT', 'DC', 11231257),
(28, 'CITIC', '中信银行(信用卡)', 'CITIC_CREDIT', 'CC', 11231258),
(29, 'BOSH', '上海银行(借记卡)', 'BOSH_DEBIT', 'DC', 11231259),
(30, 'BOSH', '上海银行(信用卡)', 'BOSH_CREDIT', 'CC', 11231260),
(31, 'CRB', '华润银行(借记卡)', 'CRB_DEBIT', 'DC', 11231261),
(32, 'HZB', '杭州银行(借记卡)', 'HZB_DEBIT', 'DC', 11231262),
(33, 'HZB', '杭州银行(信用卡)', 'HZB_CREDIT', 'CC', 11231263),
(34, 'BSB', '包商银行(借记卡)', 'BSB_DEBIT', 'DC', 11231264),
(35, 'BSB', '包商银行(信用卡)', 'BSB_CREDIT', 'CC', 11231265),
(36, 'CQB', '重庆银行(借记卡)', 'CQB_DEBIT', 'DC', 11231266),
(37, 'SDEB', '顺德农商行(借记卡)', 'SDEB_DEBIT', 'DC', 11231267),
(38, 'SZRCB', '深圳农商银行(借记卡)', 'SZRCB_DEBIT', 'DC', 11231268),
(39, 'SZRCB', '深圳农商银行(信用卡)', 'SZRCB_CREDIT', 'CC', 11231269),
(40, 'HRBB', '哈尔滨银行(借记卡)', 'HRBB_DEBIT', 'DC', 11231270),
(41, 'BOCD', '成都银行(借记卡)', 'BOCD_DEBIT', 'DC', 11231271),
(42, 'GDNYB', '南粤银行(借记卡)', 'GDNYB_DEBIT', 'DC', 11231272),
(43, 'GDNYB', '南粤银行(信用卡)', 'GDNYB_CREDIT', 'CC', 11231273),
(44, 'GZCB', '广州银行(借记卡)', 'GZCB_DEBIT', 'DC', 11231274),
(45, 'GZCB', '广州银行(信用卡)', 'GZCB_CREDIT', 'CC', 11231275),
(46, 'JSB', '江苏银行(借记卡)', 'JSB_DEBIT', 'DC', 11231276),
(47, 'JSB', '江苏银行(信用卡)', 'JSB_CREDIT', 'CC', 11231277),
(48, 'NBCB', '宁波银行(借记卡)', 'NBCB_DEBIT', 'DC', 11231278),
(49, 'NBCB', '宁波银行(信用卡)', 'NBCB_CREDIT', 'CC', 11231279),
(50, 'NJCB', '南京银行(借记卡)', 'NJCB_DEBIT', 'DC', 11231280),
(51, 'QHNX', '青海农信(借记卡)', 'QHNX_DEBIT', 'DC', 11231281),
(52, 'ORDOSB', '鄂尔多斯银行(信用卡)', 'ORDOSB_CREDIT', 'CC', 11231282),
(53, 'ORDOSB', '鄂尔多斯银行(借记卡)', 'ORDOSB_DEBIT', 'DC', 11231283),
(54, 'BJRCB', '北京农商(信用卡)', 'BJRCB_CREDIT', 'CC', 11231284),
(55, 'BHB', '河北银行(借记卡)', 'BHB_DEBIT', 'DC', 11231285),
(56, 'BGZB', '贵州银行(借记卡)', 'BGZB_DEBIT', 'DC', 11231286),
(57, 'BEEB', '鄞州银行(借记卡)', 'BEEB_DEBIT', 'DC', 11231287),
(58, 'PZHCCB', '攀枝花银行(借记卡)', 'PZHCCB_DEBIT', 'DC', 11231288),
(59, 'QDCCB', '青岛银行(信用卡)', 'QDCCB_CREDIT', 'CC', 11231289),
(60, 'QDCCB', '青岛银行(借记卡)', 'QDCCB_DEBIT', 'DC', 11231290),
(61, 'SHINHAN', '新韩银行(借记卡)', 'SHINHAN_DEBIT', 'DC', 11231291),
(62, 'QLB', '齐鲁银行(借记卡)', 'QLB_DEBIT', 'DC', 11231292),
(63, 'QSB', '齐商银行(借记卡)', 'QSB_DEBIT', 'DC', 11231293),
(64, 'ZZB', '郑州银行(借记卡)', 'ZZB_DEBIT', 'DC', 11231294),
(65, 'CCAB', '长安银行(借记卡)', 'CCAB_DEBIT', 'DC', 11231295),
(66, 'RZB', '日照银行(借记卡)', 'RZB_DEBIT', 'DC', 11231296),
(67, 'SCNX', '四川农信(借记卡)', 'SCNX_DEBIT', 'DC', 11231297),
(68, 'BEEB', '鄞州银行(信用卡)', 'BEEB_CREDIT', 'CC', 11231298),
(69, 'SDRCU', '山东农信(借记卡)', 'SDRCU_DEBIT', 'DC', 11231299),
(70, 'BCZ', '沧州银行(借记卡)', 'BCZ_DEBIT', 'DC', 11231300),
(71, 'SJB', '盛京银行(借记卡)', 'SJB_DEBIT', 'DC', 11231301),
(72, 'LNNX', '辽宁农信(借记卡)', 'LNNX_DEBIT', 'DC', 11231302),
(73, 'JUFENGB', '临朐聚丰村镇银行(借记卡)', 'JUFENGB_DEBIT', 'DC', 11231303),
(74, 'ZZB', '郑州银行(信用卡)', 'ZZB_CREDIT', 'CC', 11231304),
(75, 'JXNXB', '江西农信(借记卡)', 'JXNXB_DEBIT', 'DC', 11231305),
(76, 'JZB', '晋中银行(借记卡)', 'JZB_DEBIT', 'DC', 11231306),
(77, 'JZCB', '锦州银行(信用卡)', 'JZCB_CREDIT', 'CC', 11231307),
(78, 'JZCB', '锦州银行(借记卡)', 'JZCB_DEBIT', 'DC', 11231308),
(79, 'KLB', '昆仑银行(借记卡)', 'KLB_DEBIT', 'DC', 11231309),
(80, 'KRCB', '昆山农商(借记卡)', 'KRCB_DEBIT', 'DC', 11231310),
(81, 'KUERLECB', '库尔勒市商业银行(借记卡)', 'KUERLECB_DEBIT', 'DC', 11231311),
(82, 'LJB', '龙江银行(借记卡)', 'LJB_DEBIT', 'DC', 11231312),
(83, 'NYCCB', '南阳村镇银行(借记卡)', 'NYCCB_DEBIT', 'DC', 11231313),
(84, 'LSCCB', '乐山市商业银行(借记卡)', 'LSCCB_DEBIT', 'DC', 11231314),
(85, 'LUZB', '柳州银行(借记卡)', 'LUZB_DEBIT', 'DC', 11231315),
(86, 'LWB', '莱商银行(借记卡)', 'LWB_DEBIT', 'DC', 11231316),
(87, 'LYYHB', '辽阳银行(借记卡)', 'LYYHB_DEBIT', 'DC', 11231317),
(88, 'LZB', '兰州银行(借记卡)', 'LZB_DEBIT', 'DC', 11231318),
(89, 'MINTAIB', '民泰银行(信用卡)', 'MINTAIB_CREDIT', 'CC', 11231319),
(90, 'MINTAIB', '民泰银行(借记卡)', 'MINTAIB_DEBIT', 'DC', 11231320),
(91, 'NCB', '宁波通商银行(借记卡)', 'NCB_DEBIT', 'DC', 11231321),
(92, 'NMGNX', '内蒙古农信(借记卡)', 'NMGNX_DEBIT', 'DC', 11231322),
(93, 'XAB', '西安银行(借记卡)', 'XAB_DEBIT', 'DC', 11231323),
(94, 'WFB', '潍坊银行(信用卡)', 'WFB_CREDIT', 'CC', 11231324),
(95, 'WFB', '潍坊银行(借记卡)', 'WFB_DEBIT', 'DC', 11231325),
(96, 'WHB', '威海商业银行(信用卡)', 'WHB_CREDIT', 'CC', 11231326),
(97, 'WHB', '威海市商业银行(借记卡)', 'WHB_DEBIT', 'DC', 11231327),
(98, 'WHRC', '武汉农商(信用卡)', 'WHRC_CREDIT', 'CC', 11231328),
(99, 'WHRC', '武汉农商行(借记卡)', 'WHRC_DEBIT', 'DC', 11231329),
(100, 'WJRCB', '吴江农商行(借记卡)', 'WJRCB_DEBIT', 'DC', 11231330),
(101, 'WLMQB', '乌鲁木齐银行(借记卡)', 'WLMQB_DEBIT', 'DC', 11231331),
(102, 'WRCB', '无锡农商(借记卡)', 'WRCB_DEBIT', 'DC', 11231332),
(103, 'WZB', '温州银行(借记卡)', 'WZB_DEBIT', 'DC', 11231333),
(104, 'XAB', '西安银行(信用卡)', 'XAB_CREDIT', 'CC', 11231334),
(105, 'WEB', '微众银行(借记卡)', 'WEB_DEBIT', 'DC', 11231335),
(106, 'XIB', '厦门国际银行(借记卡)', 'XIB_DEBIT', 'DC', 11231336),
(107, 'XJRCCB', '新疆农信银行(借记卡)', 'XJRCCB_DEBIT', 'DC', 11231337),
(108, 'XMCCB', '厦门银行(借记卡)', 'XMCCB_DEBIT', 'DC', 11231338),
(109, 'YNRCCB', '云南农信(借记卡)', 'YNRCCB_DEBIT', 'DC', 11231339),
(110, 'YRRCB', '黄河农商银行(信用卡)', 'YRRCB_CREDIT', 'CC', 11231340),
(111, 'YRRCB', '黄河农商银行(借记卡)', 'YRRCB_DEBIT', 'DC', 11231341),
(112, 'YTB', '烟台银行(借记卡)', 'YTB_DEBIT', 'DC', 11231342),
(113, 'ZJB', '紫金农商银行(借记卡)', 'ZJB_DEBIT', 'DC', 11231343),
(114, 'ZJLXRB', '兰溪越商银行(借记卡)', 'ZJLXRB_DEBIT', 'DC', 11231344),
(115, 'ZJRCUB', '浙江农信(信用卡)', 'ZJRCUB_CREDIT', 'CC', 11231345),
(116, 'AHRCUB', '安徽省农村信用社联合社(借记卡)', 'AHRCUB_DEBIT', 'DC', 11231346),
(117, 'BCZ', '沧州银行(信用卡)', 'BCZ_CREDIT', 'CC', 11231347),
(118, 'SRB', '上饶银行(借记卡)', 'SRB_DEBIT', 'DC', 11231348),
(119, 'ZYB', '中原银行(借记卡)', 'ZYB_DEBIT', 'DC', 11231349),
(120, 'ZRCB', '张家港农商行(借记卡)', 'ZRCB_DEBIT', 'DC', 11231350),
(121, 'SRCB', '上海农商银行(信用卡)', 'SRCB_CREDIT', 'CC', 11231351),
(122, 'SRCB', '上海农商银行(借记卡)', 'SRCB_DEBIT', 'DC', 11231352),
(123, 'ZJTLCB', '浙江泰隆银行(借记卡)', 'ZJTLCB_DEBIT', 'DC', 11231353),
(124, 'SUZB', '苏州银行(借记卡)', 'SUZB_DEBIT', 'DC', 11231354),
(125, 'SXNX', '山西农信(借记卡)', 'SXNX_DEBIT', 'DC', 11231355),
(126, 'SXXH', '陕西信合(借记卡)', 'SXXH_DEBIT', 'DC', 11231356),
(127, 'ZJRCUB', '浙江农信(借记卡)', 'ZJRCUB_DEBIT', 'DC', 11231357),
(128, 'AE', 'AE(信用卡)', 'AE_CREDIT', 'CC', 11231358),
(129, 'TACCB', '泰安银行(信用卡)', 'TACCB_CREDIT', 'CC', 11231359),
(130, 'TACCB', '泰安银行(借记卡)', 'TACCB_DEBIT', 'DC', 11231360),
(131, 'TCRCB', '太仓农商行(借记卡)', 'TCRCB_DEBIT', 'DC', 11231361),
(132, 'TJBHB', '天津滨海农商行(信用卡)', 'TJBHB_CREDIT', 'CC', 11231362),
(133, 'TJBHB', '天津滨海农商行(借记卡)', 'TJBHB_DEBIT', 'DC', 11231363),
(134, 'TJB', '天津银行(借记卡)', 'TJB_DEBIT', 'DC', 11231364),
(135, 'TRCB', '天津农商(借记卡)', 'TRCB_DEBIT', 'DC', 11231365),
(136, 'TZB', '台州银行(借记卡)', 'TZB_DEBIT', 'DC', 11231366),
(137, 'URB', '联合村镇银行(借记卡)', 'URB_DEBIT', 'DC', 11231367),
(138, 'DYB', '东营银行(信用卡)', 'DYB_CREDIT', 'CC', 11231368),
(139, 'CSRCB', '常熟农商银行(借记卡)', 'CSRCB_DEBIT', 'DC', 11231369),
(140, 'CZB', '浙商银行(信用卡)', 'CZB_CREDIT', 'CC', 11231370),
(141, 'CZB', '浙商银行(借记卡)', 'CZB_DEBIT', 'DC', 11231371),
(142, 'CZCB', '稠州银行(信用卡)', 'CZCB_CREDIT', 'CC', 11231372),
(143, 'CZCB', '稠州银行(借记卡)', 'CZCB_DEBIT', 'DC', 11231373),
(144, 'DANDONGB', '丹东银行(信用卡)', 'DANDONGB_CREDIT', 'CC', 11231374),
(145, 'DANDONGB', '丹东银行(借记卡)', 'DANDONGB_DEBIT', 'DC', 11231375),
(146, 'DLB', '大连银行(信用卡)', 'DLB_CREDIT', 'CC', 11231376),
(147, 'DLB', '大连银行(借记卡)', 'DLB_DEBIT', 'DC', 11231377),
(148, 'DRCB', '东莞农商银行(信用卡)', 'DRCB_CREDIT', 'CC', 11231378),
(149, 'DRCB', '东莞农商银行(借记卡)', 'DRCB_DEBIT', 'DC', 11231379),
(150, 'CSRCB', '常熟农商银行(信用卡)', 'CSRCB_CREDIT', 'CC', 11231380),
(151, 'DYB', '东营银行(借记卡)', 'DYB_DEBIT', 'DC', 11231381),
(152, 'DYCCB', '德阳银行(借记卡)', 'DYCCB_DEBIT', 'DC', 11231382),
(153, 'FBB', '富邦华一银行(借记卡)', 'FBB_DEBIT', 'DC', 11231383),
(154, 'FDB', '富滇银行(借记卡)', 'FDB_DEBIT', 'DC', 11231384),
(155, 'FJHXB', '福建海峡银行(信用卡)', 'FJHXB_CREDIT', 'CC', 11231385),
(156, 'FJHXB', '福建海峡银行(借记卡)', 'FJHXB_DEBIT', 'DC', 11231386),
(157, 'FJNX', '福建农信银行(借记卡)', 'FJNX_DEBIT', 'DC', 11231387),
(158, 'FUXINB', '阜新银行(借记卡)', 'FUXINB_DEBIT', 'DC', 11231388),
(159, 'BOCDB', '承德银行(借记卡)', 'BOCDB_DEBIT', 'DC', 11231389),
(160, 'JSNX', '江苏农商行(借记卡)', 'JSNX_DEBIT', 'DC', 11231390),
(161, 'BOLFB', '廊坊银行(借记卡)', 'BOLFB_DEBIT', 'DC', 11231391),
(162, 'CCAB', '长安银行(信用卡)', 'CCAB_CREDIT', 'CC', 11231392),
(163, 'CBHB', '渤海银行(借记卡)', 'CBHB_DEBIT', 'DC', 11231393),
(164, 'CDRCB', '成都农商银行(借记卡)', 'CDRCB_DEBIT', 'DC', 11231394),
(165, 'BYK', '营口银行(借记卡)', 'BYK_DEBIT', 'DC', 11231395),
(166, 'BOZ', '张家口市商业银行(借记卡)', 'BOZ_DEBIT', 'DC', 11231396),
(167, 'CFT', 'CFT', 'CFT', 'CFT', 11231397),
(168, 'BOTSB', '唐山银行(借记卡)', 'BOTSB_DEBIT', 'DC', 11231398),
(169, 'BOSZS', '石嘴山银行(借记卡)', 'BOSZS_DEBIT', 'DC', 11231399),
(170, 'BOSXB', '绍兴银行(借记卡)', 'BOSXB_DEBIT', 'DC', 11231400),
(171, 'BONX', '宁夏银行(借记卡)', 'BONX_DEBIT', 'DC', 11231401),
(172, 'BONX', '宁夏银行(信用卡)', 'BONX_CREDIT', 'CC', 11231402),
(173, 'GDHX', '广东华兴银行(借记卡)', 'GDHX_DEBIT', 'DC', 11231403),
(174, 'BOLB', '洛阳银行(借记卡)', 'BOLB_DEBIT', 'DC', 11231404),
(175, 'BOJX', '嘉兴银行(借记卡)', 'BOJX_DEBIT', 'DC', 11231405),
(176, 'BOIMCB', '内蒙古银行(借记卡)', 'BOIMCB_DEBIT', 'DC', 11231406),
(177, 'BOHN', '海南银行(借记卡)', 'BOHN_DEBIT', 'DC', 11231407),
(178, 'BOD', '东莞银行(借记卡)', 'BOD_DEBIT', 'DC', 11231408),
(179, 'CQRCB', '重庆农商银行(信用卡)', 'CQRCB_CREDIT', 'CC', 11231409),
(180, 'CQRCB', '重庆农商银行(借记卡)', 'CQRCB_DEBIT', 'DC', 11231410),
(181, 'CQTGB', '重庆三峡银行(借记卡)', 'CQTGB_DEBIT', 'DC', 11231411),
(182, 'BOD', '东莞银行(信用卡)', 'BOD_CREDIT', 'CC', 11231412),
(183, 'CSCB', '长沙银行(借记卡)', 'CSCB_DEBIT', 'DC', 11231413),
(184, 'BOB', '北京银行(信用卡)', 'BOB_CREDIT', 'CC', 11231414),
(185, 'GDRCU', '广东农信银行(借记卡)', 'GDRCU_DEBIT', 'DC', 11231415),
(186, 'BOB', '北京银行(借记卡)', 'BOB_DEBIT', 'DC', 11231416),
(187, 'HRXJB', '华融湘江银行(借记卡)', 'HRXJB_DEBIT', 'DC', 11231417),
(188, 'HSBC', '恒生银行(借记卡)', 'HSBC_DEBIT', 'DC', 11231418),
(189, 'HSB', '徽商银行(信用卡)', 'HSB_CREDIT', 'CC', 11231419),
(190, 'HSB', '徽商银行(借记卡)', 'HSB_DEBIT', 'DC', 11231420),
(191, 'HUNNX', '湖南农信(借记卡)', 'HUNNX_DEBIT', 'DC', 11231421),
(192, 'HUSRB', '湖商村镇银行(借记卡)', 'HUSRB_DEBIT', 'DC', 11231422),
(193, 'HXB', '华夏银行(信用卡)', 'HXB_CREDIT', 'CC', 11231423),
(194, 'HXB', '华夏银行(借记卡)', 'HXB_DEBIT', 'DC', 11231424),
(195, 'HNNX', '河南农信(借记卡)', 'HNNX_DEBIT', 'DC', 11231425),
(196, 'BNC', '江西银行(借记卡)', 'BNC_DEBIT', 'DC', 11231426),
(197, 'BNC', '江西银行(信用卡)', 'BNC_CREDIT', 'CC', 11231427),
(198, 'BJRCB', '北京农商行(借记卡)', 'BJRCB_DEBIT', 'DC', 11231428),
(199, 'JCB', '晋城银行(借记卡)', 'JCB_DEBIT', 'DC', 11231429),
(200, 'JJCCB', '九江银行(借记卡)', 'JJCCB_DEBIT', 'DC', 11231430),
(201, 'JLB', '吉林银行(借记卡)', 'JLB_DEBIT', 'DC', 11231431),
(202, 'JLNX', '吉林农信(借记卡)', 'JLNX_DEBIT', 'DC', 11231432),
(203, 'JNRCB', '江南农商(借记卡)', 'JNRCB_DEBIT', 'DC', 11231433),
(204, 'JRCB', '江阴农商行(借记卡)', 'JRCB_DEBIT', 'DC', 11231434),
(205, 'JSHB', '晋商银行(借记卡)', 'JSHB_DEBIT', 'DC', 11231435),
(206, 'HAINNX', '海南农信(借记卡)', 'HAINNX_DEBIT', 'DC', 11231436),
(207, 'GLB', '桂林银行(借记卡)', 'GLB_DEBIT', 'DC', 11231437),
(208, 'GRCB', '广州农商银行(信用卡)', 'GRCB_CREDIT', 'CC', 11231438),
(209, 'GRCB', '广州农商银行(借记卡)', 'GRCB_DEBIT', 'DC', 11231439),
(210, 'GSB', '甘肃银行(借记卡)', 'GSB_DEBIT', 'DC', 11231440),
(211, 'GSNX', '甘肃农信(借记卡)', 'GSNX_DEBIT', 'DC', 11231441),
(212, 'GXNX', '广西农信(借记卡)', 'GXNX_DEBIT', 'DC', 11231442),
(213, 'GYCB', '贵阳银行(信用卡)', 'GYCB_CREDIT', 'CC', 11231443),
(214, 'GYCB', '贵阳银行(借记卡)', 'GYCB_DEBIT', 'DC', 11231444),
(215, 'GZNX', '贵州农信(借记卡)', 'GZNX_DEBIT', 'DC', 11231445),
(216, 'HAINNX', '海南农信(信用卡)', 'HAINNX_CREDIT', 'CC', 11231446),
(217, 'HKB', '汉口银行(借记卡)', 'HKB_DEBIT', 'DC', 11231447),
(218, 'HANAB', '韩亚银行(借记卡)', 'HANAB_DEBIT', 'DC', 11231448),
(219, 'HBCB', '湖北银行(信用卡)', 'HBCB_CREDIT', 'CC', 11231449),
(220, 'HBCB', '湖北银行(借记卡)', 'HBCB_DEBIT', 'DC', 11231450),
(221, 'HBNX', '湖北农信(信用卡)', 'HBNX_CREDIT', 'CC', 11231451),
(222, 'HBNX', '湖北农信(借记卡)', 'HBNX_DEBIT', 'DC', 11231452),
(223, 'HDCB', '邯郸银行(借记卡)', 'HDCB_DEBIT', 'DC', 11231453),
(224, 'HEBNX', '河北农信(借记卡)', 'HEBNX_DEBIT', 'DC', 11231454),
(225, 'HFB', '恒丰银行(借记卡)', 'HFB_DEBIT', 'DC', 11231455),
(226, 'HKBEA', '东亚银行(借记卡)', 'HKBEA_DEBIT', 'DC', 11231456),
(227, 'JCB', 'JCB(信用卡)', 'JCB_CREDIT', 'CC', 11231457),
(228, 'MASTERCARD', 'MASTERCARD(信用卡)', 'MASTERCARD_CREDIT', 'CC', 11231458),
(229, 'VISA', 'VISA(信用卡)', 'VISA_CREDIT', 'CC', 11231459);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cs_bank_config`
--
ALTER TABLE `cs_bank_config`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `cs_bank_config`
--
ALTER TABLE `cs_bank_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=230;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
