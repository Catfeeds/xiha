<?php  
    /**
     * 忘记密码
     * @param 
     * @return 
     * @author 
     **/
    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/','forgetPass');
    $app->run();

    function forgetPass() {
        Global $app, $crypt;
        $request = $app->request();
        $response = $app->response();

        if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[forget_pass] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            exit(json_encode($data));
        }
        //  获取请求参数并判断合法性
        $validate_result = validate(
            array(
                'coach_phone'   =>'STRING', 
                'validate_code' =>'STRING', 
                'new_pass'         =>'STRING', 
                'new_repeat_pass' =>'STRING', 
            ), $request->params());

        if (!$validate_result['pass']) {
            exit(json_encode($validate_result['data']));
        }
        $p = $request->params();
        $coach_phone = $p['coach_phone'];
        $validate_code = $p['validate_code'];
        $new_pass = md5($p['new_pass']);
        $new_repeat_pass = md5($p['new_repeat_pass']);

        if($new_pass != $new_repeat_pass) {
            $data = array('code'=>102, 'data'=>'密码不相同');
            exit( json_encode($data) );
        }
        try {
            $db = getConnection();

            // 验证码验证
            $res = validateCode($coach_phone, $validate_code);
            if($res == 103) {
                $data = array('code'=>102, 'data'=>'验证码错误');
                exit( json_encode($data) );

            } else if($res == 106) {
                $data = array('code'=>102, 'data'=>'验证码时效3分钟已过期');
                exit( json_encode($data) );
            }

            $user = DBPREFIX.'user';
            $coach_tbl = DBPREFIX.'coach';
            $sql = "SELECT u.`s_password` FROM `{$user}` AS u RIGHT JOIN `{$coach_tbl}` AS c ON u.l_user_id = c.user_id ";
            $where = " WHERE u.`s_phone` = :phone AND u.`i_user_type` = 1 AND u.`i_status` = 0 ";
            $sql .= $where;
            $stmt = $db->prepare($sql);
            $stmt->bindParam('phone', $coach_phone);
            $stmt->execute();
            $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($user_info)) {
                $data = array('code'=>103, 'data'=>'账号不存在');
                exit( json_encode($data) );
            }

            /*
            // 不能与老密码重复
            if($user_info['s_password'] == $new_repeat_pass) {
                $data = array('code'=>103, 'data'=>'请勿与原密码相同');
                exit( json_encode($data) );
            }
            */

            // 修改密码
            $sql = "UPDATE `{$user}` SET `s_password` = :password";
            $sql .= " WHERE `s_phone` = :phone AND `i_user_type` = 1 AND `i_status` = 0 ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('password', $new_repeat_pass);
            $stmt->bindParam('phone', $coach_phone);
            $res = $stmt->execute();
            if($res) {
                $data = array('code'=>200, 'data'=>'修改成功');
            } else {
                $data = array('code'=>400, 'data'=>'修改失败');
            }
            $db = null;
            exit( json_encode($data) );

        } catch(PDOException $e) {
            setapilog('[forget_pass] [:error] [client ' . $request->getIp() . '] [params ' . serialize($request->params()) . '] [1 ' . $e->getMessage() . ']');
            slimLog($request, $response, $e, 'PDO数据库异常');
            $data = array('code'=>1, 'data'=>'网络错误');
            exit(json_encode($data));
        } catch (ErrorException $e) {
            slimLog($request, $response, $e, 'PDO数据库异常');
            $data = array('code'=>1, 'data'=>'网络异常');
            exit(json_encode($data));
        }
    }

    // 验证验证码
    function validateCode($phone, $code) {
        $validate_code = DBPREFIX.'verification_code';
        $sql = "SELECT `addtime` FROM `{$validate_code}` WHERE `s_phone` = :phone AND `s_code` = :code";
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('phone', $phone);
        $stmt->bindParam('code', $code);
        $stmt->execute();
        $phone_code = $stmt->fetch(PDO::FETCH_ASSOC);
        if(empty($phone_code)) {
            return 103;
        } else {
            // 验证是否过期
            $addtime = $phone_code['addtime'];
            if($addtime + 180 < time()) {
                return 106; // 过期
            } else {
                return 200;
            }
        }
    }
?>
