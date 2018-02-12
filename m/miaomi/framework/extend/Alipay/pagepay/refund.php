<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>退款</title>
</head>
<?php
	require_once dirname(dirname(__FILE__)).'/config.php';
	require_once dirname(__FILE__).'/service/AlipayTradeService.php';
	require_once dirname(__FILE__).'/buildermodel/AlipayTradeRefundContentBuilder.php';

	$RequestBuilder=new AlipayTradeRefundContentBuilder();
	$RequestBuilder->setOutTradeNo($out_trade_no);
	$RequestBuilder->setTradeNo($trade_no);
	$RequestBuilder->setRefundAmount($refund_amount);
	$RequestBuilder->setOutRequestNo($out_request_no);
	$RequestBuilder->setRefundReason($refund_reason);
	$aop = new AlipayTradeService($config);

	/**
	 * alipay.trade.refund (统一收单交易退款接口)
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @return $response 支付宝返回的信息
	 */
	$response = $aop->Refund($RequestBuilder);
	var_dump($response);
?>
</body>
</html>