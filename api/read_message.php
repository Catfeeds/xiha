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
	$app->post('/','readMsg');
	$app->run();

	function readMsg() {
		Global $app, $crypt;
		$request = $app->request();
		$i_sender_id = $request->params('id');
		$i_user_id = $request->params('member_id');
		$dt_read = time();
		$s_beizhu = '已读';
		$addtime = time();

		if($i_sender_id == '' || $i_user_id == '') {
			$data = array('code'=>-3, 'data'=>'参数错误');
			echo json_encode($data);
			exit();
		}

		try {
			$db = getConnection();
			$sql = "SELECT * FROM `cs_sms_sender` WHERE `id` = $i_sender_id AND `member_id` = $i_user_id";
			$stmt = $db->query($sql);
			$sender_info = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($sender_info)) {
				$data = array('code'=>-1, 'data'=>'消息不存在');
				echo json_encode($data);
				exit();
			}
			$sql = "UPDATE `cs_sms_sender` SET `is_read` = 1 WHERE `id` = $i_sender_id AND `member_id` = $i_user_id";
			$res = $db->query($sql);
			if($res) {
				$data = array('code'=>200, 'data'=>'已读成功');
			} else {	
				$data = array('code'=>-2, 'data'=>'已读失败');
			}	
			echo json_encode($data);

		} catch(PDOException $e) {
			setapilog('read_message:params[id:'.$i_sender_id.',member_id:'.$i_user_id.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
		}
	}

?>