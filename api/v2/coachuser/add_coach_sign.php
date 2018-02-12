<?php  
	/**
	 * 添加修改教练签名
	 * @param int $coach_id 教练id
	 * @param string $coach_content 教练签名内容
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
	$app->any('/','addCoachSign');
	$app->run();

	function addCoachSign() {
		Global $app, $crypt;
		$request = $app->request();
 		if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[add_coach_sign] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            exit(json_encode($data));
        }
        //  获取请求参数并判断合法性
        $validate_result = validate(
        	array(
        		'coach_id'      =>'INT', 
        		'coach_content' =>'STRING', 
    		), $request->params());

        if (!$validate_result['pass']) {
            exit(json_encode($validate_result['data']));
        }
		$p = $request->params();
		try {
			$db = getConnection();
			$coach_id = $p['coach_id'];
			$coach_content = $p['coach_content'];
            //签名大小限制
			$content_len = mb_strlen($coach_content);
			if($content_len > 200) {
				$data = array('code'=>110, 'data'=>'超出了200个字节的长度限制');
				exit(json_encode($data));
			}

			$coach = DBPREFIX.'coach';
			$sql = "UPDATE `{$coach}` SET `s_coach_content` = :coach_content WHERE `l_coach_id` = :coach_id";
			$stmt = $db->prepare($sql);
			$stmt->bindParam('coach_content', $coach_content);
			$stmt->bindParam('coach_id', $coach_id);
			$res = $stmt->execute();

			if($res) {
				$data = array('code'=>200, 'data'=>'发布成功');
			} else {
				$data = array('code'=>400, 'data'=>'发布失败');
			}
			$db = null;
			exit(json_encode($data));

		} catch(PDOException $e) {
            setapilog('[add_coach_sign] [:error] [client ' . $request->getIp() . '] [params ' . serialize($request->params()) . '] [1 ' . $e->getMessage() . ']');
			$data = array('code'=>1, 'data'=>'网络错误');
			exit(json_encode($data));
		}
	}
?>
