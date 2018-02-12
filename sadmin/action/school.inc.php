<?php 

	// 驾校模块
	
	header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','edit','getcity','getarea','editcheck','shifts','delbanner','addshifts','delshifts','editshifts','addshiftsoperate','editshiftsoperate','address','editaddress','addaddress','deladdress','editaddressoperate','addaddressoperate','banner','addbanner','addbanneroperate','statistics','echarts','getajaxmonthinfo','getajaxdayinfo','timeconfig','showtime')) ? $op : 'index';

	if(!isset($_SESSION['school_loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}

	$loginauth_str = authcode($_SESSION['school_loginauth'], 'DECODE');
	$loginauth_arr = explode('\t', $loginauth_str);
	$school_id = $loginauth_arr[2];
	$mschool = new mschool($db);

	if($op == 'index') {
		// 获取驾校详情
		$signuporder 	= $mschool->getAjaxStatistics($school_id, 1, 1); // 初始化报名驾校订单数目
		$learncarorder 	= $mschool->getAjaxStatistics($school_id, 2, 1); // 初始化预约学车订单数目

		$signupmember 	= $mschool->getAjaxStatistics($school_id, 1, 1, 1); // 初始化报名驾校学员数目
		$learncarmember = $mschool->getAjaxStatistics($school_id, 2, 1, 1); // 初始化预约学车学员数目

		$membernum 		= $mschool->getAjaxStatistics($school_id, 3, 1); // 学员数
		$currentmonthday = $mschool->getAjaxMonthOrders($school_id, 1); // 当月信息
		// var_dump($currentmonthday);
		$detail = $mschool->getSchoolDetail($school_id);
		$smarty->assign('school_detail', $detail);
		$smarty->assign('signuporder', $signuporder);
		$smarty->assign('learncarorder', $learncarorder);
		$smarty->assign('learncarmember', $learncarmember);
		$smarty->assign('signupmember', $signupmember);
		$smarty->assign('currentmonthday', $currentmonthday);
		$smarty->assign('membernum', $membernum);
		$smarty->display('school/index.html');

	} else if($op == 'edit') {
		// 获得当前需要修改的信息
		$schooldetail = $mschool->getSchoolDetail($school_id);
		$cityinfo = array();
		$citylist = array();
		$arealist = array();

		// 获取当前所在的城市
		if($schooldetail['city_id']) {
			$cityinfo = $mschool->getCityDetail($schooldetail['city_id']);
			$arealist = $mschool->getAreaList($schooldetail['city_id']);
		}
		if($schooldetail['province_id']) {
			$citylist = $mschool->getCityList($schooldetail['province_id']);
		}
		// 获取所有省份列表
		$provincelist = $mschool->getProvinceList();

		$smarty->assign('cityinfo'		, $cityinfo);
		$smarty->assign('id'			, $school_id);
		$smarty->assign('provincelist'	, $provincelist);
		$smarty->assign('citylist'		, $citylist);
		$smarty->assign('arealist'		, $arealist);
		$smarty->assign('schooldetail', $schooldetail);
		$smarty->display('school/edit.html');

	} else if($op == 'editcheck') {
		$arr['l_school_id'] = $_GET['id'];
		$arr['s_school_name'] = $_POST['school_name'];   // 驾校名称
		$s_thumb = $_FILES['s_thumb'];   // 驾校缩略图
		$arr['s_frdb'] = $_POST['legal_person'];	// 法人代表 
		$arr['s_location_x'] = $_POST['school_location_x'];	// 经度 
		$arr['s_location_y'] = $_POST['school_location_y'];	// 经度 
		$arr['s_frdb_mobile'] = $_POST['legal_person_phone']; // 法人手机号码
		$arr['s_frdb_tel'] = $_POST['legal_person_tel'];  // 法人固定号码
		$arr['s_yyzz'] = $_FILES['license_img'];  // 营业执照
		$arr['s_zzjgdm'] = $_POST['s_zzjgdm'];  // 组织结构代码
		$arr['i_dwxz'] = $_POST['school_character'];  // 驾校性质
		$arr['province_id'] = $_POST['province'];  // 省份
		$arr['city_id'] = $_POST['city'];  // 城市
		$arr['area_id'] = $_POST['area'];  // 区域
		$arr['s_address'] = $_POST['s_address'];  // 地址
		$arr['dc_base_je'] = $_POST['dc_base_je'];  // 收费标准
		$arr['dc_bili'] = $_POST['dc_bili'];  // 上浮最高比例
		$arr['s_yh_name'] = $_POST['s_yh_name'];  // 收款银行名称
		$arr['s_yh_zhanghao'] = $_POST['s_yh_zhanghao'];  // 收款银行账号
		$arr['s_yh_huming'] = $_POST['s_yh_huming'];  // 银行账户户名
		$arr['s_shuoming'] = $_POST['s_shuoming'];  // 驾校说明
		$arr['shifts_intro'] = $_POST['shifts_intro'];  // 班制说明

		// 地址必须选择
		if($arr['province_id'] == '' || $arr['city_id'] == '') {
			echo "<script>alert('请选择地址！');location.href='index.php?action=school&op=edit&id=".$arr['l_school_id']."';</script>";
			exit();
		}

		if($s_thumb['error'] == 0) {
			$config = array(
			    "savePath" => "upload/school/thumb/" ,             //存储文件夹
			    "maxSize" => 300 ,                   //允许的文件最大尺寸，单位KB
			    "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
			);
			//上传文件目录
			$Path = "../upload/school/thumb/".$school_id.'/';
			//背景保存在临时目录中
			$config["savePath"] = $Path;
			$up = new Uploader("s_thumb" , $config);
			$info = $up->getFileInfo();
			if($info['state'] == 'SUCCESS') {
				// 压缩图片到160*140
				$show_pic_scal = $up->showPicScal($info['url'], 160, 140);
				$thumbpath = $up->resize($info['url'], $show_pic_scal[0], $show_pic_scal[1], 4);
				$arr['s_thumb'] = $thumbpath;
			} else {
				echo "<script>alert('".$info['state']."');history.back(-1);</script>";
				exit();
			}
		} else {
			$arr['s_thumb'] = $_POST['thumb_oldimg'];
		}

		// 上传营业执照
		if($arr['s_yyzz']['error'] == 0) {
			// 上传图片
			$config = array(
			    "savePath" => "upload/school/licence/" ,             //存储文件夹
			    "maxSize" => 300 ,                   //允许的文件最大尺寸，单位KB
			    "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
			);
			//上传文件目录
			$Path = "../upload/school/licence/";
			//背景保存在临时目录中
			$config["savePath"] = $Path;

			$up = new Uploader("license_img" , $config);
			$info = $up->getFileInfo();
			if($info['state'] == 'SUCCESS') {
				$arr['lisence_imgurl'] = $info['url'];
			} else {
				echo "<script>alert('".$info['state']."');history.back(-1);</script>";
				exit();
			}
		} else {
			$arr['lisence_imgurl'] = $_POST['oldimg'];
		}

		// 更新驾校信息
		$res = $mschool->updateSchoolInfo($arr);
		if($res) {
			echo "<script>alert('更新成功！');location.href='index.php?action=school&op=index';</script>";
			exit();
		} else {
			echo "<script>alert('更新失败！');location.href='index.php?action=school&op=edit&id=".$arr['l_school_id']."';</script>";
			exit();
		}

	} else if($op == 'getcity') {

		// 获取对应的城市列表
		$province_id = isset($_REQUEST['province_id']) ? trim($_REQUEST['province_id']) : 0;
		$citylist = $mschool->getCityList($province_id);
		$html = "<option value=''>请选择市</option>";
		foreach ($citylist as $key => $value) {
			$html .= "<option value='".$value['cityid']."'>".$value['city']."</option>";
		}
		echo $html;

	} else if($op == 'getarea') {

		// 获取对饮的区域列表
		$city_id = isset($_REQUEST['city_id']) ? trim($_REQUEST['city_id']) : 0;
		$arealist = $mschool->getAreaList($city_id);
		$html = "<option value=''>请选择区域</option>";
		foreach ($arealist as $key => $value) {
			$html .= "<option value='".$value['areaid']."'>".$value['area']."</option>";
		}
		echo $html;

	} else if($op == 'shifts') {
		$shifts_list = $mschool->getShiftsList($school_id);
		$smarty->assign('shifts_list', $shifts_list);
		$smarty->display('school/shifts.html');

	} else if($op == 'addshifts') {
		$smarty->assign('school_id', $school_id);
		$smarty->display('school/addshifts.html');

	// 添加班制处理
	} else if($op == 'addshiftsoperate') {
		$arr = array();
		$arr['sh_school_id'] = $school_id;
		$arr['sh_title'] = $_POST['sh_title'];
		$arr['sh_type'] = $_POST['sh_type'];
		$arr['sh_money'] = $_POST['sh_money'];
		$arr['sh_original_money'] = $_POST['sh_original_money'];
		$arr['sh_description_1'] = $_POST['sh_description_1'];
		$arr['sh_description_2'] = $_POST['sh_description_2'];
		$arr['addtime'] = time();

		$res = $mschool->setShifts($arr);
		if($res) {
			echo "<script>alert('新增成功！');location.href='index.php?action=school&op=shifts';</script>";
			exit();
		} else {
			echo "<script>alert('新增失败！');location.href='index.php?action=school&op=shifts';</script>";
			exit();
		}

	// 编辑班制信息
	} else if($op == 'editshifts') {
		$id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : 0;

		$shiftsdetail = $mschool->getShiftsDetail($id);
		$smarty->assign('shiftsdetail', $shiftsdetail);
		$smarty->display('school/editshifts.html');

	// 编辑处理
	} else if($op == 'editshiftsoperate') {

		$arr = array();
		$arr['id'] = $_POST['shifts_id'];
		$arr['sh_school_id'] = $school_id;
		$arr['sh_title'] = $_POST['sh_title'];
		$arr['sh_type'] = $_POST['sh_type'];
		$arr['sh_money'] = $_POST['sh_money'];
		$arr['sh_original_money'] = $_POST['sh_original_money'];
		$arr['sh_description_1'] = $_POST['sh_description_1'];
		$arr['sh_description_2'] = $_POST['sh_description_2'];
		$arr['deleted'] = $_POST['deleted'];
		$arr['addtime'] = time();

		$res = $mschool->updateShifts($arr);
		if($res) {
			echo "<script>alert('编辑成功！');location.href='index.php?action=school&op=shifts';</script>";
			exit();
		} else {
			echo "<script>alert('编辑失败！');location.href='index.php?action=school&op=shifts';</script>";
			exit();
		}

	// 地址列表
	} else if($op == 'address') {

		$address_list = $mschool->getTrainAddressList($school_id);
		$smarty->assign('address_list', $address_list);
		// print_r($address_list);
		$smarty->display('school/address.html');

	// 添加地址
	} else if($op == 'addaddress') {

		$smarty->display('school/addaddress.html');

	// 添加地址操作
	} else if($op == 'addaddressoperate') {

		$arr = array();
		$arr['tl_school_id'] 		= $school_id;
		$arr['tl_train_address'] 	= $_POST['tl_train_address'];
		$arr['tl_phone'] 			= $_POST['tl_phone'];
		$arr['tl_location_x'] 		= $_POST['tl_location_x'];
		$arr['tl_location_y'] 		= $_POST['tl_location_y'];
		$arr['addtime']				= time();

		$res = $mschool->insertAddress($arr);
		if($res) {
			echo "<script>alert('添加成功！');location.href='index.php?action=school&op=address';</script>";
			exit();
		} else {
			echo "<script>alert('添加失败！');location.href='index.php?action=school&op=address';</script>";
			exit();
		}

	// 编辑地址
	} else if($op == 'editaddress') {
		
		$id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : 0;
		$addressdetail = $mschool->getAddressDetail($id);
		$smarty->assign('id', $id);
		$smarty->assign('addressdetail', $addressdetail);
		$smarty->display('school/editaddress.html');

	// 编辑地址操作
	} else if($op == 'editaddressoperate') {

		$arr = array();
		$arr['id']					= isset($_POST['id']) ? trim($_POST['id']) : 0;
		$arr['tl_school_id'] 		= $school_id;
		$arr['tl_train_address'] 	= $_POST['tl_train_address'];
		$arr['tl_phone'] 			= $_POST['tl_phone'];
		$arr['tl_location_x'] 		= $_POST['tl_location_x'];
		$arr['tl_location_y'] 		= $_POST['tl_location_y'];
		$arr['addtime']				= time();

		$res = $mschool->updateAddressInfo($arr);
		if($res) {
			echo "<script>alert('更新成功！');location.href='index.php?action=school&op=address';</script>";
			exit();
		} else {
			echo "<script>alert('更新失败！');location.href='index.php?action=school&op=address';</script>";
			exit();
		}

	// 轮播图列表
	} else if($op == 'banner') {
		$bannerlist = $mschool->getSchoolBannners($school_id);
		// print_r($bannerlist);
		$smarty->assign('bannerlist', $bannerlist);
		$smarty->display('school/banner.html');

	// 添加轮播图
	} else if($op == 'addbanner') {

		$i = 0;
		$school_banner = $_FILES['school_banner'];
		$img_url = array();

		if($school_banner) {

			$dir = '../upload/school/banner/'; 
			$path = mkFolder($dir.$school_id);

			// 获取数据库中的图片数量
			$bannerlist = $mschool->getSchoolBannners($school_id);
			if(count($school_banner['tmp_name']) + count($bannerlist) > 5) {
				echo "<script>alert('最多只能添加5张图 ');location.href='index.php?action=school&op=banner';</script>";
				exit();
			}

			foreach ($school_banner['tmp_name'] as $value) {
				$filename = uniqid().'.jpg'; 
				if ($value) {
					$savepath = $path.'/'.$filename;
					if($school_banner['size'][$i] > 2*1024*1024) {
						echo "<script>alert('名称为".$school_banner['name'][$i]."的图片超过了2M');location.href='index.php?action=school&op=banner';</script>";
						exit();
					}
					$state = move_uploaded_file($value, $savepath); 
					//如果上传成功，预览 
					if($state) {
						$img_url[] = $savepath;
					} else {
						$img_url[] = '';
					}
				}
				$i++; 
			} 
		} else {
			echo "<script>alert('请选择图片上传！');location.href='index.php?action=school&op=banner';</script>";
			exit();
		}

		$res = $mschool->addSchoolBanners($img_url, $school_id);
		if($res) {
			echo "<script>alert('轮播图添加成功！ ');location.href='index.php?action=school&op=banner';</script>";
			exit();
		} else {
			echo "<script>alert('轮播图添加失败！ ');location.href='index.php?action=school&op=banner';</script>";
			exit();
		}

	// 删除地址
	} else if($op == 'deladdress') {
		$id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : 0;
		$res = $mschool->delSchoolAddress($id);
		if($res) {
			echo 1;
			exit();
		} else {
			echo 2;
			exit();
		}

	// 删除班制
	} else if($op == 'delshifts') {
		$id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : 0;
		$res = $mschool->delSchoolShifts($id);
		if($res) {
			echo 1;
			exit();
		} else {
			echo 2;
			exit();
		}

	// 删除banner图
	} else if($op == 'delbanner') {

		$url = isset($_REQUEST['url']) ? trim($_REQUEST['url']) : '';
		$res = $mschool->delBanner($url, $school_id);
		if($res) {
			echo 1;
			exit();
		} else {
			echo 2;
			exit();
		}

	// 获取统计信息
	} else if($op == 'statistics') {
		$type = isset($_POST['type']) ? trim($_POST['type']) : 1;
		$status = isset($_POST['status']) ? trim($_POST['status']) : 1;
		$res = $mschool->getAjaxStatistics($school_id, $type, $status);
		$data = array('code'=>1, 'data'=>$res);
		echo json_encode($data);

	// 获取年订单统计数据
	} else if($op == 'echarts') {
		$type = isset($_POST['type']) ? trim($_POST['type']) : 1;
		$res = $mschool->getAjaxOrder($school_id, $type);
		$data = array('code'=>200, 'data'=>$res);
		echo json_encode($data);

	// ajax获取月份的信息
	} else if($op == 'getajaxmonthinfo') {
		$type = isset($_POST['type']) ? trim(intval($_POST['type'])) : 1;
		$month = isset($_POST['month']) ? trim(intval($_POST['month'])) : '';
		$res = $mschool->getAjaxMonthOrders($school_id, $type, $month);
		$data = array('code'=>1, 'data'=>$res);
		echo json_encode($data);

	// ajax获取天数的信息
	} else if($op == 'getajaxdayinfo') {
		$month = isset($_POST['month']) ? trim(intval($_POST['month'])) : 1;
		$day = isset($_POST['day']) ? trim(intval($_POST['day'])) : 1;
		$res = $mschool->getAjaxDayOrders($school_id, $month, $day);
		$data = array('code'=>1, 'data'=>$res);
		echo json_encode($data);

	// 显示驾校时间时间配置
	} else if($op == 'showtime') {
		$school_time_config = $mschool->getSchoolTimeConfig($school_id);

		// echo "<pre>";
		// print_r($school_time_config);

		$smarty->assign('school_time_config', $school_time_config);
		$smarty->display('school/timeconfig.html');
		
	}else if($op == 'timeconfig') {
		$price 		= array();
		$license_no = array();
		$subjects 	= array();
		$ids 		= array();
		$start_time = array();
		$end_time 	= array();

		$price 		= isset($_POST['price']) ? $_POST['price'] : array(); //价格的数组
		$license_no = isset($_POST['license_no']) ? $_POST['license_no'] : array(); //牌照数组
		$subjects 	= isset($_POST['subjects']) ? $_POST['subjects'] : array(); //科目数组
		$ids		= isset($_POST['id']) ? $_POST['id'] : array(); //id
		$start_time	= isset($_POST['start_time']) ? $_POST['start_time'] : array(); //开始时间
		$end_time	= isset($_POST['end_time']) ? $_POST['end_time'] : array(); //结束时间

		$price_arr = array();
		$subjects_arr = array();
		$start_time_arr = array();
		$end_time_arr = array();
		$license_no_arr = array();

		foreach ($price as $key => $value) {
			if($value) {
				$price_arr[$key] = $value;
			}
		}
		foreach ($subjects as $key => $value) {
			if($value) {
				$subjects_arr[$key] = $value;
			}
		}
		foreach ($start_time as $key => $value) {
			if(isset($value)) {
				$start_time_arr[$key] = $value;
			}
		}
		foreach ($end_time as $key => $value) {
			if(isset($value)) {
				$end_time_arr[$key] = $value;
			}
		}
		foreach ($license_no as $key => $value) {
			if($value) {
				$license_no_arr[$key] = $value;
			}
		}
		$res = $mschool->updateSchoolTime($school_id, $price_arr, $license_no_arr, $subjects_arr, $start_time_arr, $end_time_arr);
		if($res) {
			$ret['code'] = 1;
			echo json_encode($ret);
			exit();
		} else {
			$ret['code'] = 0;
			echo json_encode($ret);
			exit();
		}

		
	}


?>
