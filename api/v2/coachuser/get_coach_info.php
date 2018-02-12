<?php  
    /**
     * 获取教练我的信息
     * @param int $coach_id 教练id
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
    $app->any('/','getCoachInfo');
    $app->run();

    function getCoachInfo() {
        Global $app, $crypt, $lisence_config, $lesson_config;
        $request = $app->request();
        $response = $app->response();
        if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_coach_info] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            exit(json_encode($data));
        }
        //  获取请求参数并判断合法性
        $validate_result = validate(
            array(
                'coach_id'      =>'INT', 
            ), $request->params());

        if (!$validate_result['pass']) {
            exit(json_encode($validate_result['data']));
        }
        $p = $request->params();
        $coach_id = $p['coach_id'];
        try {
            $db = getConnection();
            $coach = DBPREFIX.'coach';
            $user = DBPREFIX.'user';
            $sql = "SELECT `user_id`, `l_coach_id`, `province_id`, `city_id`, `area_id`, `s_coach_car_id`, `i_coach_star`, `good_coach_star`, `s_coach_address`, `coach_star_count`, `s_coach_content`, `s_coach_lisence_id` AS license_id, `s_coach_lesson_id` AS lesson_id FROM `{$user}` AS user LEFT JOIN `{$coach}` AS coach ON user.l_user_id = coach.user_id ";
            $where = " WHERE `l_coach_id` = :coach_id";
            $sql .= $where;
            $stmt = $db->prepare($sql);
            $stmt->bindParam('coach_id', $coach_id);
            $stmt->execute();
            $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($coach_info)) {
                $data = array('code'=>103, 'data'=>'账号不存在');
                exit( json_encode($data) );
            }

            // 省份名称
            if (array_key_exists('province_id', $coach_info)) {
                $tbl = DBPREFIX.'province';
                $sql = " SELECT `province` FROM `{$tbl}` WHERE `provinceid` = :pid ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('pid', $coach_info['province_id']);
                $stmt->execute();
                $province_info = $stmt->fetch(PDO::FETCH_ASSOC);
                if (isset($province_info['province'])) {
                    $coach_info['province_name'] = $province_info['province'];
                } else {
                    $coach_info['province_name'] = '';
                }
            } else {
                $coach_info['province_name'] = '';
            }

            // 城市名称
            if (isset($coach_info['city_id'])) {
                $tbl = DBPREFIX.'city';
                $sql = " SELECT `city` FROM `{$tbl}` WHERE `cityid` = :cid ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('cid', $coach_info['city_id']);
                $stmt->execute();
                $city_info = $stmt->fetch(PDO::FETCH_ASSOC);
                if (isset($city_info['city'])) {
                    $coach_info['city_name'] = $city_info['city'];
                } else {
                    $coach_info['city_name'] = '';
                }
            } else {
                $coach_info['city_name'] = '';
            }

            // 地区名称
            if (isset($coach_info['city_id']) && isset($coach_info['area_id'])) {
                $tbl = DBPREFIX.'area';
                $sql = " SELECT `area` FROM `{$tbl}` WHERE `fatherid` = :cid  AND `areaid` = :aid ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('cid', $coach_info['city_id']);
                $stmt->bindParam('aid', $coach_info['area_id']);
                $stmt->execute();
                $area_info = $stmt->fetch(PDO::FETCH_ASSOC);
                if (isset($area_info['area'])) {
                    $coach_info['area_name'] = $area_info['area'];
                } else {
                    $coach_info['area_name'] = '';
                }
            } else {
                $coach_info['area_name'] = '';
            }

            // 获取所带学员总数
            $study_orders = DBPREFIX.'study_orders';
            $sql = "SELECT count(`l_study_order_id`) as num FROM `{$study_orders}` WHERE `l_coach_id` = :coach_id AND `i_status` = 2"; // 已完成
            $stmt = $db->prepare($sql);
            $stmt->bindParam('coach_id', $coach_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row) {
                $coach_info['students_num'] = $row['num'];
            } else {
                $coach_info['students_num'] = 0;
            }

            // 获得好评率
            if($coach_info['coach_star_count'] != 0) {
                $coach_info['good_comment_rate'] = floor(($coach_info['good_coach_star'] / $coach_info['coach_star_count']) * 100).'%'; 
            } else {
                $coach_info['good_comment_rate'] = 0;
            }
            // 获取我的车型
            $cars = DBPREFIX.'cars';
            $sql = "SELECT `name`, `car_no`, `imgurl`, `car_type` FROM `{$cars}` WHERE `id` = :car_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('car_id', $coach_info['s_coach_car_id']);
            $stmt->execute();
            $car_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($car_info)) {
                $coach_info['car_info'] = array(
                    'name' => '',
                    'car_no' => '',
                    'imgurl' => array(),
                    'car_type' => '',
                    'car_type_name' => '',
                );
            } else {
                $imgurl_arr = array();
                $imgurl = json_decode($car_info['imgurl'], true);
                if(is_array($imgurl) && !empty($imgurl)) {
                    foreach ($imgurl as $k => $v) {
                        if(file_exists(__DIR__.'/../../../sadmin/'.$v)) {
                            $imgurl_arr[] = S_HTTP_HOST.$v;
                        } elseif(file_exists(__DIR__.'/../../../admin/'.$v)) {
                            $imgurl_arr[] = HTTP_HOST.$v;
                        } elseif(file_exists(__DIR__.'/../../../'.$v)) {
                            $imgurl_arr[] = HOST.$v;
                        }
                    }
                }
                $car_info['car_type_name'] = '';
                switch ($car_info['car_type']) {
                    case '1':
                        $car_info['car_type_name'] = '普通车型';
                        break;
                    case '2':
                        $car_info['car_type_name'] = '加强车型';
                        break;
                    case '3':
                        $car_info['car_type_name'] = '模拟车型';
                        break;
                    default:
                        $car_info['car_type_name'] = '普通车型';
                        break;
                }
                $coach_info['car_info'] = array(
                    'name' => $car_info['name'],    
                    'car_no' => $car_info['car_no'],    
                    'imgurl' => $imgurl_arr,    
                    'car_type' => $car_info['car_type'],
                    'car_type_name' => $car_info['car_type_name'],
                );    
            }
            $coach_info['lesson_id'] = explode(',', $coach_info['lesson_id']);
            $coach_info['license_id'] = explode(',', $coach_info['license_id']);
            $coach_info['lesson_name_list'] = array();
            $coach_info['lesson_name'] = array();
            $coach_info['license_name_list'] = array();
            if (is_array($coach_info['lesson_id']) && !empty($coach_info['lesson_id'])) {
                foreach ($coach_info['lesson_id'] as $coach_id_index => $coach_id_value) {
                    if (isset($lesson_config[$coach_id_value])) {
                        $coach_info['lesson_name_list'][$coach_id_index] = $lesson_config[$coach_id_value];
                        $coach_info['lesson_name'][$coach_id_index] = $lesson_config[$coach_id_value];
                    }
                }
            }
            unset($coach_info['lesson_id']);

            if (is_array($coach_info['license_id']) && !empty($coach_info['license_id'])) {
                foreach ($coach_info['license_id'] as $coach_id_index => $coach_id_value) {
                    if (isset($lisence_config[$coach_id_value])) {
                        $coach_info['license_name_list'][$coach_id_index] = $lisence_config[$coach_id_value];
                    }
                }
            }
            if (count($coach_info['license_name_list']) > 0) {
                $coach_info['license_name'] = $coach_info['license_name_list'][0];
            } else {
                $coach_info['license_name'] = '';
            }
            unset($coach_info['license_id']);

            $db = null;
            $data = array('code'=>200, 'data'=>$coach_info);
            exit( json_encode($data) );
        } catch(PDOException $e) {
            slimLog($request, $response, $e, 'PDO数据库处理出错');
            $data = array('code'=>1, 'data'=>'网络错误');
            exit(json_encode($data));
        } catch(Exception $e) {
            slimLog($request, $response, $e, 'SLIM应用解析出错');
            $data = array('code'=>1, 'data'=>'网络错误');
            exit(json_encode($data));
        }
    }

    /*
     * [July 30, 2016 by gdc] 更完善的判断图片是在哪个目录下面 admin/upload or sadmin/upload or upload
    **/
?>
