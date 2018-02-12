<?php 
	/**
	 * 在线模拟首页
	 * @param $coach_phone 教练手机
	 * @param $coach_pass 教练密码
	 * @return 
	 * @author cx
	 **/
	require 'Slim/Slim.php';
	require '../include/config.php';
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
	$crypt = new Encryption;
    $app->response->headers->set('Content-Type', 'text/html;charset=utf8');
	$app->get('/index-:sid-:os.html','index');
	$app->get('/default-:sid-:ct-:st-:os.html','def');
	$app->get("/exercise-:sid-:ct-:st-:t-:chapterid-:os.html",'exercise'); // 题目列表
	$app->get("/share-:sid-:ct-:st-:score-:os.html",'share'); // 分享列表
	$app->get("/shareresult-:sid-:ct-:st-:score-:os.html",'shareresult'); // 分享后的结果表
	$app->get("/test-:sid-:ct-:st-:os-:uid.html",'test'); // 模拟考试题目列表
	$app->get("/chapter-:sid-:ct-:st-:t-:os.html",'chapter');
	$app->get("/myerr-:sid-:ct-:st-:t-:os.html",'myerr');
	$app->get("/u/r=:re_url",'login');
	$app->get("/forget/r=:re_url",'forget');
	$app->run();
	
	//选择首页
	function index($sid, $os) {
		Global $app;
		if(!is_numeric($sid)) {
			$app->notFound();
		}
		$param = array('root_path'=>ROOT_PATH, 'sid'=>$sid, 'os'=>$os, 'r'=>microtime());
		$app->render('index.php', $param, 200);
		return;
	}
	
	//练习首页
	function def($sid, $ct, $st, $os) {
		Global $app;
		if(!is_numeric($sid)) {
			$app->notFound();
		}
		$ctype_arr = array('C1', 'A1', 'A2', 'D');
		$stype_arr = array(1, 4);
		if(!in_array($ct, $ctype_arr) || !in_array($st, $stype_arr)) {
			$app->notFound();
		}
		$param = array('root_path'=>ROOT_PATH, 'sid'=>$sid, 'ctype'=>$ct, 'stype'=>$st, 'r'=>microtime(), 'os'=>$os);
		$app->render('default.php', $param, 200);
		return;
	}
	
	//练习题目页
	function exercise($sid, $ct, $st, $t, $chapterid, $os) {
		Global $app;
		if(!is_numeric($sid) || !is_numeric($t)) {
			$app->notFound();
		}
		$ctype_arr = array('C1', 'A1', 'A2', 'D');
		$stype_arr = array(1, 4);
		if(!in_array($ct, $ctype_arr) || !in_array($st, $stype_arr)) {
			$app->notFound();
		}
		// 判断练习类型 1：顺序练习 2：随机练习  3：章节练习  4：模拟考试  5：练习错题  6：模拟错题  7：我的收藏
		$param = array('root_path'=>ROOT_PATH, 'host'=>HOST,'sid'=>$sid, 'ctype'=>$ct, 'stype'=>$st, 't'=>$t, 'chapterid'=>$chapterid, 'r'=>microtime(), 'os'=>$os);
		switch($t) {
			case "1":
				$app->render('sequence.php', $param, 200);
				break;
			case "2":
				$app->render('random.php', $param, 200);
				break;
			case "3":
				$app->render('chapter_test.php', $param, 200);
				break;
			case "4":
				$app->render('web_test.php', $param, 200);
				break;
			case "5":
				$app->render('errq.php', $param, 200);
				break;
			case "6":
				$app->render('terrq.php', $param, 200);
				break;
			case "7":
				$app->render('collect.php', $param, 200);
				break;
		}
		return;
	}
	
	//模拟练习题目页（给移动端用）
	function test($sid, $ct, $st, $os, $uid) {
		Global $app;
		if(!is_numeric($sid)) {
			$app->notFound();
		}
		if($os != 'ios' && $os != 'an' && $os != 'phone') {
			$app->notFound();
		}
		$ctype_arr = array('C1', 'A1', 'A2', 'D');
		$stype_arr = array(1, 4);
		if(!in_array($ct, $ctype_arr) || !in_array($st, $stype_arr)) {
			$app->notFound();
		}
		$param = array('root_path'=>ROOT_PATH, 'host'=>HOST,'sid'=>$sid, 'ctype'=>$ct, 'stype'=>$st, 'uid'=>$uid, 'r'=>microtime(), 'os'=>$os);
		$app->render('app_test.php', $param, 200);
		return;
	}

	//章节列表页
	function chapter($sid, $ct, $st, $t, $os) {
		Global $app;
		$param = array('root_path'=>ROOT_PATH, 'host'=>HOST,'sid'=>$sid, 'ctype'=>$ct, 'stype'=>$st, 't'=>$t, 'r'=>microtime(), 'os'=>$os);
		$app->render('chapter.php', $param, 200);
		return;
	}
	
	//我的错题
	function myerr($sid, $ct, $st, $t, $os) {
		Global $app;
		$param = array('root_path'=>ROOT_PATH, 'host'=>HOST,'sid'=>$sid, 'ctype'=>$ct, 'stype'=>$st, 't'=>$t, 'r'=>microtime(), 'os'=>$os);
		$app->render('myerr.php', $param, 200);
		return;
	}
	
	//登陆
	function login($re_url) {
		Global $app;
		$re_url_params = explode(',', $re_url);
		$sid = $re_url_params[0];
		$ct = $re_url_params[1];
		$st = $re_url_params[2]; 
		$t = $re_url_params[3];
		$chapterid = $re_url_params[4];
		$os = $re_url_params[5];
		$param = array('root_path'=>ROOT_PATH, 'host'=>HOST,'sid'=>$sid, 'ctype'=>$ct, 'stype'=>$st, 't'=>$t, 'chapterid'=>$chapterid, 'r'=>microtime(), 'os'=>$os);
		$app->render('login.php', $param, 200);
		return;
	}
	//忘记密码
	function forget($re_url) {
		Global $app;
		$re_url_params = explode(',', $re_url);
		$sid = $re_url_params[0];
		$ct = $re_url_params[1];
		$st = $re_url_params[2]; 
		$t = $re_url_params[3];
		$chapterid = $re_url_params[4];
		$os = $re_url_params[5];
		$param = array('root_path'=>ROOT_PATH, 'host'=>HOST,'sid'=>$sid, 'ctype'=>$ct, 'stype'=>$st, 't'=>$t, 'chapterid'=>$chapterid, 'r'=>microtime(), 'os'=>$os);
		$app->render('forget_pwd.php', $param, 200);	
		return;
	}

	// 分享页面
	function share ($sid, $ct, $st, $score, $os) {
		Global $app;
		$param = array('root_path'=>ROOT_PATH, 'host'=>HOST, 'sid'=>$sid, 'ctype'=>$ct, 'stype'=>$st, 'os'=>$os, 'score' => $score);
		$app->render('share.php', $param, 200);	
		return;
	}

	// 分享后点击的页面
	function shareresult ($sid, $ct, $st, $score, $os) {
		Global $app;
		// $user_info = DB::table();
		$param = array('root_path'=>ROOT_PATH, 'host'=>HOST, 'sid'=>$sid, 'ctype'=>$ct, 'stype'=>$st, 'os'=>$os, 'score' => $score);
		$app->render('share_result.php', $param, 200);	
		return;
	}

?>