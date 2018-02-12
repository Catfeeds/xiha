<?php 

	/**
	 * 登陆验证
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
	$app->get('/','_loginauth');
	$app->run();

	// 登录验证
	function _loginauth() {
		if(isset($_SESSION['loginauth'])) {
			$data = array('code'=>200, 'data'=>$_SESSION['loginauth']);
		} else {
			$data = array('code'=>-1, 'data'=>'');
		}
		echo json_encode($data);
	}

?>
