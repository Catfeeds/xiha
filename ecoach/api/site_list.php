<?php
    /**
    * 获取场地列表，根据驾校
    * @param    int             $user_id        用户id
    * @param    string          $user_phone     用户手机号
    * @return   json
    * @package  /ecoach/api
    * @author   gdc
    * @date     Aug 8, 2016
    **/

    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    require 'include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'site_list');
    $app->run();

    function site_list() {
        global $app, $crypt;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '需要POST');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array(
            'user_id'       => 'INT',
            'user_phone'    => 'STRING',
        ), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res, null, '参数不正确');
            ajaxReturn($validate_ok['data']);
        }

        $p = $req->params();
        $user_phone = $p['user_phone'];
        $user_id = $p['user_id'];

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();

            // step one: get school_id
            $user_tbl = DBPREFIX.'user';
            $user_info_tbl = DBPREFIX.'users_info';
            $sql = " SELECT info.`school_id` FROM `{$user_tbl}` AS user LEFT JOIN `{$user_info_tbl}` AS info ON user.`l_user_id` = info.`user_id` WHERE user.`i_user_type` = 0 AND user.`i_status` = 0 AND user.`s_phone` = :phone AND user.`l_user_id` = :user_id ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('phone', $user_phone);
            $stmt->bindParam('user_id', $user_id);
            $stmt->execute();
            $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (false === $user_info || !isset($user_info['school_id']) || (int)$user_info['school_id'] == 0) {
                $data = array('code' => 200, 'data' => array('site_list' => array()));
                slimLog($req, $res, null, '驾校id未找到');
                ajaxReturn($data);
            }
            $school_id = $user_info['school_id'];

            $site_tbl = DBPREFIX.'site';
            ajaxReturn($user_info);

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
