<?php 

	// 驾校模块
	
	header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','add','addoperation','edit','del','getcity','getarea','editcheck','delmore','brand','online','search','showbanner','dmaster','addheadmaster','editheadmaster','delheadmaster','searchheadmaster','searchschoolname','banner','addbanner','delbanner')) ? $op : 'index';

	if(!isset($_SESSION['loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}

	$mschool = new mschool($db);
	
	if($op == 'index') {
		// 获取驾校列表
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 10;
		$pagestart = ($page - 1) * $limit;
		$school_num = count($mschool->getSchoollist()); //总数量
		$calcpagecnt = calcpagecnt($school_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $school_num, 'index.php?action=school', array('op'=>'index'));

		$school_list = $mschool->getSchoollist($pagestart, $limit);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('school_num', $school_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('school_list', $school_list);
		$smarty->display('school/index.html');

	} else if($op == 'add') {

		// 获取所有省份列表
		$provincelist = $mschool->getProvinceList();
		$smarty->assign('provincelist', $provincelist);
		$smarty->display('school/add.html');

	} else if($op == 'addoperation') {
		$arr = array();
		$arr['s_school_name'] = isset($_POST['school_name']) ? trim($_POST['school_name']) : '';   // 驾校名称
		$arr['s_frdb'] = isset($_POST['legal_person']) ? trim($_POST['legal_person']) : '';	// 法人代表 
		$arr['s_frdb_mobile'] = isset($_POST['legal_person_phone']) ? trim($_POST['legal_person_phone']) : ''; // 法人手机号码
		$arr['s_frdb_tel'] = isset($_POST['legal_person_tel']) ? trim($_POST['legal_person_tel']) : '';  // 法人固定号码
		$arr['s_zzjgdm'] = isset($_POST['s_zzjgdm']) ? trim($_POST['s_zzjgdm']) : '';  // 组织结构代码
		$arr['i_dwxz'] = isset($_POST['school_character']) ? trim($_POST['school_character']) : '';  // 驾校性质
		$arr['province_id'] = isset($_POST['province']) ? trim($_POST['province']) : 0;  // 省份
		$arr['city_id'] = isset($_POST['city']) ? trim($_POST['city']) : 0;  // 城市
		$arr['area_id'] = isset($_POST['area']) ? trim($_POST['area']) : 0;  // 区域
		$arr['s_address'] = isset($_POST['s_address']) ? trim($_POST['s_address']) : '';  // 地址
		$arr['dc_base_je'] = isset($_POST['dc_base_je']) ? trim($_POST['dc_base_je']) : '';  // 收费标准
		$arr['dc_bili'] =isset( $_POST['dc_bili']) ? trim( $_POST['dc_bili']) : '';  // 上浮最高比例
		$arr['s_yh_name'] = isset($_POST['s_yh_name']) ? trim($_POST['s_yh_name']) : '';  // 收款银行名称
		$arr['s_yh_zhanghao'] = isset($_POST['s_yh_zhanghao']) ? trim($_POST['s_yh_zhanghao']) : '';  // 收款银行账号
		$arr['s_yh_huming'] = isset($_POST['s_yh_huming']) ? trim($_POST['s_yh_huming']) : '';  // 银行账户户名
		$arr['s_shuoming'] = isset($_POST['s_shuoming']) ? trim($_POST['s_shuoming']) : '';  // 驾校说明
		$arr['brand'] = isset($_POST['school_brand']) ? trim($_POST['school_brand']) : '';  // 驾校品牌
		$arr['s_location_x'] = isset($_POST['school_location_x']) ? trim($_POST['school_location_x']) : '';  // 驾校品牌
		$arr['s_location_y'] = isset($_POST['school_location_y']) ? trim($_POST['school_location_y']) : '';  // 驾校品牌

		$s_yyzz = $_FILES['license_img'];  // 营业执照
		$s_thumb = $_FILES['s_thumb'];   // 驾校缩略图
		if($s_thumb['error'] == 0) {
			$config = array(
			    "savePath" => "upload/school/thumb/" ,             //存储文件夹
			    "maxSize" => 300 ,                   //允许的文件最大尺寸，单位KB
			    "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
			);
			//上传文件目录
			$Path = "../upload/school/thumb/";
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
			$arr['s_thumb'] = '';	
		}
		if($s_yyzz['error'] == 0) {
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
		$res = $mschool->InsertSchoolInfo($arr);
		if($res) {
			echo "<script>alert('新增成功！');location.href='index.php?action=school&op=index';</script>";
			exit();
		} else {
			echo "<script>alert('新增失败！');location.href='index.php?action=school&op=index';</script>";
			exit();
		}

	} else if($op == 'edit') {

		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

		// 获得当前需要修改的信息
		$schooldetail = $mschool->getSchoolDetail($id);

		// 获取当前所在的城市
		if($schooldetail['city_id']) {
			$cityinfo = $mschool->getCityDetail($schooldetail['city_id']);
			$smarty->assign('cityinfo', $cityinfo);
		}
		// 获取所有省份列表
		$provincelist = $mschool->getProvinceList();
		$smarty->assign('id', 			$id);
		$smarty->assign('provincelist', $provincelist);
		$smarty->assign('schooldetail', $schooldetail);
		$smarty->display('school/edit.html');

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

	} else if($op == 'editcheck') {
		$arr['l_school_id'] = $_GET['id'];
		$arr['s_school_name'] = isset($_POST['school_name']) ? trim($_POST['school_name']) : '';   // 驾校名称
		$arr['s_frdb'] = isset($_POST['legal_person']) ? trim($_POST['legal_person']) : '';	// 法人代表 
		$arr['s_frdb_mobile'] = isset($_POST['legal_person_phone']) ? trim($_POST['legal_person_phone']) : ''; // 法人手机号码
		$arr['s_frdb_tel'] = isset($_POST['legal_person_tel']) ? trim($_POST['legal_person_tel']) : '';  // 法人固定号码
		$arr['s_zzjgdm'] = isset($_POST['s_zzjgdm']) ? trim($_POST['s_zzjgdm']) : '';  // 组织结构代码
		$arr['i_dwxz'] = isset($_POST['school_character']) ? trim($_POST['school_character']) : '';  // 驾校性质
		$arr['brand'] = isset($_POST['school_brand']) ? trim($_POST['school_brand']) : '';  // 驾校性质
		$arr['province_id'] = isset($_POST['province']) ? trim($_POST['province']) : 0;  // 省份
		$arr['city_id'] = isset($_POST['city']) ? trim($_POST['city']) : 0;  // 城市
		$arr['area_id'] = isset($_POST['area']) ? trim($_POST['area']) : 0;  // 区域
		$arr['s_address'] = isset($_POST['s_address']) ? trim($_POST['s_address']) : '';  // 地址
		$arr['dc_base_je'] = isset($_POST['dc_base_je']) ? trim($_POST['dc_base_je']) : '';  // 收费标准
		$arr['dc_bili'] = isset($_POST['dc_bili']) ? trim($_POST['dc_bili']) : '';  // 上浮最高比例
		$arr['s_yh_name'] = isset($_POST['s_yh_name']) ? trim($_POST['s_yh_name']) : '';  // 收款银行名称
		$arr['s_yh_zhanghao'] = isset($_POST['s_yh_zhanghao']) ? trim($_POST['s_yh_zhanghao']) : '';  // 收款银行账号
		$arr['s_yh_huming'] = isset($_POST['s_yh_huming']) ? trim($_POST['s_yh_huming']) : '';  // 银行账户户名
		$arr['s_shuoming'] = isset($_POST['s_shuoming']) ? trim($_POST['s_shuoming']) : '';  // 驾校说明
		$arr['s_location_x'] = isset($_POST['school_location_x']) ? trim($_POST['school_location_x']) : '';  // 驾校品牌
		$arr['s_location_y'] = isset($_POST['school_location_y']) ? trim($_POST['school_location_y']) : '';  // 驾校品牌

		$s_yyzz = $_FILES['license_img'];  // 营业执照
		$s_thumb = $_FILES['s_thumb'];   // 驾校缩略图

		// print_r($s_yyzz);
		if($s_thumb['error'] == 0) {
			$config = array(
			    "savePath" => "upload/school/thumb/" ,             //存储文件夹
			    "maxSize" => 300 ,                   //允许的文件最大尺寸，单位KB
			    "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
			);
			//上传文件目录
			$Path = "../upload/school/thumb/";
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


		if($s_yyzz['error'] == 0) {
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

	} else if($op == 'del') {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$res = $mschool->delSchool($id);
		$ret['code'] = 1;
		if($res) {
			echo json_encode($ret);
			exit();
		} else {
			$ret['code'] = 0;
			echo json_encode($ret);
			exit();
		}

	} else if($op == 'delmore') {
		$check_id = $_POST['check_id'];
		echo json_encode($check_id);
		exit();

	}else if($op == 'brand') {
		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$res = $mschool->setSchoolBrand($id);
		if($res) {
			$ret = 1;
		} else {
			$ret = 0;
		}
		echo $ret;
		exit();

	} else if($op == 'online') {
		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$ret = 1;
		$res = $mschool->setSchoolOnlineStatus($id);
		if(!$res) {
			$ret = 0;
		}
		echo $ret;
		exit();

	} else if($op == 'search') {
		// 获取驾校列表
		$search_condition = isset($_REQUEST['search_condition']) ? trim($_REQUEST['search_condition']) : 1;
		$keywords = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 10;
		$pagestart = ($page - 1) * $limit;
		$school_num = count($mschool->getSearchSchoolList('', '', $search_condition, $keywords)); //总数量
		$calcpagecnt = calcpagecnt($school_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $school_num, 'index.php?action=school', array('op'=>'search', 'keyword'=>$keywords, 'search_condition'=>$search_condition));

		$school_list = $mschool->getSearchSchoolList($pagestart, $limit, $search_condition, $keywords);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('school_num', $school_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('search_condition', $search_condition); //总条数
		$smarty->assign('keywords', $keywords); //总条数
		$smarty->assign('school_list', $school_list);
		$smarty->display('school/search.html');
	} else if($op == 'headmaster') {
		// 获取校长列表
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 10;
		$pagestart = ($page - 1) * $limit;
		$headmaster_num = count($mschool->getHeadmasterList()); //总数量
		$calcpagecnt = calcpagecnt($headmaster_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $headmaster_num, 'index.php?action=school', array('op'=>'headmaster'));
		$headmaster_list = $mschool->getHeadmasterList($pagestart, $limit);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('headmaster_num', $headmaster_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('headmaster_list', $headmaster_list);
		$smarty->display('school/headmaster.html');
	} else if($op == 'addheadmaster') {
		if ($_POST) {
			//添加校长
			$arr = array();
			$arr['real_name'] = trim($_POST['real_name']);
			$arr['user_phone'] = trim($_POST['user_phone']);
			$arr['school_id'] = trim($_POST['school_id']);
			$arr['province'] = trim($_POST['province']);
			$arr['city'] = trim($_POST['city']);
			$arr['area'] = trim($_POST['area']);
			$arr['address'] = trim($_POST['s_address']);
			$arr['sex'] = trim($_POST['sex']);
			$arr['age'] = trim($_POST['age']);
			$arr['identity_id'] = trim($_POST['identity_id']);
			$arr['s_password'] = md5('xihaxueche');
			$arr['i_user_type'] = 2;
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
			$up = new Uploader("user_photo" , $config);

			$info = $up->getFileInfo();
			if($info['state'] == 'SUCCESS') {
				// 插入到数据库
				$arr['user_photo'] = $info['url'];

				$res = $mschool->insertHeadmasterInfo($arr);
				if($res == 1) {
					echo "<script>alert('号码已被注册');location.href='index.php?action=school&op=addheadmaster';</script>";
					exit();
				} else if($res == 5) { 
					echo "<script>alert('身份证已被注册');location.href='index.php?action=school&op=addheadmaster';</script>";
					exit();
				} else if($res == 2){
					echo "<script>alert('添加成功！');location.href='index.php?action=school&op=headmaster';</script>";
					exit();
				} else {
					echo "<script>alert('添加失败！');location.href='index.php?action=school&op=addheadmaster';</script>";
					exit();
				}
			} else {
				echo "<script>alert('请添加头像');location.href='index.php?action=school&op=addheadmaster';</script>";
				exit();
			}
		}
		// 获取所有省份列表
		$provincelist = $mschool->getProvinceList();
		// 获取班制信息
		$shifts_list = $mschool->getShiftsList();
		$smarty->assign('lisence_config', $lisence_config);
		$smarty->assign('provincelist', $provincelist);
		$smarty->assign('shifts_list', $shifts_list);
		$smarty->display('school/addheadmaster.html');
	} else if($op == 'editheadmaster') {
		$smarty->display('school/addheadmaster.html');
	} else if($op == 'delheadmaster') {
		//删除校长
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$res = $mschool->delHeadmaster($id);
		$ret['code'] = 1;
		if($res) {
			echo json_encode($ret);
			exit();
		} else {
			$ret['code'] = 0;
			echo json_encode($ret);
			exit();
		}
	} else if($op == 'searchheadmaster') {
		//搜索校长
		$search_condition = isset($_REQUEST['search_condition']) ? trim($_REQUEST['search_condition']) : 1;
		$keywords = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 10;
		$pagestart = ($page - 1) * $limit;
		$headmaster_num = count($mschool->getSearchHeadmasterList('', '', $search_condition, $keywords)); //总数量
		$calcpagecnt = calcpagecnt($headmaster_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $headmaster_num, 'index.php?action=school', array('op'=>'searchheadmaster', 'keyword'=>$keywords, 'search_condition'=>$search_condition));

		$headmaster_list = $mschool->getSearchHeadmasterList($pagestart, $limit, $search_condition, $keywords);

		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('headmaster_num', $headmaster_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); 
		$smarty->assign('search_condition', $search_condition); 
		$smarty->assign('keywords', $keywords); 
		$smarty->assign('headmaster_list', $headmaster_list);
		$smarty->display('school/headmaster.html');
	} else if($op == 'searchschoolname') {
		//搜索驾校名称及城市
		$school_name = isset($_REQUEST['school_name']) ? trim($_REQUEST['school_name']) : 0;
		$res = $mschool->searchSchoolName($school_name);
		if($res) {
			$data = array('code'=>200, 'msg'=>'搜索成功', 'data'=>$res);
			echo json_encode($data);
			exit();
		} else {
			$data = array('code'=>400, 'msg'=>'搜索失败', 'data'=>'');
			echo json_encode($data);
			exit();
		}

	// 轮播图列表
	} else if($op == 'banner') {
		// 获取当前驾校列表
		$school_list = $mschool->getSchoollist();
		$smarty->assign('school_list', $school_list);
		$smarty->display('school/banner.html');
	// 添加轮播图
	} else if($op == 'addbanner') {
	    $school_id = $_POST['school_id'];
		$i = 0;
		$school_banner = $_FILES['school_banner'];
		$img_url = array();
		if(is_array($school_banner)) {
			$dir = '../upload/school/banner/'; 
			$path = mkFolder($dir.$school_id);
			// 获取数据库中的图片数量
			$bannerlist = $mschool->getSchoolBannners($school_id);
			if(count($school_banner['tmp_name']) + count($bannerlist) > 5) {
				echo "<script>alert('最多只能添加5张图 ');location.href='index.php?action=school&op=banner';</script>";
				exit();
			}
			foreach ($school_banner['tmp_name'] as $value) {
				$filename = md5(time() . mt_rand(1,1000000)).'.jpg';
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
	} else if($op == 'showbanner'){
		$school_id = $_POST['school_id'];
		if($school_id){
	 		$bannerlist = $mschool->getSchoolBannners($school_id);
		}
		if (is_array($bannerlist)) {
			$data = array('code' => 200, 'data' => $bannerlist);
		} else {
			$data = array('code' => 200, 'data' => array());
		}
		exit(json_encode($data));

	} else if($op == 'deladdress') {
	// 删除地址
		$id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : 0;
		$res = $mschool->delSchoolAddress($id);
		if($res) {
			echo 1;
			exit();
		} else {
			echo 2;
			exit();
		}

	// 删除banner图
	} else if($op == 'delbanner') {
		$school_id = $_POST['school_id'];
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
	}

?>