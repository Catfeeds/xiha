<?php
    /**
    * 获取驾驶执照类型列表
    * @param    none
    * @return   json
    * @package  /api/v2/coachuser/
    * @author   gdc
    * @date     Aug 4, 2016
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'driver_license');
    $app->run();

    function driver_license() {
        global $app, $crypt, $lisence_config;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() && !$req->isGet() ) {
            slimLog($req, $res, null, '需要POST或GET');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //ready to return
        $data = array();
        try {
            $driver_license_tmp = $lisence_config;
            $driver_license = array();
            if (!isset($driver_license_tmp) || empty($driver_license_tmp)) {
                slimLog($req, $res, null, '还没有配置，请尽快配置驾照类型common.php');
                $data = array('code' => 200, 'data' => array());
            } else {
                foreach ($driver_license_tmp as $index => $value) {
                    $driver_license[] = array('license_id' => $index, 'license_name' => $value);
                }
                $data = array('code' => 200, 'data' => $driver_license);
            }
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
