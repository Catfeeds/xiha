<?php
    /**
    * 更新用户信息接口
    * @param integer $user_id 校长id
    * @param string $user_name 昵称 可选
    * @package api/v2/headmaster
    * @author gdc
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'updateUserInfo');
    $app->run();

    function updateUserInfo() {
        global $app, $crypt;

        //验证请求方式 POST
        $r = $app->request();
        if ( !$r->isPost() ) {
            setapilog('[update_user_info] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            exit( json_encode(array('code' => 106, 'data' => '请求错误')) );
        }

        //参数里必须包含 user_id
        $validate_ok = validate(array('user_id' => 'INT'), $r->params());
        if ( !$validate_ok['pass'] ) {
            exit( json_encode($validate_ok['data']) );
        }
        $user_id = $r->params('user_id');

        /*
        //参数必须还要有一个其它的字段，即要修改一个字段
        $params_length = count( $r->params() );
        if ( 2 != $params_length ) {
            setapilog('[update_user_info] [:error] [client ' . $r->getIp() . '] [user_id % ' . $user_id . '] [108 参数数目不对: ' . serialize($r->params()) . ']');
            exit( json_encode( array( 'code' => '108', 'data' => '参数错误' , 'params' => $r->params()) ) );
        }
        */

        try {
            $db = getConnection();
            //根据user_id判断是不是校长用户
            $sql = "SELECT `l_user_id` FROM `" . DBPREFIX . "user` WHERE `l_user_id` = :id AND `i_user_type` = 2 && `i_status` = 0 ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user_ok = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( !$user_ok ) {
                setapilog('[update_user_info] [:error] [client ' . $r->getIp() . '] [user_id % ' . $user_id . '] [103 该校长用户不存在]');
                exit( json_encode(array('code' => 103, 'data' => '参数错误')) );
            }

            //如果传入user_name字段则修改用户昵称
            $user_name = $r->params('user_name');
            if ( !empty($user_name) ) {
                $validate_ok = validate(array('user_name' => 'STRING'), $r->params());
                if ( !$validate_ok['pass'] ) {
                    exit( json_encode($validate_ok['data']) );
                }

                $sql = "UPDATE `" . DBPREFIX . "user` SET `s_username` = :user_name WHERE `l_user_id` = :id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('user_name', $user_name, PDO::PARAM_STR);
                $stmt->bindParam('id', $user_id, PDO::PARAM_INT);
                $update_ok = $stmt->execute();
                //关闭数据库
                $db = null;
                if ( $update_ok ) {
                    exit( json_encode(array('code' => 200, 'data' => '昵称修改成功')) );
                } else {
                    exit( json_encode(array('code' => 400, 'data' => '昵称修改失败')) );
                }
            }
            //如果传入user_name字段则修改用户昵称
        } catch ( PDOException $e ) {
            setapilog('[comment_like] [:error] [client ' . $r->getIP() . '] [user_id,type % ' . $user_id . '] [1 网络异常]');
            exit( json_encode(array('code' => 1, 'data' => '网络异常')) );
        }
    }
?>
