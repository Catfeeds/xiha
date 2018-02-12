<?php  

    /**
     * 搜索接口
     * @param $name string 名称
     * @param $type int 类型 1: 驾校 2：教练
     * @param $city_id int 城市id
     * @param $lng string 经度
     * @param $lat string 纬度
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
    $app->post('/','search');
    $app->run();

    // 搜索
    function search() {
        global $app, $crypt;
        $request = $app->request();
        $name = $request->params('name');
        // 去掉关键词左右两边的空白字符
        $name = trim($name);
        $type = $request->params('type');
        $city_id = $request->params('city_id');
        $lng = $request->params('lng');
        $lat = $request->params('lat');

        $lng = isset($lng) ? $lng : '117.201239';
        $lat = isset($lat) ? $lat : '31.856717';
        if($name == '' || $type == '' || $city_id == '' || $lng == '' || $lat == '') {
            $data = array('code'=>-2, 'data'=>'参数错误');
            echo json_encode($data);
            exit();
        }

        if($type == 1) {
            // 搜索驾校
            $sql = "SELECT `l_school_id` FROM `cs_school` WHERE `s_school_name` LIKE '%".$name."%' AND `city_id` = '".$city_id."'";

            try {
                $db = getConnection();
                $stmt = $db->query($sql);
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $school_id = array();
                foreach ($row as $key => $value) {
                    $school_id[] = $value['l_school_id'];
                }

                if(!$school_id) {
                    $data = array('code'=>200, 'data'=>array());
                    echo json_encode($data);
                    exit();
                }

                // 查找教练列表
                $sql = "SELECT s.`name` as `s_coach_car_name`, s.`car_type`, h.* FROM `cs_coach` as h LEFT JOIN `cs_cars` as s ON s.`id` = h.`s_coach_car_id` WHERE h.`s_school_name_id` IN (".implode(',', $school_id).") AND h.`order_receive_status` = 1 ";
                // order_receive_status = 1-接单 0-不接
                $stmt = $db->query($sql);
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($res as $key => $value) {
                    // 车名和车类型为null
                    if ((string)$value['s_coach_car_name'] != '') {
                        $res[$key]['s_coach_car_name'] = (string)$value['s_coach_car_name'];
                    } else {
                        $res[$key]['s_coach_car_name'] = '';
                    }
                    if (intval($value['car_type']) >= 0) {
                        $res[$key]['car_type'] = intval($value['car_type']);
                    } else {
                        $res[$key]['car_type'] = 1;
                    }

                    $distance = getDistance($lat, $lng, $value['dc_coach_distance_y'], $value['dc_coach_distance_x'], $value['l_coach_id']);
                    $res[$key]['coach_student_distance'] = $distance;    
                    // 获取驾校名称
                    $sql = "SELECT * FROM `cs_school` WHERE `l_school_id` = '".$value['s_school_name_id']."'";
                    $stmt = $db->query($sql);
                    $school_name = $stmt->fetch(PDO::FETCH_ASSOC);
                    if($school_name) {
                        $res[$key]['s_school_name'] = $school_name['s_school_name'];
                    } else {
                        $res[$key]['s_school_name'] = '嘻哈驾校';
                    }

                    if(file_exists(__DIR__.'/../sadmin/'.$value['s_coach_imgurl'])) {
                        $res[$key]['s_coach_imgurl'] = S_HTTP_HOST.$value['s_coach_imgurl'];
                    } else {
                        $res[$key]['s_coach_imgurl'] = HTTP_HOST.$value['s_coach_imgurl'];
                    }
                }

                $db = null;
                $data = array('code'=>200, 'data'=>$res);
                echo json_encode($data);
                exit();
            } catch(PDOException $e) {
                setapilog('search_coach:params[name:'.$name.',type:'.$type.',city_id:'.$city_id.',lng:'.$lng.',lat:'.$lat.'], error:'.$e->getMessage());
                $data = array('code'=>1, 'data'=>'网络错误');
                echo json_encode($data);
                exit;
            } catch(ErrorException $e) {
                setapilog('search_coach:params[name:'.$name.',type:'.$type.',city_id:'.$city_id.',lng:'.$lng.',lat:'.$lat.'], error:'.$e->getMessage());
                $data = array('code'=>1, 'data'=>'网络错误');
                echo json_encode($data);
                exit;
            }

        } elseif ($type == 2) {
            // 搜索教练
            $sql = "SELECT c.* FROM `cs_coach` AS c LEFT JOIN `cs_school` AS s ON c.`s_school_name_id` = s.`l_school_id` WHERE c.`s_coach_name` LIKE '%".$name."%' AND s.`city_id` = '".$city_id."' AND c.`order_receive_status` = 1 ";
            // order_receive_status = 1-接单 0-不接
            try {
                $db = getConnection();
                $stmt = $db->query($sql);
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if(!$row) {
                    $data = array('code'=>200, 'data'=>array());
                    echo json_encode($data);
                    exit();
                }
                foreach ($row as $key => $value) {

                    if(file_exists(__DIR__.'/../sadmin/'.$value['s_coach_imgurl'])) {
                        $row[$key]['s_coach_imgurl'] = S_HTTP_HOST.$value['s_coach_imgurl'];
                    } elseif(file_exists(__DIR__.'/../admin/'.$value['s_coach_imgurl'])) {
                        $row[$key]['s_coach_imgurl'] = HTTP_HOST.$value['s_coach_imgurl'];
                    } else {
                        $row[$key]['s_coach_imgurl'] = '';
                    }
                    
                    $distance = getDistance($lat, $lng, $value['dc_coach_distance_y'], $value['dc_coach_distance_x'], $value['l_coach_id']);
                    $row[$key]['coach_student_distance'] = "$distance";    
                    
                    // 获取驾校名称
                    $sql = "SELECT * FROM `cs_school` WHERE `l_school_id` = '".$value['s_school_name_id']."'";
                    $stmt = $db->query($sql);
                    $school_name = $stmt->fetch(PDO::FETCH_ASSOC);
                    if($school_name) {
                        $row[$key]['s_school_name'] = $school_name['s_school_name'];
                    } else {
                        $row[$key]['s_school_name'] = '嘻哈驾校';
                    }

                    // 获取车辆名称
                    $sql = "SELECT * FROM `cs_cars` WHERE `id` = '".$value['s_coach_car_id']."'";
                    $stmt = $db->query($sql);
                    $carinfo = $stmt->fetch(PDO::FETCH_ASSOC);
                    if($carinfo) {
                        if (isset($car_info['name']) && !is_null($car_info['name'])) {
                            $row[$key]['s_coach_car_name'] = $carinfo['name'];
                        } else {
                            $row[$key]['s_coach_car_name'] = '';
                        }

                        if (isset($car_info['car_type']) && !is_null($car_info['car_type'])) {
                            $row[$key]['car_type'] = $carinfo['car_type'];
                        } else {
                            $row[$key]['car_type'] = 1;
                        }
                    } else {
                        $row[$key]['s_coach_car_name'] = '暂无设置';
                        $row[$key]['car_type'] = 1;
                    }

                }
                
                $db = null;
                $data = array('code'=>200, 'data'=>$row);
                echo json_encode($data);
                exit();

            } catch(PDOException $e) {
                setapilog('search_coach:params[name:'.$name.',type:'.$type.',city_id:'.$city_id.',lng:'.$lng.',lat:'.$lat.'], error:'.$e->getMessage());
                $data = array('code'=>1, 'data'=>'网络错误');
                echo json_encode($data);
                exit;
            } catch(ErrorException $e) {
                setapilog('search_coach:params[name:'.$name.',type:'.$type.',city_id:'.$city_id.',lng:'.$lng.',lat:'.$lat.'], error:'.$e->getMessage());
                $data = array('code'=>1, 'data'=>'网络错误');
                echo json_encode($data);
                exit;
            }
        }

    }


    // 计算教练当前距离
    function getDistance($lat1, $lng1, $lat2, $lng2, $coach_id) {  
        // $coach_id = $request->params('coach_id'); //教练ID
        // $school_id = $request->params('school_id'); //驾校ID

        $earthRadius = 6367000;
    
        $lat1 = ($lat1 * pi()) / 180;  
        $lng1 = ($lng1 * pi()) / 180;   
        $lat2 = ($lat2 * pi()) / 180; 
        $lng2 = ($lng2 * pi()) / 180;    

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;  
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));  
        $calculatedDistance = $earthRadius * $stepTwo;
        return $calculatedDistance;
    }
?>
