<?php  
	/**
	 * 教练端App首页获取正在等待学员的数量
	 * @param $coach_id 教练ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getCoachUserNum');
	$app->run();

	// 获取时间配置
	function getCoachUserNum() {
		Global $app, $crypt;
		$request = $app->request();
		$coach_id = $request->params('coach_id');
		$page = $request->params('page');
		$page = isset($page) ? $page : 1;
		$page = $page == 0 ? 1 : $page;
		$limit = 10;
		$start = ($page - 1) * $limit;
		
		if(empty($coach_id) || !isset($coach_id)) {
			$data = array('code'=>-1, 'data'=>'参数错误');
			echo json_encode($data);
			exit();
		}
		try {

			$db = getConnection();
			$list = array();
			$_list = array();
			$sql = "SELECT * FROM `cs_study_orders` WHERE `l_coach_id` = $coach_id AND `i_status` = 1 GROUP BY `l_user_id` LIMIT $start, $limit";
			$stmt = $db->query($sql);
			$order_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($order_list) {
				$list['num'] = count($order_list);
				foreach ($order_list as $key => $value) {
					$_list[$key]['l_user_id'] = $value['l_user_id'];
					$_list[$key]['s_order_no'] = $value['s_order_no'];
					$_list[$key]['s_user_name'] = $value['s_user_name'] == '' ? '嘻哈学员' : $value['s_user_name'];
					$_list[$key]['s_user_phone'] = $value['s_user_phone'];
					$_list[$key]['s_lisence_name'] = $value['s_lisence_name'];
					$_list[$key]['s_lesson_name'] = $value['s_lesson_name'];

					// 获取学员头像和性别
					$sql = "SELECT `sex`, `user_photo`, `photo_id`, `learncar_status`, `identity_id` FROM `cs_users_info` WHERE `user_id` = ".$value['l_user_id'];
					$stmt = $db->query($sql);
					$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
					if($user_info) {
						$_list[$key]['sex'] = $user_info['sex'];
						if($user_info['user_photo'] == '') {
							$_list[$key]['user_photo'] = '';
						} else {	
							$_list[$key]['user_photo'] = HOST.$user_info['user_photo'];
						}
						$_list[$key]['photo_id'] = $user_info['photo_id'];
						// 获取科目
						$_list[$key]['learncar_status'] = $user_info['learncar_status'];
			
						if($_list[$key]['learncar_status'] =='') {
							// 从订单中查看
							$sql = "SELECT `s_lesson_name` FROM `cs_study_orders` WHERE `l_user_id` = '".$value['l_user_id']."'";
							$_stmt = $db->query($sql);
							$s_lesson_name = $_stmt->fetch(PDO::FETCH_ASSOC);
							if(!empty($s_lesson_name)) {
								$_list[$key]['learncar_status'] = $s_lesson_name['s_lesson_name'].'学习中';
							} else {
								$_list[$key]['learncar_status'] = '科目一学习中';
							}
						} else {
								$_list[$key]['learncar_status'] = '科目一学习中';

						}

						// 获取学员是否报名驾校了
						$sql = "SELECT * FROM `cs_school_orders` WHERE `so_user_id` = '".$value['l_user_id']."' AND `so_user_identity_id` = '".$user_info['identity_id']."' AND `so_order_status` != 101";
						$stmt = $db->query($sql);
						$school_info = $stmt->fetch(PDO::FETCH_ASSOC);
						if($school_info) {
							// 查找驾校名称
							$sql = "SELECT `s_school_name` FROM `cs_school` WHERE `l_school_id` = '".$school_info['so_school_id']."'";
							$stmt = $db->query($sql);
							$school_name = $stmt->fetch(PDO::FETCH_ASSOC);
							if($school_name) {
								$_list[$key]['school_name'] = $school_name['s_school_name'];
							} else {
								$_list[$key]['school_name'] = '';
							}
							// 获取班制
							$sql = "SELECT * FROM `cs_school_shifts` WHERE `id` = '".$school_info['so_shifts_id']."'";
							$stmt = $db->query($sql);
							$shifts_info = $stmt->fetch(PDO::FETCH_ASSOC);
							if($shifts_info) {
								$_list[$key]['sh_title'] = $shifts_info['sh_title'];
							} else {
								$_list[$key]['sh_title'] = '';
							}
								
						} else {
							$_list[$key]['school_name'] = '';	
						}

					} else {
						$_list[$key]['sex'] = 0;
						$_list[$key]['user_photo'] = '';
						$_list[$key]['learncar_status'] = '';
					}


				}
			} else {
				$list['num'] = 0;
			}


			$db = null;
			$list['user_list'] = $_list;
			$data = array('code'=>200, 'data'=>$list);
			// echo "<pre>";
			// print_r($data);
			echo json_encode($data);
			exit();

		} catch(PDOException $e) {
			setapilog('get_coach_user_num:params[coach_id:'.$coach_id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit();
		}
	}

?>