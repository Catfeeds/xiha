<?php  

	/**
	 * 获取更多文章列表
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author sunweiwei
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getArticlelist');
	$app->run();

	function getArticlelist() {
		Global $app, $crypt;
		$request = $app->request();
		$taxonomy_id = $request->params('taxonomy_id');	//1）获取文章分类id	
		$page = $request->params('page');	//1）获取文章分类id	
		$taxonomy_id = !empty($taxonomy_id) ? $taxonomy_id : '2';  //如果没传默认3---南新华专栏
		$page = !empty($page) ? $page : '1';  //如果没传默认3---南新华专栏

		$limit = 20;
		$start = ($page - 1) * $limit;

		try {
			$sql = "SELECT `object_id` FROM `cs_term_relationships` WHERE `term_taxonomy_id` = $taxonomy_id ORDER BY `object_id` DESC";		//2）根据接收的分类ID查询“分类关系表”查询文章ID
			$db = getConnection();
			$stmt = $db->query($sql);
			$post_ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$post_id = array(0);
			if ($post_ids) {
				foreach ($post_ids as $key => $value) {
					$post_id[] = $value['object_id'];
				}
			}
			$sql = "SELECT `ID`, `post_date`,`post_excerpt`, `post_title` FROM `cs_posts` WHERE `ID` IN (".implode(',',$post_id).") AND `post_status` = 'publish' ORDER BY `post_date` DESC LIMIT $start, $limit";		//3）根据查询的文章ID查询cs_posts表中ID，post_date（文章发布日期） post_excerpt（文章摘要），post_title（文章标题）

			$stmt = $db->query($sql);
			$post_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$list =array();
			if($post_info) {
				foreach ($post_info as $key => $value) {	
					$list[$key]['article_id'] = $value['ID'];//文章id
					$list[$key]['post_title'] = $value['post_title'];//文章标题
					$list[$key]['post_date'] = $value['post_date'];//文章发布时间
					$list[$key]['post_excerpt'] = $value['post_excerpt'];//文章摘要
					$sql = "SELECT m.`meta_value` FROM `cs_posts` as p LEFT JOIN `cs_postmeta` as m ON p.`ID` = m.`post_id` WHERE m.`meta_key` = '_thumbnail_id' AND m.`post_id` = '{$value['ID']}'";	
					//4）根据cs_posts表中查出的ID在postmeta表中查询特色图片地址
	                $stmt = $db->query($sql);
	                $meta_value_info = $stmt->fetch(PDO::FETCH_ASSOC);
	                if($meta_value_info) {
	                    $meta_value = $meta_value_info['meta_value'];
	                    $sql = "SELECT `guid` FROM `cs_posts` WHERE `ID` = '{$meta_value}'";
	                    $stmt = $db->query($sql);
	                    $guid_info = $stmt->fetch(PDO::FETCH_ASSOC);
	                    if($guid_info) {                        
	                        $_thumbnail_id = $guid_info['guid'];
	                    // 特色图地址                
	                    } else {                        
	                        $_thumbnail_id = '';
	                    // 特色图地址                
	                    }              
	                } else {                    
	                        $_thumbnail_id = '';
	                }
	                $list[$key]['thumbnail_img'] = $_thumbnail_id;//特色图url

				}	
			}				
			$data = array('code'=>200, 'data'=>$list);
			$db = null;
			echo json_encode($data);
			exit();				
		} catch (PDOException $e) {				
			setapilog('get_article:error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}
	}

?>