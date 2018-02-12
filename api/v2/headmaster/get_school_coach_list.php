<?php 

	/**
	 * 校长APP获取教练列表接口
	 * @param $user_id 登录id $page
	 * @return 累计学员$student_num 总订单$history_order_num 本月订单$month_order_num 今日订单$today_order_num 教练姓名$coach_name
	 * @author sunweiwei
	 **/

	require '../../Slim/Slim.php';
	require '../../include/common.php';
	require '../../include/crypt.php';
    require '../../include/functions.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->any('/','getCoachList');
    $app->response->headers->set('Content-Type', 'application/json;charset=utf8');
	$app->run();

	function getCoachList() {
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
        $page = $request->params('page');
        $validate_result = validate(array('user_id'=>'INT','page'=>'INT'), $request->params());
        if ($page == 0) {
            $page = 1;
        }
        if (!$validate_result['pass']) {
            echo json_encode($validate_result['data']);
            exit();
        }
        $limit = 20;
        $start = ($page - 1) * $limit;

        try{
            $coach_list = array();
            $db = getConnection();
            //根据传入的用户id查找用户驾校id
            $sql = "SELECT i.`school_id` FROM `cs_user` AS u LEFT JOIN `cs_users_info` AS i ON i.`user_id` = u.`l_user_id` WHERE (u.`i_user_type` = 2 AND u.`i_status` = 0) AND i.`user_id` = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $user_id);
            $stmt->execute();
            $school_id = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$school_id) {//如果该用户在user表中类型不是2(驾校)与并且在user_info表中没有与驾校绑定（即school不存在）
                $data = array('code' => 102, 'data' => '参数错误');
                echo json_encode($data);
                exit();
            }
            //根据驾校id，查找驾校所有教练
            $sql = "SELECT `l_coach_id`, `s_coach_name`, `s_coach_imgurl` FROM `cs_coach` WHERE `s_school_name_id` = :school_id";
            $sql .= " LIMIT $start, $limit";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('school_id', $school_id['school_id']);
            $stmt->execute();
            $coach_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$coach_info) {//如果根据驾校id没有获取到教练信息
                $data = array('code' => 104, 'data' => '参数错误');
                echo json_encode($data);
                exit();
            }
            foreach ($coach_info as $key => $value) {
                $coach_list[$key]['coach_name'] = $value['s_coach_name'];
                $coach_list[$key]['coach_imgurl'] = S_HTTP_HOST.$value['s_coach_imgurl'];
                //查找每位教练相关的信息
                $coach_id = $value['l_coach_id'];

                //统计教练的累计学员
                $sql = "SELECT COUNT(`l_user_id`) AS c FROM `cs_study_orders` WHERE `l_coach_id` = :coach_id AND `i_status` != 3 AND `i_status` != 101 GROUP BY `l_user_id`";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('coach_id', $coach_id);
                $stmt->execute();
                $student_num = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($student_num) {
                    $coach_list[$key]['student_num'] = $student_num['c'];
                } else {
                    $coach_list[$key]['student_num'] = 0;
                }

                //统计教练总订单数
                $sql = "SELECT COUNT(`s_order_no`) AS n FROM `cs_study_orders` WHERE `l_coach_id` = :coach_id AND `i_status` != 3 AND `i_status` != 101";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('coach_id', $coach_id);
                $stmt->execute();
                $history_order_num = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($history_order_num) {
                    $coach_list[$key]['history_order_num'] = $history_order_num['n'];
                } else {
                    $coach_list[$key]['history_order_num'] = 0;
                }

                //统计教练今日订单数
                //今日凌晨时间戳
                $day_start = strtotime(date('Y-m-d', time()));
                //现在时间戳
                $now = strtotime("now"); 
                //本月第一天凌晨时间戳
                $month_start = strtotime(date('Y-m', time()));
                $sql = "SELECT COUNT(`s_order_no`) AS today_n FROM `cs_study_orders` WHERE `l_coach_id` = :coach_id AND `i_status` != 3 AND `i_status` != 101 AND `dt_order_time` BETWEEN $day_start AND $now";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('coach_id', $coach_id);
                $stmt->execute();
                $today_order_num = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($today_order_num) {
                    $coach_list[$key]['today_order_num'] = $today_order_num['today_n'];
                } else {
                    $coach_list[$key]['today_order_num'] = 0;
                }

                //统计教练本月订单数
                $sql = "SELECT COUNT(`s_order_no`) AS month_n FROM `cs_study_orders` WHERE `l_coach_id` = :coach_id AND `i_status` != 3 AND `i_status` != 101 AND `dt_order_time` BETWEEN $month_start AND $now";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('coach_id', $coach_id);
                $stmt->execute();
                $month_order_num = $stmt->fetch(PDO::FETCH_ASSOC); 
                if ($month_order_num) {
                    $coach_list[$key]['month_order_num'] = $month_order_num['month_n'];
                } else {
                    $coach_list[$key]['month_order_num'] = 0;
                }
                
            }

            $db = null;
            $data = array('code' => 200, 'data' => $coach_list);
            echo json_encode($data);

        } catch (PDOException $e) {
            setapilog('get_user_practice: params[user_id: ' . $user_id . '],error:' . $e->getMessage());
            $data = array('code' => 1, 'data' => '网络错误');
            echo json_encode($data);
        }


	}/*end function*/ 

 ?>