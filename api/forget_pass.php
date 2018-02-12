<?php
	

	/**
	 * 忘记用户密码(修改密码)
	 * @param $phone int 手机号码
	 * @param $pass string 密码
	 * @param $code int 验证码
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','forgetPass');

	// 忘记密码方法
	function forgetPass() {
		Global $app, $crypt;
		$request = $app->request;
		$phone = $request->params('phone');
		$pass = md5($request->params('pass'));
		$code = $request->params('code');

		// 验证码不能为空
		if(empty($code)) {
			$data = array('code'=>-1, 'data'=>'请输入验证码');
			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));
			return;
		}

		// 手机号 密码不能为空
		if(empty($phone) || empty($pass)) {
			$data = array('code'=>-3, 'data'=>'请完善注册信息');
			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));
			return;
		}

		// 验证验证码是否正确
		$codeinfo = getCode($phone);
		if($codeinfo) {
			if($codeinfo->s_code != $code) {
				$data = array('code'=>-4, 'data'=>'验证码错误');
				// echo $crypt->encrypt(json_encode($data));
				echo json_encode($data);
				return;
			}
		} else {
			$data = array('code'=>-5, 'data'=>'请获取验证码');
			// echo $crypt->encrypt(json_encode($data));
			echo json_encode($data);
			return;
		}

		$sql = "SELECT * FROM `cs_user` WHERE `s_phone` = $phone";
		try {
			$db = getConnection();
			$stmt = $db->query($sql);
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if(!$row) {
				$data = array('code'=>-6, 'data'=>'账号不存在');
				echo json_encode($data);
				return;
			}

		} catch(PDOException $e) {
			setapilog('forget_pass:params[phone:'.$phone.',pass:'.$pass.',code:'.$code.'], error:'.$e->getMessage());
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
		}

		// 修改密码
		$sql = "UPDATE `cs_user` set `s_password` = :pass WHERE `s_phone` = :phone";
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam('pass', $pass);
			$stmt->bindParam('phone', $phone);
			$res = $stmt->execute();
			if($res) {
				$data = array('code'=>200, 'data'=>'修改成功');	
			} else {
				$data = array('code'=>2, 'data'=>'修改失败');
			}
			echo json_encode($data);

		} catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('forget_pass:params[phone:'.$phone.',pass:'.$pass.',code:'.$code.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));
		}
	}

	// 获取验证码
	function getCode($phone) {
		$sql = "SELECT * FROM `cs_verification_code` WHERE `s_phone` = :phone";
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam('phone', $phone);
			$stmt->execute();
			$phone_code = $stmt->fetchObject();
			return $phone_code;
		} catch(PDOException $e) {
			$data = -1;
			setapilog('forget_pass:params[phone:'.$phone.'], error:'.$e->getMessage());
			return $data;
		}
	}

	$app->run();
?>