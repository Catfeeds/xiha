<?php  
	/**
	 * 申请取消线上支付 (1：报名成功，2：申请退款中 3：退款成功 4：报名取消 5:已评价)
	 * @param $uid 学员ID 
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','cancelAlipayOrder');
	$app->run();

	// 获取教练学员信息
	function cancelAlipayOrder() {
		Global $app, $crypt;

		$request = $app->request();
		$order_no = $request->params('no');
		$user_id = $request->params('uid');
		$school_id = $request->params('sid');

		if(empty($order_no) || empty($user_id) || empty($school_id)) {
			$data = array('code'=>-1, 'data'=>'参数错误');
			echo json_encode($data);
			exit();
		}

		try {
			$db = getConnection();
			$sql = "SELECT * FROM `cs_school_orders` WHERE `so_order_no` = '".$order_no."' AND `so_school_id` = $school_id AND `so_user_id` = $user_id AND `so_order_status` != 101";
			$stmt = $db->query($sql);
			$order_info = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($order_info)) {
				$data = array('code'=>-2, 'data'=>'不存在此订单');
				echo json_encode($data);
				exit();
			}
			$sql = "UPDATE `cs_school_orders` SET `so_order_status` = 2 WHERE `so_order_no` = '".$order_no."'";
			$stmt = $db->query($sql);
			if($stmt) {
				$data = array('code'=>200, 'data'=>'申请已经提交，等待后台人员处理！');
			} else {
				$data = array('code'=>-3, 'data'=>'申请提交失败，请重新提交！');
			}
			echo json_encode($data);

		} catch(PDOException $e) {
			setapilog('alipay_school_order_cancel:params[no:'.$order_no.',uid:'.$user_id.',sid:'.$school_id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}

	}

?>