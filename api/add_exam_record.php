<?php 
    /**
     * 添加一条用户的考试记录
     * @param integer user_id
     * @param string error_exam_id
     * @param integer score
     * @param integer $license 牌照C1/A1/A2/D
     * @param integer $subject 科目1/4 
     * @param total_time
     * @return 
     * @author Gao Dcheng  
     **/
    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    require 'include/functions.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/','addExamRecord');
    $app->run();

    function addExamRecord() {
        global $app, $crypt;

        $request = $app->request();

        if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[add_exam_record] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            echo json_encode($data);
            exit();
        }
        //  获取请求参数并判断合法性
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
            ), $request->params());
        if (  !$validate_result['pass'] ) {
            echo json_encode($validate_result['data']);
            exit();
        } 

        $p = $request->params();

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
            echo json_encode($data);

        } catch (PDOException $e) {
            setapilog('[add_exam_record] [:error] [1 ' . $e->getMessage() . ']');
            $data = array('code' => 1, 'data' => '网络错误');
            echo json_encode($data);
        }

    }


?>
