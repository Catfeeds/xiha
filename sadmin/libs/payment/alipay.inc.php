<?php

/**
 * 支付宝手机网站支付
 */

!defined('IN_FILE') && exit('Access Denied');

// 包含配置文件及类库
require_once MOBILE_ROOT.'./libs/payment/alipay/alipay_config.inc.php';
require_once MOBILE_ROOT.'./libs/payment/alipay/alipay_submit.class.php';

// 请求参数
$request['format'] = "xml";
$request['v'] = "2.0";
$request['req_id'] = date('Ymdhis');
$request['notify_url'] = "http://www.kut.cn/mobile/alipay_notify.php";
$request['call_back_url'] = "http://www.kut.cn/mobile/alipay_callback.php";
$request['merchant_url'] = "http://www.kut.cn/mobile/index.php";
$request['seller_email'] = 'lingzhi.gao@gtimescn.com';
$request['out_trade_no'] = $order['order_sn'];
$request['subject'] = '订单'.$order['order_sn'];
$request['total_fee'] = $order['cal_order_amount'];

// 获取令牌
$request_data = <<<EOT
<direct_trade_create_req>
  <notify_url>{$request['notify_url']}</notify_url>
  <call_back_url>{$request['call_back_url']}</call_back_url>
  <seller_account_name>{$request['seller_email']}</seller_account_name>
  <out_trade_no>{$request['out_trade_no']}</out_trade_no>
  <subject>{$request['subject']}</subject>
  <total_fee>{$request['total_fee']}</total_fee>
  <merchant_url>{$request['merchant_url']}</merchant_url>
</direct_trade_create_req>
EOT;
$param_token = array(
	"service" => "alipay.wap.trade.create.direct",
	"partner" => $alipay_config['partner'],
	"sec_id" => $alipay_config['sign_type'],
	"format" => $request['format'],
	"v" => $request['v'],
	"req_id" => $request['req_id'],
	"req_data" => $request_data,
	"_input_charset" => $alipay_config['input_charset']
);
$alipaySubmit = new AlipaySubmit($alipay_config);
$param_token = $alipaySubmit->parseResponse(urldecode($alipaySubmit->buildRequestHttp($param_token)));
$request_token = $param_token['request_token'];

// 验证令牌并执行
$request_data = "<auth_and_execute_req><request_token>{$request_token}</request_token></auth_and_execute_req>";
$parameter = array(
	"service" => "alipay.wap.auth.authAndExecute",
	"partner" => $alipay_config['partner'],
	"sec_id" => $alipay_config['sign_type'],
	"format" => $request['format'],
	"v" => $request['v'],
	"req_id" => $request['req_id'],
	"req_data" => $request_data,
	"_input_charset" => $alipay_config['input_charset']
);
$alipaySubmit = new AlipaySubmit($alipay_config);
$response_html = $alipaySubmit->buildRequestForm($parameter, 'get', '确认');

// 更新订单状态为付款中
$muser->updateOrderStatus($orderid, 'pay', '1');

// 显示执行结果
header("Content-Type:text/html; charset=utf-8");
echo '<div style="display:none;">'.$response_html.'</div>';
echo '<div style="font-size:1em;line-height:1.3;font-family:sans-serif;color:#333;text-shadow:0 1px 0 #f3f3f3;">页面跳转中, 请稍候...</div>';

?>