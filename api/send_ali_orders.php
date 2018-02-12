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
		$date = $request->params('date');
		$dc_money = $request->params('money');
		$type = $request->params('type'); //交易类型
		$s_zhifu_dm = $request->params('s_zhifu_dm'); //支付宝交易号
		$year = date('Y',time());

		if(preg_match('|-|', $date)) {
			$date = explode('-', $date);
			$month = $date[0];
			$day = $date[1];
		} else {
			$month = 0;
			$day = 0;
		}

		if($month == 1) {
			$year = '2017';
		}
		
		try {
			$db = getConnection();
			// $year_code = array('A','B','C','D','E','F','G','H','I','J');
			// $order_sn = $year_code[intval(date('Y'))-2010].strtoupper(dechex(date('m'))).date('d').substr(time(),-5).substr(microtime(),2,5).sprintf('d',rand(0,99));

			// 生成订单号
			// $order_sn = strtoupper(dechex(date('m'))).date('d').substr(time(),-5).substr(microtime(),2,6).sprintf('%d',rand(0,99));

			$db->beginTransaction(); // 开启一个事务
			$row = null;

			// 查询是否插入答被预约时间表
			$sql = "SELECT * FROM `cs_coach_appoint_time` as t LEFT JOIN `cs_study_orders` as o ON o.`appoint_time_id` = t.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND t.`coach_id` = $coach_id AND t.`time_config_id` LIKE '%".$time_config_id."%' AND t.`user_id` = $user_id AND t.`year` = $year AND t.`month` = $month AND t.`day` = $day";
			//$sql = "SELECT * FROM `cs_study_orders` WHERE `s_zhifu_dm` = '".$s_zhifu_dm."'";
			$stmt = $db->query($sql);
			$appoint_row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($appoint_row) {
				$data = array('code'=>-1, 'data'=>'已下单');
				echo json_encode($data);
				exit();
			}

			// 查询是否插入答被预约时间表
			$sql = "SELECT * FROM `cs_coach_appoint_time` WHERE `coach_id` = $coach_id AND `time_config_id` = '".$time_config_id."' AND `user_id` = $user_id AND `year` = $year AND `month` = $month AND `day` = $day";
			$stmt = $db->query($sql);
			// echo $sql;
			$appoint_row = $stmt->fetch(PDO::FETCH_ASSOC);
			// if(!$appoint_row) {
				$sql = "INSERT INTO `cs_coach_appoint_time` (`coach_id`, `time_config_id`, `user_id`, `year`, `month`, `day`, `addtime`) ";
				$sql .= "VALUES ('".$coach_id."', '".$time_config_id."', '".$user_id."', '".$year."', '".$month."', '".$day."', ".time().")";

				$row = $db->exec($sql);
				if(!$row) {
					// $data = array('code'=>200, 'data'=>'下单失败！');
					// echo json_encode($data);
					// exit();
					throw new PDOException('提交表单错误');
				}
				$appoint_time_id = $db->lastInsertId();

				// 生成订单号
				$s_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
				
				// 获取当前预约时长
				$time_config = array();
				if($time_config_id) {
					$time_config = array_filter(explode(',', $time_config_id));
					$i_service_time = count($time_config);	
				} else {
					$i_service_time = 0;
				}

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
				$sql = "SELECT `s_username`, `s_real_name`, `s_phone` FROM `cs_user` WHERE `l_user_id` = $user_id AND `i_user_type` = 0";
				$stmt = $db->query($sql);
				$userinfo = $stmt->fetch();

				// 获取当前教练的信息
				$sql = "SELECT `s_coach_name`, `s_coach_phone` FROM `cs_coach` WHERE `l_coach_id` = $coach_id";
				$stmt = $db->query($sql);
				$coachinfo = $stmt->fetch();

				$dt_appoint_time = $year.'-'.$date[0].'-'.$date[1];
				$sql = "INSERT INTO `cs_study_orders` (";
				$sql .= " `s_order_no`, ";
				$sql .= " `dt_order_time`,";
				$sql .= " `appoint_time_id`,";
				$sql .= " `time_config_id`,";
				$sql .= " `l_user_id`,";
				$sql .= " `s_user_name`,";
				$sql .= " `s_user_phone`,";
				$sql .= " `l_coach_id`,";
				$sql .= " `s_coach_name`, ";
				$sql .=	" `s_coach_phone`,";
				$sql .=	" `s_address`,";
				$sql .= " `s_lisence_name`,";
				$sql .= " `s_lesson_name`,";
				$sql .= " `dc_money`,";
				$sql .= " `dt_appoint_time`,";
				$sql .= " `i_start_hour`,";
				$sql .= " `i_end_hour`,";
				$sql .= " `i_service_time`,";
				$sql .= " `i_status`,";
				$sql .= " `s_zhifu_dm`,";
				$sql .= " `dt_zhifu_time`,";
				$sql .= " `deal_type`";
				$sql .= ") VALUES (";
				$sql .= " '".$s_order_no."',";
				$sql .= " '".time()."',";
				$sql .= " '".$appoint_time_id."',";
				$sql .= " '".$time_config_id."',";
				$sql .= " '".$user_id."',";
				$sql .= " '".$userinfo['s_real_name']."',";
				$sql .= " '".$userinfo['s_phone']."',";
				$sql .= " '".$coach_id."',";
				$sql .= " '".$coachinfo['s_coach_name']."',";
				$sql .= " '".$coachinfo['s_coach_phone']."',";
				$sql .= " '',";
				$sql .= " '".implode(',', $lisence_name)."',";
				$sql .= " '".implode(',', $lesson_name)."',";
				$sql .= " '".$dc_money."',";
				$sql .= " '".$dt_appoint_time."',";
				$sql .= " 0,";
				$sql .= " 0,";
				$sql .= " '".$i_service_time."',";
				$sql .= " 1, '".$s_zhifu_dm."',";
				$sql .= " '".date('Y-m-d H:i:s', time())."',";
				$sql .= " '".$type."')";
				// echo $sql;
				$row = $db->exec($sql);
				$db->commit(); 
				$data = array('code'=>200, 'data'=>'下单成功！');
				
				// 获取时间段
				$time_config_arr = array();
				if(!empty($time_config)) {
					$sql = "SELECT * FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config).")";
					$stmt = $db->query($sql);
					$coach_time_config_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

					$time_config_arr = array();
					foreach ($coach_time_config_info as $key => $value) {
						$start_minute = $value['start_minute'] == 0 ? '00' : $value['start_minute'];
						$end_minute = $value['end_minute'] == 0 ? '00' : $value['end_minute'];
						$time_config_arr[] = $value['start_time'].':'.$start_minute.'-'.$value['end_time'].':'.$end_minute;

						// $time_config_arr[] = $value['start_time'].':00-'.$value['end_time'].':00';
					}	
				}

				$message_username = '';
				if($userinfo['s_real_name'] != '') {
					$message_username = $userinfo['s_real_name'];
				} else {
					$message_username = '手机号'.$userinfo['s_phone'];
				}

				// 教练端发送消息
				$params_coach = array(
					'user_phone'=>$coachinfo['s_coach_phone'],
					'member_id'=>$coach_id,
					'member_type'=>2, // 1：学员 2：教练
					's_beizhu'=>'学员订单',
					'i_yw_type'=>2, // 1:通知 2：正常订单消息
					'title'=>'预约学车订单',
					'content'=>'教练您好，'.$message_username.'学员预约您'.$month.'月'.$day.'日'.implode(',', $time_config_arr).'点的学车服务，请提前做好安排!',
					'type'=>2 //(1:学员端 2：教练端)
				);
				$res = request_post(SHOST.'api/message_push.php', $params_coach);

				// 通过教练ID获取学校名称
				$sql = "SELECT s.`s_school_name` FROM `cs_school` as s LEFT JOIN `cs_coach` as c ON c.`s_school_name_id` = s.`l_school_id` WHERE c.`l_coach_id` = $coach_id";
				$stmt = $db->query($sql);
				$school_info = $stmt->fetch(PDO::FETCH_ASSOC);
				$s_school_name = '嘻哈驾校';
				if($school_info) {
					$s_school_name = $school_info['s_school_name'];
				}

				// 学员端发送消息
				$params_student = array(
					'user_phone'=>$userinfo['s_phone'],
					'member_id'=>$user_id,
					'member_type'=>1, // 1：学员 2：教练
					's_beizhu'=>'学员订单',
					'i_yw_type'=>2, // 1:通知 2：正常订单消息
					'title'=>'预约学车订单',
					'content'=>'学员您好，您已成功预约'.$s_school_name.$coachinfo['s_coach_name'].'教练'.$month.'月'.$day.'日'.implode(',', $time_config_arr).'时的学车业务，请准时到达学车地点，提前和教练电话沟通。',
					'type'=>2 //(1:学员端 2：教练端)
				);
				$res = request_post(SHOST.'api/message_push.php', $params_student);


			// } else {
			// 	$data = array('code'=>-1, 'data'=>'已下单');
			// }
			$db = null;
			echo json_encode($data);	

		} catch (PDOException $e) {
			$db->rollBack(); // 执行任务失败 事务回滚
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('send_ali_orders:params[coach_id:'.$coach_id.',time_config_id:'.$time_config_id.',user_id:'.$user_id.',date:'.implode('-', $date).',money:'.$dc_money.',type:'.$type.',s_zhifu_dm:'.$s_zhifu_dm.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
		}
	}

	function guid($opt = true) {       //  Set to true/false as your default way to do this.

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