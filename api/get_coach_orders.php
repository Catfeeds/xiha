<?php

	/**
	 * 获得所有教练的订单列表
	 * @param $id int 教练id
	 * @param $status_id int 状态ID 1：待完成 2：已完成 3：已取消 
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->get('/id/:id/status/:status_id','getOrders');

	// 获取学员订单列表
	function getOrders($id, $status_id) {
		Global $crypt;
		$sql = "SELECT * FROM `cs_study_orders` WHERE `l_coach_id` = :id AND `i_status` = :status_id";
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam('id', $id);
			$stmt->bindParam('status_id', $status_id);
			$stmt->execute();
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

			foreach ($res as $key => $value) {

				// 获取教练头像
				$sql = "SELECT `user_photo` FROM `cs_users_info` WHERE `user_id` = ".$value['l_user_id'];
				$stmt = $db->query($sql);
				$imgurl = $stmt->fetch(PDO::FETCH_ASSOC);

				// 获取当前的日期
				$sql = "SELECT `year`, `month`, `day` FROM `cs_coach_appoint_time` WHERE `id` = ".$value['appoint_time_id'];
				$stmt = $db->query($sql);
				$date = $stmt->fetch(PDO::FETCH_ASSOC);

				$time_config_id = array_filter(explode(',', $value['time_config_id']));

				$_time = array();
				if($time_config_id) {

					// 获取当前的时间段
					$sql = "SELECT `start_time`, `end_time` FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config_id).")";
					$stmt = $db->query($sql);
					$time_config = $stmt->fetchAll(PDO::FETCH_ASSOC);


					if($time_config) {
						foreach ($time_config as $k => $v) {
							$_time[] = $v['start_time'].'.00-'.$v['end_time'].'.00';
						}
					} else {
						$_time[] = date('H:i', time());
					}
				}

				if($date) {
					$res[$key]['order_time'] = $date['year'].'/'.$date['month'].'/'.$date['day'].' '.implode(',', $_time);
				} else {
					$res[$key]['order_time'] = date('Y', time()).'/'.date('m', time()).'/'.date('d', time()).' '.implode(',', $_time);
				}
				$res[$key]['time_config_arr'] = $_time;
				$res[$key]['s_student_imgurl'] = $imgurl['user_photo'] == null ? '' : $imgurl['user_photo'];

				// 订单是否被教练评价
				$sql = "SELECT * FROM `cs_student_comment` WHERE `order_no` = '".$value['s_order_no']."'";
				// echo $sql;
				$stmt = $db->query($sql);
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				// print_r($row);

				if($row) {
					$res[$key]['is_comment'] = 1;
				} else {
					$res[$key]['is_comment'] = 2;
				}

			}


			$data = array('code'=>200, 'data'=>$res);

			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));

		} catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('get_coach_orders:params[id:'.$id.',status_id:'.$status_id.'], error:'.$e->getMessage());		
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));
		}
	}

	$app->run();
?>