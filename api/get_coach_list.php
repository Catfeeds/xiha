<?php  

	/**
	 * 我要约车
	 * @param $lat char 学员维度	31.856717
	 * @param $lng char 学员经度  	117.201239
	 * @param 教练初始经纬度 117.202059,31.859031
	 * @param $lisence_id int 牌照ID 1
	 * @param $lesson_id  int 科目iD 2
	 * @param $city_id  int 城市ID 2
	 * @param $type string 排序类型 default star distance time
	 * @param $start_time string 开始时间 (暂定)
	 * @param $end_time string 结束时间 (暂定)
	 * @param $time_config_id string 时间段ID (可选多个时间段)
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getCoachlist');
	$app->run();

	function getCoachlist() {
		Global $app,$crypt;
		$request = $app->request();
		$lat = $request->params('lat');
		$lng = $request->params('lng');
		$lat = isset($lat) ? $lat : '31.856717';
		$lng = isset($lng) ? $lng : '117.201239';

		$lisence_id = $request->params('lisence_id');
		$lesson_id = $request->params('lesson_id');
		// $city_id = $request->params('city_id'); 
		$type = $request->params('type');
		$sid = $request->params('sid');
		$start_time = strtotime($request->params('start_time')); //开始时间
		$end_time = strtotime($request->params('end_time')); //结束时间
		// $time_config_id = $request->params('time_config_id');
		$page = $request->params('page');
		$page = !empty($page) ? $page : 1;
		$page = isset($page) ? $page : 1;
		$limit = 10;
		$start = ($page - 1) * $limit;

		try {
			$db = getConnection();

			// 获取当前教练列表经纬度
			$sql = "SELECT `dc_coach_distance_x`, `dc_coach_distance_y`, `l_coach_id` FROM `cs_coach` WHERE `s_coach_lesson_id` LIKE '%".$lesson_id."%' AND `s_coach_lisence_id` LIKE '%".$lisence_id."%' AND `s_school_name_id` = '{$sid}'";
			// 循环保存数据
			$stmt = $db->query($sql);
			$coach_location = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($coach_location) {
				foreach ($coach_location as $key => $value) {

					// 保存所有教练数据
					getDistance($lat, $lng, $value['dc_coach_distance_y'], $value['dc_coach_distance_x'], $value['l_coach_id']);
				}
			} else {
				
			}

		} catch (PDOException $e) {
            $db = null;
			setapilog('get_coach_list:params[lat:'.$lat.',lng:'.$lng.',lisence_id:'.$lisence_id.',lesson_id:'.$lesson_id.',type:'.$type.',start_time:'.$start_time.',end_time:'.$end_time.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');

			exit(json_encode($data));
		}

		// 获取当前列表根据综合排序
		$sql = "SELECT s.`name` as `s_coach_car_name`, s.`car_type`, h.* FROM `cs_coach` as h LEFT JOIN `cs_cars` as s ON s.`id` = h.`s_coach_car_id` WHERE h.`s_coach_lesson_id` LIKE '%".$lesson_id."%' AND h.`s_coach_car_id` != '' AND h.`s_coach_lisence_id` LIKE '%".$lisence_id."%' AND h.`s_school_name_id` = ".$sid ." AND h.`order_receive_status` = 1";

		// 综合最优
		if($type == 'default') {
			$sql .= " ORDER BY `integrated_excellent` DESC";
			$sql .= " LIMIT $start, $limit";
			try {
				$stmt = $db->query($sql);
				$coach = $stmt->fetchAll(PDO::FETCH_ASSOC);	

				foreach ($coach as $key => $value) {
					$coach[$key]['coach_student_distance'] = getDistance($lat, $lng, $value['dc_coach_distance_y'], $value['dc_coach_distance_x'], $value['l_coach_id']);

					$sql = "SELECT `s_school_name` FROM `cs_school` WHERE `l_school_id` = ".$value['s_school_name_id'];
					$stmt = $db->query($sql);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$coach[$key]['s_school_name'] = $row['s_school_name'] == null ? '' : $row['s_school_name'];
					$coach[$key]['s_coach_car_name'] = $value['s_coach_car_name'] == null ? '' : $value['s_coach_car_name'];
					$coach[$key]['car_type'] = $value['car_type'] == null ? 0 : $value['car_type'];

					// 获取头像

					if(file_exists(__DIR__.'/../sadmin/'.$value['s_coach_imgurl'])) {
						$coach[$key]['s_coach_imgurl'] = S_HTTP_HOST.$value['s_coach_imgurl'];
					} else {
						$coach[$key]['s_coach_imgurl'] = HTTP_HOST.$value['s_coach_imgurl'];
					}
				}
				$db = null;
				$data = array('code'=>200, 'data'=>$coach);
				exit(json_encode($data));

			} catch(PDOException $e) {
                $db = null;
				setapilog('get_coach_list:params[lat:'.$lat.',lng:'.$lng.',lisence_id:'.$lisence_id.',lesson_id:'.$lesson_id.',type:'.$type.',start_time:'.$start_time.',end_time:'.$end_time.'], error:'.$e->getMessage());	
				$data = array('code'=>1, 'data'=>'网络错误');
				exit(json_encode($data));
			}

		// 星级
		} else if($type == 'star') {

			$sql .= " ORDER BY `i_coach_star` DESC";
			$sql .= " LIMIT $start, $limit";

			try {
				$stmt = $db->query($sql);
				$coach = $stmt->fetchAll(PDO::FETCH_ASSOC);	

				foreach ($coach as $key => $value) {
					$coach[$key]['coach_student_distance'] = getDistance($lat, $lng, $value['dc_coach_distance_y'], $value['dc_coach_distance_x'], $value['l_coach_id']);

					$sql = "SELECT `s_school_name` FROM `cs_school` WHERE `l_school_id` = ".$value['s_school_name_id'];
					$stmt = $db->query($sql);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$coach[$key]['s_school_name'] = $row['s_school_name'] == null ? '' : $row['s_school_name'];

					// 获取
					if(file_exists(__DIR__.'/../sadmin/'.$value['s_coach_imgurl'])) {
						$coach[$key]['s_coach_imgurl'] = S_HTTP_HOST.$value['s_coach_imgurl'];
					} else {
						$coach[$key]['s_coach_imgurl'] = HTTP_HOST.$value['s_coach_imgurl'];
					}
				}
				$db = null;
				$data = array('code'=>200, 'data'=>$coach);
				exit(json_encode($data));

			} catch(PDOException $e) {
                $db = null;
				setapilog('get_coach_list:params[lat:'.$lat.',lng:'.$lng.',lisence_id:'.$lisence_id.',lesson_id:'.$lesson_id.',type:'.$type.',start_time:'.$start_time.',end_time:'.$end_time.'], error:'.$e->getMessage());	
				$data = array('code'=>1, 'data'=>'网络错误');
				exit(json_encode($data));
			}

		// 距离
		} else if($type == 'distance') {

			try {
				$sql .= " LIMIT $start, $limit";
				$stmt = $db->query($sql);
				$coach = $stmt->fetchAll(PDO::FETCH_ASSOC);	

				if($coach) {

					foreach ($coach as $key => $value) {
						$coach[$key]['coach_student_distance'] = getDistance($lat, $lng, $value['dc_coach_distance_y'], $value['dc_coach_distance_x'], $value['l_coach_id']);

						$sql = "SELECT `s_school_name` FROM `cs_school` WHERE `l_school_id` = ".$value['s_school_name_id'];
						$stmt = $db->query($sql);
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
						$coach[$key]['s_school_name'] = $row['s_school_name'] == null ? '' : $row['s_school_name'];

						// 获取
						if(file_exists(__DIR__.'/../sadmin/'.$value['s_coach_imgurl'])) {
							$coach[$key]['s_coach_imgurl'] = S_HTTP_HOST.$value['s_coach_imgurl'];
						} else {
							$coach[$key]['s_coach_imgurl'] = HTTP_HOST.$value['s_coach_imgurl'];
						}
					}
					$coach = multiArraySort($coach, 'coach_student_distance');
				} else {
					$coach = array();
				}
				$db = null;
				$data = array('code'=>200, 'data'=>$coach);
				exit(json_encode($data));

			} catch(PDOException $e) {
                $db = null;
				setapilog('get_coach_list:params[lat:'.$lat.',lng:'.$lng.',lisence_id:'.$lisence_id.',lesson_id:'.$lesson_id.',type:'.$type.',start_time:'.$start_time.',end_time:'.$end_time.'], error:'.$e->getMessage());	
				$data = array('code'=>1, 'data'=>'网络错误');
				exit(json_encode($data));
			}

		// 时间段
		} else if($type == 'time') {
			// $time_config_id = array_filter(explode(',', $time_config_id));
			$coach_ids = array_unique(getCurrentTimeConfig($start_time, $end_time));

			if(empty($coach_ids)) {
                $db = null;
				$data = array('code'=>200, 'data'=>array());
				exit(json_encode($data));
			}
			$sql = "SELECT s.`name` as `s_coach_car_name`, s.`car_type`, h.* FROM `cs_coach` as h LEFT JOIN `cs_cars` as s ON s.`id` = h.`s_coach_car_id`";
			$sql .= " WHERE h.`s_coach_lesson_id` LIKE '%".$lesson_id."%' AND h.`s_coach_lisence_id` LIKE '%".$lisence_id."%' AND h.`s_school_name_id` = ".$sid ." AND h.`order_receive_status` = 1 AND h.`l_coach_id` IN (".implode(',', $coach_ids).")";

			try {
				$db = getConnection();
				$stmt = $db->query($sql);
				$coach = $stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach ($coach as $key => $value) {
					$coach[$key]['coach_student_distance'] = getDistance($lat, $lng, $value['dc_coach_distance_y'], $value['dc_coach_distance_x'], $value['l_coach_id']);

					$sql = "SELECT `s_school_name` FROM `cs_school` WHERE `l_school_id` = ".$value['s_school_name_id'];
					$stmt = $db->query($sql);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$coach[$key]['s_school_name'] = $row['s_school_name'] == null ? '' : $row['s_school_name'];

					// 获取
					if(file_exists(__DIR__.'/../sadmin/'.$value['s_coach_imgurl'])) {
						$coach[$key]['s_coach_imgurl'] = S_HTTP_HOST.$value['s_coach_imgurl'];
					} else {
						$coach[$key]['s_coach_imgurl'] = HTTP_HOST.$value['s_coach_imgurl'];
					}
				}
				$db = null;
				$data = array('code'=>200, 'data'=>$coach);
				exit(json_encode($data));

			}catch(PDOException $e) {
                $db = null;
				setapilog('get_coach_list:params[lat:'.$lat.',lng:'.$lng.',lisence_id:'.$lisence_id.',lesson_id:'.$lesson_id.',type:'.$type.',start_time:'.$start_time.',end_time:'.$end_time.'], error:'.$e->getMessage());	
				$data = array('code'=>1, 'data'=>'网络错误');
				exit(json_encode($data));
			}

		}
	}

	// 计算教练当前距离
	function getDistance($lat1, $lng1, $lat2, $lng2, $coach_id)  {  
		// $coach_id = $request->params('coach_id'); //教练ID
		// $school_id = $request->params('school_id'); //驾校ID

		$earthRadius = 6367000;
	
		$lat1 = ($lat1 * pi()) / 180;  
		$lng1 = ($lng1 * pi()) / 180;    
		$lat2 = ($lat2 * pi()) / 180;  
		$lng2 = ($lng2 * pi()) / 180;    

		$calcLongitude = $lng2 - $lng1;
		$calcLatitude = $lat2 - $lat1;  
		$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
		$stepTwo = 2 * asin(min(1, sqrt($stepOne)));  
		$calculatedDistance = $earthRadius * $stepTwo;

		$res = saveDistance($calculatedDistance, $coach_id, 1);
		return $calculatedDistance;
	}

	// 获取当前筛选的教练列表
	// 将距离, 综合最优, 教练平均星级, 时间段数据保存到数据表
	function saveDistance($distance, $coach_id, $school_id) {
		try {
			$db = getConnection();

			// 获取平均星级
			$sql = "SELECT `coach_star` FROM `cs_coach_comment` WHERE `coach_id` = ".$coach_id;
			$stmt = $db->query($sql);
			$coach_star_obj = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$coach_star_count = 0;
			if($coach_star_obj) {
				$coach_star_total_num = 0;
				$coach_star_count = count($coach_star_obj);
				foreach ($coach_star_obj as $key => $value) {
					$coach_star_total_num += $value['coach_star'];
				}
				$coach_star_average = round($coach_star_total_num / $coach_star_count);
			} else {
				$coach_star_average = 3;
			}

			// 获取好评数
			$sql = "SELECT count(`id`) as num FROM `cs_coach_comment` WHERE `coach_id` = ".$coach_id." AND `coach_star` >= 3";
			$stmt = $db->query($sql);
			$good_coach_star_obj = $stmt->fetch(PDO::FETCH_ASSOC);
			if($good_coach_star_obj) {
				$good_star_total_num = $good_coach_star_obj['num'];
			} else {
				$good_star_total_num = 0;
			}

			// 获取均价
			$sql = "SELECT `price` FROM `cs_coach_time_config` WHERE `school_id` = ".$school_id;
			$stmt = $db->query($sql);
			$price_arr = $stmt->fetchAll();
			$price_arr_total_num = 0;
			if($price_arr) {
				$price_arr_count = count($price_arr);
				foreach ($price_arr as $key => $value) {
					$price_arr_total_num += $value['price'];
				}
				$price_arr_average = round($price_arr_total_num / $price_arr_count);
			} else {
				$price_arr_average = 0;
			}

			// 计算综合最优
			$integrated_excellent = round($distance*0.15 + $coach_star_average*0.15 + $price_arr_average*0.2)/(0.15+0.15+0.2);
			// return $integrated_excellent;
			
			$sql = "UPDATE `cs_coach`";
			// $sql .= " SET `coach_student_distance` = ".round($distance).","; 	// 距离
			$sql .= " SET `integrated_excellent` = ".$integrated_excellent.","; 	// 综合最优
			$sql .= " `i_coach_star` = ".$coach_star_average.","; 				// 平均评分
			$sql .= " `good_coach_star` = ".$good_star_total_num.","; 			// 好评总数
			$sql .= " `total_price` = ".$price_arr_average.","; 			// 总价
			$sql .= " `coach_star_count` = ".$coach_star_count; 				// 星级评价总数
			$sql .= " WHERE `l_coach_id` = ".$coach_id;
			$stmt = $db->query($sql);
			return $stmt;
		} catch (PDOException $e) {
            $db = null;
			setapilog('get_coach_list:params[lat:'.$lat.',lng:'.$lng.',lisence_id:'.$lisence_id.',lesson_id:'.$lesson_id.',type:'.$type.',start_time:'.$start_time.',end_time:'.$end_time.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			exit(json_encode($data));
		}
	}

	// 获得当前教练ID
	function getCurrentTimeConfig($start_time, $end_time) {

		// $time_config_id = implode(',', $time_config_id);
		// $start_time = strtotime($start_time);
		$start_time_year = intval(date('Y', $start_time));
		$start_time_month = intval(date('m', $start_time));
		$start_time_day = intval(date('d', $start_time));
		$start_time_hour = intval(date('H', $start_time));

		// $end_time = strtotime($end_time);
		$end_time_year = intval(date('Y', $end_time));
		$end_time_month = intval(date('m', $end_time));
		$end_time_day = intval(date('d', $end_time));
		$end_time_hour = intval(date('H', $end_time));

		$db = getConnection();
		// $sql = "SELECT `coach_id` FROM `cs_current_coach_time_configuration` WHERE `time_config_id` IN (".$time_config_id.")";

		// 获取时间配置的id
		$sql = "SELECT `id` FROM `cs_coach_time_config` WHERE `start_time` >= $start_time_hour AND `end_time` <= $end_time_hour";
		$stmt = $db->query($sql);
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if(!$row) {
			return array();
		}
		$list = array();
		foreach ($row as $key => $value) {
			$list[] = $value['id'];	
		}
		// print_r($list);
		// 获取教练ID
		$coach_id_arr = array();
		$appoint_coach_id_arr = array();
		// echo "<pre>";
		// print_r($list);
		foreach ($list as $key => $value) {
			$sql = "SELECT `coach_id` FROM `cs_current_coach_time_configuration` WHERE `year` = $end_time_year AND `month` = $end_time_month AND `day` = $end_time_day AND `time_config_id` LIKE '%".$value."%'";
			$stmt = $db->query($sql);
			$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($row as $k => $v) {
				$coach_id_arr[] = $v['coach_id'];
			}
		}

		// print_r($coach_id_arr);

		// // 获取当前时间段的所有教练ID
		// $current_coach_time = $stmt->fetchAll(PDO::FETCH_ASSOC);
		// foreach ($current_coach_time as $key => $value) {
		// 	$coach_id_arr[] = $value['coach_id'];
		// }

		// 获取当前时间段被预约的教练ID
		foreach ($list as $key => $value) {
			$sql = "SELECT `coach_id` FROM `cs_coach_appoint_time` WHERE `time_config_id` IN (".implode(',', $list).") AND `month` = $end_time_month AND `day` = $end_time_day";
			$stmt = $db->query($sql);
			$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($row as $k => $v) {
				$appoint_coach_id_arr[] = $v['coach_id'];
			}
		}
		// $appoint_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
		// foreach ($appoint_arr as $key => $value) {
		// 	$appoint_coach_id_arr[] = $value['coach_id'];
		// }

		$coach_id_diff_arr = array_diff(array_unique($coach_id_arr), array_unique($appoint_coach_id_arr));

		return $coach_id_diff_arr;

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
