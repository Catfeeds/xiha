<?php  
	/**
	 * 获得教练学员列表
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
	$app->get('/id/:id','getcoachstudentlist');
	$app->run();

	// 获取教练学员列表
	function getcoachstudentlist($id) {

		Global $app, $crypt;
		$sql = "SELECT `l_user_id` FROM `cs_study_orders` WHERE `l_coach_id` = $id";

		try {
			$db = getConnection();
			$stmt = $db->query($sql);
			$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(!$users) {
				$data = array('code'=>-1, 'data'=>'暂无列表');
				echo json_encode($data);
				exit();
			}

			foreach ($users as $key => $value) {
				$user_id[] = $value['l_user_id'];
			}
			$user_id = array_unique($user_id);

			// 获取用户信息
			$sql = "SELECT u.`s_username`, u.`s_real_name`, u.`l_user_id`, i.`sex`, i.`sex`, i.`age`,i.`user_photo`, u.`s_phone`  FROM `cs_user` as u LEFT JOIN `cs_users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`l_user_id` IN (".implode(',', $user_id).")";
			$stmt = $db->query($sql);
			$userlist = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($userlist) {
				foreach ($userlist as $key => $value) {
					$userlist[$key]['s_username'] = $value['s_username'] == null ? '' : $value['s_username'];
					$userlist[$key]['s_real_name'] = $value['s_real_name'] == null ? '' : $value['s_real_name'];
					$userlist[$key]['l_user_id'] = $value['l_user_id'] == null ? '' : $value['l_user_id'];
					$userlist[$key]['user_photo'] = $value['user_photo'] == null ? '' : $value['user_photo'];
					$userlist[$key]['s_phone'] = $value['s_phone'] == null ? '' : $value['s_phone'];
					$userlist[$key]['sex'] = $value['sex'] == null ? '' : $value['sex'];
					$userlist[$key]['age'] = $value['age'] == null ? '' : $value['age'];
				}
			}
			$data = array('code'=>200, 'data'=>$userlist);
			echo json_encode($data);

		}catch(PDOException $e) {
			setapilog('get_coach_students:params[id:'.$id.'], error:'.$e->getMessage());		
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}
	}
?>