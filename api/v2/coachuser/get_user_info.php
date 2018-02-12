<?php  
    /**
     * 获取学员详情
     * @param int $coach_users_id 学员id
     * @param int $coach__id 教练id
     * @param string $user_phone 学员手机号码
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
    $app->any('/','getUserInfo');
    $app->run();

    function getUserInfo() {
        Global $app, $crypt;
        $request = $app->request();
         if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_user_info] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            exit(json_encode($data));
        }
        //  获取请求参数并判断合法性
        $validate_result = validate(
            array(
                'coach_users_id'    =>'INT',
                'coach_id'          =>'INT',
                'user_phone'        =>'STRING', 
            ), $request->params());

        if (!$validate_result['pass']) {
            exit( json_encode($validate_result['data']) );
        }
        $p = $request->params();
        $coach_users_id = $p['coach_users_id'];
        $coach_id = $p['coach_id'];
        $user_phone = $p['user_phone'];
        try {
            $db = getConnection();
            $coach_users = DBPREFIX.'coach_users';
            $coach_users_records = DBPREFIX.'coach_users_records';
            $user = DBPREFIX.'user';
            $study_orders = DBPREFIX.'study_orders';

            // 获取教练学员表中的数据
            $sql = "SELECT `id` FROM `{$coach_users}` WHERE `id` = :uid";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uid', $coach_users_id);
            $stmt->execute();
            $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($user_info)) {
                $data = array('code'=>104, 'data'=>'学员不存在');
                exit( json_encode($data) );
            }
            // 获取科目二，三练习次数
            $_sql = "SELECT count(1) as num FROM `{$coach_users_records}` WHERE `coach_users_id` = :user_id AND `coach_id` = :cid";
            $where = " AND `i_stage` = 2 AND `i_status` = 1002";
            $sql = $_sql.$where;
            $stmt = $db->prepare($sql);
            $stmt->bindParam('user_id', $coach_users_id);
            $stmt->bindParam('cid', $coach_id);
            $stmt->execute();
            $record_info = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_info['lesson2_practice_times'] = 0;
            if(!empty($record_info)) {
                $user_info['lesson2_practice_times'] = $record_info['num'];
            }
            
            $where = " AND `i_stage` = 3 AND `i_status` = 1002";
            $sql = $_sql.$where; 
            $stmt = $db->prepare($sql);
            $stmt->bindParam('user_id', $coach_users_id);
            $stmt->bindParam('cid', $coach_id);
            $stmt->execute();
            $record_info = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_info['lesson3_practice_times'] = 0;
            if(!empty($record_info)) {
                $user_info['lesson3_practice_times'] = $record_info['num'];
            }

            // 获取科目二，三模拟次数
            $user_info['lesson2_simulation_times'] = 0;
            $user_info['lesson3_simulation_times'] = 0;

            // 获取科目二总学时（需要判断学员是否注册嘻哈学车）
            $sql = "SELECT `l_user_id` FROM `{$user}` WHERE `s_phone` = :phone AND `i_user_type` = 0";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('phone', $user_phone);
            $stmt->execute();
            $users_info = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_info['lesson2_total_learn_times'] = 0;
            $user_info['lesson3_total_learn_times'] = 0;

            if(!empty($users_info)) {
                $user_id = $users_info['l_user_id'];
                $o_sql = "SELECT `i_service_time` as num FROM `{$study_orders}` WHERE `l_user_id` = :user_id AND `i_status` = 2 AND `l_coach_id` = :coach_id";
                $where = " AND `s_lesson_name` = '科目二'";
                $o_sql .= $where;
                $stmt = $db->prepare($o_sql);
                $stmt->bindParam('user_id', $user_id);
                $stmt->bindParam('coach_id', $coach_id);
                $stmt->execute();
                $study_orders_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($study_orders_info)) {
                    $lesson2_service_times = 0;
                    foreach ($study_orders_info as $key => $value) {
                        $lesson2_service_times += $value['num'];
                    }
                    $user_info['lesson2_total_learn_times'] = $lesson2_service_times;
                }

                $where = " AND `s_lesson_name` = '科目三'";
                $o_sql .= $where;
                $stmt = $db->prepare($o_sql);
                $stmt->bindParam('user_id', $user_id);
                $stmt->bindParam('coach_id', $coach_id);
                $stmt->execute();
                $study_orders_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($study_orders_info)) {
                    $lesson3_service_times = 0;
                    foreach ($study_orders_info as $key => $value) {
                        $lesson3_service_times += $value['num'];
                    }
                    $user_info['lesson3_total_learn_times'] = $lesson3_service_times;
                }

            }

            $user_info['lesson2_exam_status'] = '暂无';
            $user_info['lesson3_exam_status'] = '暂无';

            $db = null;
            $data = array('code'=>200, 'data'=>$user_info);
            exit( json_encode($data) );
        } catch(PDOException $e) {
            setapilog('[get_user_info] [:error] [client ' . $request->getIp() . '] [params ' . serialize($request->params()) . '] [1 ' . $e->getMessage() . ']');
            $data = array('code'=>1, 'data'=>'网络错误');
            exit(json_encode($data));
        }
    }
?>
