<?php

/**
 * 枚举类型定义
 */

!defined('IN_FILE') && exit('Access Denied');

// 订单状态
$_ENUM['status'] = array(
	'active' => '等待付款',
	'dead' => '已作废',
	'finish' => '已完成',
);

// 付款状态
$_ENUM['order_pay_status'] = array(
	0 => '等待付款',
	1 => '已付款',
	2 => '已付款至担保方',
	3 => '等待补款 ',
	4 => '部分退款',
	5 => '已退款',
);

// 发货状态
$_ENUM['ship_status'] = array(
	0 => '正在备货...',
	1 => '已发货',
	2 => '部分发货',
	3 => '部分退货',
	4 => '已退货',
);

?>