<?php 

	/**
	 * 申请取消预约学车的订单
	 * @param $order_no 订单号 
	 * @param $order_id 订单ID
	 * @param $user_id 学员ID
	 * @param $cancel_reason 内容
	 * @param $cancel_type 1：学员取消 2：驾校取消
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author sunweiwei
	 **/
 	require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/functions.php';
    require '../../include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','cancelSignup');
 	$app->response->headers->set('Content-Type', 'application/json;charset=utf8');
	$app->run();

	//取消我的报名驾校
	function cancelSignup() {
		Global $app, $crypt;
		$r = $app->request();
		 //验证请求方式 POST
        if ( !$r->isPost() ) {
            setapilog('[send_appoint_orders] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            echo json_encode(array('code' => 106, 'data' => '请求错误')) ;
            return;
        }
        //验证输入参数    
         $validate_ok = validate(
            array(
                'order_no'   => 'STRING',
                'order_id'   => 'INT',
                'user_id'    => 'INT',
                'content' => 'STRING',
                'type'   => 'INT',
             ),
            $r->params()
        );
        if ( !$validate_ok['pass'] ) {
            echo json_encode($validate_ok['data']) ;
         	return;
        }
        //获取参数
        $order_no = $r->params('order_no');
		$order_id = $r->params('order_id');
		$user_id = $r->params('user_id');
		$content = $r->params('content');
		$type = $r->params('type');
		try {
			//判断当前用户是否存在此订单
			$db = getConnection();
			$sql = "SELECT `so_pay_type`, `so_phone`, `so_order_status`, `cancel_type` FROM `cs_school_orders` ";
			$sql .= " WHERE `so_user_id` = :user_id AND `so_order_no` = :order_no AND `id` = :order_id ";
			$stmt = $db->prepare($sql);
			$stmt->bindParam('user_id', $user_id);
			$stmt->bindParam('order_no', $order_no);
			$stmt->bindParam('order_id', $order_id);
			$stmt->execute();
			$order_info = $stmt->fetch(PDO::FETCH_ASSOC);
			if (empty($order_info)) {
				$data = array('code'=>104, 'data'=>'该用户不存在此报名订单');
				echo json_encode($data);
				return;
			}
			//判断订单是否已取消或者申请退款中 
			if (($order_info['so_pay_type'] == 2 && $order_info['so_order_status'] == 2) || ($order_info['so_pay_type'] != 2 && $order_info['so_order_status'] == 3)  ) {
				if($order_info['cancel_type'] == 1) {
					$data = array('code'=>109, 'data'=>'您已取消订单');
				} elseif($order_info['cancel_type'] == 2) {
					$data = array('code'=>109, 'data'=>'您的订单已被驾校取消');
				}
				echo json_encode($data);
				return;
			}
			if (($order_info['so_pay_type'] == 2 && $order_info['so_order_status'] == 4) || ($order_info['so_pay_type'] != 2 && $order_info['so_order_status'] == 2)  ) {
				if($order_info['cancel_type'] == 1) {
					$data = array('code'=>109, 'data'=>'您的申请取消订单已被受理，请耐心等待');
				} elseif($order_info['cancel_type'] == 2) {
					$data = array('code'=>109, 'data'=>'您的订单已被驾校申请取消，请联系驾校');
				}
				echo json_encode($data);
				return;
			}
			//更改订单状态为“申请退款中（线上2 线下4）”
			if ($order_info['so_pay_type'] == 2) {
				$order_status = 1; // 报名成功未付款

				// 报名成功未付款
				if($order_info['so_order_status'] == 1) {
					$order_status = 2;

				// 报名成功已付款
				} else if($order_info['so_order_status'] == 3) {
					$order_status = 4;

				}
			} else {
				$order_status = 4; // 报名成功未付款
				// 报名成功未付款
				if($order_info['so_order_status'] == 4) {
					$order_status = 3;

				// 报名成功已付款
				} else if($order_info['so_order_status'] == 1) {
					$order_status = 2;

				}
			}
			$ctime = time();
			$sql = "UPDATE `cs_school_orders` SET `so_order_status` = :order_status, `cancel_reason` = :content, `cancel_time` = :ctime, `cancel_type` = :type WHERE `id` = :order_id";
			 $stmt = $db->prepare($sql);
            $stmt->bindParam('type', $type);
            $stmt->bindParam('content', $content);
            $stmt->bindParam('ctime', $ctime);
            $stmt->bindParam('order_status', $order_status);
            $stmt->bindParam('order_id', $order_id);
            $res = $stmt->execute();

			if ($res) {
				$data = array('code'=>200, 'data'=>'申请取消订单成功，请等待审核');
			} else {
				$data = array('code'=>400, 'data'=>'申请取消失败');
			}
			$db = null;
			echo json_encode($data);
			//消息推送	
			// 学员端取消
				if($type == 1) {
					// 推送消息给学员
					$params_student = array(
						'user_phone'=>$order_info['so_phone'],
						'member_id'=>$user_id,
						'member_type'=>1,// 1：学员 2：教练
						's_beizhu'=>'学员订单取消',
						'i_yw_type'=>2, // 1:通知 2：正常订单消息
						'title'=>'报名驾校订单申请取消',
						'content'=>'您好！您的报名驾校订单：'.$order_no.'申请取消已受理，请您耐心等待处理结果。',
						'type'=>1 // 2:教练端推送 1：学员端推送
					);
					$res = request_post(SHOST.'api/message_push.php', $params_student);

				// 驾校取消
				} else {
					// 推送消息给学员
					$params_student = array(
						'user_phone'=>$order_info['so_phone'],
						'member_id'=>$user_id,
						'member_type'=>1,// 1：学员 2：教练
						's_beizhu'=>'学员订单取消',
						'i_yw_type'=>2, // 1:通知 2：正常订单消息
						'title'=>'报名驾校订单申请取消',
						'content'=>'您好！您的报名驾校订单：'.$order_no.'已被驾校申请取消，请您及时调整您的出行计划。',
						'type'=>1 // 2:教练端推送 1：学员端推送
					);
					$res = request_post(SHOST.'api/message_push.php', $params_student);
				}

			return;
		} catch(PDOException $e) {
			setapilog('cancel_orders:params[user_id:'.$user_id.',order_no:'.$order_no.',order_id:'.$order_id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			return;
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