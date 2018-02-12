<?php  
    /**
     * 教练评价学员
     * @param string $no 订单no
     * @param int $star 评价星级
     * @param string $content 主观评价内容
     * @param int $user_id 用户ID
     * @param int $coach_id 教练ID
     * @return string AES对称加密（加密字段xhxueche）
     * @author chenxi
     **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/','comment');
    $app->run();

    // 教练评价学员
    function comment() {
        Global $app, $crypt;
        $request = $app->request();
         if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_index] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            exit(json_encode($data));
        }
        $validate_result = validate(
            array(
                'order_no'     =>'STRING', 
                'order_star'   =>'INT', 
                'content'      =>'STRING', 
                'user_id'      =>'INT', 
                'coach_id'     =>'INT', 
            ), $request->params());

        if (!$validate_result['pass']) {
            exit(json_encode($validate_result['data']));
        }

        $p = $request->params();
        $order_no   = $p['order_no'];
        $star_num   = $p['order_star'];
        $content    = $p['content'];
        $user_id    = $p['user_id'];
        $coach_id   = $p['coach_id'];

        try {
            $db = getConnection();
            $sql = " SELECT 1 FROM `cs_student_comment` WHERE `order_no` = '{$order_no}' ";
            $stmt = $db->query($sql);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $data = array('code' => -1, 'data' => '已评价');
                exit(json_encode($data));
            }

            $sql = "INSERT INTO `cs_student_comment` (`order_no`, `star_num`, `content`, `user_id`, `coach_id`,`addtime`) VALUES (:no, :star, :content, :user_id, :coach_id,'".time()."')";

            $stmt = $db->prepare($sql);
            $stmt->bindParam('no', $order_no);
            $stmt->bindParam('star', $star_num);
            $stmt->bindParam('content', $content);
            $stmt->bindParam('user_id', $user_id);
            $stmt->bindParam('coach_id', $coach_id);
            $res = $stmt->execute();
            $id = $db->lastInsertId();
            if($id) {

                // 获取教练信息
                $sql = " SELECT `s_coach_name` AS username, `s_coach_phone` AS userphone FROM `cs_coach` WHERE `l_coach_id` = {$coach_id} ";
                $stmt = $db->query($sql);
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                $username = '嘻哈教练';
                $userphone = '0551-65610256';
                if ($res) {
                    if ($res['userphone']) {
                        $userphone = $res['userphone'];
                    }
                    if ($res['username']) {
                        $username = $res['username'];
                    }
                }

                $jiguang_content = "学员您好，订单有新的教练评价了，评价内容请到订单管理中查看。此教练姓名为：" . $username . "，电话为：" . $userphone . "。";

                // 通过用户id获取学员
                $sql = " SELECT * FROM `cs_user` WHERE `l_user_id` = '{$user_id}' ";
                $stmt = $db->query($sql);
                $userinfo = $stmt->fetch(PDO::FETCH_ASSOC);

                // 学员端接收
                if($userinfo) {
                    $params = array(
                        'user_phone'=>$userinfo['s_phone'],
                        'member_id'=>$user_id,
                        'member_type'=>1, // 1：学员 2：教练
                        's_beizhu'=>'学员订单',
                        'i_yw_type'=>2, // 1:通知 2：正常订单消息
                        'title'=>'预约学车订单',
                        'content'=> $jiguang_content, //发送给学员端的推送消息
                        'type'=>1 //(1:学员端 2：教练端)
                    );
                    $res = request_post(SHOST.'api/message_push.php', $params);
                }
                    
                $data = array('code'=>200, 'data'=>"评价成功");
            } else {
                $data = array('code'=>2, 'data'=>"评价失败");
            }
            echo json_encode($data);
        } catch(PDOException $e) {
            setapilog('[coach_comment_student] [:error] [client ' . $request->getIp() . '] [params ' . serialize($request->params()) . '] [1 ' . $e->getMessage() . ']');
            $data = array('code'=>1, 'data'=>'网络错误');
            exit( json_encode($data) );
        }
    }

    /**
     * 模拟post进行url请求
     * @param string $url
     * @param array $post_data
     */
    function request_post($url = '', $post_data = array()) {
        if (empty($url) || empty($post_data)) {
            return false;
        }
        $o = "";
        foreach ( $post_data as $k => $v ) 
        { 
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        $postUrl = $url;
        $curlPost = $post_data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        
        return $data;
    }

?>
