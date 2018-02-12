<?php  
	header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('login','index','logout','logincheck','info', 'help','changepwd','editpass','statistics','checkoldpass')) ? $op : 'login';

	$madmin = new madmin($db);
	$mlearncar = new mlearncar($db);

	if($op == 'login') {
		if(isset($_SESSION['school_loginauth'])) {
			echo "<script>location.href='index.php';</script>";
			exit();	
		}
		$smarty->display('admin/login.html');

	// 登录验证
	} else if($op == 'logincheck') {
		if(isset($_SESSION['school_loginauth'])) {
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
			$_SESSION['school_loginauth'] = authcode($admininfo[0].'\t'.$admininfo[1].'\t'.$admininfo[2].'\t'.$admininfo[3],'ENCODE');
			// isetcookie('name',$admininfo[0]);
			// isetcookie('school_id', $admininfo[2]);
			// isetcookie('real_name',$admininfo[3]);
			// $_SESSION['school_id'] = $admininfo[2];
			echo "<script>location.href='index.php';</script>";
		} else {
			echo "<script>alert('登录失败！账号或者密码错误');location.href='index.php?action=admin&op=login';</script>";

		}

	// 后台首页
	} else if($op == 'index') {
		if(!isset($_SESSION['school_loginauth'])) {
			echo "<script>location.href='index.php?action=admin&op=login';</script>";
			exit();
		}
		// iheader('location:index.php?action=game&op=list');

		$loginauth_str = authcode($_SESSION['school_loginauth'], 'DECODE');
		$loginauth_arr = explode('\t', $loginauth_str);
		$school_id = $loginauth_arr[2];
		$name = $loginauth_arr[0];
		$real_name = $loginauth_arr[3];
		
		// 预约学车订单
		$orderlist = $mlearncar->getOrderList(0,5,$school_id,1);

		// 报名驾校订单
		$schoolorderlist = $mlearncar->getSchoolOrderList($school_id);

		// 检测密码
		$arr = array('school_id'=>$school_id, 'password'=>md5('123456'));
		$changepwd = $madmin->checkPwd($arr); 
		$smarty->assign('school_loginauth', $_SESSION['school_loginauth']);
		$smarty->assign('orderlist', $orderlist);
		$smarty->assign('schoolorderlist', $schoolorderlist);
		$smarty->assign('name', $name);
		$smarty->assign('real_name', $real_name);
		$smarty->assign('changepwd', $changepwd);
		$smarty->assign('op',$op);
		$smarty->assign('manage_config', $manage_config);
		$smarty->display('admin/index.html');

	// 登出
	} else if($op == 'logout') {
		isetcookie('name', '');
		isetcookie('school_id', '');
		session_destroy($_SESSION['school_loginauth']);
		unset($_SESSION['school_loginauth']);
		iheader('location:index.php?action=admin&op=login');

	// 系统信息
	} else if($op == 'info') {
		if(!isset($_SESSION['school_loginauth'])) {
			echo "<script>location.href='index.php?action=admin&op=login';</script>";
			exit();
		}
		$smarty->assign('school_loginauth', $_SESSION['school_loginauth']);
		$smarty->assign('name', $_COOKIE[$cookie_pre.'name']);
		$smarty->assign('ip', $onlineip);
		$smarty->display('admin/info.html');

	// 帮助文档
	} else if($op == 'help') {
		$smarty->display('admin/help/help.html');
	
	// 密码修改
	 }else if($op == 'changepwd') {
		$smarty->display('admin/help/changepwd.html');

	
	// 旧密码验证
	} else if($op == 'checkoldpass') {
		$manage_oldpassword = isset($_POST['manage_oldpassword']) ? trim($_POST['manage_oldpassword']) : 0;
		
		$loginauth_str = authcode($_SESSION['school_loginauth'], 'DECODE');
		$loginauth_arr = explode('\t', $loginauth_str);
		$school_id = $loginauth_arr[2];

		$arr = array(
			'oldpass'=>$manage_oldpassword,
			'school_id'=>$school_id
		);
		$res = $madmin->getoldpassword($arr);
        if($res) {
   	 		echo 1;
        	exit();     
        } else {
        	echo 2;
        	exit();
        }
	// 密码修改处理
	} else if($op == 'editpass') {
		$manage_password = isset($_POST['manage_password']) ? trim($_POST['manage_password']) : 0;
		$manage_repeat_password = isset($_POST['manage_repeat_password']) ? trim($_POST['manage_repeat_password']) : 0;

		if($manage_password != $manage_repeat_password && $manage_password == 0 && $manage_repeat_password == 0) {
			echo 1;
			exit();
		}
		$loginauth_str = authcode($_SESSION['school_loginauth'], 'DECODE');
		$loginauth_arr = explode('\t', $loginauth_str);
		$school_id = $loginauth_arr[2];

		$arr = array(
			'pass'=>$manage_repeat_password,
			'school_id'=>$school_id
		);
		$res = $madmin->changepassword($arr);
		if($res) {
			unset($_SESSION['school_loginauth']);
		}
		echo $res;
		exit();	
	
	}
?>