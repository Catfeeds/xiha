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

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        /*
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '此接口仅开放POST方式请求');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array('user_id' => 'INT'), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res, null, '此接口仅开放POST请求方式');
            ajaxReturn($validate_ok['data']);
        }
        */

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();
            $stmt = $db->query(" SHOW TABLES ");
            $table_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ( array() === $table_list ) {
                $data = array('code' => 200, 'data' => 'no need');
                ajaxReturn($data);
            }
            $sql = " DESC :tbl ";
            $stmt = $db->prepare();
            $stmt->bindParam('tbl', $table);
            foreach ($table_list as $index => $table) {
                $table = $table[''];
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
