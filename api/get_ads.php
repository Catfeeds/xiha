<?php  
	/**
	 * 获取广告图片
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->get('/','getAds');
	$app->run();
	function getAds() {
		// $config = array('banner11.jpg','banner12.jpg','banner13.jpg','banner14.jpg','banner15.jpg');
		$config = array('banner19.jpg', 'banner18.jpg', 'banner21.jpg', 'banner20.jpg');
		$data = array('code'=>200, 'data'=>$config);
		echo json_encode($data);
	}

?>