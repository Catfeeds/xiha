<?php 
	session_start();
	if(session_destroy()) {
		$data = array('code'=>200, 'data'=>'退出成功');
	} else {
		$data = array('code'=>100, 'data'=>'退出失败');
	}
	echo json_encode($data);
?>