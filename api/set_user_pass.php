<?php

	/**
	 * 修改用户密码
	 * @param $phone int 手机号
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
	$app->post('/','setPass');

	// 修改密码
	function setPass() {
		Global $app, $crypt;
		$request = $app->request();
		$phone = $request->params('phone'); //手机号
		$pass = md5($request->params('pass')); //密码

		$sql = "UPDATE `cs_user` SET `s_password` = :pass WHERE `s_phone` = :phone";

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
			// echo $crypt->encrypt(json_encode($data));

		} catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('set_user_pass:params[phone:'.$phone.',pass:'.$pass.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));
		}
	}

	$app->run();
?>