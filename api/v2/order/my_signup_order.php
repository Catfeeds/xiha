<?php 

/**
 * 获取我报名驾校的订单
 * @param $uid 学员ID 
 * @param $lng 经度 
 * @param $lat 纬度 
 * @return string AES对称加密（加密字段xhxueche）
 * @author sunweiwei
 **/
require '../../Slim/Slim.php';
require '../../include/common.php';
require '../../include/crypt.php';
require '../../include/functions.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
$app->post('/','getmyOrder');
$app->response->headers->set('Content-Type', 'application/json;charset=utf8');
$app->run();

//获取我的报名驾校
function getmyOrder() {
    Global $app, $crypt;
    $r = $app->request();
    // setapilog(serialize($r->params()));
    //验证请求方式 POST
    if ( !$r->isPost() ) {
        setapilog('[send_appoint_orders] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
        exit( json_encode(array('code' => 106, 'data' => '请求错误')) );
    }
    //验证输入参数
    $validate_ok = validate(
        array('uid' => 'INT','lng' => 'INT','lat' => 'INT',), $r->params());
    if ( !$validate_ok['pass'] ) {
        exit( json_encode($validate_ok['data']) );
    }
    //获取参数
    $uid = $r->params('uid');
    $lng = $r->params('lng'); 
    $lat = $r->params('lat');
    if(!$uid) {
        $data = array('code'=>103, 'data'=>'请重新登陆');
        echo json_encode($data);
        exit();
    }

    try {
        $db = getConnection();
        $sql = "SELECT * FROM `cs_school_orders` WHERE `so_user_id` = $uid AND `so_order_status` != 101 ORDER BY `addtime` DESC";//订单不是无效订单
        $stmt = $db->query($sql);
        $order_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(empty($order_list)) {
            $data = array('code'=>200, 'data'=>array());
            echo json_encode($data);
            exit();
        }
        foreach ($order_list as $key => $value) {
            // 获取学校相关
            $sql = "SELECT `l_school_id`, `s_thumb`, `s_school_name`, `city_id`, `s_address` FROM `cs_school` WHERE `l_school_id` = {$value['so_school_id']}";
            $stmt = $db->query($sql);
            $school_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if($school_info) {
                // $order_list[$key]['l_school_id'] = $school_info['l_school_id'];
                $order_list[$key]['school_address'] = $school_info['s_address'];

                if($school_info['s_thumb']) {
                    if(file_exists(__DIR__.'/../../../sadmin/'.$school_info['s_thumb'])) {
                        $order_list[$key]['school_thumb'] = S_HTTP_HOST.$school_info['s_thumb'];
                    } else {
                        $order_list[$key]['school_thumb'] = HTTP_HOST.$school_info['s_thumb'];
                    }
                } else {
                    $order_list[$key]['school_thumb'] = 'images/school_thumb.jpg';
                }
                $order_list[$key]['school_name'] = $school_info['s_school_name'];

                // 根据城市ID获取城市名
                $sql = "SELECT `city` FROM `cs_city` WHERE `cityid` = ".$school_info['city_id'];
                $stmt = $db->query($sql);
                $city_info = $stmt->fetch(PDO::FETCH_ASSOC);

                if($city_info) {
                    $order_list[$key]['city'] = $city_info['city'];
                } else {
                    $order_list[$key]['city'] = '合肥';
                }

                // 获取班制
                $sql = "SELECT `sh_title` FROM `cs_school_shifts` WHERE `id` = ".$value['so_shifts_id'];
                $stmt = $db->query($sql);
                $shifts_info = $stmt->fetch(PDO::FETCH_ASSOC);
                if($shifts_info) {
                    $order_list[$key]['sh_title'] = $shifts_info['sh_title'];
                } else {
                    $order_list[$key]['sh_title'] = '未知';
                }

                // 获取培训地点的电话
                $sql = "SELECT `tl_phone`, `tl_location_x`, `tl_location_y` FROM `cs_school_train_location` WHERE `tl_school_id` = '{$school_info['l_school_id']}' ORDER BY `addtime` DESC LIMIT 1";
                $stmt = $db->query($sql);
                $train_location_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $train_location = array();
                $distance = array();
                if ($train_location_info) {
                    foreach ($train_location_info as $keys => $values) {
                        $train_location[$keys]['distance'] = floor(getDistance($values['tl_location_x'], $values['tl_location_y'], $lat, $lng)/1000);
                        $train_location[$keys]['phone'] = $values['tl_phone'];
                        $distance[] = floor(getDistance($values['tl_location_x'], $values['tl_location_y'], $lat, $lng)/1000);
                    }
                    $min_distance = min($distance);
                    $min_phone = '';
                    foreach ($train_location as $k => $v) {
                        if($v['distance'] == $min_distance) {
                            $min_phone = $v['phone'];
                        }
                    }
                    $order_list[$key]['school_phone'] = $min_phone;

                } else {
                    $order_list[$key]['school_phone'] = '';
                }
                // 获取支付方式--
                //线上订单状态：so_order_status 1：报名成功已付款  2：退款处理中  3：报名取消  4：报名成功未付款 
                // 支付宝方式
                $order_list[$key]['order_status'] = '';
                if($value['so_pay_type'] == 1) {
                    if($value['so_order_status'] == 1) {
                        $order_list[$key]['order_status'] = '报名成功已付款'; // 已支付

                    } else if($value['so_order_status'] == 2) {
                        $order_list[$key]['order_status'] = '退款处理中';

                    } else if($value['so_order_status'] == 3) {
                        $order_list[$key]['order_status'] = '报名取消';

                    } else if($value['so_order_status'] == 4) {
                        $order_list[$key]['order_status'] = '报名成功未付款'; // 未支付

                    }
                    $order_list[$key]['pay_method'] = '支付宝';

                } elseif ($value['so_pay_type'] == 3) {
                    if($value['so_order_status'] == 1) {
                        $order_list[$key]['order_status'] = '报名成功已付款'; // 已支付

                    } else if($value['so_order_status'] == 2) {
                        $order_list[$key]['order_status'] = '退款处理中';

                    } else if($value['so_order_status'] == 3) {
                        $order_list[$key]['order_status'] = '报名取消';

                    } else if($value['so_order_status'] == 4) {
                        $order_list[$key]['order_status'] = '报名成功未付款'; // 未支付

                    }
                    $order_list[$key]['pay_method'] = '微信';

                } elseif ($value['so_pay_type'] == 4) {
                    if($value['so_order_status'] == 1) {
                        $order_list[$key]['order_status'] = '报名成功已付款'; // 已支付

                    } else if($value['so_order_status'] == 2) {
                        $order_list[$key]['order_status'] = '退款处理中';

                    } else if($value['so_order_status'] == 3) {
                        $order_list[$key]['order_status'] = '报名取消';

                    } else if($value['so_order_status'] == 4) {
                        $order_list[$key]['order_status'] = '报名成功未付款'; // 未支付

                    }
                    $order_list[$key]['pay_method'] = '银联';

                } else {
                    if($value['so_order_status'] == 1) {
                        $order_list[$key]['order_status'] = '报名成功未付款'; // 已支付

                    } else if($value['so_order_status'] == 2) {
                        $order_list[$key]['order_status'] = '已取消';

                    } else if($value['so_order_status'] == 3) {
                        $order_list[$key]['order_status'] = '报名成功已付款';

                    } else if($value['so_order_status'] == 4) {
                        $order_list[$key]['order_status'] = '退款处理中'; // 未支付

                    }
                    $order_list[$key]['pay_method'] = '线下';
                }

            } else {
                $order_list[$key]['l_school_id'] = '';
                $order_list[$key]['school_address'] = '';
                $order_list[$key]['school_thumb'] = 'images/school_thumb.jpg';
                $order_list[$key]['school_name'] = '';
                $order_list[$key]['city'] = '';
                $order_list[$key]['order_status'] = '无效报名';
            }
            $order_list[$key]['so_final_price'] = $value['so_final_price'];//报名驾校最终价格
            $order_list[$key]['so_original_price'] = $value['so_original_price'];//报名驾校原始价格
            $order_list[$key]['so_order_no'] = $value['so_order_no'];//报名驾校订单号
            $order_list[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);//下单时间
        }
        // foreach ($order_list as $key => $value) {
        // 	$signup_order[$key]['school_name'] = $value['school_name'];//驾校名称
        // 	$signup_order[$key]['shifts_id'] = $value['so_shifts_id'];//班制名称
        // 	$signup_order[$key]['shifts'] = $value['sh_title'];//班制名称
        // 	$signup_order[$key]['licence'] = $value['so_licence'];//驾照类型
        // 	$signup_order[$key]['school_thumb'] = $value['school_thumb'];//轮播图url
        // 	$signup_order[$key]['nearest_phone'] = $value['school_phone'];//最近报名点电话
        // 	$signup_order[$key]['order_no'] = $value['so_order_no'];//订单号
        // 	$signup_order[$key]['signup_time'] = $value['addtime'];//下单时间
        // 	$signup_order[$key]['order_status'] = $value['order_status'];//订单状态
        // 	$signup_order[$key]['pay_method'] = $value['pay_method'];//支付方式
        // 	$signup_order[$key]['final_price'] = $value['so_final_price'];//最终价格
        // 	$signup_order[$key]['original_price'] = $value['so_original_price'];//原始价格
        // }

        foreach ($order_list as $key => $value) {
            $signup_order[$key]['school_name'] = $value['school_name'];//驾校名称
            $signup_order[$key]['city'] = $value['city'];//城市
            $signup_order[$key]['school_address'] = $value['school_address'];//驾校地址
            $signup_order[$key]['shifts_id'] = $value['so_shifts_id'];//班制名称
            $signup_order[$key]['shifts'] = $value['sh_title'];//班制名称
            $signup_order[$key]['licence'] = $value['so_licence'];//驾照类型
            $signup_order[$key]['school_thumb'] = $value['school_thumb'];//轮播图url
            $signup_order[$key]['nearest_phone'] = $value['school_phone'];//最近报名点电话
            $signup_order[$key]['order_no'] = $value['so_order_no'];//订单号
            $signup_order[$key]['signup_time'] = $value['addtime'];//下单时间
            $signup_order[$key]['order_status'] = $value['order_status'];//订单状态
            $signup_order[$key]['pay_method'] = $value['pay_method'];//支付方式
            $signup_order[$key]['final_price'] = $value['so_final_price'];//最终价格
            $signup_order[$key]['original_price'] = $value['so_original_price'];//原始价格
            $signup_order[$key]['comment_status'] = $value['so_comment_status'];//评价状态
            $signup_order[$key]['order_id'] = $value['id'];//订单id
            $signup_order[$key]['school_id'] = $value['so_school_id'];//驾校id
        }

        $db = null;
        $data = array('code'=>200, 'data'=>$signup_order);
        echo json_encode($data);

    } catch(PDOException $e) {
        setapilog('my_signup_order:params[uid:'.$uid.'], error:'.$e->getMessage());	
        $data = array('code'=>1, 'data'=>'网络错误');
        echo json_encode($data);
        exit;
    }

}

// 计算报名点与学员之间距离
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
