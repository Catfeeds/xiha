<?php

/**
 * 获取验证码
 * @param $phone    int 手机号
 * @param $type     int 验证码类型  (0: 学员端  1: 教练端)
 * @return string AES对称加密（加密字段xhxueche）
 * @author chenxi
 **/

require 'Slim/Slim.php';
require 'include/common.php';
require 'include/crypt.php';
require 'include/functions.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
// $app->get('/phone/:phone/type/:type','verificationCode');
$app->any('/', 'verificationCode');

// 随机生成6位验证码并保存到数据库
function verificationCode() {
    Global $crypt, $app;
    $rand_num = rand(100000, 999999);
    $db = getConnection();

    $req = $app->request();
    $res = $app->response();

    //验证请求方式 POST
    if ( !$req->isGet() ) {
        slimLog($req, $res, null, '需要POST');
        ajaxReturn(array('code' => 106, 'data' => '请求错误'));
    }

    //取得参数列表
    $validate_ok = validate(
        array(
            'phone'     => 'INT',
            'type'      => 'INT',
            'timestamp' => 'INT',
            'sign'      => 'STRING',
        ),
        $req->params()
    );
    if ( !$validate_ok['pass'] ) {
        slimLog($req, $res, null, '参数不完整或参数类型不对，请核对文档与您传的参数');
        ajaxReturn(array('code' => 400, 'data' => '请升级新版学员端'));
    }
    $phone  = $req->params('phone');
    $type   = $req->params('type');

    // 请求在一定时间过后失效
    if ($req->params('timestamp') < time() - 300) {
        slimLog($req, $res, null, '请求已过期');
        ajaxReturn(array('code' => 400, 'data' => '参数错误'));
    }

    // 验证参数签名
    if ($req->params('sign') != sign($req->params(), XIHA_API_KEY)) {
        slimLog($req, $res, null, '签名错误');
        ajaxReturn(array('code' => 400, 'data' => '请升级新版学员端'));
    }

    // 查询手机号是否已经注册
    $sql = "SELECT * FROM `cs_user` WHERE `s_phone` = '".$phone."' AND `i_user_type` = $type AND `i_status` = 0 "; // i_status = 0-正常 2-已删除
    $stmt = $db->query($sql);
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);

    // 查询是否有生成的验证码
    $sql = "SELECT * FROM `cs_verification_code` WHERE `s_phone` = :phone";
    $stmt = $db->prepare($sql);
    $stmt->bindParam('phone', $phone);
    $stmt->execute();
    $code = $stmt->fetchObject();

    // 存在当前手机号验证码
    if($code) {
        $sql = "UPDATE `cs_verification_code` SET `s_code` = :code, `addtime` = '".time()."' WHERE `s_phone` = :phone";
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('code', $rand_num);
        $stmt->bindParam('phone', $phone);
        $stmt->execute();

        $res = sendCode($phone,$rand_num, $type);
        if($res == 100) {
            if(empty($user_info)) {
                $data = array('code'=>200, 'data'=>$rand_num, 'is_login'=>0);
            } else {
                $data = array('code'=>200, 'data'=>$rand_num, 'is_login'=>1);
            }
        } else if($res == 101) {
            $data = array('code'=>101,'data'=>'验证失败，请重新发送','is_login'=>0);
        } else if($res == 102) {
            $data = array('code'=>102,'data'=>'短信不足，请重新发送','is_login'=>0);
        } else if($res == 103) {
            $data = array('code'=>103,'data'=>'操作失败，请重新发送','is_login'=>0);
        } else if($res == 104) {
            $data = array('code'=>104,'data'=>'非法字符，请重新发送','is_login'=>0);
        } else if($res == 105) {
            $data = array('code'=>105,'data'=>'内容过多，请重新发送','is_login'=>0);
        } else if($res == 106) {
            $data = array('code'=>106,'data'=>'号码过多，请重新发送','is_login'=>0);
        } else if($res == 107) {
            $data = array('code'=>107,'data'=>'频率过快，请稍后获取','is_login'=>0);
        } else if($res == 108) {
            $data = array('code'=>108,'data'=>'号码内容为空，请重新发送','is_login'=>0);
        } else if($res == 109) {
            $data = array('code'=>109,'data'=>'账号已冻结','is_login'=>0);
        } else if($res == 110) {
            $data = array('code'=>110,'data'=>'请勿频繁单条发送','is_login'=>0);
        } else if($res == 112) {
            $data = array('code'=>112,'data'=>'号码不正确，请重新发送','is_login'=>0);
        } else if($res == 120) {
            $data = array('code'=>120,'data'=>'系统升级，请勿重新发送','is_login'=>0);
        } else {
            $data = array('code'=>2,'data'=>'验证码发送失败', 'is_login'=>0);
        }
        echo json_encode($data);

    } else {

        // 不存在手机验证码

        $sql = "INSERT INTO `cs_verification_code` (`s_phone`, `s_code`, `addtime`) VALUES (:phone, :code, '".time()."')";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('phone', $phone);
            $stmt->bindParam('code', $rand_num);
            $stmt->execute();
            $id = $db->lastInsertId();
            $db = null;
            if($id) {
                // 发送验证码
                $res = sendCode($phone,$rand_num, $type);

                if($res == 100) {
                    if(empty($user_info)) {
                        $data = array('code'=>200, 'data'=>$rand_num, 'is_login'=>0);
                    } else {
                        $data = array('code'=>200, 'data'=>$rand_num, 'is_login'=>1);
                    }
                } else if($res == 101) {
                    $data = array('code'=>101,'data'=>'验证失败，请重新发送','is_login'=>0);
                } else if($res == 102) {
                    $data = array('code'=>102,'data'=>'短信不足，请重新发送','is_login'=>0);
                } else if($res == 103) {
                    $data = array('code'=>103,'data'=>'操作失败，请重新发送','is_login'=>0);
                } else if($res == 104) {
                    $data = array('code'=>104,'data'=>'非法字符，请重新发送','is_login'=>0);
                } else if($res == 105) {
                    $data = array('code'=>105,'data'=>'内容过多，请重新发送','is_login'=>0);
                } else if($res == 106) {
                    $data = array('code'=>106,'data'=>'号码过多，请重新发送','is_login'=>0);
                } else if($res == 107) {
                    $data = array('code'=>107,'data'=>'频率过快，请稍后获取','is_login'=>0);
                } else if($res == 108) {
                    $data = array('code'=>108,'data'=>'号码内容为空，请重新发送','is_login'=>0);
                } else if($res == 109) {
                    $data = array('code'=>109,'data'=>'账号已冻结','is_login'=>0);
                } else if($res == 110) {
                    $data = array('code'=>110,'data'=>'请勿频繁单条发送','is_login'=>0);
                } else if($res == 112) {
                    $data = array('code'=>112,'data'=>'号码不正确，请重新发送','is_login'=>0);
                } else if($res == 120) {
                    $data = array('code'=>120,'data'=>'系统升级，请勿重新发送','is_login'=>0);
                }
            } else {
                $data = array('code'=>3, 'data'=>'获取验证码失败','is_login'=>0);
            }
            echo json_encode($data);
            // echo $crypt->encrypt(json_encode($data));

        } catch(PDOException $e) {
            // $data = array('code'=>1, 'data'=>$e->getMessage());
            setapilog('_get_verification_code:params[phone:'.$phone.',type:'.$type.'], error:'.$e->getMessage());
            $data = array('code'=>1, 'data'=>'网络错误');
            echo json_encode($data);
            // echo $crypt->encrypt(json_encode($data));
        }
    }

}

// 发送验证码
function sendCode($phone, $code, $type) {
    $http = 'http://api.sms.cn/mtutf8/'; //短信接口
    $uid = 'guohuaguangdian'; //用户账号
    $pwd = 'bbxycx552331'; //密码
    $mobile = $phone; //号码，以英文逗号隔开
    $mobileids = ''; //号码唯一编号
    // $content = "尊敬的教练，已向您预约的学员chenxi，7月30日15时的订单已取消，订单号为2432342342342,请您及时调整您的工作【嘻哈学车】";
    // $content = "尊敬的客户，您预约的八一驾校陈曦教练8月03日14时的订单已取消，请您及时重新预约，以免耽误您的学车【嘻哈学车】";
    if ($type == 0) {
        // $content = "尊敬的客户，欢迎您使用嘻哈学车！您的验证码是".$code."，请在3分钟内输入。【嘻哈学车】";
        $content = "【嘻哈学车】学员端手机验证码是".$code.'。';
    } elseif ($type == 1) {
        $content = "【嘻哈学车】教练端手机验证码是".$code.'。';
    }

    //即时发送
    $res = sendSMS($http,$uid,$pwd,$mobile,$content,$mobileids);
    return $res;
}

function sendSMS($http,$uid,$pwd,$mobile,$content,$mobileids,$time='',$mid='')
{
    $data = array
        (
            'uid'=>$uid, //用户账号
            'pwd'=>md5($pwd.$uid), //MD5位32密码,密码和用户名拼接字符
            'mobile'=>$mobile, //号码
            'content'=>$content, //内容
            'mobileids'=>$mobileids,
            'time'=>$time, //定时发送
        );
    $re= postSMS($http,$data); //POST方式提交
    if( strstr($re,'stat=100'))
    {
        return 100;
    }
    else if( strstr($re,'stat=101'))
    {
        return 101;
    }
    else if( strstr($re,'stat=101'))
    {
        return 101;
    }
    else if( strstr($re,'stat=102'))
    {
        return 102;
    }
    else if( strstr($re,'stat=103'))
    {
        return 103;
    }
    else if( strstr($re,'stat=104'))
    {
        return 104;
    }
    else if( strstr($re,'stat=105'))
    {
        return 105;
    }

    else if( strstr($re,'stat=106'))
    {
        return 106;
    }

    else if( strstr($re,'stat=107'))
    {
        return 107;
    }

    else if( strstr($re,'stat=108'))
    {
        return 108;
    }

    else if( strstr($re,'stat=109'))
    {
        return 109;
    }

    else if( strstr($re,'stat=110'))
    {
        return 110;
    }
    else if( strstr($re,'stat=112'))
    {
        return 112;
    }
    else if( strstr($re,'stat=120'))
    {
        return 120;
    }

}

function postSMS($url,$data='')
{
    $port="";
    $post="";
    $row = parse_url($url);
    $host = $row['host'];
    $port = isset($row['port']) ? $row['port']:80;
    $file = $row['path'];
    while (list($k,$v) = each($data))
    {
        $post .= rawurlencode($k)."=".rawurlencode($v)."&"; //转URL标准码
    }
    $post = substr( $post , 0 , -1 );
    $len = strlen($post);
    $fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
    if (!$fp) {
        return "$errstr ($errno)\n";
    } else {
        $receive = '';
        $out = "POST $file HTTP/1.1\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Content-type: application/x-www-form-urlencoded\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Content-Length: $len\r\n\r\n";
        $out .= $post;
        fwrite($fp, $out);
        while (!feof($fp)) {
            $receive .= fgets($fp, 128);
        }
        fclose($fp);
        $receive = explode("\r\n\r\n",$receive);
        unset($receive[0]);
        return implode("",$receive);
    }
}

$app->run();
?>
