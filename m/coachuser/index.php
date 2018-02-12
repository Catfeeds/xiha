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
	// require '../include/crypt.php';
	require '../include/crypt.class.php';
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
	
	// $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$crypt = new Encryption;
    $app->response->headers->set('Content-Type', 'text/html;charset=utf8');
	$app->get('/token/:token','index');
	$app->run();

	function index($token) {
		Global $app, $crypt;
		$_token_str = $crypt->decode($token);
		if($_token_str == false) {
    		$app->response->headers->set('Content-Type', 'application/json;charset=utf8');
			$data = array('code'=>'103', 'data'=>'参数错误');
			echo json_encode($data);
			return;
		}
		$token_str = urldecode($_token_str);
		$token_arr = explode('|', $token_str);
		// if(4 != count($token_arr)) {
		// 	exit('101 parameters error');
		// }
		$coach_id = isset($token_arr[0]) ? $token_arr[0] : '';
		$coach_name = isset($token_arr[1]) ? $token_arr[1] : '';
		$coach_phone = isset($token_arr[2]) ? $token_arr[2] : '';
		$random_num = isset($token_arr[3]) ? $token_arr[3] : '';
		$from = 'coachuser';
		$app->render('index.php', array('coach_id'=>$coach_id, 'coach_name'=>$coach_name, 'coach_phone'=>$coach_phone, 'random_num'=>$random_num, 'from'=>$from), 200);
		return;
	}

?>