<?php
    /**
    * 在线题库-获取章节信息
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
            slimLog($req, $res);
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
        $redis_already_up = false; // redis启动
        $ctype = $p['ctype'];
        $stype = $p['stype'];
        $data = array();

        try {
            // redis
            $redis = getRedisConnection();
            if ($redis) {
                $redis_already_up = true;
                if ($redis->exists("exam:chapter_list_id:{$ctype}:{$stype}")) {
                    $data['code'] = 200;
                    $data['data'] = unserialize($redis->get("exam:chapter_list_id:{$ctype}:{$stype}"));
                    ajaxReturn($data);
                }
            }
            // redis

            // Open connection with mysql
            $db = getConnection();
            $exam_tbl = DBPREFIX . 'exams';
            $chapter_tbl = DBPREFIX . 'exam_chapters';
            $sql = " SELECT c.`cid`, c.`title`, COUNT(1) AS total FROM `{$exam_tbl}` AS e LEFT JOIN `{$chapter_tbl}` AS c ";
            $sql .= " ON e.`stype` = c.`stype` AND e.`ctype` = c.`ctype` AND e.`chapterid` = c.`cid` ";
            $sql .= " WHERE c.`stype` = :subjectType AND c.`ctype` = :carType ";
            $sql .= " GROUP BY c.`ctype`, c.`stype`, c.`cid` ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('carType', $ctype);
            $stmt->bindParam('subjectType', $stype);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $chapter_list = array();
            if (is_array($result)) {
                $chapter_list = $result;
            }

            $data = array(
                'code' => 200,
                'data' => array(
                    'chapter_list' => $chapter_list,
                ),
            );

            // if Redis is up
            if ($redis_already_up) {
                $redis->set("exam:chapter_list_id:{$ctype}:{$stype}", serialize($data['data']), $redis_conf['TTL_WEEK']);
            }

            // shut down the connection
            $db = null;

            ajaxReturn($data);

        } catch ( PDOException $e ) {
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
