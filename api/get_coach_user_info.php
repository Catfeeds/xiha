<?php  
	/**
	 * 获取教练端学员详情
	 * @param $id int 学员ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->get('/id/:id','getCoachUserInfo');
	$app->run();

	// 获取教练端的学员学车评价信息

	function getCoachUserInfo($id) {
		Global $app, $crypt;
		$member_id = $id;

		try {
			$db = getConnection();
			// 获取学员姓名手机号
			$sql = "SELECT u.`s_username`, u.`s_real_name`, u.`s_phone`, i.`user_photo`, i.`sex`, i.`lesson_name` FROM `cs_user` as u LEFT JOIN `cs_users_info` as i ON i.`user_id` = u.`l_user_id` WHERE `l_user_id` = $member_id AND `i_user_type` = 0";
			$stmt = $db->query($sql);
			$userinfo = $stmt->fetch(PDO::FETCH_ASSOC);
			if(!$userinfo) {
				$data = array('code'=>-1, 'data'=>'参数错误');
				echo json_encode($data);
				exit();
			}

			if(file_exists(__DIR__.'/../sadmin/'.$userinfo['user_photo'])) {
				$userinfo['user_photo'] = S_HTTP_HOST.$userinfo['user_photo'];
			} else {
				$userinfo['user_photo'] = HTTP_HOST.$userinfo['user_photo'];
			}
				
			$userinfo['user_photo'] = $userinfo['user_photo'] == null ? '' : $userinfo['user_photo'];

			// 获取报名驾校的班制名称
			$sql = "SELECT `so_shifts_id` FROM `cs_school_orders` WHERE `so_user_id` = $id AND `so_order_status` != 101";
			$stmt = $db->query($sql);
			$shifts_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$shifts_name = array();
			if($shifts_info) {
				foreach ($shifts_info as $key => $value) {
					if($value['so_shifts_id'] == 1) {
						$shifts_name[] = '计时班';
					} else if($value['so_shifts_id'] == 2) {
						$shifts_name[] = '普通班';
					} else if($value['so_shifts_id'] == 3) {
						$shifts_name[] = 'VIP班';
					}
				}
			}
			$userinfo['shifts_name'] = $shifts_name;
			
			// 获取订单总数
			$sql = "SELECT count(*) as num FROM `cs_study_orders` WHERE `l_user_id` = $id";
			$stmt = $db->query($sql);
			$order_num = $stmt->fetch(PDO::FETCH_ASSOC);
			if($order_num) {
				$userinfo['order_num'] = $order_num['num'];
			} else {
				$userinfo['order_num'] = 0;
			}
			// 获取总学时, 学费统计
			$sql = "SELECT `i_service_time`, `dc_money` FROM `cs_study_orders` WHERE `l_user_id` = $id";
			$stmt = $db->query($sql);
			$service_time = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$service_time_arr = 0;
			$dc_money = 0;
			if($service_time) {
				foreach ($service_time as $key => $value) {
					$service_time_arr += $value['i_service_time'];
					$dc_money += $value['dc_money'];
				}
			}
			$userinfo['learn_time'] = $service_time_arr;
			$userinfo['dc_money'] = $dc_money;


			// 获取当前学员已通过科目
			$sql = "SELECT o.`time_config_id`, o.`l_coach_id`, o.`s_order_no`, o.`dc_money`, a.`year`, a.`month`, a.`day`, o.`s_lesson_name`, o.`s_lisence_name` FROM `cs_study_orders` as o LEFT JOIN `cs_coach_appoint_time` as a ON a.`id` = o.`appoint_time_id` WHERE o.`l_user_id` = $member_id AND o.`i_status` = 2";
			$stmt = $db->query($sql);
			$lesson_res = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if($lesson_res) {
				$time_config_id = array();
				$list = array();
				foreach ($lesson_res as $key => $value) {
					$time_config_id = array_filter(explode(',', $value['time_config_id']));
					if($time_config_id) {

						// // 查找科目和牌照
						// $sql = "SELECT `time_lesson_config_id`, `time_lisence_config_id` FROM `cs_current_coach_time_configuration` ";
						// $sql .= " WHERE `time_config_id` IN (".implode(',', $time_config_id).") AND `coach_id` = ".$value['l_coach_id']." AND `year` = ".$value['year']." AND `month` = ".$value['month']." AND `day` = ".$value['day'];
						// $stmt = $db->query($sql);
						// $time_lesson_arr = $stmt->fetch(PDO::FETCH_ASSOC);

						// $time_lesson_config_id = json_decode($time_lesson_arr['time_lesson_config_id'], true);
						// $time_lisence_config_id = json_decode($time_lesson_arr['time_lisence_config_id'], true);

						// $lesson_name_arr = array();
						// $lisence_name_arr = array();

						// if($time_lesson_config_id && $time_lisence_config_id) {
						// 	foreach ($time_config_id as $k => $v) {
						// 		if(in_array($v, array_keys($time_lesson_config_id))) {
						// 			$lesson_name_arr[] = $time_lesson_config_id[$v];
						// 			$lisence_name_arr[] = $time_lisence_config_id[$v];
						// 		}
						// 	}
						// }
						// $list[$key]['lesson_name'] = implode(',', array_unique($lesson_name_arr));
						// $list[$key]['lisence_name'] = implode(',', array_unique($lisence_name_arr));
						$list[$key]['lesson_name'] = $value['s_lesson_name'];
						$list[$key]['lisence_name'] = $value['s_lisence_name'];

						// 获取教练端的姓名和头像
						$sql = "SELECT `s_coach_name`, `s_coach_imgurl` FROM `cs_coach` WHERE `l_coach_id` = ".$value['l_coach_id'];
						$stmt = $db->query($sql);
						$coachinfo = $stmt->fetch(PDO::FETCH_ASSOC);

						if($coachinfo) {
							if(file_exists(__DIR__.'/../sadmin/'.$coachinfo['s_coach_imgurl'])) {
								$coachinfo['s_coach_imgurl'] = S_HTTP_HOST.$coachinfo['s_coach_imgurl'];
							} else {
								$coachinfo['s_coach_imgurl'] = HTTP_HOST.$coachinfo['s_coach_imgurl'];
							}
							$list[$key]['coach_photo'] = $coachinfo['s_coach_imgurl'] == null ? '' : $coachinfo['s_coach_imgurl'];
							$list[$key]['coach_name'] = $coachinfo['s_coach_name'] == null ? '' : $coachinfo['s_coach_name'];
						} else {
							$list[$key]['coach_photo'] = '';
							$list[$key]['coach_name'] = '';
						}
							

						// 获取教练评价
						$sql = "SELECT * FROM `cs_student_comment` WHERE `user_id` = $member_id AND `coach_id` = ".$value['l_coach_id']." AND `order_no` = ".$value['s_order_no'];
						$stmt = $db->query($sql);
						$student_comment_arr = $stmt->fetch(PDO::FETCH_ASSOC);
						$list[$key]['star'] = $student_comment_arr['star_num'] == null ? '' : intval($student_comment_arr['star_num']);
						$list[$key]['content'] = $student_comment_arr['content'] == null ? '' : $student_comment_arr['content'];
						$list[$key]['addtime'] = $student_comment_arr['addtime'] == '' ? date('Y-m-d H:i', time()) : date('Y-m-d H:i', $student_comment_arr['addtime']);

					}
				}

				$userinfo['learn_car_arr'] = $list;	
			} else {
				$userinfo['learn_car_arr'] = array();	
			}
			$db = null;

			$data = array('code'=>200, 'data'=>$userinfo);
			echo json_encode($data);
			exit();

		} catch(PDOException $e) {
			setapilog('get_coach_user_info:params[id:'.$id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}
	}
?>