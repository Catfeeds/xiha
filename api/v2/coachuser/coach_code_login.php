<?php
    /**
    * 教练注册之手机加验证码登陆
    * @param    string  $coach_phone    用户的（教练）手机号码
    * @param    int     $code           短信验证码
    * @return   json
    * @package  api/v2/coachuser
    * @author   wl
    * @date     July 12, 2016
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.class.php';
    require '../../include/functions.php';
    require '../../include/phpqrcode/qrlib.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    // $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $crypt = new Encryption;
    $app->any('/', 'coachLogin');
    $app->run();

    function coachLogin() {
        global $app, $crypt;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res,null,'未使用POST请求');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(
            array(
                'coach_phone'   => 'STRING',
                'code'          => 'INT',
            ), $req->params());

        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res,null,'参数不正确');
            ajaxReturn($validate_ok['data']);
        }

        $p = $req->params();
        $s_phone    = $p['coach_phone'];
        $s_code     = $p['code'];

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db     = getConnection();
            $code   = DBPREFIX.'verification_code';
            $coach  = DBPREFIX.'coach';
            $user   = DBPREFIX.'user';

            //  获取手机获得的验证码的时间
            $sql    = " SELECT  `addtime` FROM `{$code}` WHERE `s_phone` = :phone AND `s_code` = :code ";
            $stmt   = $db->prepare($sql);
            $stmt->bindParam('phone',$s_phone);
            $stmt->bindParam('code',$s_code);
            $stmt->execute();
            $code_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($code_info)) {
                $data   = array('code' => 400, 'data' => '验证码错误！');
                $db     = null;
                ajaxReturn($data);
            } 
            // 判断验证码是否查过30分钟
            $addtime    = (int)$code_info['addtime'];
            if ($addtime + 30 * 60 < time()) {
                $data   = array('code' => 400, 'data' => '验证码过期，请重新获取验证码');
                $db     = null;
                ajaxReturn($data);
            } 

            // 根据s_phone和i_user_type判断在user表中是否存在此教练
            $sql = "SELECT `l_user_id`,`i_user_type`, `s_phone` FROM `{$user}` WHERE `s_phone` = :coach_phone AND `i_user_type` = 1 AND `i_status` = 0 ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('coach_phone', $s_phone);
            $stmt->execute();
            $user_info  = $stmt->fetch(PDO::FETCH_ASSOC);
            // user表中不存在此教练
            if (empty($user_info)) {
                $sql    = " INSERT INTO {$user} (`s_username`, `s_phone`, `i_status`, `i_user_type`) VALUES ('{$s_phone}', '{$s_phone}', 0, 1) ";
                $r      = $db->query($sql);
                if ($r) {
                    $last_user_id = $db->lastInsertId();
                    $data = array(
                        'code' => 103, 
                        'data' =>array('user_id' => $last_user_id),
                    );
                    $db     = null;
                    ajaxReturn($data);
                }
            } 

            $user_id = $user_info['l_user_id'];
            // user表中存在此教练
            $sql = " SELECT  `s_coach_phone`, `user_id` ,`is_first` FROM {$coach} WHERE `s_coach_phone` = :coach_phone AND `user_id` = :user_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('coach_phone', $s_phone);
            $stmt->bindParam('user_id', $user_id);
            $stmt->execute();
            $coach_message  = $stmt->fetch(PDO::FETCH_ASSOC);
            // coach表中不存在此教练
            if (empty($coach_message)) {
               $data = array(
                        'code' => 103, 
                        'data' =>array('user_id' => $user_id),
                );
                $db     = null;
                ajaxReturn($data);
            } else {
                $coach      = DBPREFIX.'coach';
                $sql        = "SELECT `s_coach_name`,";
                $sql       .= " `s_teach_age`,";
                $sql       .= " `l_coach_id`,";
                $sql       .= " `s_coach_sex`,";
                $sql       .= " `s_coach_imgurl`,";
                $sql       .= " `certification_status`,";
                $sql       .= " `s_coach_qrcode`,";
                $sql       .= " `s_coach_share_url`,";
                $sql       .= " `s_coach_phone`,";
                $sql       .= " `s_school_name_id`,";
                $sql       .= " `s_coach_lesson_id`,";
                $sql       .= " `s_coach_lisence_id`,";
                $sql       .= " `user_id`,";
                $sql       .= " `is_first`";
                $sql       .= "  FROM `{$coach}` ";
                $sql       .= "  WHERE `s_coach_phone` = :coach_phone ";
                $stmt       = $db->prepare($sql);
                $stmt->bindParam('coach_phone', $s_phone);
                $stmt->execute();
                $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
                $coach_id   = $coach_info['l_coach_id'];
                

                if(file_exists(__DIR__.'/../../../sadmin/'.$coach_info['s_coach_imgurl'])) {
                    $coach_info['s_coach_imgurl'] = S_HTTP_HOST.$coach_info['s_coach_imgurl'];
                } else {
                    $coach_info['s_coach_imgurl'] = HTTP_HOST.$coach_info['s_coach_imgurl'];
                }
                $params_1 = $params_2 = $params_3 = 1;
                $token = urlencode($coach_id.'|'.$coach_info['s_coach_name'].'|'.$coach_info['s_coach_phone'].'|'.$params_1.'|'.$params_2.'|'.$params_3.'|'.rand());
                // $_token = $crypt->encrypt($token);
                $_token = $crypt->encode($token);
    
                // 生成二维码
                $_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'coachcode'.DIRECTORY_SEPARATOR;
                if(!file_exists($_path)) {
                    mkdir($_path, 0777, true);
                }
                // $filename = $_path.'/'.$coach_id.'/qrcode_'.date('Ymd', time()).uniqid().'.png';
                $filename = $_path.'/qrcode_'.$coach_id.'.png';
                $url = HOST_URL."m/coachuser/token/".urlencode($_token);

                // if(!file_exists($filename)) {
                $errorCorrectionLevel = 'M';
                $matrixPointSize = 6;
                QRcode::png($url, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
                // }
                $imgurl = HOST.'/upload/coachcode/'.basename($filename);
                $coach_info['s_coach_qrcode'] = $coach_info['s_coach_qrcode'] != ' ' ? $coach_info['s_coach_qrcode'] : $imgurl;
                $coach_info['s_coach_share_url'] = $coach_info['s_coach_share_url'] != ' ' ? $coach_info['s_coach_share_url'] : $url;
                // 查找驾校
                $school = DBPREFIX.'school';
                $sql = "SELECT `s_school_name` FROM `{$school}` WHERE `l_school_id` = :school_id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('school_id', $coach_info['s_school_name_id']);
                $stmt->execute();
                $school_info = $stmt->fetch(PDO::FETCH_ASSOC);
                $school_name = '';
                if($school_info) {
                    $school_name = $school_info['s_school_name'];
                }

                $coach_info['school_name'] = $school_name == '' ? '嘻哈驾校' : $school_name;
                $coach_info['l_coach_id'] = $coach_id;

                // 客服电话
                $coach_info['service_phone'] = '0551-65610256';
                $is_first = $coach_info['is_first'];
                if ($is_first == 0) {
                    $sql = " UPDATE {$coach} SET `is_first` = 1 WHERE `s_coach_phone` = {$s_phone} AND `user_id` = {$user_id} ";
                    $update_first = $db->query($sql);
                } 
                $db     = null;
                $data   = array('code'=>200, 'data'=>$coach_info);    
                $db     = null;
                ajaxReturn($data);
            }
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

    // 创建文件夹
    function createFile($id, $path) {
        if(!file_exists($path.'/'.$id)){ 
            if(mkdir($path.'/'.$id)) {
                return $path.'/'.$id;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
?>
