<?php  

	// 车辆管理

	header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','an')) ? $op : 'index';

	$mcar = new mcar($db);

	if($op == 'index') {

		$smarty->display('about/index.html');
	} elseif($op == 'an') {
		$smarty->display('an_about/index.html');
	}