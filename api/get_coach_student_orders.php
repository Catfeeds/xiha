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
	$app->post('/','getOrders');
	$app->run();

	// 获取学员订单列表
	function getOrders() {
		Global $app, $crypt;
		$request = $app->request();
		$id = $request->params('id');
		$status_id = $request->params('status');
		$page = $request->params('page');

		$page = isset($page) ? $page : 1;
		$page = $page == 0 ? 1 : $page;
		$limit = 10;
		$start = ($page - 1) * $limit;

		$sql = "SELECT * FROM `cs_study_orders` WHERE `l_coach_id` = :id AND `i_status` = :status_id ORDER BY  UNIX_TIMESTAMP(`dt_appoint_time`) DESC, `l_study_order_id` DESC LIMIT $start, $limit";
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam('id', $id);
			$stmt->bindParam('status_id', $status_id);
			$stmt->execute();
			$order_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if(empty($order_list)) {
				$data = array('code'=>200, 'data'=>array());
				echo json_encode($data);
				exit();
			}

			// 待完成
			foreach ($order_list as $key => $value) {

				$order_list[$key]['dt_order_time'] = date('Y-m-d H:i', $value['dt_order_time']);
				// 获取学员头像
				$sql = "SELECT i.`user_photo`, i.`identity_id`, u.`s_real_name`, i.`photo_id` FROM `cs_user` as u";
				$sql .= " LEFT JOIN `cs_users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`l_user_id` = ".$value['l_user_id'];
				// echo $sql;
				$stmt = $db->query($sql);
				$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
				if($user_info) {
					$order_list[$key]['user_photo'] = !empty($user_info['user_photo']) ? S_HTTP_HOST.$user_info['user_photo'] : '';
					$order_list[$key]['identity_id'] = $user_info['identity_id'] == null ? '' : $user_info['identity_id'];
					$order_list[$key]['s_real_name'] = $user_info['s_real_name'];
					$order_list[$key]['photo_id'] = $user_info['photo_id'] == null ? "0" : $user_info['photo_id'];
				} else {
					$order_list[$key]['identity_id'] = '';
					$order_list[$key]['user_photo'] = '';
					$order_list[$key]['s_real_name'] = '';
					$order_list[$key]['photo_id'] = "0";
				}

				// 获取学员报名订单所属驾校
				$sql = "SELECT s.`s_school_name` FROM `cs_coach` as c LEFT JOIN `cs_school` as s ON s.`l_school_id` = c.`s_school_name_id` WHERE c.`l_coach_id` = $id";
				$stmt = $db->query($sql);
				$coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
				if($coach_info) {
					$order_list[$key]['school_name'] = $coach_info['s_school_name'];
				} else {
					$order_list[$key]['school_name'] = '';
				}


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
					$start_time_arr = array();

					if($time_config) {
						foreach ($time_config as $k => $v) {
							$_time[] = $v['start_time'].'.00-'.$v['end_time'].'.00';
							$start_time_arr[] = $v['start_time'];
						}
					} else {
						$_time[] = date('H:i', time());
					}
				}

				if($date) {
					$order_list[$key]['order_time'] = $date['year'].'/'.$date['month'].'/'.$date['day'].' '.implode(',', $_time);
				} else {
					$order_list[$key]['order_time'] = date('Y', time()).'/'.date('m', time()).'/'.date('d', time()).' '.implode(',', $_time);
				}
				$order_list[$key]['time_config_arr'] = $_time;

				if($status_id == 1) {
					// 时间倒计时
					$countdown = strtotime($date['year'].'-'.$date['month'].'-'.$date['day'].' '.min($start_time_arr).':00') - time();
					if($countdown > 0) {
						$order_list[$key]['countdown'] = $countdown;
					} else {
						$order_list[$key]['countdown'] = 0;
					}
				}
					
				// 学员评价教练
				$sql = "SELECT `id`, `coach_content` FROM `cs_coach_comment` WHERE `coach_id` = $id AND `user_id` = ".$value['l_user_id']." AND `type` = 1";
				$stmt = $db->query($sql);
				$comment_info = $stmt->fetch(PDO::FETCH_ASSOC);
				if($comment_info) {
					$order_list[$key]['is_comment'] = 1; //评价
					$order_list[$key]['coach_comment'] = $comment_info['coach_content'];
				} else {
					$order_list[$key]['is_comment'] = 2; //未评价
					$order_list[$key]['coach_comment'] = '';
				}

				// 教练评价学员
				$sql = "SELECT * FROM `cs_student_comment` WHERE `order_no` = '".$value['s_order_no']."'";
				$stmt = $db->query($sql);
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if($row) {
					$order_list[$key]['is_coach_comment'] = 1; //评价
					$order_list[$key]['student_comment'] = $row['content'] == null ? '' : $row['content'];

				} else {
					$order_list[$key]['is_coach_comment'] = 2; //未评价
					$order_list[$key]['student_comment'] = '';		
				}
			}
			$db = null;
			$data = array('code'=>200, 'data'=>$order_list);
			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));

		} catch(PDOException $e) {
			setapilog('get_coach_user_orders:params[id:'.$id.',status_id:'.$status_id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>$e->getMessage());
			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));
		}
	}

?>