<?php
    /**
    * 获取app的最新版本型号
    * @param integer $os_type 系统类型  1 android 2 ios
    * @param integer $app_client 1 学员端app 2 教练端app 3 校长端app
    * @package api/v2/headmaster
    * @author gaodacheng
    **/

    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    require 'include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'getAppVersion');
    $app->run();

    function getAppVersion() {
        global $app, $crypt;

        //验证请求方式 POST
        $r = $app->request();
        if ( !$r->isGet() ) {
            setapilog('[update_user_info] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            exit( json_encode(array('code' => 106, 'data' => '请求错误')) );
        }

        //取得参数列表
        $validate_ok = validate(array('os_type' => 'INT', 'app_client' => 'INT'), $r->params());
        if ( !$validate_ok['pass'] ) {
            exit( json_encode($validate_ok['data']) );
        }

        $os_type = $r->params('os_type');
        $app_client = $r->params('app_client');

        try {
            //建立数据库连接
            $db = getConnection();
            $sql = " SELECT `version`, `addtime` FROM `" . DBPREFIX . "app_version` WHERE `os_type` = :os_type AND `app_client` = :app_client ORDER BY `addtime` DESC LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('os_type', $os_type, PDO::PARAM_INT);
            $stmt->bindParam('app_client', $app_client, PDO::PARAM_INT);
            $res = $stmt->execute();
            $version_info = $stmt->fetch(PDO::FETCH_ASSOC);
            //关闭数据库
            $db = null;
            if ( $version_info ) {
                $version_info['addtime'] = date('Y-m-d H:i:s', $version_info['addtime']);
                exit( json_encode( array('code' => 200, 'data' => $version_info) ) );
            } else {
                exit( json_encode( array('code' => 104, 'data' => '无数据') ) );
            }
        } catch ( PDOException $e ) {
            setapilog('[comment_like] [:error] [client ' . $r->getIP() . '] [os_type, app_client % ' . $os_type . ',' . $app_client . '] [1 网络异常:' . $e->getMessage() . ']');
            exit( json_encode(array('code' => 1, 'data' => '网络异常')) );
        }
    }
?>
