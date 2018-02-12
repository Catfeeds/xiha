<?php  
	/**
	 * 学员评价驾校
	 * @param $id int 订单ID
	 * @param $school_star int 学校星级
	 * @param $school_content string 驾校主观评价
	 * @param $user_id int 用户ID
	 * @param $order_no int 订单号
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi （1：未评价 2：已评价）
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','comment');
	$app->run();

	// 学员评价教练
	function comment() {
		Global $app, $crypt;
		$request = $app->request();
		$id = $request->params('id');
		$sid = $request->params('sid');
		$school_star = $request->params('school_star');
		$school_content = $request->params('comment_content');
		$user_id = $request->params('uid');
		$order_no = $request->params('no');

		if(empty($order_no) || empty($user_id) || empty($id) || empty($sid)) {
			$data = array('code'=>-3, 'data'=>'参数错误');
			echo json_encode($data);
			exit();
		}
		$db = getConnection();
		// 判断是否存在这个订单
		// $sql = "SELECT * FROM `cs_school_orders` WHERE `id` = $id  AND `so_order_status` != 101 AND `so_school_id` = $sid AND `so_user_id` = $user_id AND `so_order_no` = '".$order_no."' AND ((`so_pay_type` = 1 AND `so_order_status` = 1) OR (`so_pay_type` = 2 AND `so_order_status` = 3))";
		$sql = "SELECT * FROM `cs_school_orders` ";
		$sql .= " WHERE `id` = $id ";
		$sql .= " AND `so_order_status` != 101 AND `so_school_id` = $sid AND `so_user_id` = $user_id AND `so_order_no` = '{$order_no}' ";
		$sql .= " AND (`so_pay_type` IN (1,3,4) AND `so_order_status` = 1) ";
		$sql .= " OR (`so_pay_type` = 2 AND `so_order_status` = 3) ";
		$stmt = $db->query($sql);
		$orderinfo = $stmt->fetch(PDO::FETCH_ASSOC);
		if(empty($orderinfo)) {
			$data = array('code'=>-2, 'data'=>'不存在此订单, 或者订单未付款或者订单已取消');
			echo json_encode($data);
			exit();
		}

		// 订单7天之后才可评价
		// echo time().'-'.$orderinfo['addtime'];
		// exit();
		if(time() - $orderinfo['addtime'] < 7*24*3600) {
			$data = array('code'=>-4, 'data'=>'请7天之后再来评价吧');
			echo json_encode($data);	
			exit();
		}

		$sql = "SELECT * FROM `cs_coach_comment` WHERE `order_no` = '".$order_no."'";
		$db = getConnection();
		$stmt = $db->query($sql);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if($row) {
			$data = array('code'=>-1, 'data'=>'您已评价');
			echo json_encode($data);
			exit();
		}

		$sql = "INSERT INTO `cs_coach_comment` (`coach_id`, `coach_star`, `school_star`, `coach_content`, `school_content`, `user_id`, `order_no`, `school_id`, `type`, `addtime`)";
		$sql .= " VALUES ('', '', :school_star, '', :school_content, :user_id, :order_no, :school_id, 2,'".time()."')";

		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam('school_star', $school_star);
			$stmt->bindParam('school_content', $school_content);
			$stmt->bindParam('user_id', $user_id);
			$stmt->bindParam('school_id', $sid);
			$stmt->bindParam('order_no', $order_no);

			$stmt->execute();
			$id = $db->lastInsertId();
			if($id) {
				// 已评价
				$sql = "UPDATE `cs_school_orders` SET `so_comment_status` = 2 WHERE `so_order_no` = '".$order_no."'";
				$stmt = $db->query($sql);

				$data = array('code'=>200, 'data'=>'评价成功');
			} else {
				$data = array('code'=>2, 'data'=>'评价失败');
			}
			$db = null;
			echo json_encode($data);
		} catch (PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('student_comment_school:params[id:'.$id.',sid:'.$sid.',school_star:'.$school_star.',comment_content:'.$school_content.',uid:'.$user_id.',no:'.$order_no.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);	
		}

	}

?>