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
	require 'include/token.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','opayment');
	$app->run();

	function opayment() {
		Global $app, $crypt;
		$request = $app->request();
		$token = new Form_token_Core();

		// if(!isset($_SESSION['loginauth'])) {
		// 	$data = array('code'=>-2, 'data'=>'请先登录');
		// 	echo json_encode($data);
		// 	exit();
		// }
		// $loginauth = $crypt->decrypt($_SESSION['loginauth']);
		// $loginauth_arr = explode('\t', $loginauth);
		// $user_id = $loginauth_arr[0];

		$id = $request->params('id');
		$sid = $request->params('sid');
		$ptype = $request->params('ptype');
		$user_name = $request->params('user_name');
		$user_phone = $request->params('user_phone');
		$identity_id = $request->params('user_identify_id');
		$licence_type = $request->params('licence');
		$order_type = $request->params('order_type');
		$uid = $request->params('uid');

		$data = array('code'=>200, 'data'=>'可报名');
		if($uid == '') {
			$data = array('code'=>-2, 'data'=>'请先登录');
			echo json_encode($data);
			exit();
		}

		if(trim($id) == '' || trim($sid) == '' || trim($ptype) == '' || trim($user_phone) == '' || trim($user_name) == '' || trim($identity_id) == '' || trim($licence_type) == '' || trim($order_type) == '' || trim($uid) == '') {
			$data = array('code'=>-5, 'data'=>'请完善信息');
			echo json_encode($data);
			exit();
		}

		// 验证身份证
		// if(!identify($identity_id)) {
		// 	$data = array('code'=>-6, 'data'=>'身份证验证失败');
		// 	echo json_encode($data);
		// 	exit();
		// }

		$so_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);

		$db = getConnection();
		// 通过班制ID获取班制信息
		$sql = "SELECT `sh_money`, `sh_original_money` FROM `cs_school_shifts` WHERE `id` = ".$id;
		$stmt = $db->query($sql);
		$shifts_info = $stmt->fetch(PDO::FETCH_ASSOC);

		if(empty($shifts_info)) {
			$data = array('code'=>-1, 'data'=>'参数错误');
			echo json_encode($data);
			exit();
		}

		// 获取是否已报名
		$sql = "SELECT * FROM `cs_school_orders` WHERE `so_user_identity_id` = '".$identity_id."' AND `so_order_status` != 101 AND ((`so_pay_type` = 1 AND `so_order_status` = 1) OR (`so_pay_type` = 2 AND `so_order_status` = 3)  OR (`so_pay_type` = 2 AND `so_order_status` = 1) OR (`so_pay_type` = 3 AND `so_order_status` = 1))";
		$stmt = $db->query($sql);
		$order_info = $stmt->fetch(PDO::FETCH_ASSOC);
		if($order_info) {
			$data = array('code'=>-7, 'data'=>'此身份证已报过名！');
			echo json_encode($data);
			exit();
		}
		echo json_encode($data);
		exit();
	}

	// 验证是身份证
	function identify($id) {
	    $ch = curl_init();
	    $url = 'http://apis.baidu.com/apistore/idservice/id?id='.$id;
	    $header = array(
	        'apikey:3f476886841e800307821a2edb3b50c6',
	    );
	    // 添加apikey到header
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    // 执行HTTP请求
	    curl_setopt($ch , CURLOPT_URL, $url);
	    $res = curl_exec($ch);
	    $result = json_decode($res, true);
	    if($result['errNum'] == 0) {
	    	return true;
	    } else {
	    	return false;
	    }
	}
?>