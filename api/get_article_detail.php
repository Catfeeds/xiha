<?php

	/**
	 * 获取文章详情 含有5条热评
     * @param integer $id 文章 ID
     * @param timestamp $t //文章的时间戳,  客户端作缓存处理用
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author gaodacheng
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->any('/','getArticleDetail');
	$app->run();

	function getArticleDetail() {
		Global $app, $crypt;
        
        //返回的结果数组
        $data = array();

		$request = $app->request();

        if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_article_detail] [:error] [client ' . $request->getIp() . '] [Method %' . $request->getMethod() . '] 错误的请求类型');
            echo json_encode($data);
            exit();
        }

		$article_id = $request->params('id');
        $post_timestamp = $request->params('t');

        $_article_info = array();

        //文章id进行检测 [begin]
        if ( !$article_id ) {
            $data['code'] = 101;
            $data['data'] = '参数错误';
			setapilog('[get_article_detail] [:error] [client ' . $request->getIp() . '] [id % ' . $article_id . '] [101 参数空]');
            echo json_encode($data);
            exit;
        } elseif ( !is_numeric($article_id) ) {
            $data['code'] = 102;
            $data['data'] = '参数错误';
			setapilog('[get_article_detail] [:error] [client ' . $request->getIp() . ']  [id % ' . $article_id . '] [102 参数期望一个数字类型]');
            echo json_encode($data);
            exit;
        }
        //文章id进行检测 [end]

        //对时间戳类型检测[begin]
        if ( $post_timestamp ) {
            if ( !is_numeric($post_timestamp) ) {
                $data['code'] = 102;
                $data['data'] = '参数错误';
			    setapilog('[get_article_detail] [:error] [client ' . $request->getIp() . ']  [id % ' . $article_id . '] [102 参数期望一个数字类型]');
                echo json_encode($data);
                exit;
            }
        } else {
            $post_timestamp = -1;
        }
        //对时间戳类型检测[end]

		try {
			$db = getConnection();
			// 查找文章详情
			$sql = "SELECT `ID` as `article_id`, `post_author` as `post_author_id`, `post_title`, `guid`, `post_content`, `post_modified` AS post_date FROM `cs_posts` WHERE `ID` = '{$article_id}' AND `post_status` = 'publish'";
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

				//$data = array('code'=>200, 'data'=>$post_info);
                $_article_info = $post_info;
                //获取发表人姓名
                $sql = "SELECT `display_name` FROM `cs_users` WHERE `ID` = '" . $_article_info['post_author_id'] . "'";
                //$data['sql'] = $sql;
                $stmt = $db->query($sql);
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                if ( $res ) {
                    $_article_info['post_author_name'] = $res['display_name'];
                } else {
                    $_article_info['post_author_name'] = '';
                    setapilog('[get_article_detail] [:error] [client ' . $request->getIp() . '] [id:' . $article_id . '] [103 用户名不存在]');
                }

                //获取发表人头像
                $metakey = 'simple_local_avatar';
                $sql = " SELECT `meta_value` FROM `". DBPREFIX ."usermeta` WHERE `user_id` = :uid AND `meta_key` = :metakey ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('uid', $_article_info['post_author_id'], PDO::PARAM_INT);
                $stmt->bindParam('metakey', $metakey, PDO::PARAM_STR);
                $stmt->execute();
                $user_photo = $stmt->fetch(PDO::FETCH_ASSOC);
                $user_photo_many_size = unserialize($user_photo['meta_value']);
                $full_size_user_photo = $user_photo_many_size['full'];
                if ( $full_size_user_photo ) {
                    $_article_info['post_author_photo'] = $full_size_user_photo;
                } else {
                    $_article_info['post_author_photo'] = '';
                }

                //将日期转为时间戳
                $_article_info['post_timestamp'] = strtotime($_article_info['post_date']);

                //根据$post_timestamp检测要不要返回文章数据
                if ( $post_timestamp == $_article_info['post_timestamp'] ) {
                    $data['code'] = 107;
                    $data['data'] = '此文章从上次请求未作任何修改';
                } else {
                    $data['code'] = 200;
                    $data['data']['article_info'] = $_article_info;
                }

			} else {
				$data = array('code'=>104, 'data'=>'当前文章不存在');
                setapilog('[get_article_detail] [:error] [client ' . $request->getIp() . '] [id:' . $article_id . '] [104 文章不存在]');
			}

            //关闭数据库连接
            $db = null;
			echo json_encode($data);

		} catch(PDOException $e) {
			setapilog('[get_coach_detail] [:error] [client ' . $request->getIp() . '] [id:' . $article_id . '] ' . '[1' . $e->getMessage() . ']');
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
		}
	}

?>
