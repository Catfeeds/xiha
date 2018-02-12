<?php 

	/**
	 * 登录接口(验证码登录)
	 * @param $phone int 手机号码
	 * @param $pass string 密码
	 * @param $type int 类型
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','_login');
	$app->run();

	// 登录验证
	function _login() {
		Global $app, $crypt;
		$request = $app->request();
		$type = $request->params('type');
		$user_phone_1 = $request->params('user_phone_1');
		$user_phone_2 = $request->params('user_phone_2');
		$validate_code = $request->params('code');
		$user_password = $request->params('pass');

		try {
			$db = getConnection();

			// 验证码登陆
			if($type == 1) {
				if(trim($user_phone_1) == '' && trim($validate_code) == '') {
					$data = array('code'=>-1, 'data'=>'请填写登陆信息');
					echo json_encode($data);
					exit();
				}

				// 首先检测是否存在这个账号
				$sql = "SELECT * FROM `cs_user` WHERE `s_phone` = '".$user_phone_1."' AND `i_user_type` = 0";
				$stmt = $db->query($sql);
				$userinfo = $stmt->fetch(PDO::FETCH_ASSOC);

				if(empty($userinfo)) {
					$data = array('code'=>-3,'data'=>'账号不存在');
					echo json_encode($data);
					exit();
				}
				// if(empty($userinfo)) {
				// 	// 新增账号
				// 	$sql = "INSERT INTO `cs_user`(`s_phone`, `i_status`, `i_user_type`) VALUES ('".$user_phone_1."', 0, 0)";
				// 	$stmt = $db->query($sql);
				// 	if(!$stmt) {
				// 		$data = array('code'=>-5, 'data'=>'新建用户错误');
				// 		echo json_encode($data);
				// 		exit();
				// 	}
				// 	$uid = $db->lastInsertId();
				// 	$sql = "SELECT * FROM `cs_user` WHERE `l_user_id` = $uid AND `i_user_type` = 0";
				// 	$stmt = $db->query($sql);
				// 	$userinfo = $stmt->fetch(PDO::FETCH_ASSOC);
				// }

				$sql = "SELECT * FROM `cs_verification_code` WHERE `s_phone` = '".$user_phone_1."' AND `s_code` = '".$validate_code."'";
				$stmt = $db->query($sql);
				$user_code_info = $stmt->fetch(PDO::FETCH_ASSOC);
				
				if($user_code_info) {

					// 检测验证码是否过期
					if(time() - $user_code_info['addtime'] > 24*3600) {//**********
						$data = array('code'=>-4, 'data'=>'验证码过期,请重新获取');
						echo json_encode($data);
						exit();
					}
					
					// 更新登录次数信息
					$sql = "SELECT `s_username`,`is_first` FROM `cs_user` WHERE `s_phone` = ".$user_phone_1;
					$stmt = $db->query($sql);
					$row = $stmt->fetchObject();
					if($row->s_username != "" && $row->is_first == 0) {
						$sql = "UPDATE `cs_user` SET `is_first` = 1 WHERE `s_phone` = ".$user_phone_1;
						$res = $db->query($sql);
					}

					// $loginauth = $userinfo['l_user_id'].'\t'.$userinfo['s_username'].'\t'.$userinfo['s_real_name'].'\t'.$userinfo['s_phone'];
					// $_SESSION['loginauth'] = $crypt->encrypt($loginauth);
					$arr = array('s_phone'=>$userinfo['s_phone'], 'l_user_id'=>$userinfo['l_user_id'], 's_real_name'=>$userinfo['s_real_name']);
					$data = array('code'=>200, 'data'=>$arr);

				} else {
					$data = array('code'=>-2, 'data'=>'登陆失败');
				}
				$db = null;
				echo json_encode($data);
				exit();

			} else if($type == 2) {

				if(trim($user_phone_2) == '' && trim($user_password) == '') {
					$data = array('code'=>-6, 'data'=>'请填写登陆信息');
					echo json_encode($data);
					exit();
				}

				$sql = "SELECT `l_user_id`, `s_username`, `s_real_name`, `s_phone` FROM `cs_user` WHERE `i_status` = 0 AND `i_user_type` = 0 AND `s_phone` = '".$user_phone_2."' AND `s_password` = '".$user_password."'";
				$stmt = $db->query($sql);
				$userinfo = $stmt->fetch(PDO::FETCH_ASSOC);
				if($userinfo) {
					$data = array('code'=>200, 'data'=>$userinfo);
					//$loginauth = $userinfo['l_user_id'].'\t'.$userinfo['s_username'].'\t'.$userinfo['s_real_name'].'\t'.$userinfo['s_phone'];
					//$_SESSION['loginauth'] = $crypt->encrypt($loginauth);
				} else {
					$data = array('code'=>2, 'data'=>'登录失败');
				}
				$db = null;
				echo json_encode($data);
				exit();
			}

		} catch (PDOException $e) {
			setapilog('web_login:params[type:'.$type.',user_phone_1:'.$user_phone_1.',user_phone_2:'.$user_phone_2.',code:'.$validate_code.',pass:'.$user_password.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}
	}

?>
