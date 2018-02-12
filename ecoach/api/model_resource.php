<?php
    /**
    * 获取场地的模型资源文件下载地址
    * @param    int     $site_id    场地id[默认 1]
    * @return   json
    * @package  /api
    * @author   gdc
    * @date     Oct 09, 2016
    **/

    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    require 'include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'model_resource');
    $app->run();

    function model_resource() {
        global $app, $crypt;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '需要POST方式请求');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(
            array(
                'site_id' => 'INT',
            ), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res, null, '参数不正确');
            ajaxReturn($validate_ok['data']);
        }

        $p = $req->params();
        $site_id = $p['site_id'];
        //ready to return
        $data = array();

        try {
            // Open connection with mysql
            $db = getConnection();

            $site_tbl = DBPREFIX.'site';
            $sql = " SELECT `id`, `model_resource_url` FROM `{$site_tbl}` WHERE `id` = :stid ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('stid', $site_id);
            $stmt->execute();
            $model_resource_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($model_resource_info) {
                if ( !empty((string)$model_resource_info['model_resource_url'])
                    && strpos((string)$model_resource_info['model_resource_url'], '../') == 0    // 路径以'../'开始
                    && file_exists('../'.(string)$model_resource_info['model_resource_url'])  // 文件确实存在于服务器目录中
                ) {
                    $model_resource_info['model_resource_url'] = HOST.substr((string)$model_resource_info['model_resource_url'], 3, strlen((string)$model_resource_info['model_resource_url']));
                    $data = array('code' => 200, 'data' => $model_resource_info);
                } else {
                    // 文件不存在
                    $data = array('code' => 200, 'data' => array());
                }
            } else {
                //  场地不存在
                $data = array('code' => 200, 'data' => array());
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
