<?php  
	/**
	 * 获取地图模式下点击驾校显示基本信息
	 * @param $id int 驾校ID
	 * @param $lng 学员经度 117.144356
	 * @param $lat 学员维度 31.839411
	 * @return json
	 * @author cx
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getSchoolCardInfo');
	$app->run();

	function getSchoolCardInfo() {
		Global $app, $crypt;
		$request = $app->request();
		$id = $request->params('id');
		$lng = $request->params('lng');
		$lat = $request->params('lat');

		$lng = !empty($lng) ? $lng : '117.144356';
		$lat = !empty($lat) ? $lat : '31.839411';

		if(empty($id) || empty($lng) || empty($lat)) {
			$data = array('code'=>-2, 'data'=>"参数错误");
			echo json_encode($data);
			exit();
		}
		try {
			$db = getConnection();
			$sql = "SELECT `l_school_id`, `s_school_name`, `s_thumb`, `s_location_x`, `s_location_y` FROM `cs_school` WHERE `l_school_id` = '{$id}'";
			$stmt = $db->query($sql);
			$school_info = $stmt->fetch(PDO::FETCH_ASSOC);
			if($school_info) {

				if($school_info['s_thumb']) {
					if(file_exists(__DIR__.'/../sadmin/'.$school_info['s_thumb'])) {
						$school_info['s_thumb'] = S_HTTP_HOST.$school_info['s_thumb'];
					} else {
						$school_info['s_thumb'] = HTTP_HOST.$school_info['s_thumb'];
					}
				} else {
					$school_info['s_thumb'] ='';
				}

				// 获取报名驾校的订单估算平均星级
				$sql = "SELECT count(`user_id`) as num FROM `cs_coach_comment` WHERE `school_id` = '{$id}' AND `type` = 2";
				$stmt = $db->query($sql);
				$total_comment_num = $stmt->fetch(PDO::FETCH_ASSOC);

				$sql = "SELECT SUM(`school_star`) as num FROM `cs_coach_comment` WHERE `school_id` = '{$id}' AND `type` = 2"; 
				$stmt = $db->query($sql);
				$total_school_star = $stmt->fetch(PDO::FETCH_ASSOC);

				if($total_comment_num['num'] != 0) {
					$school_info['star'] = round($total_school_star['num'] / $total_comment_num['num']);
				} else {
					$school_info['star'] = 0;
				}
					
				// 获取驾校的教练数
				$sql = "SELECT count(`l_coach_id`) as num FROM `cs_coach` WHERE `s_school_name_id` = '{$id}'";
				$stmt = $db->query($sql);
				$coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
				$school_info['coach_num'] = $coach_info['num'];
				if(!empty($school_info['s_location_y']) || !empty($school_info['s_location_x'])) {
					$school_info['location'] = floor(getDistance($lat, $lng, $school_info['s_location_y'], $school_info['s_location_x'])/1000);
				} else {
					$school_info['location'] = '未知';
				}

				$db = null;
				$data = array('code'=>200, 'data'=>$school_info);
				echo json_encode($data);
				exit();

			} else {
				$db = null;
				$data = array('code'=>-1, 'data'=>'不存在此驾校');
				echo json_encode($data);
				exit();
			}

		}catch(PDOException $e) {
			setapilog('get_school_card_info:params[id:'.$id.', lng:'.$lng.', lat:'.$lat.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
		}
	}

	// 计算教练当前距离
	function getDistance($lat1, $lng1, $lat2, $lng2)  {
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
		return $calculatedDistance;
	}
?>