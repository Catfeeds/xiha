<?php  
	/**
	 * 获取取消原因接口
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
            setapilog('[get_cancel_reason] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
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
			$info = array(
				'tips'=>'退款操作，需扣除平台交易费，望谅解，线上支付用户将在15个工作日内收到退款',
				'reason_list' => array(
					'临时有事，去不了',
					'其他原因',
				)
			);

			if($order_type == 'appoint') {
				$info = array(
					'tips'=>'退款操作，需扣除平台交易费，望谅解，线上支付用户将在15个工作日内收到退款',
					'reason_list' => array(
						'临时有事，去不了',
						'计划有变，重新预约其他时间',
						'和教练协商一致取消',
						'其他原因',
					)
				);
				
			} else if ($order_type == 'signup') {
				$info = array(
					'tips'=>'退款操作，需扣除平台交易费，望谅解，线上支付用户将在15个工作日内收到退款',
					'reason_list' => array(
						'临时有事，去不了',
						'计划有变，重新预约其他时间',
						'和驾校协商一致取消',
						'其他原因',
					)
				);

			}
			$data = array('code'=>200, 'data'=>$info);
			echo json_encode($data);
			return;
		} catch (PDOException $e) {
			setapilog('[get_cancel_reason] [:error] [params '. serialize($r->params()) .'] [1 ' . $e->getMessage() . ']');    
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			return;
		}

    }