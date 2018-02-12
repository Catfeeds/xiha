<?php 
	//评价管理模块
	 header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','system_inv','system_main','system_instr','instructions_ks','instructions_jl','instructions_cl','instructions_xy','instructions_jx','instructions_pj','safe','serve')) ? $op : 'index';

	if(!isset($_SESSION['school_loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}
	$loginauth_str = authcode($_SESSION['school_loginauth'], 'DECODE');
	$loginauth_arr = explode('\t', $loginauth_str);
	$school_id = $loginauth_arr[2];

	if($op == 'index') {
		$smarty->display('admin/help/system_main.html');

	} else if($op == 'system_main') {
		$smarty->display('admin/help/system_main.html');

	} else if($op == 'system_inv') {
		$smarty->display('admin/help/system_inv.html');

	} else if($op == 'system_instr') {
		$smarty->display('admin/help/system_instr.html');
		
	} else if($op == 'instructions_ks') {
		$smarty->display('admin/help/instructions_ks.html');
	
	} else if($op == 'instructions_jl') {
		$smarty->display('admin/help/instructions_jl.html');
		
	} else if($op == 'instructions_cl') {
		$smarty->display('admin/help/instructions_cl.html');
		
	} else if($op == 'instructions_xy') {
		$smarty->display('admin/help/instructions_xy.html');
		
	} else if($op == 'instructions_jx') {
		$smarty->display('admin/help/instructions_jx.html');
		
	} else if($op == 'instructions_pj') {
		$smarty->display('admin/help/instructions_pj.html');
	} else if($op == 'safe') {
		$smarty->display('admin/help/safe.html');
	} else if($op == 'serve') {
		$smarty->display('admin/help/serve.html');
	}
?>