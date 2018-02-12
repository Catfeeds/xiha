<?php  
    /**
     * 添加学员（学员添加考试, 学员添加学车 导入学员 都在一个接口）
     * @param int $coach_id 教练id
     * @param int $stage 学员的阶段
     * @param int $status 学员的状态
     * @param JSON_string $users_json 学员的详细信息
     * @param int $year 年
     * @param int $month 月
     * @param int $day 日
     * @return json
     * @author cx
     **/

     //users_json样例
        /*
            [
                {
                    "user_name": "陈曦",
                    "user_phone": "18656999023"
                },
                {
                    "user_name": "张三",
                    "user_phone": "12345678901"
                },
                {
                    "user_name": "李四",
                    "user_phone": "12345678902"
                }
            ]
        */

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/','addUsers');
    $app->run();

    function addUsers() {
        Global $app, $crypt;
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            slimLog($req, $res, null, '此接口仅开放POST方法请求');
            ajaxReturn($data);
        }
        //  获取请求参数并判断合法性
        $validate_result = validate(
            array(
                'coach_id'      =>'INT',
                'stage'         =>'INT',
                'status'        =>'INT',
                'users_json'    =>'STRING', 
                'year'          =>'INT',
                'month'         =>'INT',
                'day'           =>'INT'
            ), $req->params());
        if (!$validate_result['pass']) {
            ajaxReturn($validate_result['data']);
        }
        $p              = $req->params();
        $coach_id       = $p['coach_id'];
        $stage          = $p['stage'];
        $status         = $p['status'];
        $users_json     = $p['users_json'];
        $year           = $p['year'];
        $month          = $p['month'];
        $day            = $p['day'];
        $identity_id    = isset($p['identity_id']) ? $p['identity_id'] : 0;
        $timestamp      = strtotime($year."-".$month."-".$day);

        /* 
         * 初始化[学员]状态码配置
         */
        $stage_status_arr = array(
            '1' => array('1'),
            '2' => array('1001', '1002', '1003'),
            '3' => array('1001', '1002', '1003'),
            '4' => array('4'),
        );
        // 添加阶段和状态的容错机制
        if ( !in_array($stage, array_keys($stage_status_arr))) {
            $rt = array(
                'code' => 110,
                'data' => '参数错误',
            );
            ajaxReturn($rt);
        }
        if ( !in_array($status, $stage_status_arr[$stage]) ) {
            $status = $stage_status_arr[$stage][0];
        }

        try {
            $db = getConnection();
            $users_json_arr = json_decode($users_json, true);
            if(empty($users_json_arr)) {
                $data = array('code'=>102, 'data'=>'学员格式错误');
                ajaxReturn($data);
            }

            if(count($users_json_arr) > 50) {
                $data = array('code'=>110, 'data'=>'一次最多只能导入五十个');
                ajaxReturn($data);
            }

            $coach_users = DBPREFIX . 'coach_users';
            $coach_users_records = DBPREFIX . 'coach_users_records';

            $sql = "";
            $insert_coach_users_keys = array(
                "`id`",
                "`user_name`",
                "`user_phone`",
                "`user_photo`",
                "`household_property`",
                "`i_stage`",
                "`identity_id`",
                "`user_property`",
                "`signup_school_name`",
                "`signup_school_id`",
                "`user_address`",
                "`lesson2_learn_times`",
                "`lesson3_learn_times`",
                "`lesson1_exam_times`",
                "`lesson2_exam_times`",
                "`lesson3_exam_times`",
                "`lesson4_exam_times`",
                "`addtime`",
                "`updatetime`"
            );

            $insert_coach_users_records_keys = array(
                "`id`",
                "`coach_users_id`",
                "`coach_id`",
                "`user_phone`",
                "`user_name`",
                "`start_time`",
                "`end_time`",
                "`year`",
                "`month`",
                "`day`",
                "`timestamp`",
                "`i_stage`",
                "`identity_id`",
                "`i_status`",
                "`lesson2_learn_times`",
                "`lesson3_learn_times`",
                "`lesson1_exam_times`",
                "`lesson2_exam_times`",
                "`lesson3_exam_times`",
                "`lesson4_exam_times`",
                "`is_bind`",
                "`addtime`",
                "`updatetime`"
            );
            foreach ($users_json_arr as $key => $value) {
                $_sql = "SELECT `id` FROM `{$coach_users}` WHERE `user_phone` = :phone";
                $stmt = $db->prepare($_sql);
                $stmt->bindParam('phone', $value['user_phone']);
                $stmt->execute();
                $coach_users_info = $stmt->fetch(PDO::FETCH_ASSOC);

                if(!empty($coach_users_info)) {
                    $coach_users_id = $coach_users_info['id'];
                    $sql = " UPDATE `{$coach_users}` SET `user_name` = '{$value['user_name']}' WHERE `user_phone` = '{$value['user_phone']}'";
                    $_res = $db->query($sql);

                    $sql = "SELECT `id` FROM `{$coach_users_records}` WHERE `user_phone` = :phone AND `coach_id` = :cid AND `coach_users_id` = :cuid AND `is_deleted` = 1 ";
                    // is_deleted = 1-未删除 2-已删除
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('phone', $value['user_phone']);
                    $stmt->bindParam('cid', $coach_id);
                    $stmt->bindParam('cuid', $coach_users_id);
                    $stmt->execute();
                    $_records_info = $stmt->fetch(PDO::FETCH_ASSOC);

                    if(empty($_records_info)) {
                        $insert_coach_users_records_values = array(
                            "NULL",
                            "{$coach_users_id}",
                            "{$coach_id}",
                            "'{$value['user_phone']}'",
                            "'{$value['user_name']}'",
                            "0",
                            "0",
                            "{$year}",
                            "{$month}",
                            "{$day}",
                            "{$timestamp}",
                            "{$stage}",
                            "'{$identity_id}'",
                            "{$status}",
                            "0",
                            "0",
                            "0",
                            "0",
                            "0",
                            "0",
                            "1",
                            time(),
                            time()
                        );
                        $sql = "INSERT INTO `{$coach_users_records}` (".implode(',', $insert_coach_users_records_keys).") VALUES (".implode(',', $insert_coach_users_records_values).")";
                        $res = $db->query($sql);
                    } else {
                        // 更新
                        $lesson2_learn_times = 0;
                        $lesson3_learn_times = 0;
                        $lesson1_exam_times = 0;
                        $lesson2_exam_times = 0;
                        $lesson3_exam_times = 0;
                        $lesson4_exam_times = 0;
                        if(2 == $stage) {
                            if(1002 == $status) {
                                $lesson2_learn_times++;
                            } else if(1003 == $status) {
                                $lesson2_learn_times++;
                            }
                        }

                        $update_sql = "UPDATE `{$coach_users_records}` SET ";
                        $update_sql .= " `year` = '{$year}', ";
                        $update_sql .= " `month` = '{$month}', ";
                        $update_sql .= " `day` = '{$day}', ";
                        $update_sql .= " `timestamp` = '{$timestamp}', ";
                        $update_sql .= " `i_stage` = '{$stage}', ";
                        $update_sql .= " `identity_id` = '{$identity_id}', ";
                        $update_sql .= " `i_status` = '{$status}', ";
                        $update_sql .= " `updatetime` = '".time()."', ";
                        $update_sql .= " `lesson2_learn_times` = '{$lesson2_learn_times}', ";
                        $update_sql .= " `lesson3_learn_times` = '{$lesson3_learn_times}', ";
                        $update_sql .= " `lesson1_exam_times` = '{$lesson1_exam_times}', ";
                        $update_sql .= " `lesson2_exam_times` = '{$lesson2_exam_times}', ";
                        $update_sql .= " `lesson3_exam_times` = '{$lesson3_exam_times}', ";
                        $update_sql .= " `lesson4_exam_times` = '{$lesson4_exam_times}' ";
                        $update_sql .= " WHERE `coach_id` = '{$coach_id}' AND `coach_users_id` = '{$coach_users_id}'";
                        $res = $db->query($update_sql);
                    }

                } else {
                    // coach_users表不存在记录
                    $insert_coach_users_values = array(
                        "NULL",
                        "'{$value['user_name']}'",
                        "'{$value['user_phone']}'",
                        "''",
                        "''",
                        "{$stage}",
                        "'{$identity_id}'",
                        "''",
                        "''",
                        "0",
                        "''",
                        "0",
                        "0",
                        "0",
                        "0",
                        "0",
                        "0",
                        time(),
                        time()
                    );
                    $sql = "INSERT INTO `{$coach_users}` (".implode(',', $insert_coach_users_keys).") VALUES (".implode(',', $insert_coach_users_values).")";
                    $res = $db->query($sql);
                    $after_last_insert_id = 0;
                    if($res) {
                        $after_last_insert_id = $db->lastInsertId();
                        // 插入记录到records表中
                        $sql = "SELECT 1 FROM `{$coach_users_records}` WHERE `user_phone` = :phone AND `coach_id` = :cid";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam('phone', $value['user_phone']);
                        $stmt->bindParam('cid', $coach_id);
                        $stmt->execute();
                        $records_info = $stmt->fetch(PDO::FETCH_ASSOC);
                        if(empty($records_info)) {
                            // 有记录
                            $insert_coach_users_records_values = array(
                                "NULL",
                                "{$after_last_insert_id}",
                                "{$coach_id}",
                                "'{$value['user_phone']}'",
                                "'{$value['user_name']}'",
                                "0",
                                "0",
                                "{$year}",
                                "{$month}",
                                "{$day}",
                                "{$timestamp}",
                                "{$stage}",
                                "'{$identity_id}'",
                                "{$status}",
                                "0",
                                "0",
                                "0",
                                "0",
                                "0",
                                "0",
                                "1",
                                time(),
                                time()
                            );
                            $sql = "INSERT INTO `{$coach_users_records}` (".implode(',', $insert_coach_users_records_keys).") VALUES (".implode(',', $insert_coach_users_records_values).")";
                            $res = $db->query($sql);
                        }
                    }
                }
            }

            $db = null;
            $data = array('code'=>200, 'data'=>'导入成功');
            exit( json_encode($data) );

        } catch(PDOException $e) {
            setapilog('[add_users] [:error] [client ' . $req->getIp() . '] [params ' . serialize($req->params()) . '] ['. $e->getLine() .' ' . $e->getMessage() . ']');
            $data = array('code'=>1, 'data'=>'网络错误');
            exit(json_encode($data));
        }
    }

?>
