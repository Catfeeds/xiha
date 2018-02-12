<?php
    /**
    * 取消在线支付未付款的订单(使用场景是点击线上支付跳转界面 然后返回来调用的接口 暂取消使用了 （在老版本在使用）)
    * @param string $order_no 订单号
    * @param integer $user_id 用户id
    * @param string $order_type 订单类型 signup/appoint
    * @package api/v2/order
    * @author gaodacheng
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
    $app->any('/', 'cancelOnlineOrder');
    $app->run();

    function cancelOnlineOrder() {
        global $app, $crypt;

        // configureation
        $allowed_order_type = array('signup', 'appoint');
        $online_pay_type_ar = array(1, 3, 4,);

        $online_pay_type    = implode(',', $online_pay_type_ar);

        //验证请求方式 POST
        $r = $app->request();
        if ( !$r->isPost() ) {
            setapilog('[update_user_info] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            echo json_encode(array('code' => 106, 'data' => '请求错误'));
            return ;
        }

        //取得参数列表
        $validate_ok = validate(array('order_no' => 'STRING', 'order_type' => 'STRING', 'user_id' => 'INT'), $r->params());
        if ( !$validate_ok['pass'] ) {
            echo json_encode($validate_ok['data']);
            return ;
        }

        $order_no   = $r->params('order_no');
        $order_type = $r->params('order_type');
        $user_id    = $r->params('user_id');
        if ( !in_array( $order_type, $allowed_order_type) ) {
            echo json_encode( array('code' => 102, 'data' => '参数错误') );
            return ;
        }

        // 兼容不同表，不同字段，不同值
        if ( $order_type == 'signup' ) {
            $order_table = 'school_orders';
            $order_status_field_name = 'so_order_status';
            $uid_field_name = 'so_user_id';
            $order_no_field_name = 'so_order_no';
            $unpaid_status_code = 1; //待完成未付款
            $cancel_status_code = 2; //已取消
            $pay_type = 'so_pay_type';
        } elseif ( $order_type == 'appoint' ) {
            $order_table = 'study_orders';
            $order_status_field_name = 'i_status';
            $uid_field_name = 'l_user_id';
            $order_no_field_name = 's_order_no';
            $unpaid_status_code = 1003; //待完成未付款
            $cancel_status_code = 3; //已取消
            $pay_type_field_name = 'deal_type';
        }

        try {
            //建立数据库连接
            $db = getConnection();

            $sql = " SELECT * FROM `". DBPREFIX . $order_table ."` WHERE `{$uid_field_name}` = :uid AND `{$order_no_field_name}` = :order_no AND `{$pay_type_field_name}` IN ({$online_pay_type}) AND `{$order_status_field_name}` = '{$unpaid_status_code}' ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uid', $user_id, PDO::PARAM_INT);
            $stmt->bindParam('order_no', $order_no, PDO::PARAM_STR);
            $stmt->execute();
            $order_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( !$order_info ) {
                $data = array(
                    'code' => '104',
                    'data' => '订单错误',
                );
                echo json_encode($data);
                return;
            }

            $sql = " UPDATE `". DBPREFIX ."{$order_table}` SET `{$order_status_field_name}` = :cancel_status_code WHERE `{$order_status_field_name}` = :unpaid_status_code AND `{$order_no_field_name}` = :order_no AND `{$uid_field_name}` = :uid AND `{$pay_type_field_name}` IN ({$online_pay_type}) ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('unpaid_status_code', $unpaid_status_code, PDO::PARAM_INT);
            $stmt->bindParam('cancel_status_code', $cancel_status_code, PDO::PARAM_INT);
            $stmt->bindParam('uid', $user_id, PDO::PARAM_INT);
            $stmt->bindParam('order_no', $order_no, PDO::PARAM_STR);
            $cancel_order_ok = $stmt->execute();

            if ( $cancel_order_ok ) {
                $data = array(
                    'code' => 200,
                    'data' => 'Cancel order success',
                );
            } else {
                $data = array(
                    'code' => 400,
                    'data' => 'Cancel order failed',
                );
            }

            echo json_encode($data);
            return ;

            //关闭数据库
            $db = null;
        } catch ( PDOException $e ) {
            setapilog('[comment_like] [:error] [client ' . $r->getIP() . '] [user_id,type % ' . $user_id . '] [1 网络异常] [' . $e->getLine() . ' ' . $e->getmessage() . ']');
            echo json_encode(array('code' => 1, 'data' => '网络异常'));
            return ;
        }
    }
?>
