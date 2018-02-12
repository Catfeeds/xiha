<?php  
	// 会员模块
	header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');

	$op = in_array($op, array('index','add','addoperate','edit','del','editoperate','examsearch','delmore','phonecheck','identitycheck','collection','collectdel','collectdelmore','restorecollectuser','restoremoreuser','createno','checkno','search','show','searchappoint','examrecord')) ? $op : 'index';

	if(!isset($_SESSION['school_loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}
	$loginauth_str = authcode($_SESSION['school_loginauth'], 'DECODE');
	$loginauth_arr = explode('\t', $loginauth_str);
	$school_id = $loginauth_arr[2];

	$mmember = new mmember($db);
	$mschool = new mschool($db);

	if($op == 'index') {
		
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;
		$member_num = count($mmember->getMemberList('','',$school_id, '')); //总数量
		$calcpagecnt = calcpagecnt($member_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $member_num, 'index.php?action=member', array('op'=>'index'));

		$member_list = $mmember->getMemberList($pagestart, $limit, $school_id, '');
		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('member_num', $member_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('member_list', $member_list);
		$smarty->display('member/index.html');

	} else if($op == 'add') {

		// 获取所有省份列表
		$provincelist = $mschool->getProvinceList();
		// 获取班制信息
		$shifts_list = $mschool->getShiftsList($school_id);
		$smarty->assign('lisence_config', $lisence_config);
		$smarty->assign('provincelist', $provincelist);
		$smarty->assign('shifts_list', $shifts_list);
		$smarty->display('member/add.html');

	// 添加学员操作
	} else if($op == 'addoperate') {

		$arr = array();
		$arr['school_id'] = trim($school_id);
		$arr['user_name'] = trim($_POST['user_name']);
		$arr['real_name'] = trim($_POST['real_name']);
		$arr['user_phone'] = trim($_POST['user_phone']);
		$arr['province'] = trim($_POST['province']);
		$arr['city'] = trim($_POST['city']);
		$arr['area'] = trim($_POST['area']);
		$arr['address'] = trim($_POST['s_address']);
		$arr['sex'] = trim($_POST['sex']);
		//$arr['age'] = trim($_POST['age']);
        //根据身份证计算学员年龄 gdc 2016-04-12
        $arr['age'] = date('Y', time()) - substr(trim($_POST['identity_id']), 6, 4);
		$arr['learncar_status'] = trim($_POST['learncar_status']);
		$arr['identity_id'] = trim($_POST['identity_id']);
		$arr['from'] = trim($_POST['from']);
		$arr['s_password'] = md5('123456');  // 学员登陆密码
		$arr['i_user_type'] = 0;
		$arr['i_status'] = 0;
		$arr['content'] = '欢迎来到嘻哈学车的世界！';

		// 驾校订单
		$arr['so_final_price'] = trim($_POST['so_final_price']);
		$arr['so_original_price'] = trim($_POST['so_original_price']);
		$arr['so_shifts_id'] = trim($_POST['so_shifts_id']);
		$arr['so_pay_type'] = trim($_POST['so_pay_type']);
		$arr['so_order_status'] = trim($_POST['so_order_status']);
		$arr['so_order_no'] = trim($_POST['so_order_no']);
		$arr['so_licence'] = trim($_POST['so_licence']);
		$arr['so_comment_status'] = 1;

		$config = array(
		    "savePath" => "upload/user/" ,             //存储文件夹
		    "maxSize" => 2000 ,                   //允许的文件最大尺寸，单位KB
		    "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
		);
		//上传文件目录
		$Path = "../upload/user/";

		//背景保存在临时目录中
		$config[ "savePath" ] = $Path;
		$up = new Uploader("user_photo" , $config);

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

	} else if($op == 'edit') {

		$id = !empty($_REQUEST['id']) ? max(1, intval($_REQUEST['id'])) : 1;
		$memberinfo = $mmember->getMemberInfo($id);
		$provincelist = $mschool->getProvinceList();
		$arealist = $mschool->getAreaList($memberinfo['city_id']);
		$citylist = $mschool->getCityList($memberinfo['province_id']);

		$smarty->assign('citylist'		, $citylist);
		$smarty->assign('arealist'		, $arealist);
		$smarty->assign('provincelist', $provincelist);
		$smarty->assign('memberinfo', $memberinfo);
		$smarty->display('member/edit.html');

	// 更新
	} else if($op == 'editoperate') {

		$arr = array();
		$arr['l_user_id'] = trim($_POST['user_id']);
		$arr['school_id'] = $school_id;
		$arr['s_username'] = trim($_POST['s_username']);
		$arr['s_real_name'] = trim($_POST['s_real_name']);
		$arr['s_phone'] = trim($_POST['s_phone']);
		$arr['i_from'] = $_POST['from'];
		$arr['province_id'] = trim($_POST['province']);
		$arr['city_id'] = trim($_POST['city']);
		$arr['area_id'] = trim($_POST['area']);
		$arr['learncar_status'] = trim($_POST['learncar_status']);
		$arr['s_address'] = trim($_POST['s_address']);
		$arr['identity_id'] = trim($_POST['identity_id']);
		$arr['sex'] = trim($_POST['sex']);
		//$arr['age'] = trim($_POST['age']);
		//$arr['age'] = trim($_POST['age']);
        //根据身份证计算学员年龄 gdc 2016-04-12
        $arr['age'] = date('Y', time()) - substr(trim($_POST['identity_id']), 6, 4);

		$user_photo = $_FILES['user_photo'];

		if($user_photo['error'] == 0) {
			$config = array(
			    "savePath" => "upload/user/" ,             //存储文件夹
			    "maxSize" => 2000 ,                   //允许的文件最大尺寸，单位KB
			    "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
			);
			//上传文件目录
			$Path = "../upload/user/";

			//背景保存在临时目录中
			$config[ "savePath" ] = $Path;
			$up = new Uploader("user_photo" , $config);
			$info = $up->getFileInfo();

			if($info['state'] == 'SUCCESS') {
				$filename = $info['url'];
			} else {
				echo "<script>alert('请添加头像');location.href='index.php?action=member&op=edit&id='".$arr['l_user_id'].";</script>";
				exit();
			}
		} else {
			$filename = $_POST['oldimg'];
		}

		$arr['user_photo'] = $filename;
		$res = $mmember->updateUserInfo($arr);
		if($res) {
			echo "<script>alert('修改成功！');location.href='index.php?action=member&op=index';</script>";
			exit();
		} else {
			echo "<script>alert('修改失败！');location.href='index.php?action=member&op=edit&id=".$arr['l_user_id']."';</script>";
			exit();
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

	// 检测手机号
	} else if($op == 'phonecheck') {
		$phone = isset($_POST['phone']) ? trim($_POST['phone']) : 0;
		$res = $mmember->getPhoneCheck($phone);
		if($res) {
			echo 1;
		} else {
			echo 2;
		}

	// 检测身份证
	} else if($op == 'identitycheck') {
		$identity_id = isset($_POST['identity_id']) ? trim($_POST['identity_id']) : 0;
		$res = $mmember->getIdentityCheck($identity_id);
		if($res) {
			echo 1;
		} else {
			echo 2;
		}

	// 删除学员档案
	} else if($op == 'del') {
		$id = isset($_POST['id']) ? trim($_POST['id']) : 0;
		$res = $mmember->delMember($id);
		if($res) {
			echo 1;
		} else {
			echo 2;
		}

	// 学员回收站
	} else if($op == 'collection') {

		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 10;
		$pagestart = ($page - 1) * $limit;
		$member_num = count($mmember->getMemberList('','',$school_id, 2)); //总数量
		$calcpagecnt = calcpagecnt($member_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $member_num, 'index.php?action=member', array('op'=>'collection'));

		$member_list = $mmember->getMemberList($pagestart, $limit, $school_id, 2);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('member_num', $member_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('member_list', $member_list);
		$smarty->display('member/collection.html');

	// 彻底删除
	} else if($op == 'collectdel') {
		$id = isset($_REQUEST['id']) ? trim($_POST['id']) : 0;
		$res =  $mmember->deleteUserInfo($id);
		if($res) {
			echo 1;
			exit();
		} else {
			echo 2;
			exit();
		}

	// 彻底删除多个 
	} else if($op == 'collectdelmore') {
		$check_id = isset($_POST['check_id']) ? trim($_POST['check_id']) : 0;
		$res = $mmember->deleteUserInfo($check_id);
		if($res) {
			echo 1;
			exit();
		} else {
			echo 2;
			exit();
		}

	// 还原用户
	} else if($op == 'restorecollectuser') {
		$id = !empty($_REQUEST['id']) ? max(1, intval($_REQUEST['id'])) : 0;
		$res = $mmember->restoreUserInfo($id);
		if($res) {
			echo 1;
			exit();
		} else {
			echo 2;
			exit();
		}

	// 还原多个用户
	} else if($op == 'restoremoreuser') {
		$check_id = isset($_POST['check_id']) ? trim($_POST['check_id']) : 0;
		$res = $mmember->restoreUserInfo($check_id);
		if($res) {
			echo 1;
			exit();
		} else {
			echo 2;
			exit();
		}

	// 创建订单号
	} else if($op == 'createno') {
		$s_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
		$data = array('code'=>200, 'data'=>$s_order_no);
		echo json_encode($data);
		exit();

	// 订单号检测
	} else if($op == 'checkno') {
		$no = isset($_POST['no']) ? trim($_POST['no']) : 0;
		$res = $mmember->getOrdernoCheck($no);
		if($res) {
			$data = array('code'=>-1, 'data'=>'有重复订单号');
		} else {
			$data = array('code'=>1, 'data'=>'没有重复订单号');
		}
		echo json_encode($data);

	// 搜索
	} else if($op == 'search') {
		$conditiontype = isset($_REQUEST['conditiontype']) ? trim($_REQUEST['conditiontype']) : '';
		$onlinetype = isset($_REQUEST['onlinetype']) ? trim($_REQUEST['onlinetype']) : '';
		$keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$member_num = count($mmember->getSearchMemberList('','',$school_id, $conditiontype, $onlinetype, $keyword)); //总数量
		$calcpagecnt = calcpagecnt($member_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $member_num, 'index.php?action=member', array('op'=>'search', 'conditiontype'=>$conditiontype, 'onlinetype'=>$onlinetype, 'keyword'=>$keyword));
		$member_list = $mmember->getSearchMemberList($pagestart, $limit, $school_id, $conditiontype, $onlinetype, $keyword);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('member_num', $member_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('member_list', $member_list);
		$smarty->assign('conditiontype', $conditiontype);
		$smarty->assign('onlinetype', $onlinetype);
		$smarty->assign('keyword', $keyword);
		$smarty->display('member/search.html');

	// 展示所有学员相关信息
	} else if($op == 'show') {
		$id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : 0;
		$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'appoint';
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$conditiontype = isset($_REQUEST['conditiontype']) ? trim($_REQUEST['conditiontype']) : '';
		$limit = 10;
		$pagestart = ($page - 1) * $limit;
		$_memberinfo = $mmember->showMemberInfo($school_id, $id, $type, $conditiontype, '', '');
		// var_dump($_memberinfo);exit;
		$member_num = count($_memberinfo['orderlist']); //总数量
		$calcpagecnt = calcpagecnt($member_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $member_num, 'index.php?action=member', array('op'=>'show', 'type'=>$type, 'conditiontype'=>$conditiontype, 'id'=>$id));

		$memberinfo = $mmember->showMemberInfo($school_id, $id, $type, $conditiontype, $pagestart, $limit);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('member_num', $member_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //分布结果
		$smarty->assign('memberinfo', $memberinfo);
		$smarty->assign('conditiontype', $conditiontype);
		$smarty->assign('member_id', $id);
		$smarty->assign('type', $type);
		if($type == 'appoint') {
			$smarty->display('member/show.html');
		} elseif ( $type == 'signup' ) {
			$smarty->display('member/showsignup.html');
		} elseif ( $type == 'exam' ) {
			$smarty->display('member/exam.html');
        }

	} else if($op == 'examrecord') {
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$conditiontype = !empty($_REQUEST['conditiontype']) ? trim($_REQUEST['conditiontype']) : 1;

		$limit = 20;
		$pagestart = ($page - 1) * $limit;
		$examrecords = array();
		$examrecords = $mmember->getExamRecords($school_id, $pagestart, $limit);
		$examrecords_num = count($mmember->getExamRecords($school_id)); //总数量
		$calcpagecnt = calcpagecnt($examrecords_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $examrecords_num, 'index.php?action=member', array('op'=>'examrecord'));

		$smarty->assign('examrecords', $examrecords); //分页数
		$smarty->assign('examrecords_num', $examrecords_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('conditiontype', $conditiontype); //总条数
		$smarty->display('member/examrecord.html');
  
	} else if($op == 'examsearch') {
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$keyword = !empty($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
		$conditiontype = !empty($_REQUEST['conditiontype']) ? trim($_REQUEST['conditiontype']) : 1;

		$limit = 10;
		$pagestart = ($page - 1) * $limit;
		$examrecords = array();
		$examrecords = $mmember->getSearchExamRecords($school_id, $pagestart, $limit, $conditiontype, $keyword);
		$examrecords_num = count($mmember->getSearchExamRecords($school_id, '', '', $conditiontype, $keyword)); //总数量
		$calcpagecnt = calcpagecnt($examrecords_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $examrecords_num, 'index.php?action=member', array('op'=>'examsearch', 'keyword'=>$keyword, 'conditiontype'=>$conditiontype));

		$smarty->assign('examrecords', $examrecords); //分页数
		$smarty->assign('examrecords_num', $examrecords_num); //总条数
		$smarty->assign('conditiontype', $conditiontype); //总条数
		$smarty->assign('keyword', $keyword); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->display('member/examsearch.html');
	}


?>
