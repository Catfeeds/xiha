<?php  

	// 广告管理模块

	header("Content-type: text/html; charset=utf-8");

	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','adsorder','adsmanager','adsposition','adslevel','addposition','edit','del')) ? $op : 'index';

	if(!isset($_SESSION['loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}

	$mads = new mads($db);

	if($op == 'index') {

		echo "广告管理";
		$smarty->display('ads/index.html');

	} else if($op == 'adsorder') {

		echo "广告订单管理";
		$smarty->display('ads/adsorder.html');

	} else if($op == 'adsmanager') {

		echo "广告商管理";
		$smarty->display('ads/adsmanager.html');

	} else if($op == 'adsposition') {

		$ads_positions = $mads->getAdsPositions();
		$smarty->assign('ads_positions', $ads_positions);
		$smarty->display('ads/adsposition.html');

	} else if($op == 'adslevel') {

		echo "等级管理";
		$smarty->display('ads/adslevel.html');

	} else if($op == 'addposition') {

		echo "添加广告位";
		$smarty->display('ads/adsposition.html');

	} else if($op == 'edit') {

		echo "编辑广告位";
		$smarty->display('ads/adsposition.html');

	} else if($op == 'del') {

		echo "删除广告位";
		$smarty->display('ads/adsposition.html');

	} 


 

?>