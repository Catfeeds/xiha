<?php 

    /**
     * 登录接口(验证码登录)
     * @param $user_phone int 手机号码
     * @param $pass string 密码
     * @param $type int 类型 2-学员
     * @return string AES对称加密（加密字段xhxueche）
     * @author chenxi
     **/

    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    require 'include/phpqrcode/qrlib.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->post('/','_login');
    $app->run();

    // 登录验证
    function _login() {
        $data = array('code'=>-1, 'data'=>'请升级为最新版本');
        echo json_encode($data);exit;
        Global $app, $crypt;
        $request = $app->request();
        $type = $request->params('type');
        $user_phone = $request->params('user_phone');
        $validate_code = $request->params('code');
        $user_password = md5($request->params('pass'));

        try {
            $db = getConnection();

            if($type == 1) {
            // 验证码登陆
                if(trim($user_phone) == '' && trim($validate_code) == '') {
                    $data = array('code'=>-1, 'data'=>'请填写登陆信息');
                    echo json_encode($data);
                    exit();
                }

                // 验证验证码是否获取

                // 首先检测是否存在这个账号
                $sql = "SELECT * FROM `cs_user` as u LEFT JOIN `cs_users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`s_phone` = '".$user_phone."' AND u.`i_user_type` = 0 AND u.`i_status` = 0 ";
                // i_status = 0-正常用户 2-已删除的用户
                $stmt = $db->query($sql);
                $userinfo = $stmt->fetch(PDO::FETCH_ASSOC);

                if(empty($userinfo)) {
                    $data = array('code'=>-3,'data'=>'账号不存在');
                    echo json_encode($data);
                    exit();
                }
                // if(empty($userinfo)) {
                //     // 新增账号
                //     $sql = "INSERT INTO `cs_user`(`s_phone`, `i_status`, `i_user_type`) VALUES ('".$user_phone."', 0, 0)";
                //     $stmt = $db->query($sql);
                //     if(!$stmt) {
                //         $data = array('code'=>-5, 'data'=>'新建用户错误');
                //         echo json_encode($data);
                //         exit();
                //     }
                //     $uid = $db->lastInsertId();
                //     $sql = "SELECT * FROM `cs_user` WHERE `l_user_id` = $uid AND `i_user_type` = 0";
                //     $stmt = $db->query($sql);
                //     $userinfo = $stmt->fetch(PDO::FETCH_ASSOC);
                // }

                $sql = "SELECT * FROM `cs_verification_code` WHERE `s_phone` = '".$user_phone."' AND `s_code` = '".$validate_code."'";
                $stmt = $db->query($sql);
                $user_code_info = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($user_code_info) {

                    // 检测验证码是否过期
                    if(time() - $user_code_info['addtime'] > 24*3600) {
                        $data = array('code'=>-4, 'data'=>'验证码过期,请重新获取');
                        echo json_encode($data);
                        exit();
                    }
                    
                    // 更新登录次数信息
                    $sql = "SELECT `s_username`,`is_first` FROM `cs_user` WHERE `s_phone` = ".$user_phone;
                    $stmt = $db->query($sql);
                    $row = $stmt->fetchObject();
                    if($row->s_username != "" && $row->is_first == 0) {
                        $sql = "UPDATE `cs_user` SET `is_first` = 1 WHERE `s_phone` = ".$user_phone;
                        $res = $db->query($sql);
                    }
                    $photo_arr = array(
                        0=>'1.png',
                        1=>"1.png",
                        2=>"2.png",
                        3=>"3.png",
                        4=>"4.png",
                        5=>"5.png",
                        6=>"6.png",
                        7=>"7.png",
                        8=>"8.png",
                        9=>"9.png",
                        10=>"10.png",
                        11=>"11.png",
                        12=>"12.png",
                        13=>"13.png",
                        14=>"14.png",
                        15=>"15.png",
                        16=>"16.png"
                    );
                    $userinfo['photo_id'] = $userinfo['photo_id'] == null ? 0 : $userinfo['photo_id'];
                    $userinfo['photo_url'] = $photo_arr[$userinfo['photo_id']];
                    $userinfo['user_photo'] = $userinfo['user_photo'] == null ? '' : HOST.$userinfo['user_photo'];

                    $loginauth = $userinfo['l_user_id'].'\t'.$userinfo['s_username'].'\t'.$userinfo['s_real_name'].'\t'.$userinfo['s_phone'];
                    $_SESSION['loginauth'] = $crypt->encrypt($loginauth);

                    $arr = array(
                        's_phone'       =>$userinfo['s_phone'], 
                        'l_user_id'     =>$userinfo['l_user_id'], 
                        's_username'    =>$userinfo['s_username'], 
                        's_real_name'   =>$userinfo['s_real_name'], 
                        'photo_id'      =>$userinfo['photo_id'], 
                        'photo_url'     =>$userinfo['photo_url'], 
                        'user_photo'    =>$userinfo['user_photo'],
                    );
                    //qrcode
                    $_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'studentqrcode'.DIRECTORY_SEPARATOR;
                    if (!file_exists($_path)) {
                        mkdir($_path, 0777, true);
                    }
                    $filename = $_path.DIRECTORY_SEPARATOR.'qrcode_'.$user_phone.'.png';
                    $content_arr = array(
                        'XHUSER',
                        $userinfo['s_phone'],
                        $userinfo['identity_id'],
                        $userinfo['l_user_id'],
                        $userinfo['s_real_name'],
                        $userinfo['photo_id'],
                        rand(),
                    );
                    $content = implode(',', $content_arr);
                    $arr['qrcode_url'] = HOST_URL.'/upload/studentqrcode/qrcode_'.$user_phone.'.png';
                    $errorCorrectionLevel = 'M';
                    $matrixPointSize = 6;
                    QRcode::png($content, $filename, $errorCorrectionLevel, $matrixPointSize);
                    //qrcode

                    $data = array('code'=>200, 'data'=>$arr);

                } else {
                    $data = array('code'=>-2, 'data'=>'验证码错误');
                }
                $db = null;
                echo json_encode($data);
                exit();

            } else if($type == 2) {
            // 密码登陆

                if(trim($user_phone) == '' && trim($user_password) == '') {
                    $data = array('code'=>-6, 'data'=>'请填写登陆信息');
                    echo json_encode($data);
                    exit();
                }

                $sql = "SELECT * FROM `cs_user` as u LEFT JOIN `cs_users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`i_status` = 0 AND u.`i_user_type` = 0 AND u.`s_phone` = '".$user_phone."' ";
                $stmt = $db->query($sql);
                $userinfo = $stmt->fetch(PDO::FETCH_ASSOC);
                if(!$userinfo) {
                    $data = array('code'=>103, 'data'=>'用户不存在');
                } elseif ($userinfo['s_password'] != $user_password) {
                    $data = array('code'=>102, 'data'=>'密码错误');
                } else {
                    $photo_arr = array(
                        0=>'1.png',
                        1=>"1.png",
                        2=>"2.png",
                        3=>"3.png",
                        4=>"4.png",
                        5=>"5.png",
                        6=>"6.png",
                        7=>"7.png",
                        8=>"8.png",
                        9=>"9.png",
                        10=>"10.png",
                        11=>"11.png",
                        12=>"12.png",
                        13=>"13.png",
                        14=>"14.png",
                        15=>"15.png",
                        16=>"16.png"
                    );
                    $userinfo['photo_id'] = $userinfo['photo_id'] == null ? 0 : $userinfo['photo_id'];
                    $userinfo['photo_url'] = $photo_arr[$userinfo['photo_id']];
                    $userinfo['user_photo'] = $userinfo['user_photo'] == null ? '' : HOST.$userinfo['user_photo'];
                    
                    $arr = array(
                        's_phone'       =>$userinfo['s_phone'], 
                        'l_user_id'     =>$userinfo['l_user_id'], 
                        's_username'    =>$userinfo['s_username'], 
                        's_real_name'   =>$userinfo['s_real_name'], 
                        'photo_id'      =>$userinfo['photo_id'], 
                        'photo_url'     =>$userinfo['photo_url'], 
                        'user_photo'    =>$userinfo['user_photo'],
                    );
                    //qrcode
                    $_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'studentqrcode'.DIRECTORY_SEPARATOR;
                    if (!file_exists($_path)) {
                        mkdir($_path, 0777, true);
                    }
                    $filename = $_path.DIRECTORY_SEPARATOR.'qrcode_'.$user_phone.'.png';
                    $content_arr = array(
                        'XHUSER',
                        $userinfo['s_phone'],
                        $userinfo['identity_id'],
                        $userinfo['l_user_id'],
                        $userinfo['s_real_name'],
                        $userinfo['photo_id'],
                        rand(),
                    );
                    $content = implode(',', $content_arr);
                    $arr['qrcode_url'] = HOST_URL.'/upload/studentqrcode/qrcode_'.$user_phone.'.png';
                    $errorCorrectionLevel = 'M';
                    $matrixPointSize = 6;
                    QRcode::png($content, $filename, $errorCorrectionLevel, $matrixPointSize);
                    //qrcode

                    $data = array('code'=>200, 'data'=>$arr);
                    $loginauth = $userinfo['l_user_id'].'\t'.$userinfo['s_username'].'\t'.$userinfo['s_real_name'].'\t'.$userinfo['s_phone'];
                    $_SESSION['loginauth'] = $crypt->encrypt($loginauth);
                }
                $db = null;
                echo json_encode($data);
                exit();
            }

        } catch (PDOException $e) {
            setapilog('_login:params[type:'.$type.',user_phone:'.$user_phone.',code:'.$validate_code.',pass:'.$user_password.'], error:'.$e->getMessage());
            $data = array('code'=>1, 'data'=>'网络错误');
            echo json_encode($data);
            exit;
        }
    }

?>
