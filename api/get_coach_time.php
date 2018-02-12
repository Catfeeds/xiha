<?php  
	/**
	 * 获取教练时间配置
	 * @param $coach_id 教练ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getCoachTime');
	$app->run();

	// 获取时间配置
	function getCoachTime() {
		Global $app, $crypt;
		$request = $app->request();
		$coach_id = $request->params('coach_id');

		// 如果传的是时间字符串
		$db = getConnection();
		if(empty($coach_id)) {
			$data = array('code'=>-1, 'data'=>'参数错误');
			echo json_encode($data);
			exit();
		}

		// 获取当前配置的教练时间表
		$sql = "SELECT `time_config_money_id`,`year`,`month`,`day` FROM `cs_current_coach_time_configuration` WHERE `coach_id` = ".$coach_id;
		try {
			$stmt = $db->query($sql);
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if($res) {
				$coach_time_list = array();
				foreach ($res as $key => $value) {
					$config_money_arr = json_decode($value['time_config_money_id'], true);

					$time_config_id = array_keys($config_money_arr);
					$money = array_values($config_money_arr);
					$month = $value['month'];
					$day = $value['day'];
					$date = $month.'-'.$day;
					
					// 获取当前时间配置
					$sql = "SELECT * FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config_id).")";
					$stmt = $db->query($sql);
					$time_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

					foreach ($time_list as $k => $v) {
						$time_list[$k]['final_price'] = $money[$k];
						$coach_time_list[$key]['time_list'] = $time_list;
						$coach_time_list[$key]['date'] = $date;
					}
				}

				$data = array('code'=>200, 'data'=>$coach_time_list);
				echo json_encode($data);
				// $data = str_replace("\/", "/", json_encode($data));
				// echo str_replace("\\\"", "'", $data);
				exit();

			} else {
				$data = array('code'=>-1, 'data'=>'暂无设置教练接单时间');
				echo json_encode($data);
				exit();
			}

		} catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('get_coach_time:params[coach_id:'.$coach_id.'], error:'.$e->getMessage());		
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit();
		}

	}
?>