<?php
/* *
 * 类名：AlipayNotify
 * 功能：支付宝通知处理类
 * 详细：处理支付宝各接口通知返回
 * 版本：3.2
 * 日期：2011-03-25
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考

 *************************注意*************************
 * 调试通知返回时，可查看或改写log日志的写入TXT里的数据，来检查通知返回是否正常
 */


require_once("alipay_core.function.php");
require_once("alipay_md5.function.php");

class AlipayNotify {
    /**
     * HTTPS形式消息验证地址
     */
	var $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
	/**
     * HTTP形式消息验证地址
     */
	var $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
	var $alipay_config;

	function __construct($alipay_config){
		$this->alipay_config = $alipay_config;
	}
    function AlipayNotify($alipay_config) {
    	$this->__construct($alipay_config);
    }
    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
	function verifyNotify(){
		//	在支付宝的业务通知中，只有交易通知状态为TRADE_SUCCESS或TRADE_FINISHED时，支付宝才会认定为买家付款成功
		$_allow_status = array('TRADE_FINISHED', 'TRADE_SUCCESS');
		if(empty($_POST)) {
			//log_result(array('notify:fail', $_POST));
			return false;
		}else if(!in_array($_POST['trade_status'], $_allow_status)){
            //log_result(array('notify:fail', $_POST));
            return false;
        }else {
        	//	通知数组
        	$notify = paraFilter($_POST);
        	/*参数列表
    			out_trade_no 	商户订单号 原支付请求的商户订单号 6823789339978248
				trade_no 		支付宝交易号 支付宝交易凭证号 2013112011001004330000121536
				notify_id 		通知校验ID 通知校验ID ac05099524730693a8b330c5ecf72da9786
				sign 			签名 601510b7970e52cc63db0f44997cf70e
        		sign_type 		签名类型 商户生成签名字符串所使用的签名算法类型，目前支持RSA2和RSA，推荐使用RSA2 RSA2
				trade_status 	交易状态 交易目前所处的状态
				total_amount	订单金额 本次交易支付的订单金额，单位为人民币（元）20
			*/

			//	生成签名结果
			$isSign = $this->getSignVeryfy($_POST, $_POST["sign"]);
			//	获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
			$responseTxt = 'false';
			if (! empty($_POST["notify_id"])) {
				$responseTxt = $this->getResponse($_POST["notify_id"]);
			}
			//	写日志记录
			if ($isSign) {
				$isSignStr = 'true';
			}else {
				$isSignStr = 'false';
			}
			$log_text = "responseTxt=".$responseTxt."\n notify_url_log:isSign=".$isSignStr.",";
			$log_text = $log_text.createLinkString($_POST);
			//log_result($log_text);

			//	验证
			//$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
			//isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
			//preg_match("/true$/i",$responseTxt)
			$responseTxt = true;
			if ($responseTxt && $isSign) {
				$trade = array('code'=>'alipay','out_trade_no'=>$notify['out_trade_no'], 'trade_no'=>$notify['trade_no'], 'trade_status'=>$_POST['trade_status'], 'total_amount'=>$notify['total_amount'],'seller_id'=>$notify['seller_id']);
                //log_result(array('notify:success', $trade));
                return $trade;
				//return true;
			} else {
				return false;
			}
		}
	}

    /**
     * 针对return_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
	function verifyReturn(){
		$_allow_status = array('TRADE_FINISHED', 'TRADE_SUCCESS');
		if(empty($_GET)) {
			//log_result(array('notify:fail', $_GET));
			return false;
		}else if(!in_array($_GET['trade_status'], $_allow_status)){
            //log_result(array('notify:fail', $_GET));
            return false;
        }else {
        	//	通知数组
        	$notify = paraFilter($_GET);
        	unset($notify['con']);
        	unset($notify['act']);
        	$get_params = $_GET;
        	unset($get_params['con']);
        	unset($get_params['act']);
			//	生成签名结果
			$isSign = $this->getSignVeryfy($get_params, $_GET["sign"]);

			//	获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
			$responseTxt = 'false';
			if (! empty($_GET["notify_id"])) {
				$responseTxt = $this->getResponse($_GET["notify_id"]);
			}
			//	写日志记录
			if ($isSign) {
				$isSignStr = 'true';
			}
			else {
				$isSignStr = 'false';
			}
			$log_text = "responseTxt=".$responseTxt."\n return_url_log:isSign=".$isSignStr.",";
			$log_text = $log_text.createLinkString($get_params);
			//log_result($log_text);

			//	验证
			//$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
			//isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
			$responseTxt = true;
			//preg_match("/true$/i",$responseTxt
			if ($responseTxt && $isSign) {
				$trade = array('code'=>'alipay','out_trade_no'=>$notify['out_trade_no'], 'trade_no'=>$notify['trade_no'], 'trade_status'=>$_GET['trade_status'], 'total_amount'=>$notify['total_fee'],'seller_id'=>$notify['seller_id']);
                //log_result(array('notify:success', $trade));
                return $trade;
			} else {
				return false;
			}
		}
	}

    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
	function getSignVeryfy($para_temp, $sign) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = paraFilter($para_temp);

		//对待签名参数数组排序
		$para_sort = argSort($para_filter);

		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = createLinkstring($para_sort);

		$isSgin = false;
		switch (strtoupper(trim($this->alipay_config['sign_type']))) {
			case "MD5" :
				$isSgin = md5Verify($prestr, $sign, $this->alipay_config['key']);
				break;
			default :
				$isSgin = false;
		}

		return $isSgin;
	}

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
	function getResponse($notify_id) {
		$transport = strtolower(trim($this->alipay_config['transport']));
		$partner = trim($this->alipay_config['partner']);
		$veryfy_url = '';
		if($transport == 'https') {
			$veryfy_url = $this->https_verify_url;
		}
		else {
			$veryfy_url = $this->http_verify_url;
		}
		$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
		$responseTxt = getHttpResponseGET(urlencode($veryfy_url), $this->alipay_config['cacert']);

		return $responseTxt;
	}
}
?>
