<?php  
	/**
	 * 获取驾校订单详情
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
	$app->post('/','getApply');
	$app->run();

	// 获取教练学员信息
	function getApply() {
		Global $app, $crypt;
		$request = $app->request();
		$sid = $request->params('sid'); // 学校ID
		$id = $request->params('id'); // 班制ID
		$user_id = $request->params('uid'); // 学员ID
		// if(!isset($_SESSION['loginauth'])) {
		// 	$data = array('code'=>-2, 'data'=>'请先登录');
		// 	echo json_encode($data);
		// 	exit();
		// }

		// $loginauth = $crypt->decrypt($_SESSION['loginauth']);
		// $loginauth_arr = explode('\t', $loginauth);
		if(empty($sid) || empty($id) || empty($user_id)) {
			$data = array('code'=>-2, 'data'=>'参数错误');
			echo json_encode($data);
			exit();
		}

		try {
			$db = getConnection();
			$apply = array();
			$sql = "SELECT `s_thumb`, `s_school_name` FROM `cs_school` WHERE `l_school_id` = $sid";
			$stmt = $db->query($sql);
			$school_info = $stmt->fetch(PDO::FETCH_ASSOC);

			if(empty($school_info)) {
				$data = array('code'=>-1, 'data'=>'操作错误');
				echo json_encode($data);
				exit();
			}

			$apply['l_school_id'] = $sid;
			$apply['shifts_id'] = $id;

			if($school_info['s_thumb']) {
				if(file_exists(__DIR__.'/../sadmin/'.$school_info['s_thumb'])) {
					$apply['s_thumb'] = S_HTTP_HOST.$school_info['s_thumb'];
				} else {
					$apply['s_thumb'] = HTTP_HOST.$school_info['s_thumb'];
				}
			} else {
				$apply['s_thumb'] = 'images/school_thumb.jpg';
			}
			$apply['s_school_name'] = $school_info['s_school_name'] == '' ? '暂无设置' : $school_info['s_school_name'];
		
			// 获取用户信息
			// $apply['s_username'] = $loginauth_arr[2];
			// $apply['s_phone'] = $loginauth_arr[3];
			$sql = "SELECT u.`s_real_name`, u.`s_username`, u.`s_phone`, i.`identity_id` FROM `cs_user` as u LEFT JOIN `cs_users_info` as i ON u.`l_user_id` = i.`user_id` WHERE u.`l_user_id` = $user_id";
			$stmt = $db->query($sql);
			$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
			if($user_info) {
				$apply['s_username'] = $user_info['s_real_name'] == null ? '' : $user_info['s_real_name'];
				$apply['s_phone'] = $user_info['s_phone'] == null ? '' : $user_info['s_phone'];
				$apply['identity_id'] = $user_info['identity_id'] == null ? '' : $user_info['identity_id'];
			} else {
				$apply['s_username'] = '';
				$apply['s_phone'] = '';
				$apply['identity_id'] = '';
			}

			// 获取订单类型
			$sql = "SELECT `sh_title` FROM `cs_school_shifts` WHERE `id` = $id";
			$stmt = $db->query($sql);
			$shifts_info = $stmt->fetch(PDO::FETCH_ASSOC);
			if($shifts_info) {
				$apply['shifts_info'] = $shifts_info['sh_title'];
			} else {
				$apply['shifts_info'] = '';
			}

			// 获取牌照
			$lisence_config = array(
				array(
					'car_type'=>1,
					'lisence_id' => 1,
					'lisence_name' =>'C1',
				),
				array(
					'car_type'=>1,
					'lisence_id' => 2,
					'lisence_name' =>'C2',
				),
				array(
					'car_type'=>1,
					'lisence_id' => 3,
					'lisence_name' =>'C3',
				),
				array(
					'car_type'=>2,
					'lisence_id' => 4,
					'lisence_name' =>'A1',
				),
				array(
					'car_type'=>2,
					'lisence_id' => 6,
					'lisence_name' =>'B1',
				),
				array(
					'car_type'=>3,
					'lisence_id' => 5,
					'lisence_name' =>'A2',
				),
				array(
					'car_type'=>3,
					'lisence_id' => 7,
					'lisence_name' =>'B2',
				),
				array(
					'car_type'=>4,
					'lisence_id' => 8,
					'lisence_name' =>'D',
				),
				array(
					'car_type'=>4,
					'lisence_id' => 9,
					'lisence_name' =>'E',
				),
				array(
					'car_type'=>4,
					'lisence_id' => 10,
					'lisence_name' =>'F',
				)
			);

			$apply['lisence_list'] = $lisence_config;
			$apply['access_token'] = md5(((float) date("YmdHis") + rand(100,999)).rand(1000,9999));
			$_SESSION['access_token'] = $apply['access_token'];
			$db = null;
			$data = array('code'=>200, 'data'=>$apply);
			echo json_encode($data);

		} catch(PDOException $e) {
			setapilog('apply:params[id:'.$id.',uid:'.$user_id.',sid:'.$sid.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}

	}

?>