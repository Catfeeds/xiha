<?php  
	/**
	 * 获取成员（教练和学员）的推送消息
	 * @param $member_id int 成员ID
	 * @param $member_type 成员类型 
	 * @param $page 页码 
	 * @return string AES对称加密（加密字段xhxueche）
		* @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getMsg');
	$app->run();

	function getMsg() {
		Global $app, $crypt;
		$db = getConnection();
		$request = $app->request();
		$ids = $request->params('ids');
		$ids_arr = array_filter(explode(',', $ids));
		if(empty($ids_arr)) {
			$data = array('code'=>-3, 'data'=>'请选择需要删除的消息');
			echo json_encode($data);
			exit();
		}
		$sql = "SELECT * FROM `cs_sms_sender` WHERE `id` IN (".implode(',', $ids_arr).")";
		$stmt = $db->query($sql);
		$message_info = $stmt->fetch(PDO::FETCH_ASSOC);

		if(empty($message_info)) {
			$data = array('code'=>-1, 'data'=>'不存在此消息，请重新刷新');
			echo json_encode($data);
			exit();
		}
		$sql = "UPDATE `cs_sms_sender` SET `is_read` = 101 WHERE `id` IN (".implode(',', $ids_arr).")";
		$stmt = $db->query($sql);
		if($stmt) {
			$data = array('code'=>200, 'data'=>'删除成功');
		} else {
			$data = array('code'=>-2, 'data'=>'删除失败');
		}
		echo json_encode($data);
		exit();
	}