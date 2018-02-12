<?php  
    /**
     * 编辑学员（编辑学车或者编辑考试）
     * @param int $coach_users_id 学员ID
     * @param int $coach_id 教练ID
     * @param string $user_name 学员姓名
     * @param string $user_phone 学员号码
     * @param int $stage 状态（1：待定 2：科目二 3：科目三 4：毕业）
     * @param string $identity_id 身份证
     * @param $int year 年
     * @param int $month 月
     * @param int $day 日
     * @return json
     * @author cx
     **/
    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/','editUser');
    $app->run();

    function editUser() {
        Global $app, $crypt;
        $request = $app->request();
         if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[edit_user] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            exit(json_encode($data));
        }
        //  获取请求参数并判断合法性
        $validate_result = validate(
            array(
                'coach_users_id'     =>'INT', 
                'coach_id'           =>'INT',
                'user_name'            =>'STRING',
                'user_phone'        =>'STRING',
                'stage'                =>'INT',
                'status'            =>'INT',
                'identity_id'        =>'STRING',
                'year'                =>'INT',
                'month'                =>'INT',
                'day'                =>'INT',
            ), $request->params());

        if (!$validate_result['pass']) {
            exit(json_encode($validate_result['data']));
        }
        $p = $request->params();
        $coach_users_id = $p['coach_users_id'];
        $coach_id         = $p['coach_id'];
        $user_name         = $p['user_name'];
        $user_phone     = $p['user_phone'];
        $stage             = $p['stage'];
        $status         = $p['status'];
        $identity_id     = $p['identity_id'];
        $year             = $p['year'];
        $month             = $p['month'];
        $day             = $p['day'];
        $timestamp = strtotime($year.'-'.$month.'-'.$day);
        $updatetime = time();

        if(trim($user_name) == '' || trim($user_phone) == '' || trim($identity_id) == '') {
            $data = array('code'=>102, 'data'=>'请填写完整信息');
            exit( json_encode($data) );
        }

        try {
            $db = getConnection();
            $coach_users = DBPREFIX.'coach_users';
            $coach_users_records = DBPREFIX.'coach_users_records';

            $sql = "SELECT 1 FROM `{$coach_users}` WHERE `id` = :uid";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uid', $coach_users_id);
            $stmt->execute();
            $coach_users_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($coach_users_info)) {
                $data = array('code'=>103, 'data'=>'不存在此学员');
                exit( json_encode($data) );
            }

            // 判断号码不能对统一教练相同
            $sql = "SELECT 1 FROM `{$coach_users_records}` WHERE `coach_id` = :cid AND `user_phone` = :uphone AND `coach_users_id` != :uid";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid', $coach_id);
            $stmt->bindParam('uphone', $user_phone);
            $stmt->bindParam('uid', $coach_users_id);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!empty($res)) {
                $data = array('code'=>103, 'data'=>'此学员号码已添加');
                exit( json_encode($data) );
            }

            $sql = "UPDATE `{$coach_users_records}` SET `user_name` = :uname, `user_phone` = :uphone, `i_stage` = :stage, `i_status` = :status, `identity_id` = :iid, `year` =:y, `month` = :m, `day` = :d, `timestamp` = :ts, `updatetime` = :ut";
            $where = " WHERE `coach_users_id` = :cuid AND `coach_id` = :cid";

            $sql .= $where;
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uname', $user_name);
            $stmt->bindParam('uphone', $user_phone);
            $stmt->bindParam('stage', $stage);
            $stmt->bindParam('status', $status);
            $stmt->bindParam('iid', $identity_id);
            $stmt->bindParam('y', $year);
            $stmt->bindParam('m', $month);
            $stmt->bindParam('d', $day);
            $stmt->bindParam('ts', $timestamp);
            $stmt->bindParam('ut', $updatetime);
            $stmt->bindParam('cuid', $coach_users_id);
            $stmt->bindParam('cid', $coach_id);
            $res = $stmt->execute();

            if($res) {
                $data = array('code'=>200, 'data'=>'更新成功');
            } else {
                $data = array('code'=>400, 'data'=>'更新失败');
            }
                
            $db = null;
            exit( json_encode($data) );

        } catch(PDOException $e) {
            setapilog('[edit_user] [:error] [client ' . $request->getIp() . '] [params ' . serialize($request->params()) . '] [1 ' . $e->getMessage() . ']');
            $data = array('code'=>1, 'data'=>'网络错误');
            exit(json_encode($data));
        }
    }
?>
