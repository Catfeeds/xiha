<?php 
    /**
     * 获取消息列表（主要是学员在微信端等第三方平台上
     *   添加了学车信息时候发送的消息）
     * @param int $coach_id 教练id
     * @param OPTIONAL int $sms_type 消息类型 (1 系统消息 2 订单消息)
     * @param int $member_type 人员类型 (1 学员 2 教练)
     * @return json $rt
     * @author  gaodacheng
     **/
    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/','getMessageList');
    $app->run();

    function getMessageList() {
        Global $app, $crypt;
        $r = $app->request();
         if ( !$r->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_message_list] [:error] [client ' . $r->getIp() . '] [Method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            exit(json_encode($data));
        }
        //  获取请求参数并判断合法性
        $validate_result = validate(
            array(
                'coach_id'      => 'INT',
                'sms_type'      => 'INT',      // 1：系统消息 2：订单消息
                'member_type'   => 'INT',      // 1：学员 2：教练
            ), $r->params()
        );

        if (!$validate_result['pass']) {
            exit(json_encode($validate_result['data']));
        }

        $p = $r->params();
        $member_id = $p['coach_id'];
        $member_type = $p['member_type'];
        $sms_type = $p['sms_type'];

        try {
            $db = getConnection();
            $sms_sender = DBPREFIX.'sms_sender';
            // return $rt
            $rt = array();
            if ( $sms_type == '0') { //unread msg index page of app
                $sql = " SELECT COUNT(1) AS total FROM `{$sms_sender}` ";
                $where = " WHERE `member_type` = '{$member_type}' AND `member_id` = '{$member_id}' AND `is_read` = 2 ";
                $_sql = $sql . $where;
                $stmt = $db->query($_sql);
                $have_new_msg = $stmt->fetch(PDO::FETCH_ASSOC);
                $rt = array(
                    'code' => '200',
                    'data' => $have_new_msg['total'],
                );
            } elseif ( $sms_type == '3' ) { // 3 = 1 + 2 系统消息和订单消息
                $msg_type_ar = array('1', '2');
                $sql = " SELECT `i_yw_type` AS `msg_type`, COUNT(1) AS `total` FROM `{$sms_sender}` ";
                $where = " WHERE `member_type` = '{$member_type}' AND `member_id` = '{$member_id}' AND `is_read` = 2 AND `i_yw_type` IN ('".implode("','", $msg_type_ar)."') GROUP BY `i_yw_type` ";
                $_sql = $sql . $where;
                $stmt = $db->query($_sql);
                $have_new_msg = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $unread_msgs = array();
                if ($have_new_msg) {
                    foreach ($have_new_msg as $key => $val) {
                        $unread_msgs[$val['msg_type']] = $val['total'];
                        $msg_type_ar = array_diff($msg_type_ar, array($val['msg_type']));
                    }
                    if ($msg_type_ar) {
                        foreach ($msg_type_ar as $key) {
                            $unread_msgs[$key] = '0';
                        }
                    }
                } else {
                    foreach ($msg_type_ar as $val) {
                        $unread_msgs[$val] = '0';
                    }
                }
                $rt = array(
                    'code' => '200',
                    'data' => $unread_msgs,
                );
            } elseif ( $sms_type == '1' or $sms_type == '2') {
                $msg_type_ar = array('1', '2');
                $orderby = array('DESC', 'ASC');
                $select = " SELECT `id`, `s_content` AS `content`, `s_from` AS `from`, `s_beizhu` AS `beizhu`, `addtime`, `is_read`, `i_yw_type` AS `msg_type` ";
                $from = " FROM `{$sms_sender}` ";
                $where = " WHERE `member_type` = '{$member_type}' AND `member_id` = '{$member_id}' AND `is_read` != '101' AND `i_yw_type` = '{$sms_type}' ";
                if (in_array('sort', array_keys($p)) and 'is_read' == $p['sort']) {
                    $order = " ORDER BY `{$p['sort']}` ";
                    if (in_array('order', array_keys($p)) and in_array($p['order'], $orderby)) {
                        $order .= " {$p['order']} ";
                    } else {
                        $order .= " DESC ";
                    }
                    $order .= " , `addtime` DESC ";
                } else {
                    $order = " ORDER BY `addtime` DESC ";
                }
                if (in_array('page', array_keys($p)) and is_numeric($p['page']) and $p['page'] != 0) {
                    $page = (int)$p['page'];
                } else {
                    $page = 1;
                }
                $ipp = 20; // Item Per Page
                $start = $ipp * ($page - 1);
                $limit = " LIMIT $start, $ipp ";
                $sql = $select . $from . $where . $order . $limit;
                $stmt = $db->query($sql);
                $msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($msgs) {
                    $rt = array(
                        'code' => '200',
                        'data' => $msgs,
                    );
                } else {
                    $rt = array(
                        'code' => '104',
                        'data' => '暂无消息',
                    );
                }
            } else {
                $rt = array('code' => 110, 'data' => '参数错误');
            }
            $db = null;
            exit( json_encode($rt) );
        } catch(PDOException $e) {
            setapilog('[get_message_list] [:error] [client ' . $r->getIp() . '] [params ' . serialize($r->params()) . '] ['.$e->getLine().' ' . $e->getMessage() . ']');
            $data = array('code'=>1, 'data'=>'网络错误');
            exit(json_encode($data));
        }
    }
?>
