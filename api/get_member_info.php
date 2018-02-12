<?php  
	/**
	 * 获取学员或者教练我的信息
	 * @param $type int 1：获取教练信息 2：获取学员信息
	 * @param $member_id int 学员或者教练ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getmemberinfo');
	$app->run();

	// 获取教练学员信息
	function getmemberinfo() {
		
		Global $app, $crypt;
		try {
			$db = getConnection();
			$request = $app->request();
			$type = $request->params('type');
			$member_id = $request->params('member_id');

			// 获取教练信息
			if($type === '1') {
				$sql = "SELECT * FROM `cs_coach` WHERE `l_coach_id` = $member_id";
				$stmt = $db->query($sql);
				$coachinfo = $stmt->fetch(PDO::FETCH_ASSOC);

				if(!$coachinfo) {
					$data = array('code'=>-2, 'data'=>array());
					echo json_encode($data);
					exit();
				}

				if(file_exists(__DIR__.'/../sadmin/'.$coachinfo['s_coach_imgurl'])) {
					$coachinfo['s_coach_imgurl'] = S_HTTP_HOST.$coachinfo['s_coach_imgurl'];
				} else {
					$coachinfo['s_coach_imgurl'] = HTTP_HOST.$coachinfo['s_coach_imgurl'];
				}
					
				// 获取已带学员
				$sql = "SELECT count(*) as num FROM `cs_study_orders` WHERE `l_coach_id` = $member_id AND `i_status` = 2";
				$stmt = $db->query($sql);
				$student_pass_count = $stmt->fetch(PDO::FETCH_ASSOC);

				// 获得通过率
				$sql = "SELECT count(*) as num FROM `cs_study_orders` WHERE `l_coach_id` = $member_id";
				$stmt = $db->query($sql);
				$student_all_count = $stmt->fetch(PDO::FETCH_ASSOC);

				if($student_all_count['num'] && $student_pass_count['num']) {
					$passing_rate = round(($student_pass_count['num'] / $student_all_count['num']) * 100,1).'%';
				} else {
					$passing_rate = '50%';
				}
				$coachinfo['pass_count'] = $student_pass_count['num'];
				$coachinfo['rate'] = $passing_rate;
				// 获取车辆
				if($coachinfo['s_coach_car_id']) {
					$imgurl_arr = array();
					$sql = "SELECT * FROM `cs_cars` WHERE `id` IN (".$coachinfo['s_coach_car_id'].")";
					$stmt = $db->query($sql);
					$res_car = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if($res_car){
						foreach ($res_car as $key => $value) {
							if($value['imgurl'] && $value['imgurl'] != 'null') {
								$imgurl = json_decode($value['imgurl'], true);
								if(is_array($imgurl)) {
									foreach ($imgurl as $k => $v) {
										if(file_exists(__DIR__.'/../sadmin/'.$v)) {
											$imgurl_arr[] = S_HTTP_HOST.$v;
										} else {
											$imgurl_arr[] = HTTP_HOST.$v;
										}
									}
								}		
							}
							$res_car[$key]['imgurl'] = $imgurl_arr;
							// $res_car[$key]['imgurl'] = json_decode($value['imgurl'],true);
						}
					} else {
						$res_car = array();
					}
				}

				$coachinfo['car_info'] = $res_car;
				
				// 获取当前学员信息
				$sql = "SELECT * FROM `cs_study_orders` WHERE `l_coach_id` = ".$member_id;
				$stmt = $db->query($sql);
				$user_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$list = array();
				foreach ($user_info as $key => $value) {
					$user_id[] = $value['l_user_id'];
					$sql = "SELECT * FROM `cs_user` as u LEFT JOIN `cs_users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`l_user_id` = ".$value['l_user_id'];
					$stmt = $db->query($sql);
					$userinfo = $stmt->fetch(PDO::FETCH_ASSOC);
					if(!empty($userinfo)) {
						// 获取当前评价
						$userinfo['photo_id'] = $userinfo['photo_id'] == 0 ? 1 : $userinfo['photo_id'];
						$sql = "SELECT * FROM `cs_coach_comment` WHERE `user_id` = ".$value['l_user_id']." AND `coach_id` = $member_id AND `order_no` = ".$value['s_order_no'];
						$stmt = $db->query($sql);
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
						if($row) {
							$list[$key]['s_username'] = $userinfo['s_username'];
							$list[$key]['s_phone'] = $userinfo['s_phone'];
							$photo_arr = array(
								0=>'1.png',
								1=>"1.png",
								2=>"2.png",
								3=>"3.png",
								4=>"4.png",
								5=>"5.png",
								6=>"6.png",
								7=>"7.png",
								8=>"8.png",
								9=>"9.png",
								10=>"10.png",
								11=>"11.png",
								12=>"12.png",
								13=>"13.png",
								14=>"14.png",
								15=>"15.png",
								16=>"16.png"
							);
							$list[$key]['photo_id'] = $userinfo['photo_id'];
							$list[$key]['photo_url'] = $photo_arr[$userinfo['photo_id']];
							$list[$key]['user_photo'] = $userinfo['user_photo'] == '' ? '' : HOST.$userinfo['user_photo'];
							$list[$key]['coach_star'] = $row['coach_star'] == null ? '3' : $row['coach_star'];
							$list[$key]['school_star'] = $row['school_star'] == null ? '3' : $row['school_star'];
							$list[$key]['coach_content'] = $row['coach_content'] == null ? '暂无评价' : $row['coach_content'];
							$list[$key]['school_content'] = $row['school_content'] == null ? '暂无评价' : $row['school_content'];
						}
					}
				}

				$coachinfo['learn_car_arr'] = array_values($list);
				$data = array('code'=>200, 'data'=>$coachinfo);
				echo json_encode($data);
				exit();

			// 获取学员信息
			} else if($type === '2') {

				$sql = "SELECT u.*, i.`age`, i.`sex`, i.`identity_id`, i.`address`, i.`user_photo`, i.`license_num`, i.`photo_id`, i.`learncar_status` FROM `cs_user` as u LEFT JOIN `cs_users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`l_user_id` = $member_id";
				$stmt = $db->query($sql);
				$userinfo = $stmt->fetch(PDO::FETCH_ASSOC);
				if(empty($userinfo)) {
					$data = array('code'=>-1, 'data'=>'此用户不存在');
					echo json_encode($data);
					exit();
				}

				if($userinfo) {
					$userinfo['sex'] = $userinfo['sex'] == null ? '' : $userinfo['sex'];
					$userinfo['age'] = $userinfo['age'] == null ? '' : $userinfo['age'];
					$userinfo['identity_id'] = $userinfo['identity_id'] == null ? '' : $userinfo['identity_id'];
					$userinfo['address'] = $userinfo['address'] == null ? '' : $userinfo['address'];
					$userinfo['user_photo'] = $userinfo['user_photo'] == null ? '' : HOST.$userinfo['user_photo'];
					$userinfo['license_num'] = $userinfo['license_num'] == null ? '' : $userinfo['license_num'];
					$userinfo['photo_id'] = $userinfo['photo_id'] == 0 ? 1 : $userinfo['photo_id'];		
					$userinfo['learncar_status'] = $userinfo['learncar_status'] == null ? '' : $userinfo['learncar_status'];  // 科目学习中
				} else {
					$userinfo['sex'] = '';
					$userinfo['age'] = '';
					$userinfo['identity_id'] = '';
					$userinfo['address'] = '';
					$userinfo['user_photo'] = '';
					$userinfo['license_num'] = '';
					$userinfo['photo_id'] = 0;
					$userinfo['learncar_status'] = '';  // 科目学习中
				}
				// 获取预约学车订单已完成的订单时间
				$sql = "SELECT `i_service_time`, `dc_money` FROM `cs_study_orders` WHERE `l_user_id` = $member_id AND `i_status` = 2";
				$stmt = $db->query($sql);
				$service_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$all_learn_time = 0;
				$total_price = 0;
				if($service_info) {
					foreach ($service_info as $key => $value) {
						$all_learn_time += $value['i_service_time'];
						$total_price += intval($value['dc_money']);
					}
				}
					
				$userinfo['all_learn_time'] = $all_learn_time; // 总学时
				$userinfo['learn_car_time'] = count($service_info);  // 学车次数
				$userinfo['total_price'] = $total_price;  // 总价
				
				$photo_arr = array(
					0=>'1.png',
					1=>"1.png",
					2=>"2.png",
					3=>"3.png",
					4=>"4.png",
					5=>"5.png",
					6=>"6.png",
					7=>"7.png",
					8=>"8.png",
					9=>"9.png",
					10=>"10.png",
					11=>"11.png",
					12=>"12.png",
					13=>"13.png",
					14=>"14.png",
					15=>"15.png",
					16=>"16.png"
				);

				$userinfo['photo_url'] = $photo_arr[$userinfo['photo_id']];

				// 获取当前学员已通过科目
				$sql = "SELECT o.`time_config_id`, o.`l_coach_id`, o.`s_order_no`, o.`deal_type`, o.`dc_money`, a.`year`, a.`month`, a.`day` FROM `cs_study_orders` as o LEFT JOIN `cs_coach_appoint_time` as a ON a.`id` = o.`appoint_time_id` WHERE o.`l_user_id` = $member_id AND a.`year` != '' AND o.`i_status` = 2";
				$sql .= " ORDER BY o.`dt_order_time` DESC";
				$stmt = $db->query($sql);
				$lesson_res = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if($lesson_res) {
					$time_config_id = array();
					$list = array();
					foreach ($lesson_res as $key => $value) {
						$time_config_id = array_filter(explode(',', $value['time_config_id']));

						$list[$key]['deal_type'] = $value['deal_type'] == 1 ? '支付宝' : '线下支付';
						if($value['deal_type'] == 1) {
							$list[$key]['deal_type'] = '支付宝';

						} else if($value['deal_type'] == 2) {
							$list[$key]['deal_type'] = '线下支付';

						} else if($value['deal_type'] == 3) {
							$list[$key]['deal_type'] = '微信';

						}

						// 查找科目和牌照
						$sql = "SELECT `time_lesson_config_id`, `time_lisence_config_id` FROM `cs_current_coach_time_configuration` ";
						$sql .= " WHERE `time_config_id` IN (".implode(',', $time_config_id).") AND `coach_id` = ".$value['l_coach_id']." AND `year` = ".$value['year']." AND `month` = ".$value['month']." AND `day` = ".$value['day'];
						$stmt = $db->query($sql);
						$time_lesson_arr = $stmt->fetch(PDO::FETCH_ASSOC);

						$time_lesson_config_id = json_decode($time_lesson_arr['time_lesson_config_id'], true);
						$time_lisence_config_id = json_decode($time_lesson_arr['time_lisence_config_id'], true);

						$lesson_name_arr = array();
						$lisence_name_arr = array();

						if($time_lesson_config_id && $time_lisence_config_id) {
							foreach ($time_config_id as $k => $v) {
								if(in_array($v, array_keys($time_lesson_config_id))) {
									$lesson_name_arr[] = $time_lesson_config_id[$v];
									$lisence_name_arr[] = $time_lisence_config_id[$v];
								}
							}
						}

						$list[$key]['lesson_name'] = implode(',', array_unique($lesson_name_arr));
						$list[$key]['lisence_name'] = implode(',', array_unique($lisence_name_arr));

						// 获取学校名称
						$sql = "SELECT s.`s_school_name`, c.`s_coach_name`,s.`s_address` FROM `cs_school` as s LEFT JOIN `cs_coach` as c ON c.`s_school_name_id` = s.`l_school_id` WHERE c.`l_coach_id` = ".$value['l_coach_id'];
						$stmt = $db->query($sql);
						$school_info = $stmt->fetch(PDO::FETCH_ASSOC);
						$list[$key]['school_name'] = $school_info['s_school_name'] == null ? '' : $school_info['s_school_name'];
						$list[$key]['coach_name'] = $school_info['s_coach_name'] == null ? '' : $school_info['s_coach_name'];
						$list[$key]['s_address'] = $school_info['s_address'] == null ? '' : $school_info['s_address'];

						// 获取学费统计
						$list[$key]['money'] = $value['dc_money'];

						// 获取学车时间
						$sql = "SELECT `start_time`, `end_time` FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config_id).")";
						$stmt = $db->query($sql);
						$time_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
						$time_config_arr = array();
						foreach ($time_arr as $k => $v) {
							$time_config_arr[] = $v['start_time'].':00 -'.$v['end_time'].':00';
						}
						$list[$key]['time_config'] = implode(',', $time_config_arr);
						$list[$key]['date_time'] = $value['year'].'-'.$value['month'].'-'.$value['day'].' '.implode(',', $time_config_arr);

						// 获取教练评价
						$sql = "SELECT * FROM `cs_student_comment` WHERE `user_id` = $member_id AND `coach_id` = ".$value['l_coach_id']." AND `order_no` = ".$value['s_order_no'];
						$stmt = $db->query($sql);
						$student_comment_arr = $stmt->fetch(PDO::FETCH_ASSOC);
						$list[$key]['star'] = $student_comment_arr['star_num'] == null ? '' : intval($student_comment_arr['star_num']);
						$list[$key]['content'] = $student_comment_arr['content'] == null ? '' : $student_comment_arr['content'];

						// 
					}

					$userinfo['learn_car_arr'] = $list;	
				} else {
					$userinfo['learn_car_arr'] = array();
				}

				$data = array('code'=>200, 'data'=>$userinfo);
				echo json_encode($data);
				exit();
			}

		}catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('get_member_info:params[type:'.$type.',member_id:'.$member_id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}

	}

?>