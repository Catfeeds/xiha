<?php 
/**
 * 校长APP获取今日订单接口
 * @param $type :signup/appoint $user_id
 * @return 
 * @author sun
 **/

	require '../../Slim/Slim.php';
	require '../../include/common.php';
	require '../../include/crypt.php';
    require '../../include/functions.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->any('/','getPresentOrder');
    $app->response->headers->set('Content-Type', 'application/json;charset=utf8');
	$app->run();

	function getPresentOrder() {
		global $app, $crypt;
        $request = $app->request();
        if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_user_practice] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            echo json_encode($data);
            exit();
        }
        //	获取请求参数并判断合法性
        $user_id = $request->params('user_id');//用户id
        $type = $request->params('type');//请求类型：signup报名驾校今日订单 or  appoint预约学车今日订单
        if ($type != 'signup' && $type != 'appoint') {
            $data = array('code' => 102, 'data' => '参数错误');
            echo json_encode($data);
            exit();
        }
        $validate_result = validate(array('user_id'=>'INT', 'type'=>'STRING'), $request->params());
        if (!$validate_result['pass']) {
            echo json_encode($validate_result['data']);
            exit();
        }
         //现在时间戳
        $now = strtotime("now");
        //今日凌晨时间戳
        $day_start = strtotime(date('Y-m-d', time()));
        //前1天凌晨时间戳
        $one_days_ago = $day_start - 60*60*24; 
        //前2天凌晨时间戳
        $two_days_ago = $day_start - 2*60*60*24;
        //前3天凌晨时间戳
        $three_days_ago = $day_start - 3*60*60*24;
        //前4天凌晨时间戳
        $four_days_ago = $day_start - 4*60*60*24;
        //前5天凌晨时间戳
        $five_days_ago = $day_start - 5*60*60*24;
        //前6天凌晨时间戳
        $six_days_ago = $day_start - 6*60*60*24;
        $return = array();
        try{
            $db = getConnection();
            //根据传入的用户id查找用户驾校id
            $sql = "SELECT i.`school_id` FROM `cs_user` AS u LEFT JOIN `cs_users_info` AS i ON i.`user_id` = u.`l_user_id` WHERE (u.`i_user_type` = 2 AND u.`i_status` = 0) AND i.`user_id` = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $user_id);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $school_id = $res['school_id'];
            if (!$school_id) {//如果该用户在user表中类型不是2(校长)与并且在user_info表中没有与驾校绑定（即school不存在）
                $data = array('code' => 104, 'data' => '参数错误');
                echo json_encode($data);
                exit();
            }    
             //获取报名驾校今日和前六天订单信息
            $return['present_day'] = getSpecifiedOrder($school_id, $type, $day_start, $now);
            $return['one_days_ago'] = getSpecifiedOrder($school_id, $type, $one_days_ago, $day_start);
            $return['two_days_ago'] = getSpecifiedOrder($school_id, $type, $two_days_ago, $one_days_ago);
            $return['three_days_ago'] = getSpecifiedOrder($school_id, $type, $three_days_ago, $two_days_ago);
            $return['four_days_ago'] = getSpecifiedOrder($school_id, $type, $four_days_ago, $three_days_ago);
            $return['five_days_ago'] = getSpecifiedOrder($school_id, $type, $five_days_ago, $four_days_ago);
            $return['six_days_ago'] = getSpecifiedOrder($school_id, $type, $six_days_ago, $five_days_ago);
            $db = null;
            $data = array('code' => 200, 'data' => $return);
            echo json_encode($data);
            
        } catch (PDOException $e) {
            setapilog('get_present_order: params[user_id: ' . $user_id . ', type: ' . $type . '],error:' . $e->getMessage());
            $data = array('code' => 1, 'data' => '网络错误');
            echo json_encode($data);
        }

     }/*end function*/

      /**
     * 根据传入的驾校id，类型：报名驾校/预约学车 ，起始时间，结束时间，获取订单信息
     * @param  $school_id $type, $start $end 
     * @return 
     * @author sun
     */
    function getSpecifiedOrder($school_id, $type, $start, $end) {
        $orders = array();
        $total_money = '';
        $res = array();
        $db = getConnection();
        if ($type == 'signup') {//报名驾校类型
             $sql = "SELECT  `so_final_price`, `so_username`, `addtime` AS dt_time, `so_shifts_id`, `so_phone` FROM `cs_school_orders` WHERE `so_school_id` = :school_id AND `so_order_status` != 101 AND `so_order_status` != 3 AND (`addtime` BETWEEN :start AND :end)";
             $stmt = $db->prepare($sql);
             $stmt->bindParam('school_id', $school_id);
        } else {//预约学车类型
            //获取该驾校所有教练id
            $coach_ids = 0;
            $sql = "SELECT `l_coach_id` FROM `cs_coach` WHERE `s_school_name_id` = :school_id";
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
            $sql = "SELECT `s_user_name`, `dc_money`, `dt_order_time` AS dt_time, `s_lesson_name`, `s_lisence_name`, `s_user_name`, `s_user_phone` FROM `cs_study_orders` WHERE `l_coach_id` IN ( $coach_ids ) AND `i_status` != 101 AND `i_status` != 3 AND `dt_order_time` BETWEEN :start AND :end";
            $stmt = $db->prepare($sql);
        }

        $stmt->bindParam('start', $start);
        $stmt->bindParam('end', $end);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($res) {
            foreach ($res as $key => $value) {
                $res[$key]['dt_time'] = date('Y-m-d', $value['dt_time']);
            }
            $orders['order_info'] = $res;
            $orders['total_order'] = count($res);
            foreach ($res as $key => $value) {
                if ($type == 'signup') {
                    $total_money += $value['so_final_price'];
                    $sql = "SELECT `sh_title` FROM `cs_school_shifts` WHERE `id` = :shifts_id";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('shifts_id', $value['so_shifts_id']);
                    $stmt->execute();
                    $shifts_name = $stmt->fetch(PDO::FETCH_ASSOC);
                    $orders['order_info'][$key]['so_shifts_id'] = $shifts_name['sh_title'];

                } else {
                    $total_money += $value['dc_money'];
                }
            }
            $orders['total_money'] = sprintf("%.2f", $total_money);
            
            
        }
        return $orders;          
    }
 ?>