<?php  
	/**
	 * 获得所有学员的订单列表
	 * @param $id int 学员id
	 * @param $status_id int 状态ID 1：待已完成已付款订单 2：已完成 3：已取消 1003：待完成未付款订单 
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->get('/id/:id/status/:status_id/page/:page','getOrders');
	$app->run();

	// 获取学员订单列表
	function getOrders($id, $status_id, $page) {
		Global $crypt;
		$page = isset($page) ? $page : 1;
		$page = $page == 0 ? 1 : $page;
		$limit = 10;
		$start = ($page - 1) * $limit;
		$sql = "SELECT * FROM `cs_study_orders` as o LEFT JOIN `cs_coach_appoint_time` as a ON a.`id` = o.`appoint_time_id` WHERE a.`id` != '' AND o.`l_user_id` = :id AND o.`i_status` = :status_id ORDER BY o.`l_study_order_id` DESC, o.`dt_appoint_time` DESC LIMIT $start, $limit";
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam('id', $id);
			$stmt->bindParam('status_id', $status_id);
			$stmt->execute();
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

			foreach ($res as $key => $value) {

				$res[$key]['dt_order_time'] = date('Y-m-d H:i', $value['dt_order_time']);

				// 获取教练头像
				$sql = "SELECT c.`s_coach_imgurl`, s.`s_school_name` FROM `cs_coach` as c LEFT JOIN `cs_school` as s ON c.`s_school_name_id` = s.`l_school_id` WHERE c.`l_coach_id` = ".$value['l_coach_id'];
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
					$sql = "SELECT `start_time`, `end_time`, `start_minute`, `end_minute` FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config_id).")";
					$stmt = $db->query($sql);
					$time_config = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$start_time_arr = array();

					if($time_config) {
						foreach ($time_config as $k => $v) {
							$start_minute = $v['start_minute'] == 0 ? '00' : $v['start_minute'];
							$end_minute = $v['end_minute'] == 0 ? '00' : $v['end_minute'];
							$_time[] = $v['start_time'].':'.$start_minute.'-'.$v['end_time'].':'.$end_minute;
							$start_time_arr[] = $v['start_time'];
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
				$res[$key]['s_school_name'] = $imgurl['s_school_name'] == null ? '嘻哈驾校' : $imgurl['s_school_name'];

				// 待完成已付款订单
				if($status_id == 1) {
					// 时间倒计时
					$countdown = strtotime($date['year'].'-'.$date['month'].'-'.$date['day'].' '.min($start_time_arr).':00') - time();
					// print_r(min($start_time_arr));
					// echo "<br>";
					if($countdown > 0) {
						$res[$key]['countdown'] = $countdown;
					} else {
						$res[$key]['countdown'] = 0;
					}

				// 已完成订单
				} else if($status_id == 2) {


				// 已取消订单
				} else if($status_id == 3) {

				// 待完成未付款订单
				} else if($status_id == 1003) {

				}


				// echo $res[$key]['countdown'].'<br>';
				if($imgurl['s_coach_imgurl'] != '') {
					if(file_exists(__DIR__.'/../sadmin/'.$imgurl['s_coach_imgurl'])) {
						$res[$key]['s_coach_imgurl'] = S_HTTP_HOST.$imgurl['s_coach_imgurl'];
					} else {
						$res[$key]['s_coach_imgurl'] = HTTP_HOST.$imgurl['s_coach_imgurl'];
					}
				} else {
					$res[$key]['s_coach_imgurl'] = '';
				}
					
				// 学员评价教练
				$sql = "SELECT * FROM `cs_coach_comment` WHERE `order_no` = '".$value['s_order_no']."'";
				$stmt = $db->query($sql);
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if($row) {
					$res[$key]['is_comment'] = 1;
					$res[$key]['coach_comment'] = $row['coach_content'];
				} else {
					$res[$key]['is_comment'] = 2;
					$res[$key]['coach_comment'] = '';
				}

				// 教练评价学员
				$sql = "SELECT * FROM `cs_student_comment` WHERE `order_no` = '".$value['s_order_no']."'";
				$stmt = $db->query($sql);
				$student_comment_info = $stmt->fetch(PDO::FETCH_ASSOC);
				if($student_comment_info) {
					$res[$key]['is_coach_comment'] = 1;
					$res[$key]['student_comment'] = $student_comment_info['content'];
				} else {
					$res[$key]['is_coach_comment'] = 2;
					$res[$key]['student_comment'] = '';
				}
					
			}
			$data = array('code'=>200, 'data'=>$res);

			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));

		} catch(PDOException $e) {
			setapilog('get_student_orders:params[id:'.$id.',status_id:'.$status_id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));
		}
	}
?>