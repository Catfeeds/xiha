<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "lib/WxPay.Api.php";
require_once 'lib/WxPay.Notify.php';
require_once 'lib/WxPay.Config.php'; //加载商户号和appid配置
require_once 'example/log.php';
require_once '../../include/common.php';

//初始化日志
$logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);
//Log::DEBUG("Notify Begin: I get your pay result.");

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		//Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}

    	$attach = json_decode( $data['attach'], true ); //attach为商家数据包
        $param = array();
        $param['trade_no'] = $data['transaction_id']; //三方平台交易号
        $param['order_no'] = $attach['order_no']; //商户订单号
        $param['order_type'] = $attach['order_type']; // signup or appoint
        //$param['order_status'] = 2; //老的代码表示法: 已完成
        $param['pay_type'] = 3; // 3 means WxPay
        $param['order_money'] = $attach['order_money'];
        $param['zhifu_time'] = date('Y-m-d H:i:s', strtotime($data['time_end']));
		//查询订单，判断订单真实性
        // 比对信息正确性，修改订单状态
        //Log::DEBUG('data:' . json_encode($data));
        //Log::DEBUG('param:' . json_encode($param));
		if( $this->Queryorder( $data["transaction_id"] )
            && $data['appid'] == WxPayConfig::APPID //应用号
            && $data['mch_id'] == WxPayConfig::MCHID //商户号
            && $data['total_fee'] == $attach['order_money'] * 100  //付款金额
            && $data['out_trade_no'] == $attach['order_no']  //商户订单号
        ) {
            if ( $attach['order_type'] == 'signup' ) {
                $param['order_status'] = 1;
            } elseif ( $attach['order_type'] == 'appoint') {
                $param['order_status'] = 1;
            }
		    $res = $this->request_post(PAYHOST . 'api/v2/order/set_order_status.php', $param);
		    Log::DEBUG('SetOrderStatus:' . $res);
		}
		return true;
	}

	/**
	 * 模拟post进行url请求
	 * @param string $url
	 * @param array $post_data
	 */
	public function request_post($url = '', $post_data = array()) {
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
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);

