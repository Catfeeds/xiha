<?php  
    /**
     * 预约教练时间生成订单
     * @param integer $coach_id 教练ID
     * @param integer $user_id 当前学员ID 
     * @param integer $pay_type 支付方式 1 AliPay,2 OfflinePay,3 WxPay,4 UnionPay
     * @param $time_config_id 时间配置的ID  eg: 3,2,5
     * @param $date 预约的时间  eg: 2016-04-12
     * @param $from 支付平台平源  可选参数 $from = 'h5' 默认为app平台支付
     * @param float $money 总价钱 
     * @return string AES对称加密（加密字段xhxueche）
     * @author gaodacheng
     **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';
    require '../pay/apppay/wxpay/lib/WxPay.Api.php';
    require '../pay/include/common.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/','sendAppointOrders');
    $app->run();

    function sendAppointOrders() {
        Global $app, $crypt;

        $r = $app->request();
        //验证请求方式 POST
        if ( !$r->isPost() ) {
            setapilog('[send_appoint_orders] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            exit( json_encode(array('code' => 106, 'data' => '请求错误')) );
        }

        //验证输入参数
        $validate_ok = validate(
            array(
                'coach_id'          => 'INT',
                'user_id'           => 'INT',
                'pay_type'           => 'INT',
                'time_config_id'    => 'STRING',
                'date'              => 'STRING',
                'money'             => 'INT',
             ),
            $r->params()
        );

        if ( !$validate_ok['pass'] ) {
            exit( json_encode($validate_ok['data']) );
        }
        //测试参数
        //exit( json_encode( $r->params()) );

        //获取参数列表
        $coach_id       = $r->params('coach_id');
        $time_config_id = $r->params('time_config_id');
        $paytype        = $r->params('pay_type');
        $user_id        = $r->params('user_id');
        $datetime       = $r->params('date');
        $dc_money       = $r->params('money');
        /*
        $date           = explode('-', $datetime); // 2016-04-12
        if ( count($date) !== 3 ) {
            exit( json_encode( array('code' => 101, 'data' => '参数错误') ) );
        }
        $year           = $date[0];
        $month          = $date[1];
        $day            = $date[2];
        */
        $date           = explode('-', $datetime);
        if ( count($date) !== 2 ) {
            exit( json_encode( array('code' => 101, 'data' => '参数错误') ) );
        }
        $year           = date('Y', time());
        $month          = $date[0];
        $day            = $date[1];
        $this_month     = date('m', time());
        if ( $this_month == 12 && $month == 1 ) {
            $year = $year + 1;
        }
        $datetime = date('Y-m-d', strtotime("$year/$month/$day"));
        //$order_status   = 1001; // 正在付款中
        $order_status   = 1003; //兼容老的

        //去除空的值 1,2,,3 -> 1,2,3
        $time_config_id = implode(',', array_filter(explode(',', $time_config_id)));

        // 分别存放支付信息
        $alipay = array();
        $wxpay = array();
        $unionpay = array();
        
        try {
            $db = getConnection();
            // 获取当前预约时长
            $time_config = array();
            $time_config = array_filter(explode(',', $time_config_id));
            $i_service_time = count($time_config);    

            // 检测教练合法性
            $sql = "SELECT `l_coach_id` FROM `cs_coach` WHERE `l_coach_id` = '{$coach_id}' AND `order_receive_status` = 1";
            $stmt = $db->query($sql);
            $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($coach_info)) {
                $data = array('code'=>-5, 'data'=>'当前教练不在线不可预约，请选择其他教练');
                echo json_encode($data);
                exit();
            }

            //去除不可预约的时间段,后续优化

            // 判断当前时间段价格是否跟提交的总价格是否相同
            $params = array(
                'user_id'           => $user_id,
                'coach_id'          => $coach_id,
                'time_config_id'    => $time_config_id,
                'date'              => $datetime,
                'coupon_id'         => 1,
                'param_1'           => 1,    
                'param_2'           => 1,    
                'param_3'           => 1,    
                'param_4'           => 1
            );
            $res = request_post(SHOST . 'api/v2/order/order_check.php', $params);
            $check = json_decode(trim($res,chr(239).chr(187).chr(191)), true);
            if($check['code'] == 200) {
                if($check['data']['final_price'] != $dc_money) {
                    exit( json_encode( array( 'code' => -3, 'data' => '价格不正确' ) ) );
                }
            } else {
                if (trim((string)$check['data']) != '') {
                    $error_hint = trim((string)$check['data']);
                } else {
                    $error_hint = '网络异常';
                }
                $data = array('code'=>-5, 'data'=>$error_hint);
                echo json_encode($data);
                exit();
            }

            // 生成订单号
            $s_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);

            //根据支付方式，请求统一下单
            switch ( $paytype ) {
                case 1:
                    $body = json_encode(array('order_no'=>$s_order_no, 'order_time'=>date('Y-m-d H:i',time()),'order_type'=>'appoint','order_money'=>$dc_money) );
                    $param = "_input_charset=\"utf-8\"&body=\"".$body."\"&it_b_pay=\"30m\"&notify_url=\"".PAYHOST."api/v2/pay/apppay/alipay/notify_url.php\"&out_trade_no=\"".$s_order_no."\"&partner=\"".PARTNER."\"&seller=\"".PARTNER."\"&service=\"mobile.securitypay.pay\"&show_url=\"m.alipay.com\"&subject=\"嘻哈学车APP预约学车支付\"&total_fee=\"".$dc_money."\"";
                    $rsa = rsaSign($param, APP_PRIVATE_KEY_PATH);
                    $alipay = array(
                        'orderstring'   => $param,
                        'signstring'    => $rsa,
                    );
                    $order_status = 1003; //1：待完成 2：已完成  3：已取消  101：删除                
                    break;
                case 3:
                    //微信支付预支付订单
                    $input = new WxPayUnifiedOrder();
                    $input->SetOut_trade_no($s_order_no);
                    $input->SetBody('嘻哈学车APP预约学车支付');
                    $input->SetTotal_fee($dc_money * 100); //微信支付单位: 分
                    $input->SetTrade_type('APP'); //App支付
                    $input->SetSpbill_create_ip($r->getIp()); //App支付
                    $input->SetNotify_url(PAYHOST . 'api/v2/pay/apppay/wxpay/notify_url.php'); //异步通知回调地址
                    $attach = array(
                        'order_money'   => $dc_money,
                        'order_no'      => $s_order_no,
                        'order_type'    => 'appoint',
                    );
                    $input->SetAttach(json_encode($attach));
                    $uo_result = WxPayApi::unifiedOrder($input);

                    //订单查询
                    $qinput = new WxPayOrderQuery();
                    $qinput->SetOut_trade_no($s_order_no);
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
                                    //$order_status = 1003; //未付款
                                    $order_status = 1003; // 1：待完成 2：已完成  3：已取消  101：删除
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
                                    $order_status = 1; //1：待完成已付款 2：已完成  3：已取消  101：删除
                                }
                            } elseif ( $oq_result['result_code'] == 'FAIL' ) {
                                exit( json_encode( array('code' => 104, 'data' => '订单不存在') ) );
                            }
                        } else {
                            exit( json_encode( array('code' => 1, 'data' => '网络错误' ) ) );
                        }
                    }
                    break;
                case 4:
                    $url = PAYHOST."/api/v2/pay/apppay/unipay/return_url.php";
                    $params = array(
                        'order_no'      => $s_order_no,
                        'order_time'    => date('YmdHis',time()),
                        'order_money'   => $dc_money * 100,
                        'order_type'      => 'appoint'
                    );
                    $res = request_post($url, $params);
                    $_data = json_decode($res, true);
                    if($_data['code'] == 200) {
                        $unionpay = $_data['data'];
                    } else {
                        exit( json_encode( array('code' => '101', 'data' => $_data['data']) ) );
                    }
                    break;
                default :
                    exit( json_encode( array('code' => '109', 'data' => '不支持的支付方式') ) );
                    break;
            }

            $db->beginTransaction(); // 开启一个事务
            $row = null;
            // 查询是否已下单
            $sql = "SELECT * FROM `cs_coach_appoint_time` as t LEFT JOIN `cs_study_orders` as o ON o.`appoint_time_id` = t.`id` WHERE (o.`i_status` != 3 AND o.`i_status` != 101) AND t.`coach_id` = $coach_id AND t.`time_config_id` IN (".implode(',', $time_config).") AND t.`year` = $year AND t.`month` = $month AND t.`day` = $day";
            $stmt = $db->query($sql);
            $appoint_row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($appoint_row) {
                $data = array('code'=>-1, 'data'=>'已下单');
                echo json_encode($data);
                exit();
            }

            $sql = "INSERT INTO `cs_coach_appoint_time` (`coach_id`, `time_config_id`, `user_id`, `year`, `month`, `day`, `addtime`) ";
            $sql .= "VALUES ('".$coach_id."', '".$time_config_id."', '".$user_id."', '".$year."', '".$month."', '".$day."', ".time().")";

            $row = $db->exec($sql);
            if(!$row) {
                $data = array('code'=>-2, 'data'=>'下单失败！');
                echo json_encode($data);
                exit();
            }
            $appoint_time_id = $db->lastInsertId();
            // 生成订单号
            //$s_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);

            // 获取当前预约时间段的科目名称和牌照名称
            $sql = "SELECT `time_lisence_config_id`, `time_lesson_config_id` FROM `cs_current_coach_time_configuration` WHERE `coach_id` = $coach_id AND `year` = $year AND `month` = $month AND `day` = $day";
            $stmt = $db->query($sql);
            $coach_time_config = $stmt->fetch(PDO::FETCH_ASSOC);
            $lisence_name = array();
            $lesson_name = array();
            if($coach_time_config) {
                if($coach_time_config['time_lisence_config_id']) {
                    $time_lisence_config_id = json_decode($coach_time_config['time_lisence_config_id'], true);
                    foreach ($time_lisence_config_id as $key => $value) {
                        if(in_array($key, $time_config)) {
                            $lisence_name[] = $value;
                        }
                    }
                }
                if($coach_time_config['time_lesson_config_id']) {
                    $time_lesson_config_id = json_decode($coach_time_config['time_lesson_config_id'], true);
                    foreach ($time_lesson_config_id as $key => $value) {
                        if(in_array($key, $time_config)) {
                            $lesson_name[] = $value;
                        }
                    }
                }    
            } else {
                // 从配置表中获取科目，课程
                $sql = "SELECT `license_no`, `subjects` FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config).") AND `status` = 1";
                $stmt = $db->query($sql);
                $_coach_time_config_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if($_coach_time_config_info) {
                    foreach ($_coach_time_config_info as $key => $value) {
                        $lisence_name[] = $value['license_no'];
                        $lesson_name[] = $value['subjects'];
                    }
                }
            }

            $lisence_name = array_unique($lisence_name);
            $lesson_name = array_unique($lesson_name);

            // 获取当前用户的信息
            $sql = "SELECT `s_username`,`s_real_name`, `s_phone` FROM `cs_user` WHERE `l_user_id` = $user_id AND `i_user_type` = 0";
            $stmt = $db->query($sql);
            $userinfo = $stmt->fetch();

            // 获取当前教练的信息
            $sql = "SELECT `s_coach_name`, `s_coach_phone` FROM `cs_coach` WHERE `l_coach_id` = $coach_id";
            $stmt = $db->query($sql);
            $coachinfo = $stmt->fetch();

            $dt_appoint_time = $year.'-'.$date[0].'-'.$date[1];
            $nowts = time();
            $sql = "INSERT INTO `cs_study_orders` (";
            $sql .= " `s_order_no`, ";
            $sql .= " `dt_order_time`,";
            $sql .= " `appoint_time_id`,";
            $sql .= " `time_config_id`,";
            $sql .= " `l_user_id`,";
            $sql .= " `s_user_name`,";
            $sql .= " `s_user_phone`,";
            $sql .= " `l_coach_id`,";
            $sql .= " `s_coach_name`, ";
            $sql .= " `s_coach_phone`,";
            $sql .= " `s_address`,";
            $sql .= " `s_lisence_name`,";
            $sql .= " `s_lesson_name`,";
            $sql .= " `dc_money`,";
            $sql .= " `dt_appoint_time`,";
            $sql .= " `i_start_hour`,";
            $sql .= " `i_end_hour`,";
            $sql .= " `i_service_time`,";
            $sql .= " `i_status`,";
            $sql .= " `s_zhifu_dm`,";
            $sql .= " `dt_zhifu_time`,";
            $sql .= " `deal_type`";
            $sql .= ") VALUES (";
            $sql .= " '".$s_order_no."',";
            $sql .= " '{$nowts}',";
            $sql .= " '{$appoint_time_id}',";
            $sql .= " '{$time_config_id}',";
            $sql .= " '{$user_id}',";
            $sql .= " '{$userinfo['s_real_name']}',";
            $sql .= " '{$userinfo['s_phone']}',";
            $sql .= " '{$coach_id}',";
            $sql .= " '{$coachinfo['s_coach_name']}',";
            $sql .= " '{$coachinfo['s_coach_phone']}',";
            $sql .= " '',";
            $sql .= " '".implode(',', $lisence_name)."',";
            $sql .= " '".implode(',', $lesson_name)."',";
            $sql .= " '{$dc_money}',";
            $sql .= " '{$dt_appoint_time}',";
            $sql .= " 0,";
            $sql .= " 0,";
            $sql .= " '{$i_service_time}',";
            //$sql .= " 1, '".guid(false)."',";
            //正在付款中订单
            $sql .= " {$order_status},";
            //订单唯一识别码通过支付平台返回的商户号，回调时更新
            $sql .= " '',";
            $sql .= " '".date('Y-m-d H:i:s', $nowts)."',";
            //支付类型0，未知类型
            $sql .= " '{$paytype}')";

            $row = $db->exec($sql);
            $res = $db->commit(); 
            if ( !$res ) {
                $db->rollBack();
                exit( json_encode( array('code' => 400, 'data' => '下单失败') ) );
            }
            // 获取时间段
            $time_config_arr = array();
            if(!empty($time_config)) {
                $sql = "SELECT * FROM `cs_coach_time_config` WHERE `id` IN (".implode(',', $time_config).")";
                $stmt = $db->query($sql);
                $coach_time_config_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $time_config_arr = array();
                foreach ($coach_time_config_info as $key => $value) {
                    $start_minute = $value['start_minute'] == 0 ? '00' : $value['start_minute'];
                    $end_minute = $value['end_minute'] == 0 ? '00' : $value['end_minute'];
                    $time_config_arr[] = $value['start_time'].':'.$start_minute.'-'.$value['end_time'].':'.$end_minute;
                }    
            }
            
            $db = null;
            $data = array(
                'code'  => 200, 
                'data'  => array(
                    'pay' => array(
                        'wxpay'    => $wxpay,
                        'alipay'   => $alipay,
                        'unionpay' => $unionpay,
                    ),
                    'order_info'    => array(
                        'order_id'  => $s_order_no,
                    ),
                    /*
                    'order_no'          => $s_order_no,
                    'order_type'        => 'appoint',
                    'order_money'       => $dc_money,
                    'appoint_info'      => array(
                        'appoint_date'      => $datetime,
                        'appoint_time'      => $time_config_arr,
                    ),
                    */
                ),
            );
            echo json_encode($data);
        } catch (PDOException $e) {
            $db->rollBack(); // 执行任务失败 事务回滚
            setapilog('[send_appoint_orders] [:error] [params'. serialize($r->params()) .'] [1 ' . $e->getMessage() . ']');    
            $data = array('code'=>1, 'data'=>'网络错误');
            echo json_encode($data);
        }
    }
    /* send_appoint_orders End */

    function guid($opt = true){       //  Set to true/false as your default way to do this.

        if( function_exists('com_create_guid')) {
            if( $opt ){ 
                return com_create_guid(); 
            } else { 
                return trim( com_create_guid(), '{}' ); 
            }
        } else {
            mt_srand( (double)microtime() * 10000 );    // optional for php 4.2.0 and up.
            $charid = strtoupper( md5(uniqid(rand(), true)) );
            $hyphen = chr( 45 );    // "-"
            $left_curly = $opt ? chr(123) : "";     //  "{"
            $right_curly = $opt ? chr(125) : "";    //  "}"
            $uuid = $left_curly
                . substr( $charid, 0, 8 ) . $hyphen
                . substr( $charid, 8, 4 ) . $hyphen
                . substr( $charid, 12, 4 ) . $hyphen
                . substr( $charid, 16, 4 ) . $hyphen
                . substr( $charid, 20, 12 )
                . $right_curly;
            return $uuid;
        }
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

    
?>
