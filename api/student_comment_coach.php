<?php  
    /**
     * 学员评价教练 驾校
     * @param $coach_id int 教练ID
     * @param $coach_star int 教练星级
     * @param $school_star int 学校星级
     * @param $coach_content string 教练主观评价
     * @param $school_content string 驾校主观评价
     * @param $user_id int 用户ID
     * @param $order_no int 订单号
     * @return string AES对称加密（加密字段xhxueche）
     * @author chenxi
     **/

    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->post('/','comment');
    $app->run();

    // 学员评价教练
    function comment() {
        Global $app, $crypt;
        $request = $app->request();
        $coach_id = $request->params('coach_id');
        $coach_star = $request->params('coach_star');
        $school_star = $request->params('school_star');
        $coach_content = $request->params('coach_content');
        $school_content = $request->params('school_content');
        $user_id = $request->params('user_id');
        $order_no = $request->params('order_no');

        try {
            $db = getConnection();
            $sql = "SELECT * FROM `cs_coach_comment` WHERE `order_no` = '".$order_no."'";
            $stmt = $db->query($sql);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row) {
                $data = array('code'=>-1, 'data'=>'已评价');
                exit(json_encode($data));
            }

            $sql = "INSERT INTO `cs_coach_comment` (`coach_id`, `coach_star`, `school_star`, `coach_content`, `school_content`, `user_id`, `order_no`, `school_id`, `type`, `addtime`)";
            $sql .= " VALUES (:coach_id, :coach_star, :school_star, :coach_content, :school_content, :user_id, :order_no, 0, 1, '".time()."')";

            $stmt = $db->prepare($sql);
            $stmt->bindParam('coach_id', $coach_id);
            $stmt->bindParam('coach_star', $coach_star);
            $stmt->bindParam('school_star', $school_star);
            $stmt->bindParam('coach_content', $coach_content);
            $stmt->bindParam('school_content', $school_content);
            $stmt->bindParam('user_id', $user_id);
            $stmt->bindParam('order_no', $order_no);

            $stmt->execute();
            $id = $db->lastInsertId();
            if($id) {

                // 获取学员信息
                $sql = " SELECT `s_real_name` AS realname, `s_username` AS username, `s_phone` AS userphone FROM `cs_user` WHERE `l_user_id` = '{$user_id}' ";
                $stmt = $db->query($sql);
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                $username = '嘻哈学员';
                $userphone = '0551-65610256';
                if ($res) {
                    if ($res['userphone']) {
                        $userphone = $res['userphone'];
                    }
                    if ($res['realname']) {
                        $username = $res['realname'];
                    } elseif ($res['username']) {
                        $username = $res['username'];
                    }
                }

                $jiguang_content = "教练您好，订单有新的学员评价了，评价内容请到订单管理中查看。此学员姓名为：". $username . "，电话为：". $userphone . "。";

                // 通过教练id获取教练姓名
                $sql = "SELECT * FROM `cs_coach` WHERE `l_coach_id` = $coach_id";
                $stmt = $db->query($sql);
                $coachinfo = $stmt->fetch(PDO::FETCH_ASSOC);

                // 教练端接收
                if($coachinfo) {
                    $params = array(
                        'user_phone'=>$coachinfo['s_coach_phone'],
                        'member_id'=>$coach_id,
                        'member_type'=>2, // 1：学员 2：教练
                        's_beizhu'=>'学员订单',
                        'i_yw_type'=>2, // 1:通知 2：正常订单消息
                        'title'=>'预约学车订单',
                        //'content'=>'您有新的评价信息，请点击查看。',
                        'content' => $jiguang_content, //发送给教练端的提示消息
                        'type'=>2 //(1:学员端 2：教练端)
                    );
                    $res = request_post(SHOST.'api/message_push.php', $params);
                }

                $data = array('code'=>200, 'data'=>'评价成功');
            } else {
                $data = array('code'=>2, 'data'=>'评价失败');
            }
            echo json_encode($data);
        } catch (PDOException $e) {
            // $data = array('code'=>1, 'data'=>$e->getMessage());
            setapilog('student_comment_coach:params[coach_id:'.$coach_id.',coach_star:'.$coach_star.',school_star:'.$school_star.',coach_content:'.$coach_content.',school_content:'.$school_content.',user_id:'.$user_id.',order_no:'.$order_no.'], error:' . $e->getLine() . ' ' .$e->getMessage());    
            $data = array('code'=>1, 'data'=>'网络错误');
            echo json_encode($data);    
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
