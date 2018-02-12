<?php
/**
 * 教练端完善信息
 * @param    int        $school_id  驾校id
 * @param    int        $user_id    用户id
 * @param    string     $phone      用户手机号码
 * @param    string     $user_name  用户姓名
 * @param    string     $user_pass  用户密码
 * @param    string     $lesson_id  科目id 2,3
 * @param    string     $license_id 牌照id 2,3,4
 * @return   json
 * @package  api/v2/coachuser
 * @author   wl
 * @date     July 15, 2016
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
$app->any('/', 'completeCoachInfo');
$app->run();

function completeCoachInfo() {
    global $app, $crypt;

    //验证请求方式 POST
    $req = $app->request();
    $res = $app->response();

    if ( !$req->isPost() ) {
        slimLog($req, $res,null,'此接口仅开放POST请求');
        ajaxReturn(array('code' => 106, 'data' => '请求错误'));
    }

    //取得参数列表
    $validate_ok = validate(
        array(
            'school_id' => 'INT',
            'user_id'   => 'INT',
            'phone'     => 'STRING',
            'user_name' => 'STRING',
            'user_pass' => 'STRING',
            'lesson_id' => 'STRING',
            'license_id' => 'STRING',
        ), $req->params());

    if ( !$validate_ok['pass'] ) {
        slimLog($req, $res,null,'参数不完整或类型不对');
        ajaxReturn($validate_ok['data']);
    }

    $p          = $req->params();
    $phone      = $p['phone'];
    $user_id    = $p['user_id'];
    $school_id  = $p['school_id'];
    $coach_name = $p['user_name'];
    $lesson_id  = $p['lesson_id'];
    $license_id = $p['license_id'];
    $coach_pass = md5($p['user_pass']);

    //ready to return
    $data = array();
    try {
        // Open connection with mysql
        $db     = getConnection();
        $coach  = DBPREFIX.'coach';
        $school = DBPREFIX.'school';
        $user   = DBPREFIX.'user';


        // 判断user表中是否有此教练
        $sql = " SELECT `s_phone` FROM `{$user}` WHERE `s_phone` = :phone AND `l_user_id` = :user_id AND `i_user_type` = 1 AND `i_status` = 0 ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('phone', $phone);
        $stmt->bindParam('user_id', $user_id);
        $stmt->execute();
        $s_user_phone = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($s_user_phone)) {
            $data   = array('code' => 400, 'data' => '号码错误');
            $db     = null;
            ajaxReturn($data);
        }
        // 在coach表中判断此用户是否存在
        $sql = " SELECT `s_coach_phone`, `user_id` FROM `{$coach}` WHERE `s_coach_phone` = :phone AND `user_id` = :uid  ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('phone', $phone);
        $stmt->bindParam('uid', $user_id);
        $stmt->execute();
        $_phone = $stmt->fetch(PDO::FETCH_ASSOC);
        // 若coach表中存在此教练则更新相关信息，不存在则插入
        if (!empty($_phone)) {
            $sql    = " UPDATE {$coach} SET `s_coach_name` = '{$coach_name}', `s_coach_phone` = '{$phone}', `user_id` = '{$user_id}', `s_school_name_id` = '{$school_id}', `s_coach_lesson_id` = '{$lesson_id}', `s_coach_lisence_id` = '{$license_id}', `i_type` = 1 WHERE `s_coach_phone` = '{$phone}' AND  `user_id` = '{$user_id}' ";
            $result = $db->query($sql);
        } else {
            // 将号码和user_id插入到coach表中
            $now_time = time();
            $sql    = " INSERT INTO {$coach} (`s_coach_name`, `s_coach_phone`, `user_id`, `s_school_name_id`, `s_coach_lesson_id`, `s_coach_lisence_id`, `i_type`, `addtime`) VALUES ('{$coach_name}', '{$phone}', '{$user_id}', '{$school_id}', '{$lesson_id}', '{$license_id}', 1, {$now_time}) ";
            $result = $db->query($sql);
        }

        if (false !== $result) {
            // 数组为空表示user表中没有此教练，将号码插入到user表中
            $sql    = " UPDATE {$user}  SET `s_username` = '{$coach_name}', `s_password` = '{$coach_pass}', `s_phone` = '{$phone}', `i_user_type` = 1, `i_status` = 0,`s_real_name` = '{$coach_name}' WHERE `s_phone` = '{$phone}' AND `i_user_type` = 1 AND  `l_user_id` = '{$user_id}'  ";
            $re = $db->query($sql);
            if ($re) {
                // $coach_id = $last_coach_id;
                $coach  = DBPREFIX.'coach';
                $sql = "SELECT `s_coach_name`,";
                $sql .= " `l_coach_id`,";
                $sql .= " `s_teach_age`,";
                $sql .= " `s_coach_sex`,";
                $sql .= " `s_coach_imgurl`,";
                $sql .= " `certification_status`,";
                $sql .= " `s_coach_qrcode`,";
                $sql .= " `s_coach_share_url`,";
                $sql .= " `s_coach_phone`,";
                $sql .= " `s_school_name_id`,";
                $sql .= " `s_coach_lesson_id`, ";
                $sql .= " `s_coach_lisence_id`,";
                $sql .= " `user_id`, ";
                $sql .= " `is_first` ";
                $sql .= " FROM `{$coach}` ";
                $sql .= " WHERE `s_coach_phone` = :cphone AND `user_id` = :uid ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('cphone', $phone);
                $stmt->bindParam('uid', $user_id);
                $stmt->execute();
                $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
                if(empty($coach_info)) {
                    $data = array('code'=>103, 'data'=>'不存在此教练');
                    exit(json_encode($data));
                }
                $coach_id = $coach_info['l_coach_id'];
                if(file_exists(__DIR__.'/../../../sadmin/'.$coach_info['s_coach_imgurl'])) {
                    $coach_info['s_coach_imgurl'] = S_HTTP_HOST.$coach_info['s_coach_imgurl'];
                }elseif(file_exists(__DIR__.'/../../../admin/'.$coach_info['s_coach_imgurl'])) {
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
                $sql = "SELECT `s_school_name` FROM `{$school}` WHERE `l_school_id` = :school_id ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('school_id', $school_id);
                $stmt->execute();
                $school_info = $stmt->fetch(PDO::FETCH_ASSOC);
                $school_name = '';
                if($school_info) {
                    $school_name = $school_info['s_school_name'];
                }
                $coach_info['school_name'] = $school_name == '' ? '嘻哈驾校' : $school_name;
                $coach_info['l_coach_id'] = $coach_id;
                $coach_info['s_coach_name'] = $coach_name;


                // 客服电话
                $coach_info['service_phone'] = '0551-65610256';

                $is_first = $coach_info['is_first'];
                if ($is_first == 0) {
                    $sql = " UPDATE {$coach} SET `is_first` = 1 WHERE `s_coach_phone` = {$phone} AND `user_id` = {$user_id} ";
                    $update_first = $db->query($sql);
                }

                $data = array('code' => 200,'data' => $coach_info);
                $db = null;
                ajaxReturn($data);
            }
        } else {
            $data   = array('code' => 103,'data' => '添加教练失败');
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
?>
