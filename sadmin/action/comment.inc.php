<?php 
	//评价管理模块
	 header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','scomment','stucomment','delccom','delscom','delstucom','delmore','delmorestucom','searchccom','searchscom','searchstu')) ? $op : 'index';

	if(!isset($_SESSION['school_loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}
	$loginauth_str = authcode($_SESSION['school_loginauth'], 'DECODE');
	$loginauth_arr = explode('\t', $loginauth_str);
	$school_id = $loginauth_arr[2];

	$mcomment = new mcomment($db);
	$mschool = new mschool($db);

	if($op == 'index') {
		// 获取驾校评价列表
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$coach_comment_num = count($mcomment->getCoachCommentList('', '', $school_id)); //总数量
		$calcpagecnt = calcpagecnt($coach_comment_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $coach_comment_num, 'index.php?action=comment', array('op'=>'index'));

		$coach_comment_list = $mcomment->getCoachCommentList($pagestart, $limit, $school_id);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('coach_comment_num', $coach_comment_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('coach_comment_list', $coach_comment_list);
		// print_r($coach_comment_list) ;
		$smarty->display('comment/index.html');

	}else if($op == 'scomment') {
		// 获取驾校评价列表
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$school_comment_num = count($mcomment->getSchoolCommentList('', '', $school_id)); //总数量
		$calcpagecnt = calcpagecnt($school_comment_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $school_comment_num, 'index.php?action=comment', array('op'=>'scomment'));

		$school_comment_list = $mcomment->getSchoolCommentList($pagestart, $limit, $school_id);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('school_comment_num', $school_comment_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('school_comment_list', $school_comment_list);
		// print_r($school_comment_list) ;
		$smarty->display('comment/scomment.html');

	}else if($op == 'stucomment') {
		// 获取学员评价列表
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$student_comment_num = count($mcomment->getStudentCommentList('', '', $school_id)); //总数量
		$calcpagecnt = calcpagecnt($student_comment_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $student_comment_num, 'index.php?action=comment', array('op'=>'stucomment'));

		$student_comment_list = $mcomment->getStudentCommentList($pagestart, $limit, $school_id);
		// echo "<pre>";
		// print_r($student_comment_list);
		// exit();
		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('student_comment_num', $student_comment_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('student_comment_list', $student_comment_list);

		$smarty->display('comment/stucomment.html');

	// 删除更多驾校/教练评价
	} else if($op == 'delmore') {
		$id = isset($_POST['check_id']) ? trim($_POST['check_id']) : 0;
		$res = $mcomment->deleteMoreCComment($id);
		if($res) {
			echo 1;
			exit;
		} else {
			echo 2;
			exit();
		}


	// 删除更多学员评价
	} else if($op == 'delmorestucom') {
		$id = isset($_POST['check_id']) ? trim($_POST['check_id']) : 0;
		$res = $mcomment->deleteMoreSComment($id);
		if($res) {
			echo 1;
			exit;
		} else {
			echo 2;
			exit();
		}
	
	}else if($op == 'delccom') {
		$id = $_POST['id'];
		$res = $mcomment->deleteCoachComment($id); 
		if($res) {
			$ret = 1;
		} else {
			$ret = 0;
		}
		echo $ret;
		exit();
	    
	
	}else if($op == 'delscom') {
		$id = $_POST['id'];
		$res = $mcomment->deleteSchoolComment($id); 
		if($res) {
			$ret = 1;
		} else {
			$ret = 0;
		}
		echo $ret;
		exit();
	    
	
	}else if($op == 'delstucom') {
		$id = $_POST['id'];
		$res = $mcomment->deleteStudentComment($id); 
		if($res) {
			$ret = 1;
		} else {
			$ret = 0;
		}
		echo $ret;
		exit();
	    
	//搜索评价教练
	}else if($op == 'searchccom') {
		$conditiontype = isset($_POST['conditiontype']) ? trim($_POST['conditiontype']) : '';
		
		$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$coach_comment_num = count($mcomment->getSearchCCommentList('','',$school_id, $conditiontype, $keyword)); //总数量
		$calcpagecnt = calcpagecnt($coach_comment_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $coach_comment_num, 'index.php?action=comment', array('op'=>'searchccom'));
		$coach_comment_list = $mcomment->getSearchCCommentList($pagestart, $limit, $school_id, $conditiontype, $keyword);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('coach_comment_num', $coach_comment_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('coach_comment_list', $coach_comment_list);
		//print_r($coach_comment_list);
		$smarty->display('comment/index.html');
		
	//搜索评价驾校
	}else if($op == 'searchscom') {
		$conditiontype = isset($_POST['conditiontype']) ? trim($_POST['conditiontype']) : '';
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$school_comment_num = count($mcomment->getSearchSCommentList('','',$school_id, $conditiontype)); //总数量
		$calcpagecnt = calcpagecnt($school_comment_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $school_comment_num, 'index.php?action=comment', array('op'=>'searchscom'));
		$school_comment_list = $mcomment->getSearchSCommentList($pagestart, $limit, $school_id, $conditiontype);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('school_comment_num', $school_comment_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('school_comment_list', $school_comment_list);
		//print_r($school_comment_list);
		$smarty->display('comment/scomment.html');

	//搜索评价学员信息
	}else if($op == 'searchstu') {
		$studentconditiontype = isset($_POST['studentconditiontype']) ? trim($_POST['studentconditiontype']) : 1; // 学员条件
		$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : ''; // 关键词
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$student_comment_num = count($mcomment->getSearchStuCommentList('','',$school_id, $studentconditiontype, $keyword)); //总数量
		$calcpagecnt = calcpagecnt($student_comment_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $student_comment_num, 'index.php?action=comment', array('op'=>'searchstu'));
		$student_comment_list = $mcomment->getSearchStuCommentList($pagestart, $limit, $school_id, $studentconditiontype, $keyword);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('student_comment_num', $student_comment_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('student_comment_list', $student_comment_list);
		 // print_r($student_comment_list);
		$smarty->display('comment/stucomment.html');
	}



 ?>