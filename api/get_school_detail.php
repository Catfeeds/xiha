<?php  
	/**
	 * 获取驾校详情
	 * @param $id 驾校ID 1
	 * @param $lng 学员经度 117.144356
	 * @param $lat 学员维度 31.839411
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
	$app->post('/','getSchoolDetail');
	$app->run();

	// 获取教练学员信息
	function getSchoolDetail() {
		Global $app, $crypt;
		$request = $app->request();
		$id = $request->params('id');
		$lng = $request->params('lng');
		$lat = $request->params('lat');
		$user_id = $request->params('uid');

		$lng = !empty($lng) ? $lng : '117.144356';
		$lat = !empty($lat) ? $lat : '31.839411';
		$user_id = !empty($user_id) ? $user_id : 0;
		try {
			$detail = array();
			$s_imgurl = array();
			$min_distance = 0;
			$db = getConnection();
			// 获取驾校基本信息
			$sql = "SELECT * FROM `cs_school` WHERE `l_school_id` = $id";
			$stmt = $db->query($sql);
			$school_detail = $stmt->fetch(PDO::FETCH_ASSOC);

			// 获取驾校图片
			if($school_detail['s_imgurl']) {
				$s_imgurl = json_decode($school_detail['s_imgurl'], true);
			}
			$img_arr = array();
			foreach ($s_imgurl as $key => $value) {

				// if($value) {
				// 	if(file_exists(__DIR__.'/../sadmin/'.$value)) {
				// 		$img_arr[] = S_HTTP_HOST.$value;
				// 	} else {
				// 		$img_arr[] = HTTP_HOST.$value;
				// 	}
				// } else {
				// 	$img_arr[] = 'images/school_thumb.jpg';
				// }
				$img_arr[] = S_HTTP_HOST.$value;
			}
			$detail['l_school_id'] = $school_detail['l_school_id'];
			$detail['s_school_name'] = $school_detail['s_school_name'];
			$detail['s_imgurl'] = $img_arr;
			$detail['s_shifts_intro'] = $school_detail['shifts_intro'];
			$detail['s_school_intro'] = $school_detail['s_shuoming'];

			// 最近的地方
			$sql = "SELECT * FROM `cs_school_train_location` WHERE `tl_school_id` = $id";
			$stmt = $db->query($sql);
			$train_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$list = array();
			$distance = array();
			// if(!$train_list) {
			// 	$data = array('code'=>-1, 'data'=>'未设置培训点');
			// 	echo json_encode($data);
			// 	exit();
			// }

			if($train_list) {
				foreach ($train_list as $key => $value) {
					// 获取所有距离
					$list[$key]['tl_train_address'] = $value['tl_train_address'];
					$list[$key]['tl_phone'] 		= $value['tl_phone'];
					$list[$key]['tl_location_x'] 	= $value['tl_location_x'];
					$list[$key]['tl_location_y'] 	= $value['tl_location_y'];
					$list[$key]['distance'] = floor(getDistance($lat, $lng, $value['tl_location_y'], $value['tl_location_x'])/1000);
					$distance[] = floor(getDistance($lat, $lng, $value['tl_location_y'], $value['tl_location_x'])/1000);
				}
			} else {
				$distance[] = floor(getDistance($lat, $lng, $school_detail['s_location_y'], $school_detail['s_location_x'])/1000);
			}
				
			$min_distance = min($distance);
			if($list) {
				foreach ($list as $k => $v) {
					if($min_distance == $v['distance']) {
						$detail['tl_train_address'] = $v['tl_train_address'];
						$detail['tl_phone'] = $v['tl_phone'];
						$detail['tl_location_x'] = $v['tl_location_x'];
						$detail['tl_location_y'] = $v['tl_location_y'];
					} else {
						continue;
					}
				}
			} else {
				$detail['tl_train_address'] = $school_detail['s_address'];
				$detail['tl_phone'] = $school_detail['s_frdb_tel'];
				$detail['tl_location_x'] = $school_detail['s_location_x'];
				$detail['tl_location_y'] = $school_detail['s_location_y'];
			}
				
			$detail['min_distance'] = $min_distance;

			// 获取评价
			$sql = "SELECT `l_coach_id` FROM `cs_coach` WHERE `s_school_name_id` = $id";
			$stmt = $db->query($sql);
			$coach_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($coach_list) {
				foreach ($coach_list as $key => $value) {
					$coach_ids[] = $value['l_coach_id'];
				}				

				$sql = "SELECT `school_star` FROM `cs_coach_comment` WHERE `coach_id` IN (".implode(',', $coach_ids).") OR `school_id` = $id";
				$stmt = $db->query($sql);
				$comment_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$school_star_num = 0;
				if($comment_info) {
					foreach ($comment_info as $key => $value) {
						$school_star_num += intval($value['school_star']);
					}
					// 总评价数
					$comment_num = count($comment_info);
					// 平均星级
					$average_star_num = intval($school_star_num / $comment_num);
				} else {
					$comment_num = 0;
					$average_star_num = 0;	
				}
					
			} else {
				$comment_num = 0;
				$average_star_num = 0;
			}

			$detail['total_comment_num'] = $comment_num;
			$detail['average_star_num'] = $average_star_num;

			// 获取班制信息
			$sql = "SELECT * FROM `cs_school_shifts` WHERE `sh_school_id` = $id ORDER BY `sh_type` ASC";
			$stmt = $db->query($sql);
			$shifts_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// foreach ($shifts_list as $key => $value) {
				
			// }
			$detail['shifts_list'] = $shifts_list;
			// 获取登陆状态
			// $detail['loginauth'] = isset($_SESSION['loginauth']) ? $_SESSION['loginauth'] : '';
			$sql = "SELECT * FROM `cs_user` WHERE `l_user_id` = $user_id";
			$stmt = $db->query($sql);
			$userinfo = $stmt->fetch(PDO::FETCH_ASSOC);
			if($userinfo) {
				$loginauth = $userinfo['l_user_id'].'\t'.$userinfo['s_username'].'\t'.$userinfo['s_real_name'].'\t'.$userinfo['s_phone'];
				$detail['loginauth'] = $crypt->encrypt($loginauth);
			} else {
				$detail['loginauth'] = '';

			}

			$detail['lng'] = $lng;
			$detail['lat'] = $lat;

			$db = null;
			$data = array('code'=>200, 'data'=>$detail);
			echo json_encode($data);
				
		} catch(PDOException $e) {
			setapilog('get_school_detail:params[id:'.$id.',lng:'.$lng.',lat:'.$lat.',uid:'.$user_id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
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
