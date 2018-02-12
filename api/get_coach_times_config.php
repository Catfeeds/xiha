<?php
/**
 * 获取教练时间配置
 * @param $coach_id 教练ID
 * @param $date 时间段 10-18
 * @return string AES对称加密（加密字段xhxueche）
 * @author chenxi
 **/

require 'Slim/Slim.php';
require 'include/common.php';
require 'include/crypt.php';
require 'include/functions.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
$app->post('/','getCoachTime');
$app->run();

// 获取时间配置
function getCoachTime() {
    Global $app, $crypt, $lisence_config, $lesson_config;
    $req = $app->request();
    $res = $app->response();
    $coach_id = $req->params('coach_id');
    $date = $req->params('date');

    $year = date('Y', time()); //年
    // 如果没有传日期，则取当前日期
    if(!isset($date) || empty($date)) {
        $month = intval(date('m', time())); //月
        $day = intval(date('d', time())); //日
        $date = $month.'-'.$day;
    }

    if(empty($coach_id)) {
        $data = array('code'=>-2, 'data'=>'参数错误');
        echo json_encode($data);
        exit();
    }

    if(!preg_match('|-|', $date)) {
        $data = array('code'=>-1, 'data'=>'日期格式错误');
        echo json_encode($data);
        exit();
    }

    $date = explode('-', $date);
    $month = $date[0];
    $day = $date[1];

    try {
        $db = getConnection();

        $list = array();
        // 获取驾校设置的时间配置
        $sql = "SELECT `s_school_name_id` FROM `cs_coach` WHERE `l_coach_id` = $coach_id";
        $stmt = $db->query($sql);
        $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
        if(empty($coach_info)) {
            $data = array('code'=>-3, 'data'=>'参数错误');
            echo json_encode($data);
            exit();
        }

        // 获取驾校的时间配置
        $sql = "SELECT `s_time_list`, `is_automatic` FROM `cs_school_config` WHERE `l_school_id` = '{$coach_info['s_school_name_id']}'";
        $stmt = $db->query($sql);
        $school_config = $stmt->fetch(PDO::FETCH_ASSOC);
        $s_time_list = array();
        $is_automatic = 1;
        if($school_config) {
            $s_time_list = isset($school_config['s_time_list']) ? explode(',', $school_config['s_time_list']) : array();
            $is_automatic = $school_config['is_automatic'];
        }

        // 获取教练时间配置
        $sql = "SELECT `s_am_subject`, `s_pm_subject`, `s_am_time_list`, `s_pm_time_list`, `s_coach_lisence_id`, `s_coach_lesson_id` FROM `cs_coach` WHERE `l_coach_id` = '{$coach_id}'";
        $stmt = $db->query($sql);
        $_coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
        $s_am_subject = 2;
        $s_pm_subject = 3;
        $s_am_time_list = array();
        $s_pm_time_list = array();
        $s_coach_lisence_id_list = array();
        $s_coach_lesson_id_list = array();

        if($_coach_info) {
            $s_am_subject = $_coach_info['s_am_subject'];
            $s_pm_subject = $_coach_info['s_pm_subject'];
            $s_am_time_list = isset($_coach_info['s_am_time_list']) ? explode(',', $_coach_info['s_am_time_list']) : array();
            $s_pm_time_list = isset($_coach_info['s_pm_time_list']) ? explode(',', $_coach_info['s_pm_time_list']) : array();
            $s_coach_lisence_id_list = isset($_coach_info['s_coach_lisence_id']) ? explode(',', $_coach_info['s_coach_lisence_id']) : array();
            $s_coach_lesson_id_list = isset($_coach_info['s_coach_lesson_id']) ? explode(',', $_coach_info['s_coach_lesson_id']) : array();
        }

        if(!empty($s_am_time_list) && !empty($s_pm_time_list)) {
            $time_config_ids_arr = array_merge($s_am_time_list, $s_pm_time_list);
        }  else {
            $time_config_ids_arr = $s_time_list;
        }

        $sql = " SELECT * FROM `cs_coach_time_config` WHERE `status` = 1 ";

        $time_config_ids_arr = array_filter($time_config_ids_arr);
        if(!empty($time_config_ids_arr)) {
            $sql .= " AND `id` IN (".implode(',', $time_config_ids_arr).")";
        }

        $stmt = $db->query($sql);
        $coach_time_config = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(empty($coach_time_config)) {
            $data = array('code'=>-4, 'data'=>'你所属的驾校还未设置时间段，请联系你所属驾校！');
            echo json_encode($data);
            exit();
        }
        // 获取7天日期
        $date_config = getCoachTimeConfig();
        $list['date_config'] = $date_config;

        // 获取教练被预约时间ID
        $sql = "SELECT a.`time_config_id` FROM `cs_study_orders` as o LEFT JOIN `cs_coach_appoint_time` as a ON a.`id` = o.`appoint_time_id` WHERE a.`id` != '' AND a.`coach_id` = $coach_id AND o.`i_status` != 3 AND a.`month` = $month AND a.`day` = $day AND a.`year` = $year";
        $stmt = $db->query($sql);
        $time_config_id_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $time_config_ids = array();
        if($time_config_id_arr) {
            foreach ($time_config_id_arr as $key => $value) {
                $time_config_ids = array_merge($time_config_ids,
                    array_filter(
                        explode(',',
                        $value['time_config_id'])
                    )
                );
            }
        }

        // 获取当前教练所设置的时间端配置
        $sql = "SELECT * FROM `cs_current_coach_time_configuration` WHERE `coach_id` = $coach_id AND `month` = $month AND `day` = $day";
        $stmt = $db->query($sql);
        $current_time_config = $stmt->fetch(PDO::FETCH_ASSOC);
        $time_config_id = array();

        // 设置时间配置已生成
        if($current_time_config) {
            $time_config_id = explode(',', $current_time_config['time_config_id']);
            $time_lisence_config_id = json_decode($current_time_config['time_lisence_config_id'], true);
            $time_lesson_config_id = json_decode($current_time_config['time_lesson_config_id'], true);
            $time_config_money_id = json_decode($current_time_config['time_config_money_id'], true);

            // 所有教练时间配置表 coach_time_config
            foreach ($coach_time_config as $key => $value) {

                if(in_array($value['id'], $time_config_id)) {
                    $coach_time_config[$key]['is_set'] = 1; // 设置
                    $coach_time_config[$key]['price'] = $time_config_money_id[$value['id']];
                    $coach_time_config[$key]['license_name'] = $time_lisence_config_id[$value['id']];
                    $coach_time_config[$key]['subjects'] = $time_lesson_config_id[$value['id']];
                } else {
                    $coach_time_config[$key]['is_set'] = 2; // 未设置
                    $coach_time_config[$key]['subjects'] = $value['subjects'];
                }
                $coach_time_config[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);

                // 获取当前时间段是否被预约
                if(in_array($value['id'], $time_config_ids)) {
                    $coach_time_config[$key]['is_appointed'] = 1; //被预约
                } else {
                    $coach_time_config[$key]['is_appointed'] = 2; //没被预约
                }
            }

            // 没有设置时间配置默认生成
        } else {
            foreach ($coach_time_config as $key => $value) {
                $coach_time_config[$key]['is_set'] = 1;

                // 牌照按照基础信息设置
                if(count($s_coach_lisence_id_list) == 1) {
                    if (array_key_exists($s_coach_lisence_id_list[0], $lisence_config)) {
                        $coach_time_config[$key]['license_no'] = $lisence_config[$s_coach_lisence_id_list[0]];
                    }
                }
                if(count($s_coach_lesson_id_list) == 1) {
                    if (array_key_exists($s_coach_lesson_id_list[0], $lesson_config)) {
                        $coach_time_config[$key]['subjects'] = $lesson_config[$s_coach_lesson_id_list[0]];
                    }
                }

                // 如果教练设置了上午时间和下午时间
                if(!empty($s_am_time_list) && !empty($s_pm_time_list)) {
                    if(in_array($value['id'], $s_am_time_list)) {
                        if($s_am_subject == 1) {
                            $coach_time_config[$key]['subjects'] = '科目一';

                        } else if($s_am_subject == 2) {
                            $coach_time_config[$key]['subjects'] = '科目二';

                        } else if($s_am_subject == 3) {
                            $coach_time_config[$key]['subjects'] = '科目三';

                        } else if($s_am_subject == 4) {
                            $coach_time_config[$key]['subjects'] = '科目四';

                        }
                    }

                    if(in_array($value['id'], $s_pm_time_list)) {

                        if($s_pm_subject == 1) {
                            $coach_time_config[$key]['subjects'] = '科目一';

                        } else if($s_pm_subject == 2) {
                            $coach_time_config[$key]['subjects'] = '科目二';

                        } else if($s_pm_subject == 3) {
                            $coach_time_config[$key]['subjects'] = '科目三';

                        } else if($s_pm_subject == 4) {
                            $coach_time_config[$key]['subjects'] = '科目四';

                        }
                    }

                    // 教练没有设置，驾校有设置
                } else {
                    if($value['end_time'] <= 12) {
                        if($s_am_subject == 1) {
                            $coach_time_config[$key]['subjects'] = '科目一';

                        } else if($s_am_subject == 2) {
                            $coach_time_config[$key]['subjects'] = '科目二';

                        } else if($s_am_subject == 3) {
                            $coach_time_config[$key]['subjects'] = '科目三';

                        } else if($s_am_subject == 4) {
                            $coach_time_config[$key]['subjects'] = '科目四';

                        }
                    } else {

                        if($s_pm_subject == 1) {
                            $coach_time_config[$key]['subjects'] = '科目一';

                        } else if($s_pm_subject == 2) {
                            $coach_time_config[$key]['subjects'] = '科目二';

                        } else if($s_pm_subject == 3) {
                            $coach_time_config[$key]['subjects'] = '科目三';

                        } else if($s_pm_subject == 4) {
                            $coach_time_config[$key]['subjects'] = '科目四';

                        }
                    }
                }

                // 获取当前时间段是否被预约
                // 获取当前教练当前时间被预约时间ID数组
                if(in_array($value['id'], $time_config_ids)) {
                    $coach_time_config[$key]['is_appointed'] = 1; //被预约
                } else {
                    $coach_time_config[$key]['is_appointed'] = 2; //没被预约
                }
            }
        }
        /**
         * 获取这一天,该教练的被预约信息
         */
        $sql = " SELECT * FROM `cs_coach_appoint_time` WHERE coach_id = :cid AND year = :y AND month = :m AND day = :d ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('cid', $coach_id);
        $stmt->bindParam('y', $year);
        $stmt->bindParam('m', $month);
        $stmt->bindParam('d', $day);
        $stmt->execute();
        $appoint_result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $appoint_time_id_list = array();
        if ($appoint_result) {
            foreach ($appoint_result as $index => $appoint) {
                if (isset($appoint->id)) {
                    $appoint_time_id_list[] = intval($appoint->id);
                }
            }
        }

        $order_list = array();
        if (!empty($appoint_time_id_list)) {
            $appoint_time_id = "('".implode("','", $appoint_time_id_list)."')";
            $order_status = "('".implode("','", array(1, 2, 1001, 1004, 1003, 1006))."')";
            $sql = " SELECT l_study_order_id as order_id, i_status as order_status, time_config_id, l_user_id as user_id, s_user_name as user_name, s_user_phone as user_phone FROM `cs_study_orders` WHERE l_coach_id = :cid AND appoint_time_id IN {$appoint_time_id} AND i_status IN {$order_status} ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid', $coach_id);
            $stmt->execute();
            $order_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $student_list = array();
        $order_status_config = array(
            '1'    => '已付款',
            '2'    => '已完成',
            '1001' => '付款中',
            '1003' => '未付款',
            '1004' => '取消处理中',
            '1006' => '退款处理中',
        );
        if (!empty($order_list)) {
            foreach ($order_list as $i => $order) {
                $time_config_ids = array_filter(explode(',', $order['time_config_id']));
                if (!empty($time_config_ids)) {
                    foreach ($time_config_ids as $j => $time_config_id) {
                        if (isset($order_status_config[$order['order_status']])) {
                            $order['order_status_text'] = $order_status_config[$order['order_status']];
                        } else {
                            $order['order_status_text'] = '未知状态';
                        }
                        $order['time_config_id'] = $time_config_id;
                        $student_list[] = $order;
                    }
                }
            }
        }

        if (is_array($coach_time_config) && count($coach_time_config) > 0) {
            // 排序
            usort($coach_time_config, function($a, $b) {
                if ($a['start_time'] == $b['start_time']) {
                    return 0;
                }
                return ($a['start_time'] < $b['start_time']) ? -1 : 1;
            });

            foreach ($coach_time_config as $config_index => $time_config) {
                if (isset($time_config['price']) && intval($time_config['price']) >= 1) {
                    $coach_time_config[$config_index]['price'] = intval($time_config['price']);
                }
                // add 学员姓名,学员手机,订单状态 for tmp
                if (!empty($student_list)) {
                    foreach ($student_list as $student_index => $student) {
                        if ($student['time_config_id'] == $time_config['id']) {
                            $coach_time_config[$config_index]['user_name'] = $student['user_name'];
                            $coach_time_config[$config_index]['user_phone'] = $student['user_phone'];
                            $coach_time_config[$config_index]['order_status'] = $student['order_status'];
                            $coach_time_config[$config_index]['order_status_text'] = $student['order_status_text'];
                            break;
                        }
                        $coach_time_config[$config_index]['user_name'] = '';
                        $coach_time_config[$config_index]['user_phone'] = '';
                        $coach_time_config[$config_index]['order_status'] = '';
                        $coach_time_config[$config_index]['order_status_text'] = '';
                    }
                } else {
                    $coach_time_config[$config_index]['user_name'] = '';
                    $coach_time_config[$config_index]['user_phone'] = '';
                    $coach_time_config[$config_index]['order_status'] = '';
                    $coach_time_config[$config_index]['order_status_text'] = '';
                }
            }
        }

        $list['time_list'] = $coach_time_config;
        $db = null;
        $data = array('code'=>200, 'data'=>$list);
        echo json_encode($data);

    } catch(PDOException $e) {
        slimLog($req, $res, $e, 'PDO数据库错误');
        $data = array('code'=>1, 'data'=>'网络错误');
        echo json_encode($data);
        exit();
    } catch(ErrorException $e) {
        slimLog($req, $res, $e, '解析出错');
        $data = array('code'=>1, 'data'=>'网络异常');
        echo json_encode($data);
        exit();
    }
}

/**
 * 生成可变的日期配置
 * @param $id int 教练ID
 * @return array
 */
function getCoachTimeConfig() {
    $current_time = time();
    $year = date('Y', $current_time); //年
    $month = intval(date('m', $current_time)); //月
    $day = intval(date('d', $current_time)); //日

    // 构建一个时间
    $build_date_timestamp = mktime(0,0,0,$month,$day,$year);

    // 循环7天日期
    $date_config = array();
    for($i = 0; $i <= 6; $i++) {
        $date_config[$i]['date'] = date('m-d', $build_date_timestamp + ( 24 * 3600 * $i));
        $date_config[$i]['month'] = intval(date('m', $build_date_timestamp + ( 24 * 3600 * $i)));
        $date_config[$i]['day'] = intval(date('d', $build_date_timestamp + ( 24 * 3600 * $i)));
        $date_config[$i]['date_format'] = intval(date('m', $build_date_timestamp + ( 24 * 3600 * $i))).'-'.intval(date('d', $build_date_timestamp + ( 24 * 3600 * $i)));

    }
    return $date_config;
}
?>
