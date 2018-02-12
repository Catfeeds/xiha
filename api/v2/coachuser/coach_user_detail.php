<?php
    /**
    * 学员详情接口
    * @param    int     $coach_id   教练id
    * @param    int     $user_id    学员id
    * @param    string  $user_phone 学员手机号码
    * @return   json
    * @package  /api/v2/coachuser/
    * @author   gdc
    * @date     July 10, 2016
    * @update   July 13, 2016 [gdc]
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'coach_user_detail');
    $app->run();

    function coach_user_detail() {
        global $app, $crypt;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '此接口仅开放POST请求方式');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array(
                'coach_id'  => 'INT',
                'user_id'   => 'INT',
                'user_phone'=> 'STRING',
            ), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res);
            ajaxReturn($validate_ok['data']);
        }

        $p          = $req->params();
        $coach_id   = $p['coach_id'];
        $user_id    = $p['user_id'];
        $user_phone = $p['user_phone'];

        //ready to return
        $data = array();

        //学员详情
        $user_detail = array();

        try {
            // Open connection with mysql
            $db = getConnection();

            // step a : 基本信息
            $tbl = DBPREFIX . 'coach_users_records';
            $sql = " SELECT `user_name`, `is_bind`, `photo_id`, `photo_img`, `i_stage`, `identity_id`, `identity_img`, `signup_timestamp`, `original_price`, `final_price`, `lesson2_learn_times`, `lesson3_learn_times` ";
            $sql .= " FROM `{$tbl}` WHERE `coach_id` = :cid AND `coach_users_id` = :uid AND `user_phone` = :uphone AND `is_deleted` = 1 ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid', $coach_id, PDO::PARAM_INT);
            $stmt->bindParam('uid', $user_id, PDO::PARAM_INT);
            $stmt->bindParam('uphone', $user_phone, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $user_detail['user_id'] = (int)$user_id;
                $user_detail['user_photo_id'] = (isset($result['photo_id']) && !empty($result['photo_id']) && (int)($result['photo_id']) != 0) ? (int)$result['photo_id'] : 0;
                $user_detail['user_photo_img'] = (isset($result['photo_img']) && !empty($result['photo_img']) ) ? HOST . $result['photo_img'] : '';
                $user_detail['user_name'] = (isset($result['user_name']) && !empty(trim($result['user_name']))) ? trim($result['user_name']) : '';
                $user_detail['user_phone'] = $user_phone;
                $user_detail['user_stage'] = (isset($result['i_stage']) && (int)$result['i_stage'] != 0) ? (int)$result['i_stage'] : 1 ;
                $user_detail['is_bind'] = (isset($result['is_bind']) && $result['is_bind'] == '1') ? 1 : 2;
                $user_detail['base']['user_identity_id'] = (isset($result['identity_id']) && !empty(trim($result['identity_id']))) ? $result['identity_id'] : '';
                $user_detail['base']['user_identity_img'] = (isset($result['identity_img']) && !empty(trim($result['identity_img']))) ? HOST . $result['identity_img'] : '';
                $user_detail['learn']['signup_timestamp'] = (isset($result['signup_timestamp']) && (int)$result['signup_timestamp']) ? (int)$result['signup_timestamp'] : time();
                $user_detail['learn']['original_price'] = isset($result['original_price']) ? intval($result['original_price']) : "0" ;
                $user_detail['learn']['final_price'] = isset($result['final_price']) ? intval($result['final_price']) : "0" ;
                $user_detail['learn']['lesson2_learn_times'] = isset($result['lesson2_learn_times']) ? (int)$result['lesson2_learn_times'] : 0;
                $user_detail['learn']['lesson3_learn_times'] = isset($result['lesson3_learn_times']) ? (int)$result['lesson3_learn_times'] : 0;
            } else {
                $data['code'] = 103;
                $data['data'] = '无此学员';
                $db = null;
                ajaxReturn($data);
            }

            //报名的驾校
            $tbl = DBPREFIX . 'coach_users';
            $sql = " SELECT `signup_school_name`, `signup_school_id` FROM `{$tbl}` WHERE `id` = :uid ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uid', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                if (isset($result['signup_school_name']) && trim($result['signup_school_name']) != '') {
                    $user_detail['base']['signup_school_name'] = trim($result['signup_school_name']);
                    $user_detail['base']['signup_school_id'] = (int)trim($result['signup_school_id']);
                    $user_detail['is_register'] = 1;
                } else {
                    $user_detail['base']['signup_school_name'] = '';
                    $user_detail['base']['signup_school_id'] = 0;
                    $user_detail['is_register'] = 2;
                }
            }

            // 学员通过app注册否
            $tbl = DBPREFIX . "user";
            $sql = " SELECT 1 FROM `{$tbl}` WHERE `s_phone` = :uphone ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uphone', $user_phone, PDO::PARAM_STR);
            $stmt->execute();
            $is_register = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($is_register ) {
                $user_detail['is_register'] = 1;
            } else {
                $user_detail['is_register'] = 2;
            }

            // step b 考试记录信息
            $tbl = DBPREFIX . 'coach_users_exam_records';
            $sql = " SELECT `lesson`, `exam_timestamp` FROM `{$tbl}` WHERE `coach_id` = :cid AND `coach_users_id` = :uid ORDER BY  `lesson` ASC, `exam_timestamp` ASC ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid', $coach_id, PDO::PARAM_INT);
            $stmt->bindParam('uid', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                $user_detail['exam']['exam_record_list'] = $result;
                $lesson = '';
                $not_pass = 0;
                foreach ($result as $index => $record) {
                    if ($lesson != $record['lesson']) {
                        $lesson = $record['lesson'];
                    } else {
                        $not_pass++;
                    }
                }
                $user_detail['exam']['not_pass_count'] = $not_pass;
            } else {
                $user_detail['exam'] = array();
            }

            // shut down the connection
            $db = null;
            if ($user_detail) {
                $data['code'] = 200;
                $data['data'] = array('user_detail' => $user_detail);
            }

            ajaxReturn($data);

        } catch ( PDOException $e ) {
            slimLog($req, $res, $e, '网络异常');
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
