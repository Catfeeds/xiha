

-- 1、在so_school_order中添加so_coach_id字段
ALTER TABLE `cs_school_orders` 
ADD `so_coach_id` INT(11) NOT NULL COMMENT '教练ID' AFTER `so_user_id`;

-- 2、cs_users_info数据表中添加如下字段
ALTER TABLE `cs_users_info` 
ADD `license_name` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '牌照名称' AFTER `lesson_name`, 
ADD `balance` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0.00' COMMENT '钱包余额' AFTER `license_name`, 
ADD `is_paypass_activated` TINYINT(2) NOT NULL DEFAULT '2' COMMENT '支付密码是否被激活(1：是；2：否 )' AFTER `balance`, 
ADD `wallet_access_pass` INT(32) NOT NULL COMMENT '钱包的手势访问密码' AFTER `is_paypass_activated`, 
ADD `pay_pass` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付密码6位' AFTER `wallet_access_pass`, 
ADD `xiha_coin` INT(11) NOT NULL COMMENT '嘻哈币数量' AFTER `pay_pass`, 
ADD `signin_num` INT(11) NOT NULL COMMENT '连续签到数量' AFTER `xiha_coin`, 
ADD `signin_lasttime` BIGINT NOT NULL DEFAULT '0' COMMENT '签到最近时间' AFTER `signin_num`;

-- 3、cs_school_train_location 报名点表 添加order 排序
ALTER TABLE `cs_school_train_location` 
ADD `order` BIGINT UNSIGNED NOT NULL COMMENT '排序' AFTER `addtime`;

-- 4、报名：cs_coach 教练表
ALTER TABLE `cs_coach`
ADD `average_license_time` INT NOT NULL COMMENT '平均拿证时间' AFTER `s_coach_lisence_id`,
ADD `lesson2_pass_rate` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '科目二通过率' AFTER `average_license_time`, 
ADD `lesson3_pass_rate` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '科目三通过率' AFTER `lesson2_pass_rate`;

-- 5、Cs_school_shifts 驾校班制表（主要是驾校设置的班制表）
ALTER TABLE `cs_school_shifts` ADD `sh_license_name` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '牌照名称' AFTER `sh_description_1`, 
ADD `sh_license_id` INT(11) NOT NULL COMMENT '牌照ID' AFTER `sh_license_name`, 
ADD `sh_tag` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '班制标签' AFTER `sh_license_id`, 
ADD `sh_tag_id` INT(11) NOT NULL COMMENT '标签ID' AFTER `sh_tag`, 
ADD `is_promote` TINYINT(2) NOT NULL DEFAULT '1' COMMENT '是否促销(1：是, 2：否)' AFTER `sh_tag_id`, 
ADD `coupon_id`INT(11) NOT NULL COMMENT '优惠券ID' AFTER `is_promote`, 
ADD `order` BIGINT NOT NULL AFTER `coupon_id`,
ADD `updatetime` BIGINT NOT NULL DEFAULT '0' AFTER `order`;



-- 6、cs_coach_shifts 教练班制表（教练自主设置的招生班制表）
DROP TABLE IF EXISTS `cs_coach_shifts`;
CREATE TABLE IF NOT EXISTS `cs_coach_shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coach_id` int(11) NOT NULL,
  `sh_title` varchar(200) NOT NULL,
  `sh_money` decimal(6,2) NOT NULL,
  `sh_original_money` decimal(10,2) NOT NULL,
  `sh_type` int(11) NOT NULL,
  `sh_tag` varchar(200) NOT NULL,
  `sh_tag_id` int(11) NOT NULL,
  `sh_description` varchar(200) NOT NULL COMMENT '描述(如：最快7天上车，4人/车 自行前往)',
  `is_promote` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否促销（1：是，2：否）',
  `coupon_id` int(11) NOT NULL,
  `sh_license_id` int(11) NOT NULL,
  `sh_license_name` varchar(200) NOT NULL,
  `order` bigint(20) NOT NULL,
  `addtime` bigint(20) NOT NULL DEFAULT '0',
  `updatetime` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='教练班制表（教练自主设置的招生班制表）';



-- 7、Cs_coach_time_config 教练时间最终配置表（时间配置结果表）（新表）暂时命名为Cs_coach_time_config_new
DROP TABLE IF EXISTS `cs_coach_time_config_new`;
CREATE TABLE IF NOT EXISTS `cs_coach_time_config_new` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` varchar(20) NOT NULL COMMENT '8:00',
  `end_time` varchar(20) NOT NULL COMMENT '9:30',
  `start_hour` int(11) NOT NULL COMMENT '8',
  `start_minute` int(11) DEFAULT '0',
  `end_hour` int(11) NOT NULL COMMENT '9',
  `end_minute` int(11) DEFAULT '0' COMMENT '30',
  `lesson_time` varchar(20) NOT NULL COMMENT '课程时间（单位小时）',
  `lesson_name` varchar(200) NOT NULL COMMENT '科目二，科目三',
  `lesson_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `max_user_num` int(11) NOT NULL DEFAULT '1',
  `is_online` tinyint(2) NOT NULL COMMENT '是否在线（1：是，2：否）',
  `is_publish` tinyint(2) NOT NULL DEFAULT '1' COMMENT '最终发布时间配置（1 :未发布, 2:已发布）',
  `year` int(11) NOT NULL DEFAULT '0',
  `month` int(11) NOT NULL DEFAULT '0',
  `day` int(11) NOT NULL DEFAULT '0',
  `timestamp` bigint(20) NOT NULL DEFAULT '0' COMMENT '年月日的时间戳',
  `addtime` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='教练时间最终配置表（时间配置结果表）';

-- 8、Cs_time_config_template 模板列表表
DROP TABLE IF EXISTS `cs_time_config_template`;
CREATE TABLE IF NOT EXISTS `cs_time_config_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `temp_id` int(11) NOT NULL COMMENT '模板ID',
  `start_time` int(11) NOT NULL COMMENT '（如：8:00）',
  `end_time` int(11) NOT NULL COMMENT '（如：9:30）',
  `start_hour` int(11) NOT NULL COMMENT '8',
  `start_minute` int(4) DEFAULT '0',
  `end_hour` int(11) NOT NULL COMMENT '9',
  `end_minute` int(4) DEFAULT '0' COMMENT '30',
  `lesson_time` int(11) NOT NULL COMMENT '课程时间（单位小时）',
  `lesson_name` varchar(20) NOT NULL COMMENT '科目二，科目三',
  `lesson_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `max_user_num` int(2) NOT NULL DEFAULT '1' COMMENT '最大可预约用户数（暂时不开放的 默认一个人）',
  `is_online` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否在线（1：是，2：否 ，3可休息）',
  `addtime` bigint(20) NOT NULL,
  `updatetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='模板列表表';

-- 9、cs_template_relationship  匹配不同角色不同星期的模板关联（驾校，教练，嘻哈...）
DROP TABLE IF EXISTS `cs_template_relationship`;
CREATE TABLE IF NOT EXISTS `cs_template_relationship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `temp_owner_id` int(11) NOT NULL COMMENT '模板所有者ID（驾校ID, 教练ID...）',
  `temp_type` tinyint(2) NOT NULL COMMENT '模板角色分类（1：教练，2：驾校...）',
  `weekday` int(11) NOT NULL COMMENT '星期几（1代表星期一 2，3，4，5，6，7）',
  `is_default` tinyint(2) NOT NULL COMMENT '是否针对于星期几的默认（1：是，2：否）',
  `is_online` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否这一天在线（1：是，2：否）',
  `addtime` bigint(20) NOT NULL,
  `updatetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='匹配不同角色不同星期的模板关联（驾校，教练，嘻哈...）';

-- 10、cs_user_tag 用户自定义标签表（用户包括教练，学员...）用户不可修改 只能选择或不选择
DROP TABLE IF EXISTS `cs_user_tag`;
CREATE TABLE IF NOT EXISTS `cs_user_tag` (
  `id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL COMMENT '系统的标签配置id（教练不选择系统标签的话为0）',
  `tag_name` varchar(200) NOT NULL,
  `tag_slug` varchar(200) NOT NULL,
  `user_type` tinyint(2) NOT NULL COMMENT '用户类型（0:学员,1:教练...）',
  `user_id` int(11) NOT NULL COMMENT '用户id（0:学员,1:教练...）',
  `is_system` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否系统标签（1:是,2:否；用户不能修改）',
  `order` bigint(20) UNSIGNED NOT NULL,
  `addtime` bigint(20) NOT NULL,
  `apdatetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户自定义标签表（用户包括教练，学员...）用户不可修改 只能选择或不选择';

-- 11、cs_system_tag_config 系统的标签配置表（学员和教练不同角色共用）
DROP TABLE IF EXISTS `cs_system_tag_config`;
CREATE TABLE IF NOT EXISTS `cs_system_tag_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(200) NOT NULL COMMENT '标签中文名',
  `tag_slug` varchar(200) NOT NULL COMMENT '标签英文名',
  `user_type` int(11) NOT NULL COMMENT '标签类别（适用于0:学员,1,教练，2,驾校...）',
  `order` bigint(20) UNSIGNED NOT NULL,
  `addtime` bigint(20) NOT NULL,
  `updatetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统的标签配置表（学员和教练不同角色共用）';


-- 12、cs_coin_goods 金币商品表
DROP TABLE IF EXISTS `cs_coin_goods`;
CREATE TABLE IF NOT EXISTS `cs_coin_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_name` varchar(200) NOT NULL,
  `goods_desc` varchar(255) NOT NULL,
  `goods_original_price` decimal(10,2) NOT NULL COMMENT '商品原金币价格',
  `goods_final_price` decimal(10,2) NOT NULL COMMENT '商品最终金币价格',
  `goods_real_price` decimal(10,2) NOT NULL COMMENT '商品真实价格',
  `goods_total_num` int(11) NOT NULL COMMENT '总数',
  `goods_images_url` varchar(255) NOT NULL COMMENT '商品图片',
  `goods_detail` text NOT NULL COMMENT '商品详情',
  `goods_expiretime` bigint(20) NOT NULL COMMENT '商品过期时间',
  `is_hot` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否热销（1：是，2：否）',
  `is_recommend` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否推荐（1：是，2：否）',
  `is_promote` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否促销（1：是，2：否）',
  `goods_order` int(10) UNSIGNED NOT NULL COMMENT '自定义排序',
  `is_publish` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否发布商品（1：是，2：否）',
  `order` bigint(20) UNSIGNED NOT NULL,
  `addtime` bigint(20) NOT NULL,
  `updatetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='金币商品表';

-- 13、cs_coingoods_category 金币商品分类 
DROP TABLE IF EXISTS `cs_coingoods_category`;
CREATE TABLE IF NOT EXISTS `cs_coingoods_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cate_name` varchar(200) NOT NULL COMMENT '分类名称',
  `cate_desc` varchar(255) NOT NULL COMMENT '分类描述',
  `cate_order` int(10) UNSIGNED NOT NULL COMMENT '分类排序',
  `order` int(10) UNSIGNED NOT NULL,
  `addtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='金币商品分类';

-- 14、cs_exchange_orders 兑换记录表 （相当于订单表）
DROP TABLE IF EXISTS `cs_exchange_orders`;
CREATE TABLE IF NOT EXISTS `cs_exchange_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_name` varchar(200) NOT NULL COMMENT '商品名称',
  `mch_name` varchar(200) NOT NULL COMMENT '商户名称',
  `goods_original_price` decimal(10,2) NOT NULL COMMENT '商品原来金币价格',
  `goods_final_price` decimal(10,2) NOT NULL COMMENT '商品最终金币价格',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `exchange_no` varchar(20) NOT NULL COMMENT '兑换订单号',
  `pay_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '支付状态（1、金币支付 1、支付成功 2、支付失败）',
  `exchange_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '兑换状态 （1、兑换成功，2、正在发货中，3、未抽中奖品，4、兑换失败）',
  `exchange_num` int(11) NOT NULL COMMENT '兑换数量',
  `addtime` bigint(20) NOT NULL COMMENT '兑换时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=' 兑换记录表';

-- 15、cs_coin_rule 金币规则表
DROP TABLE IF EXISTS `cs_coin_rule`;
CREATE TABLE IF NOT EXISTS `cs_coin_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL COMMENT '名称（区别是发帖，完善信息，签到...）',
  `slug` varchar(200) NOT NULL COMMENT '规则英文缩写（唯一的）',
  `description` varchar(255) NOT NULL COMMENT '规则描述',
  `coin_num` int(11) NOT NULL COMMENT '金币数',
  `rule_starttime` bigint(20) NOT NULL COMMENT '金币当前规则开始时间',
  `rule_endtime` bigint(20) NOT NULL COMMENT '金币当前规则结束时间',
  `addtime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='金币规则表';

-- 16、首页：金币商城 cs_task_records 完成任务记录表
DROP TABLE IF EXISTS `cs_task_records`;
CREATE TABLE IF NOT EXISTS `cs_task_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL COMMENT '任务标题',
  `signin_coin_num` int(11) NOT NULL COMMENT '得金币数量',
  `addtime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='完成任务记录表';

-- 17、cs_feedback 反馈表 app反馈
DROP TABLE IF EXISTS `cs_feedback`;
CREATE TABLE IF NOT EXISTS `cs_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) NOT NULL COMMENT '反馈的内容',
  `user_id` int(11) NOT NULL COMMENT '反馈的用户id',
  `addtime` bigint(20) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='app反馈';

-- 18、cs_transaction_records 交易记录表（提现记录，充值记录，领取红包记录，发红包记录...）
DROP TABLE IF EXISTS `cs_transaction_records`;
CREATE TABLE IF NOT EXISTS `cs_transaction_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_money` varchar(200) NOT NULL DEFAULT '0.00',
  `transaction_no` varchar(200) NOT NULL COMMENT '交易单号',
  `transaction_body` varchar(200) NOT NULL COMMENT '商品名称',
  `transaction_detail` varchar(200) NOT NULL COMMENT '商品详情',
  `transaction_mch_name` varchar(200) NOT NULL COMMENT '交易商户名称(如：从安徽嘻哈网络科技有限公司提现的)',
  `transaction_receiver_no` varchar(200) NOT NULL COMMENT '收款账户',
  `transaction_receiver_name` varchar(200) NOT NULL COMMENT '收款名称',
  `transaction_status` int(2) NOT NULL COMMENT '交易状态',
  `transaction_starttime` bigint(20) NOT NULL COMMENT '交易开始时间',
  `transaction_endtime` bigint(20) NOT NULL COMMENT '交易结束时间',
  `transaction_pay_type` int(2) NOT NULL COMMENT '支付方式',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='交易记录表';

-- 19、cs_pay_account_config 账户支持配置表
DROP TABLE IF EXISTS `cs_pay_account_config`;
CREATE TABLE IF NOT EXISTS `cs_pay_account_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_name` varchar(200) NOT NULL COMMENT '账户名称 包括支付宝，微信等 不限于银行账户',
  `account_slug` varchar(200) NOT NULL COMMENT '英文名',
  `account_description` varchar(255) NOT NULL COMMENT '账户说明',
  `is_open` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否开启（1：是，2：否）',
  `is_bank` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否是银行账号（1：是，2：否）',
  `order` bigint(20) NOT NULL COMMENT '排序',
  `addtime` bigint(20) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='账户支持配置表';

-- 21、cs_users_wallet 用户所绑定的银行账户关联表（一个人对应多个账户）
DROP TABLE IF EXISTS `cs_users_wallet`;
CREATE TABLE IF NOT EXISTS `cs_users_wallet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `bank_user_name` varchar(200) NOT NULL COMMENT '账户名（银行开户名）',
  `bank_account` varchar(200) NOT NULL COMMENT '银行账号',
  `bank_name` varchar(200) NOT NULL COMMENT '银行名称',
  `bank_phone` varchar(200) NOT NULL COMMENT '银行预留手机号',
  `bank_identifyId` varchar(255) NOT NULL COMMENT '银行预留的身份证信息',
  `addtime` bigint(20) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户所绑定的银行账户关联表（一个人对应多个账户）';

-- 22、cs_coupon 优惠券表（用于不同角色所设置的券， 优惠券，学车券，活动券）
DROP TABLE IF EXISTS `cs_coupon`;
CREATE TABLE IF NOT EXISTS `cs_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_type` int(2) NOT NULL DEFAULT '1' COMMENT '角色类别（1：教练，2：驾校）',
  `owner_id` int(11) NOT NULL COMMENT '券的所有者id',
  `owner_name` varchar(32) NOT NULL COMMENT '驾校名称或者教练名称',
  `coupon_total_num` int(10) UNSIGNED NOT NULL COMMENT '券的总数量',
  `coupon_get_num` int(10) UNSIGNED NOT NULL COMMENT '券被领取数目',
  `addtime` bigint(20) NOT NULL COMMENT '添加时间',
  `expiretime` bigint(20) NOT NULL COMMENT '过期时间',
  `coupon_value` varchar(32) NOT NULL COMMENT '优惠面值（100元，95折...）',
  `coupon_category` int(2) NOT NULL COMMENT '券的种类（1、现金券，2、打折券...）',
  `coupon_code` varchar(32) NOT NULL COMMENT '兑换码',
  `coupon_limit_num` int(11) NOT NULL COMMENT '领取数量限制',
  `province_id` int(11) NOT NULL DEFAULT '0' COMMENT '如果是全国的话 默认值就是0',
  `city_id` int(11) NOT NULL DEFAULT '0',
  `area_id` int(11) NOT NULL DEFAULT '0',
  `order` bigint(20) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='优惠券表（用于不同角色所设置的券， 优惠券，学车券，活动券）';

-- 23、cs_user_coupon 学车领取券表
DROP TABLE IF EXISTS `cs_user_coupon`;
CREATE TABLE IF NOT EXISTS `cs_user_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(15) NOT NULL COMMENT '领取人的姓名',
  `user_phone` bigint(20) NOT NULL COMMENT '领取人的手机号码',
  `title` varchar(32) NOT NULL COMMENT '标题',
  `description` varchar(64) NOT NULL COMMENT '描述',
  `coupon_code` varchar(32) NOT NULL COMMENT '兑换码',
  `coupon_value` varchar(32) NOT NULL COMMENT '优惠面值',
  `coupon_category` int(10) NOT NULL COMMENT '优惠券的类别（1：现金券，2：打折券等等）',
  `coupon_sender_owner_id` int(11) NOT NULL COMMENT '发放学车券的角色（教练，嘻哈自营，汽车厂家...）',
  `coupon_status` int(2) NOT NULL DEFAULT '1' COMMENT '学车券状态1： 未使用，2： 已使用，:3：已过期 ，4：已删除',
  `coupon_type` int(2) NOT NULL DEFAULT '1' COMMENT '用于区别自己领取还是系统推送的， 后台设置随机发放，学员自己领取（1：自己领取，2：系统推送）',
  `addtime` bigint(20) NOT NULL COMMENT '领取时间',
  `expiretime` bigint(20) NOT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学车领取券表';

-- 券的种类表
DROP TABLE IF EXISTS `cs_coupon_category`;
CREATE TABLE IF NOT EXISTS `cs_coupon_category` (
  `id` int(11) NOT NULL,
  `cate_name` varchar(200) NOT NULL COMMENT '券分类名称',
  `cate_desc` varchar(255) NOT NULL COMMENT '券分类描述',
  `coupon_rule` varchar(255) NOT NULL COMMENT '券规则(规则可添加)',
  `addtime` bigint(20) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='券分类表（优惠券，学车优惠费用券，学车优惠小时券等活动券）';



-- 后期修改的相关字段
ALTER TABLE `cs_coin_goods` ADD `cate_id` INT NOT NULL COMMENT '商品分类id' AFTER `id`;

ALTER TABLE `cs_template_relationship` ADD `temp_name` VARCHAR(255) NOT NULL COMMENT '模板名称' AFTER `id`;

ALTER TABLE `cs_time_config_template` CHANGE `start_time` `start_time` VARCHAR(11) NOT NULL COMMENT '（如：8:00）', CHANGE `end_time` `end_time` VARCHAR(11) NOT NULL COMMENT '（如：9:30）';

ALTER TABLE `cs_time_config_template` CHANGE `lesson_time` `lesson_time` VARCHAR(11) NOT NULL COMMENT '课程时间（单位小时）', CHANGE `addtime` `addtime` BIGINT(20) NOT NULL DEFAULT '0', CHANGE `updatetime` `updatetime` BIGINT(20) NOT NULL DEFAULT '0';

-- 修改system_tag_config表中的user_type、addtime、updatetime字段
ALTER TABLE `cs_system_tag_config` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `tag_name` `tag_name` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标签中文名', CHANGE `tag_slug` `tag_slug` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标签英文名', CHANGE `user_type` `user_type` INT(11) NOT NULL COMMENT '标签类别（适用于1:学员,2:教练...）', CHANGE `order` `order` BIGINT(20) UNSIGNED NOT NULL, CHANGE `addtime` `addtime` BIGINT(20) NOT NULL DEFAULT '0', CHANGE `updatetime` `updatetime` BIGINT(20) NOT NULL DEFAULT '0';


-- 将排序字段的值自定义为0
ALTER TABLE `cs_system_tag_config` CHANGE `order` `order` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0';

-- 将user_tag中id设置为自增长
ALTER TABLE `cs_user_tag` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT

ALTER TABLE `cs_system_tag_config` CHANGE `user_type` `user_type` TINYINT(2) NOT NULL COMMENT '标签类别（适用于1:学员,2:教练，3：驾校）';

ALTER TABLE `cs_user_tag` CHANGE `user_type` `user_type` TINYINT(2) NOT NULL COMMENT '用户类型（1:学员,2:教练,3:驾校）';

ALTER TABLE `cs_user_tag` CHANGE `user_id` `user_id` INT(11) NOT NULL COMMENT '用户id';

ALTER TABLE `cs_pay_account_config` CHANGE `order` `order` BIGINT(20) NOT NULL DEFAULT '0' COMMENT '排序', CHANGE `addtime` `addtime` BIGINT(20) NOT NULL DEFAULT '0' COMMENT '添加时间';

ALTER TABLE `cs_transaction_records` CHANGE `addtime` `addtime` INT(11) NOT NULL DEFAULT '0' COMMENT '添加时间';

















