<?php  
	/**
	 * 教练端修改登陆密码
	 * @param int $coach_id 教练id
	 * @param string $coach_phone 教练手机号
	 * @param string $old_pass 旧密码
	 * @param string $new_pass 输入新密码
	 * @param string $new_repeat_pass 重复输入新密码
     * @return json
	 * @author cx
	 **/
	require '../../Slim/Slim.php';
	require '../../include/common.php';
	require '../../include/crypt.php';
	require '../../include/functions.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->any('/','changePass');
	$app->run();

	function changePass() {
		Global $app, $crypt;
		$request = $app->request();
		$response = $app->response();
 		if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[change_pass] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            exit(json_encode($data));
        }
        //  获取请求参数并判断合法性
        $validate_result = validate(
        	array(
        		'coach_id'          =>'INT', 
        		'coach_phone'       =>'STRING', 
        		'old_pass'          =>'STRING', 
        		'new_pass'          =>'STRING', 
        		'new_repeat_pass'   =>'STRING', 
    		), $request->params());

        if (!$validate_result['pass']) {
            exit(json_encode($validate_result['data']));
        }
		$p = $request->params();
		$coach_id = $p['coach_id'];
		$coach_phone = $p['coach_phone'];
		$old_pass = md5($p['old_pass']);
		$new_pass = md5($p['new_pass']);
		$new_repeat_pass = md5($p['new_repeat_pass']);

		if($new_pass != $new_repeat_pass) {
			$data = array('code'=>110, 'data'=>'密码不相同');
			exit( json_encode($data) );
		}

		if($old_pass == $new_repeat_pass) {
			$data = array('code'=>102, 'data'=>'请勿与原密码设置相同密码');
			exit( json_encode($data) );	
		}

		try {
			$db = getConnection();
			$user = DBPREFIX.'user';
            $coach = DBPREFIX.'coach';
			$sql = "SELECT user.`l_user_id` FROM `{$user}` AS user LEFT JOIN `{$coach}` AS coach ON user.`l_user_id` = coach.`user_id` ";
			$where = " WHERE user.`i_user_type` = 1 AND coach.`l_coach_id` = :id AND user.`s_phone` = :phone ";
			$sql .= $where;
			$stmt = $db->prepare($sql);
			$stmt->bindParam('id', $coach_id);
			$stmt->bindParam('phone', $coach_phone);
			$stmt->execute();
			$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($user_info)) {
				$data = array('code'=>104, 'data'=>'账号不存在');
				exit( json_encode($data) );
			}

			// 老密码错误
			$sql .= ' AND user.`s_password` = :password';
			$stmt = $db->prepare($sql);
			$stmt->bindParam('id', $coach_id);
			$stmt->bindParam('phone', $coach_phone);
			$stmt->bindParam('password', $old_pass);
			$stmt->execute();
			$_user_info = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($_user_info)) {
				$data = array('code'=>102, 'data'=>'原密码错误');
				exit( json_encode($data) );
			}

			$sql = "UPDATE `{$user}` SET `s_password` = :password";
			$sql .= " WHERE `l_user_id` = :uid ";
			$stmt = $db->prepare($sql);
			$stmt->bindParam('password', $new_repeat_pass);
			$stmt->bindParam('uid', $user_info['l_user_id']);
			$res = $stmt->execute();
			if($res) {
				$data = array('code'=>200, 'data'=>'修改成功');
			} else {
				$data = array('code'=>400, 'data'=>'修改失败');
			}
			$db = null;
			exit( json_encode($data) );

		} catch(PDOException $e) {
            slimLog($request, $response, $e, 'PDO数据库异常');
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		} catch(Exception $e) {
            slimLog($request, $response, $e, 'slim应用解析异常');
			$data = array('code'=>1, 'data'=>'网络异常');
			echo json_encode($data);
			exit;
		}
	}

?>
