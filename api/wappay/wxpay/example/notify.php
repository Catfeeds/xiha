<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';
require_once '../../../include/config.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
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
		// 预约学车
		$attach = explode('|', $data['attach']);
		Log::DEBUG('OK--------------------------------------------------');
		// 报名驾校
		$attach_arr = array();
		if($attach[0] == 1) {
    		$combine = array('entrance', 'id', 'sid', 'ptype', 'user_name', 'user_phone', 'user_identify_id', 'licence', 'order_type', 'uid', 'money', 'access_token');
    		$attach_arr = array_combine($combine, $attach);
			$attach_arr['type'] = 3;
			Log::DEBUG("wxpay_apply:".json_encode($attach_arr) );
	        $res = $this->request_post(API_URL.'school_payment.php', $attach_arr);
	        // $res = $this->request_post('http://api.xihaxueche.com/service/api/school_payment.php', $attach_arr);

    	// 预约学车
		} else {
    		$combine = array('entrance', 'coach_id', 'time_config_id', 'user_id', 'date', 'money');
    		$attach_arr = array_combine($combine, $attach);
			$attach_arr['type'] = 3;
			$attach_arr['s_zhifu_dm'] = $data['out_trade_no'];
			Log::DEBUG("wxpay_order:".json_encode($attach_arr) );
			$res = $this->request_post(API_URL.'send_ali_orders.php', $attach_arr);
			// $res = $this->request_post('http://api.xihaxueche.com/service/api/send_ali_orders.php', $attach_arr);
		}
		
		Log::DEBUG('OK:'.$this->Queryorder($data["transaction_id"]));
		
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
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
