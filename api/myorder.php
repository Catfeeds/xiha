<?php  
	/**
	 * 获取我报名驾校的订单
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
	$app->post('/','getmyOrder');
	$app->run();

	// 获取教练学员信息
	function getmyOrder() {
		Global $app, $crypt;

		$request = $app->request();
		// if(!isset($_SESSION['loginauth'])) {
		// 	$data = array('code'=>-2, 'data'=>'请先登录');
		// 	echo json_encode($data);
		// 	exit();
		// }
		// $loginauth = $crypt->decrypt($_SESSION['loginauth']);
		// $loginauth_arr = explode('\t', $loginauth);

		// $user_id = $loginauth_arr[0];
		// $uid = $request->params('uid') == '' ? $user_id : $request->params('uid');
		
		$uid = $request->params('uid');

		if(!$uid) {
			$data = array('code'=>-2, 'data'=>'请重新登陆');
			echo json_encode($data);
			exit();
		}

		try {
			$db = getConnection();
			// $sql = "SELECT * FROM `cs_school_orders` WHERE `so_user_id` = $uid AND `so_order_status` != 101 AND `so_order_status` != 1 ORDER BY `addtime` DESC";
			$sql = "SELECT * FROM `cs_school_orders` WHERE `so_user_id` = $uid AND `so_order_status` != 101 AND ((`so_pay_type` IN (1,3,4) AND `so_order_status` != 4) OR (`so_pay_type` = 2 ) ) ORDER BY `addtime` DESC";
			$stmt = $db->query($sql);
			$order_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if(empty($order_list)) {
				$data = array('code'=>200, 'data'=>array());
				echo json_encode($data);
				exit();
			}

			foreach ($order_list as $key => $value) {
				// 获取学校相关
				$sql = "SELECT `l_school_id`, `s_thumb`, `s_school_name`, `city_id`, `s_address` FROM `cs_school` WHERE `l_school_id` = {$value['so_school_id']}";
				$stmt = $db->query($sql);
				$school_info = $stmt->fetch(PDO::FETCH_ASSOC);
				if($school_info) {
					$order_list[$key]['l_school_id'] = $school_info['l_school_id'];
					$order_list[$key]['s_address'] = $school_info['s_address'];

					if($school_info['s_thumb']) {
						if(file_exists(__DIR__.'/../sadmin/'.$school_info['s_thumb'])) {
							$order_list[$key]['s_thumb'] = S_HTTP_HOST.$school_info['s_thumb'];
						} else {
							$order_list[$key]['s_thumb'] = HTTP_HOST.$school_info['s_thumb'];
						}
					} else {
						$order_list[$key]['s_thumb'] = 'images/school_thumb.jpg';
					}
					$order_list[$key]['s_school_name'] = $school_info['s_school_name'];

					// 根据城市ID获取城市名
					$sql = "SELECT `city` FROM `cs_city` WHERE `cityid` = ".$school_info['city_id'];
					$stmt = $db->query($sql);
					$city_info = $stmt->fetch(PDO::FETCH_ASSOC);

					if($city_info) {
						$order_list[$key]['city'] = $city_info['city'];
					} else {
						$order_list[$key]['city'] = '合肥';
					}
					
					// 获取学员信息
					// $sql = "SELECT `s_username`, `s_phone` FROM `cs_user` WHERE `l_user_id` = $uid";
					// $stmt = $db->query($sql);
					// $user_info = $stmt->fetch(PDO::FETCH_ASSOC);

					// if($user_info) {
					// 	$order_list[$key]['s_username'] = $user_info['s_username'];
					// 	$order_list[$key]['s_phone'] = $user_info['s_phone'];
					// } else {
					// 	$order_list[$key]['s_username'] = '';
					// 	$order_list[$key]['s_phone'] = '';
					// }
						
					// $order_list[$key]['s_username'] = $loginauth_arr[2];
					// $order_list[$key]['s_phone'] = $loginauth_arr[3];
					
					// 获取班制
					$sql = "SELECT `sh_title` FROM `cs_school_shifts` WHERE `id` = ".$value['so_shifts_id'];
					$stmt = $db->query($sql);
					$shifts_info = $stmt->fetch(PDO::FETCH_ASSOC);
					if($shifts_info) {
						$order_list[$key]['sh_title'] = $shifts_info['sh_title'];
					} else {
						$order_list[$key]['sh_title'] = '未知';
					}

					// 获取培训地点的电话
					$sql = "SELECT `tl_phone` FROM `cs_school_train_location` WHERE `tl_school_id` = '{$school_info['l_school_id']}' ORDER BY `addtime` DESC LIMIT 1";
					$stmt = $db->query($sql);
					$train_location_info = $stmt->fetch(PDO::FETCH_ASSOC);

					if($train_location_info) {
						$order_list[$key]['so_phone'] = $train_location_info['tl_phone'];

					} else {
						$order_list[$key]['so_phone'] = '';

					}
					
					// 获取支付方式
					// 支付宝方式
					if($value['so_pay_type'] == 1) {
						if($value['so_order_status'] == 1) {
							$order_list[$key]['order_status'] = '报名成功已付款'; // 已支付

						} else if($value['so_order_status'] == 2) {
							$order_list[$key]['order_status'] = '申请退款中';

						} else if($value['so_order_status'] == 3) {
							$order_list[$key]['order_status'] = '报名取消';

						} else if($value['so_order_status'] == 4) {
							$order_list[$key]['order_status'] = '报名成功未付款'; // 未支付

						}
						$order_list[$key]['so_pay_method'] = '支付宝';

					// 线下
					} elseif ($value['so_pay_type'] == 2) {
						if($value['so_order_status'] == 1) {
							$order_list[$key]['order_status'] = '报名成功未支付';

						} else if($value['so_order_status'] == 2) {
							$order_list[$key]['order_status'] = '报名取消';

						} else if($value['so_order_status'] == 3) {
							$order_list[$key]['order_status'] = '报名成功已支付';

						} else if($value['so_order_status'] == 4) {
							$order_list[$key]['order_status'] = '申请退款中';
						}

						$order_list[$key]['so_pay_method'] = '线下支付';
						
					} elseif ($value['so_pay_type'] == 3) {
						if($value['so_order_status'] == 1) {
							$order_list[$key]['order_status'] = '报名成功已付款'; // 已支付

						} else if($value['so_order_status'] == 2) {
							$order_list[$key]['order_status'] = '申请退款中';

						} else if($value['so_order_status'] == 3) {
							$order_list[$key]['order_status'] = '报名取消';

						} else if($value['so_order_status'] == 4) {
							$order_list[$key]['order_status'] = '报名成功未付款'; // 未支付

						}
						$order_list[$key]['so_pay_method'] = '微信';
						
					} elseif ($value['so_pay_type'] == 4) {
						if($value['so_order_status'] == 1) {
							$order_list[$key]['order_status'] = '报名成功已付款'; // 已支付

						} else if($value['so_order_status'] == 2) {
							$order_list[$key]['order_status'] = '申请退款中';

						} else if($value['so_order_status'] == 3) {
							$order_list[$key]['order_status'] = '报名取消';

						} else if($value['so_order_status'] == 4) {
							$order_list[$key]['order_status'] = '报名成功未付款'; // 未支付

						}
						$order_list[$key]['so_pay_method'] = '银联';
						
					} 
					$order_list[$key]['so_pay_type'] = $value['so_pay_type'];
					$order_list[$key]['so_comment_status'] = $value['so_comment_status'];

				} else {
					$order_list[$key]['l_school_id'] = '';
					$order_list[$key]['s_address'] = '';
					$order_list[$key]['s_thumb'] = 'images/school_thumb.jpg';
					$order_list[$key]['s_school_name'] = '';
					$order_list[$key]['city'] = '';
					// $order_list[$key]['s_username'] = '';
					// $order_list[$key]['s_phone'] = '';
					$order_list[$key]['order_status'] = '无效报名';
				}
				$order_list[$key]['so_order_no'] = $value['so_order_no'];
				$order_list[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);	

				// 获取评价内容
				$sql = "SELECT `school_star`, `school_content` FROM `cs_coach_comment` WHERE `order_no` = '".$value['so_order_no']."' AND `school_id` = ".$value['so_school_id'];
				$stmt = $db->query($sql);
				$comment_detail = $stmt->fetch(PDO::FETCH_ASSOC);
				$order_list[$key]['school_content'] = $comment_detail['school_content'] == null ? '系统默认好评' : $comment_detail['school_content'];
				$order_list[$key]['school_star'] 	= $comment_detail['school_star'] == null ? 3 : $comment_detail['school_star'];
				$order_list[$key]['school_star_format']	= $comment_detail['school_star'] == null ? 3 : intval($comment_detail['school_star']);
			}

			$data = array('code'=>200, 'data'=>$order_list);
			// echo $crypt->encrypt(json_encode($data));
			echo json_encode($data);

		} catch(PDOException $e) {
			setapilog('myorder:params[uid:'.$uid.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}

	}

?>