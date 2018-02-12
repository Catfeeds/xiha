<?php  

	/**
	 * 获取教练详情配置(包含已经被预约或者没被预约状态)
	 * @param $lesson_type 科目 
	 * @param $licence_type 牌照 
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
	$app->post('/','getArticleDetail');
	$app->run();

	// 获取教练时间
	function getArticleDetail() {
		Global $app, $crypt;
		$request = $app->request();
		$article_id = $request->params('article_id');
		try {
			$db = getConnection();
			// 查找文章详情
			$sql = "SELECT `ID` as `article_id`, `post_author`, `post_title`, `guid`, `post_content`, `post_date` FROM `cs_posts` WHERE `ID` = '{$article_id}' AND `post_status` = 'publish'";
			$stmt = $db->query($sql);
			$post_info = $stmt->fetch(PDO::FETCH_ASSOC);

			if($post_info) {
				// 获取缩略图
				$sql = "SELECT m.`meta_value` FROM `cs_posts` as p LEFT JOIN `cs_postmeta` as m ON p.`ID` = m.`post_id` WHERE m.`meta_key` = '_thumbnail_id' AND m.`post_id` = '{$article_id}'";
				$stmt = $db->query($sql);
				$meta_value_info = $stmt->fetch(PDO::FETCH_ASSOC);
				if($meta_value_info) {
					$meta_value = $meta_value_info['meta_value'];
					$sql = "SELECT `guid` FROM `cs_posts` WHERE `ID` = '{$meta_value}'";
					$stmt = $db->query($sql);
					$guid_info = $stmt->fetch(PDO::FETCH_ASSOC);
					if($guid_info) {
						$_thumbnail_id = $guid_info['guid'];  // 特色图地址
					} else {
						$_thumbnail_id = '';  // 特色图地址
					}
				} else {
					$_thumbnail_id = '';
				}
				$post_info['thumbnail_img'] = $_thumbnail_id;
				// 获取视频从文章附属表找
				$sql = "SELECT m.`meta_value` FROM `cs_postmeta` as m LEFT JOIN `cs_posts` as p ON p.`ID` = m.`post_id` WHERE m.`post_id` = '{$article_id}' AND `meta_key` = 'enclosure'";
				$stmt = $db->query($sql);
				$meta_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$_list = array();
				if($meta_info) {
					foreach ($meta_info as $key => $value) {

						$_value = explode("\n", $value['meta_value']);
						if(is_array($_value)) {
							$thumb = $_value[0];
						} else {
							$thumb = '';
						}
						$_list[] = $thumb;
					}
				}
				$db = null;
				$post_info['video_list'] = array_values($_list);
				$data = array('code'=>200, 'data'=>$post_info);
			} else {
				$data = array('code'=>-1, 'data'=>'当前文章不存在');
			}
			echo json_encode($data);

		} catch(PDOException $e) {
			setapilog('get_coach_detail:params[id:'.$id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
		}
	}

?>