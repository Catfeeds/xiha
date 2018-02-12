<?php

	/**
	 * 上传教练坐标接口
	 * @param $x char 经度
	 * @param $y char 纬度
	 * @param $phone int 手机号
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
	$app->post('/','sendLocation');

	// 上传位置坐标
	function sendLocation() {
		Global $app, $crypt;
		$request = $app->request();
		$distance_x = $request->params('x');
		$distance_y = $request->params('y');
		$phone = $request->params('phone');
		$coach_id = $request->params('id');
		try {
			$sql = "UPDATE `cs_coach` SET `dc_coach_distance_x` = :distance_x, `dc_coach_distance_y` = :distance_y";
			$sql .= " WHERE `s_coach_phone` = :phone AND `l_user_id` = :coach_id";
 			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam('phone', $phone);
			$stmt->bindParam('coach_id', $coach_id);
			$stmt->bindParam('distance_x', $distance_x);
			$stmt->bindParam('distance_y', $distance_y);
			$res = $stmt->execute();
			if($res) {
				$data = array('code'=>200, 'data'=>'上传成功');
			} else {
				$data = array('code'=>2, 'data'=>'上传错误');
			}
			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));

		} catch (PDOException $e) {
			setapilog('send_coach_location:params[x:'.$distance_x.',y:'.$distance_y.',phone:'.$phone.',id:'.$coach_id.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));
		}
	}

	$app->run();
?>