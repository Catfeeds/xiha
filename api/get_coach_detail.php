<?php  

	/**
	 * 获取教练详情配置(包含已经被预约或者没被预约状态)
	 * @param $week_id 周ID 1,2,3,4,6,7
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
	$app->get('/id/:id','getCoachDetail');
	$app->run();

	// 获取教练时间
	function getCoachDetail($id) {
		$sql = "SELECT * FROM `cs_coach` WHERE `l_coach_id` = ".$id;

		try {
			$db = getConnection();

			// 获取教练信息
			$stmt = $db->query($sql);
			$coach_info = $stmt->fetchObject();

			if($coach_info) {
				// 获取学校名称
				$sql = "SELECT * FROM `cs_school` WHERE `l_school_id` = ".$coach_info->s_school_name_id;
				$stmt = $db->query($sql);
				$school_detail = $stmt->fetch(PDO::FETCH_ASSOC);
				if($school_detail) {
					$coach_info->school_name = $school_detail['s_school_name'];
				} else {
					$coach_info->school_name = '嘻哈驾校';
				}

				// 获取所带学生数
				$sql = "SELECT count(*) as num FROM `cs_study_orders` WHERE `l_coach_id` = $id AND `i_status` = 2";
				$stmt = $db->query($sql);
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if($row) {
					$coach_info->students_num = $row['num'];
				} else {
					$coach_info->students_num = 0;
				}

				// 获取通过率
				$sql = "SELECT count(*) as num FROM `cs_study_orders` WHERE `l_coach_id` = $id AND `i_status` = 2";
				$stmt = $db->query($sql);
				$order_num = $stmt->fetch(PDO::FETCH_ASSOC);
				if($order_num) {
					if($coach_info->students_num != 0) {
						$coach_info->pass_rate = floor(($order_num['num'] / $coach_info->students_num)*100);
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
					
				// $sql = "SELECT count(`id`) as num FROM `cs_coach_comment` WHERE `coach_id` = $id AND `coach_star` >= 3";

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

				// 删除之前的所有已过期时间
				$sql = "DELETE FROM `cs_current_coach_time_configuration` WHERE `current_time` < ".(time() - 24*3600)." AND `coach_id` = $id";
				$stmt = $db->query($sql);

				// 获取当前配置的教练时间表
				$sql = "SELECT `time_config_money_id`,`time_lisence_config_id`, `time_lesson_config_id`, `year`, `month`, `day` FROM `cs_current_coach_time_configuration` WHERE `coach_id` = $id ORDER BY `current_time` ASC";
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
								$sql = "SELECT * FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config_id).") AND `status` = 1";
								$stmt = $db->query($sql);
								$time_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

								// // 删除之前的所有已过期时间
								// $sql = "DELETE FROM `cs_current_coach_time_configuration` WHERE `current_time` < ".(time() - 24*3600)." AND `coach_id` = $id";
								// $stmt = $db->query($sql);
								$sql = "SELECT o.`l_study_order_id`, o.`time_config_id` FROM `cs_coach_appoint_time` as t LEFT JOIN `cs_study_orders` as o ON o.`appoint_time_id` = t.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND t.`coach_id` = '{$id}' AND t.`year` = '{$year}' AND t.`month` = '{$month}' AND t.`day` = '{$day}'";
								$stmt = $db->query($sql);
								$time_config_ids = array();
								$time_config_id_arr = array();
								$is_appoint = $stmt->fetchAll(PDO::FETCH_ASSOC);
								// echo "<pre>";
								// print_r($is_appoint);
								if($is_appoint) {
									foreach ($is_appoint as $k => $v) {
										$time_config_ids = array_filter(explode(',', $v['time_config_id']));
										foreach ($time_config_ids as $e => $t) {
											$time_config_id_arr[] = $t;
										}
									}
								}
		
								// print_r($time_config_id_arr);
								foreach ($time_list as $k => $v) {
									
									$time_list[$k]['final_price'] = $money[$k];
									$time_list[$k]['license_no'] = $lisence[$k];
									$time_list[$k]['subjects'] = $lesson[$k];
									$start_minute = $v['start_minute'] == 0 ? '00' : $v['start_minute'];
									$end_minute = $v['end_minute'] == 0 ? '00' : $v['end_minute'];
									$time_list[$k]['start_time'] = $v['start_time'].':'.$start_minute;
									$time_list[$k]['end_time'] = $v['end_time'].':'.$end_minute;
									// $sql = "SELECT o.`l_study_order_id` FROM `cs_coach_appoint_time` as t LEFT JOIN `cs_study_orders` as o ON o.`appoint_time_id` = t.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND t.`coach_id` = $id AND t.`year` = $year AND t.`month` = $month AND t.`day` = $day AND t.`time_config_id` LIKE '%$v[id]%'";
									// $stmt = $db->query($sql);
									// $is_appoint = $stmt->fetch(PDO::FETCH_ASSOC);
									// if($is_appoint) {
									// 	$time_list[$k]['is_appointed'] = 1; //被预约
									// } else {
									// 	$time_list[$k]['is_appointed'] = 2; //没被预约
									// }
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


							} else {
								// 有时间配置的判断中没有设置时间配置

								// 获取所有时间段
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
								$stmt = $db->query($sql);
								$_time_config_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
								foreach ($_time_config_list as $k => $v) {
									$_time_config_list[$k]['final_price'] = $v['price'];
									$start_minute = $v['start_minute'] == 0 ? '00' : $v['start_minute'];
									$end_minute = $v['end_minute'] == 0 ? '00' : $v['end_minute'];
									$_time_config_list[$k]['start_time'] = $v['start_time'].':'.$start_minute;
									$_time_config_list[$k]['end_time'] = $v['end_time'].':'.$end_minute;

									// $_time_config_list[$key]['is_appointed'] = 2;

									// 获取是否已经被预约
									// $sql = "SELECT o.`l_study_order_id` FROM `cs_coach_appoint_time` as t LEFT JOIN `cs_study_orders` as o ON o.`appoint_time_id` = t.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND t.`coach_id` = $id AND t.`year` = '".$value['year']."' AND t.`month` = '".$value['month']."' AND t.`day` = '".$value['day']."' AND t.`time_config_id` LIKE '%$value[id]%'";
									// $stmt = $db->query($sql);
									// $is_appoint = $stmt->fetch(PDO::FETCH_ASSOC);
									// if($is_appoint) {
									// 	$_time_config_list[$k]['is_appointed'] = 1; //被预约
									// } else {
									// 	$_time_config_list[$k]['is_appointed'] = 2; //没被预约
									// }
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
								$coach_time_list[$key]['time_list'] = $_time_config_list;
								$coach_time_list[$key]['date'] = $date;
								$coach_time_list[$key]['timestamp'] = $timestamp;
							}

						}

						// 判断有的日期是否选择了
						$date_config = getCoachTimeConfig();
						$coach_time_list_ = array();
						foreach ($date_config as $key => $value) {
							if(!in_array($value['date_format'], $date_arr)) {
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
								$stmt = $db->query($sql);
								$_time_config_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
								foreach ($_time_config_list as $k => $v) {
									$_time_config_list[$k]['final_price'] = $v['price'];
									$start_minute = $v['start_minute'] == 0 ? '00' : $v['start_minute'];
									$end_minute = $v['end_minute'] == 0 ? '00' : $v['end_minute'];
									$_time_config_list[$k]['start_time'] = $v['start_time'].':'.$start_minute;
									$_time_config_list[$k]['end_time'] = $v['end_time'].':'.$end_minute;

									if(!empty($time_config_id_arr)) {
										if(in_array($v['id'], $time_config_id_arr)) {
											$_time_config_list[$k]['is_appointed'] = 1; //被预约
										} else {
											$_time_config_list[$k]['is_appointed'] = 2; //没被预约
										}
									} ELSE {
										$_time_config_list[$k]['is_appointed'] = 2; //没被预约	
									}
										
								}
									
								$coach_time_list_[$key]['time_list'] = $_time_config_list;
								$coach_time_list_[$key]['date'] = $value['date_format'];
								$coach_time_list_[$key]['timestamp'] = $value['timestamp'];
							}
						}	

						// 数组合并
						$coach_time_list_merge = array_merge($coach_time_list, $coach_time_list_);
						
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
						$stmt = $db->query($sql);
						$_time_config_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
						foreach ($_time_config_list as $key => $value) {
							$_time_config_list[$key]['final_price'] = $value['price'];
						}

						$date_config = getCoachTimeConfig(); // 获取日期配置
						$coach_time_list = array();
						foreach ($date_config as $key => $value) {
							
							$sql = "SELECT o.`l_study_order_id` FROM `cs_coach_appoint_time` as t LEFT JOIN `cs_study_orders` as o ON o.`appoint_time_id` = t.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND t.`coach_id` = $id AND t.`year` = '".$value['year']."' AND t.`month` = '".$value['month']."' AND t.`day` = '".$value['day']."'";
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
								// 获取是否已经被预约
								$start_minute = $v['start_minute'] == 0 ? '00' : $v['start_minute'];
								$end_minute = $v['end_minute'] == 0 ? '00' : $v['end_minute'];
								$_time_config_list[$k]['start_time'] = $v['start_time'].':'.$start_minute;
								$_time_config_list[$k]['end_time'] = $v['end_time'].':'.$end_minute;
								
								// $sql = "SELECT o.`l_study_order_id` FROM `cs_coach_appoint_time` as t LEFT JOIN `cs_study_orders` as o ON o.`appoint_time_id` = t.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND t.`coach_id` = $id AND t.`year` = '".$value['year']."' AND t.`month` = '".$value['month']."' AND t.`day` = '".$value['day']."' AND t.`time_config_id` LIKE '%$v[id]%'";
								// $stmt = $db->query($sql);
								// $is_appoint = $stmt->fetch(PDO::FETCH_ASSOC);
								// if($is_appoint) {
								// 	$_time_config_list[$k]['is_appointed'] = 1; //被预约
								// } else {
								// 	$_time_config_list[$k]['is_appointed'] = 2; //没被预约
								// }
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
							$coach_time_list[$key]['time_list'] = $_time_config_list;
							$coach_time_list[$key]['date'] = $value['date'];
							$coach_time_list[$key]['timestamp'] = $value['timestamp'];
						}
						$coach_info->time_config_list = array_values($coach_time_list);
						$db = null;
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