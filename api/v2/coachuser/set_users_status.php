<?php  
    /**
     * 设置学员管理中的科目状态
     * @param $coach_users_id int 学员ID
     * @param $coach_id int 教练ID
     * @param $stage int 阶段（1：待定 2：科目二 3：科目三 4：毕业）
     * @param $status int 状态（1：待定 1001：休息中 1002：练车中 1003：考试中 4：毕业）
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
    $app->any('/','getIndex');
    $app->run();

    function getIndex() {
        Global $app, $crypt;
        $request = $app->request();
         if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[set_users_status] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            exit(json_encode($data));
        }
        //  获取请求参数并判断合法性
        $validate_result = validate(
            array(
                'coach_users_id'     => 'INT', 
                'coach_id'             => 'INT', 
                'stage'             => 'INT', 
                'status'             => 'INT', 
            ), $request->params());

        if (!$validate_result['pass']) {
            exit(json_encode($validate_result['data']));
        }
        $p = $request->params();
        $coach_users_id = $p['coach_users_id'];
        $coach_id = $p['coach_id'];
        $stage = $p['stage'];
        $status = $p['status'];
        $time = time();
        $year = date('Y', $time);
        $month = date('m', $time);
        $day = date('d', $time);
        $timestamp = strtotime($year.'-'.$month.'-'.$day);

        $stage_status_arr = array(
            '1' => array('1'),
            '2' => array('1001', '1002', '1003'),    
            '3' => array('1001', '1002', '1003'),    
            '4' => array('4'),    
        );
        if(!isset($stage_status_arr[$stage]) || !in_array($status, $stage_status_arr[$stage])) {
            $data = array('code'=>102, 'data'=>'参数错误');
            exit( json_encode($data) );
        }

        try {
            $db = getConnection();
            $coach_users_records = DBPREFIX.'coach_users_records';
            $sql = "SELECT 1 FROM `{$coach_users_records}`";
            $where = " WHERE `coach_users_id` = :user_id AND `coach_id` = :cid";
            $sql .= $where;
            $stmt = $db->prepare($sql);
            $stmt->bindParam('user_id', $coach_users_id);
            $stmt->bindParam('cid', $coach_id);
            $stmt->execute();
            $records_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($records_info)) {
                $data = array('code'=>103, 'data'=>'请求错误');
                exit( json_encode($data) );
            }

            $sql = "UPDATE `{$coach_users_records}` SET `i_stage` = :stage, `i_status` = :status, `year` = :y, `month` = :m, `day` = :d, `timestamp` = :ts, `updatetime` = :ut ";
            $sql .= $where;
            $stmt = $db->prepare($sql);
            $stmt->bindParam('stage', $stage);
            $stmt->bindParam('status', $status);
            $stmt->bindParam('y', $year);
            $stmt->bindParam('m', $month);
            $stmt->bindParam('d', $day);
            $stmt->bindParam('ts', $timestamp);
            $stmt->bindParam('ut', $time);
            $stmt->bindParam('user_id', $coach_users_id);
            $stmt->bindParam('cid', $coach_id);
            $res = $stmt->execute();
            if($res) {
                $data = array('code'=>200, 'data'=>'设置成功');
            } else {
                $data = array('code'=>400, 'data'=>'设置失败');
            }
            $db = null;
            exit( json_encode($data) );
        } catch(PDOException $e) {
            setapilog('[set_users_status] [:error] [client ' . $request->getIp() . '] [params ' . serialize($request->params()) . '] [1 ' . $e->getMessage() . ']');
            $data = array('code'=>1, 'data'=>'网络错误');
            exit(json_encode($data));
        }
    }
?>
