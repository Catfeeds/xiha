<?php  

	// 会员模块

	header("Content-type: text/html; charset=utf-8");

	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','add','addoperate','edit','editoperate','del','search','delmore')) ? $op : 'index';

	if(!isset($_SESSION['loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}
	
	$mmember = new mmember($db);
	$mschool = new mschool($db);

	if($op == 'index') {
		
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;
		// $member_num = count($mmember->getMemberList()); //总数量
		$member_num_info = $mmember->getMemberListNum();
		$member_num = $member_num_info['num'];
		$calcpagecnt = calcpagecnt($member_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $member_num, 'index.php?action=member', array('op'=>'index'));

		$member_list = $mmember->getMemberList($pagestart, $limit);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('member_num', $member_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('member_list', $member_list);
		$smarty->display('member/index.html');

	} else if($op == 'add') {

		// 获取所有省份列表
		$provincelist = $mschool->getProvinceList();
		// 获取班制信息
		$shifts_list = $mschool->getShiftsList();
		$smarty->assign('lisence_config', $lisence_config);
		$smarty->assign('provincelist', $provincelist);
		$smarty->assign('shifts_list', $shifts_list);
		$smarty->display('member/add.html');

	// 添加学员操作
	} else if($op == 'addoperate') {

		$arr = array();
		$arr['user_name'] = trim($_POST['user_name']);
		$arr['real_name'] = trim($_POST['real_name']);
		$arr['user_phone'] = trim($_POST['user_phone']);
		$arr['province'] = trim($_POST['province']);
		$arr['city'] = trim($_POST['city']);
		$arr['area'] = trim($_POST['area']);
		$arr['address'] = trim($_POST['s_address']);
		$arr['sex'] = trim($_POST['sex']);
		$arr['age'] = trim($_POST['age']);
		$arr['identity_id'] = trim($_POST['identity_id']);
		$arr['from'] = trim($_POST['from']);
		$arr['s_password'] = md5('xihaxueche');
		$arr['i_user_type'] = 0;
		$arr['i_status'] = 0;
		$arr['content'] = '欢迎来到嘻哈学车的世界！';

		$config = array(
		    "savePath" => "upload/user/" ,             //存储文件夹
		    "maxSize" => 2000 ,                   //允许的文件最大尺寸，单位KB
		    "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
		);
		//上传文件目录
		$Path = "../upload/user/";

		//背景保存在临时目录中
		$config[ "savePath" ] = $Path;
		$up = new Uploader("_user_photo" , $config);

		$info = $up->getFileInfo();
		if($info['state'] == 'SUCCESS') {
			// 插入到数据库
			$arr['user_photo'] = $info['url'];

			$res = $mmember->insertUserInfo($arr);
			if($res == 1) {
				echo "<script>alert('号码已被注册');location.href='index.php?action=member&op=add';</script>";
				exit();
			} else if($res == 5) { 
				echo "<script>alert('身份证已被注册');location.href='index.php?action=member&op=add';</script>";
				exit();
			} else if($res == 2){
				echo "<script>alert('添加成功！');location.href='index.php?action=member&op=index';</script>";
				exit();
			} else if($res == 6) {
				echo "<script>alert('订单号已存在！');location.href='index.php?action=member&op=index';</script>";
				exit();
			} else if($res == 7) {
				echo "<script>alert('学员添加成功，但添加订单失败！请重新添加订单');location.href='index.php?action=member&op=index';</script>";
				exit();
			} else {
				echo "<script>alert('添加失败！');location.href='index.php?action=member&op=add';</script>";
				exit();
			}
		} else {
			echo "<script>alert('请添加头像');location.href='index.php?action=member&op=add';</script>";
			exit();
		}

	}else if($op == 'edit') {

		$id = !empty($_REQUEST['id']) ? max(1, intval($_REQUEST['id'])) : 1;
		$memberinfo = $mmember->getMemberInfo($id);
		$smarty->assign('memberinfo', $memberinfo);
		$smarty->display('member/edit.html');
	// 搜索
	} else if($op == 'search') {
		$conditiontype = isset($_REQUEST['conditiontype']) ? trim($_REQUEST['conditiontype']) : '';
		$onlinetype = isset($_REQUEST['onlinetype']) ? trim($_REQUEST['onlinetype']) : '';
		$keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$member_num = count($mmember->getSearchMemberList('','', $conditiontype, $onlinetype, $keyword)); //总数量
		$calcpagecnt = calcpagecnt($member_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $member_num, 'index.php?action=member', array('op'=>'search', 'conditiontype'=>$conditiontype, 'onlinetype'=>$onlinetype, 'keyword'=>$keyword));
		$member_list = $mmember->getSearchMemberList($pagestart, $limit, $conditiontype, $onlinetype, $keyword);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('member_num', $member_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('member_list', $member_list);
		$smarty->assign('conditiontype', $conditiontype);
		$smarty->assign('onlinetype', $onlinetype);
		$smarty->assign('keyword', $keyword);
		$smarty->display('member/search.html');
	
	// 删除学员档案
	} else if($op == 'del') {
		$id = isset($_POST['id']) ? trim($_POST['id']) : 0;
		$res = $mmember->delMember($id);
		if($res) {
			echo 1;
		} else {
			echo 2;
		}
	// 删除全部
	} else if($op == 'delmore') {
		$check_id = isset($_POST['check_id']) ? trim($_POST['check_id']) : 0;
		$res = $mmember->deleteMoreMember($check_id);
		if($res) {
			echo 1;
			exit;
		} else {
			echo 2;
			exit();
		}
	}		
?>
