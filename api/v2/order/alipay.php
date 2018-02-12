<?php 
/**
	 * 报名驾校/预约学车待完成未付款订单支付宝支付结算
	 * @param $order_no  订单号
	 * @param $order_time  下单时间
	 * @param $shifts_id  班制id  如果是预约学车$shifts_id = -1
	 * @param $order_type  订单类型
	 * @param $order_money  订单价格
	 * @return 
	 * @author sunweiwei
	 **/
 	require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';
    require '../pay/include/common.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','alipay');
 	$app->response->headers->set('Content-Type', 'application/json;charset=utf8');
	$app->run();

	function alipay() {
		Global $app, $crypt;
		$r = $app->request();
        setapilog(serialize($r->params()));
		 //验证请求方式 POST
        if ( !$r->isPost() ) {
            setapilog('[send_appoint_orders] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            exit( json_encode(array('code' => 106, 'data' => '请求错误')) );
        }
        //验证输入参数
        $validate_ok = validate(
				            array(	'order_no' => 'INT',				  
				            		'shifts_id' => 'INT',
				            		'order_type' => 'STRING',
				            		'order_money' => 'INT',
				            		), 
				            $r->params()
			            );
        if ( !$validate_ok['pass'] ) {
            exit( json_encode($validate_ok['data']) );
        }
        //获取参数
        $order_no = $r->params('order_no');//订单号
	    $order_time = $r->params('order_time'); //下单时间
	    $shifts_id = $r->params('shifts_id');//班制id  
	    $order_type = $r->params('order_type');//订单类型
	    $order_money = $r->params('order_money');//订单价格

	   if(empty($order_time)) {
			$data = array('code'=>101, 'data'=>'请求参数错误');
			echo json_encode($data);
			exit();
		}
      	$alipay = array();
      	if ($order_type == 'signup') {
		    $body = json_encode(array('order_no'=>$order_no, 'order_time'=>$order_time, 'shifts_id'=>$shifts_id, 'order_type'=>$order_type,'order_money'=>$order_money));
		    $subject = '嘻哈学车APP报名驾校支付';

		} elseif ($order_type == 'appoint') {
      		$body = json_encode(array('order_no'=>$order_no, 'order_time'=>$order_time,'order_type'=>$order_type,'order_money'=>$order_money) );
		    $subject = '嘻哈学车APP预约学车支付';
      	}
        $param = "_input_charset=\"utf-8\"&body=\"".$body."\"&it_b_pay=\"30m\"&notify_url=\"".PAYHOST."api/v2/pay/apppay/alipay/notify_url.php\"&out_trade_no=\"".$order_no."\"&partner=\"".PARTNER."\"&seller=\"".PARTNER."\"&service=\"mobile.securitypay.pay\"&show_url=\"m.alipay.com\"&subject=\"".$subject."\"&total_fee=\"".$order_money."\"";
      	$rsa = rsaSign($param, APP_PRIVATE_KEY_PATH);
		$alipay = array(
            'orderstring'   => $param, 
            'signstring'    => $rsa,
        );
	    $data = array('code'=>200, 'data'=>$alipay);
		echo json_encode($data);
	}

	/**
	 * RSA签名
	 * @param $data 待签名数据
	 * @param $private_key_path 商户私钥文件路径
	 * return 签名结果
	 */
	function rsaSign($data, $private_key_path) {
	    $priKey = file_get_contents($private_key_path);
	    $res = openssl_get_privatekey($priKey);
	    openssl_sign($data, $sign, $res);
	    openssl_free_key($res);
		//base64编码
	    $sign = urlencode(base64_encode($sign));
	    return $sign;
	}

 ?>