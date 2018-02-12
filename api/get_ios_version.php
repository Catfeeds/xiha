<?php  
	/**
	 * 获取IOS端的版本号
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->get('/','getIosVersion');
	$app->run();

	// 获取iOS的版本号
	function getIosVersion() {
		Global $app, $crypt;

		$sql = "SELECT `version`,`addtime` FROM `cs_app_version` WHERE `type_id` = 2 ORDER BY `addtime` DESC LIMIT 1";
		try {
			$db = getConnection();
			$stmt = $db->query($sql);
			$versioninfo = $stmt->fetch(PDO::FETCH_ASSOC);
			$versioninfo['addtime'] = date('Y-m-d H:i', $versioninfo['addtime']);
			$data = array('code'=>200, 'data'=>$versioninfo);
			echo json_encode($data);

		} catch(PDOException $e) {
			$data = array('code'=>1, 'data'=>$e->getMessage());
			echo json_encode($data);
			exit;
		}
	}
?>
