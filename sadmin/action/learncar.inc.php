<?php  
	// 报名管理模块

	header("Content-type: text/html; charset=utf-8");

	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','add','addoperate','edit','del','editcheck','search','dellearncar','setorderstatus','getusername','getlearncarorderdetail','checkno','getpendingorders')) ? $op : 'index';

	if(!isset($_SESSION['school_loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}
	$loginauth_str = authcode($_SESSION['school_loginauth'], 'DECODE');
	$loginauth_arr = explode('\t', $loginauth_str);
	$school_id = $loginauth_arr[2];
	
	$mlearncar = new mlearncar($db);
	$mcoach = new mcoach($db);

	if($op == 'index') {

		// 获取所有订单列表
		// 1：已付款 2：未付款 3：取消订单 4：已完成
		// 1: 线上支付 2：线下支付

		$order_id = !empty($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 1;
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$orderlist = array();
		// 订单
		$order_num = count($mlearncar->getOrderList('', '', $school_id, $order_id)); //总数量
		$calcpagecnt = calcpagecnt($order_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $order_num, 'index.php?action=learncar', array('op'=>'index', 'order_id'=>$order_id));
		$orderlist = $mlearncar->getOrderList($pagestart, $limit, $school_id, $order_id);
			
		$smarty->assign('calcpagecnt'	, $calcpagecnt); //分页数
		$smarty->assign('order_num'		, $order_num); //总条数
		$smarty->assign('pagehtml'		, $pagehtml); //总条数
		$smarty->assign('orderlist'		, $orderlist);

		$smarty->assign('order_id', $order_id);
		$smarty->display('learncar/index.html');

	// 添加订单
	} else if($op == 'add') {
		//
		$order_id = isset($_GET['order_id']) ? trim($_GET['order_id']) : 1; 
		// 获取教练列表
		$coachlist = $mcoach->getCoachlist('','',$school_id);

		// 获取预约时间
		// 获取当前教练的时间配置
		$time_config_list = $mcoach->getCoachTimeConfig($school_id);
		// $time_config_list = $mlearncar->getTimeConfigList($school_id);

		$smarty->assign('coachlist', $coachlist);
		$smarty->assign('lesson_config', $lesson_config);
		$smarty->assign('lisence_config', $lisence_config);
		$smarty->assign('time_config_list', $time_config_list);
		$smarty->assign('order_id', $order_id);
		$smarty->display('learncar/add.html');

	// 添加操作
	} else if($op == 'addoperate') {
		$arr = array();
		$arr['coach_id'] = isset($_POST['coach_id']) ? trim($_POST['coach_id']) : ''; // 教练ID
		$arr['time_config_id'] = isset($_POST['time_config_id']) ? $_POST['time_config_id'] : ''; // 预约时间段
		$arr['s_order_no'] = isset($_POST['s_order_no']) ? trim($_POST['s_order_no']) : ''; // 订单号
		$arr['s_user_phone'] = isset($_POST['s_user_phone']) ? trim($_POST['s_user_phone']) : ''; // 学员手机号
		$arr['s_user_name'] = isset($_POST['s_real_name']) ? trim($_POST['s_real_name']) : ''; // 学员姓名
		$arr['coach_phone'] = isset($_POST['coach_phone']) ? trim($_POST['coach_phone']) : ''; // 教练手机号
		$arr['s_coach_name'] = isset($_POST['s_coach_name']) ? trim($_POST['s_coach_name']) : ''; // 教练姓名
		$arr['appoint_date'] = isset($_POST['appoint_date']) ? trim($_POST['appoint_date']) : ''; // 预约日期

		$arr['lesson_id'] = isset($_POST['lesson_id']) ? $_POST['lesson_id'] : ''; // 培训科目
		$arr['lisence_id'] = isset($_POST['lisence_id']) ? $_POST['lisence_id'] : ''; // 培训牌照
		$arr['dc_money'] = isset($_POST['dc_money']) ? trim($_POST['dc_money']) : ''; // 订单总价
		$arr['i_service_time'] = isset($_POST['i_service_time']) ? trim($_POST['i_service_time']) : ''; // 服务时长
		$arr['deal_type'] = isset($_POST['deal_type']) ? trim($_POST['deal_type']) : '';  // 交易类型

		// echo "<pre>";
		// print_r($arr);
		// exit();
		$res = $mlearncar->insertStudyOrder($arr);
		if($res == 1) {
			echo "<script>alert('该订单号已经存在,请添加前验证订单号是否存在。');location.href='index.php?action=learncar&op=index';</script>";
			exit();

		} else if($res == 2) {
			echo "<script>alert('该学员预约时间段超过两个小时');location.href='index.php?action=learncar&op=index';</script>";
			exit();

		} else if($res == 3) {

			echo "<script>alert('添加失败！');location.href='index.php?action=learncar&op=index';</script>";
			exit();

		} else if($res == 4 || $res == 5) {
			echo "<script>alert('添加失败！');location.href='index.php?action=learncar&op=index';</script>";
			exit();

		} else if($res == 200) {
			echo "<script>alert('添加成功！');location.href='index.php?action=learncar&op=index';</script>";
			exit();

		} else if($res == 6) {
			echo "<script>alert('你选择时间段有已经被预约的！');location.href='index.php?action=learncar&op=index';</script>";
			exit();

		}

	} else if($op == 'edit') {
		
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$order_id = isset($_GET['order_id']) ? trim($_GET['order_id']) : '1'; 

		// 获取教练列表
		$coachlist = $mcoach->getCoachlist('','',$school_id);

		// 获取预约时间
		// 获取当前教练的时间配置
		$time_config_list = $mcoach->getCoachTimeConfig($school_id);
		// $time_config_list = $mlearncar->getTimeConfigList($school_id);

		$orderdetail = $mlearncar->getOrderDetail($id);

		$smarty->assign('coachlist', $coachlist);
		$smarty->assign('lesson_config', $lesson_config);
		$smarty->assign('lisence_config', $lisence_config);
		$smarty->assign('time_config_list', $time_config_list);
		$smarty->assign('orderdetail', $orderdetail); //总条数
		$smarty->assign('id', $id); //总条数
		$smarty->assign('order_id', $order_id);
		$smarty->display('learncar/edit.html');

	} else if($op == 'del') {

		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$res = $mlearncar->delOrder($id);
		$ret['code'] = 1;
		if($res) {
			echo json_encode($ret);
			exit();
		} else {
			$ret['code'] = 0;
			echo json_encode($ret);
			exit();
		}

	} else if($op == 'delmore') {

		$id = $_REQUEST['check_id'];
		$res = $mlearncar->delOrder($id);
		if($res) {
			$ret['code'] = 1;
			echo json_encode($ret);
			exit();
		} else {
			$ert['code'] = 0;
			echo json_encode($ret);
			exit();
		}

	// 获取待完成的订单
	} elseif($op == 'getpendingorders') {

		// 获取未完成订单
		$orderlist = $mlearncar->getOrderList('','',$school_id,1);
		$smarty->assign('orderlist', $orderlist);
		$smarty->display('learncar/getpendingorders.html');
		
	} elseif($op == 'getalipay') {
		print_r($_REQUEST);

	// 搜索 
	} elseif($op == 'search') {
		$keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
		$paytype = isset($_REQUEST['paytype']) ? trim($_REQUEST['paytype']) : 1;
		$conditiontype = isset($_REQUEST['conditiontype']) ? trim($_REQUEST['conditiontype']) : 1;
		$order_id = isset($_REQUEST['order_id']) ? trim($_REQUEST['order_id']) : 1;

		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$order_num = count($mlearncar->getSearchOrderList('', '', $school_id,$keyword, $paytype, $conditiontype, $order_id)); //总数量
		$calcpagecnt = calcpagecnt($order_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $order_num, 'index.php?action=learncar', array('op'=>'search', 'order_id'=>$order_id, 'paytype'=>$paytype, 'conditiontype'=>$conditiontype, 'keyword'=>$keyword));
		$orderlist = $mlearncar->getSearchOrderList($pagestart, $limit, $school_id, $keyword, $paytype, $conditiontype, $order_id);
			
		$smarty->assign('calcpagecnt'	, $calcpagecnt); //分页数
		$smarty->assign('order_num'		, $order_num); //总条数
		$smarty->assign('pagehtml'		, $pagehtml); //总条数
		$smarty->assign('orderlist'		, $orderlist);
		$smarty->assign('order_id'		, $order_id);
		$smarty->assign('s_keyword'		, $keyword);
		$smarty->assign('paytype'		, $paytype);
		$smarty->assign('conditiontype'		, $conditiontype);
		$smarty->display('learncar/search.html');

	// 设置完成状态
	} else if($op == 'setorderstatus') {
		$id = isset($_POST['id']) ? trim($_POST['id']) : 0;
		$status = isset($_POST['status']) ? trim($_POST['status']) : 0;

		$res = $mlearncar->setOrderStatus($id, $status);
		if($res) {
			$data = array('code'=>200, 'data'=>$res);
		} else {
			$data = array('code'=>-1, 'data'=>'');
		}
		echo json_encode($data);
	
	// 删除订单
	} else if($op == 'dellearncar') {
		$id = isset($_POST['id']) ? trim($_POST['id']) : '';
		$res = $mlearncar->delLearnCarOrder($id);
		if($res) {
			echo 1;
		} else {
			echo 2;
		}
		exit();

	// 获取用户名
	} else if($op == 'getusername') {
		$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
		$type = isset($_POST['type']) ? trim($_POST['type']) : '';
		if($phone == '' || $type == '') {
			$data = array('code'=>-2, 'data'=>'');
			echo json_encode($data);
			exit();
		}
		$user_info = $mlearncar->getUsername($phone, $type);
		if($user_info) {
			$data = array('code'=>200, 'data'=>$user_info['s_real_name']);
		} else {
			$data = array('code'=>-1, 'data'=>'');
		}
		echo json_encode($data);
		exit();

	// 检测订单号是否重复
	} else if($op == 'checkno') {
		$no = isset($_POST['no']) ? trim($_POST['no']) : 0;
		$res = $mlearncar->getLearncarOrdernoCheck($no);
		if($res) {
			$data = array('code'=>-1, 'data'=>'有重复订单号');
		} else {
			$data = array('code'=>1, 'data'=>'没有重复订单号');
		}
		echo json_encode($data);
	}

?>