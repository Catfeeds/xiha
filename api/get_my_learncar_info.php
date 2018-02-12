<?php  
	/**
	 * 获取学员学车档案(已废除)
	 * @param $type int 1：获取教练信息 2：获取学员信息
	 * @param $member_id int 学员或者教练ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getmemberinfo');
	$app->run();

	// 获取教练学员信息
	function getmemberinfo() {

		Global $app, $crypt;
		$request = $app->request();
		$user_id = $request->params('user_id');
		$page = $request->params('page');
		$page = isset($page) ? $page : 1;
		$start = 10;
		$start = ($page-1) * $limit;
		try {
			$db = getConnection();

			// 判断当前有无用户
			$sql = "SELECT * FROM `cs_user` WHERE `l_user_id` = $user_id";
			$stmt = $db->query($sql);
			$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($user_info)) {
				$data = array('code'=>-1, 'data'=>'不存在当前用户');
				echo json_encode($data);
				exit();
			}

			$user_info['learncar_status'] = '科目二学习中';
			$user_info['learn_num'] = 5;
			$user_info['all_learn_time'] = 34;

			// 学车记录
			$sql = "SELECT * FROM `cs_study_orders` WHERE `l_user_id` = $user_id AND `i_status` = 2 ORDER BY `dt_order_time` DESC";
			$stmt = $db->query($sql);
			$study_orders_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
			

		} catch(PDOException $e) {
			setapilog('get_my_learncar_info:params[user_id:'.$user_id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}

	}