<?php  
    /**
     * 定时完成订单任务(学车后30分钟)
     * @return string AES对称加密（加密字段xhxueche）
     * @author chenxi
     **/
    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->get('/','finishOrders');
    $app->run();

    function finishOrders() {
        global $app, $crypt;
        $sql = "SELECT * FROM `cs_study_orders` WHERE `i_status` = 1";
        try {
            $db = getConnection();
            $stmt = $db->query($sql);
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // $date = date('Y-m-d', time());
            // $date_arr = explode('-', $date);
            // $year = $date_arr[0];
            // $month = $date_arr[1];
            // $day = $date_arr[2];
            if($row) {
                foreach ($row as $key => $value) {
                    $time_config_id = array_filter(explode(',', $value['time_config_id']));
                    if($time_config_id) {
                        $end_time = array();
                        
                        // 获取当前所预约的时间段
                        $sql = "SELECT `start_time`, `end_time` FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config_id).")";
                        $query = $db->query($sql);
                        $res = $query->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($res as $k => $v) {
                            $end_time[] = $v['end_time'];
                        }
                        // 获取当前被预约时间的年月日
                        $sql = "SELECT * FROM `cs_coach_appoint_time` WHERE `id` = ".$value['appoint_time_id'];
                        $stmt = $db->query($sql);
                        $appoint_time_config = $stmt->fetch(PDO::FETCH_ASSOC);
                        if($appoint_time_config) {
                            $year = $appoint_time_config['year'];
                            $month = $appoint_time_config['month'];
                            $day = $appoint_time_config['day'];
                            $current_date = $year.'-'.$month.'-'.$day.' '.max($end_time).':00';
                            // echo $current_date.'<br>';
                            $final_date = strtotime($current_date) + 3600*0.5;
                            // $dt_appoint_time = strtotime($value['dt_appoint_time']);
                            // $appoint_end_time = $dt_appoint_time + max($end_time) * 3600 * (1+0.5);
                            // var_dump($end_time);
                            // echo $final_date.'<br>';
                            // echo $appoint_end_time."<br>";
                            // echo time()."<br>";
                            // exit();
                            if(time() >= $final_date) {
                                $sql = "UPDATE `cs_study_orders` SET `i_status` = 2 WHERE `l_study_order_id` = ".$value['l_study_order_id'];
                                $rs = $db->query($sql);
                                if($rs) {
                                    $str = "当前订单ID：".$value['l_study_order_id']."，已自动完成！(".date('Y-m-d H:i', time()).")";
                                    setlog($str);
                                    echo "当前订单ID：".$value['l_study_order_id'].'，已自动完成！'.'<br>';
                                } else {
                                    $str = "当前订单ID：".$value['l_study_order_id']."，自动完成订单出错！(".date('Y-m-d H:i', time()).")";
                                    setlog($str);
                                    echo "当前订单ID：".$value['l_study_order_id'].'，自动完成订单出错！'.'<br>';
                                }
                            } else {
                                $str = "当前订单ID：".$value['l_study_order_id']."，不可自动完成的订单！(".date('Y-m-d H:i', time()).")";
                                setlog($str);
                                echo '当前订单ID：'.$value['l_study_order_id'].'，不可自动完成的订单！'.'<br>';
                            }
                        } else {
                            continue;
                        }
                            
                    } else {
                        // 自动完成订单
                        $sql = "UPDATE `cs_study_orders` SET `i_status` = 2 WHERE `l_study_order_id` = ".$value['l_study_order_id'];
                        $rs = $db->query($sql);
                        if($rs) {
                            $str = "当前订单ID：".$value['l_study_order_id']."，已自动完成！当前订单没有预约时间(".date('Y-m-d H:i', time()).")";
                            setlog($str);
                            echo "当前订单ID：".$value['l_study_order_id'].'，已自动完成！当前订单没有预约时间'.'<br>';
                        } else {
                            $str = "当前订单ID：".$value['l_study_order_id']."，已自动完成！当前订单没有预约时间(".date('Y-m-d H:i', time()).")";
                            setlog($str);
                            echo "当前订单ID：".$value['l_study_order_id'].'，，自动完成订单出错！当前订单没有预约时间'.'<br>';
                        }

                    }

                }
                /*
                echo "<script>
                     window.opener=null;
                     window.open('','_self');
                     window.close(); 
                    </script>";
                 */
            } else {
                /*
                echo "<script>
                        window.open('','_self','');
                        window.close();
                        </script>";
                 */
                echo '没有可自动完成的订单';
            }
        } catch(PDOException $e) {
            echo "ERROR";
        }
        
    }

    function setlog($word='') {
        $fp = fopen("dingshi.txt","a");
        flock($fp, LOCK_EX) ;
        fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time()+8*3600)."\n".$word."\n");
        flock($fp, LOCK_UN);
        fclose($fp);

    }

 ?>
