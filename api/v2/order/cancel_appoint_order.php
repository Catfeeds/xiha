<?php 

/**
 * 申请取消我预约学车的订单
 * @param $order_no 订单号 
 * @param $order_id 订单ID
 * @param $user_id 学员ID
 * @param $cancel_reason 内容
 * @param $cancel_type 1：学员取消 2：驾校取消
 * @return string AES对称加密（加密字段xhxueche）
 * @author sunweiwei
 **/
require '../../Slim/Slim.php';
require '../../include/common.php';
require '../../include/functions.php';
require '../../include/crypt.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
$app->any('/','cancelAppointOrder');
$app->response->headers->set('Content-Type', 'application/json;charset=utf8');
$app->run();

function cancelAppointOrder() {
    Global $app, $crypt;
    $r = $app->request();
    //验证请求方式 POST
    if ( !$r->isPost() ) {
        setapilog('[cancel_appoint_orders] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
        echo json_encode(array('code' => 106, 'data' => '请求错误')) ;
        exit();
    }
    //验证输入参数    
    $validate_ok = validate(
        array(
            'order_no'   => 'STRING',
            'order_id'   => 'INT',
            'user_id'    => 'INT',
            'content' => 'STRING',
            'type' => 'INT',
        ),
        $r->params()
    );

    if ( !$validate_ok['pass'] ) {
        echo json_encode($validate_ok['data']) ;
        exit();
    }
    //获取参数
    $order_no = $r->params('order_no');
    $order_id = $r->params('order_id');
    $user_id = $r->params('user_id');
    $content = $r->params('content');
    $type = $r->params('type');

    try {
        $db = getConnection();
        // 查找订单是否存在
        $sql = "SELECT `l_study_order_id` FROM `".DBPREFIX."study_orders` WHERE `s_order_no` = '{$order_no}' AND `l_user_id` = '{$user_id}' AND `l_study_order_id` = '{$order_id}' ";
        $sql .= " AND `i_status` != 2";
        $stmt = $db->query($sql);
        $orderinfo = $stmt->fetch(PDO::FETCH_ASSOC);
        if(empty($orderinfo)) {
            $data = array('code'=>-4, 'data'=>'不存在要取消的订单');
            echo json_encode($data);
            exit();
        }
        // 判断当前订单时间需在两个小时之前取消
        $time = time();
        $year = date('Y', $time);
        $month = date('m', $time);
        $day = date('d', $time);
        $hour = date('H', $time);

        $sql = "SELECT s.`dt_appoint_time`, s.`time_config_id`, s.`l_coach_id`, s.`i_status` FROM `".DBPREFIX."study_orders` as s LEFT JOIN `".DBPREFIX."coach_appoint_time` as a ON a.`id` = s.`appoint_time_id` ";
        $sql .= " WHERE s.`s_order_no` = '{$order_no}' AND s.`l_study_order_id` = '{$order_id}'";
        $stmt = $db->query($sql);
        $study_order_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if($study_order_info) {
            $time_config_id = array_filter(explode(',', $study_order_info['time_config_id']));
            $sql = "SELECT `start_time`, `start_minute` FROM `".DBPREFIX."coach_time_config` WHERE `id` IN (".implode(',', $time_config_id).")";
            $stmt = $db->query($sql);
            $time_config_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $starttime_arr = array();
            if($time_config_info) {
                foreach ($time_config_info as $key => $value) {
                    $starttime_arr[] = $value['start_time'] * 60 + $value['start_minute'];
                }
            }

            // 获取系统设置的取消时间设定
            $sql = "SELECT `s_school_name_id` FROM `".DBPREFIX."coach` WHERE `l_coach_id` = '{$study_order_info['l_coach_id']}'";
            $stmt = $db->query($sql);
            $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
            $s_school_name_id = 0;
            if($coach_info) {
                $s_school_name_id = $coach_info['s_school_name_id'];
            }

            $sql = "SELECT * FROM `".DBPREFIX."school_config` WHERE `l_school_id` = '{$s_school_name_id}'";
            $stmt = $db->query($sql);
            $school_config_info = $stmt->fetch(PDO::FETCH_ASSOC);
            $i_cancel_order_time = 2;
            if($school_config_info) {
                $i_cancel_order_time = $school_config_info['i_cancel_order_time'];
            }
            $appoint_time = strtotime($study_order_info['dt_appoint_time']) + min($starttime_arr) * 60;

            if($appoint_time > $time) {
                $ctime = time();
                if ($study_order_info['i_status'] == '3') {
                    $data = array('code' => 200, 'data' => '已经取消此订单');
                    echo json_encode($data);
                    exit();
                }
                else if ($study_order_info['i_status'] == '1003') {
                    $sql = "UPDATE `cs_study_orders` SET `i_status` = 3, `cancel_reason` = :content, `cancel_time` = :ctime, `cancel_type` = :type WHERE `s_order_no` = :order_no AND `l_study_order_id` = :order_id";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('type', $type);
                    $stmt->bindParam('content', $content);
                    $stmt->bindParam('ctime', $ctime);
                    $stmt->bindParam('order_no', $order_no);
                    $stmt->bindParam('order_id', $order_id);
                    $res = $stmt->execute();
                    $data = array('code'=>200, 'data'=>'超时未付款订单已取消');
                    echo json_encode($data);
                    exit();
                } else {
                    if($appoint_time - $time < $i_cancel_order_time * 3600) {
                        $data = array('code'=>-5, 'data'=>'请提前'.$i_cancel_order_time.'个小时取消');
                        echo json_encode($data);
                        exit();
                    }
                }
            } else if($appoint_time < $time) {
                $ctime = time();

                if ($study_order_info['i_status'] == '3') {
                    $data = array('code' => 200, 'data' => '已经取消此订单');
                    echo json_encode($data);
                    exit();
                }
                else if ($study_order_info['i_status'] == '1003') {
                    $sql = "UPDATE `cs_study_orders` SET `i_status` = 3, `cancel_reason` = :content, `cancel_time` = :ctime, `cancel_type` = :type WHERE `s_order_no` = :order_no AND `l_study_order_id` = :order_id";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('type', $type);
                    $stmt->bindParam('content', $content);
                    $stmt->bindParam('ctime', $ctime);
                    $stmt->bindParam('order_no', $order_no);
                    $stmt->bindParam('order_id', $order_id);
                    $res = $stmt->execute();
                    $data = array('code'=>200, 'data'=>'过期未付款订单已取消');
                    echo json_encode($data);
                    exit();
                } else {
                    $data = array('code'=>-6, 'data'=>'预约时间已过期不能取消');
                    echo json_encode($data);
                    exit();
                }

            }
        }

        // 判断当前订单是否被取消
        $sql = "SELECT `i_status`, `cancel_type` FROM `".DBPREFIX."study_orders` WHERE `i_status` = 3 AND `s_order_no` = ".$order_no." AND `l_study_order_id` = ".$order_id;

        try {
            $stmt = $db->query($sql);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if($row) {
                if($row['cancel_type'] == 1) {
                    $data = array('code'=>-2, 'data'=>'订单已被学员取消');

                } elseif($row['cancel_type'] == 2) {
                    $data = array('code'=>-3, 'data'=>'订单已被教练取消');
                }
                echo json_encode($data);
                exit();
            }

        } catch(PDOException $e) {
            setapilog('[cancel_appoint_order] [:error] [client ' . $r->getIP() . '] [params'. serialize($r->params()) .'] [1 网络异常] [' . $e->getLine() . ' ' . $e->getmessage() . ']');
            $data = array('code'=>1, 'data'=>'网络错误');
            echo json_encode($data);
            exit();
        }


        try {
            // 更改订单状态
            $time = time();
            $sql = "UPDATE `cs_study_orders` SET `i_status` = 3, `cancel_reason` = :content, `cancel_time` = :ctime, `cancel_type` = :type WHERE `s_order_no` = :order_no AND `l_study_order_id` = :order_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('type', $type);
            $stmt->bindParam('content', $content);
            $stmt->bindParam('ctime', $time);
            $stmt->bindParam('order_no', $order_no);
            $stmt->bindParam('order_id', $order_id);
            $res = $stmt->execute();
            if($res) {
                $push_info = array(
                    'student_name' => '',
                    'student_phone' => '',
                    'coach_name' => '',
                    'coach_phone' => '',
                    'cancel_type' => $type,
                    'cancel_reason' => $content,
                );
                //查找学员信息
                $fields_buf = array(
                    's_phone',
                    's_real_name',
                );
                $table_buf = DBPREFIX . "user";
                $sql = " SELECT `".implode('`,`', $fields_buf)."` FROM `{$table_buf}` WHERE `l_user_id` = '{$user_id}' ";
                $stmt = $db->query($sql);
                $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!empty($user_info) && is_array($user_info)) {
                    $push_info['student_name'] = (isset($user_info['s_real_name']) && !empty($user_info['s_real_name'])) ? $user_info['s_real_name'] : '嘻哈学员无名氏';
                    $push_info['student_phone'] = (isset($user_info['s_phone']) && !empty($user_info['s_phone'])) ? $user_info['s_phone'] : '手机号为空';
                }
                //查找教练信息
                $fields_buf = array(
                    's_coach_name',
                    's_coach_phone',
                );
                $table_buf = DBPREFIX . "coach";
                $sql = " SELECT `".implode('`,`', $fields_buf)."` FROM `{$table_buf}` WHERE `l_coach_id` = '{$study_order_info['l_coach_id']}' ";
                $stmt = $db->query($sql);
                $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!empty($coach_info) && is_array($coach_info)) {
                    $push_info['coach_name'] = (isset($coach_info['s_coach_name']) && !empty($coach_info['s_coach_name'])) ? $coach_info['s_coach_name'] : '嘻哈教练无名氏';
                    $push_info['coach_phone'] = (isset($coach_info['s_coach_phone']) && !empty($coach_info['s_coach_phone'])) ? $coach_info['s_coach_phone'] : '手机号空';
                }

                $data = array('code'=>200, 'data'=>'取消成功');

                $_appoint_time_m = date('m', $appoint_time);
                $_appoint_time_d = date('d', $appoint_time);
                $_appoint_time_h = date('H', $appoint_time);

                // 学员端取消
                if($type == 1) {

                    // 推送消息
                    $params_coach = array(
                        'user_phone'=>$push_info['coach_phone'],
                        'member_id'=>$study_order_info['l_coach_id'],
                        'member_type'=>2, // 1：学员 2：教练
                        's_beizhu'=>'学员订单取消',
                        'i_yw_type'=>2, // 1:通知 2：正常订单消息
                        'title'=>'预约学车订单',
                        'content'=>'教练您好，您的'.$_appoint_time_m.'月'.$_appoint_time_d.'日'.$_appoint_time_h.':00时开始的订单(订单号：'.$order_no.')已被学员取消，原因是: '.$push_info['cancel_reason'].'，请您及时调整您的教学计划。该学员的姓名为 ' . $push_info['student_name'] . ',手机号为 ' . $push_info['student_phone'] . '。',
                        'type'=>2 // 2:教练端推送 1：学员端推送
                    );
                    $res = request_post(SHOST.'api/message_push.php', $params_coach);

                    // 推送消息
                    $params_student = array(
                        'user_phone'=>$push_info['student_phone'],
                        'member_id'=>$user_id,
                        'member_type'=>1,// 1：学员 2：教练
                        's_beizhu'=>'学员订单取消',
                        'i_yw_type'=>2, // 1:通知 2：正常订单消息
                        'title'=>'预约学车订单取消',
                        'content'=>'学员您好！您的'.$_appoint_time_m.'月'.$_appoint_time_d.'日'.$_appoint_time_h.':00时学车服务已取消成功(订单号：'.$order_no.')，请和教练电话确认。该教练姓名为 ' . $push_info['coach_name'] . ', 手机号为 ' . $push_info['coach_phone'] . '。',
                        'type'=>1 // 2:教练端推送 1：学员端推送
                    );

                    $res = request_post(SHOST.'api/message_push.php', $params_student);

                    // 教练端取消
                } else {
                    // 推送消息
                    $params_coach = array(
                        'user_phone'=>$push_info['coach_phone'],
                        'member_id'=>$study_order_info['l_coach_id'],
                        'member_type'=>2, // 1：学员 2：教练
                        's_beizhu'=>'学员订单取消',
                        'i_yw_type'=>2, // 1:通知 2：正常订单消息
                        'title'=>'预约学车订单',
                        'content'=>'教练您好，您的'.$_appoint_time_m.'月'.$_appoint_time_d.'日'.$_appoint_time_h.':00时开始的订单已取消成功(订单号：'.$order_no.')，请及时电话告知学员。该学员姓名为 '.$push_info['student_name'] . ', 手机号为 '.$push_info['student_phone'].'。',
                        'type'=>2 // 2:教练端推送 1：学员端推送
                    );
                    $res = request_post(SHOST.'api/message_push.php', $params_coach);

                    // 推送消息
                    $params_student = array(
                        'user_phone'=>$push_info['student_phone'],
                        'member_id'=>$user_id,
                        'member_type'=>1,// 1：学员 2：教练
                        's_beizhu'=>'学员订单取消',
                        'i_yw_type'=>2, // 1:通知 2：正常订单消息
                        'title'=>'预约学车订单取消',
                        'content'=>'学员您好！您的'.$_appoint_time_m.'月'.$_appoint_time_d.'日'.$_appoint_time_h.':00时开始的订单(订单号：'.$order_no.')已被教练取消，原因是: '.$push_info['cancel_reason'].'，请您及时调整您的出行计划。该教练姓名为 ' .$push_info['coach_name'].', 手机号为 '.$push_info['coach_phone'].'。',
                        'type'=>1 // 2:教练端推送 1：学员端推送
                    );

                    $res = request_post(SHOST.'api/message_push.php', $params_student);
                }

            } else {
                $data = array('code'=>-1, 'data'=>'取消失败');
            }
            $db = null;
            echo json_encode($data);
            exit();
        } catch(PDOException $e) {
            // $data = array('code'=>1, 'data'=>$e->getMessage());
            setapilog('[cancel_appoint_order] [:error] [client ' . $r->getIP() . '] [params'. serialize($r->params()) .'] [1 网络异常] [' . $e->getLine() . ' ' . $e->getmessage() . ']');
            $data = array('code'=>1, 'data'=>'网络错误');

            echo json_encode($data);
            exit();
        }

        $db = null;
        echo json_encode($data);
        exit();
    }catch (PDOException $e) {
        setapilog('[cancel_appoint_order] [:error] [client ' . $r->getIP() . '] [params'. serialize($r->params()) .'] [1 网络异常] [' . $e->getLine() . ' ' . $e->getmessage() . ']');
        echo json_encode(array('code' => 1, 'data' => '网络错误'));
        exit();
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
