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
$app->post('/','getIndexSchoolList');
$app->run();

// 获取教练学员信息
function getIndexSchoolList() {

    Global $app, $crypt;
    $request = $app->request();
    $lng = $request->params('lng');
    $lat = $request->params('lat');
    $sort = $request->params('sort');
    $city_id = $request->params('city_id');
    $page = $request->params('page');

    $city_id = !empty($city_id) ? $city_id : '0';
    $lng = !empty($lng) ? $lng : '117.144356';
    $lat = !empty($lat) ? $lat : '31.839411';
    $sort = !empty($sort) ? $sort : 'default';
    $page = isset($page) ? $page : 1;
    $limit = 10;
    $start = ($page - 1) * $limit;

    $db = getConnection();

    $sql = "SELECT * FROM `cs_school` WHERE `city_id` = $city_id AND `is_show` = 1 AND `brand` = 2";
    try {
        switch ($sort) {
        case 'default':
            $sql .= " ORDER BY `brand` DESC, `addtime` DESC";
            break;
        case 'location':
            $sql .= " ORDER BY `brand` DESC ";
            break;
        default:
            $sql .= " ORDER BY `brand` DESC, `addtime` DESC";
            break;
        }

        $sql .= " LIMIT $start, $limit";
        $stmt = $db->query($sql);
        $schoollist = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $list = array();
        foreach ($schoollist as $key => $value) {

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
                $list[$key]['s_thumb'] = 'images/school_thumb.jpg';
            }

            $list[$key]['location'] = floor(getDistance($lat, $lng, $value['s_location_y'], $value['s_location_x'])/1000);

            $sql = " SELECT * FROM `cs_school_shifts` WHERE `sh_school_id` = '{$value['l_school_id']}' AND deleted = 1 "; // deleted=1-正常 2-已删除的班制
            $stmt = $db->query($sql);
            $shifts_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $list[$key]['shifts_list'] = $shifts_list;

                /*
                if($shifts_list) {
                    foreach ($shifts_list as $k => $v) {
                        if($v['sh_type'] == 1) {
                            $list[$key]['sh_money_1'] = $v['sh_money'];
                            break;
                        } else {
                            $list[$key]['sh_money_1'] = 0;
                            continue;
                        }					
                    }
                } else {
                    $list[$key]['sh_money_1'] = 0;
                }
                 */

            $sql = " SELECT MIN(sh_money) AS min_shift_money FROM `cs_school_shifts` WHERE `sh_school_id` = '{$value['l_school_id']}' AND deleted = 1 "; // deleted=1-正常 2-已删除的班制
            $stmt = $db->query($sql);
            $school_shift_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($school_shift_info['min_shift_money'])) {
                $min_shift_money = round(floatval($school_shift_info['min_shift_money']), 2);
            } else {
                $min_shift_money = 0;
            }
            $list[$key]['sh_money_1'] = $min_shift_money; // 最低的班制价格

        }

        $sql = "SELECT count(*) as num FROM `cs_school` WHERE `city_id` = $city_id ORDER BY `addtime` DESC";
        $stmt = $db->query($sql);
        $school_num = $stmt->fetch(PDO::FETCH_ASSOC);

        $db = null;
        $pagenum = $school_num['num'] % $limit == 0 ? $school_num['num'] / $limit : ceil($school_num['num'] / $limit);
        $data = array('code'=>200, 'count'=>$school_num['num'], 'pagenum'=>$pagenum, 'data'=>$list);
        echo json_encode($data);

    } catch(PDOException $e) {
        setapilog('get_school_list:params[lng:'.$lng.',lat:'.$lat.',sort:'.$sort.',city_id:'.$city_id.',page:'.$page.',order:'.$order.'], error:'.$e->getMessage());
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

// 多维数组排序
function multiArraySort($arr, $field, $sort = 'SORT_ASC') {
    $sort = array(
        'direction' => $sort, //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
        'field'     => $field,       //排序字段  
    );

    // 多维数组根据某个字段排序
    $arrSort = array();  
    foreach($arr AS $uniqid => $row){  
        foreach($row AS $key=>$value){  
            $arrSort[$key][$uniqid] = $value;  
        }  
    }
    if($sort['direction']){  
        array_multisort($arrSort[$sort['field']], constant($sort['direction']), $arr);  
    }
    return $arr;
}
?>
