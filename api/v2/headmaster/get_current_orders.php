<?php  
	/**
	 * 获取学员或者教练我的信息
	 * @param $type int 1：获取教练信息 2：获取学员信息
	 * @param $member_id int 学员或者教练ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require '../../Slim/Slim.php';
	require '../../include/common.php';
	require '../../include/crypt.php';
    require '../../include/functions.php';
	
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->any('/','getCurrentOrders');
	$app->run();

	// 获取教练学员信息
	function getCurrentOrders() {
		
		Global $app, $crypt;
        $request = $app->request();
		if(!$request->isPost()) {
			$data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_current_orders] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            echo json_encode($data);
            exit();
		}

	 	//	获取请求参数并判断合法性
        $license = $request->params('license');//牌照C1/A1/A2/D
        $subject = $request->params('subject');//科目1/4 
        $validate_result = validate(array('license'=>'NOT_NULL', 'subject'=>'INT'), $request->params());
        if (!$validate_result['pass']) {
            echo json_encode($validate_result['data']);
            exit();
        }
        
		try {
			$db = getConnection();

		} catch(PDOException $e) {
			setapilog('get_member_info:params[type:'.$type.',member_id:'.$member_id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}

	}

?>