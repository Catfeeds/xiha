<?php  
	// 报名管理模块

	header("Content-type: text/html; charset=utf-8");

	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','add','addoperate','edit','getpendingorders','editoperate','del','editcheck','phonecheck','identitycheck','setorderstatus','search')) ? $op : 'index';

	if(!isset($_SESSION['school_loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}
	$loginauth_str = authcode($_SESSION['school_loginauth'], 'DECODE');
	$loginauth_arr = explode('\t', $loginauth_str);
	$school_id = $loginauth_arr[2];

	$msignup = new msignup($db);
	$mschool = new mschool($db);

	if($op == 'index') {

		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$shifts_id = !empty($_REQUEST['shifts_id']) ? max(1, intval($_REQUEST['shifts_id'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		// 获取班制列表
		$shiftslist = $mschool->getShiftsList($school_id);
		
		if(!isset($_REQUEST['shifts_id'])) {
			// 获取第一个班制的列表
			if($shiftslist) {
				foreach ($shiftslist as $key => $value) {
					if($key == 0) {
						$shifts_id = $value['id'];
					}
				}
			} else {
				$shifts_id = 0;
			}
		}

		$shiftslist_num = count($msignup->getShiftsOrderList('','',$school_id, $shifts_id)); //总数量
		$calcpagecnt = calcpagecnt($shiftslist_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $shiftslist_num, 'index.php?action=signup', array('op'=>'index','shifts_id'=>$shifts_id)); 
		$shifts_list = $msignup->getShiftsOrderList($pagestart, $limit, $school_id, $shifts_id);
		
		$smarty->assign('shiftslist', $shiftslist);
		$smarty->assign('shifts_list', $shifts_list);
		$smarty->assign('shifts_id', $shifts_id);
		$smarty->assign('pagehtml', $pagehtml); //预约计时班总条数
		$smarty->assign('calcpagecnt', $calcpagecnt); //预约计时班总条数
		$smarty->assign('shiftslist_num', $shiftslist_num); //预约计时班总条数

		$smarty->display('signup/index.html');

	} else if($op == 'add') {

		$shifts_id = isset($_GET['shifts_id']) ? trim($_GET['shifts_id']) : 1;
		// 获取班制信息
		$shiftslist = $mschool->getShiftsList($school_id);
		$smarty->assign('lisence_config', $lisence_config);
		$smarty->assign('shiftslist', $shiftslist);
		$smarty->assign('shifts_id', $shifts_id);
		$smarty->display('signup/add.html');

	} else if($op == 'addoperate') {
		$arr = array();
		$arr['so_school_id'] = $school_id;
		$arr['so_username'] = trim($_POST['so_username']);
		$arr['so_phone'] = trim($_POST['so_phone']);
		$arr['so_licence'] = trim($_POST['so_licence']);
		$arr['so_final_price'] = trim($_POST['so_final_price']);
		$arr['so_original_price'] = trim($_POST['so_original_price']);
		$arr['so_shifts_id'] = trim($_POST['so_shifts_id']);
		$arr['so_pay_type'] = trim($_POST['so_pay_type']);
		$arr['so_order_status'] = trim($_POST['so_order_status']);
		$arr['so_comment_status'] = 1;
		$arr['so_order_no'] = trim($_POST['so_order_no']);
		$arr['so_user_identity_id'] = trim($_POST['so_user_identity_id']);

		// 通过身份证找出用户ID
		$user_info = $msignup->getUserInfoById($arr['so_user_identity_id']);

		if($user_info) {
			$arr['so_user_id'] = $user_info['user_id'];
		} else {
			echo "<script>if(confirm('当前没有录入学员信息，请先录入学员信息')) {location.href='index.php?action=member&op=add';} else {location.href='index.php?action=signup&op=add';}</script>";
			exit();
		}
		$res = $msignup->addSchoolOrder($arr);
		if($res == 1) {
			echo "<script>alert('此号码已经报名驾校！');location.href='index.php?action=signup&op=index';</script>";
			exit();
		} else if($res == 2) {
			echo "<script>alert('此身份证已经报名驾校！');location.href='index.php?action=signup&op=index';</script>";
			exit();
		} else if($res == 3) {
			echo "<script>alert('添加成功！');location.href='index.php?action=signup&op=index';</script>";
			exit();
		} else {
			echo "<script>alert('添加失败！');location.href='index.php?action=signup&op=add';</script>";
			exit();
		}

	// 检测手机重复性
	} else if($op == 'phonecheck') {
		$so_phone = isset($_POST['phone']) ? trim($_POST['phone']) : 0;
		if(!preg_match('/^1[3-5,8]{1}[0-9]{9}$/', $so_phone)) {
			echo 3;
			exit();
		}
		$res = $msignup->phonecheck($so_phone);
		if($res) {
			echo 1;
		} else {
			echo 2;
		}
	// 检测身份证
	} else if($op == 'identitycheck') {
		$identity_id = isset($_POST['identity_id']) ? trim($_POST['identity_id']) : 0;
		$res = $msignup->identitycheck($identity_id);
		if($res) {
			echo 1;
		} else {
			echo 2;
		}

	// 设置订单状态
	} else if($op == 'setorderstatus') {
		$order_id = isset($_POST['id']) ? trim($_POST['id']) : 0;
		$pay_type = isset($_POST['pay']) ? trim($_POST['pay']) : 0;
		$order_status = isset($_POST['status']) ? trim($_POST['status']) : 0;
		$type = isset($_POST['type']) ? trim($_POST['type']) : 0;

		$res = $msignup->setOrderStatus($order_id, $pay_type, $order_status, $type);
		if($res) {
			echo 1;
		} else {
			echo 2;
		}

	} else if($op == 'edit') {

		$shifts_id = !empty($_REQUEST['shifts_id']) ? max(1, intval($_REQUEST['shifts_id'])) : 1;
		$shiftslist = $mschool->getShiftsList($school_id);
		// 获取订单详情
		$oid = isset($_GET['id']) ? trim($_GET['id']) : 0;
		$signup_detail = $msignup->getOrderDetail($oid);
		// print_r($signup_detail);
		$smarty->assign('lisence_config', $lisence_config);
		$smarty->assign('shiftslist', $shiftslist);
		$smarty->assign('shifts_id', $shifts_id);
		$smarty->assign('signup_detail', $signup_detail);
		$smarty->display('signup/edit.html');

	// 编辑报名驾校订单
	} else if($op == 'editoperate') {
		$arr['id'] = isset($_POST['id']) ? trim($_POST['id']) : 0;
		$arr['so_school_id'] = $school_id;
		$arr['so_username'] = trim($_POST['so_username']);
		$arr['so_phone'] = trim($_POST['so_phone']);
		$arr['so_licence'] = isset($_POST['so_licence']) ? trim($_POST['so_licence']) : 0;

		$arr['so_final_price'] = trim($_POST['so_final_price']);
		$arr['so_original_price'] = trim($_POST['so_original_price']);
		$arr['so_shifts_id'] = isset($_POST['so_shifts_id']) ? trim($_POST['so_shifts_id']) : 0;
		$arr['so_pay_type'] = trim($_POST['so_pay_type']);
		$arr['so_order_status'] = trim($_POST['so_order_status']);
		$arr['so_comment_status'] = 1;
		$arr['so_order_no'] = trim($_POST['so_order_no']);
		$arr['so_user_identity_id'] = trim($_POST['so_user_identity_id']);
		// echo $arr['so_licence'];
		// exit();
		if($arr['so_licence'] === 0) {
			echo "<script>alert('请选择驾照类型！');location.href='index.php?action=signup&op=edit&id=".$arr['id']."';</script>";
			exit();
		}

		if($arr['so_shifts_id'] == 0) {
			echo "<script>alert('请选择班制！');location.href='index.php?action=signup&op=edit&id=".$arr['id']."';</script>";
			exit();
		}

		$res = $msignup->updateSchoolOrders($arr);
		if($res) {
			echo "<script>alert('修改成功！');location.href='index.php?action=signup&op=index';</script>";
			exit();
		} else {
			echo "<script>alert('修改失败！');location.href='index.php?action=signup&op=index';</script>";
			exit();
		}

	// 搜索
	} else if($op == 'search') {
		$keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
		$shifts_id = isset($_REQUEST['shifts_id']) ? trim($_REQUEST['shifts_id']) : 1;
		$membertype = isset($_REQUEST['membertype']) ? trim($_REQUEST['membertype']) : 1;
		$paytype = isset($_REQUEST['paytype']) ? trim($_REQUEST['paytype']) : 0;

		if($keyword == '' || $membertype == '' || $shifts_id == '') {
			echo "<script>alert('参数错误');history.back(-1);</script>";
			return false;
		}

		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		// 获取班制列表
		$shiftslist = $msignup->getShiftslist($school_id);
		
		if(!isset($_REQUEST['shifts_id'])) {
			// 获取第一个班制的列表
			if($shiftslist) {
				foreach ($shiftslist as $key => $value) {
					if($key == 0) {
						$shifts_id = $value['id'];
					}
				}
			} else {
				$shifts_id = 0;
			}
		}

		$shiftslist_num = count($msignup->getSchoolOrdersByShifts('','',$school_id, $shifts_id, $keyword, $membertype, $paytype)); //总数量
		$calcpagecnt = calcpagecnt($shiftslist_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $shiftslist_num, 'index.php?action=signup', array('op'=>'search', 'shifts_id'=>$shifts_id, 'membertype'=>$membertype, 'paytype'=>$paytype, 'keyword'=>$keyword)); 
		$shifts_list = $msignup->getSchoolOrdersByShifts($pagestart, $limit, $school_id, $shifts_id, $keyword, $membertype, $paytype);
		
		$smarty->assign('shiftslist', $shiftslist); // 班制列表
		$smarty->assign('shifts_list', $shifts_list); // 订单列表
		$smarty->assign('keyword', $keyword); // 关键词
		$smarty->assign('membertype', $membertype); // 学员条件
		$smarty->assign('paytype', $paytype); // 支付条件
		$smarty->assign('shifts_id', $shifts_id); // 班制ID
		$smarty->assign('pagehtml', $pagehtml); //预约计时班总条数
		$smarty->assign('calcpagecnt', $calcpagecnt); //预约计时班总条数
		$smarty->assign('shiftslist_num', $shiftslist_num); //预约计时班总条数

		$smarty->display('signup/search.html');

	// 删除订单
	} else if($op == 'del') {
		$id = isset($_POST['id']) ? trim($_POST['id']) : '';
		$res = $msignup->delSignupOrder($id);
		if($res) {
			echo 1;
		} else {
			echo 2;
		}
		exit();

	// 报名驾校订单首页弹出提示
	} else if($op == 'getpendingorders') {
		//获取报名驾校订单信息（订单号，学员号码，下单时间，订单状态）
		$orderlist = $msignup->getSignupOrderTips($school_id);
		$smarty->assign('orderlist', $orderlist);
		$smarty->display('signup/getpendingorders.html');
	}
?>
