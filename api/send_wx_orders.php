<?php  
	/**
	 * 预约教练时间生成订单
	 * @param $coach_id int 教练ID
	 * @param $time_config_id 时间配置的ID 
	 * @param $user_id 当前学员ID 
	 * @param $date 预约的时间 
	 * @param $money 总价钱 
	 * @param $type 交易类型 1:支付宝 2：线下支付 3：微信
	 * @return string AES对称加密（加密字段xhxueche）
		* @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';

	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','takeOrders');
	$app->run();

	function takeOrders() {
		Global $app, $crypt;
		$request = $app->request();
		$coach_id = $request->params('coach_id');
		$time_config_id = $request->params('time_config_id');
		$user_id = $request->params('user_id');
		$datetime = $request->params('date');
		$dc_money = $request->params('money');
		$type = $request->params('type'); //交易类型
		$year = date('Y',time());
		$date = explode('-', $datetime);
		$month = $date[0];
		$day = $date[1];

		if($month == 1) {
			$year = '2016';
		}
		
		$db = getConnection();
		try {
			if(empty($time_config_id) || empty($coach_id) || empty($user_id) || empty($datetime) || empty($type)) {
				$data = array('code'=>-6, 'data'=>'参数错误');
				echo json_encode($data);
				exit();
			}
			// 获取当前预约时长
			$time_config = array();
			if($time_config_id) {
				$time_config = array_filter(explode(',', $time_config_id));
				$i_service_time = count($time_config);	
			} else {
				$i_service_time = 0;
			}	

			// 判断当前时间段价格是否跟提交的总价格是否相同
			$params = array(
				'user_id'=>"$user_id",
				'coach_id'=>"$coach_id",
				'time_config_id'=>"$time_config_id",
				'date'=>"$datetime",
				'coupon_id'=>1,
				'param_1'=>1,	
				'param_2'=>1,	
				'param_3'=>1,	
				'param_4'=>1
			);
			$res = request_post(SHOST.'api/order_check.php', $params);
			$check = json_decode(trim($res,chr(239).chr(187).chr(191)), true);
			if($check['code'] == 200) {
				if($check['data']['final_price'] != $dc_money) {
					$data = array('code'=>-3, 'data'=>'价格不正确');
					echo json_encode($data);
					exit();
				}
			} else {
				$data = array('code'=>-5, 'data'=>$check['data']);
				echo json_encode($data);
				exit();
			}

			// exit();
			$db->beginTransaction(); // 开启一个事务
			$row = null;
			// 查询是否插入答被预约时间表
			$sql = "SELECT * FROM `cs_coach_appoint_time` as t LEFT JOIN `cs_study_orders` as o ON o.`appoint_time_id` = t.`id` WHERE (o.`i_status` != 3 OR o.`i_status` != 101) AND t.`coach_id` = $coach_id AND t.`time_config_id` IN (".implode(',', $time_config).") AND t.`year` = $year AND t.`month` = $month AND t.`day` = $day";
			$stmt = $db->query($sql);
			$appoint_row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($appoint_row) {
				$data = array('code'=>-1, 'data'=>'已下单');
				echo json_encode($data);
				exit();
			}

			$sql = "INSERT INTO `cs_coach_appoint_time` (`coach_id`, `time_config_id`, `user_id`, `year`, `month`, `day`, `addtime`) ";
			$sql .= "VALUES ('".$coach_id."', '".$time_config_id."', '".$user_id."', '".$year."', '".$month."', '".$day."', ".time().")";

			$row = $db->exec($sql);
			if(!$row) {
				$data = array('code'=>-2, 'data'=>'下单失败！');
				echo json_encode($data);
				exit();
				throw new PDOException('提交表单错误');
			}
			$appoint_time_id = $db->lastInsertId();
			// 生成订单号
			// $s_order_no = strtoupper(dechex(date('m'))).date('d').substr(time(),-5).substr(microtime(),2,6).sprintf('%d',rand(0,99));
			$s_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);

			// 获取当前预约时间段的科目名称和牌照名称
			$sql = "SELECT `time_lisence_config_id`, `time_lesson_config_id` FROM `cs_current_coach_time_configuration` WHERE `coach_id` = $coach_id AND `year` = $year AND `month` = $month AND `day` = $day";
			$stmt = $db->query($sql);
			$coach_time_config = $stmt->fetch(PDO::FETCH_ASSOC);
			$lisence_name = array();
			$lesson_name = array();
			if($coach_time_config) {
				if($coach_time_config['time_lisence_config_id']) {
					$time_lisence_config_id = json_decode($coach_time_config['time_lisence_config_id'], true);
					foreach ($time_lisence_config_id as $key => $value) {
						if(in_array($key, $time_config)) {
							$lisence_name[] = $value;
						}
					}
				}
				if($coach_time_config['time_lesson_config_id']) {
					$time_lesson_config_id = json_decode($coach_time_config['time_lesson_config_id'], true);
					foreach ($time_lesson_config_id as $key => $value) {
						if(in_array($key, $time_config)) {
							$lesson_name[] = $value;
						}
					}
				}	
			} else {
				// 从配置表中获取科目，课程
				$sql = "SELECT `license_no`, `subjects` FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config).") AND `status` = 1";
				$stmt = $db->query($sql);
				$_coach_time_config_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if($_coach_time_config_info) {
					foreach ($_coach_time_config_info as $key => $value) {
						$lisence_name[] = $value['license_no'];
						$lesson_name[] = $value['subjects'];
					}
				}
			}

			$lisence_name = array_unique($lisence_name);
			$lesson_name = array_unique($lesson_name);

			// 获取当前用户的信息
			$sql = "SELECT `s_username`,`s_real_name`, `s_phone` FROM `cs_user` WHERE `l_user_id` = $user_id AND `i_user_type` = 0";
			$stmt = $db->query($sql);
			$userinfo = $stmt->fetch();

			// 获取当前教练的信息
			$sql = "SELECT `s_coach_name`, `s_coach_phone` FROM `cs_coach` WHERE `l_coach_id` = $coach_id";
			$stmt = $db->query($sql);
			$coachinfo = $stmt->fetch();

			$dt_appoint_time = $year.'-'.$date[0].'-'.$date[1];
			$sql = "INSERT INTO `cs_study_orders` ";
			$sql .= " (`s_order_no`, `dt_order_time`, `appoint_time_id`, `time_config_id`, `l_user_id`, `s_user_name`, `s_user_phone`, `l_coach_id`, `s_coach_name`, ";
			$sql .=	" `s_coach_phone`, `s_lisence_name`, `s_lesson_name`, `dc_money`, `dt_appoint_time`, `i_service_time`, `i_status`, `s_zhifu_dm`, `dt_zhifu_time`, `deal_type`) VALUES (";
			$sql .= " '".$s_order_no."','".time()."','".$appoint_time_id."','".$time_config_id."','".$user_id."','".$userinfo['s_real_name']."','".$userinfo['s_phone']."',";
			$sql .= " '".$coach_id."','".$coachinfo['s_coach_name']."','".$coachinfo['s_coach_phone']."','".implode(',', $lisence_name)."','".implode(',', $lesson_name)."','".$dc_money."', '".$dt_appoint_time."','".$i_service_time."', 1, '".guid(false)."','".time()."','".$type."')";
			$row = $db->exec($sql);
			$db->commit();
			$data = array('code'=>200, 'data'=>'下单成功！');
			$db = null;
			echo json_encode($data);

		} catch (PDOException $e) {
			$db->rollBack(); // 执行任务失败 事务回滚
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('send_orders:params[coach_id:'.$coach_id.',time_config_id:'.$time_config_id.',user_id:'.$user_id.',date:'.$datetime.',money:'.$dc_money.',type:'.$type.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
		}
	}

	function guid($opt = true){       //  Set to true/false as your default way to do this.

	    if( function_exists('com_create_guid')) {
	        if( $opt ){ 
	        	return com_create_guid(); 
	        } else { 
	        	return trim( com_create_guid(), '{}' ); 
	        }
	    } else {
	        mt_srand( (double)microtime() * 10000 );    // optional for php 4.2.0 and up.
	        $charid = strtoupper( md5(uniqid(rand(), true)) );
	        $hyphen = chr( 45 );    // "-"
	        $left_curly = $opt ? chr(123) : "";     //  "{"
	        $right_curly = $opt ? chr(125) : "";    //  "}"
	        $uuid = $left_curly
	            . substr( $charid, 0, 8 ) . $hyphen
	            . substr( $charid, 8, 4 ) . $hyphen
	            . substr( $charid, 12, 4 ) . $hyphen
	            . substr( $charid, 16, 4 ) . $hyphen
	            . substr( $charid, 20, 12 )
	            . $right_curly;
	        return $uuid;
	    }
	}

	/**
	 * 模拟post进行url请求
	 * @param string $url
	 * @param array $post_data
	 */
	function request_post($url = '', $post_data = array()) {
	    if (empty($url) || empty($post_data)) {
	        return false;
	    }
	    
	    $o = "";
	    foreach ( $post_data as $k => $v ) 
	    { 
	        $o.= "$k=" . urlencode( $v ). "&" ;
	    }
	    $post_data = substr($o,0,-1);

	    $postUrl = $url;
	    $curlPost = $post_data;
	    $ch = curl_init();//初始化curl
	    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
	    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
	    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
	    $data = curl_exec($ch);//运行curl
	    curl_close($ch);
	    
	    return $data;
	}
	
?>