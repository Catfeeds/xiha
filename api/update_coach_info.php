<?php  
	/**
	 * 更改个性签名
	 * @param $coach_id int 教练ID
	 * @param $content string 签名
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','updatecoachinfo');
	$app->run();

	// 更新教练信息
	function updatecoachinfo() {
		Global $app, $crypt;
		$request = $app->request();
		$coach_id = $request->params('coach_id');
		$content = $request->params('content');

		$sql = "UPDATE `cs_coach` SET `s_coach_content` = '".$content."' WHERE `l_coach_id` = $coach_id";
		try {
			$db = getConnection();
			$stmt = $db->query($sql);
			if($stmt) {
				$data = array('code'=>200, 'data'=>'更新成功');
			} else {
				$data = array('code'=>-1, 'data'=>'更新失败');
			}
			echo json_encode($data);
			
		} catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('update_coach_info:params[coach_id:'.$coach_id.',content:'.$content.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}
	}

?>