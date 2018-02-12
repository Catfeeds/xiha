<?php  
	/**
	 * 根据经纬度计算位置接口
	 * @param $lat1 char 经度1
	 * @param $lng1 char 纬度1
	 * @param $lat2 char 经度2
	 * @param $lng2 char 纬度2
	 * @param $coach_id int 教练ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getDistance');
	$app->run();

	// 117.201239,31.856717  117.204366,31.8546
	// 计算位置
	function getDistance()  {  
		Global $app, $crypt;
		$request = $app->request();
		$lat1 = $request->params('lat1'); //经度1坐标
		$lng1 = $request->params('lng1'); //纬度1坐标
		$lat2 = $request->params('lat2'); //经度2坐标
		$lng2 = $request->params('lng2'); //纬度2坐标
		$coach_id = $request->params('coach_id'); //教练ID
		$school_id = $request->params('school_id'); //驾校ID

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
		
		$e = saveDistance($calculatedDistance, $coach_id, $school_id);
		// echo $e;
		$data = array('code'=>200, 'data'=>round($calculatedDistance)/1000);
		echo json_encode($data);

	}

	// 将距离, 综合最优, 教练平均星级, 时间段数据保存到数据表
	function saveDistance($distance, $coach_id, $school_id) {
		try {
			$db = getConnection();

			// 获取平均星级
			$sql = "SELECT `coach_star` FROM `cs_coach_comment` WHERE `coach_id` = ".$coach_id;
			$stmt = $db->query($sql);
			$coach_star_obj = $stmt->fetchAll();

			$coach_star_count = 0;
			if($coach_star_obj) {
				$coach_star_total_num = 0;
				$coach_star_count = count($coach_star_obj);
				foreach ($coach_star_obj as $key => $value) {
					$coach_star_total_num += $value['coach_star'];
				}
				$coach_star_average = round($coach_star_total_num / $coach_star_count);
			} else {
				$coach_star_average = 0;
			}

			// 获取好评数
			$sql = "SELECT count(*) FROM `cs_coach_comment` WHERE `coach_id` = ".$coach_id." AND `coach_star` >= 3";
			$stmt = $db->query($sql);
			$good_coach_star_obj = $stmt->fetch();
			if($good_coach_star_obj) {
				$good_star_total_num = $good_coach_star_obj[0];
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
			$sql .= " SET `coach_student_distance` = ".round($distance).","; 	// 距离
			$sql .= " `integrated_excellent` = ".$integrated_excellent.","; 	// 综合最优
			$sql .= " `i_coach_star` = ".$coach_star_average.","; 				// 平均评分
			$sql .= " `good_coach_star` = ".$good_star_total_num.","; 			// 好评总数
			$sql .= " `total_price` = ".$price_arr_total_num.","; 			// 总价
			$sql .= " `coach_star_count` = ".$coach_star_count; 				// 星级评价总数
			$sql .= " WHERE `l_coach_id` = ".$coach_id;
			$stmt = $db->query($sql);
			return $stmt;
		} catch (PDOException $e) {
			$data = -1;
			setapilog('calculate_location:params[lat1:'.$lat1.',lng1:'.$lng1.',lat2:'.$lat2.',lng2:'.$lng2.',coach_id:'.$coach_id.',school_id:'.$school_id.'], error:'.$e->getMessage());
			return json_encode($data);
		}
	}



?>