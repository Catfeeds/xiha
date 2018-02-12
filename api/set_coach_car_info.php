<?php  
	/**
	 * 教练端添加车辆记录
	 * @param $car_id int 车辆ID
	 * @param $type_id int 记录类型
	 * @param $content string 记录
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','setCarInfo');
	$app->run();

	// 教练端添加记录
	function setCarInfo() {
		Global $app, $crypt;

		$request = $app->request();
		$car_id = $request->params('car_id');
		$type_id = $request->params('type_id');
		$content = $request->params('content');

		$sql = "INSERT INTO `cs_cars_info`(`car_id`, `type_id`, `content`, `addtime`) VALUES ($car_id, $type_id, '".$content."', '".time()."')";
		try {
			$db = getConnection();
			$stmt = $db->query($sql);
			if($stmt) {
				$data = array('code'=>200, 'data'=>'添加成功');
			} else {
				$data = array('code'=>-1, 'data'=>'添加失败');
			}
			echo json_encode($data);

		}catch(PDOException $e) {
			setapilog('set_coach_car_info:params[car_id:'.$car_id.',type_id:'.$type_id.',content:'.$content.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
		}
	}
?>