<?php  
    /**
     * 预约学车/报名驾校未支付订单重新付款
     * @param int $user_id 用户id
     * @param string $order_type 预约学车 appoint 报名驾校 signup
     * @param string $order_no 数据库里已经存在的订单号
     * @param float $order_money 总价钱 
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
    $app->any('/','WxPay');
    $app->response->headers->set('Content-Type', 'application/json; charset=utf8');
    $app->run();

    function WxPay() {
        Global $app, $crypt;

        //验证请求方式 POST
        $r = $app->request();
        if ( !$r->isPost() ) {
            setapilog('[send_appoint_orders] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            exit( json_encode(array('code' => 106, 'data' => '请求错误')) );
        }

        //验证输入参数
        $validate_ok = validate(
            array(
                'user_id'           => 'INT',
                'order_type'        => 'STRING',
                'order_no'          => 'STRING',
                'order_money'       => 'INT',
             ),
            $r->params()
        );

        // configure
        $allowed_order_type = array(
            'signup',
            'appoint',
        );

        if ( !$validate_ok['pass'] ) {
            echo json_encode($validate_ok['data']);
            return ;
        }

        //获取参数列表
        $user_id        = $r->params('user_id');
        $order_type     = $r->params('order_type');
        $order_money    = $r->params('order_money');
        $order_no       = $r->params('order_no');

        if ( !in_array($order_type, $allowed_order_type) ) {
            echo json_encode(array('code' => 102, 'data' => '参数错误'));
            return ;
        }

        $order_table_field = array(
            'appoint' => array(
                'table_name'        => 'study_orders',
                'order_no_field'    => 's_order_no',
                'unpaid_status'     => '1003',
                'user_id_field'     => 'l_user_id',
                'status_field'      => 'i_status',
                'order_money_field' => 'dc_money',
            ),
            'signup' => array(
                'table_name'        => 'school_orders',
                'order_no_field'    => 'so_order_no',
                'unpaid_status'     => '4',
                'user_id_field'     => 'so_user_id',
                'status_field'      => 'so_order_status',
                'order_money_field' => 'so_final_price',
            ),
        );

        try {
            $db = getConnection();

            $fields_buf = array(
                $order_table_field[$order_type]['order_no_field'],
                $order_table_field[$order_type]['user_id_field'],
                $order_table_field[$order_type]['unpaid_status'],
            );
            $sql = " SELECT `dt_zhifu_time` FROM `".DBPREFIX."{$order_table_field[$order_type]['table_name']}` WHERE `{$order_table_field[$order_type]['order_no_field']}` = '{$order_no}' AND `{$order_table_field[$order_type]['status_field']}` = '{$order_table_field[$order_type]['unpaid_status']}' AND `{$order_table_field[$order_type]['user_id_field']}` = '{$user_id}' AND `{$order_table_field[$order_type]['order_money_field']}` = '{$order_money}' ";
            $stmt = $db->query($sql);
            $order_buf = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( empty($order_buf) ) {
                $data = array('code' => 104, 'data' => '无此订单');
                echo json_encode($data);
                return ;
            }

            //微信支付预支付订单
            $wxpay = array();
            $input = new WxPayUnifiedOrder();
            $input->SetOut_trade_no($order_no);
            $input->SetBody('嘻哈学车APP预约学车支付');
            $input->SetTotal_fee($order_money * 100); //微信支付单位: 分
            $input->SetTrade_type('APP'); //App支付
            $input->SetSpbill_create_ip($r->getIp()); //App支付
            $input->SetNotify_url(PAYHOST . 'api/v2/pay/apppay/wxpay/notify_url.php'); //异步通知回调地址
            $attach = array(
                'order_money'   => $order_money,
                'order_no'      => $order_no,
                'order_type'    => $order_type,
            );
            $input->SetAttach(json_encode($attach));
            $uo_result = WxPayApi::unifiedOrder($input);

            //订单查询
            $qinput = new WxPayOrderQuery();
            $qinput->SetOut_trade_no($order_no);
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
                                $stringSignTemp = $stringA . "key=" . WxPayConfig::KEY;
                                $sign = strtoupper(md5($stringSignTemp));
                                $wxpay['sign'] = $sign;
                            }
                        } elseif ( $oq_result['trade_state'] == 'SUCCESS' ) {
                            //$order_status = 1; //1：待完成已付款 2：已完成  3：已取消  101：删除
                        }
                    } elseif ( $oq_result['result_code'] == 'FAIL' ) {
                        echo json_encode( array('code' => 104, 'data' => '订单不存在') );
                        return ;
                    }
                } else {
                    echo json_encode( array('code' => 1, 'data' => '网络错误' ) );
                    return ;
                }
            }

            $data = array(
                'code'  => 200, 
                'data'  => $wxpay,
            );
            echo json_encode($data);
            return ;
        } catch (PDOException $e) {
            setapilog('[wxpay] [:error] [params'. serialize($r->params()) .'] [' . $e->getLine().' '. $e->getMessage() . ']');    
            $data = array('code'=>1, 'data'=>'网络错误');
            echo json_encode($data);
            return ;
        }
    }
    /* wxpay End */

?>
