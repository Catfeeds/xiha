<?php
    /**
    * 自动完成教练注册，场景：将user表中的用户补充到coach表中，开成一对一的关联
    * @param    type            $var    comment
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

        $req = $app->request();
        $res = $app->response();

        /*
        //验证请求方式 POST
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '需要POST');
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
        $count = 0;
        try {
            // Open connection with mysql
            $db = getConnection();

            // 需要将user表中的l_user_id更新到coach表中的user_id
            $sql = "SELECT u.* FROM cs_user AS u LEFT JOIN cs_coach AS c ON u.s_phone = c.s_coach_phone WHERE u.i_status = 0 AND i_user_type = 1 AND c.user_id <= 0 ORDER BY l_user_id DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $user_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($user_list) > 0) {
                $sql = "UPDATE cs_coach SET `user_id` = :uid WHERE `s_coach_phone` = :cp";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('uid', $uid);
                $stmt->bindParam('cp', $cp);
                foreach ($user_list as $index => $user) {
                    $uid = $user['l_user_id'];
                    $cp = $user['s_phone'];
                    $result = $stmt->execute();
                    if ($result) {
                        $count++;
                    }
                }
            }

            // 需要在coach表中插入一条记录
            $sql = "SELECT u.* FROM cs_user AS u LEFT JOIN cs_coach AS c ON u.s_phone = c.s_coach_phone WHERE u.i_status = 0 AND i_user_type = 1 AND c.user_id IS NULL ORDER BY l_user_id DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $user_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($user_list) > 0) {
                $sql = "INSERT INTO cs_coach (s_coach_name, s_teach_age, s_coach_sex, s_coach_phone, user_id) VALUES (:name, '3', '1', :cp, :uid)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('name', $name);
                $stmt->bindParam('uid', $uid);
                $stmt->bindParam('cp', $cp);
                foreach ($user_list as $index => $user) {
                    $name = $user['s_real_name'];
                    $uid = $user['l_user_id'];
                    $cp = $user['s_phone'];
                    $result = $stmt->execute();
                    if ($result) {
                        $count++;
                    }
                }
            }

            $data = array(
                'code' => 200,
                'data' => $count,
            );
            ajaxReturn(['code' => 200, 'data' => $data]);

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
