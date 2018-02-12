<?php
    /**
    * 获取在线题库的问题列表，根据题号
    * @param    string      $question_ids   逗号分隔的题目编号
    * @return   json
    * @package  api/v2/exam
    * @author   gdc
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';
    require '../../include/exam.inc.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    //$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'funcname');
    $app->run();

    function funcname() {
        global $app, $crypt;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        $question_ids = '0';
        if ( !$req->isPost() ) {
            slimLog($req, $res);
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array('question_ids' => 'STRING'), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res);
            ajaxReturn($validate_ok['data']);
        }
        $question_ids = $req->params('question_ids');
        $question_ids = array_filter(explode(',', $question_ids));
        $question_ids = empty($question_ids) ? '0' : "'" . implode("','", $question_ids) . "'";
        $question_list = array();
        $page = 1;
        $limit = 25;
        $start = ($page-1)*$limit;

        try {
            // Open connection with mysql
            $db = getConnection();
            $tbl = DBPREFIX . 'exams';
            $sql = " SELECT * FROM `{$tbl}` WHERE `id` IN ({$question_ids}) LIMIT {$start}, {$limit} ";
            $stmt = $db->query($sql);
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

            // shut down the connection
            $db = null;
            ajaxReturn($data);
        } catch ( PDOException $e ) {
            slimLog($req, $res, $e, '网络异常');
            ajaxReturn(array('code' => 1, 'data' => '网络异常'));
        }
    }
?>
