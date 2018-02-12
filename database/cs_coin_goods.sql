-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-10-14 07:04:24
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
-- 表的结构 `cs_coin_goods`
--

DROP TABLE IF EXISTS `cs_coin_goods`;
CREATE TABLE IF NOT EXISTS `cs_coin_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cate_id` int(11) NOT NULL COMMENT '商品分类id',
  `goods_name` varchar(200) NOT NULL,
  `goods_desc` varchar(255) NOT NULL,
  `goods_original_price` int(11) NOT NULL COMMENT '商品原金币（嘻哈币）价格',
  `goods_final_price` int(11) NOT NULL COMMENT '系统默认可以减去的嘻哈币',
  `goods_original_money` decimal(10,2) NOT NULL COMMENT '商品原价格',
  `goods_final_money` decimal(10,2) NOT NULL COMMENT '商品最终价格（商品原始价格-商品嘻哈币价格）',
  `goods_total_num` int(11) NOT NULL COMMENT '总数',
  `goods_images_url` varchar(255) NOT NULL COMMENT '商品图片',
  `goods_detail` text NOT NULL COMMENT '商品详情',
  `goods_expiretime` bigint(20) NOT NULL COMMENT '商品过期时间',
  `is_hot` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否热销（1：是，2：否）',
  `is_recommend` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否推荐（1：是，2：否）',
  `is_promote` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否促销（1：是，2：否）',
  `goods_order` int(10) UNSIGNED NOT NULL COMMENT '自定义排序',
  `is_publish` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否发布商品（1：是，2：否）',
  `addtime` bigint(20) NOT NULL,
  `updatetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='金币商品表';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
