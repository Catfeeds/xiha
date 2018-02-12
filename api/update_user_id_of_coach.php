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

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();

            $coach_tbl = DBPREFIX.'coach';
            $user_tbl = DBPREFIX.'user';
            //$sql = " SELECT c.`l_coach_id` AS coach_id, c.`user_id` AS old_user_id, u.`l_user_id` AS user_id FROM `{$coach_tbl}` AS c LEFT JOIN `{$user_tbl}` AS u ON c.`l_coach_id` = u.`coach_id` WHERE c.`user_id` = 0 ";
            $sql = " SELECT c.`l_coach_id` AS coach_id, c.`user_id` AS old_user_id, u.`l_user_id` AS user_id FROM `{$coach_tbl}` AS c LEFT JOIN `{$user_tbl}` AS u ON c.`s_coach_phone` = u.`s_phone` WHERE c.`user_id` = 0 ";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $coach_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (array() === $coach_list) {
                $data = array('code' => 200, 'data' => 'all is well');
                $db = null;
                ajaxReturn($data);
            }

            $sql = " UPDATE `{$coach_tbl}` SET `user_id` = :uid WHERE `l_coach_id` = :cid ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uid', $user_id);
            $stmt->bindParam('cid', $coach_id);
            $count = 0;
            foreach ($coach_list as $index => $value) {
                if ((int)$value['old_user_id'] !== (int)$value['user_id']) {
                    $user_id = $value['user_id'];
                    $coach_id = $value['coach_id'];
                    $stmt->execute();
                    $count++;
                }
            }

            if ($count > 0) {
                $data = array('code' => 200, 'data' => $count);
            } else {
                $data = array('code' => 200, 'data' => 'ok');
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
