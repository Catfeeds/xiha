<?php  
	/**
	 * 获取支付方式
	 * @param $order_type string 订单类型 signup ：报名驾校 appoint ：预约学车
	 * @return json
	 * @author cx
	 **/
	require '../../Slim/Slim.php';
	require '../../include/common.php';
	require '../../include/crypt.php';
	require '../../include/functions.php';
	require '../pay/apppay/wxpay/lib/WxPay.Api.php';
	require '../pay/include/common.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->any('/','getPayMethods');
	$app->response->headers->set('Content-Type','application/json; charset=utf-8');
	$app->run();

	function getPayMethods() {
        Global $app, $crypt;
		//验证请求方式 POST
        $r = $app->request();
        if ( !$r->isPost() ) {
            setapilog('[get_pay_methods] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            echo json_encode(array('code' => 106, 'data' => '请求错误'));
			return;
        }

        //验证输入参数
        $validate_ok = validate(
            array(
                'order_type' => 'STRING',
             ),
            $r->params()
        );

        if ( !$validate_ok['pass'] ) {
            echo json_encode($validate_ok['data']);
			return;
        }
        $order_type = $r->params('order_type');
		$order_type_arr = array('appoint', 'signup');
		if(!in_array($order_type, $order_type_arr)) {
			echo json_encode(array('code'=>106, 'data'=>'参数错误'));
			return;
		}

		try {
			$db = getConnection();

			$order_methods = array(
				array(
					'pay_id' => 1, // 支付宝
					'sort' => 1, // 排序
					'title' => '支付宝',
					'desc' => '拥有支付宝账号的用户使用',
					'is_commend' => 1, // 推荐 1:推荐 2:不推荐 3：正常
				),
				array(
					'pay_id' => 2, // 线下支付
					'sort' => 4,
					'title' => '线下支付',
					'desc' => '到驾校支付',
					'is_commend' => 2, // 推荐 1:推荐 2:不推荐 3：正常
				),
				array(
					'pay_id' => 3, // 微信支付
					'sort' => 2,
					'title' => '微信支付',
					'desc' => '推荐安装微信5.0及以上版本使用',
					'is_commend' => 3, // 推荐 1:推荐 2:不推荐 3：正常
				),
				array(
					'pay_id' => 4, // 银联
					'sort' => 3,
					'title' => '银联支付',
					'desc' => '支持储蓄卡信用卡，无需开通网银',
					'is_commend' => 3, // 推荐 1:推荐 2:不推荐 3：正常
				),
			);
			// 报名驾校
			switch ($order_type) {
				case 'signup':
					unset($order_methods[1]);
					break;

				case 'appoint':
					unset($order_methods[1]);
					break;

				default:
					break;
			}
			$db = null;
			$data = array('code'=>200, 'data'=>array_values($order_methods));
			echo json_encode($data);
			return;

		} catch (PDOException $e) {
			setapilog('[get_pay_methods] [:error] [params'. serialize($r->params()) .'] [1 ' . $e->getMessage() . ']');    
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			return;
		}
	}
?>