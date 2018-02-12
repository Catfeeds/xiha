<?php  

	// 教练管理

	header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','add','addoperate','edit','editoperate','del','online','savetime','timeconfig','delpretime','delalltime','delcoach','delmore','coachinfo','getcurrenttime','getdateappoint','appointinfo','searchcoach','searchstudent','studentlist')) ? $op : 'index';

	if(!isset($_SESSION['school_loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}
	$loginauth_str = authcode($_SESSION['school_loginauth'], 'DECODE');
	$loginauth_arr = explode('\t', $loginauth_str);
	$school_id = $loginauth_arr[2];

	$mcoach = new mcoach($db);
	$mschool = new mschool($db);
	$mcar = new mcar($db);
	// $school_id = 1;
	if($op == 'index') {
		// 获取驾校列表
		$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'default';
		$order = isset($_REQUEST['order']) ? trim($_REQUEST['order']) : 'desc';

		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$coach_num = count($mcoach->getCoachlist('', '', $school_id)); //总数量
		$calcpagecnt = calcpagecnt($coach_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $coach_num, 'index.php?action=coach', array('op'=>'index', 'type'=>$type, 'order'=>$order));

		$coach_list = $mcoach->getCoachlist($pagestart, $limit, $school_id, $type, $order);

		$smarty->assign('type', $type);
		$smarty->assign('order', $order);
		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('coach_num', $coach_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('coach_list', $coach_list);

		$smarty->display('coach/index.html');

	//更改在线状态 
	} else if($op == 'online') {
		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$ret = 1;
		$res = $mcoach->setCoachOnlineStatus($id);
		if(!$res) {
			$ret = 0;
		}
		echo $ret;
		exit();

	} else if($op == 'add') {

		// 获取所有省份列表
		$provincelist = $mschool->getProvinceList();

		// 获取当前驾校列表
		$school_list = $mschool->getSchoollist();

		// 获取教练详情
		$school_detail = $mschool->getSchoolDetail($school_id);
		// print_r($school_detail);

		// 获得当前车辆列表
		$car_list = $mcar->getCarList('','',$school_id);

		// 获取所有省份列表
		$provincelist = $mschool->getProvinceList();
		$smarty->assign('provincelist', $provincelist);
		$smarty->assign('school_detail', $school_detail);
		$smarty->assign('school_list', $school_list);
		$smarty->assign('car_list', $car_list);
		$smarty->assign('lisence_config', $lisence_config);
		$smarty->assign('lesson_config', $lesson_config);
		$smarty->display('coach/add.html');

	//添加操作 
	} else if($op == 'addoperate') {

		$arr = array();
		$arr['s_coach_name'] = $_POST['s_coach_name'];
		$arr['s_coach_phone'] = $_POST['s_coach_phone'];
		$arr['s_coach_sex'] = $_POST['s_coach_sex'];
		$arr['s_teach_age'] = $_POST['s_coach_age'];
		$arr['s_school_name_id'] = $school_id;
		$arr['s_coach_lesson_id'] = implode(',', $_POST['lesson_id']);
		$arr['s_coach_lisence_id'] = implode(',', $_POST['lisence_id']);

		$arr['s_coach_car_id'] = $_POST['coach_school_car'];
		$arr['province_id'] = $_POST['province'];
		$arr['city_id'] = $_POST['city'];
		$arr['area_id'] = $_POST['area'];
		$arr['s_address'] = $_POST['s_address'];
		$arr['is_online'] = $_POST['is_online'];
		$arr['i_type'] = $_POST['coach_type'];

		$s_coach_imgurl = $_FILES['coach_img'];
		if($s_coach_imgurl['error'] == 0) {
			$config = array(
			    "savePath" => "upload/coach/" ,             //存储文件夹
			    "maxSize" => 300 ,                   //允许的文件最大尺寸，单位KB
			    "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
			);
			//上传文件目录
			// $Path = "../upload/coach/";
			$Path = "../upload/coach/".$school_id.'/';
			//背景保存在临时目录中
			$config["savePath"] = $Path;
			$up = new Uploader("coach_img" , $config);
			$info = $up->getFileInfo();
			if($info['state'] == 'SUCCESS') {
				// 压缩图片到100*100
				$show_pic_scal = $up->showPicScal($info['url'], 100, 100);
				$thumbpath = $up->resize($info['url'], $show_pic_scal[0], $show_pic_scal[1], 2);
				$arr['s_coach_original_imgurl'] = $info['url'];
				$arr['s_coach_imgurl'] = $thumbpath;
			} else {
				echo "<script>alert('".$info['state']."');history.back(-1);</script>";
				exit();
			}
		} else {
			$arr['s_coach_original_imgurl'] = $_POST['oldimg'];
			$arr['s_coach_imgurl'] = $_POST['oldimg'];
		}

		if($arr['s_coach_car_id'] == '') {
			echo "<script>alert('请绑定车辆');location.href='index.php?action=coach&op=add;</script>";
			exit();
		}

		// 更新教练信息
		$res = $mcoach->insertCoachInfo($arr);
		if($res) {
			echo "<script>alert('添加成功！');location.href='index.php?action=coach&op=index';</script>";
			exit();
		} else if($res == 1) {
			echo "<script>alert('教练号码重复！');location.href='index.php?action=coach&op=index';</script>";
			exit();
		} else {
			echo "<script>alert('添加失败！');location.href='index.php?action=coach&op=index';</script>";
			exit();
		}

	// ajax保存时间
	} else if($op == 'savetime') {

		$time_config_id = !empty($_POST['time_config_id']) ? $_POST['time_config_id'] : array(); // 选择的时间段
		$lisence_no = !empty($_POST['lisence_no']) ? $_POST['lisence_no'] : array();
		$subjects = !empty($_POST['subjects']) ? $_POST['subjects'] : array();
		$single_price = !empty($_POST['single_price']) ? $_POST['single_price'] : array();
		$time_config_ids = !empty($_POST['time_config_ids']) ? $_POST['time_config_ids'] : array(); // 全部时间段ID
		$currentdate = isset($_POST['currentdate']) ? $_POST['currentdate'] : date('Y-m-d', time()); //日期
		$l_coach_id = isset($_POST['l_coach_id']) ? $_POST['l_coach_id'] : 0;

		if(empty($time_config_id) || empty($lisence_no) || empty($subjects) || empty($single_price) || empty($time_config_ids)) {
			$ret['code'] = 3;
			echo json_encode($ret);
			exit();
		}

		$date_config_arr = explode('-', $currentdate);
		$arr['year'] = $date_config_arr[0];   // 年
		$arr['month'] = $date_config_arr[1];  // 月
		$arr['day'] = $date_config_arr[2];    // 日

		$lisence_no_arr = array();
		$subjects_arr = array();
		$single_price_arr = array();

		if(is_array($lisence_no)) {
			foreach ($lisence_no as $key => $value) {
				$_stmt = explode('|', $value);
				$lisence_no_arr[$_stmt[0]] = $_stmt[1];
			}
		}
		if(is_array($subjects)) {
			foreach ($subjects as $key => $value) {
				$_stmt = explode('|', $value);
				$subjects_arr[$_stmt[0]] = $_stmt[1];
			}
		}
		$single_price_arr = array_combine($time_config_ids, $single_price);

		foreach ($time_config_id as $key => $value) {
			$time_money_config[$value] = $single_price_arr[$value];
			$time_subjects_config[$value] = $subjects_arr[$value];
			$time_lisence_config[$value] = $lisence_no_arr[$value];
		}
		foreach ($time_money_config as $key => $value) {
			if(!is_numeric($value)) {
				$ret['code'] = 2;
				echo json_encode($ret);
				exit();
			}
		}
		$arr['time_config_money_id'] = json_encode($time_money_config);
		$arr['time_lisence_config_id'] = json_encode($time_lisence_config);
		$arr['time_lesson_config_id'] = $mcoach->JSON($time_subjects_config);
		$arr['current_time'] = strtotime($currentdate); // 当前时间
		$arr['time_config_id'] = implode(',', $time_config_id);
		$arr['coach_id'] = $l_coach_id;

		// 更新数据
		$res = $mcoach->updateCoachTime($arr);
		echo json_encode($res);

	// 删除上一天时间段
	} else if($op == 'delpretime') {

		$coach_id = $_POST['coach_id'];
		$pre_time = strtotime('-1 day');

		$res = $mcoach->delPreTime($coach_id);
		if($res) {
			$ret['code'] = 1;
			echo json_encode($ret);
			exit();
		} else {
			$ret['code'] = 0;
			echo json_encode($ret);
			exit();
		}

	// 删除所有时间配置数据 
	} else if($op == 'delalltime') {
		$coach_id = $_POST['coach_id'];
		$res = $mcoach->delAllTime($coach_id);
		if($res) {
			$ret['code'] = 1;
			echo json_encode($ret);
			exit();
		} else {
			$ret['code'] = 0;
			echo json_encode($ret);
			exit();
		}

	// 保存修改教练数据
	} else if($op == 'edit') {
		
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

		// 获取教练详情
		$coachdetail = $mcoach->getCoachDetail($id);

		// 获取当前所在的城市
		if($coachdetail['city_id']) {
			$cityinfo = $mschool->getCityDetail($coachdetail['city_id']);
			$smarty->assign('cityinfo', $cityinfo);
		}

		// 获取当前所在的区域
		if($coachdetail['area_id']) {
			$areainfo = $mschool->getAreaDetail($coachdetail['area_id']);
			$smarty->assign('areainfo', $areainfo);
		}

		// 获取所有省份列表
		$provincelist = $mschool->getProvinceList();

		// 获取当前驾校列表
		$school_list = $mschool->getSchoollist();

		// 获得当前车辆列表
		$car_list = $mcar->getCarList('','',$school_id);

		$smarty->assign('id', $id);
		$smarty->assign('provincelist', $provincelist);
		$smarty->assign('school_list', $school_list);
		$smarty->assign('car_list', $car_list);
		$smarty->assign('coachdetail', $coachdetail);
		$smarty->assign('lisence_config', $lisence_config);
		$smarty->assign('lesson_config', $lesson_config);
		$smarty->display('coach/edit.html');
	
	// 编辑操作
	} else if($op == 'editoperate') {

		$arr = array();
		$arr['l_coach_id'] = $_POST['l_coach_id'];
		$arr['s_coach_name'] = $_POST['s_coach_name'];
		$arr['s_coach_phone'] = $_POST['s_coach_phone'];
		$arr['s_coach_sex'] = $_POST['s_coach_sex'];
		$arr['s_teach_age'] = $_POST['s_coach_age'];
		$arr['s_school_name_id'] = $school_id;
		$arr['s_coach_lesson_id'] = implode(',', $_POST['lesson_id']);
		$arr['s_coach_lisence_id'] = implode(',', $_POST['lisence_id']);

		$arr['s_coach_car_id'] = $_POST['coach_school_car'];
		$arr['province_id'] = $_POST['province'];
		$arr['city_id'] = $_POST['city'];
		$arr['area_id'] = $_POST['area'];
		$arr['s_address'] = $_POST['s_address'];
		$arr['is_online'] = $_POST['is_online'];
		$arr['i_type'] = $_POST['coach_type'];

		$s_coach_imgurl = $_FILES['coach_img'];

		if($arr['s_coach_car_id'] == '') {
			echo "<script>alert('请绑定车辆');location.href='index.php?action=coach&op=edit&id=".$arr['l_coach_id']."';</script>";
			exit();
		}

		if($s_coach_imgurl['error'] == 0) {
			$config = array(
			    "savePath" => "upload/coach/" ,             //存储文件夹
			    "maxSize" => 300 ,                   //允许的文件最大尺寸，单位KB
			    "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
			);
			//上传文件目录
			$Path = "../upload/coach/".$school_id.'/';

			//背景保存在临时目录中
			$config["savePath"] = $Path;
			$up = new Uploader("coach_img" , $config);
			$info = $up->getFileInfo();
			if($info['state'] == 'SUCCESS') {
				// 压缩图片到100*100
				$show_pic_scal = $up->showPicScal($info['url'], 100, 100);
				$thumbpath = $up->resize($info['url'], $show_pic_scal[0], $show_pic_scal[1], 2);
				$arr['s_coach_original_imgurl'] = $info['url'];
				$arr['s_coach_imgurl'] = $thumbpath;
			} else {
				echo "<script>alert('".$info['state']."');history.back(-1);</script>";
				exit();
			}
		} else {
			$arr['s_coach_imgurl'] = $_POST['oldimg'];
			$arr['s_coach_original_imgurl'] = $_POST['original_oldimg'];
		}


		// 更新教练信息
		$res = $mcoach->updateCoachInfo($arr);
		if($res) {
			echo "<script>alert('更新成功！');location.href='index.php?action=coach&op=index';</script>";
			exit();
		} else {
			echo "<script>alert('更新失败！');location.href='index.php?action=coach&op=index';</script>";
			exit();
		}


	} else if($op == 'delcoach') {
		$id = $_POST['id'];
		$res = $mcoach->deleteCoach($id); 
		if($res) {
			$ret = 1;
		} else {
			$ret = 0;
		}
		echo $ret;
		exit();

	} else if($op == 'delmore') {
		$id = $_POST['check_id'];
		$res = $mcoach->deleteCoach($id);
		if($res) {
			$ret['code'] = 1;
			echo json_encode($ret);
			exit();
		} else {
			$ert['code'] = 0;
			echo json_encode($ret);
			exit();
		}

	// 展示弹出层
	} else if($op == 'coachinfo') {

		$coach_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

		// 获取教练详情
		$coachdetail = $mcoach->getCoachDetail($coach_id);

		// 获取所能培训牌照
		if($coachdetail['s_coach_lisence_id']) {
			$s_coach_lisence_name = array();
			$s_coach_lisence_id = explode(',', $coachdetail['s_coach_lisence_id']);
			foreach ($s_coach_lisence_id as $key => $value) {
				$s_coach_lisence_name[] = $lisence_config[$value];
			}
		} else {
			$s_coach_lisence_name[] = array();
		}
		$lisence_name = implode(',', $s_coach_lisence_name);

		// 获取所能培训科目
		if($coachdetail['s_coach_lesson_id'] && $lesson_config) {
			$s_coach_lesson_name = array();
			$s_coach_lesson_id = explode(',', $coachdetail['s_coach_lesson_id']);
			foreach ($s_coach_lesson_id as $key => $value) {
				if(isset($lesson_config[$value])) {
					$s_coach_lesson_name[] = $lesson_config[$value];
				}
					
			}
		} else {
			$s_coach_lesson_name[] = array();
		}
		$lesson_name = implode(',', $s_coach_lesson_name);

		// 获取所属车辆
		if($coachdetail['s_coach_car_id']) {
			$carinfo = $mcar->getCarById($coachdetail['s_coach_car_id']);
		} else {
			$carinfo = array();
		}

		$smarty->assign('coachdetail', $coachdetail);
		$smarty->assign('coach_id', $coach_id);
		$smarty->assign('lisence_name', $lisence_name);
		$smarty->assign('carinfo', $carinfo);
		$smarty->assign('lesson_name', $lesson_name);
		$smarty->display('coach/coachinfo.html');	

	// 时间配置
	} else if($op == 'timeconfig') {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

		// 获取教练详情
		$coachdetail = $mcoach->getCoachDetail($id);
		$lesson_arr = array();
		$lisence_arr = array();
		$lesson_name_arr = array();
		$lisence_name_arr = array();

		if($coachdetail) {
			$lesson_arr = explode(',', $coachdetail['s_coach_lesson_id']);
			$lisence_arr = explode(',', $coachdetail['s_coach_lisence_id']);	
		}
		foreach ($lesson_config as $key => $value) {
			if(in_array($key, $lesson_arr)) {
				$lesson_name_arr[$key] = $value;
			}
		}
		foreach ($lisence_config as $key => $value) {
			if(in_array($key, $lisence_arr)) {
				$lisence_name_arr[$key] = $value;
			}
		}

		// 获得时间配置每天都不同
		// $school_id = 1;
		$coach_time_config = $mcoach->getCoachTimeConfig($school_id, $id); // 默认时间配置
		$smarty->assign('id', $id);
		$smarty->assign('coach_time_config', $coach_time_config);
		$smarty->assign('coachdetail', $coachdetail);
		$smarty->assign('lesson_name_arr', $lesson_name_arr);
		$smarty->assign('lisence_name_arr', $lisence_name_arr);
		$smarty->assign('lisence_config', $lisence_config);
		$smarty->assign('lesson_config', $lesson_config);
		$smarty->display('coach/timeconfig.html');

	// ajax获取当前日期的时间配置
	} else if($op == 'getcurrenttime') {
		
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$date = isset($_REQUEST['date']) ? trim($_REQUEST['date']) : date('Y-m-d', time());

		$current_time_config = $mcoach->getCurrentCoachTimeConfigIdByDate($date, $id, $school_id); // 教练时间配置
		// $current_time_config = $mcoach->getCoachCurrentTimeConfig(1, $id, $date); // 教练时间配置
		// echo "<pre>";
		// print_r($current_time_config);
		$data = array('code'=>200, 'data'=>$current_time_config);
		echo json_encode($data);
	
	//获取当前教练的学员列表
	} else if($op == 'studentlist') {

		$coach_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$conditiontype = isset($_POST['conditiontype']) ? trim($_POST['conditiontype']) : ''; // 学员条件

		$limit = 20;
		$pagestart = ($page - 1) * $limit;
		$student_num = count($mcoach->getStudentList('','',$coach_id)); //总数量
		$calcpagecnt = calcpagecnt($student_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $student_num, 'index.php?action=coach', array('op'=>'studentlist', 'id'=>$coach_id));

		$student_list = $mcoach->getStudentList($pagestart, $limit, $coach_id);
		// var_dump($student_list);
		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('student_num', $student_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('student_list', $student_list);
		$smarty->assign('coach_id', $coach_id);
		$smarty->assign('conditiontype', $conditiontype);
		$smarty->display('coach/studentlist.html');	
		
	//教练详情中学员列表查询
	} else if($op == 'searchstudent') {
		$coach_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		// echo $coach_id;
		$conditiontype = isset($_POST['conditiontype']) ? trim($_POST['conditiontype']) : 1; // 学员条件
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;
		$student_list_num = count($mcoach->getSearchStuList('','',$coach_id, $conditiontype)); //总数量
		$calcpagecnt = calcpagecnt($student_list_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $student_list_num, 'index.php?action=coach', array('op'=>'searchstudent'));
		$student_list = $mcoach->getSearchStuList($pagestart, $limit, $coach_id, $conditiontype);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('student_list_num', $student_list_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('student_list', $student_list);
		$smarty->assign('coach_id', $coach_id);
		$smarty->assign('conditiontype', $conditiontype);
		$smarty->display('coach/studentlist.html');	

	 // ajax获取预约详情
	} else if($op == 'getdateappoint') {
		$coach_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$date = isset($_REQUEST['date']) ? trim($_REQUEST['date']) : date('m-d', time());

		$current_time_config = $mcoach->getCoachCurrentTimeConfig($school_id, $coach_id, $date); // 教练时间配置

		if($current_time_config == 1) {
			$data = array('code'=>-1, 'data'=>'');
		} else if($current_time_config == 2) {
			$data = array('code'=>-2, 'data'=>'');
		} else {
			$data = array('code'=>200, 'data'=>$current_time_config);
		}
		echo json_encode($data);

	// 预约详情
	} else if($op == 'appointinfo') {
		
		// 获取当前教练的时间配置
		$coach_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$date = isset($_REQUEST['date']) ? trim($_REQUEST['date']) : date('Y-m-d', time());
		// $school_id = 1;
		$current_time_config = $mcoach->getCoachCurrentTimeConfig($school_id, $coach_id, $date);
		$smarty->assign('current_time_config', $current_time_config);
		$smarty->assign('coach_id', $coach_id);
		$smarty->assign('date', $date);
		$smarty->display('coach/appointinfo.html');

	// 搜索教练
	} else if($op == 'searchcoach') {
		$keywords 		= isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
		$conditiontype 	= isset($_REQUEST['conditiontype']) ? trim($_REQUEST['conditiontype']) : '';
		$type 			= isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'default';
		$page 			= !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$order 			= isset($_REQUEST['order']) ? trim($_REQUEST['order']) : 'desc';

		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$coach_num = count($mcoach->getSearchCoachlist('', '', $school_id, $conditiontype, $type, $order, $keywords)); //总数量
		$calcpagecnt = calcpagecnt($coach_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $coach_num, 'index.php?action=coach', array('op'=>'searchcoach', 'keyword'=>$keywords, 'conditiontype'=>$conditiontype));

		$coach_list = $mcoach->getSearchCoachlist($pagestart, $limit, $school_id, $conditiontype, $type, $order, $keywords);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('coach_num', $coach_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('coach_list', $coach_list);
		$smarty->assign('keywords', $keywords);
		$smarty->assign('conditiontype', $conditiontype);
		$smarty->assign('type', $type);
		$smarty->assign('order', $order);

		$smarty->display('coach/searchcoach.html');
	}

?>
