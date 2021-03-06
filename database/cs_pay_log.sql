DROP TABLE IF EXISTS `cs_pay_log`;

CREATE TABLE `cs_pay_log` (
    `id` BIGINT(16) UNSIGNED AUTO_INCREMENT COMMENT '主键',
    `title` CHAR(127) NOT NULL COMMENT '商品名称',
    `desc` CHAR(255) NOT NULL COMMENT '商品描述',
    `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '商品金额',
    `order_id` CHAR(127) NOT NULL COMMENT '订单号',
    `trade_id` CHAR(127) COMMENT '交易号',
    `attach_params` CHAR(127) COMMENT '附加数据包',
    `gateway` CHAR(32) COMMENT '支付网关，例如：Alipay，Wechatpay，Unionpay',
    `pay_method` SMALLINT COMMENT '支付方式，兼容性考虑。支付宝1，微信3，银联4',
    `pay_amount` DECIMAL(10,2) COMMENT '实付金额',
    `refund_amount` DECIMAL(10,2) COMMENT '实付金额',
    `transfer_amount` DECIMAL(10,2) COMMENT '转账金额',
    `trade_status` CHAR(32) COMMENT '交易状态',
    `trade_create` datetime COMMENT '交易创建于',
    `trade_payment` datetime COMMENT '交易付款于',
    `trade_refund` datetime COMMENT '交易退款于',
    `trade_transfer` datetime COMMENT '交易转账于',
    `trade_close` datetime COMMENT '交易关闭于',
    `pay_user_type` CHAR(32) COMMENT '支付用户类型',
    `pay_user_id` CHAR(32) COMMENT '支付用户id',
    `pay_user_logon_id` CHAR(32) COMMENT '支付用户登录id',
    `remark` CHAR(255) COMMENT '备注',
    `app_id` CHAR(32) COMMENT '商户应用ID',
    `created_at` INT UNSIGNED COMMENT '创建于',
    `updated_at` INT UNSIGNED COMMENT '更新于',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付流水表';
