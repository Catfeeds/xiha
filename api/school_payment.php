<?php  
	/**
	 * 驾校下单 (1：报名成功，2：申请退款中 3：退款成功 4：报名取消)
	 * @param $uid 学员ID 
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	require 'include/token.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','opayment');
	$app->run();

	function opayment() {
		Global $app, $crypt;
		$request = $app->request();
		$token = new Form_token_Core();

		// if(!isset($_SESSION['loginauth'])) {
		// 	$data = array('code'=>-2, 'data'=>'请先登录');
		// 	echo json_encode($data);
		// 	exit();
		// }
		// $loginauth = $crypt->decrypt($_SESSION['loginauth']);
		// $loginauth_arr = explode('\t', $loginauth);
		// $user_id = $loginauth_arr[0];

		$id = $request->params('id');
		$sid = $request->params('sid');
		$ptype = $request->params('ptype');
		$user_name = $request->params('user_name');
		$user_phone = $request->params('user_phone');
		$identity_id = $request->params('user_identify_id');
		$licence_type = $request->params('licence');
		$order_type = $request->params('order_type');
		$uid = $request->params('uid');
		$access_token = $request->params('access_token');

		if($uid == '') {
			$data = array('code'=>-2, 'data'=>'请先登录');
			echo json_encode($data);
			exit();
		}

		if(trim($id) == '' || trim($sid) == '' || trim($ptype) == '' || trim($user_phone) == '' || trim($user_name) == '' || trim($identity_id) == '' || trim($licence_type) == '' || trim($order_type) == '' || trim($uid) == '' || trim($access_token) == '') {
			$data = array('code'=>-5, 'data'=>'请完善信息');
			echo json_encode($data);
			exit();
		}

		// 验证身份证
		if(!identify($identity_id)) {
			$data = array('code'=>-6, 'data'=>'身份证验证失败');
			echo json_encode($data);
			exit();
		}

		if ($ptype == 2) {
			$data = array('code'=>-6, 'data'=>'线下支付已关闭，请选择线上支付');
			echo json_encode($data);
			exit();
		} 
		// if($access_token != $_SESSION['access_token']) {
		// 	$data= array('code'=>-4, 'data'=>'请勿重复提交');
		// 	echo json_encode($data);
		// 	exit();
		// }
		$_SESSION['access_token'] = $token->grante_key();

		$so_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
		try {
			$db = getConnection();
			// 通过班制ID获取班制信息
			$sql = "SELECT `sh_money`, `sh_original_money` FROM `cs_school_shifts` WHERE `id` = ".$id;
			$stmt = $db->query($sql);
			$shifts_info = $stmt->fetch(PDO::FETCH_ASSOC);

			if(empty($shifts_info)) {
				$data = array('code'=>-1, 'data'=>'参数错误');
				echo json_encode($data);
				exit();
			}

			// 获取是否已报名
			// $sql = "SELECT * FROM `cs_school_orders` WHERE `so_user_identity_id` = '".$identity_id."' AND `so_order_status` != 101 AND ((`so_pay_type` = 1 AND `so_order_status` = 1) OR (`so_pay_type` = 2 AND `so_order_status` = 3)  OR (`so_pay_type` = 2 AND `so_order_status` = 1) OR (`so_pay_type` = 3 AND `so_order_status` = 1))";
			$sql = "SELECT * FROM `cs_school_orders` WHERE (`so_user_identity_id` = '{$identity_id}' OR `so_user_id` = '{$uid}')";
			$sql .= " AND `so_order_status` != 101 AND (`so_pay_type` IN (1, 3, 4) AND `so_order_status` IN (1, 4) ) ";
			$sql .= " OR (`so_pay_type` = 2 AND `so_order_status` IN (1, 3) )";
			
			$stmt = $db->query($sql);
			$order_info = $stmt->fetch(PDO::FETCH_ASSOC);
			if($order_info) {
				$data = array('code'=>-7, 'data'=>'您已下单，请勿重新提交');
				echo json_encode($data);
				exit();
			}

			// 更新用户信息
			$sql = "SELECT `s_real_name` FROM `cs_user` WHERE `l_user_id` = $uid";
			$stmt = $db->query($sql);
			$_user_info = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($_user_info)) {
				$sql = "UPDATE `cs_user` SET `s_real_name` = '".$user_name."' WHERE `l_user_id` = $uid";
				$stmt = $db->query($sql);
				if(!$stmt) {
					$data = array('code'=>-10, 'data'=>'当前报名人数太多，请从新报名');
					echo json_encode($data);
					exit();	
				}
			}
			$sql = "SELECT * FROM `cs_users_info` WHERE `user_id` = $uid";
			$stmt = $db->query($sql);
			$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
			if($user_info) {
				$sql = "UPDATE `cs_users_info` SET `identity_id` = '".$identity_id."', `school_id` = '".$sid."', `sex` = 1 WHERE `user_id` = $uid";
				$stmt = $db->query($sql);
				if(!$stmt) {
					$data = array('code'=>-8, 'data'=>'当前报名人数太多，请从新报名');
					echo json_encode($data);
					exit();
				}
			} else {
				$sql = "INSERT INTO `cs_users_info` (";
				$sql .= " `x`,";
				$sql .= " `y`,";
				$sql .= " `user_id`,";
				$sql .= " `sex`,";
				$sql .= " `age`,";
				$sql .= " `identity_id`,";
				$sql .= " `address`,";
				$sql .= " `user_photo`,";
				$sql .= " `license_num`,";
				$sql .= " `school_id`,";
				$sql .= " `lesson_name`,";
				$sql .= " `province_id`,";
				$sql .= " `city_id`,";
				$sql .= " `area_id`,";
				$sql .= " `photo_id`,";
				$sql .= " `learncar_status`";
				$sql .= ") VALUES (";
				$sql .= "0,0,";
				$sql .= "'".$uid."',";
				$sql .= "1,18,";
				$sql .= " '".$identity_id."',";
				$sql .= " '','',";
				$sql .= " 0,'".$sid."','',0,0,0,";
				$sql .= " 1,'科目二学习中')";
				$stmt = $db->query($sql);
				if(!$stmt) {
					$data = array('code'=>-9, 'data'=>'当前报名人数太多，请从新报名');
					echo json_encode($data);
					exit();
				}
			}
				
			$sql = "INSERT INTO `cs_school_orders` (";
			$sql .= "`so_school_id`,";
			$sql .= " `so_final_price`,";
			$sql .= " `so_original_price`,";
			$sql .= " `so_shifts_id`,";
			$sql .= " `so_pay_type`,";
			$sql .= " `so_order_status`,";
			$sql .= " `so_comment_status`,";
			$sql .= " `so_order_no`,";
			$sql .= " `so_user_id`,";
			$sql .= " `so_user_identity_id`,";
			$sql .= " `so_licence`,";
			$sql .= " `so_username`,";
			$sql .= " `so_phone`,";
			$sql .= " `addtime`) ";
			$sql .= "VALUES ('".$sid."',";
			$sql .= " '".$shifts_info['sh_money']."',";
			$sql .= " '".$shifts_info['sh_original_money']."',";
			$sql .= " '".$id."',";
			$sql .= " '".$ptype."',";
			$sql .= " 1, 1,";
			$sql .= " '".$so_order_no."',";
			$sql .= " '".$uid."',";
			$sql .= " '".$identity_id."',";
			$sql .= " '".$licence_type."',";
			$sql .= " '".$user_name."',";
			$sql .= " '".$user_phone."',";
			$sql .= " '".time()."')";
			$stmt = $db->query($sql);
			if($stmt) {
				$data = array('code'=>200, 'data'=>'报名成功','info'=>array('no'=>$so_order_no, 'ordertime'=>date('Y-m-d H:i',time()), 'shifts_id'=>$id));
			} else {
				$data = array('code'=>-3, 'data'=>'报名失败');
			}
			echo json_encode($data);
			exit();

		} catch(PDOException $e) {
			setapilog('school_payment:params[id:'.$id.',sid:'.$sid.',ptype:'.$ptype.',user_name:'.$user_name.',user_phone:'.$user_phone.',user_identify_id:'.$identity_id.',licence:'.$licence_type.',order_type:'.$order_type.',uid:'.$uid.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>$e->getMessage());
			echo json_encode($data);
			exit;
		}
	}

	// 验证是身份证
	function identify($id) {
	    $ch = curl_init();
	    $url = 'http://apis.baidu.com/apistore/idservice/id?id='.$id;
	    $header = array(
	        'apikey:3f476886841e800307821a2edb3b50c6',
	    );
	    // 添加apikey到header
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    // 执行HTTP请求
	    curl_setopt($ch , CURLOPT_URL, $url);
	    $res = curl_exec($ch);
	    $result = json_decode($res, true);
	    if($result['errNum'] == 0) {
	    	return true;
	    } else {
	    	return false;
	    }
	}
?>