<?php  

	// 班制模块

	header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','add','edit','del','editcheck','cancel', 'delmore')) ? $op : 'index';

	if(!isset($_SESSION['loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}
	
	$morder = new morder($db);

	if($op == 'index') {
		$smarty->display('shifts/index.html');
	}

?>