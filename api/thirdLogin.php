<?php
/**
 * 第三方登录接口
 * @param    INT            $third_type 第三方类型: 1 微信; 2 QQ
 * @param    string         $thid_key   用户的登录标识
 * @param    INT            $user_type  用户类型：0 学员; 1 教练
 * @param    INT            $with_phone 带上手机号 (1: yes|2: No)
 * @param    string         $phone      用户的手机号
 * @param    string         $code       手机验证码
 * @return   json
 * @package  /api/thirdLogin.php
 * @author   wl
 * @date     Nov 04, 2016
 **/

require 'Slim/Slim.php';
require 'include/common.php';
require 'include/crypt.class.php';
require 'include/functions.php';
require 'include/phpqrcode/qrlib.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
// $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
$crypt = new Encryption;
$app->any('/', 'thirdLogin');
$app->run();

function thirdLogin() {
    global $app, $crypt;

    $req = $app->request();
    $res = $app->response();

    //验证请求方式 POST
    if ( !$req->isPost() ) {
        slimLog($req, $res, null, '需要POST');
        ajaxReturn(array('code' => 106, 'data' => '请求错误'));
    }

    //取得参数列表
    $validate_ok = validate(
        array(
            'third_type'    => 'INT',
            'third_key'     => 'STRING',
            'user_type'     => 'INT',
            'with_phone'    => 'INT',
        ), $req->params());

    if ( !$validate_ok['pass'] ) {
        slimLog($req, $res, null, '参数不完整或参数类型不对，请核对文档与您传的参数');
        ajaxReturn($validate_ok['data']);
    }

    $p          = $req->params();
    $third_type = $p['third_type'];
    $third_key  = $p['third_key'];
    $user_type  = $p['user_type'];
    $with_phone = $p['with_phone'];

    if ( $with_phone == 1) {
        $validate_ok = validate(
            array(
                'phone' => 'STRING',
                'code'  => 'STRING',
            ), $req->params());
        if ( $validate_ok['pass'] ) {
            // phone_code check pass
            $user_phone = $p['phone'];
            $phone_code = $p['code'];
        } else {
            //需要输手机号绑定
            ajaxReturn(array('code' => 102, 'data' => '需要手机号绑定'));
        }
    }

    if (!in_array($user_type, array(0, 1))) {
        slimLog($req, $res,null,'不允许的用户类型');
        ajaxReturn(array('code' => 101, 'data' => '用户类型错误'));
    }

    if (!in_array($third_type, array(1, 2))) {
        slimLog($req, $res,null,'参数不正确');
        ajaxReturn(array('code' => 101, 'data' => '参数不正确'));
    }

    if ( ! $req->params('user_name')) {
        $user_name = '嘻哈用户'.time()%10000;
    } else {
        $user_name = trim((string)$req->params('user_name'));
    }

    //ready to return
    $data = array();
    try {
        // Open connection with mysql
        $db = getConnection();

        $third_login_tbl = DBPREFIX.'third_login';
        $user_tbl = DBPREFIX.'user';
        $users_info_tbl = DBPREFIX.'users_info';
        $coach_tbl = DBPREFIX.'coach';
        $addtime = time();
        $s_password = md5('123456');

        $sql = " SELECT login.`user_id`, user.s_phone FROM `{$user_tbl}` AS user LEFT JOIN `{$third_login_tbl}` AS login ON user.l_user_id = login.user_id AND user.i_user_type = login.i_user_type WHERE `third_key` = :key AND `third_type` = :type AND login.`i_user_type` = :user_type AND user.i_status = 0 ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('key', $third_key);
        $stmt->bindParam('type', $third_type);
        $stmt->bindParam('user_type', $user_type);
        $stmt->execute();
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!empty($user_info)) {
            // 使用第三方成功绑定过手机号
            $user_id = $user_info['user_id'];
            if ($user_type == 1) {
                $coach_tbl = DBPREFIX.'coach';
                $user_tbl = DBPREFIX.'user';
                $sql = " SELECT l_coach_id FROM {$user_tbl} AS user LEFT JOIN {$coach_tbl} AS coach ON coach.user_id = user.l_user_id WHERE coach.user_id = :uid AND user.i_status = 0 AND user.i_user_type = 1 ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('uid', $user_id);
                $stmt->execute();
                $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($coach_info) {
                    $coach_id = $coach_info['l_coach_id'];
                } else {
                    $coach_id = 0;
                }
            }
        } else {
            // 第一次使用第三方账号登陆
            // user
            if ( $with_phone != 1 or ! isset($user_phone) ) {
                ajaxReturn(array('code' => 102, 'data' => '需要与手机号绑定'));
            }

            // 检查验证码是否有效
            $ver_code_tbl = DBPREFIX.'verification_code';
            $sql = "SELECT * FROM `{$ver_code_tbl}` WHERE `s_phone` = :phone AND `s_code` = :code AND `addtime` >= :begin_time AND `addtime` <= :end_time ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('phone', $user_phone);
            $stmt->bindParam('code', $phone_code);
            $now = time();
            $begin_time = $now - 60 * 3; // 验证码有效期3分钟
            $end_time = $now;
            $stmt->bindParam('begin_time', $begin_time);
            $stmt->bindParam('end_time', $end_time);
            $stmt->execute();
            $code_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (! $code_info) {
                $data = array(
                    'code' => 102,
                    'data' => '请重新获取手机验证码',
                );
                ajaxReturn($data);
            }

            $sql = " SELECT l_user_id FROM {$user_tbl} WHERE `s_phone` = :phone AND `i_status` = 0 AND `i_user_type` = :user_type ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('phone', $user_phone);
            $stmt->bindParam('user_type', $user_type);
            $stmt->execute();
            $phone_user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($phone_user) {
                $l_user_id = $phone_user['l_user_id'];
                if ($user_type == 0) {
                    $sql = " SELECT * FROM `{$users_info_tbl}` WHERE `user_id` = :uid  ";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('uid', $l_user_id);
                    $stmt->execute();
                    $usersinfo = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (empty($usersinfo)) {
                        $sql = "INSERT INTO `{$users_info_tbl}` (`user_id`, `identity_id`, `address`, `school_id`, `sex`, `age`, `addtime`) VALUES ('{$l_user_id}', '', '', 0, 1, 0, {$addtime})";
                        $user = $db->query($sql);
                        if ($user) {
                            $user_id = $user['user_id'];
                        }
                    } else {
                        $user_id = $l_user_id;
                    }

                } elseif ($user_type == 1) {
                    $sql = " SELECT * FROM `{$coach_tbl}` WHERE `user_id` = :uid AND `s_coach_phone` = :cphone ";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('uid', $l_user_id);
                    $stmt->bindParam('cphone', $user_phone);
                    $stmt->execute();
                    $coach_message = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (empty($coach_message)) {
                        $sql = "INSERT INTO {$coach_tbl} (`s_coach_phone`, `user_id`, `s_coach_name`, `s_teach_age`, `s_coach_sex`, `s_school_name_id`, `s_coach_address`, `order_receive_status`, `addtime`) VALUES ('{$user_phone}', '{$l_user_id}', '{$user_name}', 0, 1, 0, '', 0, '{$addtime}') ";
                        $user = $db->query($sql);
                        if ($user) {
                            $coach_id = $db->lastInsertId();
                            $user_id = $l_user_id;
                        }
                    } else {
                        $coach_id = $coach_message['l_coach_id'];
                        $user_id = $coach_message['user_id'];
                    }
                }
            } else {
                $sql = " INSERT INTO {$user_tbl} (`s_phone`, `s_username`, `s_password`, `i_user_type`, `i_status`, `s_real_name`, `content`) VALUES ('{$user_phone}', '{$user_name}', '{$s_password}', '{$user_type}', 0, '{$user_name}', '使用第三方登陆成为嘻哈学车用户' )  ";
                $r  = $db->query($sql);
                // usersinfo | coach
                $user_id = $db->lastInsertId();
                if ($user_type == 0) {
                    $sql = "INSERT INTO `{$users_info_tbl}` (`user_id`, `identity_id`, `address`, `school_id`, `sex`, `age`, `addtime`) VALUES ('{$user_id}', '', '', 0, 1, 22, {$addtime})";
                    $user = $db->query($sql);
                } else if ($user_type == 1) {
                    $sql = "INSERT INTO {$coach_tbl} (`s_coach_phone`, `user_id`, `s_coach_name`, `s_teach_age`, `s_coach_sex`, `s_school_name_id`, `s_coach_address`, `order_receive_status`, `addtime`) VALUES ('{$user_phone}', '{$user_id}', '{$user_name}', 0, 1, 0, '', 0, '{$addtime}') ";
                    $user = $db->query($sql);
                    if ($user) {
                        $coach_id = $db->lastInsertId();
                    }
                }
            }
            // third_login
            $sql = " INSERT INTO {$third_login_tbl} (`third_key`, `third_type`, `user_id`, `i_user_type`, `add_time`) VALUES ('{$third_key}', '{$third_type}', '{$user_id}', '{$user_type}', '{$addtime}') ";
            $db->query($sql);
        }

        // 返回登陆成功后的信息
        if ($user_type == 0) {
            $sql = " SELECT * FROM `{$user_tbl}` as u LEFT JOIN `{$users_info_tbl}` as uf ON uf.`user_id` = u.`l_user_id` WHERE u.`l_user_id` = :uid AND `i_status` = 0 AND `i_user_type` = :user_type ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uid', $user_id);
            $stmt->bindParam('user_type', $user_type);
            $stmt->execute();
            $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
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
            $user_info['photo_id'] = $user_info['photo_id'] == null ? 0 : $user_info['photo_id'];
            $user_info['photo_url'] = $photo_arr[$user_info['photo_id']];
            $user_info['user_photo'] = $user_info['user_photo'] == null ? '' : HOST.$user_info['user_photo'];

            $user_info_arr = array(
                's_phone'       =>$user_info['s_phone'],
                'l_user_id'     =>$user_info['l_user_id'],
                's_username'    =>$user_info['s_username'],
                's_real_name'   =>$user_info['s_real_name'],
                'photo_id'      =>$user_info['photo_id'],
                'photo_url'     =>$user_info['photo_url'],
                'user_photo'    =>$user_info['user_photo'],
            );
            $is_first = $user_info['is_first'];
            if ($is_first == 0) {
                $sql = " UPDATE {$user_tbl} SET `is_first` = 1 WHERE `l_user_id` = {$user_id} ";
                $update_first = $db->query($sql);
            }

            $db     = null;
            $data   = array('code'=>200, 'data'=>$user_info_arr);
            $db     = null;
            ajaxReturn($data);

        } elseif ($user_type == 1) {
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
            $sql       .= "  FROM `{$coach_tbl}` ";
            // $sql       .= "  WHERE `s_coach_phone` = :coach_phone AND `l_coach_id` = :cid";
            $sql       .= "  WHERE `l_coach_id` = :cid";
            $stmt       = $db->prepare($sql);
            // $stmt->bindParam('coach_phone', $user_phone);
            $stmt->bindParam('cid', $coach_id);
            $stmt->execute();
            $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (file_exists(__DIR__.'/../../../sadmin/'.$coach_info['s_coach_imgurl'])) {
                $coach_info['s_coach_imgurl'] = S_HTTP_HOST.$coach_info['s_coach_imgurl'];
            } elseif (file_exists(__DIR__.'/../../../admin/'.$coach_info['s_coach_imgurl'])) {
                $coach_info['s_coach_imgurl'] = HTTP_HOST.$coach_info['s_coach_imgurl'];
            } else {
                $coach_info['s_coach_imgurl'] = '';
            }
            $params_1 = $params_2 = $params_3 = 1;
            $token = urlencode($coach_id.'|'.$coach_info['s_coach_name'].'|'.$coach_info['s_coach_phone'].'|'.$params_1.'|'.$params_2.'|'.$params_3.'|'.rand());
            // $_token = $crypt->encrypt($token);
            $_token = $crypt->encode($token);

            // 生成二维码
            $_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'coachcode'.DIRECTORY_SEPARATOR;
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
                $sql = " UPDATE {$coach_tbl} SET `is_first` = 1 WHERE ";
                if (isset($user_phone)) {
                    $sql .= " `s_coach_phone` = {$user_phone} AND ";
                }
                $sql .= " `user_id` = {$user_id} ";
                $update_first = $db->query($sql);
            }
            $db     = null;
            $data   = array('code'=>200, 'data'=>$coach_info);
            ajaxReturn($data);
        }
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
