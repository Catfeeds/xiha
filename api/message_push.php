<?php

    // 推送接口
    require 'Slim/Slim.php';
    require 'include/jpush.php';
    require 'include/common.php';
    require 'include/crypt.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->post('/','jpushnotice');
    $app->run();

    // 取消订单
    function jpushnotice() {
        Global $app, $crypt;
        $request = $app->request();
        $user_phone = $request->params('user_phone');
        $member_id = $request->params('member_id');
        $member_type = $request->params('member_type');
        $s_beizhu = $request->params('s_beizhu');
        $title = $request->params('title');
        $content = $request->params('content');
        $type = $request->params('type'); // (1:学员端 2：教练端)
        $i_yw_type = $request->params('i_yw_type'); // 1:通知 2：正常订单消息

        if($type == 2) {
            // 教练端
            $app_key = '3ebbbf7c2e811171a6e5c836';
            $master_secret = 'b4272f005b740f30d49a6758';

        } else {
            // 学员端
            $app_key = 'c1b9d554f52b5668cba58c75';
            $master_secret = '68ce861810390d1e88112310';        
        }

        try {
            $Jpush = new Jpush($app_key, $master_secret);
            $receive = array(
                'alias'=>array(
                    $user_phone
                    )
                );
            // $content = '测试陈锋1'; // app显示内容
            // $m_type = 'tips';
            // $m_txt = '测试陈锋2';
            $m_time = '86400';
            $result = $Jpush->send_pub($receive, $content, $title, $content, $m_time);
            $result = json_decode($result, true);
            // print_r($result);
            $arr = array();
            $arr['sendno'] = isset($result['sendno']) ? $result['sendno'] : '';
            $arr['msg_id'] = isset($result['msg_id']) ? $result['msg_id'] : '';
            $arr['content'] = $content;
            $arr['s_from'] = '嘻哈学车';
            $arr['time'] = time();
            $arr['member_id'] = $member_id;
            $arr['member_type'] = $member_type;
            $arr['s_beizhu'] = $s_beizhu;
            $arr['i_yw_type'] = $i_yw_type;

            $res = insertSms($arr);
            if($res) {
                $data = array('code'=>200, 'data'=>$result);
            } else {
                $data = array('code'=>-2, 'data'=>'推送失败');
            }
            echo json_encode($data);

        }catch(PDOException $e) {
            setapilog('message_push:params[user_phone:'.$user_phone.',member_id:'.$member_id.',member_type:'.$member_type.',s_beizhu:'.$s_beizhu.',title:'.$title.',content:'.$content.',type:'.$type.'], error:'.$e->getMessage());    
            $data = array('code'=>1, 'data'=>'推送失败');
            echo json_encode($data);
        }
    }

    // 插入到数据库
    function insertSms($arr) {
        $db = getConnection();
        $sql = "INSERT INTO `cs_sms_sender` (`dt_sender`, `i_jpush_sendno`, `i_jpush_msg_id`, `s_content`, `s_from`, `s_beizhu`, `member_id`, `member_type`, `i_yw_type`, `addtime`) VALUES (";
        $sql .= "'".$arr['time']."', '".$arr['sendno']."', '".$arr['msg_id']."', '".$arr['content']."', '".$arr['s_from']."', '".$arr['s_beizhu']."', '".$arr['member_id']."', '".$arr['member_type']."', '".$arr['i_yw_type']."', '".$arr['time']."')";
        $stmt = $db->query($sql);
        return $stmt;
    }
?>
