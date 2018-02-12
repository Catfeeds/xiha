<?php
    /**
    * 获取教练端学员订单列表
    * @param int $coach_id 教练id
    * @param int $status 订单状态
    * @param int $page 分页码
    * @author cx
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->response->headers->set('content-type', 'application/json; charset=utf-8');
    $app->any('/', 'getCoachStudentOrders');
    $app->run();

    function getCoachStudentOrders() {
        global $app, $crypt;

        // Configuration
        $allowed_status = array(
            1, // 待完成已付款
            2, // 已完成
            3, // 已取消
            1003, // 待完成未付款
        );

        $pay_type_title = array(
            '0' => '未知',
            '1' => '支付宝支付',
            '2' => '线下支付',
            '3' => '微信支付',
            '4' => '银行卡支付',
        );

        $order_status_title = array(
            '1' => '已付款',
            '2' => '已完成',
            '3' => '已取消',
            '101' => '已删除',
            '1001' => '正在付款中',
            '1003' => '未付款',
            '1004' => '取消受理中',
            '1006' => '退款受理中',
        );

        $unpaid_status = array(
            '1003', //未付款
            '1001', //正在付款中,主要是针对等待回调通知有延迟
        );

        $paid_status = array(
            '1', //已付款
        );

        $completed_status = array(
            '2', //已完成
        );

        $canceled_status = array(
            '3', //已取消
            '1004', //取消订单处理中
            '1006', //退款处理中
        );

        $pay_time_limit = 180; // in second

        //验证请求方式 POST
        $r = $app->request();
        if ( !$r->isPost() ) {
            setapilog('[get_coach_student_orders] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            $data = array('code' => 106, 'data' => '请求错误');
            exit( json_encode($data) );
        }

        //取得参数列表
        $validate_ok = validate(array(
            'coach_id'   => 'INT',
            'status'    => 'INT',
            'page'      => 'INT',
            ), 
            $r->params()
        );

        if ( !$validate_ok['pass'] ) {
            exit( json_encode($validate_ok['data']) );
        }

        $p = $r->params();
        $coach_id = $p['coach_id'];
        $status = $p['status'];
        $page = $p['page'];

        //分配恰当的分类
        $status_buf = array();
        switch ($status) {
            case '1': 
                $status_buf = $paid_status;
                break;
            case '2': 
                $status_buf = $completed_status;
                break;
            case '3': 
                $status_buf = $canceled_status;
                break;
            case '1003': 
                $status_buf = $unpaid_status;
                break;
            default : 
                break;
        }

        //不允许的状态
	    if(empty($status_buf)) {
	    	$data = array(
                'code' => 102, 
                'data' => '参数错误'
            );
            exit(json_encode($data));
	    }

        if ( !in_array($status, $allowed_status) ) {
            exit(json_encode(array('code'=>102, 'data'=>'参数错误')));
        }

        if ( $page <= 0 ) {
            $page = 1;
        }

        $limit = 10;
        $start = $limit * ($page-1);

        try {
            //建立数据库连接
            $db = getConnection();

            // get original order info from order table
            $fields_buf = array(
                'l_user_id',
                's_user_name',
                's_user_phone',
                'l_study_order_id',
                's_order_no',
                'dt_appoint_time',
                'appoint_time_id',
                'dt_order_time',
                'dt_zhifu_time',
                'l_coach_id',
                's_coach_name',
                's_coach_phone',
                's_lisence_name',
                's_lesson_name',
                'dc_money',
                'deal_type',
                'i_status',
                'time_config_id',
                'cancel_type',
                'cancel_reason',
                'l_study_order_id',
            );
            $study_orders = DBPREFIX.'study_orders';
            $sql = " SELECT `".implode('`,`', $fields_buf)."` FROM `{$study_orders}` WHERE `l_coach_id` = '{$coach_id}' AND `i_status` IN ('".implode("','", $status_buf)."') ORDER BY `dt_order_time` DESC LIMIT $start, $limit ";
            $stmt = $db->query($sql);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ( empty($orders) ) {
                $data = array(
                    'code' => 200, 
                    'data' => array()
                );
                exit( json_encode($data) );
            }

            $info = array();
            $users_info = DBPREFIX.'users_info';
            foreach ( $orders as $key => $val ) {
                $appoint_period_buf = array();
                if ( array_key_exists('time_config_id', $val) && !empty($val['time_config_id']) ) {
                    $appoint_period_buf = getAppointPeriodList( $db, explode(',', $val['time_config_id']) );
                }
                $coach_comment_buf = getCoachCommentByOrder($db, $val['s_order_no']);
                $student_comment_buf = getStudentCommentByOrder($db, $val['s_order_no']);
                $dt_appoint_time = explode(' ', $val['dt_appoint_time']);

                // 获取学员头像
                $sql = "SELECT `photo_id` FROM `{$users_info}` WHERE `user_id` = :uid";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('uid', $val['l_user_id']);
                $stmt->execute();
                $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
                $photo_id = 1;
                if(!empty($user_info)) {
                    $photo_id = $user_info['photo_id'];
                }

                $order_info_buf = array(
                    'user_id' => $val['l_user_id'],
                    'user_name' => $val['s_user_name'] == '' ? '未知' : $val['s_user_name'],
                    'user_phone' => $val['s_user_phone'],
                    'photo_id'  => $photo_id,
                    'order_no' => $val['s_order_no'],
                    'order_id' => $val['l_study_order_id'],
                    'order_status' => $val['i_status'],
                    'order_status_title' => isset($order_status_title[$val['i_status']]) ? $order_status_title[$val['i_status']] : '未知状态',
                    'appoint_time' => $dt_appoint_time[0],
                    'order_time' => $val['dt_order_time'],
                    'coach_id' => $val['l_coach_id'],
                    'money' => $val['dc_money'],
                    'pay_type' => $val['deal_type'],
                    'pay_type_title' => array_key_exists($val['deal_type'], $pay_type_title) ? $pay_type_title[$val['deal_type']] : '未知',
                    'licence' => $val['s_lisence_name'],
                    'lesson' => $val['s_lesson_name'],
                    'appoint_period' => $appoint_period_buf,
                    'pay_time_limit' => $pay_time_limit,
                    'coach_comment' => empty($coach_comment_buf) ? '' : $coach_comment_buf,
                    'is_student_commented' => $student_comment_buf['is_commented'],
                    'student_comment' => $student_comment_buf['student_comment'],
                    'cancel_type' => $val['cancel_type'],
                    'cancel_reason' => $val['cancel_reason'],
                );
                $info[] = $order_info_buf;
            }
            //关闭数据库
            $db = null;
            $data = array('code'=>200, 'data'=>$info);
            exit( json_encode($data) );
    
        } catch ( PDOException $e ) {
            setapilog('[get_coach_student_orders] [:error] [client ' . $r->getIP() . '] [params ' . serialize($r->params()) . '] [' . $e->getLine() . ' ' . $e->getMessage().']');
            exit(json_encode(array('code' => 1, 'data' => '网络错误')));
        }
    }

    function getSchoolNameById($db = null, $id = 0) {
        if ( !is_object($db) || !($db instanceof PDO) || empty($id) ) {
            return false;
        }

        $fields_buf = array(
            's_school_name',
        );
        $sql = " SELECT `".implode('`,`', $fields_buf)."` FROM `".DBPREFIX."school` WHERE `l_school_id` = '{$id}' ";
        $stmt = $db->query($sql);
        $school_info_buf = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( empty($school_info_buf) ) {
            return false;
        }

        if ( array_key_exists('s_school_name', $school_info_buf) ) {
            return $school_info_buf['s_school_name'];
        }

        return false;

    } /* getSchoolNameById End */

    function getCarNoById( $db = null, $id = 0) {
        if ( !is_object($db) || !($db instanceof PDO) || empty($id) ) {
            return false;
        }

        $fields_buf = array(
            'car_no',
        );
        $sql = " SELECT `".implode('`,`', $fields_buf)."` FROM `".DBPREFIX."cars` WHERE `id` = '{$id}' ";
        $stmt = $db->query($sql);
        $car_info_buf = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( empty($car_info_buf) ) {
            return false;
        }

        if ( array_key_exists('car_no', $car_info_buf) ) {
            return $car_info_buf['car_no'];
        }

        return false;
    } /* getCarNoById End */

    function getAppointPeriodList($db = null, $id = array()) {
        if ( !is_object($db) || !($db instanceof PDO) || empty($id) ) {
            return false;
        }

        $fields_buf = array(
            'id',
            'start_time',
            'end_time',
            'start_minute',
            'end_minute',
        );
        $sql = " SELECT `".implode('`,`', $fields_buf)."` FROM `".DBPREFIX."coach_time_config` WHERE `id` IN ('".implode("','", $id)."') ";
        $stmt = $db->query($sql);
        $appoint_period_info_buf = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $appoint_period = array();
        foreach ( $appoint_period_info_buf as $key => $val ) {
            $start = $val['start_time'] . ':' . ($val['start_minute'] <= 9 ? '0' . $val['start_minute'] : $val['start_minute']);
            $end = $val['end_time'] . ':' . ($val['end_minute'] <= 9 ? '0' . $val['end_minute'] : $val['end_minute']);
            $appoint_period[] = $start . '-' . $end;
        }
        return $appoint_period;
    } /* getAppointPeriodList End */

    function getCoachCommentByOrder( $db = null, $id = 0) {
        if ( !is_object($db) || !($db instanceof PDO) || empty($id) ) {
            return false;
        }
        
        $fields_buf = array(
            'id',
            'content',
        );
        $sql = " SELECT `".implode('`,`', $fields_buf)."` FROM `".DBPREFIX."student_comment` WHERE `order_no` = '{$id}' ";
        $stmt = $db->query($sql);
        $coach_comment_buf = $stmt->fetch(PDO::FETCH_ASSOC);

        if ( !empty( $coach_comment_buf ) ) {
            return $coach_comment_buf['content'];
        }

        return false;
    } /* getCoachCommentByOrder */

    function getStudentCommentByOrder( $db = null, $id = 0) {
        if ( !is_object($db) || !($db instanceof PDO) || empty($id) ) {
            return false;
        }
        
        //$id = '2015110554555554';

        $fields_buf = array(
            'id',
            'coach_content',
        );
        $sql = " SELECT `".implode('`,`', $fields_buf)."` FROM `".DBPREFIX."coach_comment` WHERE `order_no` = '{$id}' ";
        $stmt = $db->query($sql);
        $student_comment_info_buf = $stmt->fetch(PDO::FETCH_ASSOC);

        $student_comment_info = array();
        if ( !empty( $student_comment_info_buf ) ) {
            $student_comment_info['is_commented'] = 1;
            $student_comment_info['student_comment'] = $student_comment_info_buf['coach_content'];
        } else {
            $student_comment_info['is_commented'] = 0;
            $student_comment_info['student_comment'] = '';
        }

        return $student_comment_info;
    } /* getStudentCommentByOrder End */
?>
