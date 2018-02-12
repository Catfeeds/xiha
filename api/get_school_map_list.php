<?php
/**
 * 获取所有驾校列表
 * @param $lng 学员经度 117.144356
 * @param $lat 学员维度 31.839411
 * @param $member_id int 学员或者教练ID
 * @return string AES对称加密（加密字段xhxueche）
 * @author chenxi
 **/

require 'Slim/Slim.php';
require 'include/common.php';
require 'include/crypt.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
$app->post('/','getSchoolList');
$app->run();

// 获取教练学员信息
function getSchoolList() {

    Global $app, $crypt;
    $request = $app->request();
    $lng = $request->params('lng');
    $lat = $request->params('lat');
    $city_id = $request->params('city_id');

    $city_id = !empty($city_id) ? $city_id : '340100';
    $lng = !empty($lng) ? $lng : '117.144356';
    $lat = !empty($lat) ? $lat : '31.839411';

    try {
        $db = getConnection();
        $sql = "SELECT * FROM `cs_school` WHERE `city_id` = $city_id AND `is_show` = 1 ORDER BY `addtime` DESC, `brand` DESC ";

        $stmt = $db->query($sql);
        $schoollist = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $list = array();
        $school_id_list = array();
        foreach ($schoollist as $key => $value) {

            $school_id_list[] = $value['l_school_id'];

            $list[$key]['l_school_id'] = $value['l_school_id'];
            $list[$key]['s_school_name'] = $value['s_school_name'];
            $list[$key]['brand'] = $value['brand'];

            if($value['s_thumb']) {
                if(file_exists(__DIR__.'/../sadmin/'.$value['s_thumb'])) {
                    $list[$key]['s_thumb'] = S_HTTP_HOST.$value['s_thumb'];
                } else {
                    $list[$key]['s_thumb'] = HTTP_HOST.$value['s_thumb'];
                }
            } else {
                $list[$key]['s_thumb'] ='';
            }
            $list[$key]['lng'] = $value['s_location_x'];
            $list[$key]['lat'] = $value['s_location_y'];
            $list[$key]['location'] = floor(getDistance($lat, $lng, $value['s_location_y'], $value['s_location_x'])/1000);
        }

        $sql = "SELECT count(*) as num FROM `cs_school` WHERE `city_id` = $city_id ORDER BY `addtime` DESC";
        $stmt = $db->query($sql);
        $school_num = $stmt->fetch(PDO::FETCH_ASSOC);

        // 获取驾校里的教练数量 by gaodacheng at 2016/01/17
        if (! empty($school_id_list)) {
            $school_id_list_str = "('".implode("','", $school_id_list)."')";
            $sql = " SELECT coach.s_school_name_id AS school_id, COUNT(l_coach_id) AS coach_count FROM `cs_coach` AS coach LEFT JOIN `cs_user` AS user ON coach.user_id = user.l_user_id WHERE user.i_user_type = 1 AND user.i_status = 0 AND coach.order_receive_status = 1 AND coach.s_school_name_id IN {$school_id_list_str} GROUP BY coach.s_school_name_id ";
            $stmt = $db->query($sql);
            $coach_list = $stmt->fetchAll(PDO::FETCH_OBJ);

            if (! empty($coach_list)) {
                // $list <= $schoollist
                foreach ($list as $index => $school) {
                    $found = false;
                    foreach ($coach_list as $j => $coach) {
                        if ($coach->school_id == $school['l_school_id']) {
                            $list[$index]['coach_count'] = intval($coach->coach_count);
                            $found = true;

                            // 跳出查找过程
                            break;
                        }
                    }
                    if (! $found) {
                        $list[$index]['coach_count'] = 0;
                    }
                }
            }
        } else {
            foreach ($list as $index => $school) {
                $list[$index]['coach_count'] = 0;
            }
        }

        $db = null;
        $data = array('code'=>200, 'count'=>$school_num['num'], 'data'=>$list);
        echo json_encode($data);

    } catch(PDOException $e) {
        setapilog('get_school_list:params[lng:'.$lng.',lat:'.$lat.',city_id:'.$city_id.'], error:'.$e->getMessage());
        $data = array('code'=>1, 'data'=>'网络错误');
        echo json_encode($data);
        exit;
    }
}

// 计算教练当前距离
function getDistance($lat1, $lng1, $lat2, $lng2)  {
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
