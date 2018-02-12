<?php 

	/**
	 * 用户订单验证
	 * @param $user_id int 会员ID
	 * @param $coach_id int 教练ID
	 * @param $time_config_id int 时间配置ID  1,2,3,4
	 * @param $date string 日期 2016-04-24
	 * @param $coupon_id int 优惠ID
	 * @param $param_1 string 参数一
	 * @param $param_2 string 参数二
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi,gaodacheng
	 **/

	require '../../Slim/Slim.php';
	require '../../include/common.php';
	require '../../include/crypt.php';
	require '../../include/functions.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->any('/','orderCheck');
	$app->run();

	// 登录验证
	function orderCheck() {
		Global $app, $crypt;
		$r = $app->request();

        //验证请求方式 POST
        if ( !$r->isPost() ) {
            setapilog('[order_check] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            exit( json_encode(array('code' => 106, 'data' => '请求错误')) );
        }

        //验证输入参数
        $validate_ok = validate(
            array(
                'coach_id'          => 'INT',
                'user_id'           => 'INT',
                'time_config_id'    => 'STRING',
                'date'              => 'STRING',
                'coupon_id'         => 'INT',
             ),
            $r->params()
        );

        if ( !$validate_ok['pass'] ) {
            exit( json_encode($validate_ok['data']) );
        }
		$user_id = $r->params('user_id');
		$coach_id = $r->params('coach_id');
		$time_config_id = $r->params('time_config_id');
		$date = $r->params('date');
		$coupon_id = $r->params('coupon_id');
		$param_1 = $r->params('param_1');
		$param_2 = $r->params('param_2');
		$param_3 = $r->params('param_3');
		$param_4 = $r->params('param_4');

        $date_config = explode('-', $date);
        if ( count($date_config) !== 3 ) {
            exit( json_encode( array('code' => 101, 'data' => '参数错误') ) );
        }
        $year           = $date_config[0];
        $month          = $date_config[1];
        $day            = $date_config[2];

		$time_config_id_arr = array_filter(explode(',', $time_config_id));

		try {
			$db = getConnection();

			// 获取当前教练的学校ID
			$sql = "SELECT `s_school_name_id` FROM `cs_coach` WHERE `l_coach_id` = $coach_id";
			$stmt = $db->query($sql);
			$coach_detail = $stmt->fetch(PDO::FETCH_ASSOC);
			if($coach_detail) {
				$school_id = $coach_detail['s_school_name_id'];
			} else {
				$school_id = 0;
			}

			// 查看该时间段是否被预约
			$sql = "SELECT o.`i_service_time`, o.`time_config_id` FROM `cs_study_orders` as o LEFT JOIN `cs_coach_appoint_time` as a ON o.`appoint_time_id` = a.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND o.`l_coach_id` = '{$coach_id}' AND a.`year` = '{$year}' AND a.`month` = '{$month}' AND a.`day` = '{$day}'";
			$stmt = $db->query($sql);
			$time_config_ids = array();
			$time_config_ids_arr = array();
			$is_appoint = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($is_appoint) {
				foreach ($is_appoint as $k => $v) {
					$time_config_ids = array_filter(explode(',', $v['time_config_id']));
					foreach ($time_config_ids as $e => $t) {
						$time_config_ids_arr[] = $t;
					}
				}
			}
			if(array_intersect($time_config_id_arr, $time_config_ids_arr)) {
				$data = array('code'=>-45, 'data'=>'你选择的时间段有被预约的，请您刷新当前日期');
				echo json_encode($data);
				exit();
			}

			// 获取当前驾校设置的可预约时间段
			$sql = "SELECT * FROM `cs_school_config` WHERE `l_school_id` = '{$school_id}'";
			$stmt = $db->query($sql);
			$school_config_info = $stmt->fetch(PDO::FETCH_ASSOC);
			$i_sum_appoint_time = 2;
			if($school_config_info) {
				$i_sum_appoint_time = $school_config_info['i_sum_appoint_time'];
			}

			// 查看时间段预约情况
			$sql = "SELECT o.`i_service_time` FROM `cs_study_orders` as o LEFT JOIN `cs_coach_appoint_time` as a ON o.`appoint_time_id` = a.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND o.`l_user_id` = '{$user_id}' AND o.`l_coach_id` = '{$coach_id}' AND a.`year` = '{$year}' AND a.`month` = '{$month}' AND a.`day` = '{$day}'";
			$stmt = $db->query($sql);
			$service_time_ = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$service_time_count = 0;
			if($service_time_) {
				foreach ($service_time_ as $key => $value) {
					$service_time_count += $value['i_service_time'];
				}
			}

			$total_time_price = 0;
			$start_time_arr = array();
			$end_time_arr = array();

			// 学员最多可预约两个小时
			// if(count($time_config_id_arr)+$service_time_count > $i_sum_appoint_time * 2) {
			if(count($time_config_id_arr)+$service_time_count > $i_sum_appoint_time ) {
				$data = array('code'=>-12, 'data'=>'同一天最多只能预约'.$i_sum_appoint_time.'个小时');	
				echo json_encode($data);
				exit();
			}

			// 判断当前教练有没有设置预约时间
			// $sql = "SELECT * FROM `cs_current_coach_time_configuration` WHERE `coach_id` = $coach_id AND `month` = $month AND `day` = $day";
			// $stmt = $db->query($sql);
			// $coach_time_config_info = $stmt->fetch(PDO::FETCH_ASSOC);
			// if(empty($coach_time_config_info)) {
			// 	$data = array('code'=>-11, 'data'=>'当前教练有没有设置预约时间');
			// 	echo json_encode($data);
			// 	exit();
			// }

			// 获取时间段总价
			$sql = "SELECT `time_config_money_id` FROM `cs_current_coach_time_configuration` WHERE `coach_id` = '{$coach_id}' AND `year` = '{$year}' AND `month` = '{$month}' AND `day` = '{$day}'";
			$stmt = $db->query($sql);
			$time_config_money_id_arr = $stmt->fetch(PDO::FETCH_ASSOC);
			$time_config_money_id = array();
			$total_time_price = 0;
			
			if($time_config_money_id_arr) {
				$time_config_money_id = json_decode($time_config_money_id_arr['time_config_money_id']);
				// print_r($time_config_money_id);
				foreach ($time_config_money_id as $key => $value) {
					if(in_array($key, $time_config_id_arr)) {
						$total_time_price += $value;
					}
				}
			} else {
				$sql = "SELECT `price`, `id` FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config_id_arr).")";
				$stmt = $db->query($sql);
				$coach_time_config_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if($coach_time_config_arr) {
					foreach ($coach_time_config_arr as $key => $value) {
						$total_time_price += $value['price'];
					}
				}
			}

			// 获取时间段总价
			//$sql = "SELECT `price`, `start_time`, `end_time` FROM `cs_coach_time_config` WHERE `school_id` = $school_id AND `id` IN (".implode(',', $time_config_id_arr).")";
			$sql = "SELECT `price`, `start_time`, `end_time` FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config_id_arr).")";
			$stmt = $db->query($sql);
			$coach_time_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			if($coach_time_info) {
				foreach ($coach_time_info as $key => $value) {
					// $total_time_price += $value['price'];
					$start_time_arr[] = $value['start_time'];
					$end_time_arr[] = $value['end_time'];
				}
			}

			//$start_date_format = strtotime($year.'-'.$date.' '.min($start_time_arr).':00');
			$start_date_format = strtotime($date.' '.min($start_time_arr).':00');
			//$end_date_format = strtotime($year.'-'.$date.' '.max($end_time_arr).':00');
			$end_date_format = strtotime($date.' '.max($end_time_arr).':00');

			//$min_end_time = strtotime($year.'-'.$date.' '.min($end_time_arr).':00'); // 最小的结束时间
			$min_end_time = strtotime($date.' '.min($end_time_arr).':00'); // 最小的结束时间
			//$max_start_time = strtotime($year.'-'.$date.' '.max($start_time_arr).':00'); // 最大的开始时间
			$max_start_time = strtotime($date.' '.max($start_time_arr).':00'); // 最大的开始时间

			// 当前时间在选择的两个时间段中间时
			if(time() > $start_date_format && time() < $end_date_format) {

				// 最大开始时间减去当前时间小于一个小时 请提前一个小时下单
				if($max_start_time - time() < 3600) {
					$data = array('code'=>-13, 'data'=>'请提前一个小时下单');
					echo json_encode($data);
					exit();
				}

				// 最小结束时间比当前时间小
				if($min_end_time < time()) {
					$data = array('code'=>-14, 'data'=>'所选时间段有过期的，请预约其他时间');
					echo json_encode($data);
					exit();
				}

			// 在选择的时间之外
			} else {

				// 当前时间大于最大结束时间 时间过期
				if(time() > $end_date_format) {
					$data = array('code'=>-15, 'data'=>'所选时间段有过期的，请预约其他时间');
					echo json_encode($data);
					exit();
				}

				// 当最小开始时间减去当前时间小于一个小时
				if($start_date_format - time() < 3600) {
					$data = array('code'=>-16, 'data'=>'请提前一个小时下单');
					echo json_encode($data);
					exit();
				}
			}

			// 查找当前学员有无报名驾校
			// $sql = "SELECT `so_school_id`, `so_shifts_id`, `so_order_status`, `so_pay_type` FROM `cs_school_orders` WHERE `so_user_id` = $user_id  AND `so_order_status` != 101 AND ((`so_pay_type` = 1 AND `so_order_status` = 1) OR (`so_pay_type` = 1 AND `so_order_status` = 3) OR (`so_pay_type` = 2 AND `so_order_status` = 1) OR (`so_pay_type` = 2 AND `so_order_status` = 3) OR (`so_pay_type` = 3 AND `so_order_status` = 1) OR (`so_pay_type` = 4 AND `so_order_status` = 1) OR (`so_pay_type` = 4 AND `so_order_status` = 3))";
			$sql = "SELECT `so_school_id`, `so_shifts_id`, `so_order_status`, `so_pay_type` FROM `cs_school_orders`";
			$sql .= " WHERE `so_user_id` = $user_id ";
			$sql .= " AND `so_order_status` != 101 ";
			$sql .= " AND ((`so_pay_type` IN (1, 3, 4) AND `so_order_status` IN (1, 4) ) OR (`so_pay_type` = 2 AND `so_order_status` IN (1, 3) ))";
			
			$stmt = $db->query($sql);
			$school_orders = $stmt->fetch(PDO::FETCH_ASSOC);

			// 本校生或者报名驾校的社会学员
			if($school_orders) {
				$school_id = $school_orders['so_school_id']; // 学员报名的驾校ID
				$shifts_id = $school_orders['so_shifts_id']; // 班制类型
				$so_pay_status = $school_orders['so_pay_type']; // 付款方式 1：支付宝 2：线下
				$so_order_status = $school_orders['so_order_status']; // 订单状态 // 支付宝情况下（1:已支付） 线下情况下：（1：报名成功未付款 3：报名成功已支付）
				
				// 获取班制等级
				$sql = "SELECT `sh_type`, `sh_title` FROM `cs_school_shifts` WHERE `id` = '{$shifts_id}'";
				$stmt = $db->query($sql);
				$school_shifts_info = $stmt->fetch(PDO::FETCH_ASSOC);
				$shifts_type = 2; // 1：计时班 2：普通班 3：VIP班
				$shifts_title = '普通班';

				if($school_shifts_info) {
					$shifts_type = $school_shifts_info['sh_type'];
					$shifts_title = $school_shifts_info['sh_title'];

				}
				// 获取当前驾校设置计时班免费时间
				$free_time = isset($school_config_info['free_time']) ? $school_config_info['free_time'] : 0;

				// 获取学员类型
				$sql = "SELECT `i_from` FROM `cs_user` WHERE `l_user_id` = $user_id AND `i_status` = 0 AND `i_user_type` = 0";
				$stmt = $db->query($sql);
				$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
				if($user_info) {
					$from = $user_info['i_from']; // 用户来源 2：线下
				} else {
					$data = array('code'=>-3, 'data'=>'您已被禁止预约学车');
					echo json_encode($data);
					exit();
				}

				// 通过coach_id获取教练详情
				$sql = "SELECT c.`car_type`, h.* FROM `cs_coach` as h LEFT JOIN `cs_cars` as c ON c.`id` = h.`s_coach_car_id` WHERE h.`l_coach_id` = $coach_id";
				$stmt = $db->query($sql);
				$coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
				
				if(empty($coach_info)) {
					$data = array('code'=>-44, 'data'=>'不存在当前教练');
					echo json_encode($data);
					exit();
				}

				if($coach_info) {
					$car_type = $coach_info['car_type']; // 车辆类型
					$l_bx_kaifang = $coach_info['l_bx_kaifang']; // 是否对本驾校开放(0：是 1：否)
					$l_shehui_kaifang = $coach_info['l_shehui_kaifang']; // 预约类型(0：对社会学员开放 1：不对社会学员开放)
					$l_notbx_kaifang = $coach_info['l_notbx_kaifang']; // 是否对非驾校开放(0：是 1：否)
					$s_school_name_id = $coach_info['s_school_name_id']; // 教练所在驾校ID

					$msg = '您当前预约学车没有免费项';
					// 获取优惠价格（预留）

					// 学员报名不同班制不同教练对应车型所产生的情况
					if($l_bx_kaifang == 0) {
						// 对本驾校开放
						if($l_shehui_kaifang == 0) {
							// 对社会学员开放
							if($l_notbx_kaifang == 0) {
								// 对外校开放
								if($from == 2) {
									// 线下学员录入（属于本驾校学员）
									if($s_school_name_id == $school_id) {
										// 本校线下
										if($so_pay_status == 1) {
											// 支付宝支付
											if($so_order_status == 1) {
												// 订单状态是已支付
												if($shifts_type == 3) {
													// VIP班学员
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约教练学车免费';
												} else if($shifts_type == 2 && $car_type == 1){
													// 普通班学员普通车型
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

												} else if($shifts_type == 1) {
													// 计时班（预约计时班为0 不免费） 
													if($free_time != 0) {
														if($service_time_count <= $free_time) {
															// 预约总时长小于等于设置的计时班的免费时间
															$total_time_price = 0;
															$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
														} else {
															$msg = '您报名的'.$shifts_title.'，免费时间用完';

														}
													} else {
														$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
													}
												}

											} else if($so_order_status == 2) {
												// 申请退款中
												// $data = array('code'=>-17, 'data'=>'您当前的报名驾校订单正在申请退款，不能预约学车');
												// echo json_encode($data);
												// exit();

											} else if($so_order_status == 3) {
												// 报名取消
												// $data = array('code'=>-18, 'data'=>'您当前的报名驾校订单已取消，不能预约学车');
												// echo json_encode($data);
												// exit();

											} else if($so_order_status == 4) {
												// 报名成功未付款
												$data = array('code'=>-17, 'data'=>'您当前的报名驾校订单未付款，不能预约学车');
												echo json_encode($data);
												exit();
											}
												
										} else if($so_pay_status == 2) {
											// 线下支付
											if($so_order_status == 1) {
												// 报名成功未支付
												$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
												echo json_encode($data);
												exit();

											} else if($so_order_status == 2) {
												// 报名驾校订单取消
												// $data = array('code'=>-20, 'data'=>'您报名驾校订单已取消，不能预约学车');
												// echo json_encode($data);
												// exit();

											} else if($so_order_status == 3) {
												// 报名成功已付款

												if($shifts_type == 3) {
													// VIP班学员
													$total_time_price = 0;

												} else if($shifts_type == 2 && $car_type == 1) {
													// 普通班学员普通车型
													$total_time_price = 0;
												}
											} else if($so_order_status == 4) {
												// 申请退款中

											}

										} else if($so_pay_status == 3) {
											// 微信支付
											if($so_order_status == 1) {
												// 报名成功已付款

												if($shifts_type == 3) {
													// VIP班学员
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约教练学车免费';
													
												} else if($shifts_type == 2 && $car_type == 1) {
													// 普通班学员普通车型
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';
													
												} else if($shifts_type == 1) {
													// 计时班（预约计时班为0 不免费） 
													if($free_time != 0) {
														if($service_time_count <= $free_time) {
															// 预约总时长小于等于设置的计时班的免费时间
															$total_time_price = 0;
															$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
														} else {
															$msg = '您报名的'.$shifts_title.'，免费时间用完';

														}
													} else {
														$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
													}
												}

											} else if($so_order_status == 2) {
												// 申请退款中

											} else if($so_order_status == 3) {
												// 报名取消

											} else if($so_order_status == 4) {
												// 报名成功未付款
												$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
												echo json_encode($data);
												exit();

											}

										} else if($so_pay_status == 4) {
											// 银联支付
											if($so_order_status == 1) {
												// 报名成功已付款

												if($shifts_type == 3) {
													// VIP班学员
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

												} else if($shifts_type == 2 && $car_type == 1) {
													// 普通班学员普通车型
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';
													
												} else if($shifts_type == 1) {
													// 计时班（预约计时班为0 不免费） 
													if($free_time != 0) {
														if($service_time_count <= $free_time) {
															// 预约总时长小于等于设置的计时班的免费时间
															$total_time_price = 0;
															$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
														} else {
															$msg = '您报名的'.$shifts_title.'，免费时间用完';

														}
													} else {
														$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
													}
												}

											} else if($so_order_status == 2) {
												// 申请退款中

											} else if($so_order_status == 3) {
												// 报名取消

											} else if($so_order_status == 4) {
												// 报名成功未付款
												$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
												echo json_encode($data);
												exit();

											}
										}
											
									}
								} else {
									// 社会学员
									if($s_school_name_id == $school_id) {
										if($so_pay_status == 1) {
											// 支付宝支付
											if($so_order_status == 1) {
												// 订单状态是已支付
												if($shifts_type == 3) {
													// VIP班学员
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

												} else if($shifts_type == 2 && $car_type == 1){
													// 普通班学员普通车型
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';
													
												} else if($shifts_type == 1) {
													// 计时班（预约计时班为0 不免费） 

													if($free_time != 0) {
														if($service_time_count <= $free_time) {
															// 预约总时长小于等于设置的计时班的免费时间
															$total_time_price = 0;
															$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
														} else {
															$msg = '您报名的'.$shifts_title.'，免费时间用完';

														}
													} else {
														$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
													}
												}
											} else if($so_order_status == 2) {
												// 申请退款中
												// $data = array('code'=>-17, 'data'=>'您当前的报名驾校订单正在申请退款，不能预约学车');
												// echo json_encode($data);
												// exit();

											} else if($so_order_status == 3) {
												// 报名取消
												// $data = array('code'=>-18, 'data'=>'您当前的报名驾校订单已取消，不能预约学车');
												// echo json_encode($data);
												// exit();

											} else if($so_order_status == 4) {
												// 报名成功未付款
												$data = array('code'=>-17, 'data'=>'您当前的报名驾校订单未付款，不能预约学车');
												echo json_encode($data);
												exit();
											}
												
										} else if($so_pay_status == 2) {
											// 线下支付
											if($so_order_status == 1) {
												// 报名成功未支付
												$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
												echo json_encode($data);
												exit();

											} else if($so_order_status == 2) {
												// 报名驾校订单取消
												// $data = array('code'=>-20, 'data'=>'您报名驾校订单已取消，不能预约学车');
												// echo json_encode($data);
												// exit();

											} else if($so_order_status == 3) {
												// 报名成功已付款

												if($shifts_type == 3) {
													// VIP班学员
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

												} else if($shifts_type == 2 && $car_type == 1) {
													// 普通班学员普通车型
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';
													
												} else if($shifts_type == 1) {
													// 计时班（预约计时班为0 不免费） 
													
													if($free_time != 0) {
														if($service_time_count <= $free_time) {
															// 预约总时长小于等于设置的计时班的免费时间
															$total_time_price = 0;
															$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
														} else {
															$msg = '您报名的'.$shifts_title.'，免费时间用完';

														}
													} else {
														$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
													}
												}
											} else if($so_order_status == 4) {
												// 申请退款中

											}

										} else if($so_pay_status == 3) {
											// 微信支付
											if($so_order_status == 1) {
												// 报名成功已付款

												if($shifts_type == 3) {
													// VIP班学员
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

												} else if($shifts_type == 2 && $car_type == 1) {
													// 普通班学员普通车型
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';
													
												} else if($shifts_type == 1) {
													// 计时班（预约计时班为0 不免费） 
													
													if($free_time != 0) {
														if($service_time_count <= $free_time) {
															// 预约总时长小于等于设置的计时班的免费时间
															$total_time_price = 0;
															$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
														} else {
															$msg = '您报名的'.$shifts_title.'，免费时间用完';

														}
													} else {
														$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
													}
												}

											} else if($so_order_status == 2) {
												// 申请退款中

											} else if($so_order_status == 3) {
												// 报名取消

											} else if($so_order_status == 4) {
												// 报名成功未付款
												$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
												echo json_encode($data);
												exit();

											}

										} else if($so_pay_status == 4) {
											// 银联支付
											if($so_order_status == 1) {
												// 报名成功已付款

												if($shifts_type == 3) {
													// VIP班学员
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

												} else if($shifts_type == 2 && $car_type == 1) {
													// 普通班学员普通车型
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';
													
												}else if($shifts_type == 1) {
													// 计时班（预约计时班为0 不免费） 
													
													if($free_time != 0) {
														if($service_time_count <= $free_time) {
															// 预约总时长小于等于设置的计时班的免费时间
															$total_time_price = 0;
															$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
														} else {
															$msg = '您报名的'.$shifts_title.'，免费时间用完';

														}
													} else {
														$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
													}
												}

											} else if($so_order_status == 2) {
												// 申请退款中

											} else if($so_order_status == 3) {
												// 报名取消

											} else if($so_order_status == 4) {
												// 报名成功未付款
												$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
												echo json_encode($data);
												exit();

											}
										}
									}

								}						
							} else {
								// 对本驾校开放，对社会学员开放，不对外校开放
		
								if($s_school_name_id != $school_id) {
									$data = array('code'=>-36, 'data'=>'您不是当前教练所在学校的学员，不能预约学车');  // 此教练不对外校开放
									echo json_encode($data);
									exit();
								}

								if($from == 2) {
									// 线下录入
									if($so_pay_status == 1) {
										// 支付宝支付
										if($so_order_status == 1) {
											// 订单状态是已支付
											if($shifts_type == 3) {
												// VIP班学员
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

											} else if($shifts_type == 2 && $car_type == 1){
												// 普通班学员普通车型
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';
											
											} else if($shifts_type == 1) {
												// 计时班（预约计时班为0 不免费） 
												
												if($free_time != 0) {
													if($service_time_count <= $free_time) {
														// 预约总时长小于等于设置的计时班的免费时间
														$total_time_price = 0;
														$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
													} else {
														$msg = '您报名的'.$shifts_title.'，免费时间用完';

													}
												} else {
													$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
												}
											}

										} else if($so_order_status == 2) {
											// 申请退款中
											// $data = array('code'=>-17, 'data'=>'您当前的报名驾校订单正在申请退款，不能预约学车');
											// echo json_encode($data);
											// exit();

										} else if($so_order_status == 3) {
											// 报名取消
											// $data = array('code'=>-18, 'data'=>'您当前的报名驾校订单已取消，不能预约学车');
											// echo json_encode($data);
											// exit();

										} else if($so_order_status == 4) {
											// 报名成功未付款
											$data = array('code'=>-17, 'data'=>'您当前的报名驾校订单未付款，不能预约学车');
											echo json_encode($data);
											exit();
										}
											
									} else if($so_pay_status == 2) {
										// 线下支付
										if($so_order_status == 1) {
											// 报名成功未支付
											$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
											echo json_encode($data);
											exit();

										} else if($so_order_status == 2) {
											// 报名驾校订单取消
											// $data = array('code'=>-20, 'data'=>'您报名驾校订单已取消，不能预约学车');
											// echo json_encode($data);
											// exit();

										} else if($so_order_status == 3) {
											// 报名成功已付款

											if($shifts_type == 3) {
												// VIP班学员
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

											} else if($shifts_type == 2 && $car_type == 1) {
												// 普通班学员普通车型
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

											}else if($shifts_type == 1) {
												// 计时班（预约计时班为0 不免费） 
												
												if($free_time != 0) {
													if($service_time_count <= $free_time) {
														// 预约总时长小于等于设置的计时班的免费时间
														$total_time_price = 0;
														$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
													} else {
														$msg = '您报名的'.$shifts_title.'，免费时间用完';

													}
												} else {
													$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
												}
											}
										} else if($so_order_status == 4) {
											// 申请退款中

										}

									} else if($so_pay_status == 3) {
										// 微信支付
										if($so_order_status == 1) {
											// 报名成功已付款

											if($shifts_type == 3) {
												// VIP班学员
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

											} else if($shifts_type == 2 && $car_type == 1) {
												// 普通班学员普通车型
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

											} else if($shifts_type == 1) {
												// 计时班（预约计时班为0 不免费） 
												
												if($free_time != 0) {
													if($service_time_count <= $free_time) {
														// 预约总时长小于等于设置的计时班的免费时间
														$total_time_price = 0;
														$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
													} else {
														$msg = '您报名的'.$shifts_title.'，免费时间用完';

													}
												} else {
													$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
												}
											}

										} else if($so_order_status == 2) {
											// 申请退款中

										} else if($so_order_status == 3) {
											// 报名取消

										} else if($so_order_status == 4) {
											// 报名成功未付款
											$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
											echo json_encode($data);
											exit();

										}

									} else if($so_pay_status == 4) {
										// 银联支付
										if($so_order_status == 1) {
											// 报名成功已付款

											if($shifts_type == 3) {
												// VIP班学员
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

											} else if($shifts_type == 2 && $car_type == 1) {
												// 普通班学员普通车型
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

											} else if($shifts_type == 1) {
												// 计时班（预约计时班为0 不免费） 
												
												if($free_time != 0) {
													if($service_time_count <= $free_time) {
														// 预约总时长小于等于设置的计时班的免费时间
														$total_time_price = 0;
														$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
													} else {
														$msg = '您报名的'.$shifts_title.'，免费时间用完';

													}
												} else {
													$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
												}
											}

										} else if($so_order_status == 2) {
											// 申请退款中

										} else if($so_order_status == 3) {
											// 报名取消

										} else if($so_order_status == 4) {
											// 报名成功未付款
											$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
											echo json_encode($data);
											exit();

										}
									}
										
								} else {
									// 外校的学员

										// 报名了当前教练对应的驾校
									if($s_school_name_id == $school_id) {
										if($so_pay_status == 1) {
											// 支付宝支付
											if($so_order_status == 1) {
												// 订单状态是已支付
												if($shifts_type == 3) {
													// VIP班学员
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

												} else if($shifts_type == 2 && $car_type == 1){
													// 普通班学员普通车型
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

												} else if($shifts_type == 1) {
													// 计时班（预约计时班为0 不免费） 
													
													if($free_time != 0) {
														if($service_time_count <= $free_time) {
															// 预约总时长小于等于设置的计时班的免费时间
															$total_time_price = 0;
															$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
														} else {
															$msg = '您报名的'.$shifts_title.'，免费时间用完';

														}
													} else {
														$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
													}
												}
											} else if($so_order_status == 2) {
												// 申请退款中
												// $data = array('code'=>-17, 'data'=>'您当前的报名驾校订单正在申请退款，不能预约学车');
												// echo json_encode($data);
												// exit();

											} else if($so_order_status == 3) {
												// 报名取消
												// $data = array('code'=>-18, 'data'=>'您当前的报名驾校订单已取消，不能预约学车');
												// echo json_encode($data);
												// exit();

											} else if($so_order_status == 4) {
												// 报名成功未付款
												$data = array('code'=>-17, 'data'=>'您当前的报名驾校订单未付款，不能预约学车');
												echo json_encode($data);
												exit();
											}
												
										} else if($so_pay_status == 2) {
											// 线下支付
											if($so_order_status == 1) {
												// 报名成功未支付
												$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
												echo json_encode($data);
												exit();

											} else if($so_order_status == 2) {
												// 报名驾校订单取消
												// $data = array('code'=>-20, 'data'=>'您报名驾校订单已取消，不能预约学车');
												// echo json_encode($data);
												// exit();

											} else if($so_order_status == 3) {
												// 报名成功已付款

												if($shifts_type == 3) {
													// VIP班学员
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

												} else if($shifts_type == 2 && $car_type == 1) {
													// 普通班学员普通车型
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

												} else if($shifts_type == 1) {
													// 计时班（预约计时班为0 不免费） 
													
													if($free_time != 0) {
														if($service_time_count <= $free_time) {
															// 预约总时长小于等于设置的计时班的免费时间
															$total_time_price = 0;
															$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
														} else {
															$msg = '您报名的'.$shifts_title.'，免费时间用完';

														}
													} else {
														$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
													}
												}
											} else if($so_order_status == 4) {
												// 申请退款中

											}

										} else if($so_pay_status == 3) {
											// 微信支付
											if($so_order_status == 1) {
												// 报名成功已付款

												if($shifts_type == 3) {
													// VIP班学员
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

												} else if($shifts_type == 2 && $car_type == 1) {
													// 普通班学员普通车型
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

												} else if($shifts_type == 1) {
													// 计时班（预约计时班为0 不免费） 
													
													if($free_time != 0) {
														if($service_time_count <= $free_time) {
															// 预约总时长小于等于设置的计时班的免费时间
															$total_time_price = 0;
															$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
														} else {
															$msg = '您报名的'.$shifts_title.'，免费时间用完';

														}
													} else {
														$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
													}
												}

											} else if($so_order_status == 2) {
												// 申请退款中

											} else if($so_order_status == 3) {
												// 报名取消

											} else if($so_order_status == 4) {
												// 报名成功未付款
												$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
												echo json_encode($data);
												exit();

											}

										} else if($so_pay_status == 4) {
											// 银联支付
											if($so_order_status == 1) {
												// 报名成功已付款

												if($shifts_type == 3) {
													// VIP班学员
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

												} else if($shifts_type == 2 && $car_type == 1) {
													// 普通班学员普通车型
													$total_time_price = 0;
													$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

												} else if($shifts_type == 1) {
													// 计时班（预约计时班为0 不免费） 
													
													if($free_time != 0) {
														if($service_time_count <= $free_time) {
															// 预约总时长小于等于设置的计时班的免费时间
															$total_time_price = 0;
															$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
														} else {
															$msg = '您报名的'.$shifts_title.'，免费时间用完';

														}
													} else {
														$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
													}
												}

											} else if($so_order_status == 2) {
												// 申请退款中

											} else if($so_order_status == 3) {
												// 报名取消

											} else if($so_order_status == 4) {
												// 报名成功未付款
												$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
												echo json_encode($data);
												exit();

											}
										}
									}
	
								}
							}
						} else {
							// 对本驾校开放，不对社会学员开放

							if($l_notbx_kaifang == 1) {
								// 对外校开放
								if($from == 2) {
									// 线下录入
									if($so_pay_status == 1) {
										// 支付宝支付
										if($so_order_status == 1) {
											// 订单状态是已支付
											if($shifts_type == 3) {
												// VIP班学员
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

											} else if($shifts_type == 2 && $car_type == 1){
												// 普通班学员普通车型
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

											} else if($shifts_type == 1) {
												// 计时班（预约计时班为0 不免费） 
												
												if($free_time != 0) {
													if($service_time_count <= $free_time) {
														// 预约总时长小于等于设置的计时班的免费时间
														$total_time_price = 0;
														$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
													} else {
														$msg = '您报名的'.$shifts_title.'，免费时间用完';

													}
												} else {
													$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
												}
											}

										} else if($so_order_status == 2) {
											// 申请退款中
											// $data = array('code'=>-17, 'data'=>'您当前的报名驾校订单正在申请退款，不能预约学车');
											// echo json_encode($data);
											// exit();

										} else if($so_order_status == 3) {
											// 报名取消
											// $data = array('code'=>-18, 'data'=>'您当前的报名驾校订单已取消，不能预约学车');
											// echo json_encode($data);
											// exit();

										} else if($so_order_status == 4) {
											// 报名成功未付款
											$data = array('code'=>-17, 'data'=>'您当前的报名驾校订单未付款，不能预约学车');
											echo json_encode($data);
											exit();
										}
											
									} else if($so_pay_status == 2) {
										// 线下支付
										if($so_order_status == 1) {
											// 报名成功未支付
											$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
											echo json_encode($data);
											exit();

										} else if($so_order_status == 2) {
											// 报名驾校订单取消
											// $data = array('code'=>-20, 'data'=>'您报名驾校订单已取消，不能预约学车');
											// echo json_encode($data);
											// exit();

										} else if($so_order_status == 3) {
											// 报名成功已付款

											if($shifts_type == 3) {
												// VIP班学员
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

											} else if($shifts_type == 2 && $car_type == 1) {
												// 普通班学员普通车型
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

											} else if($shifts_type == 1) {
												// 计时班（预约计时班为0 不免费） 
												
												if($free_time != 0) {
													if($service_time_count <= $free_time) {
														// 预约总时长小于等于设置的计时班的免费时间
														$total_time_price = 0;
														$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
													} else {
														$msg = '您报名的'.$shifts_title.'，免费时间用完';

													}
												} else {
													$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
												}
											}

										} else if($so_order_status == 4) {
											// 申请退款中

										}

									} else if($so_pay_status == 3) {
										// 微信支付
										if($so_order_status == 1) {
											// 报名成功已付款

											if($shifts_type == 3) {
												// VIP班学员
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

											} else if($shifts_type == 2 && $car_type == 1) {
												// 普通班学员普通车型
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

											} else if($shifts_type == 1) {
												// 计时班（预约计时班为0 不免费） 
												
												if($free_time != 0) {
													if($service_time_count <= $free_time) {
														// 预约总时长小于等于设置的计时班的免费时间
														$total_time_price = 0;
														$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
													} else {
														$msg = '您报名的'.$shifts_title.'，免费时间用完';

													}
												} else {
													$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
												}
											}

										} else if($so_order_status == 2) {
											// 申请退款中

										} else if($so_order_status == 3) {
											// 报名取消

										} else if($so_order_status == 4) {
											// 报名成功未付款
											$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
											echo json_encode($data);
											exit();

										}

									} else if($so_pay_status == 4) {
										// 银联支付
										if($so_order_status == 1) {
											// 报名成功已付款

											if($shifts_type == 3) {
												// VIP班学员
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

											} else if($shifts_type == 2 && $car_type == 1) {
												// 普通班学员普通车型
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

											} else if($shifts_type == 1) {
												// 计时班（预约计时班为0 不免费） 
												
												if($free_time != 0) {
													if($service_time_count <= $free_time) {
														// 预约总时长小于等于设置的计时班的免费时间
														$total_time_price = 0;
														$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
													} else {
														$msg = '您报名的'.$shifts_title.'，免费时间用完';

													}
												} else {
													$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
												}
											}

										} else if($so_order_status == 2) {
											// 申请退款中

										} else if($so_order_status == 3) {
											// 报名取消

										} else if($so_order_status == 4) {
											// 报名成功未付款
											$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
											echo json_encode($data);
											exit();

										}
									}
										
								} else {
									// 社会学员
									if($s_school_name_id == $school_id) {

										$data = array('code'=>-5, 'data'=>'此教练不对社会学员开放');
										echo json_encode($data);
										exit();
									}
								}

							} else {
								// 不对外校开放
								if($s_school_name_id != $school_id) {
									$data = array('code'=>-36, 'data'=>'您不是当前教练所在学校的学员不能预约学车');
									echo json_encode($data);
									exit();
								}

								if($from == 2) {
									// 线下录入
									if($so_pay_status == 1) {
										// 支付宝支付
										if($so_order_status == 1) {
											// 订单状态是已支付
											if($shifts_type == 3) {
												// VIP班学员
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

											} else if($shifts_type == 2 && $car_type == 1){
												// 普通班学员普通车型
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

											} else if($shifts_type == 1) {
												// 计时班（预约计时班为0 不免费） 
												
												if($free_time != 0) {
													if($service_time_count <= $free_time) {
														// 预约总时长小于等于设置的计时班的免费时间
														$total_time_price = 0;
														$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
													} else {
														$msg = '您报名的'.$shifts_title.'，免费时间用完';

													}
												} else {
													$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
												}
											}

										} else if($so_order_status == 2) {
											// 申请退款中
											// $data = array('code'=>-17, 'data'=>'您当前的报名驾校订单正在申请退款，不能预约学车');
											// echo json_encode($data);
											// exit();

										} else if($so_order_status == 3) {
											// 报名取消
											// $data = array('code'=>-18, 'data'=>'您当前的报名驾校订单已取消，不能预约学车');
											// echo json_encode($data);
											// exit();

										} else if($so_order_status == 4) {
											// 报名成功未付款
											$data = array('code'=>-17, 'data'=>'您当前的报名驾校订单未付款，不能预约学车');
											echo json_encode($data);
											exit();
										}
											
									} else if($so_pay_status == 2) {
										// 线下支付
										if($so_order_status == 1) {
											// 报名成功未支付
											$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
											echo json_encode($data);
											exit();

										} else if($so_order_status == 2) {
											// 报名驾校订单取消
											// $data = array('code'=>-20, 'data'=>'您报名驾校订单已取消，不能预约学车');
											// echo json_encode($data);
											// exit();

										} else if($so_order_status == 3) {
											// 报名成功已付款

											if($shifts_type == 3) {
												// VIP班学员
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

											} else if($shifts_type == 2 && $car_type == 1) {
												// 普通班学员普通车型
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

											} else if($shifts_type == 1) {
												// 计时班（预约计时班为0 不免费） 
												
												if($free_time != 0) {
													if($service_time_count <= $free_time) {
														// 预约总时长小于等于设置的计时班的免费时间
														$total_time_price = 0;
														$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
													} else {
														$msg = '您报名的'.$shifts_title.'，免费时间用完';

													}
												} else {
													$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
												}
											}

										} else if($so_order_status == 4) {
											// 申请退款中

										}

									} else if($so_pay_status == 3) {
										// 微信支付
										if($so_order_status == 1) {
											// 报名成功已付款

											if($shifts_type == 3) {
												// VIP班学员
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

											} else if($shifts_type == 2 && $car_type == 1) {
												// 普通班学员普通车型
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

											} else if($shifts_type == 1) {
												// 计时班（预约计时班为0 不免费） 
												
												if($free_time != 0) {
													if($service_time_count <= $free_time) {
														// 预约总时长小于等于设置的计时班的免费时间
														$total_time_price = 0;
														$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
													} else {
														$msg = '您报名的'.$shifts_title.'，免费时间用完';

													}
												} else {
													$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
												}
											}

										} else if($so_order_status == 2) {
											// 申请退款中

										} else if($so_order_status == 3) {
											// 报名取消

										} else if($so_order_status == 4) {
											// 报名成功未付款
											$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
											echo json_encode($data);
											exit();

										}

									} else if($so_pay_status == 4) {
										// 银联支付
										if($so_order_status == 1) {
											// 报名成功已付款

											if($shifts_type == 3) {
												// VIP班学员
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约教练学车免费';

											} else if($shifts_type == 2 && $car_type == 1) {
												// 普通班学员普通车型
												$total_time_price = 0;
												$msg = '您报名的'.$shifts_title.'，预约当前教练绑定的普通车型学车免费';

											} else if($shifts_type == 1) {
												// 计时班（预约计时班为0 不免费） 
												
												if($free_time != 0) {
													if($service_time_count <= $free_time) {
														// 预约总时长小于等于设置的计时班的免费时间
														$total_time_price = 0;
														$msg = '您报名的'.$shifts_title.'，预约教练学车可免费'.$free_time.'小时，您已预约当前教练'.$service_time_count.'小时';
													} else {
														$msg = '您报名的'.$shifts_title.'，免费时间用完';

													}
												} else {
													$msg = '您报名的'.$shifts_title.'，预约教练学车不免费';
												}
											}

										} else if($so_order_status == 2) {
											// 申请退款中

										} else if($so_order_status == 3) {
											// 报名取消

										} else if($so_order_status == 4) {
											// 报名成功未付款
											$data = array('code'=>-19, 'data'=>'您报名驾校订单未付款，不能预约学车');
											echo json_encode($data);
											exit();

										}
									}
										
								} else {
									// 社会学员
									$data = array('code'=>'-6', 'data'=>'此教练不对社会学员开放且不对外校开放');
									echo json_encode($data);
									exit();
								}
							}
						}
							
					} else {
						// 不对本驾校开放

						if($s_school_name_id == $school_id) {
							$data = array('code'=>'-43', 'data'=>'此教练不对本驾校学员开放，请预约其他教练');
							echo json_encode($data);
							exit();
						}
						if($l_shehui_kaifang == 0) {
							// 对社会学员开放
							if($l_notbx_kaifang == 0) {
								// 对外校开放
								if($from == 2) {
									// 线下
									if($s_school_name_id == $school_id) {
										// 本驾校学员线下
										$data = array('code'=>-4, 'data'=>'此教练不对本驾校学员开放');
										echo json_encode($data);
										exit();
									}	
								}

							} else {
								// 不对外校开放
								if($from == 2) {
									// 线下
									if($s_school_name_id == $school_id) {
										// 本校学员线下录入的
										$data = array('code'=>-7, 'data'=>'此教练不对本驾校学员且不对外校开放');
										echo json_encode($data);
										exit();
									}
										
								}
							}
						} else {
							// 不对社会学员开放
							if($l_notbx_kaifang == 0) {
								// 对外校开放
								if($from == 2) {
									// 线下
									if($s_school_name_id == $school_id) {
										// 本校学员线下录入的
										$data = array('code'=>-8, 'data'=>"此教练不对本驾校学员开放");
										echo json_encode($data);
										exit();
									}
								}
							} else {
								// 不对外校开放
								$data = array('code'=>-9, 'data'=>'此教练不对本驾校开放且不对社会学员开放且不对外校开放');
								echo json_encode($data);
								exit();
							}
						}

					}
					$arr = array('msg'=>$msg,'final_price'=>$total_time_price, 'discount'=>0);
					$data = array('code'=>200, 'data'=>$arr);
				} else {
					$data = array('code'=>-2, 'data'=>'参数错误');
				}
				echo json_encode($data);

			} else {
				// 没有报名驾校

				// // 获取时间段总价
				// $sql = "SELECT `time_config_money_id` FROM `cs_current_coach_time_configuration` WHERE `coach_id` = '{$coach_id}' AND `year` = '{$year}' AND `month` = '{$month}' AND `day` = '{$day}'";
				// $stmt = $db->query($sql);
				// $time_config_money_id_arr = $stmt->fetch(PDO::FETCH_ASSOC);
				// $time_config_money_id = array();
				// $total_time_price = 0;
				// if($time_config_money_id_arr) {
				// 	$time_config_money_id = json_decode($time_config_money_id_arr['time_config_money_id']);
				// 	// print_r($time_config_money_id);
				// 	foreach ($time_config_money_id as $key => $value) {
				// 		if(in_array($key, $time_config_id_arr)) {
				// 			$total_time_price += $value;
				// 		}
				// 	}
				// } else {
				// 	$sql = "SELECT `price`, `id` FROM `cs_coach_time_config`";
				// 	$stmt = $db->query($sql);
				// 	$coach_time_config_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
				// 	if($coach_time_config_arr) {
				// 		foreach ($coach_time_config_arr as $key => $value) {
				// 			if(in_array($value['id'], $time_config_id_arr)) {
				// 				$total_time_price += $value['price'];
				// 			}
				// 		}
				// 	}
				// }
				// echo $total_time_price;
				// $sql = "SELECT t.`price` FROM `cs_coach_time_config` as t LEFT JOIN `cs_coach` as c ON c.`s_school_name_id` = t.`school_id` WHERE c.`l_coach_id` = $coach_id AND t.`id` IN (".implode(',', $time_config_id_arr).")";
				// $stmt = $db->query($sql);
				// $coach_time_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
				// if($coach_time_info) {
				// 	foreach ($coach_time_info as $key => $value) {
				// 		$total_time_price += $value['price'];
				// 	}
				// }
				$arr = array('msg'=>'没有报名驾校的学员没有免费项','final_price'=>$total_time_price, 'discount'=>0);
				$data = array('code'=>200, 'data'=>$arr);
				echo json_encode($data);
			}

		}catch(PDOException $e) {
			setapilog('order_check:params[user_id:'.$user_id.',coach_id:'.$coach_id.',time_config_id:'.$time_config_id.',date:'.$date.',coupon_id:'.$coupon_id.',param_1:'.$param_1.',param_2:'.$param_2.',param_3:'.$param_3.',param_4:'.$param_4.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
		}
	}

?>
