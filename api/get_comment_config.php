<?php  
	/**
	 * 获取评价星级的配置
	 * @param $type int 评论类型 区分教练端和学员单的星级评价显示
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->get('/type/:type','getCommentConfig');
	$app->run();

	// 评价配置
	function getCommentConfig($type) {
		Global $crypt;
		$sql = "SELECT * FROM `cs_comment_config` WHERE `xh_type` = :type";
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam('type', $type);
			$stmt->execute();

			$res = $stmt->fetchAll(PDO::FETCH_OBJ);
			if($res) {
				$data = array('code'=>200, 'data'=>$res);
			} else {
				$data = array('code'=>2, 'data'=>'暂无配置');
			}
			echo json_encode($data);

		} catch (PDOException $e) {
			setapilog('get_comment_config:params[type:'.$type.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
		}
	}
?>