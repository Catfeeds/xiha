<?php
    /**
    * 删除学员的身份证图片
    * @param    int              $coach_id      教练id
    * @param    int              $user_id       学员id
    * @param    string           $user_phone    学员手机号码
    * @return   json
    * @package  /path/from/api
    * @author   gdc
    * @date     July 15, 2016
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'del_student_identity_img');
    $app->run();

    function del_student_identity_img() {
        global $app, $crypt;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '此接口仅开放POST方式请求');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array(
                'coach_id'      => 'INT',
                'user_id'       => 'INT',
                'user_phone'    => 'string',
            ), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res);
            ajaxReturn($validate_ok['data']);
        }

        $p          = $req->params();
        $coach_id   = $p['coach_id'];
        $user_id    = $p['user_id'];
        $user_phone = $p['user_phone'];

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();

            //  判断学员是否存在
            $tbl = DBPREFIX . 'coach_users_records';
            $sql = " SELECT 1 FROM `{$tbl}` WHERE `coach_users_id` = :uid AND `coach_id` = :cid AND `user_phone` = :uphone ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid',$coach_id, PDO::PARAM_INT);
            $stmt->bindParam('uid',$user_id, PDO::PARAM_INT);
            $stmt->bindParam('uphone',$user_phone, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$result) {
                $data = array('code' => 103, 'data' => '不存在此学员');
                $db = null;
                ajaxReturn($data);
            }

            $tbl = DBPREFIX . 'coach_users_records';
            $blank = '';
            $sql = " UPDATE `{$tbl}` SET `identity_img` = '{$blank}' WHERE `coach_id` = :cid AND `coach_users_id` = :uid AND `user_phone` = :uphone ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid', $coach_id, PDO::PARAM_INT);
            $stmt->bindParam('uid', $user_id, PDO::PARAM_INT);
            $stmt->bindParam('uphone', $user_phone, PDO::PARAM_STR);
            $result = $stmt->execute();
            if ($result) {
                $data['code'] = 200;
                $data['data'] = '删除成功';
            } else {
                $data['code'] = 400;
                $data['data'] = '删除失败';
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
