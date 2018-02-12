<?php  

	/**
	 * 学员端完成订单
	 * @param $order_no 订单号 
	 * @param $order_id 订单ID
	 * @param $user_id 学员ID
	 * @return string AES对称加密（加密字段xhxueche）
	* @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','finishOrders');
	$app->run();

	// 完成订单
	function finishOrders() {
		Global $app, $crypt;
		$request = $app->request();
		$order_no = $request->params('order_no');
		$order_id = $request->params('order_id');
		$user_id = $request->params('user_id');

		$db = getConnection();

		// 判断当前订单时间与我培训完的时间是否相差半个小时
		$sql = "SELECT `time_config_id`, `dt_appoint_time` FROM `cs_study_orders` WHERE `s_order_no` = '".$order_no."' AND `l_user_id` = $user_id AND `l_study_order_id` = $order_id";
		$stmt = $db->query($sql);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if($row) {
			$time_config_id = array_filter(explode(',', $row['time_config_id']));
			$end_time = array();

			if(!$time_config_id) {
				$data = array('code'=>-6, 'data'=>'参数错误');
				echo json_encode($data);
				exit();
			}
			
			// 获取当前订单的年月日
			// 获取当前所预约的时间段
			$sql = "SELECT `start_time`, `end_time` FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config_id).")";
			$query = $db->query($sql);
			$res = $query->fetchAll(PDO::FETCH_ASSOC);
			foreach ($res as $k => $v) {
				$end_time[] = $v['end_time'];
			}
			
			// 判断是否是预约时间段的结束时间
			// $year = date('Y',time());
			// $month = date('m',time());
			// $day = date('d',time());

			$dt_appoint_time = strtotime($row['dt_appoint_time']);
			// $year = date('Y', $dt_appoint_time);
			// $month = date('m', $dt_appoint_time);
			// $day = date('d', $dt_appoint_time);
			$appoint_end_time = $dt_appoint_time + max($end_time) * 3600;
			// $appoint_end_time = strtotime($year.'-'.$month.'-'.$day.' '.max($end_time).':00');
			// setapilog('finish_orders:params[order_no:'.$order_no.',order_id:'.$order_id.',user_id:'.$user_id.']');

			// setapilog('finish_orders:params[appoint_end_time:'.$appoint_end_time.',time:'.time().']');

			if(time() < $appoint_end_time) {
				// 时间没到不能完成
				$data = array('code'=>-4, 'data'=>'预约时间未学完不能完成');
				echo json_encode($data);
				exit();
			}
		} else {
			$data = array('code'=>-5, 'data'=>'参数错误');
			echo json_encode($data);
			exit();
		}

		// 判断当前用户是否是这个订单
		$sql = "SELECT * FROM `cs_study_orders` WHERE `s_order_no` = ".$order_no." AND `l_user_id` = $user_id AND `l_study_order_id` = $order_id";
		try {
			$stmt = $db->query($sql);
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			if(!$res) {
				$data = array('code'=>-3, 'data'=>'当前用户不存在此订单');
				echo json_encode($data);
				exit();
			}
		} catch(PDOException $e) {
			setapilog('finish_orders:params[order_no:'.$order_no.',order_id:'.$order_id.',user_id:'.$user_id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>$e->getMessage());
			echo json_encode($data);
			exit();
		}

		// 判断当前订单号是否已经完成
		try {
			$sql = "SELECT * FROM `cs_study_orders` WHERE `s_order_no` = $order_no AND `l_user_id` = $user_id AND `i_status` = 2";
			$db = getConnection();
			$stmt = $db->query($sql);
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if($res) {
				$data = array('code'=>-1, 'data'=>'订单已完成');
				echo json_encode($data);
				exit();
			}

			// 操作完成订单
			$sql = "UPDATE `cs_study_orders` SET `i_status` = 2 WHERE `s_order_no` = :order_no AND `l_user_id` = :user_id";
			$stmt = $db->prepare($sql);
			$stmt->bindParam('order_no', $order_no);
			$stmt->bindParam('user_id', $user_id);
			$res = $stmt->execute();
			if($res) {
				$data = array('code'=>200, 'data'=>'订单完成');
			} else {
				$data = array('code'=>-2, 'data'=>'订单操作完成失败');
			}
			echo json_encode($data);
			exit();

		} catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('finish_orders:params[order_no:'.$order_no.',order_id:'.$order_id.',user_id:'.$user_id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');

			echo json_encode($data);
			exit();
		}
	}
?>