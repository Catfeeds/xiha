<?php
    /**
    * 接口模版
    * @param    type            $var    comment
    * @return   json
    * @package  /path/from/api
    * @author   gdc
    * @date     July 8, 2016
    **/

    require '../Slim/Slim.php';
    require '../include/common.php';
    require '../include/crypt.php';
    require '../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $logger = $app->log;
    $logger->setEnabled(true);
    $logger->setLevel(\Slim\Log::DEBUG);
    $env = \Slim\Environment::getInstance();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'funcname');
    $app->run();

    function funcname() {
        global $app, $crypt, $logger;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        $logger->info("request begin");
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '需要POST');
            $logger->info("需要POST");
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array('user_id' => 'INT'), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res, null, '参数不完整或参数类型不对，请核对文档与您传的参数');
            ajaxReturn($validate_ok['data']);
        }

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();

            $db = null;
            ajaxReturn($data);

        } catch ( PDOException $e ) {
            slimLog($req, $res, $e, 'PDO数据库异常');
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
