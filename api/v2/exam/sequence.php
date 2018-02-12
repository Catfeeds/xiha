<?php
    /**
    * 在线题库-顺序练习
    * @param    string      $ctype 车型     (C1,A2)
    * @param    int         $stype 科目类型 (1,4)
    * @return   json
    * @package  api/v2/exam
    * @author   gdc
    * @update   July 5, 2016
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    //require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    //$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'sequence');
    $app->run();

    function sequence() {
        global $app, $crypt, $redis_conf;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '此接口仅开放POST请求');
            $data = array('code' => '106', 'data' => '请求错误');
            ajaxReturn($data);
        }

        //取得参数列表
        $validate_ok = validate(array(
            'stype' => 'INT',
            'ctype' => 'STRING',
        ), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res);
            $data = $validate_ok['data'];
            ajaxReturn($data);
        }

        $p = $req->params();
        $redis_already_up = false; //redis启动
        $ctype = $p['ctype'];
        $stype = $p['stype'];
        $data = array();

        try {
            // redis
            $redis = getRedisConnection();
            if ($redis) {
                $redis_already_up = true;
                if ($redis->exists("exam:sequence_id:{$ctype}:{$stype}")) {
                    $data['code'] = 200;
                    $data['data'] = unserialize($redis->get("exam:sequence_id:{$ctype}:{$stype}"));
                    ajaxReturn($data);
                }
            }
            // redis

            // Open connection with mysql
            $db = getConnection();
            $tbl = DBPREFIX . 'exams';
            $sql = " SELECT `id` FROM `{$tbl}` WHERE `ctype` = :c AND `stype` = :s ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('c', $ctype);
            $stmt->bindParam('s', $stype);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $question_ids = array();
            if (is_array($result)) {
                $question_ids = $result;
            }

            $data = array(
                'code'  => 200,
                'data'  => array(
                    'question_ids' => $question_ids,
                ),);

            // if Redis is up
            if ($redis_already_up && !empty($question_ids)) {
                $redis->set("exam:sequence_id:{$ctype}:{$stype}", serialize($data['data']), $redis_conf['TTL_WEEK']);
            }

            // shut down the connection
            $db = null;
            ajaxReturn($data);

        } catch (PDOException $e) {
            slimLog($req, $res, $e, '网络异常');
            $data['code'] = 1;
            $data['data'] = '网络错误';
            ajaxReturn($data);
        } catch (ErrorException $e) {
            slimLog($req, $res, $e, 'Slim应用错误');
            $data['code'] = 1;
            $data['data'] = '网络错误';
            ajaxReturn($data);
        }
    } // main func
?>
