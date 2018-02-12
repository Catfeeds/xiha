<?php 

	/**
	 * 校长APP获取财务统计接口
	 * @param $user_id $type(today month year custom) $start $end 
	 * @return $signup_total_order $signup_total_money $signup_order_info $appoint_total_order $appoint_total_money $appoint_order_info $time $name $price
	 * @author sunweiwei
	 **/

	require '../../Slim/Slim.php';
	require '../../include/common.php';
	require '../../include/crypt.php';
    require '../../include/functions.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->any('/','FinanceStatistics');
    $app->response->headers->set('Content-Type', 'application/json;charset=utf8');
	$app->run();

	function FinanceStatistics() {
		global $app, $crypt;
        $request = $app->request();
        if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_user_practice] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            echo json_encode($data);
            return;
            
        }

        //	获取请求参数并判断合法性
        $user_id = $request->params('user_id');//用户id
        $type = $request->params('type');//请求类型
        $start = $request->params('start');//开始时间戳
        $end = $request->params('end');//结束时间戳
        $validate_result = validate(array('user_id'=>'INT', 'type'=>'STRING'), $request->params());
        if (!$validate_result['pass']) {
            echo json_encode($validate_result['data']);
            exit();
        }

        try{
            $db = getConnection();
            //根据传入的用户id查找用户驾校id
            $sql = "SELECT i.`school_id` FROM `cs_user` AS u LEFT JOIN `cs_users_info` AS i ON i.`user_id` = u.`l_user_id` WHERE (u.`i_user_type` = 2 AND u.`i_status` = 0) AND i.`user_id` = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $user_id);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $school_id = $res['school_id'];
            if (!$school_id) {//如果该用户在user表中类型不是2(驾校)与并且在user_info表中没有与驾校绑定（即school不存在）
                $data = array('code' => 102, 'data' => '参数错误');
                echo json_encode($data);
                exit();
            } 
             //现在时间戳
            $now = strtotime("now");
            //今日凌晨时间戳
            $day_start = strtotime(date('Y-m-d', time()));
            //本月第一天凌晨时间戳
            $month_start = strtotime(date('Y-m-01', time()));
            //今年第一天凌晨时间戳
            $year_start = strtotime(date('Y-01-01', time()));
            if ($type == 'today') {
                $return = getOrderContent($school_id, $day_start, $now);  
               } elseif ($type == 'month') {
                 $return = getOrderContent($school_id, $month_start, $now); 
                 // var_dump($return);
                 // exit();       
               } elseif ($type == 'year') {
                 $return = getOrderContent($school_id, $year_start, $now);        
               } elseif ($type == 'custom') {
                 $start = strtotime($start);
                 $end = strtotime($end);
                 $return = getOrderContent($school_id, $start, $end);        
               }     
            $db = null;
            $data = array('code' => 200, 'data' => $return);
            echo json_encode($data);

        } catch (PDOException $e) {
            setapilog('get_user_practice: params[user_id: ' . $user_id . ',start: ' . $start . ',end: ' . $end . '],error:' . $e->getMessage());
            $data = array('code' => 1, 'data' => '网络错误');
            echo json_encode($data);
        }


	}/*end function*/ 

    //根据传入的起始和结束时间戳查找订单信息
    function getOrderContent($school_id, $start, $end) {
        //报名驾校
            $signup_total_order = 0;
            $signup_total_money = 0;
            $signup_order_info = array();
            $appoint_total_order = 0;
            $appoint_total_money = 0;
            $appoint_order_info = array();
            $coach_ids = '';

            $db = getConnection();
            $sql = "SELECT `so_final_price`, `so_username`, `addtime` FROM `cs_school_orders` WHERE `so_school_id` = :school_id AND `so_order_status` != 101 AND `so_order_status` != 3 AND (`addtime` BETWEEN :start AND :end)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('school_id', $school_id);
            $stmt->bindParam('start', $start);
            $stmt->bindParam('end', $end);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($res) {
                $signup_order_info = $res;
                $signup_total_order = count($res);
                foreach ($res as $key => $value) {
                    $signup_total_money += $value['so_final_price'];
                }
                $signup_total_money = sprintf("%.2f", $signup_total_money);
            }          
            
            //预约学车
            $sql = "SELECT `l_coach_id`FROM `cs_coach` WHERE `s_school_name_id` = :school_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('school_id', $school_id);
            $stmt->execute();
            $coach_id = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($coach_id) {
                foreach ($coach_id as $key => $value) {
                    $ids[] = $value['l_coach_id'];
                }
                $coach_ids = implode(',', $ids);//所有教练id   
            }
            $sql = "SELECT `s_user_name`, `dc_money`, `dt_order_time` FROM `cs_study_orders` WHERE `l_coach_id` IN(:coach_ids) AND `i_status` != 101 AND `i_status` != 3 AND (`dt_order_time` BETWEEN :start AND :end)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('coach_ids', $coach_ids);
            $stmt->bindParam('start', $start);
            $stmt->bindParam('end', $end);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($res) {
                $appoint_order_info = $res;
                $appoint_total_order = count($res);
                foreach ($res as $key => $value) {
                    $appoint_total_money += $value['dc_money'];
                }
                $appoint_total_money = sprintf("%.2f", $appoint_total_money);
            } 

            $ret = array('signup_total_order' => $signup_total_order, 'signup_total_money' => $signup_total_money, 'appoint_total_order' => $appoint_total_order, 'appoint_total_money' => $appoint_total_money, 'signup_order_info' => $signup_order_info, 'appoint_order_info' => $appoint_order_info);
            return $ret;
    }

 ?>