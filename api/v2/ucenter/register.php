<?php
/**
 * 注册接口(修改添加关联驾校的注册)
 * @param $phone int 手机号码
 * @param $pass string 密码
 * @param $code int 验证码
 * @return string AES对称加密（加密字段xhxueche）
 * @author chenxi
 **/

require '../../Slim/Slim.php';
require '../../include/common.php';
require '../../include/crypt.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
$app->post('/','register');

function register() {
    Global $app, $crypt;
    $request = $app->request();
    $phone = $request->params('phone');
    $pass = $request->params('pass');
    $code = $request->params('code');
    $sid = $request->params('sid');

    // 验证码不能为空
    if(empty($code)) {
        $data = array('code'=>-1, 'data'=>'请输入验证码');
        echo json_encode($data);
        return;
    }

    // 手机号 密码不能为空
    if(empty($phone) || empty($pass)) {
        $data = array('code'=>-2, 'data'=>'请完善注册信息');
        echo json_encode($data);
        return;
    }

    // 手机号已被注册

    $phone_check = getRegistered($phone);
    if ($phone_check) {
        if($phone_check === 1) {
            $data = array('code'=>-6, 'data'=>'手机号已被注册为学员，可直接登陆');
            echo json_encode($data);
            exit();
        } else {
            $data = array('code'=>-7, 'data'=>'手机号已被注册为教练，可直接登陆');
            echo json_encode($data);
            exit();
        }
    }

    // 验证验证码是否正确
    $codeinfo = getCode($phone);
    if($codeinfo) {
        if($codeinfo->s_code != $code) {
            $data = array('code'=>-4, 'data'=>'验证码错误');
            echo json_encode($data);
            return;
        }
    } else {
        $data = array('code'=>-5, 'data'=>'请获取验证码');
        echo json_encode($data);
        return;
    }
    $pass = md5($pass);
    $username = '嘻哈用户'.substr($phone, -4, 4);
    $sql = "INSERT INTO `cs_user` (`s_phone`, `s_password`, `s_username`, `i_user_type`, `i_status`, `s_real_name`, `l_yw_incode`, `i_from`, `is_first`, `s_imgurl`, `content`, `coach_id`) ";
    $sql .= "VALUES ('{$phone}', '{$pass}', '{$username}', 0, 0, '{$username}', 0, 1, 0, '', '欢迎来到嘻哈学车', 0)";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $id = $db->lastInsertId();
        if($id) {
            // users_info插入数据
            $sql = "SELECT `id` FROM `cs_users_info` WHERE `user_id` = '{$id}'";
            $stmt = $db->query($sql);
            $users_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if($users_info) {
                $sql = "UPDATE `cs_users_info` SET `school_id` = '{$sid}' WHERE `user_id` = '{$id}'";
                $res = $db->query($sql);
            } else {
                $sql = "INSERT INTO `cs_users_info` (`x`, `y`, `user_id`, `sex`, `age`, `identity_id`, `address`, `user_photo`, `license_num`, `school_id`, `lesson_name`, `province_id`, `city_id`, `area_id`, `photo_id`, `learncar_status`, `addtime`) VALUES (";
                $nowtime = time();
                $sql .= "0, 0, '{$id}', 1, 18, '', '', '', 0, '{$sid}', '', 0, 0, 0, 1, '', {$nowtime})";
                $res = $db->query($sql);
            }
            $data = array('code'=>200,'data'=>array('id'=>$id,'phone'=>$phone));
        } else {
            $data = array('code'=>2,'data'=>'注册失败');
        }
        $db = null;
        echo json_encode($data);

    } catch(PDOException $e) {
        setapilog('register:params[phone:'.$phone.',pass:'.$pass.',code:'.$code.'], error:'.$e->getMessage());
        $data = array('code'=>1, 'data'=>'网络错误');

        echo json_encode($data);
    }
}

// 获取验证码
function getCode($phone) {
    $sql = "SELECT * FROM `cs_verification_code` WHERE `s_phone` = :phone";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('phone', $phone);
        $stmt->execute();
        $phone_code = $stmt->fetchObject();
        return $phone_code;
    } catch(PDOException $e) {
        $data = -1;
        return $data;
    }
}

// 获取手机号是否被注册
function getRegistered($phone) {
    $sql = "SELECT `l_user_id`, `i_user_type` FROM `cs_user` WHERE `s_phone` = :phone";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('phone', $phone);
        $stmt->execute();
        $res = $stmt->fetchObject();
        if($res) {
            $ajax = $res->i_user_type == 0 ? 1 : 2;  // 0(1)：学员 1(2)：教练
            return $ajax;
        } else {
            return false;
        }
    } catch(PDOException $e) {
        return false;
    }
}

$app->run();
?>
