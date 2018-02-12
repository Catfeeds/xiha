<?php  
    /**
     * 教练登录 (包含二维码地址，二维码分享出去的链接地址{web URL}，客服电话，教练基本信息)
     * @param $coach_phone 教练手机
     * @param $coach_pass 教练密码
     * @return json
     * @author cx
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
    $app->any('/','login');
    $app->run();

    function login() {
        Global $app, $crypt;
        $request = $app->request();
        if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[login] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            exit(json_encode($data));
        }
        //  获取请求参数并判断合法性
        $validate_result = validate(
            array(
                'coach_phone'      =>'STRING', 
                'coach_pass'      =>'STRING', 
            ), $request->params());

        if (!$validate_result['pass']) {
            exit(json_encode($validate_result['data']));
        }
        $p = $request->params();
        try {
            $db = getConnection();
            $coach_phone = $p['coach_phone'];
            $coach_pass = md5($p['coach_pass']);
            $user = DBPREFIX."user";
            $coach = DBPREFIX."coach";

            $sql = "SELECT u.`s_password`,  c.`l_coach_id` AS coach_id, c.`certification_status` FROM `{$user}` AS u LEFT JOIN `{$coach}` AS c ON u.`l_user_id` = c.`user_id` WHERE u.`s_phone` = :coach_phone AND u.`i_user_type` = 1 AND u.`i_status` = 0";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('coach_phone', $coach_phone);
            $stmt->execute();
            $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($user_info) || intval($user_info['coach_id']) <= 0) {
                $data = array('code'=>103, 'data'=>'您还未注册教练');
                exit(json_encode($data));
            } elseif ($user_info['s_password'] != $coach_pass) {
                $data = array('code'=>102, 'data'=>'手机或密码不正确');
                exit(json_encode($data));
            }

            
            $coach = DBPREFIX.'coach';
            $sql = "SELECT `s_coach_name`,";
            $sql .= " `l_coach_id`,";
            $sql .= " `user_id`,";
            $sql .= " `s_teach_age`,";
            $sql .= " `s_coach_sex`,";
            $sql .= " `s_coach_imgurl`,";
            $sql .= " `s_coach_qrcode`,";
            $sql .= " `s_coach_share_url`,";
            $sql .= " `user_id`, ";
            $sql .= " `is_first`, ";
            $sql .= " `s_coach_phone`,";
            $sql .= " `s_school_name_id`,";
            $sql .= " `s_coach_lesson_id`,";
            $sql .= " `s_coach_lisence_id`";
            $sql .= " FROM `{$coach}`";
            $sql .= " WHERE `l_coach_id` = :cid";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid', $user_info['coach_id']);
            //$stmt->bindParam('cphone', $coach_phone);
            $stmt->execute();
            $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($coach_info)) {
                $data = array('code'=>103, 'data'=>'您还没注册教练');
                exit(json_encode($data));
            }
            $coach_id = $coach_info['l_coach_id'];
            if(file_exists(__DIR__.'/../../../sadmin/'.$coach_info['s_coach_imgurl'])) {
                $coach_info['s_coach_imgurl'] = S_HTTP_HOST.$coach_info['s_coach_imgurl'];
            } elseif(file_exists(__DIR__.'/../../../admin/'.$coach_info['s_coach_imgurl'])) {
                $coach_info['s_coach_imgurl'] = HTTP_HOST.$coach_info['s_coach_imgurl'];
            } else {
                $coach_info['s_coach_imgurl'] = '';
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
            $coach_info['certification_status'] = isset($user_info['certification_status']) ? (int)$user_info['certification_status']: 1; // 1 表示未认证

            // 客服电话
            $coach_info['service_phone'] = '0551-65610256';
            $is_first = $coach_info['is_first'];
            if ($is_first == 0) {
                $sql = " UPDATE {$coach} SET `is_first` = 1 WHERE `s_coach_phone` = {$coach_phone} ";
                $update_first = $db->query($sql);
            } 
            $db = null;
            $data = array('code'=>200, 'data'=>$coach_info);    
            exit(json_encode($data));
        } catch(PDOException $e) {
            setapilog('[login] [:error] [client ' . $request->getIp() . '] [params ' . serialize($request->params()) . '] ['.$e->getLine() . ' ' . $e->getMessage() . ']');
            $data = array('code'=>1, 'data'=>'网络错误');
            exit(json_encode($data));
        }
    }

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
