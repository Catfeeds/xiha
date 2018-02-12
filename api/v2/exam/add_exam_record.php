<?php 
    /**
     * 添加一条用记的考试记录
     * @param   int     $user_id
     * @param   string  $error_exam_id
     * @param   int     $score
     * @param   int     $license        牌照C1/A1/A2/D
     * @param   int     $subject        科目1/4 
     * @param   int     $total_time     考试用时，单位：秒
     * @return  json
     * @author  Gao Dcheng 
     **/
    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/','getExamRecord');
    $app->run();

    function getExamRecord() {
        global $app, $crypt;

        $req = $app->request();
        $res = $app->response();

        if ( !$req->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[add_exam_record] [:error] [client ' . $req->getIp() . '] [Method % ' . $req->getMethod() . '] [106 错误的请求方式]');
            slimLog($req, $res, null, '此接口仅开放POST方式提交');
            echo json_encode($data);
            exit();
        }
        //    获取请求参数并判断合法性
        $validate_result = validate(array(
                'user_id'       => 'INT',
                'score'         => 'INT',
                'license'       => 'STRING', 
                'subject'       => 'INT',
                'total_time'    => 'INT',
                'school_id'     => 'INT',
                'os'            => 'STRING',
                //'real_name'    => 'STRING',
                //'user_phone'   => 'INT',
                //'identify_id'  => 'STRING',
                //去掉这些参数，避免用户在此输入，从数据库取，如果数据库没有，提醒用户在学员app补全 [gdc July 27 2016]
            ), $req->params());
        if (  !$validate_result['pass'] ) {
            echo json_encode($validate_result['data']);
            exit();
        } 

        $p = $req->params();
        $user_id = $p['user_id'];
        $score = $p['score'];
        $license = $p['license'];
        $subject = $p['subject'];
        $total_time = $p['total_time'];
        $school_id = $p['school_id'];
        $error_exam_id = $p['error_exam_id'];
        $os = $p['os'];

        try{
            $db = getConnection();
            // get student's identity_id, user_name, user_phone from 'user' table
            $user_tbl = DBPREFIX . 'user';
            $info_tbl = DBPREFIX . 'users_info';
            $sql = " SELECT u.`s_real_name` AS real_name, u.`s_phone` AS user_phone, i.`identity_id`, i.`school_id` FROM `{$user_tbl}` AS u LEFT JOIN `{$info_tbl}` AS i ON u.`l_user_id` = i.`user_id` WHERE u.`l_user_id` = :uid AND u.`i_status` = 0 AND u.`i_user_type` = 0 ";
            // 'i_status' 0 正常用户状态 2 用户已删除
            // 'i_user_type' 0 学员 1 教练 2 驾校
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uid', $user_id);
            $stmt->execute();
            $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user_info) {
                //数据库无此学员
                $user_name = '';
                $identity_id = '';
                $school_id = $school_id;
                $user_phone = '';
            } else {
                $user_name = isset($user_info['real_name']) ? $user_info['real_name'] : '';
                $user_phone = isset($user_info['user_phone']) ? $user_info['user_phone'] : '';
                $identity_id = isset($user_info['identity_id']) ? $user_info['identity_id'] : '';
                $school_id = isset($user_info['school_id']) ? $user_info['school_id'] : $school_id;
            }
            
            $now = time();
            $records_tbl = DBPREFIX.'user_exam_records';
            $sql = " INSERT INTO `{$records_tbl}` (`user_id`, `realname`, `phone_num`, `identify_id`, `error_exam_id`, `score`, `stype`, `ctype`, `exam_total_time`, `school_id`, `os`, `addtime`) ";
            $sql .= " VALUES ( ";
            $sql .= " '{$user_id}', '{$user_name}', '{$user_phone}', '{$identity_id}', '{$error_exam_id}', '{$score}', '{$subject}', '{$license}', '{$total_time}', '{$school_id}', '{$os}', '{$now}' ";
            $sql .= " ) ";
            $stmt = $db->query($sql);
            if ( $stmt ) {
                $data = array('code' => 200, 'data' => '操作成功');
            } else {
                $data = array('code' => 400, 'data' => '操作失败');
            }
            $db = null;
            echo json_encode($data);

        } catch (PDOException $e) {
            $data = array('code' => 1, 'data' => '网络错误');
            slimLog($req, $res, $e, '网络错误');
            echo json_encode($data);
        } catch (ErrorException $e) {
            $data = array('code' => 1, 'data' => '网络错误');
            slimLog($req, $res, $e, 'slim应用错误');
            echo json_encode($data);
        }
    } // main func
?>
