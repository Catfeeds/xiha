<?php
    /**
    * 获取app更新信息
    * @param    int             $os_type    系统类型 1 Android 2 iOS
    * @param    int             $app_type   app类型 1 学员端 2 教练端 3 校长端
    * @return   json
    * @package  /api
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
    $app->any('/', 'get_app_update_info');
    $app->run();

    function get_app_update_info() {
        global $app, $crypt;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '需要POST');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(
            array(
                'os_type'   => 'INT',
                'app_type'  => 'INT',
            ), $req->params()
        );
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res, null, '参数不正确');
            ajaxReturn($validate_ok['data']);
        }

        //ready to return
        $data = array();

        $p = $req->params();
        $os_type = $p['os_type'];
        $app_type = $p['app_type'];

        if (!in_array($os_type, array(1,2))) { /* 1-Android  2-iOS */
            $data = array('code' => 102, 'data' => '不支持的系统');
            slimLog($req, $res, null, '不支持的系统');
            ajaxReturn($data);
        }
        
        if (!in_array($app_type, array(1,2,3))) { /* 1-学员端 2-教练端 3-校长端 */
            $data = array('code' => 102, 'data' => '不支持的客户端');
            slimLog($req, $res, null, '不支持的客户端');
            ajaxReturn($data);
        }

        try {
            // Open connection with mysql
            $db = getConnection();

            $app_tbl = DBPREFIX.'app_version';
            $sql = " SELECT `app_name`, `version` AS app_version, `version_code` AS app_version_code, `app_update_log`, `app_download_url`, `addtime` FROM `{$app_tbl}` WHERE `os_type` = :ost AND `app_client` = :client ORDER BY `addtime` DESC LIMIT 1 ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('ost', $os_type, PDO::PARAM_INT);
            $stmt->bindParam('client', $app_type, PDO::PARAM_INT);
            $stmt->execute();
            $app_update_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (file_exists($app_update_info['app_download_url'])) {
                $app_update_info['app_download_url'] = HTTP_HOST .$app_update_info['app_download_url'];
            } else {
                $app_update_info['app_download_url'] = '';
            }

            if (false === $app_update_info) {
                $data = array('code' => 200, 'data' => array('app_update_info' => array()));
            } elseif (is_array($app_update_info)) {
                $data = array('code' => 200, 'data' => array('app_update_info' => $app_update_info));
            }

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
