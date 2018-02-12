<?php  
    /**
     * 获取学员列表（增加学员同时在records表中增加一条记录）
     * @param int $coach_id 教练ID
     * @param string $coach_phone 教练手机号
     * @param int $page 分页
     * @param int $stage 状态 1：待定 2：科目二 3：科目三 4：毕业
     * @param int $year 年
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
    $app->any('/','getUsersList');
    $app->run();

    function getUsersList() {
        Global $app, $crypt;
        $request = $app->request();
         if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_users_list] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            exit(json_encode($data));
        }
        //  获取请求参数并判断合法性
        $validate_result = validate(
            array(
                'coach_id'      =>'INT',
                'coach_phone'   =>'STRING',
                'page'          =>'INT', 
                'stage'         =>'INT',
                'year'          =>'INT', 
                'month'         =>'INT', 
                'day'           =>'INT', 
            ), $request->params());

        if (!$validate_result['pass']) {
            exit(json_encode($validate_result['data']));
        }
        $p = $request->params();
        $coach_id = $p['coach_id'];
        $coach_phone = $p['coach_phone'];
        $stage = $p['stage'];
        $page = $p['page'] === '0' ? 1 : abs($p['page']);
        $year = $p['year'];
        $month = $p['month'];
        $day = $p['day'];

        $limit = 10;
        $start = ($page - 1) * $limit;
        try {
            $db = getConnection();

            //教练存在
            $coach = DBPREFIX.'coach';
            $sql = "SELECT 1 FROM `{$coach}` WHERE `l_coach_id` = :coach_id AND `s_coach_phone` = :coach_phone";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('coach_id', $coach_id);
            $stmt->bindParam('coach_phone', $coach_phone);
            $stmt->execute();
            $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($coach_info)) {
                $data = array('code'=>103, 'data'=>'教练不存在');
                exit( json_encode($data) );
            }

            $coach_users = DBPREFIX.'coach_users';
            $coach_users_relation = DBPREFIX.'coach_users_relation';
            $coach_users_records = DBPREFIX.'coach_users_records';
            $users = DBPREFIX.'user';

            $_sql = '';
            $sql = "SELECT u.`id`, u.`user_name`, u.`user_phone`, u.`user_photo`, r.`identity_id`, r.`i_status`, r.`addtime`, r.`updatetime`, r.`timestamp`, r.`year`, r.`month`, r.`day`, r.`user_name` as r_user_name, r.`user_phone` as r_user_phone, r.`is_bind` FROM `{$coach_users}` AS u INNER JOIN `{$coach_users_records}` AS r";        
            $where = " ON u.`id` = r.`coach_users_id` WHERE r.`coach_id` = :cid AND r.`i_stage` = :stage AND `is_deleted` = 1 ";
            $group = " GROUP BY r.`timestamp` ";
            $order = " ORDER BY r.`timestamp` DESC, r.`updatetime` DESC ";
            $sql .= $where;
            $_sql .= $sql.$group.$order;

            $stmt = $db->prepare($_sql);
            $stmt->bindParam('cid', $coach_id);
            $stmt->bindParam('stage', $stage);
            $stmt->execute();
            $coach_users_time_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(empty($coach_users_time_list)) {
                $data = array('code'=>200, 'data'=>array('stage'=>$stage, 'date_list'=>array(), 'users_list'=>array()));
                exit( json_encode($data) );
            }

            $date_list = array();
            foreach ($coach_users_time_list as $key => $value) {
                $date_list[$key]['year'] = $value['year'];
                $date_list[$key]['month'] = sprintf('%02d', $value['month']);
                $date_list[$key]['day'] = sprintf('%02d', $value['day']);
                $date_list[$key]['timestamp'] = $value['timestamp'];
                
                // 获取每一天的人员统计
                $csql = " SELECT count(1) as num FROM `{$coach_users_records}` WHERE `coach_id` = :cid AND `i_stage` = :stage AND `year` = :ye AND `month` = :mo AND `day` = :da AND `is_deleted` = 1 ";
                $stmt = $db->prepare($csql);
                $stmt->bindParam('cid', $coach_id);
                $stmt->bindParam('stage', $stage);
                $stmt->bindParam('ye', $value['year']);
                $stmt->bindParam('mo', $value['month']);
                $stmt->bindParam('da', $value['day']);
                $stmt->execute();
                $coach_num = $stmt->fetch(PDO::FETCH_ASSOC);
                $date_list[$key]['total_num'] = 0;
                if($coach_num) {
                    $date_list[$key]['total_num'] = $coach_num['num'];
                }
            }
            // 获取学员列表
            $sql .= $order;
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid', $coach_id);
            $stmt->bindParam('stage', $stage);
            $stmt->execute();
            $coach_users_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(empty($coach_users_list)) {
                $_data = array('stage'=>$stage, 'date_list'=>array_values($date_list), 'users_list'=>array());
            } else {
                $list = array();
                foreach ($coach_users_list as $key => $value) {
                    $coach_users_list[$key]['user_name']     = ($value['r_user_name'] != '' && $value['r_user_name'] != $value['user_name']) ? $value['r_user_name'] : $value['user_name'];
                    $coach_users_list[$key]['user_phone']     = ($value['r_user_phone'] != '' && $value['r_user_phone'] != $value['user_phone']) ? $value['r_user_phone'] : $value['user_phone'];

                    // 获取学员是否已注册嘻哈学车
                    $coach_users_list[$key]['is_register'] = 1; // 未注册
                    $coach_users_list[$key]['is_bind'] = (int)$value['is_bind'];
                    $sql = "SELECT 1 FROM `{$users}` WHERE `s_phone` = :phone AND `i_user_type` = 0";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('phone', $coach_users_list[$key]['user_phone']);
                    $stmt->execute();
                    $users_info = $stmt->fetch(PDO::FETCH_ASSOC);

                    if(!empty($users_info)) {
                        $coach_users_list[$key]['is_register'] = 2; // 已注册
                    }
                    if(!empty($date_list)) {
                        foreach ($date_list as $k => $v) {
                            if($v['timestamp'] == $value['timestamp']) {
                                $list[$k]['timestamp'] = $v['timestamp'];
                                $list[$k]['year'] = $value['year'];
                                $list[$k]['month'] = $value['month'];
                                $list[$k]['day'] = $value['day'];
                                if(isset($coach_users_list[$key])) {
                                    $list[$k]['users_list'][] = $coach_users_list[$key];
                                } else {
                                    $list[$k]['users_list'][] = array();
                                }
                                    
                            }
                        }
                    }
                }
                $_data = array('stage'=>$stage, 'date_list'=>array_values($date_list), 'users_list'=>array_values($list));
            }
            $db = null;
            $data = array('code'=>200, 'data'=>$_data);
            exit( json_encode($data) );
        } catch(PDOException $e) {
            setapilog('[get_users_list] [:error] [client ' . $request->getIp() . '] [params ' . serialize($request->params()) . '] ['. $e->getLine() . ' ' . $e->getMessage() . ']');
            $data = array('code'=>1, 'data'=>'网络错误');
            exit(json_encode($data));
        }
    }
?>
