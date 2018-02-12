<?php 
    /**
     * 保存学员的考试记录
     * @param   int     $user_id        用户id
     * @param   string  $error_exam_id  逗号分开的错题号
     * @param   int     $score          考试分数(100分満分)
     * @param   int     $license        牌照 (C1/A1/A2/D)
     * @param   int     $subject        科目 (1/4)
     * @param   int     $total_time     考试用时(单位：秒)
     * @param   int     $school_id      驾校id
     * @param   string  $real_name      真实姓名
     * @param   int     $user_phone     学员手机号码
     * @param   string  $identify_id    学员身份证号码
     * @return  json
     * @author  GDC
     **/
    require '../../Slim/Slim.php';
    require '../../include/common.php';
    //require '../../include/crypt.php';
    require '../../include/functions.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    //$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/','saveScore');
    $app->run();

    function saveScore() {
        global $app, $crypt;

        $req = $app->request();
        $res = $app->response();

        if ( !$req->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            slimLog($req, $res);
            ajaxReturn($data);
        }
        //    获取请求参数并判断合法性
        $validate_result = validate(array(
                'user_id'       => 'INT',
                'score'         => 'INT',
                'license'       => 'STRING', 
                'subject'       => 'INT',
                'total_time'    => 'INT',
                'school_id'     => 'INT',
                'real_name'     => 'STRING',
                'user_phone'    => 'INT',
                'identify_id'   => 'STRING',
            ), $req->params());
        if (  !$validate_result['pass'] ) {
            ajaxReturn($validate_result['data']);
        } 

        $p = $req->params();

        try{
            $now = time();
            $sql = " INSERT INTO `cs_user_exam_records` (`user_id`, `realname`, `phone_num`, `identify_id`, `error_exam_id`, `score`, `stype`, `ctype`, `exam_total_time`, `school_id`, `addtime`) ";
            $sql .= " VALUES ( ";
            $sql .= " '{$p['user_id']}', '{$p['real_name']}', '{$p['user_phone']}', '{$p['identify_id']}', '{$p['error_exam_id']}', '{$p['score']}', '{$p['subject']}', '{$p['license']}', '{$p['total_time']}', '{$p['school_id']}', '{$now}' ";
            $sql .= " ) ";
            $db = getConnection();
            $stmt = $db->query($sql);
            if ( $stmt ) {
                $data = array('code' => 200, 'data' => '操作成功');
            } else {
                $data = array('code' => 400, 'data' => '操作失败');
            }
            $db = null;
            ajaxReturn($data);

        } catch (PDOException $e) {
            slimLog($req, $res, $e);
            $data = array('code' => 1, 'data' => '网络错误');
            $db = null;
            ajaxReturn($data);
        }
    } /*saveScore*/
?>
