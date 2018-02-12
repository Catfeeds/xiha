<?php
    /**
    * 删除教练下的一个用户
    * @param    int             $user_id    用户id
    * @param    int             $coach_id   教练id
    * @param    string          $user_phone 用户手机号码
    * @return   json
    * @package  /api/v2/coachuser/
    * @author   gdc
    * @date     July 8, 2016
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

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
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '此接口仅开放POST请求方式');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array(
            'user_id'       => 'INT',
            'user_phone'    => 'STRING',
        ), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res);
            ajaxReturn($validate_ok['data']);
        }

        $p = $req->params();
        $coach_id = $p['coach_id'];
        $user_id = $p['user_id'];
        $user_phone = $p['user_phone'];

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();
            $tbl = DBPREFIX . 'coach_users_records';
            $sql = " SELECT `id` as del_id FROM `{$tbl}` WHERE `is_deleted` = 1 AND `coach_id` = :cid AND `coach_users_id` = :uid AND `user_phone` = :uphone ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid', $coach_id, PDO::PARAM_INT);
            $stmt->bindParam('uid', $user_id, PDO::PARAM_INT);
            $stmt->bindParam('uphone', $user_phone, PDO::PARAM_STR);
            $stmt->execute();
            $del_id = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($del_id) {
                // delete it logically: is_deleted = 1 -> 2
                $sql = " UPDATE `{$tbl}` SET `is_deleted` = 2 WHERE `id` = :did ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('did', $del_id['del_id'],PDO::PARAM_INT);
                $res = $stmt->execute();
                if ($res) {
                    $data['code'] = 200;
                    $data['data'] = '用户删除成功';
                } else {
                    $data['code'] = 400;
                    $data['data'] = '用户删除失败';
                }
            } else {
                // user is not already existed
                $data['code'] = 200;
                $data['data'] = '用户已经被删除';
            }

            // shut down the connection
            $db = null;

            // return to client
            ajaxReturn($data);

        } catch ( PDOException $e ) {
            slimLog($req, $res, $e, '网络异常');
            $data['code'] = 1;
            $data['data'] = '网络异常';
            ajaxReturn($data);
        } catch ( ErrorException $e ) {
            slimLog($req, $res, $e, 'slim应用错误');
            $data['code'] = 1;
            $data['data'] = '网络错误';
            ajaxReturn($data);
        }
    } // main func
    /**
     * 此接口逻辑删除用户
     * 用字段is_deleted表示
     * is_deleted = 1用户状态正常未删除
     * is_deleted = 2用户处于被删除状态
     * updated at July 9, 2016 [GDC]
     */
?>
