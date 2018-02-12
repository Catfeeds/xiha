<?php  
	/**
	 * 获取学员端教练评价信息
	 * @param $id int 教练ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->get('/','getAndroidVersion');
	$app->run();

	// 获取安卓的版本号
	function getAndroidVersion() {
		Global $app, $crypt;

		$sql = "SELECT `version`,`addtime` FROM `cs_app_version` WHERE `type_id` = 1 ORDER BY `addtime` DESC LIMIT 1";
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