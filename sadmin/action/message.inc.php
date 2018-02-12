<?php  

	// 消息模块

	header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');

	$op = in_array($op, array('index','add','addoperate','scomment','studentsend','studentsendoperate','multistudentsend','multistudentsendoperate','getmemberlist')) ? $op : 'index';

	if(!isset($_SESSION['school_loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}
	$loginauth_str = authcode($_SESSION['school_loginauth'], 'DECODE');
	$loginauth_arr = explode('\t', $loginauth_str);
	$school_id = $loginauth_arr[2];

	$mmessage = new mmessage($db);

	if($op == 'index') {

		$i_type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 2;  // 1：通知 2：正常
		$is_read = isset($_REQUEST['is_read']) ? trim($_REQUEST['is_read']) : '';
		$member_type = isset($_REQUEST['member_type']) ? trim($_REQUEST['member_type']) : 1;  // 1：学员 2：教练

		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 10;
		$pagestart = ($page - 1) * $limit;

		$message_num = count($mmessage->getMessageList('', '', $school_id, $i_type, $is_read, $member_type)); //总数量
		$calcpagecnt = calcpagecnt($message_num, $limit); //计算分页数
		$pagehtml    = ShowPage($page, $limit, $calcpagecnt, $message_num, 'index.php?action=message', array('op'=>'index', 'type'=>$i_type, 'is_read'=>$is_read, 'member_type'=>$member_type));

		$messagelist = $mmessage->getMessageList($pagestart, $limit, $school_id, $i_type, $is_read, $member_type);

		$smarty->assign('i_type', $i_type);
		$smarty->assign('is_read', $is_read);
		$smarty->assign('member_type', $member_type);
		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('message_num', $message_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('messagelist', $messagelist);

		$smarty->display('message/index.html');

	} else if($op == 'scomment') {

		$i_type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 2;
		$is_read = isset($_REQUEST['is_read']) ? trim($_REQUEST['is_read']) : 1;
		$member_type = isset($_REQUEST['member_type']) ? trim($_REQUEST['member_type']) : 2;

		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$message_num = count($mmessage->getMessageList('', '', $school_id, $i_type, $is_read, $member_type)); //总数量
		$calcpagecnt = calcpagecnt($message_num, $limit); //计算分页数
		$pagehtml    = ShowPage($page, $limit, $calcpagecnt, $message_num, 'index.php?action=message', array('op'=>'index', 'type'=>$i_type, 'is_read'=>$is_read, 'member_type'=>$member_type));

		$messagelist = $mmessage->getMessageList($pagestart, $limit, $school_id, $i_type, $is_read, $member_type);

		// echo "<pre>";
		// print_r($messagelist);
		$smarty->assign('i_type', $i_type);
		$smarty->assign('is_read', $is_read);
		$smarty->assign('member_type', $member_type);
		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('message_num', $message_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('messagelist', $messagelist);

		$smarty->display('message/index.html');

	// 发送消息
	} else if($op == 'studentsend') {
		$type = isset($_POST['type']) ? trim($_POST['type']) : 1;
		$studentlist = $mmessage->getMemberList($type, $school_id);
		$smarty->assign('studentlist', $studentlist);
		$smarty->display('message/studentsend.html');

	} else if($op == 'studentsendoperate') {

		$member_id_phone = isset($_POST['user_phone']) ? trim($_POST['user_phone']) : '';
		$member_id_phone_arr = explode('|', $member_id_phone);
		$user_phone = $member_id_phone_arr[0]; // 学员号码
		$member_id = $member_id_phone_arr[1]; // 学员id
		$member_type = 1; // 1：学员端 2：教练端   用户类型
		$s_beizhu = isset($_POST['s_beizhu']) ? trim($_POST['s_beizhu']) : ''; // 备注
		$title = isset($_POST['title']) ? trim($_POST['title']) : ''; // 标题
		$content = isset($_POST['content']) ? trim($_POST['content']) : '';  // 内容
		$i_yw_type = isset($_POST['i_yw_type']) ? trim($_POST['i_yw_type']) : 1; // 1：通知 2：正常 4：其他
		$m_time = '86400'; // 

		if($member_id_phone == '' || $title == '' || $content == '' || $i_yw_type == '') {
			echo "<script>alert('请将完善发送信息');location.href='index.php?action=message&op=index';</script>";
			exit();
		}
		$arr = array();
		$arr['user_phone'] = $user_phone;
		$arr['member_id'] = $member_id;
		$arr['member_type'] = $member_type;
		$arr['s_beizhu'] = $s_beizhu;
		$arr['title'] = $title;
		$arr['content'] = $content;
		$arr['i_yw_type'] = $i_yw_type;
		$arr['m_time'] = $m_time;
		$arr['school_id'] = $school_id;

		$arr['receive'] = array(
	 			'alias' => array(
	 				$user_phone
 				)
 			);
		// print_r($arr);
		// exit();	
		$res = $mmessage->sendmessage($arr);
		if($res) {
			echo "<script>alert('发送成功！');location.href='index.php?action=message&op=index';</script>";
			exit();
		} else {
			echo "<script>alert('发送失败！');location.href='index.php?action=message&op=index';</script>";
			exit();
		}

	// 群发消息
	} else if($op == 'multistudentsend') {

		// 获取所有发送主题

		$smarty->display('message/multistudentsend.html');

	} else if($op == 'getmemberlist') {

		$type = isset($_POST['type']) ? trim($_POST['type']) : 1;
		$list = $mmessage->getMemberList($type, $school_id);
		echo json_encode($list);
	}