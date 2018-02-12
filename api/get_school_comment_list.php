<?php  
	/**
	 * 获取所有驾校评价列表
	 * @param $id int 驾校ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getSchoolCommentList');
	$app->run();

	// 获取教练学员信息
	function getSchoolCommentList() {
		Global $app, $crypt;

		$request = $app->request();
		$id = $request->params('id');

		try {
			// 获取评价
			$db = getConnection();
			$sql = "SELECT `l_coach_id` FROM `cs_coach` WHERE `s_school_name_id` = $id";
			$stmt = $db->query($sql);
			$coach_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$list = array();
			$comment_detail = array();

			if($coach_list) {
				foreach ($coach_list as $key => $value) {
					$coach_ids[] = $value['l_coach_id'];
				}			
				$sql = "SELECT `school_star`, `school_content`, `addtime`, `user_id` FROM `cs_coach_comment` WHERE `coach_id` IN (".implode(',', $coach_ids).") OR `school_id` = $id";
				$stmt = $db->query($sql);
				$comment_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$school_star_num = 0;
				if($comment_list) {
					foreach ($comment_list as $key => $value) {
						$list[$key]['school_star'] = intval($value['school_star']);
						$list[$key]['school_content'] = $value['school_content'];
						if($value['addtime'] == 0) {
							$list[$key]['addtime'] = date('Y-m-d H:i', time());
						} else {
							$list[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);
						}	

						// 获取学员信息
						$sql = "SELECT u.`s_username`, i.`user_photo`, i.`photo_id` FROM `cs_user` as u LEFT JOIN `cs_users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`l_user_id` = '".$value['user_id']."' AND u.`i_user_type` = 0";
						$stmt = $db->query($sql);
						$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
						if($user_info) {
							$list[$key]['s_username'] = $user_info['s_username'] == '' ? '嘻哈学车学员' : $user_info['s_username'];

							if($user_info['user_photo']) {
								if(file_exists(__DIR__.'/../sadmin/'.$user_info['user_photo'])) {
									$list[$key]['user_photo'] = S_HTTP_HOST.$user_info['user_photo'];
								} else {
									$list[$key]['user_photo'] = HTTP_HOST.$user_info['user_photo'];
								}
							} else {
								$list[$key]['user_photo'] = 'images/default/1.png';
							}
							$list[$key]['photo_id'] = $user_info['photo_id'] == null ? "1" : $user_info['photo_id'];	
						} else {
							$list[$key]['s_username'] = '嘻哈学车学员';
							$list[$key]['user_photo'] = 'images/default/1.png';
							$list[$key]['photo_id'] = "1";	
						}
														
						$school_star_num += intval($value['school_star']);
					}

					// 总评价数
					$comment_num = count($comment_list);
					// 平均星级
					$average_star_num = intval($school_star_num / $comment_num);
				} else {
					$comment_num = 0;
					$average_star_num = 0;	
				}
					
			} else {
				$comment_num = 0;
				$average_star_num = 0;
			}

			$db = null;
			$comment_detail['s_school_id'] = $id;
			$comment_detail['total_comment_num'] = $comment_num;
			$comment_detail['average_star_num'] = $average_star_num;
			$comment_detail['comment_list'] = $list;
			$data = array('code'=>200, 'data'=>$comment_detail);
			echo json_encode($data);

		} catch (PDOException $e) {
			setapilog('get_school_comment_list:params[id:'.$id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}

	}

?>