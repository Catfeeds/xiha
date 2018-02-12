<?php  

	// 报名订单管理

	header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','dellearncar','setorderstatus','search')) ? $op : 'index';

	if(!isset($_SESSION['loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}

	$mlearncar = new mlearncar($db);

   	if($op == 'index') {

		$order_id = !empty($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 1;
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$orderlist = array();
		// 订单
		$order_num = count($mlearncar->getOrderList('', '', $order_id)); //总数量
		$calcpagecnt = calcpagecnt($order_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $order_num, 'index.php?action=learncar', array('op'=>'index', 'order_id'=>$order_id));
		$orderlist = $mlearncar->getOrderList($pagestart, $limit, $order_id);
			
		$smarty->assign('calcpagecnt'	, $calcpagecnt); //分页数
		$smarty->assign('order_num'		, $order_num); //总条数
		$smarty->assign('pagehtml'		, $pagehtml); //总条数
		$smarty->assign('orderlist'		, $orderlist);

		$smarty->assign('order_id', $order_id);
		$smarty->assign('paytype', 0);
		$smarty->assign('keyword', '');
		$smarty->assign('conditiontype', '');
		$smarty->display('learncar/index.html');

	
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
	// 搜索 
	} elseif($op == 'search') {
		$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
		$paytype = isset($_POST['paytype']) ? trim($_POST['paytype']) : 1;
		$conditiontype = isset($_POST['conditiontype']) ? trim($_POST['conditiontype']) : 1;
		$order_id = isset($_POST['order_id']) ? trim($_POST['order_id']) : 1;
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 10;
		$pagestart = ($page - 1) * $limit;

		$order_num = count($mlearncar->getSearchOrderList('', '', $keyword, $paytype, $conditiontype, $order_id)); //总数量
		$calcpagecnt = calcpagecnt($order_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $order_num, 'index.php?action=learncar', array('op'=>'search', 'order_id'=>$order_id, 'paytype'=>$paytype, 'conditiontype'=>$conditiontype, 'keyword'=>$keyword));
		$orderlist = $mlearncar->getSearchOrderList($pagestart, $limit,  $keyword, $paytype, $conditiontype, $order_id);
	
		$smarty->assign('calcpagecnt'	, $calcpagecnt); //分页数
		$smarty->assign('order_num'		, $order_num); //总条数
		$smarty->assign('pagehtml'		, $pagehtml); //总条数
		$smarty->assign('orderlist'		, $orderlist);

		$smarty->assign('order_id'		, $order_id);

        // 搜索条件
		$smarty->assign('keyword'		, $keyword);
		$smarty->assign('paytype'		, $paytype);
		$smarty->assign('conditiontype' , $conditiontype);
		$smarty->display('learncar/index.html');

	}

?>
