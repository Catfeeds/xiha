<?php  
	// 管理员模块

	header("Content-type: text/html; charset=utf-8");

	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','add','addoperate','edit','editoperate','del','usercheck','addrole','addroleoperate','rolelist','editrole','editroleoperate','map')) ? $op : 'index';

	if(!isset($_SESSION['school_loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}
	$loginauth_str = authcode($_SESSION['school_loginauth'], 'DECODE');
	$loginauth_arr = explode('\t', $loginauth_str);
	$school_id = $loginauth_arr[2];

	$madmin = new madmin($db);

	if($op == 'index') {
		
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 10;
		$pagestart = ($page - 1) * $limit;
		
		$manage_num = count($madmin->getManagerList('','',$school_id)); //总数量
		$calcpagecnt = calcpagecnt($manage_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $manage_num, 'index.php?action=manager', array('op'=>'index'));

		$manage_list = $madmin->getManagerList($pagestart, $limit, $school_id);
		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('manage_num', $manage_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('manage_list', $manage_list);
		$smarty->display('manager/index.html');

	} else if($op == 'add') {

		$permission_list = $madmin->getPermissionList();
		// echo "<pre>";
		// print_r($permission_list);
		// exit();
		$smarty->assign('permission_list', $permission_list); //权限列表
		$smarty->display('manager/add.html');

	} else if($op == 'addoperate') {
		
		$manage_repeat_password = $_POST['manage_repeat_password'];
		$manage_password = $_POST['manage_password'];
		if($manage_repeat_password == '' || $manage_password == '') {
			echo "<script>alert('请填写密码！');history.back(-1);</script>";
			exit();
		}

		if($manage_password != $manage_repeat_password) {	
			echo "<script>alert('密码不一致！');history.back(-1);</script>";
			exit();
		}

		if($school_id == '') {
			echo "<script>alert('请选择绑定驾校');history.back(-1);</script>";
			exit();
		}

		$manage_name = $_POST['manage_name'];

		$arr['school_id'] = $school_id;
		$arr['role_permission_id'] = 2;
		$arr['password'] = md5($manage_repeat_password);
		$arr['role_id'] = 2;
		$arr['manage_name'] = $manage_name;

		$res = $madmin->addManager($arr);
		if($res) {
			echo "<script>alert('添加成功');location.href='index.php?action=manager&op=index';</script>";
			exit();
		} else {
			echo "<script>alert('添加失败');location.href='index.php?action=manager&op=index';</script>";
			exit();
		}

	// 用户名重复性检测
	} else if($op == 'usercheck') {
		$name = $_POST['name'];
		$res = $madmin->userCheck($name);
		if($res) {
			$ret['code'] = 1;
		} else {
			$ret['code'] = 2;
		}
		echo json_encode($ret);
		exit();

	// 添加角色
	} else if($op == 'addrole') {

		$smarty->assign('manage_config', $manage_config);
		$smarty->display('manager/addrole.html');

	// 管理员角色操作
	} else if($op == 'addroleoperate') {

		$arr = array();
		$arr['manage_role_name'] = $_POST['manage_role_name'];
		$arr['manage_role_description'] = $_POST['manage_role_description'];
		$arr['permission_id'] = implode(',', $_POST['permission_id']);

		if($arr['manage_role_name'] == '' || $arr['manage_role_description'] == '' || $arr['permission_id'] == '') {
			echo "<script>alert('请填写信息');location.href='index.php?action=manager&op=add';</script>";
			exit();
		}

		$res = $madmin->setRolePermission($arr);
		if($res) {
			echo "<script>alert('添加成功');location.href='index.php?action=manager&op=index';</script>";
			exit();

		} else {
			echo "<script>alert('添加失败');location.href='index.php?action=manager&op=index';</script>";
			exit();
		}

	// 获取角色列表
	} else if ($op == 'rolelist') {

		$rolelist = $madmin->getRoleList();
		$smarty->assign('role_list', $rolelist);
		$smarty->display('manager/rolelist.html');

	// 编辑
	} else if($op == 'edit') {
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

		$managerinfo = $madmin->getManageInfo($id);
		$permission_list = $madmin->getPermissionList();

		$smarty->assign('id', $id);
		$smarty->assign('permission_list', $permission_list); //权限列表
		$smarty->assign('managerinfo', $managerinfo);
		$smarty->display('manager/edit.html');

	// 编辑处理
	} else if($op == 'editoperate') {
		$arr['id'] = $_POST['id'];
		$arr['manage_name'] = $_POST['manage_name'];
		$arr['manage_permission_id'] = $_POST['manage_permission'];
		$arr['manage_password'] = $_POST['manage_password'];
		$arr['manage_repeat_password'] = $_POST['manage_repeat_password'];
		$arr['manage_content'] = $_POST['manage_content'];

		if($arr['manage_repeat_password'] != $arr['manage_password']) {
			echo "<script>alert('密码不一致！');location.href='index.php?action=manager&op=edit&id=".$arr['id']."';</script>";
			exit();
		}

		$res = $madmin->updateManageInfo($arr);
		if($res) {
			echo "<script>alert('修改成功');location.href='index.php?action=manager&op=index';</script>";
			exit();

		} else {
			echo "<script>alert('修改失败');location.href='index.php?action=manager&op=edit&id=".$arr['id']."';</script>";
			exit();
		}

	} else if($op == 'del') {
		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$res = $madmin->deleteManage($id);
		if($res) {
			$ret['code'] = 1;
		} else {
			$ret['code'] = 2;
		}
		echo json_encode($ret);
		exit();

	} else if($op == 'editrole') {
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		
		$roleinfo = $madmin->getRoleInfo($id);
		$smarty->assign('manage_config', $manage_config);
		$smarty->assign('roleinfo', $roleinfo);
		$smarty->assign('id', $id);
		$smarty->display('manager/editrole.html');

	} else if($op == 'editroleoperate') {
		$arr['id'] = $_POST['id'];
		$arr['manage_role_name'] = $_POST['manage_role_name'];
		$arr['manage_role_content'] = $_POST['manage_role_content'];
		$arr['permission_id'] = isset($_POST['permission_id']) ? $_POST['permission_id'] : '';

		if(empty($arr['permission_id'])) {
			echo "<script>alert('请选择权限');history.back(-1);</script>";
			exit();
		}

		$res = $madmin->updateRoleInfo($arr);
		if($res) {
			echo "<script>alert('修改成功');location.href='index.php?action=manager&op=rolelist';</script>";
			exit();

		} else {
			echo "<script>alert('修改失败');location.href='index.php?action=manager&op=rolelist';</script>";
			exit();
		}

	// 地图
	} else if($op == 'map') {
		$lng = isset($_GET['lng']) ? $_GET['lng'] : '117.145041';
		$lat = isset($_GET['lat']) ? $_GET['lat'] : '31.840383';
		$smarty->assign('lng', $lng);
		$smarty->assign('lat', $lat);
		$smarty->display('manager/map.html');
	}
?>