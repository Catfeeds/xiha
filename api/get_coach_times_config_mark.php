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
		if(empty($coach_id)) {
			$data = array('code'=>-1, 'data'=>'参数错误');
			echo json_encode($data);
			exit();
		}

		try {
			$db = getConnection();

			// 获取驾校设置的时间配置
			$sql = "SELECT `s_school_name_id` FROM `cs_coach` WHERE `l_coach_id` = $coach_id";
			$stmt = $db->query($sql);
			$coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($coach_info)) {
				$data = array('code'=>-3, 'data'=>'参数错误');
				echo json_encode($data);
				exit();
			}

			$sql = "SELECT * FROM `cs_coach_time_config` WHERE `school_id` = ".$coach_info['s_school_name_id'];
			$stmt = $db->query($sql);
			$coach_time_config = $stmt->fetchAll(PDO::FETCH_ASSOC);
			echo "<pre>";

			if(empty($coach_time_config)) {
				$data = array('code'=>-4, 'data'=>'你所属的驾校还未设置时间段，请联系你所属驾校！');
				echo json_encode($data);
				exit();
			}
			// 获取7天日期
			$date_config = getCoachTimeConfig();
			foreach ($date_config as $key => $value) {
				if($coach_time_config) {
					foreach ($coach_time_config as $k => $v) {
						$date_config[$key]['time_config_id'][] = $v['id'];
					}
					$date_config[$key]['time_list'] = $coach_time_config;
				}
					
			}

			// 获取当前配置的教练时间表
			$sql = "SELECT `time_config_money_id`,`year`,`month`,`day` FROM `cs_current_coach_time_configuration` WHERE `coach_id` = ".$coach_id;
			$stmt = $db->query($sql);
			$coach_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if($coach_list) {
				$coach_time_list = array();
				foreach ($coach_list as $key => $value) {
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
						$coach_time_list[$key]['date'] = $date;
						$coach_time_list[$key]['time_config_id'][] = $v['id'];
					}
					$coach_time_list[$key]['time_list'] = $time_list;
				}

				print_r($date_config);
				print_r($coach_time_list);

				foreach ($date_config as $key => $value) {
					foreach ($coach_time_list as $k => $v) {
						if($v['date'] == $value['date_format']) {
							print_r($value['time_config_id']);
							print_r($v['time_config_id']);
							$diff = array_diff($value['time_config_id'], $v['time_config_id']);
							print_r($diff);
						}
					}				
				}

				$data = array('code'=>200, 'data'=>$coach_time_list);
				echo json_encode($data);
				// $data = str_replace("\/", "/", json_encode($data));
				// echo str_replace("\\\"", "'", $data);
				exit();

			} else {
				$data = array('code'=>-2, 'data'=>'暂无设置教练接单时间');
				echo json_encode($data);
				exit();
			}

		} catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('get_coach_times_config_mark:params[coach_id:'.$coach_id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');

			echo json_encode($data);
			exit();
		}

	}

	/**
	 * 生成可变的日期配置
	 * @param $id int 教练ID
	 * @return array 
	 */
	function getCoachTimeConfig() {
		$current_time = time();
		$year = date('Y', $current_time); //年
		$month = intval(date('m', $current_time)); //月
		$day = intval(date('d', $current_time)); //日

		// 构建一个时间
		$build_date_timestamp = mktime(0,0,0,$month,$day,$year);

		// 循环7天日期
		$date_config = array();
		for($i = 0; $i <= 6; $i++) {
			// $date_config['date'][] = date('m-d', strtotime("+".($i)." day")); //或者这种算法
			$date_config[$i]['date'] = date('m-d', $build_date_timestamp + ( 24 * 3600 * $i));	
			$date_config[$i]['month'] = intval(date('m', $build_date_timestamp + ( 24 * 3600 * $i)));	
			$date_config[$i]['day'] = intval(date('d', $build_date_timestamp + ( 24 * 3600 * $i)));	
			$date_config[$i]['date_format'] = intval(date('m', $build_date_timestamp + ( 24 * 3600 * $i))).'-'.intval(date('d', $build_date_timestamp + ( 24 * 3600 * $i)));	

		}

		// 数据表获取当前时间配置
		// $sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config`";
		// $row = $this->_getAllRecords($sql);
		// $date_config['time'] = $row;

		return $date_config;
	}
?>