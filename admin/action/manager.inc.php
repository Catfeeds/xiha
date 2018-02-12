<?php  
	// 管理员模块

	header("Content-type: text/html; charset=utf-8");

	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','add','addoperate','edit','editoperate','del','usercheck','addrole','addroleoperate','rolelist','editrole','editroleoperate','map')) ? $op : 'index';

	if(!isset($_SESSION['loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}
	
	$madmin = new madmin($db);
	$mschool = new mschool($db);

	if($op == 'index') {
		
		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 10;
		$pagestart = ($page - 1) * $limit;

		$manage_num = count($madmin->getManagerList()); //总数量
		$calcpagecnt = calcpagecnt($manage_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $manage_num, 'index.php?action=manager', array('op'=>'index'));

		$manage_list = $madmin->getManagerList($pagestart, $limit);
		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('manage_num', $manage_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('manage_list', $manage_list);
		$smarty->display('manager/index.html');

	} else if($op == 'add') {

		// 获取所有权限
		$permission_list = $madmin->getPermissionList();

		// 获取所有驾校列表
		$school_list = $madmin->_getSchoolList();
		$smarty->assign('school_list', $school_list); //驾校列表
		$smarty->assign('permission_list', $permission_list); //权限列表
		$smarty->display('manager/add.html');

	} else if($op == 'addoperate') {
		
		$manage_repeat_password = isset($_POST['manage_repeat_password']) ? trim($_POST['manage_repeat_password']) : '';
		$manage_password = isset($_POST['manage_password']) ? trim($_POST['manage_password']) : '';
		$arr['school_id'] = isset($_POST['school_id']) ? trim($_POST['school_id']) : 0;
		$arr['content'] = isset($_POST['manage_content']) ? trim($_POST['manage_content']) : '';
		$arr['school_location_x'] = isset($_POST['school_location_x']) ? trim($_POST['school_location_x']) : 0;
		$arr['school_location_y'] = isset($_POST['school_location_y']) ? trim($_POST['school_location_y']) : 0;

		if($manage_repeat_password == '' || $manage_password == '') {
			echo "<script>alert('请填写密码！');history.back(-1);</script>";
			exit();
		}

		if($manage_password != $manage_repeat_password) {	
			echo "<script>alert('密码不一致！');history.back(-1);</script>";
			exit();
		}

		if($arr['school_id'] == '') {
			echo "<script>alert('请选择所属驾校');history.back(-1);</script>";
			exit();
		}

		$arr['name'] = $_POST['manage_name'];
		$arr['password'] = md5($manage_repeat_password);
		$arr['role_id'] = $_POST['role_id'];

		// 通过角色ID获取权限ID
		$permission_id = $madmin->getPermissionInfo($arr['role_id']);

		$arr['role_permission_id'] = $permission_id['l_rolepress_incode'];

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
		$arr['manage_role_name'] = isset($_POST['manage_role_name']) ? trim($_POST['manage_role_name']) : '';
		$arr['manage_role_description'] = isset($_POST['manage_role_description']) ? trim($_POST['manage_role_description']) : '';
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
		$school_list = $madmin->_getSchoolList();
		// 获取所有角色列表
		$rolelist = $madmin->getRoleList();

		$smarty->assign('id', $id);
		$smarty->assign('role_list', $rolelist);
		$smarty->assign('school_list', $school_list); //驾校列表
		$smarty->assign('permission_list', $permission_list); //权限列表
		$smarty->assign('managerinfo', $managerinfo);
		$smarty->display('manager/edit.html');

	// 编辑处理
	} else if($op == 'editoperate') {
		$arr['id'] = $_POST['id'];
		$arr['name'] = isset($_POST['manage_name']) ? trim($_POST['manage_name']) : '';
		$arr['role_id'] = isset($_POST['role_id']) ? trim($_POST['role_id']) : '';

		$arr['manage_password'] = isset($_POST['manage_password']) ? trim($_POST['manage_password']) : '';
		$arr['manage_repeat_password'] = isset($_POST['manage_repeat_password']) ? trim($_POST['manage_repeat_password']) : '';
		$arr['content'] = isset($_POST['manage_content']) ? trim($_POST['manage_content']) : '';

		$arr['school_id'] = isset($_POST['school_id']) ? trim($_POST['school_id']) : '';

		if($arr['school_id'] == '') {
			echo "<script>alert('请选择所属驾校');history.back(-1);</script>";
			exit();
		}

		// 通过角色ID获取权限ID
		$permission_id = $madmin->getPermissionInfo($arr['role_id']);

		$arr['role_permission_id'] = $permission_id['l_rolepress_incode'];
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

	} else if($op == 'map') {
		$smarty->display('manager/map.html');
	}
?>