<?php  
/**
 * 驾校下单 ()
 * @param $id 班制id $sid驾校id  $pay_type支付类型...
 * @return 
 * @author sun
 **/
require '../../Slim/Slim.php';
require '../../include/common.php';
require '../../include/crypt.php';
require '../../include/functions.php';
require '../pay/include/common.php';
require '../pay/apppay/wxpay/lib/WxPay.Api.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
$app->any('/','sendSignupOrders');
$app->response->headers->set('Content-Type', 'application/json;charset=utf8');
$app->run();

function sendSignupOrders() {
    Global $app, $crypt;
    $r = $app->request();
    if ( !$r->isPost() ) {
        $data = array('code' => 106, 'data' => '请求错误');
        setapilog('[get_user_practice] [:error] [client ' . $r->getIp() . '] [Method % ' . $r->getMethod() . '] [106 错误的请求方式]');
        echo json_encode($data);
        exit();
    }
    $r = $app->request();
    $id = $r->params('id');
    $sid = $r->params('sid');
    $pay_type = $r->params('pay_type');
    $user_name = $r->params('user_name');
    $user_phone = $r->params('user_phone');
    $identity_id = $r->params('user_identify_id');
    $licence_type = $r->params('licence');
    $order_status = 4; // 1：报名成功已付款  2：申请退款中  3：报名取消  4：报名成功未付款 
    $uid = $r->params('uid');
    $validate_result = validate(array('id'=>'INT', 'sid'=>'INT', 'pay_type'=>'INT','user_name'=>'STRING', 'user_phone'=>'INT', 'user_identify_id'=>'STRING','licence'=>'STRING', 'uid'=>'INT'), $r->params());
    if (!$validate_result['pass']) {
        echo json_encode($validate_result['data']);
        exit();
    }
    //判断是否登录状态
    if($uid == '') {
        $data = array('code'=>-2, 'data'=>'请先登录');
        echo json_encode($data);
        exit();
    }
    //判断请求参数信息是否完整
    if(trim($id) == '' || trim($sid) == '' || trim($pay_type) == '' || trim($user_phone) == '' || trim($user_name) == '' || trim($identity_id) == '' || trim($licence_type) == '' || trim($uid) == '') {
        $data = array('code'=>-5, 'data'=>'请完善信息');
        echo json_encode($data);
        exit();
    }

    // 验证身份证
    if(!identify($identity_id)) {
        $data = array('code'=>-6, 'data'=>'身份证验证失败');
        echo json_encode($data);
        exit();
    }

    try {
        $db = getConnection();

        /*
        // [cancel_other_unpaid_school_orders] 删除其它未付款的订单
        $school_order_tbl = DBPREFIX.'school_orders';
        $order_status_unpaid = 4; // 4-未付款
        $sql = " SELECT `so_order_no` FROM `{$school_order_tbl}` WHERE `so_order_status` = :order_status_unpaid AND ( `so_user_identity_id` = :identity_id OR `so_phone` = :user_phone) ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('order_status_unpaid', $order_status_unpaid);
        $stmt->bindParam('identity_id', $identity_id);
        $stmt->bindParam('user_phone', $user_phone);
        $stmt->execute();
        $unpaid_order_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (is_array($unpaid_order_list) && count($unpaid_order_list) > 0) {
            $ready_delete_order_list = array();
            foreach ($unpaid_order_list as $order_index => $unpaid_order) {
                if (isset($unpaid_order['so_order_no']) && trim((string)$unpaid_order['so_order_no']) != '') {
                    $ready_delete_order_list[] = $unpaid_order['so_order_no'];
                }
            }

            if (count($ready_delete_order_list) > 0) {
                $read_delete_order_no_list_str = implode(",", $ready_delete_order_list);
                $school_order_tbl = DBPREFIX.'school_orders';
                $order_status_delete = 101; // 101-订单状态：删除
                $sql = " UPDATE `{$school_order_tbl}` SET `so_order_status` = :order_status_delete WHERE `so_order_status` = :order_status_unpaid AND ( `so_user_identity_id` = :identity_id OR `so_phone` = :user_phone) ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('order_status_delete', $order_status_delete);
                $stmt->bindParam('order_status_unpaid', $order_status_unpaid);
                $stmt->bindParam('identity_id', $identity_id);
                $stmt->bindParam('user_phone', $user_phone);
                $delete_ok = $stmt->execute();
            }
        }
        // [cancel_other_unpaid_school_orders] 删除其它未付款的订单
        */

        //生成订单号
        $so_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);

        // 通过班制ID获取班制信息
        $sql = "SELECT `sh_money`, `sh_original_money` FROM `cs_school_shifts` WHERE `id` = ".$id;
        $stmt = $db->query($sql);
        $shifts_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if(empty($shifts_info)) {
            $data = array('code'=>-1, 'data'=>'参数错误');
            echo json_encode($data);
            exit();
        }

        //分开存放它们的支付信息
        $alipay     = array();
        $wxpay      = array();
        $unionpay   = array();

        // 获取是否已报名
        $sql = "SELECT * FROM `cs_school_orders` WHERE (`so_user_identity_id` = '{$identity_id}' OR (`so_user_id` = '{$uid}' AND `so_phone` = '{$user_phone}')) AND `so_order_status` != 101 AND ((`so_pay_type` IN (1, 3, 4) AND `so_order_status` IN (1, 2, 4)) OR (`so_pay_type` = 2 AND `so_order_status` IN (1, 3, 4)))";
        $stmt = $db->query($sql);
        $order_info = $stmt->fetch(PDO::FETCH_ASSOC);
        if($order_info) {
            $data = array('code'=>-7, 'data'=>'此身份证已报过名！');
            echo json_encode($data);
            exit();
        }

        // 更新用户信息
        $sql = "SELECT `s_real_name` FROM `cs_user` WHERE `l_user_id` = $uid";
        $stmt = $db->query($sql);
        $_user_info = $stmt->fetch(PDO::FETCH_ASSOC);
        if(isset($_user_info['s_real_name']) && trim($_user_info['s_real_name']) == '') {
            $sql = "UPDATE `cs_user` SET `s_real_name` = '".$user_name."' WHERE `l_user_id` = $uid";
            $stmt = $db->query($sql);
            if(!$stmt) {
                $data = array('code'=>-10, 'data'=>'当前报名人数太多，请从新报名');
                echo json_encode($data);
                exit();	
            }
        }
        $sql = "SELECT * FROM `cs_users_info` WHERE `user_id` = $uid";
        $stmt = $db->query($sql);
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user_info) {
            $sql = "UPDATE `cs_users_info` SET `identity_id` = '".$identity_id."', `school_id` = '".$sid."', `sex` = 1 WHERE `user_id` = $uid";
            $stmt = $db->query($sql);
            if(!$stmt) {
                $data = array('code'=>-8, 'data'=>'当前报名人数太多，请从新报名');
                echo json_encode($data);
                exit();
            }
        } else {
            $sql = '';
            $sql = "INSERT INTO `cs_users_info` (";
            $sql .= " `x`,";
            $sql .= " `y`,";
            $sql .= " `user_id`,";
            $sql .= " `sex`,";
            $sql .= " `age`,";
            $sql .= " `identity_id`,";
            $sql .= " `address`,";
            $sql .= " `user_photo`,";
            $sql .= " `license_num`,";
            $sql .= " `school_id`,";
            $sql .= " `lesson_name`,";
            $sql .= " `province_id`,";
            $sql .= " `city_id`,";
            $sql .= " `area_id`,";
            $sql .= " `photo_id`,";
            $sql .= " `learncar_status`";
            $sql .= ") VALUES (";
            $sql .= "0,0,";
            $sql .= "'".$uid."',";
            $sql .= "1,18,";
            $sql .= " '".$identity_id."',";
            $sql .= " '','',";
            $sql .= " 0,'".$sid."','',0,0,0,";
            $sql .= " 1,'科目二学习中')";
            $stmt = $db->query($sql);
            if(!$stmt) {
                $data = array('code'=>-9, 'data'=>'当前报名人数太多，请从新报名');
                echo json_encode($data);
                exit();
            }
        }

        // 判断不同支付方式返回不同参数
        switch ($pay_type) {
        case 1://支付宝
            $body = json_encode(array('order_no'=>$so_order_no, 'order_time'=>date('Y-m-d H:i',time()), 'shifts_id'=>$id, 'order_type'=>'signup','order_money'=>$shifts_info['sh_money']));
            $param = "_input_charset=\"utf-8\"&body=\"".$body."\"&it_b_pay=\"30m\"&notify_url=\"".PAYHOST."api/v2/pay/apppay/alipay/notify_url.php\"&out_trade_no=\"".$so_order_no."\"&partner=\"".PARTNER."\"&seller=\"".PARTNER."\"&service=\"mobile.securitypay.pay\"&show_url=\"m.alipay.com\"&subject=\"嘻哈学车APP报名驾校支付\"&total_fee=\"".$shifts_info['sh_money']."\"";
            $rsa = rsaSign($param, APP_PRIVATE_KEY_PATH);
            $alipay = array(
                'orderstring'   => $param, 
                'signstring'    => $rsa,
            );
            break;
        case 2://线下
            exit( json_encode( array('code'=>109, 'data'=>'不支持的支付方式:线下')  ) );
            break;
        case 3:
            //微信支付预支付订单
            $input = new WxPayUnifiedOrder();
            $input->SetOut_trade_no($so_order_no);
            $input->SetBody('嘻哈学车APP报名驾校支付');
            $input->SetTotal_fee($shifts_info['sh_money'] * 100); //微信支付单位: 分
            $input->SetTrade_type('APP'); //App支付
            $input->SetSpbill_create_ip($r->getIp()); //App支付
            $input->SetNotify_url(PAYHOST . 'api/v2/pay/apppay/wxpay/notify_url.php'); //异步通知回调地址
            $attach = array(
                'order_money'   => $shifts_info['sh_money'],
                'order_no'      => $so_order_no,
                'order_type'    => 'signup',
            );
            $input->SetAttach(json_encode($attach));
            $uo_result = WxPayApi::unifiedOrder($input);

            //订单查询
            $qinput = new WxPayOrderQuery();
            $qinput->SetOut_trade_no($so_order_no);
            $oq_result = WxPayApi::orderQuery($qinput);

            //通信成功
            if ( $oq_result['appid'] == WxPayConfig::APPID
                && $oq_result['mch_id'] == WxPayConfig::MCHID
                && array_key_exists('return_code', $oq_result) 
                && $oq_result['return_code'] == 'SUCCESS' 
            ) {
                //查询成功
                if ( array_key_exists('result_code', $oq_result) ) {
                    if ( $oq_result['result_code'] == 'SUCCESS' ) {
                        if ( $oq_result['trade_state'] == 'NOTPAY' ) {
                            if ( array_key_exists('prepay_id',  $uo_result ) && array_key_exists('sign', $uo_result) ) {
                                $nowts = time();
                                $wxpay = array(
                                    'appid' => WxPayConfig::APPID,
                                    'partnerid' => WxPayConfig::MCHID,
                                    'prepayid' => $uo_result['prepay_id'],
                                    'package' => 'Sign=WXPay',
                                    'noncestr' => md5($nowts),
                                    'timestamp' => $nowts,
                                );
                                //sort by key
                                ksort($wxpay);
                                $stringA = '';
                                foreach ( $wxpay as $k => $v ) {
                                    $stringA .= "$k=$v&";
                                }
                                //$stringSignTemp = $stringA . WxPayConfig::KEY;
                                $stringSignTemp = $stringA . "key=" . WxPayConfig::KEY;
                                $sign = strtoupper(md5($stringSignTemp));
                                $wxpay['sign'] = $sign;
                            }
                        } elseif ( $oq_result['trade_state'] == 'SUCCESS' ) {
                            $order_status = 1; //已付款
                        }
                    }
                }
            }
            break;
        case 4:
            $url = PAYHOST."/api/v2/pay/apppay/unipay/return_url.php";
            $params = array(
                'order_no'      => $so_order_no,
                'order_time'    => date('YmdHis',time()),
                'order_money'   => $shifts_info['sh_money'] * 100,
                'order_type'      => 'signup'
            );
            $res = request_post($url, $params);
            $_data = json_decode($res, true);
            if($_data['code'] == 200) {
                $unionpay = $_data['data'];
            } else {
                exit( json_encode( array('code'=>109, 'data'=>$_data['data'])) );
            }
            break;
        default :
            exit( json_encode( array('code'=>109, 'data'=>'不支持的支付方式')) );
            break;
        }

        $sql = "INSERT INTO `cs_school_orders` (";
        $sql .= "`so_school_id`,";
        $sql .= " `so_final_price`,";
        $sql .= " `so_original_price`,";
        $sql .= " `so_shifts_id`,";
        $sql .= " `so_pay_type`,";
        $sql .= " `so_order_status`,";
        $sql .= " `so_comment_status`,";
        $sql .= " `so_order_no`,";
        $sql .= " `s_zhifu_dm`,";
        $sql .= " `so_user_id`,";
        $sql .= " `so_user_identity_id`,";
        $sql .= " `so_licence`,";
        $sql .= " `so_username`,";
        $sql .= " `so_phone`,";
        $sql .= " `addtime`) ";
        $sql .= "VALUES ('".$sid."',";
        $sql .= " '".$shifts_info['sh_money']."',";
        $sql .= " '".$shifts_info['sh_original_money']."',";
        $sql .= " '".$id."',";
        $sql .= " '".$pay_type."',";
        $sql .= " '{$order_status}', 1,";
        $sql .= " '".$so_order_no."','',";
        $sql .= " '".$uid."',";
        $sql .= " '".$identity_id."',";
        $sql .= " '".$licence_type."',";
        $sql .= " '".$user_name."',";
        $sql .= " '".$user_phone."',";
        $sql .= " '".time()."')";
        $stmt = $db->query($sql);

        if(!$stmt) {
            exit( json_encode( array('code'=>400, 'data'=>'报名失败')  ) );
        }
        $db = null;
        $data = array(
            'code'  => 200,
            'data'  => array(
                'pay'   => array(
                    'alipay'    => $alipay,
                    'wxpay'     => $wxpay,
                    'unionpay'  => $unionpay,
                ),
                'order_info'     => array(
                    'order_id'   => $so_order_no,
                ),
            ),
        );
        echo json_encode($data);
        exit();
    } catch(PDOException $e) {
        setapilog('school_payment:params[id:'.$id.',sid:'.$sid.',pay_type:'.$pay_type.',user_name:'.$user_name.',user_phone:'.$user_phone.',user_identify_id:'.$identity_id.',licence:'.$licence_type.',uid:'.$uid.'], error:'.$e->getMessage());
        $data = array('code'=>1, 'data'=> '网络错误');
        $data = array('code'=>1, 'data'=> $e->getLine().' '.$e->getMessage());
        echo json_encode($data);
        exit;
    } catch (ErrorException $e) {
        setapilog('send_signup_orders:'.$e->getLine().' '.$e->getMessage());
        $data = array('code' => 1, 'data' => '网络错误');
        echo json_encode($data);
        exit();
    }
} /* sendSignupOrders End */

/**
 * RSA签名
 * @param $data 待签名数据
 * @param $private_key_path 商户私钥文件路径
 * return 签名结果
 */
function rsaSign($data, $private_key_path) {
    $priKey = file_get_contents($private_key_path);
    $res = openssl_get_privatekey($priKey);
    openssl_sign($data, $sign, $res);
    openssl_free_key($res);
    //base64编码
    $sign = urlencode(base64_encode($sign));
    return $sign;
}

/**
 * 模拟post进行url请求
 * @param string $url
 * @param array $post_data
 */
function request_post($url = '', $post_data = array()) {
    if (empty($url) || empty($post_data)) {
        return false;
    }

    $o = "";
    foreach ( $post_data as $k => $v ) 
    { 
        $o.= "$k=" . urlencode( $v ). "&" ;
    }
    $post_data = substr($o,0,-1);

    $postUrl = $url;
    $curlPost = $post_data;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);

    return $data;
}
// 验证是身份证
function identify($id) {
    $ch = curl_init();
    $url = 'http://apis.baidu.com/apistore/idservice/id?id='.$id;
    $header = array(
        'apikey:3f476886841e800307821a2edb3b50c6',
    );
    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL, $url);
    $res = curl_exec($ch);
    $result = json_decode($res, true);
    if($result['errNum'] == 0) {
        return true;
    } else {
        return false;
    }
}
?>
