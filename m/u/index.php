<?php 
	/**
	 * 教练登录 (包含二维码地址，二维码分享出去的链接地址{web URL}，客服电话，教练基本信息)
	 * @param $coach_phone 教练手机
	 * @param $coach_pass 教练密码
	 * @return 
	 * @author cx
	 **/
	require 'Slim/Slim.php';
	require '../include/config.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim(array(
			'templates.path' => './templates'
		));

	// 404错误
	$app->notFound(function () use ($app) {
	    $app->render('404.php', array('root_path'=>ROOT_PATH));
	});
	// 500错误
	$app->error(function (\Exception $e) use ($app) {
	    $app->render('500.php', array('root_path'=>ROOT_PATH));
	});
    $app->response->headers->set('Content-Type', 'text/html;charset=utf8');
	$app->get('/:code','index');
	$app->post('/app/ajax', 'ajax');
	$app->run();

	function index($code) {
		Global $app;
		$from = 'u';
		try {
            $db = getConnection();
			$sql = "SELECT `content`, `name`, `qrcode`, `phone` FROM `cs_admin` WHERE `qrcode` = '{$code}' and `role_id` = 1"; //10
	        $stmt = $db->query($sql);
	        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if($user_info) {
                $app->render('index.php', array('from'=>$from, 'user_info'=>$user_info), 200);
            } else {
                $app->render('404.php', array('root_path'=>ROOT_PATH));
            }
			return;
		}catch(PDOException $e) {
            $data = array('code'=>1, 'data'=>$e->getMessage());
            // $data = array('code'=>1, 'data'=>'网络错误');
            exit(json_encode($data));
		}

	}

	function ajax() {
		Global $app;
        $app->response->headers->set('Content-Type', 'application/json; charset=utf8');
		try {
			$db = getConnection();
			$r = $app->request();
	        if ( !$r->isPost() ) {
        		$data = array('code' => 106, 'data' => '请求错误');
	            exit( json_encode($data) );
	        }
        	$p = $r->params();
        	$phone = isset($p['phone']) ? $p['phone'] : '';
        	$name = isset($p['name']) ? $p['name'] : '';
        	$qrcode = isset($p['qrcode']) ? $p['qrcode'] : '';
        	if($phone == '' || $name == '' || $qrcode == '') {
        		$data = ['code'=>400, 'msg'=>'填写手机号', 'data'=>''];
        	    exit( json_encode($data) );
        	}
        	$url = "http://m.xihaxueche.com:8001/html_h5/index.html";
        	$addtime = time();
        	$sql = "SELECT `content` FROM `cs_admin` WHERE `name`='{$name}' AND `qrcode` = '{$qrcode}'";
        	$stmt = $db->query($sql);
        	$admin_info = $stmt->fetch(PDO::FETCH_ASSOC);
        	if(!$admin_info) {
    			$data = ['code'=>200, 'msg'=>'_直接下载', 'data'=>['url'=>$url]];
        	    exit( json_encode($data) );
        	}
            $content = $admin_info['content'];
        	$sql = "SELECT 1 FROM `cs_operation_app_records` WHERE `user_phone` = '{$phone}'";
        	$stmt = $db->query($sql);
        	$record_info = $stmt->fetch(PDO::FETCH_ASSOC);
        	$data = [];
        	if($record_info) {
        		$data = ['code'=>200, 'msg'=>'直接下载', 'data'=>['url'=>$url]];
        	    exit( json_encode($data) );
        	}
        	$sql = "INSERT INTO `cs_operation_app_records` (`operation_name`, `operation_content`, `user_phone`, `addtime`) VALUES ('{$name}', '{$conent}', '{$phone}', '{$addtime}')";
        	$result = $db->query($sql);
        	if($result) {
        		$data = ['code'=>200, 'msg'=>'获取运营人员下载链接成功', 'data'=>['url'=>$url]];
        	} else {
        		$data = ['code'=>200, 'msg'=>'关联运营人员失败', 'data'=>['url'=>$url]];
        	}
        	exit( json_encode($data) );

		}catch(PDOException $e) {
			$data = array('code'=>1, 'msg'=>'', 'data'=>$e->getMessage());
            exit( json_encode($data) );
		}
	}

	// 连接PDO
	function getConnection() {
	    $dbhost="127.0.0.1";
	    $dbuser="root";
        $dbpass="";
        $dbname="xihaxueche";
        // $dbname="xihaxueche";
        // $dbpass="T!fc!Gqy5T@wLqMA";
	    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, array(PDO::ATTR_PERSISTENT => true));
	    $dbh->exec("SET NAMES 'UTF8'");
	    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    return $dbh;
	} /* for PDO getConnection() */

?>