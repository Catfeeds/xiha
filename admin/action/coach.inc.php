<?php  

	// 教练管理

	header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','add','addoperate','edit','editoperate','del','online','savetime','delpretime','delalltime','delcoach','delmore','showiframe','savename','savephone', 'searchcoach')) ? $op : 'index';

	if(!isset($_SESSION['loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}

	$mcoach = new mcoach($db);
	$mschool = new mschool($db);
	$mcar = new mcar($db);

	if($op == 'index') {
		// 获取驾校列表
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 10;
		$pagestart = ($page - 1) * $limit;

		$coach_num = count($mcoach->getCoachlist()); //总数量
		$calcpagecnt = calcpagecnt($coach_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $coach_num, 'index.php?action=coach', array('op'=>'index'));

		$coach_list = $mcoach->getCoachlist($pagestart, $limit);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('coach_num', $coach_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('coach_list', $coach_list);

		$smarty->display('coach/index.html');

	//更改在线状态 
	} else if($op == 'online') {
		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$ret['code'] = 1;
		$res = $mcoach->setCoachOnlineStatus($id);
		if(!$res) {
			$ret['code'] = 0;
		}
		echo json_encode($ret);
		exit();

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

		// 获得所有车辆列表
		$car_list = $mcar->getCarList();

		// 获得时间配置每天都不同
		$coach_time_config = $mcoach->getCoachTimeConfig();

		$smarty->assign('id', $id);
		$smarty->assign('provincelist', $provincelist);
		$smarty->assign('school_list', $school_list);
		$smarty->assign('car_list', $car_list);
		$smarty->assign('coachdetail', $coachdetail);
		$smarty->assign('coach_time_config', $coach_time_config);
		$smarty->assign('lisence_config', $lisence_config);
		$smarty->assign('lesson_config', $lesson_config);
		$smarty->display('coach/edit.html');

	// 添加操作
	} else if($op == 'addoperate') {

		$arr = array();
		$arr['s_coach_name'] = $_POST['s_coach_name'];
		$arr['s_coach_phone'] = $_POST['s_coach_phone'];
		$arr['s_school_name_id'] = $_POST['coach_school_id'];
		$arr['s_coach_lesson_id'] = implode(',', $_POST['lesson_id']);
		$arr['s_coach_lisence_id'] = implode(',', $_POST['lisence_id']);

		$arr['s_coach_car_id'] = $_POST['coach_school_car'];
		$arr['province_id'] = $_POST['province'];
		$arr['city_id'] = $_POST['city'];
		$arr['area_id'] = $_POST['area'];
		$arr['s_address'] = $_POST['s_address'];
		$arr['is_online'] = $_POST['is_online'];
		// $arr['s_yh_name'] = $_POST['s_yh_name'];
		// $arr['s_yh_zhanghao'] = $_POST['s_yh_zhanghao'];
		// $arr['s_yh_huming'] = $_POST['s_yh_huming'];
		$arr['i_type'] = $_POST['coach_type'];

		$s_coach_imgurl = $_FILES['license_img'];

		if($s_coach_imgurl['error'] == 0) {
			// 上传图片
			$filename = uploadimg($s_coach_imgurl, 'upload/coach/', uniqid());
			if($filename == 1) {
				echo "<script>alert('上传失败！');location.href='index.php?action=coach&op=index;</script>";
				exit();
			}
		} else {
			$filename = $_POST['oldimg'];
		}

		$arr['s_coach_imgurl'] = $filename;

		// 更新教练信息
		$res = $mcoach->insertCoachInfo($arr);
		if($res) {
			echo "<script>alert('添加成功！');location.href='index.php?action=coach&op=index';</script>";
			exit();
		} else {
			echo "<script>alert('添加失败！');location.href='index.php?action=coach&op=index';</script>";
			exit();
		}

	} else if($op == 'add') {

		// 获取所有省份列表
		$provincelist = $mschool->getProvinceList();

		// 获取当前驾校列表
		$school_list = $mschool->getSchoollist();

		// 获得当前所有车辆列表
		$car_list = $mcar->getCarList();

		// 获得时间配置每天都不同
		$coach_time_config = $mcoach->getCoachTimeConfig();

		// 获取所有省份列表
		$provincelist = $mschool->getProvinceList();
		$smarty->assign('provincelist', $provincelist);
		$smarty->assign('school_list', $school_list);
		$smarty->assign('car_list', $car_list);
		$smarty->assign('lisence_config', $lisence_config);
		$smarty->assign('coach_time_config', $coach_time_config);
		$smarty->assign('lesson_config', $lesson_config);
		$smarty->display('coach/add.html');

	//保存时间段 
	} else if($op == 'savetime') {

		// ajax 保存时间段
		$time_config = array_filter($_POST['time_money_config']); //时间段ID与价格的数组
		$date_config = $_POST['date_config']; //日期
		$lisence_no = array_filter($_POST['lisence_no']); //牌照数组
		$subjects = array_filter($_POST['subjects']); //科目数组
		$coach_id = $_POST['coach_id'];

		$arr = array();

		$arr['coach_id'] = $coach_id;
		// 获取当前年份
		$arr['year'] = date('Y', time()); // 年

		// 获取时间设置的当前时间
		$arr['current_time'] = strtotime($arr['year'].'-'.$date_config); // 当前时间

		$date_config_arr = explode('-', $date_config);
		$arr['month'] = $date_config_arr[0];  // 月
		$arr['day'] = $date_config_arr[1];    // 日

		// 时间ID与价格json
		$arr['time_config_money_id'] = json_encode($time_config);

		// 时间配置ID
		$arr['time_config_id'] = implode(',',array_keys($time_config));

		// 时间对应牌照json
		$arr['time_lisence_config_id'] = json_encode($lisence_no);

		// 时间科目对应json
		// $arr['time_lesson_config_id'] = json_encode($subjects,JSON_UNESCAPED_UNICODE);
		$arr['time_lesson_config_id'] = $mcoach->JSON($subjects);

	    // $arr['time_config_config_id'] preg_replace_callback('/\\\\u([0-9a-f]{4})/i', create_function('$matches', 'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'), json_encode($subjects));

		// 更新数据
		$res = $mcoach->updateCoachTime($arr);
		if($res) {
			$ret['code'] = 1;
			echo json_encode($ret);
			exit();
		} else {
			$ret['code'] = 0;
			echo json_encode($ret);
			exit();
		}

	// 删除上一天时间段
	} else if($op == 'delpretime') {

		$coach_id = $_POST['coach_id'];

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
	} else if($op == 'editoperate') {

		$arr = array();
		$arr['l_coach_id'] = $_POST['l_coach_id'];
		$arr['s_coach_name'] = $_POST['s_coach_name'];
		$arr['s_coach_phone'] = $_POST['s_coach_phone'];
		$arr['s_school_name_id'] = $_POST['coach_school_id'];
		$arr['s_coach_lesson_id'] = implode(',', $_POST['lesson_id']);
		$arr['s_coach_lisence_id'] = implode(',', $_POST['lisence_id']);

		$arr['s_coach_car_id'] = $_POST['coach_school_car'];
		$arr['province_id'] = $_POST['province'];
		$arr['city_id'] = $_POST['city'];
		$arr['area_id'] = $_POST['area'];
		$arr['s_address'] = $_POST['s_address'];
		$arr['is_online'] = $_POST['is_online'];
		// $arr['s_yh_name'] = $_POST['s_yh_name'];
		// $arr['s_yh_zhanghao'] = $_POST['s_yh_zhanghao'];
		// $arr['s_yh_huming'] = $_POST['s_yh_huming'];
		$arr['i_type'] = $_POST['coach_type'];

		$s_coach_imgurl = $_FILES['license_img'];

		if($s_coach_imgurl['error'] == 0) {
			// 上传图片
			$filename = uploadimg($s_coach_imgurl, 'upload/coach/', uniqid());
			if($filename == 1) {
				echo "<script>alert('上传失败！');location.href='index.php?action=coach&op=index;</script>";
				exit();
			}
		} else {
			$filename = $_POST['oldimg'];
		}

		$arr['s_coach_imgurl'] = $filename;

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
			$ret['code'] = 1;
			echo json_encode($ret);
			exit();
		} else {
			$ert['code'] = 0;
			echo json_encode($ret);
			exit();
		}

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
	} else if($op == 'showiframe') {

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
		if($coachdetail['s_coach_lesson_id']) {
			$s_coach_lesson_name = array();
			$s_coach_lesson_id = explode(',', $coachdetail['s_coach_lesson_id']);
			foreach ($s_coach_lesson_id as $key => $value) {
				$s_coach_lesson_name[] = $lesson_config[$value];
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

		// 获得所有车辆列表
		$car_list = $mcar->getCarList();

		// 获取当前教练的时间配置
		$current_time_config = $mcoach->getCoachCurrentTimeConfig($id);
		// echo "<pre>";
		// print_r($current_time_config);

		// print_r($coachdetail);
		$smarty->assign('coachdetail', $coachdetail);
		$smarty->assign('lisence_name', $lisence_name);
		$smarty->assign('carinfo', $carinfo);
		$smarty->assign('current_time_config', $current_time_config);
		$smarty->assign('provincelist', $provincelist);
		$smarty->assign('car_list', $car_list);
		$smarty->assign('lesson_name', $lesson_name);
		$smarty->display('coach/show.html');	

	// 保存教练名称
	} else if($op == 'savename') {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$arr['id'] = $id;
		$arr['coach_name'] = $_POST['coach_name'];

		$res = $mcoach->saveCoachName($arr);
		if($res) {
			$ret = array('code'=>1, 'msg'=>'保存成功');
		} else {
			$ret = array('code'=>0, 'msg'=>'保存失败');
		}
		echo json_encode($ret);
		exit();
			
	// 保存教练电话
	} else if($op == 'savephone') {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

		$res = $mcoach->saveCoachName($arr);
		if($res) {
			$ret = array('code'=>1, 'msg'=>'保存成功');
		} else {
			$ret = array('code'=>0, 'msg'=>'保存失败');
		}
		echo json_encode($ret);
		exit();
			
	// 搜索教练
	} else if($op == 'searchcoach') {
		$keywords 		= isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
		$conditiontype 	= isset($_REQUEST['conditiontype']) ? trim($_REQUEST['conditiontype']) : '';
		$type 			= isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'default';
		$page 			= !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$order 			= isset($_REQUEST['order']) ? trim($_REQUEST['order']) : 'desc';

		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$coach_num = count($mcoach->getSearchCoachlist('', '',  $conditiontype, $type, $order, $keywords)); //总数量
		$calcpagecnt = calcpagecnt($coach_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $coach_num, 'index.php?action=coach', array('op'=>'searchcoach', 'keyword'=>$keywords, 'conditiontype'=>$conditiontype));

		$coach_list = $mcoach->getSearchCoachlist($pagestart, $limit, $conditiontype, $type, $order, $keywords);
		// var_dump($coach_list);exit;
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