<?php 

	/**
	 * 登录接口(正常密码登录)
	 * @param $phone int 手机号码
	 * @param $pass string 密码
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author gaodacheng
	 **/

	require '../../Slim/Slim.php';
	require '../../include/common.php';
	require '../../include/crypt.php';
	require '../../include/functions.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->any('/','login');
	$app->run();

	// 登录验证
	function login() {
		global $app, $crypt;
        $r = $app->request();
		$phone = $r->params('phone'); //手机号
		$pass = md5($r->params('pass')); //密码 first md5()

        //验证请求方式 POST
        if ( !$r->isPost() ) {
            setapilog('[update_user_info] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            exit( json_encode(array('code' => 106, 'data' => '请求错误')) );
        }

        //取得参数列表
        $validate_ok = validate(array('phone' => 'INT', 'pass' => 'STRING'), $r->params());
        if ( !$validate_ok['pass'] ) {
            exit( json_encode($validate_ok['data']) );
        }

		$sql = "SELECT * FROM `cs_user` WHERE `i_status` = 0 AND `s_phone` = :phone AND `s_password` = :pass AND `i_user_type` = 2";
		try {

			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam('phone',$phone);
			$stmt->bindParam('pass',$pass);
			$stmt->execute();
			$user = $stmt->fetch(PDO::FETCH_ASSOC);

			if($user) {
                //返回登陆信息
                !empty($user['s_imgurl']) ? $user['s_imgurl'] = HOST . '/' . $user['s_imgurl'] : '';
				$data = array('code'=>200, 'data'=>$user);

				// 更新登录次数信息
				$sql = "SELECT `s_username`,`is_first` FROM `cs_user` WHERE `s_phone` = ".$phone;
				$stmt = $db->query($sql);
				$row = $stmt->fetchObject();
				if($row->s_username != "" && $row->is_first == 0) {
					$sql = "UPDATE `" . DBPREFIX . "user` SET `is_first` = 1 WHERE `s_phone` = ".$phone;
					$res = $db->query($sql);
				}

				$loginauth = $user['l_user_id'].'\t'.$user['s_username'].'\t'.$user['s_real_name'].'\t'.$user['s_phone'];
				$_SESSION['loginauth'] = $crypt->encrypt($loginauth);

			} else {
				$data = array('code'=>400, 'data'=>'登录失败');
			}
			echo json_encode($data);

		} catch(PDOException $e) {
			setapilog('[login] [:error] [phone,pass % '.$phone.','.$pass.'] [1 '.$e->getMessage() . ']');	
			echo json_encode( array('code'=>1, 'data'=>'网络错误') );
		}
	}


?>
