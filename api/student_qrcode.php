<?php 

    /**
     * 学员二维码
     * @param $user_phone   string  手机号码 (17355100855)
     * @param $user_id      int     学员用户id (721)
     * @return string AES对称加密（加密字段xhxueche）
     * @author gaodacheng
     **/

    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    require 'include/functions.php';
    require 'include/phpqrcode/qrlib.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/','student_qrcode');
    $app->run();

    function student_qrcode() {
        Global $app, $crypt;
        $request = $app->request();
        $response = $app->response();

        if ( !$request->isPost() ) {
            slimLog($request, $response, null, '需要POST');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array('user_id' => 'INT', 'user_phone' => 'STRING',), $request->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($request, $response, null, '');
            ajaxReturn($validate_ok['data']);
        }

        $p = $request->params();
        $user_id = $p['user_id'];
        $user_phone = $p['user_phone'];

        // ready to return
        $data = array();

        try {
            $db = getConnection();

            // get user info
            $user_tbl = DBPREFIX.'user';
            $users_info_tbl = DBPREFIX.'users_info';
            $province_tbl = DBPREFIX.'province';
            $city_tbl = DBPREFIX.'city';
            $sql = " SELECT province.province, city.city, user.`l_user_id`, info.`sex`, user.`s_real_name`, user.`s_phone`, info.`identity_id`, info.`photo_id` FROM `{$user_tbl}` AS user LEFT JOIN `{$users_info_tbl}` AS info ON user.`l_user_id` = info.`user_id` LEFT JOIN `{$province_tbl}` AS province ON info.`province_id` = province.`provinceid` LEFT JOIN `{$city_tbl}` AS city ON info.`city_id` = city.`cityid` WHERE user.`i_status` = 0 AND user.`i_user_type` = 0 AND user.`s_phone` = :user_phone AND user.`l_user_id` = :user_id ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('user_phone', $user_phone);
            $stmt->bindParam('user_id', $user_id);
            $stmt->execute();
            $userinfo = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$userinfo) {
                $data = array('code' => 103, 'data' => '用户不存在');
                slimLog($request, $response, null, '用户不存在');
                ajaxReturn($data);
            }

            if ((string)$userinfo['province'] == '') {
                $userinfo['province'] = '';
            }
            if ((string)$userinfo['city'] == '') {
                $userinfo['city'] = '';
            }

            //qrcode
            $_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'studentqrcode'.DIRECTORY_SEPARATOR;
            if (!file_exists($_path)) {
                mkdir($_path, 0777, true);
            }
            $t = time();
            $filename = $_path.DIRECTORY_SEPARATOR.'qrcode_'.$user_phone.'_'.$t.'.png';
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
            $userinfo['qrcode_url'] = HOST_URL.'/upload/studentqrcode/qrcode_'.$user_phone.'_'.$t.'.png';
            $errorCorrectionLevel = 'M';
            $matrixPointSize = 6;
            QRcode::png($content, $filename, $errorCorrectionLevel, $matrixPointSize);
            //qrcode

            $userinfo['qrcode_desc'] = '扫一扫二维码，可登陆电子教练';

            // 身份证为空不能生成二维码，提醒用户完善
            if (empty((string) $userinfo['identity_id'])) {
                $userinfo['errmsg'] = '请您到个人资料中填写身份证号';
                $userinfo['qrcode_url'] = '';
            }

            unset($userinfo['identity_id']);

            if (file_exists($filename)) {
                $data = array('code' => 200, 'data' => array('qrcode_info' => $userinfo));
            } else {
                $userinfo['qrcode_url'] = '';
                $data = array('code' => 200, 'data' => array('qrcode_info' => $userinfo));
            }

            $db = null;
            ajaxReturn($data);

        } catch ( PDOException $e ) {
            slimLog($request, $response, $e, 'PDO数据库异常');
            $data['code'] = 1;
            $data['data'] = '网络异常';
            $db = null;
            ajaxReturn($data);
        } catch ( ErrorException $e ) {
            slimLog($request, $response, $e, 'slim应用错误');
            $data['code'] = 1;
            $data['data'] = '网络错误';
            $db = null;
            ajaxReturn($data);
        }
    } // main func
?>
