<?php  
	/**
	 * 取消订单
	 * @param $order_no 订单号 
	 * @param $order_id 订单ID
	 * @param $user_id 学员ID
	 * @param $content 取消原因
	 * @param $type 1：学员取消 2：教练取消
	 * @return string AES对称加密（加密字段xhxueche）
	* @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','cancelOrders');
	$app->run();

	// 取消订单
	function cancelOrders() {

		Global $app, $crypt;
		$request = $app->request();
		$order_no = $request->params('order_no');
		$type = $request->params('type');
		$order_id = $request->params('order_id');
		$user_id = $request->params('user_id');
		$content = $request->params('content');

		// 判断当前用户是否是这个订单
		$db = getConnection();
		$sql = "SELECT * FROM `cs_study_orders` WHERE `s_order_no` = ".$order_no." AND `l_user_id` = $user_id AND `l_study_order_id` = $order_id";
		try {
			$stmt = $db->query($sql);
			$orderinfo = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($orderinfo)) {
				$data = array('code'=>-4, 'data'=>'当前用户不存在此订单');
				echo json_encode($data);
				exit();
			}
		} catch(PDOException $e) {
			$data = array('code'=>1, 'data'=>$e->getMessage());
			echo json_encode($data);
			exit();
		}

		// 判断当前订单时间需在两个小时之前取消
		$time = time();
		$year = date('Y', $time);
		$month = date('m', $time);
		$day = date('d', $time);
		$hour = date('H', $time);

		$sql = "SELECT s.`dt_appoint_time`, s.`time_config_id` FROM `cs_study_orders` as s LEFT JOIN `cs_coach_appoint_time` as a ON a.`id` = s.`appoint_time_id` WHERE s.`s_order_no` = '".$order_no."' AND s.`l_study_order_id` = ".$order_id;
		$stmt = $db->query($sql);
		$study_order_info = $stmt->fetch(PDO::FETCH_ASSOC);

		if($study_order_info) {
			$time_config_id = array_filter(explode(',', $study_order_info['time_config_id']));
			$sql = "SELECT * FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config_id).")";
			$stmt = $db->query($sql);
			$time_config_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$starttime_arr = array();
			if($time_config_info) {
				foreach ($time_config_info as $key => $value) {
					$starttime_arr[] = $value['start_time'];
				}
			}
			$appoint_time = strtotime($study_order_info['dt_appoint_time']) + min($starttime_arr)*3600;
			if($appoint_time > $time) {
				if($appoint_time - $time < 2*3600) {
					$data = array('code'=>-5, 'data'=>"请提前两个小时下单");
					echo json_encode($data);
					exit();
				}
					
			} else if($appoint_time < $time) {
				$data = array('code'=>-6, 'data'=>'订单已过期不能取消');
				// $data = array('code'=>-6, 'data'=>"订单已过期不能取消");
				echo json_encode($data);
				exit();
			}
		}

		// 判断当前订单是否被取消
		$sql = "SELECT `i_status`, `cancel_type` FROM `cs_study_orders` WHERE `i_status` = 3 AND `s_order_no` = ".$order_no." AND `l_study_order_id` = ".$order_id;

		try {
			$stmt = $db->query($sql);
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			if($row) {
				if($row['cancel_type'] == 1) {
					$data = array('code'=>-2, 'data'=>'订单已经学员取消');
				} elseif($row['cancel_type'] == 2) {
					$data = array('code'=>-2, 'data'=>'订单已经教练取消');
				}
				echo json_encode($data);
				exit();
			}
			
		} catch(PDOException $e) {
			setapilog('cancel_orders_by_coach:params[order_no:'.$order_no.',type:'.$type.',order_id:'.$order_id.',user_id:'.$user_id.',content:'.$content.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>$e->getMessage());
			echo json_encode($data);
			exit();
		}


		try {
			// 更改订单状态
			$sql = "UPDATE `cs_study_orders` SET `i_status` = 3, `cancel_reason` = :content, `cancel_type` = :type WHERE `s_order_no` = :order_no AND `l_study_order_id` = :order_id";
			$stmt = $db->prepare($sql);
			$stmt->bindParam('content', $content);
			$stmt->bindParam('type', $type);
			$stmt->bindParam('order_no', $order_no);
			$stmt->bindParam('order_id', $order_id);
			$res = $stmt->execute();
			if($res) {
				// 同时去除教练可被预约的时间段(cs_coach_appoint_time)
				if($orderinfo['appoint_time_id']) {
					$sql = "DELETE FROM `cs_coach_appoint_time` WHERE `id` = ".$orderinfo['appoint_time_id'];
					$stmt = $db->query($sql);
				}

				$data = array('code'=>200, 'data'=>'取消成功');
			} else {
				$data = array('code'=>-1, 'data'=>'取消失败');
			}
			$db = null;
			echo json_encode($data);

		} catch(PDOException $e) {
			setapilog('cancel_orders_by_coach:params[order_no:'.$order_no.',type:'.$type.',order_id:'.$order_id.',user_id:'.$user_id.',content:'.$content.'], error:'.$e->getMessage());
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');

			echo json_encode($data);
		}
	}

?>