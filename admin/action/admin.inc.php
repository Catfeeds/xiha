<?php  
	header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('login','index','logout','logincheck','info')) ? $op : 'login';

	$madmin = new madmin($db);

	if($op == 'login') {
		if(isset($_SESSION['loginauth'])) {
			echo "<script>location.href='index.php';</script>";
			exit();	
		}

		$smarty->display('admin/login.html');

	// 登录验证
	} else if($op == 'logincheck') {
		if(isset($_SESSION['loginauth'])) {
			echo "<script>location.href='index.php';</script>";			
			exit();
		}
		$username = isset($_POST['username']) ? trim($_POST['username']) : '';
		$password = isset($_POST['password']) ? trim($_POST['password']) : '';
		if(empty($username) || empty($password)) {
			echo "<script>alert('请输入用户名密码！');location.href='index.php?action=admin&op=login';</script>";
			exit();
		}
		$admininfo = $madmin->_UserLogin($username, $password);

		if($admininfo) {
			$_SESSION['loginauth'] = authcode($admininfo[0].'\t'.$admininfo[1].'\t','ENCODE');
			// isetcookie('name',$admininfo[0]);
			// isetcookie('school_id', $admininfo[2]);
			// $_SESSION['school_id'] = $admininfo[2];
			echo "<script>location.href='index.php';</script>";
		} else {
			echo "<script>alert('登录失败！');location.href='index.php?action=admin&op=login';</script>";

		}

	// 后台首页
	} else if($op == 'index') {
		if(!isset($_SESSION['loginauth'])) {
			echo "<script>location.href='index.php?action=admin&op=login';</script>";
			exit();
		}
		$loginauth_str = authcode($_SESSION['loginauth'], 'DECODE');
		$loginauth_arr = explode('\t', $loginauth_str);
		$admin_name = $loginauth_arr[0];
		
		$smarty->assign('loginauth', $_SESSION['loginauth']);
		$smarty->assign('name', $admin_name);
		$smarty->assign('op',$op);
		$smarty->assign('manage_config', $manage_config);
		$smarty->display('admin/index.html');

	// 登出
	} else if($op == 'logout') {
		isetcookie('name', '');
		isetcookie('school_id', '');
		unset($_SESSION['loginauth']);
		iheader('location:index.php?action=admin&op=login');

	// 系统信息
	} else if($op == 'info') {
		if(!isset($_SESSION['loginauth'])) {
			echo "<script>location.href='index.php?action=admin&op=login';</script>";
			exit();
		}
		$smarty->assign('loginauth', $_SESSION['loginauth']);
		$smarty->assign('name', $_COOKIE[$cookie_pre.'name']);
		$smarty->assign('ip', $onlineip);
		$smarty->display('admin/info.html');
	} else {

	}
?>