<?php  
	/**
	 * 评论点赞
	 * @param integer $id 评论id
	 * @param integer $user_id 用户id
	 * @param integer $type (1 点赞 | 2取消点赞)
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author gaodacheng
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->any('/','comment_like');
	$app->run();

    // like or dislike a comment
    function comment_like() {
        global $app, $crypt;

        $request = $app->request();

        //
        if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[comment_like] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            echo json_encode($data);
            exit();
        }

        //configure like table name
        $like_tbl_name = 'cs_like_dislike_comments';
        //configure type list: 1 => like, 2 => dislike
        $type_list = array('1', '2');
        //configure user table
        $user_tbl_name = 'cs_user';
        //configure comment table
        $comment_tbl_name = 'cs_comments';

        //获取参数: id, user_id, type
        $id = $request->params('id');
        $user_id = $request->params('user_id');
        $type = $request->params('type');
            


        try {
            // 返回的结果数组
            $_data = array();

            //测试参数，完成接口后可去掉
            //$_data['params'] = $request->params();
            /*
            $data['method'] = $request->getMethod();
            //对请求方法(GET, POST)进行处理
            if ( !$request->isPost() ) {
                $_data['code'] = 104;
                $_data['data'] = '参数错误';
                setapilog('comment_like: error request method, post is needed.');
                echo json_encode($_data);
                exit;
            }
            */


            //参数安全检测[begin]
            if ( !$id or !$user_id or !$type ) {
                $_data['code'] = 101;
                $_data['data'] = '参数错误';
                setapilog('[comment_like] [:error] [client ' . $request->getIp() . '] [id,user_id,type % ' . $id . ',' .  $user_id . ',' . $type . '] [101 参数不能为空]');
                echo json_encode($_data);
                exit ;
            } elseif ( !is_numeric($id) or !is_numeric($user_id) or !in_array($type, $type_list) ) {
                $_data['code'] = 102;
                $_data['data'] = '参数错误';
                setapilog('[comment_like] [:error] [client ' . $request->getIp() . '] [id,user_id,type % ' . $id . ',' . $user_id . ',' . $type . '] [102 参数类型不正确]');
                echo json_encode($_data);
                exit ;
            }
            //参数安全检测[end]

            //create a connection to mysql
            $db = getConnection();

            //检测用户是否存在[begin]
            $sql = '';
            $sql .= "SELECT `l_user_id` FROM `$user_tbl_name` WHERE `l_user_id` = $user_id";
            //调试过程中可以去除注释查看sql
            //$_data['sql'] = $sql;
            $stmt = $db->query($sql);
            if ( $stmt ) {
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            if ( !$res ) {
                $_data['code'] = 103;
                $_data['data'] = '参数错误';
                setapilog('[comment_like] [:error] [client ' . $request->getIp() . '] [id,user_id,type %' . $id . ',' . $user_id . ',' . $type . '] [103 用户不存在]');
                echo json_encode($_data);
                exit ;
            }
            //检测用户是否存在[end]

            //检测将要点赞的评论是否存在[begin]
            $sql = "SELECT `comment_ID` FROM {$comment_tbl_name} WHERE `comment_ID` = '{$id}'";
            //调试过程中可以去除注释查看sql
            //$_data['sql'] = $sql;
            $stmt = $db->query($sql);
            if ( $stmt ) {
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if ( !$res ) {
                $_data['code'] = 104;
                $_data['data'] = '参数错误';
                setapilog('[comment_list] [:error] [client ' . $request->getIp() . '] [id,user_id,type % ' . $id . ',' . $user_id . ',' . $type . '] [104 评论不存在]');
                echo json_encode($_data);
                exit();
            }
            //检测将要点赞的评论是否存在[end]

            //检查同一条评论有没有被同一用户评论过 [begin]
            //组合sql查询语句
            $sql = "";
            $sql .= " SELECT * FROM `$like_tbl_name` ";
            $sql .= " WHERE `comment_id` = " . $id;
            $sql .= " AND `user_id` = " . $user_id;
            //调试时可以打开下面的选项
            //$_data['sql'] = $sql;
            $stmt = $db->query($sql);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( $res ) {
                $_data['code'] = 105;
                $_data['data'] = '你已经赞过此条评论啦.';
                echo json_encode($_data);
                exit ;
            }
            //检查同一条评论有没有被此用户评论过 [end]

            //可以点一个赞[begin]
            //记录访问接口时的IP
            $ip = ip2long($request->getIp());
            //$_data['ip'] = $request->getIp();
            //组合插入的sql
            $sql = '';
            $sql .= " INSERT INTO $like_tbl_name (";
            $sql .= " `comment_id`, `user_id` ";
            if ( $type == 1) {
                $sql .= " ,`rate_like_value`, `rate_like_ip` ) VALUES ( ";
            } elseif( $type == 2 ) {
                $sql .= " ,`rate_dislike_value`, `rate_dislike_ip` ) VALUES ( ";
            }
            $sql .= " $id, $user_id, 1, $ip )";
            //调试时可以打开下面的选项
            //$_data['sql'] = $sql;
            $stmt = $db->query($sql);
            if ( $stmt ) {
                $_data['code'] = 200;
                $_data['data'] = '点赞成功';
            } else {
                $_data['code'] = 400;
                $_data['data'] = '点赞失败';
                setapilog('[comment_like] [:error] [client ' . $request->getIp() . '] [id,user_id,type % ' . $id . ',' . $user_id . ',' . $type . '] [400 点赞失败]');
            }
            //可以点一个赞[end]

            //关闭数据库连接
            $db = null;
            echo json_encode($_data);
        } catch (PDOException $e) {
            setapilog('[comment_like] [:error] [client ' . $request->getIp() . '] [id,user_id,type % ' . $id . ',' . $user_id . ',' . $type . '] [1 ' . $e->getMessage() . ']');
            //$data = array('code' => 1, 'data' => '网络错误', 'msg' => $e->getMessage());
            $data = array('code' => 1, 'data' => '网络错误');
            echo json_encode($data);
        }
    }
