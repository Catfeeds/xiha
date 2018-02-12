<?php  
	/**
	 * 预约教练时间
	 * @param $coach_id int 教练ID
	 * @param $time_config_id 时间配置的ID 
	 * @param $user_id 当前学员ID 
	 * @param $date 预约的时间 
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','appointTime');
	$app->run();

	// 预约教练时间
	function appointTime() {
		Global $app, $crypt;
		$request = $app->request();
		$coach_id = $request->params('coach_id');
		$time_config_id = $request->params('time_config_id');
		$user_id = $request->params('user_id');
		$date = strtotime($request->params('date'));
		$year = date('Y',$date);
		$month = date('m',$date);
		$day = date('d',$date);

		$sql = "INNSERT INTO `cs_coach_appoint_time` (`coach_id`, `time_config_id`, `user_id`, `year`, `month`, `day`) ";
		$sql .= "VALUES (:coach_id, :time_config_id, :week_id, :user_id, :year, :month, :day)";
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam('coach_id', $coach_id);
			$stmt->bindParam('time_config_id', $time_config_id);
			$stmt->bindParam('user_id', $user_id);
			$stmt->bindParam('year', $year);
			$stmt->bindParam('month', $month);
			$stmt->bindParam('day', $day);

			$res = $stmt->execute();
			if($res) {
				$data = array('code'=>200, 'data'=>'预约成功');
				echo json_encode(value);
			} else {
				$data = array('code'=>2, 'data'=>'预约失败');
			}
		} catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('appoint_coach_time:params[coach_id:'.$coach_id.',time_config_id:'.$time_config_id.',user_id:'.$user_id.',date:'.$date.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
		}
	}
?>