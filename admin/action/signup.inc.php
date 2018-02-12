<?php 
// 报名订单管理

	header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','del','edit','search', 'setorderstatus')) ? $op : 'index';

	if(!isset($_SESSION['loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}

	$msignup = new msignup($db);

	if($op == 'index'){
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;
		// 获取班制列表
		// $shiftslist = $msignup->getShiftsList($school_id);
		$shiftslist = '';
		
		$shiftslist_num = count($msignup->getShiftsOrderList('','')); //总数量
		$calcpagecnt = calcpagecnt($shiftslist_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $shiftslist_num, 'index.php?action=signup', array('op'=>'index')); 
		$shifts_list = $msignup->getShiftsOrderList($pagestart, $limit);
		$smarty->assign('shifts_list', $shifts_list);
		$smarty->assign('pagehtml', $pagehtml); //预约计时班总条数
		$smarty->assign('calcpagecnt', $calcpagecnt); //预约计时班总条数
		$smarty->assign('shiftslist_num', $shiftslist_num); //预约计时班总条数
		$smarty->assign('shiftslist', $shiftslist); //预约计时班总条数
		$smarty->display('signup/index.html');
  
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
	} else if($op == 'search') {
		$keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';//输入的查询具体内容
		$membertype = isset($_REQUEST['membertype']) ? trim($_REQUEST['membertype']) : 1;//搜索的类型：学员姓名，学员号码，订单号，唯一识别码
		$paytype = isset($_REQUEST['paytype']) ? trim($_REQUEST['paytype']) : 0;//支付方式

		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		if ( $membertype == '' || $paytype == '') {
			echo "<script>alert('参数错误');history.back(-1);</script>";
			return false;
		}
		$signup_order_list = count($msignup->getSignupOrders('','', $keyword, $membertype, $paytype)); //总数量
		$calcpagecnt = calcpagecnt($signup_order_list, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $signup_order_list, 'index.php?action=signup', array('op'=>'search', 'membertype'=>$membertype, 'paytype'=>$paytype, 'keyword'=>$keyword)); 
		$order_list = $msignup->getSignupOrders($pagestart, $limit, $keyword, $membertype, $paytype);
		
		$shiftslist = '';
		$smarty->assign('shiftslist', $shiftslist); // 班制列表
		$smarty->assign('order_list', $order_list); // 订单列表
		$smarty->assign('keyword', $keyword); // 关键词
		$smarty->assign('membertype', $membertype); // 学员条件
		$smarty->assign('paytype', $paytype); // 支付条件
		$smarty->assign('pagehtml', $pagehtml); 
		$smarty->assign('calcpagecnt', $calcpagecnt); 
		$smarty->assign('signup_order_list', $signup_order_list);

		$smarty->display('signup/search.html');
		
	} else if($op == 'setorderstatus') {
		$order_id = isset($_POST['id']) ? trim($_POST['id']) : 0;
		$pay_type = isset($_POST['pay']) ? trim($_POST['pay']) : 0;
		$type = isset($_POST['type']) ? trim($_POST['type']) : 0;

		$res = $msignup->setOrderStatus($order_id, $pay_type, $type);
		if($res) {
			echo 1;
		} else {
			echo 2;
		}

	}		
?>