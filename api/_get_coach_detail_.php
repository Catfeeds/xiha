<?php  

	/**
	 * 获取教练详情配置(包含已经被预约或者没被预约状态)
	 * @param $lesson_type 科目 
	 * @param $licence_type 牌照 
	 * @param $coach_id 教练ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getCoachDetail');
	$app->run();

	// 获取教练时间
	function getCoachDetail() {
		Global $app, $crypt, $lisence_config, $lesson_config;
		$request = $app->request();
		$id = $request->params('id');
		$lesson_type = $request->params('lesson_type');
		$licence_type = $request->params('licence_type');

		try {
			$db = getConnection();

			// 获取教练信息
			$sql = "SELECT `l_coach_id`, `s_coach_name`, `s_teach_age`, `s_coach_sex`, `s_coach_imgurl`, `s_coach_phone`, `s_school_name_id`, `s_coach_lisence_id`, `s_coach_lesson_id`, `s_coach_car_id`, `s_coach_car_no`, `dc_coach_distance_x`, `dc_coach_distance_y`, `coach_student_distance`, `i_coach_star`, `good_coach_star`, `i_service_count`, `i_success_count`, `s_coach_address`, `i_type`, `order_receive_status`, `integrated_excellent`, `province_id`, `city_id`, `area_id`, `total_price`, `coach_star_count`, `s_coach_content`, `user_id`, `s_am_subject`, `s_pm_subject`, `s_am_time_list`, `s_pm_time_list` FROM `cs_coach` WHERE `l_coach_id` = ".$id;
			$stmt = $db->query($sql);
			$coach_info = $stmt->fetchObject();

			if($coach_info) {
				// 获取学校名称
				$sql = "SELECT `s_school_name` FROM `cs_school` WHERE `l_school_id` = ".$coach_info->s_school_name_id;
				$stmt = $db->query($sql);
				$school_detail = $stmt->fetch(PDO::FETCH_ASSOC);
				if($school_detail) {
					$coach_info->school_name = $school_detail['s_school_name'];
				} else {
					$coach_info->school_name = '嘻哈驾校';
				}

				// 获取所带学员总数
				$sql = "SELECT count(`l_study_order_id`) as num FROM `cs_study_orders` WHERE `l_coach_id` = $id AND `i_status` = 2";
				$stmt = $db->query($sql);
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if($row) {
					$coach_info->students_num = $row['num'];
				} else {
					$coach_info->students_num = 0;
				}

				// 获取通过率
				$sql = "SELECT count(`l_study_order_id`) as num FROM `cs_study_orders` WHERE `l_coach_id` = $id";
				$stmt = $db->query($sql);
				$order_num = $stmt->fetch(PDO::FETCH_ASSOC);
				if($order_num) {
					if($coach_info->students_num != 0) {
						$coach_info->pass_rate = floor(($coach_info->students_num / $order_num['num'])*100);
					} else {
						$coach_info->pass_rate = 0;
					}			
				} else {
					$coach_info->pass_rate = 0;
				}
				
				// 获得好评率
				if($coach_info->coach_star_count != 0) {
					$coach_info->good_comment_rate = floor(($coach_info->good_coach_star / $coach_info->coach_star_count) * 100); 
				} else {
					$coach_info->good_comment_rate = 0;
				}

				if($coach_info->s_coach_car_id) {

					// 获取车辆
					$sql = "SELECT * FROM `cs_cars` WHERE `id` IN (".$coach_info->s_coach_car_id.")";
					$stmt = $db->query($sql);
					$res_car = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$imgurl_arr = array();
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
					}
					
					if(file_exists(__DIR__.'/../sadmin/'.$coach_info->s_coach_imgurl)) {
						$coach_info->s_coach_imgurl = S_HTTP_HOST.$coach_info->s_coach_imgurl;
					} else {
						$coach_info->s_coach_imgurl = HTTP_HOST.$coach_info->s_coach_imgurl;
					}

					$coach_info->car_info = $res_car;
				} else {
					$arr = array(
				    	array(
				            "id" => '',
				            "name" => '',
				            "car_no" => '',
				            "addtime" => '',
				            "imgurl" => array(),
				            "car_type" => '',
				            "school_id" => ''
				        )
					);

					$coach_info->car_info = $arr;
				}

				// 获取驾校的时间配置
				$sql = "SELECT `s_time_list`, `is_automatic` FROM `cs_school_config` WHERE `l_school_id` = '{$coach_info->s_school_name_id}'";
				$stmt = $db->query($sql);
				$school_config = $stmt->fetch(PDO::FETCH_ASSOC);
				$s_time_list = array();
				$is_automatic = 1;
				if($school_config) {
					$s_time_list = isset($school_config['s_time_list']) ? explode(',', $school_config['s_time_list']) : array();
					$is_automatic = $school_config['is_automatic'];
				}

				// 获取教练时间配置
				$sql = "SELECT `s_am_subject`, `s_pm_subject`, `s_am_time_list`, `s_pm_time_list`, `s_coach_lisence_id`, `s_coach_lesson_id` FROM `cs_coach` WHERE `l_coach_id` = '{$id}'";
				$stmt = $db->query($sql);
				$_coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
				$s_am_subject = 2;
				$s_pm_subject = 3;
				$s_am_time_list = array();
				$s_pm_time_list = array();
				$s_coach_lisence_id_list = array();
				$s_coach_lesson_id_list = array();

				if($_coach_info) {
					$s_am_subject = $_coach_info['s_am_subject'];
					$s_pm_subject = $_coach_info['s_pm_subject'];
					$s_am_time_list = isset($_coach_info['s_am_time_list']) ? explode(',', $_coach_info['s_am_time_list']) : array(); 
					$s_pm_time_list = isset($_coach_info['s_pm_time_list']) ? explode(',', $_coach_info['s_pm_time_list']) : array();
					$s_coach_lisence_id_list = isset($_coach_info['s_coach_lisence_id']) ? explode(',', $_coach_info['s_coach_lisence_id']) : array();
					$s_coach_lesson_id_list = isset($_coach_info['s_coach_lesson_id']) ? explode(',', $_coach_info['s_coach_lesson_id']) : array();
				}

				if(!empty($s_am_time_list) && !empty($s_pm_time_list)) {
			 		$time_config_ids_arr = array_merge($s_am_time_list, $s_pm_time_list);
			 	}  else {
			 		$time_config_ids_arr = $s_time_list;
			 	}

				// 删除之前的所有已过期时间
				$sql = "DELETE FROM `cs_current_coach_time_configuration` WHERE `current_time` < ".(time() - 24*3600)." AND `coach_id` = $id";
				$stmt = $db->query($sql);

				$curr_time = date('Y-m-d', time());
				$_current_time = strtotime($curr_time) + 7 * 24 * 3600;
				
				// 获取当前配置的教练时间表
				$sql = "SELECT `time_config_money_id`,`time_lisence_config_id`, `time_lesson_config_id`, `year`, `month`, `day` FROM `cs_current_coach_time_configuration` WHERE `coach_id` = $id AND `current_time` < $_current_time ORDER BY `current_time` ASC";
				try {
					$stmt = $db->query($sql);
					$current_coach_time_config_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

					//有时间配置 
					if($current_coach_time_config_arr) {
						$coach_time_list = array();
						$date_arr = array();

						foreach ($current_coach_time_config_arr as $key => $value) {

							$config_money_arr = json_decode($value['time_config_money_id'], true);
							$config_lisence_arr = json_decode($value['time_lisence_config_id'], true);
							$config_lesson_arr = json_decode($value['time_lesson_config_id'], true);

							$year = $value['year'];
							$month = $value['month'];
							$day = $value['day'];
							$date_arr[] = $month.'-'.$day;
							$date = $month.'-'.$day;
							$timestamp = strtotime($year.'-'.$month.'-'.$day);
							
							// 有设置时间配置ID
							if($config_money_arr) {

								$time_config_id = array_keys($config_money_arr);
								$money = array_values($config_money_arr);
								$lisence = array_values($config_lisence_arr);
								$lesson = array_values($config_lesson_arr);

								// 获取当前时间配置
								$sql = "SELECT o.`l_study_order_id`, o.`time_config_id` FROM `cs_coach_appoint_time` as t LEFT JOIN `cs_study_orders` as o ON o.`appoint_time_id` = t.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND t.`coach_id` = '{$id}' AND t.`year` = '{$year}' AND t.`month` = '{$month}' AND t.`day` = '{$day}'";
								$stmt = $db->query($sql);
								$time_config_ids = array();
								$time_config_id_arr = array();
								$is_appoint = $stmt->fetchAll(PDO::FETCH_ASSOC);
								if($is_appoint) {
									foreach ($is_appoint as $k => $v) {
										$time_config_ids = array_filter(explode(',', $v['time_config_id']));
										foreach ($time_config_ids as $e => $t) {
											$time_config_id_arr[] = $t;
										}
									}
								}
								$sql = "SELECT * FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config_id).") AND `status` = 1";
								$stmt = $db->query($sql);
								$time_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

								// // 删除之前的所有已过期时间
								// $sql = "DELETE FROM `cs_current_coach_time_configuration` WHERE `current_time` < ".(time() - 24*3600)." AND `coach_id` = $id";
								// $stmt = $db->query($sql);

								$_coach_time_list = array();
								foreach ($time_list as $k => $v) {
									$time_list[$k]['final_price'] = $money[$k];
									$time_list[$k]['license_no'] = $lisence[$k];
									$time_list[$k]['subjects'] = $lesson[$k];
									$start_minute = $v['start_minute'] == 0 ? '00' : $v['start_minute'];
									$end_minute = $v['end_minute'] == 0 ? '00' : $v['end_minute'];
									$start_time = sprintf('%02d', $v['start_time']);
									$end_time = sprintf('%02d', $v['end_time']);

									$time_list[$k]['start_time'] = $start_time.':'.$start_minute;
									$time_list[$k]['end_time'] = $end_time.':'.$end_minute;

									// $sql = "SELECT o.`l_study_order_id`, o.`time_config_id` FROM `cs_coach_appoint_time` as t LEFT JOIN `cs_study_orders` as o ON o.`appoint_time_id` = t.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND t.`coach_id` = $id AND t.`year` = $year AND t.`month` = $month AND t.`day` = $day AND t.`time_config_id` LIKE '%$v[id]%'";
									if(!empty($time_config_id_arr)) {
										if(in_array($v['id'], $time_config_id_arr)) {
											$time_list[$k]['is_appointed'] = 1; //被预约
										} else {
											$time_list[$k]['is_appointed'] = 2; //没被预约
										}
									} else {
										$time_list[$k]['is_appointed'] = 2; //没被预约

									}
										
										
									$coach_time_list[$key]['time_list'] = $time_list;
									$coach_time_list[$key]['date'] = $date;
									$coach_time_list[$key]['timestamp'] = $timestamp;
								}
							}
						}

						// 判断有的日期是否选择了
						$date_config = getCoachTimeConfig();
						$coach_time_list_ = array();

						foreach ($date_config as $key => $value) {

							// 筛选没有设置时间段的日期
							if(!in_array($value['date_format'], $date_arr)) {
								// 获取是否已经被预约
								$sql = "SELECT o.`l_study_order_id`, o.`time_config_id` FROM `cs_coach_appoint_time` as t LEFT JOIN `cs_study_orders` as o ON o.`appoint_time_id` = t.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND t.`coach_id` = $id AND t.`year` = '".$value['year']."' AND t.`month` = '".$value['month']."' AND t.`day` = '".$value['day']."'";
								$stmt = $db->query($sql);
								$time_config_ids = array();
								$time_config_id_arr = array();
								$is_appoint = $stmt->fetchAll(PDO::FETCH_ASSOC);
								if($is_appoint) {
									foreach ($is_appoint as $k => $v) {
										$time_config_ids = array_filter(explode(',', $v['time_config_id']));
										foreach ($time_config_ids as $e => $t) {
											$time_config_id_arr[] = $t;
										}
									}
								}
								$sql = "SELECT * FROM `cs_coach_time_config` WHERE `status` = 1";
								if(!empty($time_config_ids_arr)) {
									$sql .= " AND `id` IN (".implode(',', $time_config_ids_arr).")";
								}
								$stmt = $db->query($sql);
								$_time_config_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
								foreach ($_time_config_list as $k => $v) {

									// 牌照按照基础信息设置
									if(count($s_coach_lisence_id_list) == 1) {
										$_time_config_list[$k]['license_no'] = $lisence_config[$s_coach_lisence_id_list[0]];
									}
									if(count($s_coach_lesson_id_list) == 1) {
										$_time_config_list[$k]['subjects'] = $lesson_config[$s_coach_lesson_id_list[0]];
									}

									// 如果教练设置了上午时间和下午时间
									if(!empty($s_am_time_list) && !empty($s_pm_time_list)) {
										if(in_array($v['id'], $s_am_time_list)) {
											if($s_am_subject == 1) {
												$_time_config_list[$k]['subjects'] = '科目一';

											} else if($s_am_subject == 2) {
												$_time_config_list[$k]['subjects'] = '科目二';

											} else if($s_am_subject == 3) {
												$_time_config_list[$k]['subjects'] = '科目三';

											} else if($s_am_subject == 4) {
												$_time_config_list[$k]['subjects'] = '科目四';

											}
										}

										if(in_array($v['id'], $s_pm_time_list)) {

											if($s_pm_subject == 1) {
												$_time_config_list[$k]['subjects'] = '科目一';

											} else if($s_pm_subject == 2) {
												$_time_config_list[$k]['subjects'] = '科目二';

											} else if($s_pm_subject == 3) {
												$_time_config_list[$k]['subjects'] = '科目三';

											} else if($s_pm_subject == 4) {
												$_time_config_list[$k]['subjects'] = '科目四';

											}
										}

									// 教练没有设置，驾校有设置
									} else {
										if($v['end_time'] <= 12) {
											if($s_am_subject == 1) {
												$_time_config_list[$k]['subjects'] = '科目一';

											} else if($s_am_subject == 2) {
												$_time_config_list[$k]['subjects'] = '科目二';

											} else if($s_am_subject == 3) {
												$_time_config_list[$k]['subjects'] = '科目三';

											} else if($s_am_subject == 4) {
												$_time_config_list[$k]['subjects'] = '科目四';

											}
										} else {

											if($s_pm_subject == 1) {
												$_time_config_list[$k]['subjects'] = '科目一';

											} else if($s_pm_subject == 2) {
												$_time_config_list[$k]['subjects'] = '科目二';

											} else if($s_pm_subject == 3) {
												$_time_config_list[$k]['subjects'] = '科目三';

											} else if($s_pm_subject == 4) {
												$_time_config_list[$k]['subjects'] = '科目四';

											}
										}
									}

									$_time_config_list[$k]['final_price'] = $v['price'];

									$start_minute = $v['start_minute'] == 0 ? '00' : $v['start_minute'];
									$end_minute = $v['end_minute'] == 0 ? '00' : $v['end_minute'];
									$start_time = sprintf('%02d', $v['start_time']);
									$end_time = sprintf('%02d', $v['end_time']);

									$_time_config_list[$k]['start_time'] = $start_time.':'.$start_minute;
									$_time_config_list[$k]['end_time'] = $end_time.':'.$end_minute;

									if(!empty($time_config_id_arr)) {
										if(in_array($v['id'], $time_config_id_arr)) {
											$_time_config_list[$k]['is_appointed'] = 1; //被预约
										} else {
											$_time_config_list[$k]['is_appointed'] = 2; //没被预约
										}
									} else {
										$_time_config_list[$k]['is_appointed'] = 2; //没被预约

									}
										

								}
								if($is_automatic == 1) {
									$coach_time_list_[$key]['time_list'] = $_time_config_list;
								} else {
									$coach_time_list_[$key]['time_list'] = array();
								}
								$coach_time_list_[$key]['date'] = $value['date_format'];
								$coach_time_list_[$key]['timestamp'] = $value['timestamp'];
							}
						}	

						// 数组合并
						$coach_time_list_merge = array_merge($coach_time_list, $coach_time_list_);
						// echo "<pre>";
						// print_r($coach_time_list_merge);

						$year = date('Y', time());

						foreach ($coach_time_list_merge as $key => $value) {
							$date = explode('-', $value['date']);
							$month = $date[0];
							$day = $date[1];
							$_coach_time_list = array();
							
							if($month == 1) {
								$year = '2017';
							}
							$sql = "SELECT o.`l_study_order_id`, o.`time_config_id` FROM `cs_coach_appoint_time` as t LEFT JOIN `cs_study_orders` as o ON o.`appoint_time_id` = t.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND t.`coach_id` = $id AND t.`year` = '".$year."' AND t.`month` = '".$month."' AND t.`day` = '".$day."'";
							$stmt = $db->query($sql);
							$time_config_ids = array();
							$time_config_id_arr = array();
							$is_appoint = $stmt->fetchAll(PDO::FETCH_ASSOC);
							if($is_appoint) {
								foreach ($is_appoint as $k => $v) {
									$time_config_ids = array_filter(explode(',', $v['time_config_id']));
									foreach ($time_config_ids as $e => $t) {
										$time_config_id_arr[] = $t;
									}
								}
							}

							foreach ($value['time_list'] as $k => $v) {

								// 判断是否符合筛选的牌照和科目
								if(trim($v['license_no']) == trim($licence_type) && trim($v['subjects']) == trim($lesson_type)) {

									$_coach_time_list[$k]['id'] = $v['id'];	
									$_coach_time_list[$k]['start_time'] = $v['start_time'];	
									$_coach_time_list[$k]['end_time'] = $v['end_time'];	
									$_coach_time_list[$k]['license_no'] = $v['license_no'];	
									$_coach_time_list[$k]['subjects'] = $v['subjects'];	
									$_coach_time_list[$k]['price'] = $v['price'];	
									$_coach_time_list[$k]['school_id'] = $v['school_id'];	
									$_coach_time_list[$k]['addtime'] = date('Y-m-d H:i', $v['addtime']);	
									$_coach_time_list[$k]['status'] = $v['status'];	
									$_coach_time_list[$k]['final_price'] = $v['final_price'];	

									if(!empty($time_config_id_arr)) {
										if(in_array($v['id'], $time_config_id_arr)) {
											$_coach_time_list[$k]['is_appointed'] = 1; //被预约
										} else {
											$_coach_time_list[$k]['is_appointed'] = 2; //没被预约
										}
									} else {
										$_coach_time_list[$k]['is_appointed'] = 2; //没被预约

									}
										

								} else {
									unset($v);
								}
							}
		
							$coach_time_list_merge[$key]['time_list'] = array_values($_coach_time_list);
							$coach_time_list_merge[$key]['date'] = $value['date'];
							$coach_time_list_merge[$key]['timestamp'] = $value['timestamp'];
						}

						// 排序多维数组
						$coach_time_list_merge = multiArraySort($coach_time_list_merge, 'timestamp');
						$coach_info->time_config_list = array_values($coach_time_list_merge);
						$db = null;
						$data = array('code'=>200, 'data'=>$coach_info);
						echo json_encode($data);

					} else {
						// 如果所有都没有设置时间
						// 获取所有时间段

						$sql = "SELECT * FROM `cs_coach_time_config` WHERE `status` = 1";
						if(!empty($time_config_ids_arr)) {
							$sql .= " AND `id` IN (".implode(',', $time_config_ids_arr).")";
						}

						$stmt = $db->query($sql);
						$_time_config_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
						foreach ($_time_config_list as $key => $value) {
							$_time_config_list[$key]['final_price'] = $value['price'];
							
							// 牌照按照基础信息设置
							if(count($s_coach_lisence_id_list) == 1) {
								$_time_config_list[$key]['license_no'] = $lisence_config[$s_coach_lisence_id_list[0]];
							}
							if(count($s_coach_lesson_id_list) == 1) {
								$_time_config_list[$key]['subjects'] = $lesson_config[$s_coach_lesson_id_list[0]];
							}

							// 如果教练设置了上午时间和下午时间
							if(!empty($s_am_time_list) && !empty($s_pm_time_list)) {
								if(in_array($value['id'], $s_am_time_list)) {
									if($s_am_subject == 1) {
										$_time_config_list[$key]['subjects'] = '科目一';

									} else if($s_am_subject == 2) {
										$_time_config_list[$key]['subjects'] = '科目二';

									} else if($s_am_subject == 3) {
										$_time_config_list[$key]['subjects'] = '科目三';

									} else if($s_am_subject == 4) {
										$_time_config_list[$key]['subjects'] = '科目四';

									}
								}

								if(in_array($value['id'], $s_pm_time_list)) {

									if($s_pm_subject == 1) {
										$_time_config_list[$key]['subjects'] = '科目一';

									} else if($s_pm_subject == 2) {
										$_time_config_list[$key]['subjects'] = '科目二';

									} else if($s_pm_subject == 3) {
										$_time_config_list[$key]['subjects'] = '科目三';

									} else if($s_pm_subject == 4) {
										$_time_config_list[$key]['subjects'] = '科目四';

									}
								}

							// 教练没有设置，驾校有设置
							} else {
								if($value['end_time'] <= 12) {
									if($s_am_subject == 1) {
										$_time_config_list[$key]['subjects'] = '科目一';

									} else if($s_am_subject == 2) {
										$_time_config_list[$key]['subjects'] = '科目二';

									} else if($s_am_subject == 3) {
										$_time_config_list[$key]['subjects'] = '科目三';

									} else if($s_am_subject == 4) {
										$_time_config_list[$key]['subjects'] = '科目四';

									}
								} else {

									if($s_pm_subject == 1) {
										$_time_config_list[$key]['subjects'] = '科目一';

									} else if($s_pm_subject == 2) {
										$_time_config_list[$key]['subjects'] = '科目二';

									} else if($s_pm_subject == 3) {
										$_time_config_list[$key]['subjects'] = '科目三';

									} else if($s_pm_subject == 4) {
										$_time_config_list[$key]['subjects'] = '科目四';

									}
								}
							}

						}

						$date_config = getCoachTimeConfig(); // 获取日期配置
						$coach_time_list = array();
						$_coach_time_list = array();
						foreach ($date_config as $key => $value) {
							// 获取是否已经被预约
							$sql = "SELECT o.`l_study_order_id`, o.`time_config_id` FROM `cs_coach_appoint_time` as t LEFT JOIN `cs_study_orders` as o ON o.`appoint_time_id` = t.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND t.`coach_id` = $id AND t.`year` = '".$value['year']."' AND t.`month` = '".$value['month']."' AND t.`day` = '".$value['day']."'";
							$stmt = $db->query($sql);
							$time_config_ids = array();
							$time_config_id_arr = array();
							$is_appoint = $stmt->fetchAll(PDO::FETCH_ASSOC);
							if($is_appoint) {
								foreach ($is_appoint as $k => $v) {
									$time_config_ids = array_filter(explode(',', $v['time_config_id']));
									foreach ($time_config_ids as $e => $t) {
										$time_config_id_arr[] = $t;
									}
								}
							}
							foreach ($_time_config_list as $k => $v) {

								// 判断是否符合筛选的牌照和科目
								if(trim($v['license_no']) == trim($licence_type) && trim($v['subjects']) == trim($lesson_type)) {

									$_coach_time_list[$k]['id'] = $v['id'];	
									$_coach_time_list[$k]['start_time'] = $v['start_time'];	
									$_coach_time_list[$k]['end_time'] = $v['end_time'];	
									$_coach_time_list[$k]['license_no'] = $v['license_no'];	
									$_coach_time_list[$k]['subjects'] = $v['subjects'];	
									$_coach_time_list[$k]['price'] = $v['price'];	
									$_coach_time_list[$k]['school_id'] = $v['school_id'];	
									$_coach_time_list[$k]['addtime'] = date('Y-m-d H:i', $v['addtime']);		
									$_coach_time_list[$k]['status'] = $v['status'];	
									$_coach_time_list[$k]['final_price'] = $v['final_price'];	

									$start_minute = $v['start_minute'] == 0 ? '00' : $v['start_minute'];
									$end_minute = $v['end_minute'] == 0 ? '00' : $v['end_minute'];

									$start_time = sprintf('%02d', $v['start_time']);
									$end_time = sprintf('%02d', $v['end_time']);

									$_coach_time_list[$k]['start_time'] = $start_time.':'.$start_minute;
									$_coach_time_list[$k]['end_time'] = $end_time.':'.$end_minute;

									if(!empty($time_config_id_arr)) {
										if(in_array($v['id'], $time_config_id_arr)) {
											$_coach_time_list[$k]['is_appointed'] = 1; //被预约
										} else {
											$_coach_time_list[$k]['is_appointed'] = 2; //没被预约
										}
									} else {
										$_coach_time_list[$k]['is_appointed'] = 2; //没被预约

									}

								} else {
									continue;
								}

							}
							if($is_automatic == 1) {
								$coach_time_list[$key]['time_list'] = array_values($_coach_time_list);
							} else {
								$coach_time_list[$key]['time_list'] = array();
							}
							//$coach_time_list[$key]['time_list'] = $_coach_time_list;
							$coach_time_list[$key]['date'] = $value['date'];
							$coach_time_list[$key]['timestamp'] = $value['timestamp'];
						}

						// echo "<pre>";
						// print_r($coach_time_list);
						// exit();
						$db = null;
						$coach_info->time_config_list = array_values($coach_time_list);

						$data = array('code'=>200, 'data'=>$coach_info);
						echo json_encode($data);
					}

				} catch(PDOException $e) {
					// $data = array('code'=>1, 'data'=>$e->getMessage());
					setapilog('get_coach_detail:params[id:'.$id.'], error:'.$e->getMessage());
					$data = array('code'=>1, 'data'=>'网络错误');
					echo json_encode($data);
				}
			} else {
				$data = array('code'=>-2, 'data'=>'不存在教练');
				echo json_encode($data);
			}
		} catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('get_coach_detail:params[id:'.$id.'], error:'.$e->getMessage());		
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
		}
	}

	/**
	 * 生成可变的日期配置
	 * @param $id int 教练ID
	 * @return array 
	 */
	function getCoachTimeConfig() {
		$current_time = time();
		$year = date('Y', $current_time); //年
		$month = intval(date('m', $current_time)); //月
		$day = intval(date('d', $current_time)); //日

		// 构建一个时间
		$build_date_timestamp = mktime(0,0,0,$month,$day,$year);

		// 循环7天日期
		$date_config = array();
		for($i = 0; $i <= 6; $i++) {
			// $date_config['date'][] = date('m-d', strtotime("+".($i)." day")); //或者这种算法
			$date_config[$i]['year'] = date('Y', strtotime("+".($i)." day"));	
			$date_config[$i]['date'] = date('m-d', $build_date_timestamp + ( 24 * 3600 * $i));	
			$date_config[$i]['month'] = intval(date('m', $build_date_timestamp + ( 24 * 3600 * $i)));	
			$date_config[$i]['day'] = intval(date('d', $build_date_timestamp + ( 24 * 3600 * $i)));	
			$date_config[$i]['date_format'] = intval(date('m', $build_date_timestamp + ( 24 * 3600 * $i))).'-'.intval(date('d', $build_date_timestamp + ( 24 * 3600 * $i)));	
			$date_config[$i]['timestamp'] = strtotime(date('Y', strtotime("+".($i)." day")).'-'.$date_config[$i]['month'].'-'.$date_config[$i]['day']);
		}

		// 数据表获取当前时间配置
		// $sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config`";
		// $row = $this->_getAllRecords($sql);
		// $date_config['time'] = $row;

		return $date_config;
	}

	// 多维数组排序
	function multiArraySort($arr, $field, $sort = 'SORT_ASC') {
		$sort = array(
		        'direction' => $sort, //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
		        'field'     => $field,       //排序字段  
		);

		// 多维数组根据某个字段排序
		$arrSort = array();  
		foreach($arr AS $uniqid => $row){  
		    foreach($row AS $key=>$value){  
		        $arrSort[$key][$uniqid] = $value;  
		    }  
		}
		if($sort['direction']){  
		    array_multisort($arrSort[$sort['field']], constant($sort['direction']), $arr);  
		}
		return $arr;
	}

?>