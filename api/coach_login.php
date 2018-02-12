<?php 

	/**
	 * 教练端登录接口(正常密码登录)
	 * @param $phone int 手机号码
	 * @param $pass string 密码
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','login');

	// 登录验证
	function login() {
		Global $app, $crypt;
		$request = $app->request();
		$phone = $request->params('phone'); //手机号
		$pass = md5($request->params('pass')); //密码

		$sql = "SELECT * FROM `cs_user` WHERE i_status = 0 AND i_user_type = 1 AND s_phone = :phone AND s_password = :pass";
		try {

			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam('phone',$phone);
			$stmt->bindParam('pass',$pass);
			$stmt->execute();
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			$list = array();
			if($user) {
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

				// 根据教练id获取教练详情
				$sql = "SELECT * FROM `cs_coach` WHERE `l_coach_id` = ".$user['coach_id'];
				$stmt = $db->query($sql);
				$coach_info = $stmt->fetch(PDO::FETCH_ASSOC);

				if(file_exists(__DIR__.'/../sadmin/'.$coach_info['s_coach_imgurl'])) {
					$coach_info['s_coach_imgurl'] = S_HTTP_HOST.$coach_info['s_coach_imgurl'];
				} else {
					$coach_info['s_coach_imgurl'] = HTTP_HOST.$coach_info['s_coach_imgurl'];
				}	
				if($coach_info) {
					$data = array('code'=>200, 'data'=>$coach_info);
				} else {
					$data = array('code'=>1, 'data'=>'登录失败');
				}

			} else { 
				$data = array('code'=>2, 'data'=>'登录失败');
			}
			echo json_encode($data);

		} catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('coach_login:params[phone:'.$phone.',pass:'.$pass.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');

			echo json_encode($data);
		}
	}

	$app->run();

?>