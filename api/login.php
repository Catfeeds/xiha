<?php 

	/**
	 * 登录接口(正常密码登录)
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
 	$app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
	$app->post('/','login');

	// 登录验证
	function login() {
		Global $app, $crypt;
		$request = $app->request();
		$phone = $request->params('phone'); //手机号
		$pass = md5($request->params('pass')); //密码
		$type = $request->params('type'); //类型 0:学员 1：教练

		$type = isset($type) ? $type : 0;
		if(empty($phone) || empty($pass)) {
			$data = array('code'=>-1, 'data'=>'参数错误');
			echo json_encode($data);
			return;
		}

		//$sql = "SELECT * FROM `cs_user` WHERE i_status = 0 AND i_user_type = :type AND s_phone = :phone AND s_password = :pass";
		$sql = "SELECT * FROM `cs_user` WHERE i_status = 0 AND s_phone = :phone AND s_password = :pass";
		try {

			$db = getConnection();
			$stmt = $db->prepare($sql);
			// $stmt->bindValue(':type',$type);
			// $stmt->bindValue(':phone',$phone);
			// $stmt->bindValue(':pass',$pass);
			// $stmt->bindParam('type',$type);
			$stmt->bindParam('phone',$phone);
			$stmt->bindParam('pass',$pass);
			$stmt->execute();
			$user = $stmt->fetch(PDO::FETCH_ASSOC);

			if($user) {
				$user['l_coach_id'] = $user['coach_id'];
				if(file_exists(__DIR__.'/../sadmin/'.$user['s_imgurl'])) {
					$user['s_imgurl'] = S_HTTP_HOST.$user['s_imgurl'];
				} else {
					$user['s_imgurl'] = HTTP_HOST.$user['s_imgurl'];
				}
				// 获取身份证号码
				$sql = "SELECT `identity_id`, `photo_id` FROM `cs_users_info` WHERE `user_id` = '{$user['l_user_id']}'";
				$stmt = $db->query($sql);
				$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
				if($user_info) {
					$user['identity_id'] = $user_info['identity_id'];
					$user['photo_id'] = $user_info['photo_id'];
				} else {
					$user['identity_id'] = '';
					$user['photo_id'] = '';
				}
				$data = array('code'=>200, 'data'=>$user);

				// 更新登录次数信息
				$sql = "SELECT `s_username`,`is_first` FROM `cs_user` WHERE `s_phone` = ".$phone;
				$stmt = $db->query($sql);
				$row = $stmt->fetchObject();
				if($row->s_username != "" && $row->is_first == 0) {
					$sql = "UPDATE `cs_user` SET `is_first` = 1 WHERE `s_phone` = ".$phone;
					$res = $db->query($sql);
				}

				$loginauth = $user['l_user_id'].'\t'.$user['s_username'].'\t'.$user['s_real_name'].'\t'.$user['s_phone'];
				$_SESSION['loginauth'] = $crypt->encrypt($loginauth);

			} else {
				$data = array('code'=>2, 'data'=>'登录失败');
			}
			echo json_encode($data);
			return;
		} catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('login:params[phone:'.$phone.',pass:'.$pass.',type:'.$type.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');

			echo json_encode($data);
			return;
		}
	}

	$app->run();

?>