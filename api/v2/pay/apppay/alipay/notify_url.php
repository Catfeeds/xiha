<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电
 脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */

require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
require_once("../../include/common.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result) {//验证成功
	//支付宝交易号
	$trade_no = $_POST['trade_no'];
    //交易付款时间
    $gmt_payment = $_POST['gmt_payment'];
	//交易状态
	$trade_status = $_POST['trade_status'];
    if($_POST['trade_status'] == 'TRADE_FINISHED') {
		//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
				
		//注意：
		//该种交易状态只在两种情况下出现
		//1、开通了普通即时到账，买家付款成功后。
		//2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
    else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
        $bo = str_replace('\\', '', $_POST['body']);
        $body = json_decode($bo, true);
        logResult('post:' . serialize($_POST));
        logResult('body:' . serialize($body));
        logResult('json error:' . json_last_error_msg());
		//验证该通知数据中的out_trade_no是否为嘻哈系统中创建的订单号
        $order_no = $body['order_no'];//嘻哈系统创建的订单号
        $out_trade_no = $_POST['out_trade_no']; //商户订单号
        if ($order_no == $out_trade_no) {
            //判断total_fee是否确实为该订单的实际金额（即嘻哈订单创建时的金额）
             $total_fee = $_POST['total_fee']; //用户实际支付金额
             $order_money = $body['order_money'];//用户应支付金额
             if ($total_fee == $order_money) {
                 //校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方
                $seller_id = $_POST['seller_id']; //收取金额的身份id
                $xiha_id = $alipay_config['partner'];//嘻哈的身份id
                if ($seller_id == $xiha_id) {
                    $order_type = $body['order_type'];//交易类型：signup报名驾校、appoint预约学车
                    $order_status = 1;
                    if($order_type == 'signup') {
                        $order_status = 1;
                    } elseif($order_type == 'appoint') {
                        $order_status = 1;
                    }
                    $data = array(
                        'order_no'      => $order_no,      //订单号
                        'order_type'    => $order_type,    //交易类型
                        'order_money'   => $order_money,   //支付金额    
                        'trade_no'      => $trade_no,      //支付宝交易号
                        'pay_type'      => 1,              //支付方式
                        'order_status'  => $order_status,           //订单状态
                        'zhifu_time'    => $gmt_payment    //交易付款时间
                    );
                   
                    $res = request_post(PAYHOST.'api/v2/order/set_order_status.php', $data);
                    logResult($res);
                    echo "success";     //请不要修改或删除                     
                } else {
                   //验证失败
                    $res = '支付宝交易号 : '.$trade_no.'  收取金额的身份id--seller_id :'.$seller_id.'嘻哈的身份id--partner : '.$xiha_id.'参数不相同';
                    logResult($res);
                    echo "fail";  
                }

             } else {
                   //验证失败
                    $res = '支付宝交易号 : '.$trade_no.'  用户实际支付金额--total_fee :'.$total_fee.'用户应支付金额--order_money : '.$order_money.'参数不相同';
                    logResult($res);
                    echo "fail";  
                }
        } else {
                   //验证失败
                    $res = '支付宝交易号 : '.$trade_no.'  嘻哈系统创建的订单号--order_no :'.$order_no.'商户订单号--out_trade_no : '.$out_trade_no.'参数不相同 ';
                    logResult($res);
                    echo "fail";  
                }
    }
}
else {
    //验证失败
    $res = '交易失败';
    logResult($res);
    echo "fail";
    
    //调试用，写文本函数记录程序运行情况是否正常
    // logResult($res);
    // unlink('log.txt');
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
?>
