<?php
    /**
    * 缓存在线题库
    * @param    string      $ctype      车型(C1, A2)
    * @param    int         $stype      科目(1,4)
    * @return   json
    * @package  api/v2/exam
    * @author   gdc [Dickens.Gao at gmail dot com]
    * @date     July 5, 2016
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    //require '../../include/crypt.php';
    require '../../include/functions.php';
    require '../../include/exam.inc.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    //$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'funcname');
    $app->run();

    function funcname() {
        global $app, $crypt, $redis_conf;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '此接口仅开放POST请求方式');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array(
                'ctype' => 'STRING',
                'stype' => 'INT',
            ), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res);
            ajaxReturn($validate_ok['data']);
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
                if ($redis->exists("exam:question_list:{$ctype}:{$stype}")) {
                    $data['code'] = 200;
                    $data['data'] = unserialize($redis->get("exam:question_list:{$ctype}:{$stype}"));
                    ajaxReturn($data);
                }
            }
            // redis

            // Open connection with mysql
            $db = getConnection();
            $tbl = DBPREFIX . 'exams';
            $sql = " SELECT * FROM `{$tbl}` WHERE `ctype` = :c AND `stype` = :s ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('c', $ctype, PDO::PARAM_STR);
            $stmt->bindParam('s', $stype, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (is_array($result) && count($result) > 0) {
                $imghost = HOST . 'm/assets/images';
                foreach ($result as $index => $question) {
                    $result[$index]['id'] = (int)$question['id'];
                    $result[$index]['type'] = (int)$question['type'];
                    $result[$index]['chapterid'] = (int)$question['chapterid'];
                    $result[$index]['stype'] = (int)$question['stype'];
                    if (empty($question['imageurl'])) {
                        continue;
                    }
                    $result[$index]['imageurl'] = $imghost . $question['imageurl'];
                    $mime = getMimeType(ROOT . 'm/assets/images' . $question['imageurl']);
                    switch (explode('/', $mime)[0]) {
                        case 'image' :
                            $result[$index]['mediaType'] = 1;
                            break;
                        case 'video' :
                            $result[$index]['mediaType'] = 2;
                            break;
                        case 'other' :
                            $result[$index]['mediaType'] = 99;
                            break;
                    }
                }
                $question_list = $result;
            }

            $data['code'] = 200;
            $data['data'] = array('question_list' => $question_list);

            // if Redis is up
            if ($redis_already_up) {
                $redis->set("exam:question_list:{$ctype}:{$stype}", serialize($data['data']), $redis_conf['TTL_WEEK']);
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
