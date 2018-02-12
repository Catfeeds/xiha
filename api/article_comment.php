<?php  
	/**
	 * 文章评论接口
	 * @param $id bigint 文章ID
	 * @param $content text 评论内容
	 * @param $comment_id  被评论ID
	 * @param $user_id bigint 用户ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author sunweiwei
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','addComment');
	$app->run();

	function addComment() {
		Global $app, $crypt;
		$request = $app->request();
		$comment_post_ID = $request->params('id');//文章id
		$comment_content = $request->params('content');//评论内容
		$comment_ID = $request->params('comment_id');//被评论的id
		$user_id = $request->params('user_id');//用户id
		$comment_date = time();
		$comment_approved = 1;

		try{
			//请求参数合理性判断
			 if ( !$comment_post_ID or !$comment_content or !$user_id ) {	       
			 //文章ID，评论内容，用户id，都不能为空
	            $data['code'] = 101;
	            $data['data'] = '参数错误';
	            setapilog('article_comment: params[comment_post_ID:' . $comment_post_ID . ', comment_content:' . $comment_content . ', user_id:' . $user_id . '], code:101, error:参数错误');
	            echo json_encode($data);
	            exit ;
	        } elseif ( !is_numeric($comment_post_ID) or !is_numeric($user_id)) {
	            $data['code'] = 102;
	            $data['data'] = '参数错误';
	            setapilog('article_comment: params[id:' . $comment_post_ID . ', user_id:' . $user_id . '], code:102, error:参数错误');
	            echo json_encode($data);
	            exit ;
	        }
	        $db = getConnection();//连接数据库
	        //判断用户是否存在
			$sql = "SELECT `l_user_id` FROM `cs_user` WHERE `i_status` = 0 AND `l_user_id` = $user_id";
			$stmt = $db->query($sql);
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!$res) {
				$data['code'] = 102;
				$data['data'] = "该用户不存在，请先注册会员";
				echo json_encode($data);
				exit;
			}
			
			//判断文章是否存在
			$sql = "SELECT `ID` FROM `cs_posts` WHERE `ID` = '{$comment_post_ID}'";
			$stmt = $db->query($sql);
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!$res) {
				$data['code'] = 102;
				$data['data'] = "该文章不存在";
				echo json_encode($data);
				exit;
			}
	        if ($comment_ID) {
				if (!is_numeric($comment_ID)) {//对评论ID合理性检测
						$data['code'] = 102;
			            $data['data'] = '参数错误';
			            setapilog('article_comment: params[comment_ID:' . $comment_ID . '], code:102, error:参数错误');
			            echo json_encode($data);
			            exit ;
					}
					//判断文章id和被评论id是否关联并存在于comments表中
					$sql = "SELECT `comment_post_ID` FROM `cs_comments` WHERE `comment_post_ID` = '{$comment_post_ID}' AND `comment_ID` = '{$comment_ID}'";
					$stmt = $db->query($sql);
					$res = $stmt->fetch(PDO::FETCH_ASSOC);
					if (!$res) {
						$data['code'] = 102;
						$data['data'] = "该评论不存在";
						echo json_encode($data);
						exit;
					}
						$comment_parent = $comment_ID;//将被评论的id赋给comment_parent
					} else {
						$comment_parent = 0;
			}	
			
			$sql = '';
			$sql .= "INSERT INTO `cs_comments` (`comment_post_ID`, `comment_content`, `comment_parent`, `user_id`, `comment_date`, `comment_approved` ";

			$sql .=" ) ";
			$sql .=" VALUES('".$comment_post_ID."', '".$comment_content."', '".$comment_parent."', '".$user_id."', '".date('Y-m-d H:i:s', $comment_date)."', '".$comment_approved."') ";
			$res = $db->query($sql);
			$id = $db->lastInsertId();//当前评论id
			$sql = "SELECT u.`s_username`, i.`photo_id` FROM `cs_user` as u LEFT JOIN `cs_users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`l_user_id` = '{$user_id}'";
			$stmt = $db->query($sql);
			$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
			$user_name = $user_info['s_username'];
			$user_photo_id = $user_info['photo_id'];
			if ($res) {
				$data['code'] = 200;
				$data['data'] = array(
								'评论成功', 
								array(
									'comment_id'=>$id,//当前评论id
									'id'=>$comment_post_ID,//文章id
									 'content'=>$comment_content, //评论内容
									 'parent_id' => $comment_parent,//评论父id
									 'user_id' => $user_id, //用户id
									 'user_name' => $user_name, //用户名称
									 'user_photo_id' => $user_photo_id, //用户头像id
									 'date' => strtotime($comment_date)//评论时间
									 )
								);
			} else {
				$data['code'] = 103;
				$data['data'] = array(
								'评论失败', 
								array(
									'comment_id'=>'',//当前评论id
									'id'=>'',//文章id
									 'content'=>'', //评论内容
									 'parent_id' => '',//评论父id
									 'user_id' => '', //用户id
									 'user_name' => '', //用户名称
									 'user_photo_id' => '', //用户头像id
									 'date' => ''//评论时间
									 )
								);
			}
			//关闭数据库连接
            $db = null;
            echo json_encode($data);
		} catch (PDOException $e) {
            setapilog('article_comment: params[comment_post_ID: ' . $comment_post_ID . ', comment_content: ' . $comment_content . ', user_id: ' . $user_id . '],error:' . $e->getMessage());
            $data = array('code' => 1, 'data' => '网络错误', 'msg'=>$e->getMessage());
            echo json_encode($data);
        }

	}
	
?>
