<?php
/**
 * 教练APP配置教练端时间配置
 * @param $coach_id int 教练ID
 * @param $time_config_id string 时间配置ID 1,2,3,4...
 * @param $date string 当前日期 例如：10-16
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
$app->post('/','setCoachTimeConfig');
$app->run();

// 设置教练的时间配置
function setCoachTimeConfig() {
    Global $app, $crypt, $lisence_config, $lesson_config;

    $req            = $app->request();
    $res            = $app->response();
    $coach_id       = $req->params('coach_id');         // 教练ID
    $time_config_id = $req->params('time_config_id');   // 时间配置ID
    $date_config    = $req->params('date');             // 当前日期

    if(!preg_match('|-|', $date_config) || $coach_id == '' || $date_config == '' || !isset($time_config_id)) {
        $data = array('code'=>-3, 'data'=>'参数错误');
        echo json_encode($data);
        exit();
    }

    // 获取时间设置的当前时间
    $year               = date('Y', time());                 // 年
    $current_time       = strtotime($year.'-'.$date_config); // 当前时间
    $date_config_arr    = explode('-', $date_config);
    $month              = (int)$date_config_arr[0];               // 月
    $day                = (int)$date_config_arr[1];               // 日

    if($month == 1) {
        $year = '2017';
    }

    $last_time_config_id = array();

    try {
        $db = getConnection();

        if(trim($time_config_id) == '') {
            /*
                // 如果time_config_id传值为空删除当前日期的时间配置
                $sql = "DELETE FROM `cs_current_coach_time_configuration` WHERE `year` = $year AND `month` = $month AND `day` = $day AND `coach_id` = $coach_id";
                $stmt = $db->query($sql);
                if($stmt) {
                    $data = array('code'=>200, 'data'=>'更新成功');
                } else {
                    $data = array('code'=>-8, 'data'=>'更新失败');
                }
                echo json_encode($data);
                exit();
             */

            // 查询这一天，有没有过自定义设置时间
            $sql = " SELECT 1 FROM `cs_current_coach_time_configuration` WHERE `year` = :y AND `month` = :m AND `day` = :d AND `coach_id`  = :cid ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('y', $year);
            $stmt->bindParam('m', $month);
            $stmt->bindParam('d', $day);
            $stmt->bindParam('cid', $coach_id);
            $stmt->execute();
            $coach_has_configured_this_day = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($coach_has_configured_this_day) {
                // 如果time_config_id传值为空则清空当前日期的时间配置
                $sql = " UPDATE `cs_current_coach_time_configuration` SET `time_config_money_id` = '', `time_config_id` = '', `time_lisence_config_id` = '', `time_lesson_config_id` = '', `updatetime` = :t WHERE `year` = :y AND `month` = :m AND `day` = :d AND `coach_id`  = :cid ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('y', $year);
                $stmt->bindParam('m', $month);
                $stmt->bindParam('d', $day);
                $stmt->bindParam('cid', $coach_id);
                $t = time();
                $stmt->bindParam('t', $t);
                $update_ok = $stmt->execute();
                if($update_ok) {
                    $data = array('code'=>200, 'data'=>'更新成功');
                } else {
                    $data = array('code'=>-8, 'data'=>'更新失败');
                }
            } else {
                $sql = " INSERT INTO `cs_current_coach_time_configuration` (`current_time`, `time_config_money_id`, `time_config_id`, `time_lisence_config_id`, `time_lesson_config_id`, `coach_id`, `year`, `month`, `day`, `addtime`, `updatetime`) VALUES (:current_t, :time_config_money, :time_config, :time_lisence_config, :time_lesson_config, :cid, :y, :m, :d, :addt, :updatet) ";
                $stmt = $db->prepare($sql);
                $t = time();
                $blank = '';
                $stmt->bindParam('current_t', $t);
                $stmt->bindParam('time_config_money', $blank);
                $stmt->bindParam('time_config', $blank);
                $stmt->bindParam('time_lisence_config', $blank);
                $stmt->bindParam('time_lesson_config', $blank);
                $stmt->bindParam('y', $year);
                $stmt->bindParam('m', $month);
                $stmt->bindParam('d', $day);
                $stmt->bindParam('cid', $coach_id);
                $stmt->bindParam('addt', $t);
                $stmt->bindParam('updatet', $t);
                $stmt->execute();
                $last_time_config_id = $db->lastInsertId();
                if ($last_time_config_id > 0) {
                    $data = array('code'=>200, 'data'=>'更新成功');
                } else {
                    $data = array('code'=>-8, 'data'=>'更新失败');
                }
            }

            echo json_encode($data);
            exit();
        }

        $time_config = array_values(array_filter(explode(',', $time_config_id)));
        // 找到教练所属驾校
        $sql = "SELECT `s_school_name_id` FROM `cs_coach` WHERE `l_coach_id` = $coach_id";
        $stmt = $db->query($sql);
        $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
        if(empty($coach_info)) {
            $data = array('code'=>-1, 'data'=>'参数错误');
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
            $s_time_list = isset($school_config['s_time_list']) ? array_filter(explode(',', $school_config['s_time_list'])) : array();
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

        // 判断时间段ID是否有被预约了
        //$sql = "SELECT * FROM `cs_coach_appoint_time` WHERE `coach_id` = $coach_id AND `month` = $month AND `day` = $day";
        $sql = "SELECT a.`time_config_id` FROM `cs_study_orders` as o LEFT JOIN `cs_coach_appoint_time` as a ON a.`id` = o.`appoint_time_id` WHERE a.`id` != '' AND a.`coach_id` = $coach_id AND o.`i_status` != 3 AND a.`month` = $month AND a.`day` = $day";
        $stmt = $db->query($sql);
        $appoint_time = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $time_config_id_arr = array();
        $time_final_appoint_config = array();

        if($appoint_time) {
            foreach ($appoint_time as $key => $value) {
                $time_config_id_arr[] = explode(',', $value['time_config_id']);
            }
            foreach ($time_config_id_arr as $k => $v) {
                if(is_array($v)) {
                    $v = array_filter($v);
                    foreach ($v as $e => $t) {
                        $time_final_appoint_config[] = $t;
                    }
                }
            }
        }
        $time_final_appoint_config = array_merge(array_unique($time_final_appoint_config));
        $final_time_config = array_diff($time_config, $time_final_appoint_config);
            /*
            if(empty($final_time_config)) {
                $data = array('code'=>-4, 'data'=>'你选择的时间已被学员预约！');
                echo json_encode($data);
                exit();
            }
             */

        // 获取教练当前的时间配置
        $tbl = DBPREFIX.'current_coach_time_configuration';
        $sql = " SELECT * FROM `{$tbl}` WHERE `year` = :y AND `month` = :m AND `day` = :d AND `coach_id` = :cid ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('y', $year);
        $stmt->bindParam('m', $month);
        $stmt->bindParam('d', $day);
        $stmt->bindParam('cid', $coach_id);
        $stmt->execute();
        $coach_current_time_config = $stmt->fetch(PDO::FETCH_ASSOC);

        // save in the middle
        $time_money_config = array();
        $time_license_config = array();
        $time_lesson_config = array();

        if ($time_config) {
            if ($coach_current_time_config) {
                $_time_config_money_id = json_decode($coach_current_time_config['time_config_money_id'], true);
                $_time_lisence_config_id = json_decode($coach_current_time_config['time_lisence_config_id'], true);
                $_time_lesson_config_id = json_decode($coach_current_time_config['time_lesson_config_id'], true);
                foreach ($time_config as $index => $config) {
                    if ( isset($_time_config_money_id[$config]) &&
                        isset($_time_lisence_config_id[$config]) &&
                        isset($_time_lesson_config_id[$config])) {

                            $time_money_config[$config] = $_time_config_money_id[$config];
                            $time_license_config[$config] = $_time_lisence_config_id[$config];
                            $time_lesson_config[$config] = $_time_lesson_config_id[$config];
                            $last_time_config_id[] = $config;
                            unset($time_config[$index]);
                        }
                } // 从旧的教练配置已知列表中获取最新的一部分列表
            }
        }

        // 假如有些时段之前是没有设置的，现在重新设置为开放，需要从驾校的配置中获取
        if ($time_config) {
            // 获取当前驾校的时间配置
            $sql = "SELECT * FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config).")";
            $stmt = $db->query($sql);
            $coach_time_config_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(empty($coach_time_config_info)) {
                $data = array('code'=>-2, 'data'=>'当前驾校没有时间配置');
                echo json_encode($data);
                exit();
            }
            //$time_money_config = array();
            //$time_license_config = array();
            //$time_lesson_config = array();

            // 获取价格，牌照，科目
            foreach ($coach_time_config_info as $key => $value) {

                $time_money_config[$value['id']] = $value['price'];
                $time_license_config[$value['id']] = $value['license_no'];
                $time_lesson_config[$value['id']] = $value['subjects'];
                $last_time_config_id[] = $value['id'];

                // 牌照按照基础信息设置
                if(count($s_coach_lisence_id_list) == 1) {
                    if (array_key_exists($s_coach_lisence_id_list[0], $lisence_config)) {
                        $time_license_config[$value['id']] = $lisence_config[$s_coach_lisence_id_list[0]];
                    }
                }
                if(count($s_coach_lesson_id_list) == 1) {
                    if (array_key_exists($s_coach_lesson_id_list[0], $lesson_config)) {
                        $time_lesson_config[$value['id']] = $lesson_config[$s_coach_lesson_id_list[0]];
                    }
                }

                // 如果教练设置了上午时间和下午时间
                if(!empty($s_am_time_list) && !empty($s_pm_time_list)) {
                    if(in_array($value['id'], $s_am_time_list)) {
                        if($s_am_subject == 1) {
                            $time_lesson_config[$value['id']] = '科目一';

                        } else if($s_am_subject == 2) {
                            $time_lesson_config[$value['id']] = '科目二';

                        } else if($s_am_subject == 3) {
                            $time_lesson_config[$value['id']] = '科目三';

                        } else if($s_am_subject == 4) {
                            $time_lesson_config[$value['id']] = '科目四';

                        }
                    }

                    if(in_array($value['id'], $s_pm_time_list)) {

                        if($s_pm_subject == 1) {
                            $time_lesson_config[$value['id']] = '科目一';

                        } else if($s_pm_subject == 2) {
                            $time_lesson_config[$value['id']] = '科目二';

                        } else if($s_pm_subject == 3) {
                            $time_lesson_config[$value['id']] = '科目三';

                        } else if($s_pm_subject == 4) {
                            $time_lesson_config[$value['id']] = '科目四';

                        }
                    }

                    // 教练没有设置，驾校有设置
                } else {
                    if($value['end_time'] <= 12) {
                        if($s_am_subject == 1) {
                            $time_lesson_config[$value['id']] = '科目一';

                        } else if($s_am_subject == 2) {
                            $time_lesson_config[$value['id']] = '科目二';

                        } else if($s_am_subject == 3) {
                            $time_lesson_config[$value['id']] = '科目三';

                        } else if($s_am_subject == 4) {
                            $time_lesson_config[$value['id']] = '科目四';

                        }
                    } else {

                        if($s_pm_subject == 1) {
                            $time_lesson_config[$value['id']] = '科目一';

                        } else if($s_pm_subject == 2) {
                            $time_lesson_config[$value['id']] = '科目二';

                        } else if($s_pm_subject == 3) {
                            $time_lesson_config[$value['id']] = '科目三';

                        } else if($s_pm_subject == 4) {
                            $time_lesson_config[$value['id']] = '科目四';

                        }
                    }
                }

            }
        }

        // 时间ID与价格json
        $time_config_money_id = json_encode($time_money_config);

        // 时间对应牌照json
        $time_license_config_id = json_encode($time_license_config);

        // 时间科目对应json
        $time_lesson_config_id = JSON($time_lesson_config);

        // 时间配置id
        $final_time_config = $last_time_config_id;

        // 查找有无当前时间配置
        $sql = "SELECT * FROM `cs_current_coach_time_configuration` WHERE `year` = $year AND `month` = $month AND `day` = $day AND `coach_id` = $coach_id";
        $stmt = $db->query($sql);
        $coach_configuration_info = $stmt->fetch(PDO::FETCH_ASSOC);
        if($coach_configuration_info) {
            // 更新当前时间配置
            $sql = "UPDATE `cs_current_coach_time_configuration` SET ";
            $sql .= "`time_config_money_id`     = '".$time_config_money_id."', ";
            $sql .= "`time_config_id`             = '".implode(',', $final_time_config)."', ";
            $sql .= "`time_lisence_config_id`     = '".$time_license_config_id."', ";
            $sql .= "`time_lesson_config_id`     = '".$time_lesson_config_id."', ";
            $sql .= "`updatetime`                 = '".time()."' ";
            $sql .= " WHERE `year` = $year AND `month` = $month AND `day` = $day AND `coach_id` = $coach_id";
            $stmt = $db->query($sql);
            if($stmt) {
                $data = array('code'=>200, 'data'=>'更新成功！');
            } else {
                $data = array('code'=>-6, 'data'=>'更新失败');
            }

        } else {
            $sql = "INSERT INTO `cs_current_coach_time_configuration` (`current_time`, `time_config_money_id`, `time_config_id`, `time_lisence_config_id`, `time_lesson_config_id`, `coach_id`, `year`, `month`, `day`, `addtime`) VALUES (";
            $sql .= "'".$current_time."','".$time_config_money_id."','".implode(',', $final_time_config)."','".$time_license_config_id."','".$time_lesson_config_id."','".$coach_id."','".$year."','".$month."','".$day."','".time()."')";
            $stmt = $db->query($sql);
            if($stmt) {
                $data = array('code'=>200, 'data'=>'更新成功！');
            } else {
                $data = array('code'=>-7, 'data'=>'更新失败');
            }
        }
        $db = null;
        echo json_encode($data);
        exit();

    } catch(PDOException $e) {
        slimLog($req, $res, $e, 'PDO数据库出错');
        $data = array('code'=>1, 'data'=>'网络错误');
        echo json_encode($data);
        exit;
        } catch(ErrorException $e) {
            slimLog($req, $res, $e, '解析出错');
            $data = array('code'=>1, 'data'=>'网络异常');
            echo json_encode($data);
            exit;
        }
    }

    /**************************************************************
     *
     *  使用特定function对数组中所有元素做处理
     *  @param  string  &$array     要处理的字符串
     *  @param  string  $function   要执行的函数
     *  @return boolean $apply_to_keys_also     是否也应用到key上
     *  @access public
     *
     *************************************************************/
    function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }

            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }

    /**************************************************************
     *
     *  将数组转换为JSON字符串（兼容中文）
     *  @param  array   $array      要转换的数组
     *  @return string      转换得到的json字符串
     *  @access public
     *
     *************************************************************/
    function JSON($array) {
        arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }

?>
