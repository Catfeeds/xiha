<?php
	header ( 'Content-type:text/html;charset=utf-8' );
	include_once 'sdk/acp_service.php';
	require_once("../../include/common.php");

	$post = $_REQUEST;
	/*{
	    "accessType": "0",
	    "bizType": "000201",
	    "certId": "68759585097",
	    "currencyCode": "156",
	    "encoding": "utf-8",
	    "merId": "802310053110697",
	    "orderId": "2016041651101101",
	    "queryId": "201604161602273177768",
	    "reqReserved": "{\"order_type\":\"appoint\"}",
	    "respCode": "00",
	    "respMsg": "Success!",
	    "settleAmt": "13000",
	    "settleCurrencyCode": "156",
	    "settleDate": "0416",
	    "signMethod": "01",
	    "traceNo": "317776",
	    "traceTime": "0416160227",
	    "txnAmt": "13000",
	    "txnSubType": "01",
	    "txnTime": "20160416160227",
	    "txnType": "01",
	    "version": "5.0.0",
	    "signature": "W7+76TJ89lGmxvVflUbrnAPxnywcAVCDgT3tvevvotXisRakTnGQ4f5P2DJNksYwSQOfLRk0+PGqexdWehANl84eb58rQHUF3JkMTD+8fZkatciovIxRneYHFgcHC8q2eDuw/m+9HirTBE3clAUBKac6itHpmn1YOQBNFID4lr+hbvxLwa/0e7C8yTaR+ojWbAuk6m35jyR9ra7SliIzS7KcsyA+3rvIH+Fqy7RwBuwbVx+3loaDhfxwizkbA3BhCaiavXI6rnGJCX4AO1D//MKq+ioQPIWyqWCflm+6ne0iQDHuacIw96rcYoqYVC4L4vvrtn9TJAccvS0p1wImAA=="
	}*/


	$phplog = new PhpLog(SDK_LOG_FILE_PATH, 'PRC', SDK_LOG_LEVEL);
	$phplog->Log('银联支付json：'.json_encode($post), SDK_LOG_LEVEL, SDK_LOG_FILE_PATH, 1);
	$phplog->Log('银联支付string：'.htmlentities(createLinkString( $post , false, true )) . "<br>\n", SDK_LOG_LEVEL, SDK_LOG_FILE_PATH, 1);

	if(!empty($post)) {
		$reqReserved = $post['reqReserved'];  // order_type
		$respCode = $post['respCode']; // 00
		$certId = $post['certId']; // 证书ID
		$merId = $post['merId']; // 商户代码
		$orderId = $post['orderId']; // 订单号
		$queryId = $post['queryId']; // 商户平台订单号 ？？
		$txnAmt = $post['txnAmt']; // 价格
		$txnTime = $post['txnTime']; // 时间
		$order_info = json_decode($reqReserved, true);
		$order_status = 1;
		if($order_info['order_type'] == 'signup') {
			$order_status = 1;
		} else if($order_info['order_type'] == 'appoint') {
			$order_status = 1;
		}

		$data = array(
	        'order_no'      => $orderId,      //订单号
	        'order_type'    => $order_info['order_type'],    //交易类型
	        'order_money'   => $txnAmt / 100,   //支付金额    
	        'trade_no'      => $queryId,      //银联交易号
	        'pay_type'      => 4,              //支付方式
	        'order_status'  => $order_status,           //订单状态
	        'zhifu_time'    =>  date('Y-m-d H:i:s',$txnTime)    //交易付款时间
        );
		$res = request_post(PAYHOST.'api/v2/order/set_order_status.php', $data);
		$result = json_decode($res, true);

		if($result['code'] == '200') {
			if($order_info['order_type'] == 'signup') {
				$phplog->Log('回调日志：[ :订单类型 ] 报名驾校 [ :status ] 状态设置成功！[ :支付方式 ] 银联支付 [ :订单号 ] ' .$orderId. '[ :params ]' . json_encode($post), SDK_LOG_LEVEL, SDK_LOG_FILE_PATH, 1);

			} elseif($order_info['order_type'] == 'appoint') {
				$phplog->Log('回调日志：[ :订单类型 ] 预约学车 [ :status ] 状态设置成功！[ :支付方式 ] 银联支付 [ :订单号 ] ' .$orderId. '[ :params ]' . json_encode($post), SDK_LOG_LEVEL, SDK_LOG_FILE_PATH, 1);
			}
				
		} else {
			if($order_info['order_type'] == 'signup') {
				$phplog->Log('回调日志：[ :订单类型 ] 报名驾校 [ :status ] 状态设置失败！[ :支付方式 ] 银联支付 [ :订单号 ] ' .$orderId. '[ :params ]' . json_encode($post), SDK_LOG_LEVEL, SDK_LOG_FILE_PATH, 1);

			} elseif($order_info['order_type'] == 'appoint') {
				$phplog->Log('回调日志：[ :订单类型 ] 预约学车 [ :status ] 状态设置失败！[ :支付方式 ] 银联支付 [ :订单号 ] ' .$orderId. '[ :params ]' . json_encode($post), SDK_LOG_LEVEL, SDK_LOG_FILE_PATH, 1);

			}
		}
	}

    /**
     * 模拟post进行url请求
     * @param string $url
     * @param array $post_data
     */
    function request_post($url = '', $post_data = array()) {
        if (empty($url) || empty($post_data)) {
            return false;
        }
        
        $o = "";
        foreach ( $post_data as $k => $v ) 
        { 
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        $postUrl = $url;
        $curlPost = $post_data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        
        return $data;
    }

?>