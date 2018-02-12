<?php  
	/**
	 * 驾校下单 (1：报名成功，2：申请退款中 3：退款成功 4：报名取消)
	 * @param $uid 学员ID 
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','opayment');
	$app->run();

	function opayment() {
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
			$year = '2017';
		}
		$db = getConnection();
		try {
			$data = array('code'=>200, 'data'=>'可下单');
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

			// 查询是否插入答被预约时间表
			$sql = "SELECT * FROM `cs_coach_appoint_time` as t LEFT JOIN `cs_study_orders` as o ON o.`appoint_time_id` = t.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND t.`coach_id` = $coach_id AND t.`time_config_id` IN (".implode(',', $time_config).") AND t.`year` = $year AND t.`month` = $month AND t.`day` = $day";
			$stmt = $db->query($sql);
			$appoint_row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($appoint_row) {
				$data = array('code'=>-1, 'data'=>'已下单');
				echo json_encode($data);
				exit();
			}
			echo json_encode($data);
		} catch (PDOException $e) {
			setapilog('coach_order_check:params[coach_id:'.$coach_id.',time_config_id:'.$time_config_id.',user_id:'.$user_id.',date:'.$datetime.',money:'.$dc_money.',type:'.$type.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
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