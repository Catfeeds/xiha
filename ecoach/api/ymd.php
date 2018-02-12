<?php
    /**
    * 接口模版
    * @param    type $var comment
    * @return   json
    * @package  /path/from/api
    * @author   gdc
    * @date     July 8, 2016
    **/

    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    require 'include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'funcname');
    $app->run();

    function funcname() {
        global $app, $crypt;

        /*
        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '此接口仅开放POST方式请求');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array('user_id' => 'INT'), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res, null, '参数不完整或参数类型不对，请核对文档与您传的参数');
            ajaxReturn($validate_ok['data']);
        }
        */

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();
            $history_tbl = DBPREFIX.'exam_history';
            $sql = " SELECT `id`, `time_interval` AS time FROM `{$history_tbl}` WHERE 1 ";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $exam_history_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (is_array($exam_history_list) && !empty($exam_history_list)) {
                $sql = " UPDATE `{$history_tbl}` SET `year` = :y, `month` = :m, `day` = :d WHERE `id` = :i ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('y', $year);
                $stmt->bindParam('m', $month);
                $stmt->bindParam('d', $day);
                $stmt->bindParam('i', $id);
                foreach ($exam_history_list as $index => $exam) {
                    $year = date('Y', $exam['time']);
                    $month = date('m', $exam['time']);
                    $day = date('d', $exam['time']);
                    $id = $exam['id'];
                    $stmt->execute();
                }
                $data['code'] = 200;
                $data['data'] = 'ok';
                ajaxReturn($data);
            }

            $db = null;
            ajaxReturn($data);

        } catch ( PDOException $e ) {
            slimLog($req, $res, $e, '网络异常');
            $data['code'] = 1;
            $data['data'] = '网络异常';
            $db = null;
            ajaxReturn($data);
        } catch ( ErrorException $e ) {
            slimLog($req, $res, $e, 'slim应用错误');
            $data['code'] = 1;
            $data['data'] = '网络错误';
            $db = null;
            ajaxReturn($data);
        }
    } // main func
?>
