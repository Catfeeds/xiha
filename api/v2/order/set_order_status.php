<?php  
	/**
	 * 设置订单支付状态
	 * @param $order_no 订单号
	 * @param $order_type 订单类型  signup：报名驾校 appoint：预约学车 
	 * @param $order_status 订单状态  1002：已支付 
	 * @param $pay_type 支付方式 0：未知 1：支付宝 2：线下 3：微信 4：银联
	 * @param $trade_no 唯一订单号
	 * @param $order_money 订单价格
     * @param $zhifu_time  订单支付完成时间
	 * @return bool
	 * @author cx
	 **/
	require '../../Slim/Slim.php';
	require '../../include/common.php';
	require '../../include/crypt.php';
	require '../../include/functions.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->any('/','opayment');
	$app->run();

	function opayment() {
		Global $app, $crypt;
		$request = $app->request();
 		if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_mix_ads] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            exit(json_encode($data));
        }
        //  获取请求参数并判断合法性
        $validate_result = validate(
        	array(
        		'order_no'      =>'STRING', 
        		'order_type'    => 'STRING',
        		'order_status'  => 'INT',
        		'pay_type'      => 'INT',
        		'trade_no'      => 'STRING',
        		'order_money'   => 'INT',
        		'zhifu_time'    => 'STRING',
    		), $request->params());

        if (!$validate_result['pass']) {
            exit(json_encode($validate_result['data']));
        }
		$p = $request->params();
		try {
			$db = getConnection();

			if($p['order_type'] == 'signup') {
			    // 报名驾校
				$sql = "SELECT `id` FROM `".DBPREFIX."school_orders` WHERE `so_order_no` = :order_no AND `so_final_price` = :order_money";
				$stmt = $db->prepare($sql);
				$stmt->bindParam('order_no', $p['order_no'], PDO::PARAM_STR);
				$stmt->bindParam('order_money', $p['order_money'], PDO::PARAM_STR);
				$stmt->execute();
				$order_info = $stmt->fetch(PDO::FETCH_ASSOC);
				if(empty($order_info)) {
					echo json_encode(array('code'=>101, 'data'=>'订单不存在或者订单价格不正确', 'order_info'=>$order_info));
					exit();
				}

				$sql = " UPDATE `".DBPREFIX."school_orders` SET `so_order_status` = :order_status, `s_zhifu_dm` = :trade_no, `dt_zhifu_time` =  :time_end, `so_pay_type` = :paytype, `so_comment_status` = 1 ";
				$sql .= " WHERE `so_order_no` = :order_no";
				$stmt = $db->prepare($sql);
				$stmt->bindParam('order_status', $p['order_status'], PDO::PARAM_INT);
				$stmt->bindParam('trade_no', $p['trade_no'], PDO::PARAM_STR);
				$stmt->bindParam('order_no', $p['order_no'], PDO::PARAM_STR);
				$stmt->bindParam('paytype', $p['pay_type'], PDO::PARAM_INT);
				$stmt->bindParam('time_end', $p['zhifu_time'], PDO::PARAM_STR);
				$res = $stmt->execute();
				if($res) {
					echo json_encode( array('code'=>200, 'data'=>'支付成功', 'order_info'=>$order_info) );
                    exit();
				} else {
					echo json_encode( array('code'=>400, 'data'=>'支付失败', 'order_info'=>$order_info) );
                    exit();
				}

			} else if($p['order_type'] == 'appoint') {
			    // 预约学车
				$sql = "SELECT `l_study_order_id` FROM `".DBPREFIX."study_orders` WHERE `s_order_no` = :order_no AND `dc_money` = :order_money";
				$stmt = $db->prepare($sql);
				$stmt->bindParam('order_no', $p['order_no'], PDO::PARAM_STR);
				$stmt->bindParam('order_money', $p['order_money'], PDO::PARAM_STR);
				$stmt->execute();
				$sorder_info = $stmt->fetch(PDO::FETCH_ASSOC);
				if(empty($sorder_info)) {
					exit( json_encode( array('code'=>101, 'data'=>'订单不存在或者订单价格不正确', 'order_info'=>$sorder_info) ) );
				}

				$sql = "UPDATE `".DBPREFIX."study_orders` SET `s_zhifu_dm` = :trade_no, `i_status` = :order_status, `dt_zhifu_time` = :time_end, `deal_type` = :paytype";
				$sql .= " WHERE `s_order_no` = :order_no";
				$stmt = $db->prepare($sql);
				$stmt->bindParam('trade_no', $p['trade_no'], PDO::PARAM_STR);
				$stmt->bindParam('order_status', $p['order_status'], PDO::PARAM_INT);
				$stmt->bindParam('order_no', $p['order_no'], PDO::PARAM_STR);
				$stmt->bindParam('paytype', $p['pay_type'], PDO::PARAM_INT);
				$stmt->bindParam('time_end', $p['zhifu_time'], PDO::PARAM_STR);
				$res = $stmt->execute();
				if($res) {
					echo json_encode( array('code'=>200, 'data'=>'支付成功', 'order_info'=>$sorder_info) );
                    exit();
				} else {
					echo json_encode( array('code'=>400, 'data'=>'支付失败', 'order_info'=>$sorder_info) );
                    exit();
				}
			} else {
				echo json_encode( array('code'=>400, 'data'=>'支付失败') );
                exit();
			}

		} catch(PDOException $e) {
            setapilog('[set_order_status] [:error] [client ' . $request->getIp() . '] [params ' . serialize($request->params()) . '] [1 ' . $e->getMessage() . ']');
			$data = array('code'=>1, 'data'=>$e->getMessage());
			echo json_encode($data);
			exit;
		}
	}

?>
