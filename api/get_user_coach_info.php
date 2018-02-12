<?php  
	/**
	 * 获取学员端教练评价信息
	 * @param $id int 教练ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->get('/id/:id','getUserCoachInfo');
	$app->run();

	// 获取学员端教练学车评价信息
	function getUserCoachInfo($id) {
		Global $app, $crypt;

		try {
			$db = getConnection();
			$sql = "SELECT `s_coach_name`, `s_coach_imgurl`, `s_coach_imgurl`, `s_coach_phone`, `s_teach_age`, `s_coach_sex`, `s_coach_content`, `s_coach_car_id`, `s_coach_address` FROM `cs_coach` WHERE `l_coach_id` = $id";
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
			$sql = "SELECT count(*) as num FROM `cs_study_orders` WHERE `l_coach_id` = $id AND `i_status` = 2";
			$stmt = $db->query($sql);
			$student_pass_count = $stmt->fetch(PDO::FETCH_ASSOC);

			// 获得通过率
			$sql = "SELECT count(*) as num FROM `cs_study_orders` WHERE `l_coach_id` = $id";
			$stmt = $db->query($sql);
			$student_all_count = $stmt->fetch(PDO::FETCH_ASSOC);

			if($student_all_count['num'] && $student_pass_count['num']) {
				$passing_rate = round(($student_pass_count['num'] / $student_all_count['num']) * 100, 1).'%';
			} else {
				$passing_rate = '50%';
			}
			
			$coachinfo['pass_count'] = $student_pass_count['num']; //已带学员数
			$coachinfo['rate'] = $passing_rate; //通过率
			// // 获取车辆
			// if($coachinfo['s_coach_car_id']) {
				
			// 	$sql = "SELECT * FROM `cs_cars` WHERE `id` IN (".$coachinfo['s_coach_car_id'].")";
			// 	$stmt = $db->query($sql);
			// 	$res_car = $stmt->fetchAll(PDO::FETCH_ASSOC);
			// 	foreach ($res_car as $key => $value) {
			// 		$res_car[$key]['imgurl'] = json_decode($value['imgurl'],true);
			// 	}
			// }

			// $coachinfo['car_info'] = $res_car;
			
			// 获取当前学员信息
			$sql = "SELECT * FROM `cs_study_orders` WHERE `l_coach_id` = ".$id;
			$stmt = $db->query($sql);
			$user_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$list = array();
			foreach ($user_info as $key => $value) {
				$user_id[] = $value['l_user_id'];
				$sql = "SELECT * FROM `cs_user` as u LEFT JOIN `cs_users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`l_user_id` = ".$value['l_user_id'];
				$stmt = $db->query($sql);
				$userinfo = $stmt->fetch(PDO::FETCH_ASSOC);

				// 获取当前评价
				$sql = "SELECT * FROM `cs_coach_comment` WHERE `user_id` = ".$value['l_user_id']." AND `coach_id` = $id AND `order_no` = ".$value['s_order_no'];

				$stmt = $db->query($sql);
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if($row) {
					$list[$key]['s_username'] = $userinfo['s_username'];
					$list[$key]['s_real_name'] = $userinfo['s_real_name'];
					$list[$key]['user_photo'] = $userinfo['user_photo'] == null ? '' : $userinfo['user_photo'];
					$list[$key]['s_phone'] = $userinfo['s_phone'];
					$list[$key]['coach_star'] = $row['coach_star'] == null ? '3' : $row['coach_star'];
					$list[$key]['school_star'] = $row['school_star'] == null ? '3' : $row['school_star'];
					$list[$key]['coach_content'] = $row['coach_content'] == null ? '暂无评价' : $row['coach_content'];
					$list[$key]['school_content'] = $row['school_content'] == null ? '暂无评价' : $row['school_content'];
					$list[$key]['addtime'] = $row['addtime'] == null ? date('Y-m-d H:i', time()) : date('Y-m-d H:i', $row['addtime']);
				}
			}

			$coachinfo['learn_car_arr'] = array_values($list);
			$data = array('code'=>200, 'data'=>$coachinfo);
			echo json_encode($data);
			exit();

		} catch(PDOException $e) {
			setapilog('get_user_coach_info:params[id:'.$id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}
	}
?>