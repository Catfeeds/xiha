<?php 
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';
require_once '../../../include/config.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();

$attach = urldecode($_GET['attach']);  // 订单相关数据
$attach_arr = explode('|', $attach);
// print_r($attach_arr);

// 报名驾校
if($attach_arr[0] == 1) {
	if(!isset($attach_arr[10])) {
		echo "<script>alert('参数错误，支付失败'); history.back(-1);</script>";
		exit();
	}
	$total_fee = isset($attach_arr[10]) ? $attach_arr[10] : 3000;
	$body = '报名驾校订单支付';

// 预约学车
} else if($attach_arr[0] == 2) {
	if(!isset($attach[5])) {
		echo "<script>alert('参数错误，支付失败'); history.back(-1);</script>";
		exit();
	}
	$total_fee = isset($attach_arr[5]) ? $attach_arr[5] : 130;
	$body = '预约学车订单支付';
} else {
	echo "<script>alert('参数错误，支付失败'); history.back(-1);</script>";
	exit();
}
// echo $body;

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($body);   // 商品描述
$input->SetAttach($attach);  // 订单自定义数据
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
// $input->SetTotal_fee($total_fee * 100);
$input->SetTotal_fee('1');
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");
$input->SetNotify_url(PAY_HOST."/wxpay/example/notify.php");
// $input->SetNotify_url("http://api.xihaxueche.com/service/api/wappay/wxpay/example/notify.php");

$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
// echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
// printf_info($order);
$jsApiParameters = $tools->GetJsApiParameters($order);
// print_r($jsApiParameters);

//获取共享收货地址js函数参数
// $editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title><?php echo $body; ?></title>
    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				// alert(res.err_code+res.err_desc+res.err_msg);
				if(res.err_msg == 'get_brand_wcpay_request:cancel') {
					alert('你已取消了付款');
					history.back(-1);
				} else if(res.err_msg == 'get_brand_wcpay_request:ok') {
					alert('你已付款成功,请下载最新app查看订单');
					var type = "<?php echo $attach_arr[0]; ?>";

					// 报名驾校
					if(type == 1) {
						location.href="<?php echo REDIRECT_URL; ?>/school/default.php?id=<?php echo $attach_arr[2]; ?>"
					} else if(type == 2) {
						location.href="<?php echo REDIRECT_URL; ?>/coach/detail.php?id=<?php echo $attach_arr[1]; ?>"
					} else {
						history.back(-1);
					}
				}

			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>
	<script type="text/javascript">
	window.onload = function() {
		callpay();
	}
	//获取共享地址
	// function editAddress()
	// {
	// 	WeixinJSBridge.invoke(
	// 		'editAddress',
	// 		<?php echo $editAddress; ?>,
	// 		function(res){
	// 			var value1 = res.proviceFirstStageName;
	// 			var value2 = res.addressCitySecondStageName;
	// 			var value3 = res.addressCountiesThirdStageName;
	// 			var value4 = res.addressDetailInfo;
	// 			var tel = res.telNumber;
				
	// 			alert(value1 + value2 + value3 + value4 + ":" + tel);
	// 		}
	// 	);
	// }
	
	// window.onload = function(){
	// 	if (typeof WeixinJSBridge == "undefined"){
	// 	    if( document.addEventListener ){
	// 	        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
	// 	    }else if (document.attachEvent){
	// 	        document.attachEvent('WeixinJSBridgeReady', editAddress); 
	// 	        document.attachEvent('onWeixinJSBridgeReady', editAddress);
	// 	    }
	// 	}else{
	// 		editAddress();
	// 	}
	// };
	
	</script>
</head>
<body>
<!--     <br/>
    <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px">1分</span>钱</b></font><br/><br/>
	<div align="center">
		<button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>
	</div> -->
</body>
</html>