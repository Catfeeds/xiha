<?php
	/**
	 * 报名驾校/预约学车待完成未付款订单银联支付结算
	 * @param $order_no  订单号
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
	$app->any('/','Unipay');
 	$app->response->headers->set('Content-Type', 'application/json;charset=utf8');
	$app->run();

	function Unipay() {
		Global $app, $crypt;
		$r = $app->request();
		if ( !$r->isPost() ) {
            setapilog('[unipay] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            echo json_encode(array('code' => 106, 'data' => '请求错误'));
            return;
        }
        //验证输入参数
        $validate_ok = validate(
		            array(	
		            	'order_no' => 'INT',				  
	            		'order_type' => 'STRING',
	            		'order_money' => 'INT',
		    		), 
		            $r->params()
	            );
        if ( !$validate_ok['pass'] ) {
            echo json_encode($validate_ok['data']);
            return;
        }
        //获取参数
        $order_no = $r->params('order_no');//订单号
	    $order_type = $r->params('order_type');//订单类型
	    $order_money = $r->params('order_money');//订单价格

		$url = PAYHOST."/api/v2/pay/apppay/unipay/return_url.php";
	    $params = array(
	        'order_no'      => $order_no,
	        'order_time'    => date('YmdHis', time()),
	        'order_money'   => $order_money * 100,
	        'order_type'      => $order_type
	    );
	    $res = request_post($url, $params);
	    $_data = json_decode($res, true);
	    if($_data['code'] == 200) {
	        $unionpay = $_data['data'];
	        echo json_encode( array('code' => '200', 'data' => $unionpay) ) ;
	        return;
	    } else {
	        echo json_encode( array('code' => '101', 'data' => $_data['data']) );
	        return;
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