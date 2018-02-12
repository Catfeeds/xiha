<?php  
	/**
	 * 上传学员坐标位置
	 * @param $user_id 学员ID
	 * @param $x 学员经度
	 * @param $y 学员纬度
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
		$user_id = $request->params('id');

		try {

			$sql = "INSERT INTO `cs_users_info` (`x`, `y`, `user_id`) VALUES (:distance_x, :distance_y, :user_id)";
 			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam('distance_x', $distance_x);
			$stmt->bindParam('distance_y', $distance_y);
			$stmt->bindParam('user_id', $user_id);
			$res = $stmt->execute();
			if($res) {
				$data = array('code'=>200, 'data'=>'上传成功');
			} else {
				$data = array('code'=>2, 'data'=>'上传错误');
			}
			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));

		} catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('send_student_location:params[x:'.$distance_x.',y:'.$distance_y.',id:'.$user_id.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));
		}
	}

	$app->run();
?>