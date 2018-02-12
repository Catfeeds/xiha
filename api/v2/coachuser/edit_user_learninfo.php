<?php
    /**
    * 教练端编辑培训信息
    * @param    int     $coach_id               教练ID
    * @param    int     $user_id                学员ID
    * @param    int     $signup_timestamp       报名驾校的时间戳
    * @param    int     $original_price         报名费
    * @param    int     $final_price            已收费
    * @param    int     $lesson2_learn_times    科目二学时
    * @param    int     $lesson3_learn_times    科目三学时
    * @return   json
    * @package  api/v2/coachuser
    * @author   wl
    * @date     July 11, 2016
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'editUserLearnInfo');
    $app->run();

    function editUserLearnInfo() {
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
                'coach_id'              => 'INT',
                'user_id'               => 'INT',
                'signup_timestamp'      => 'INT',
                'original_price'        => 'INT',
                'final_price'           => 'INT',
                'lesson2_learn_times'   => 'INT',
                'lesson3_learn_times'   => 'INT',
            ), $req->params());

        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res,null,'参数不完整或类型不对');
            ajaxReturn($validate_ok['data']);
        }

        $p = $req->params();
        $coach_id            = $p['coach_id'];
        $coach_users_id      = $p['user_id'];
        $signup_timestamp    = $p['signup_timestamp'];
        $original_price      = $p['original_price'];
        $final_price         = $p['final_price'];
        $lesson2_learn_times = $p['lesson2_learn_times'];
        $lesson3_learn_times = $p['lesson3_learn_times'];
        $updatetime          = time();

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();
            $coach_users_records = DBPREFIX.'coach_users_records';
             //  判断学员是否存在
            $sql = " SELECT 1 FROM `{$coach_users_records}` WHERE `coach_users_id` = :uid AND `coach_id` = :cid";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uid',$coach_users_id, PDO::PARAM_INT);
            $stmt->bindParam('cid',$coach_id, PDO::PARAM_INT);
            $stmt->execute();
            $coach_users_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($coach_users_info)) {
                $data = array('code' => '103','data' => '不存在此学员');
                ajaxReturn($data);
            }   

            $sql = " UPDATE `{$coach_users_records}` SET `signup_timestamp`=:signup_timestamp,`original_price`=:original,`final_price`=:final,`lesson2_learn_times`=:lesson2_learn_times,`lesson3_learn_times`=:lesson3_learn_times, `updatetime` = :ut ";
            $where = " WHERE `coach_users_id`=:cuid AND `coach_id`=:cid";
            $sql.= $where;
            $stmt = $db->prepare($sql);
            $stmt->bindParam('signup_timestamp', $signup_timestamp);
            $stmt->bindParam('original', $original_price);
            $stmt->bindParam('final', $final_price);
            $stmt->bindParam('lesson2_learn_times', $lesson2_learn_times);
            $stmt->bindParam('lesson3_learn_times', $lesson3_learn_times);
            $stmt->bindParam('ut', $updatetime);
            $stmt->bindParam('cuid', $coach_users_id);
            $stmt->bindParam('cid', $coach_id);
            $result = $stmt->execute();

            if($result) {
                $data = array('code'=>200,'data'=>'保存成功');
            } else {
                $data = array('code'=>400,'data'=>'保存失败');
            }
            // shut down the connection
            $db = null;
            ajaxReturn($data);

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
