<?php
	// header ( 'Content-type:text/html;charset=utf-8' );
	include_once 'sdk/acp_service.php';
	if(!isset($_REQUEST['order_no']) || !isset($_REQUEST['order_time']) || !isset($_REQUEST['order_money']) || !isset($_REQUEST['order_type'])) {
		exit( json_encode(array('code'=>101, 'data'=>'参数错误')) );
	}
	$params = array(
		
		//以下信息非特殊情况不需要改动
		'version' => '5.0.0',                 //版本号
		'encoding' => 'utf-8',				  //编码方式
		'txnType' => '01',				      //交易类型
		'txnSubType' => '01',				  //交易子类
		'bizType' => '000201',				  //业务类型
		'frontUrl' =>  SDK_FRONT_NOTIFY_URL,  //前台通知地址 控件接入方式无作用
		'backUrl' => SDK_BACK_NOTIFY_URL,	  //后台通知地址
		'signMethod' => '01',	              //签名方法
		'channelType' => '08',	              //渠道类型，07-PC，08-手机
		'accessType' => '0',		          //接入类型
		'currencyCode' => '156',	          //交易币种，境内商户固定156
		
		//TODO 以下信息需要填写
		// 'merId' => $_POST["merId"],		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数 测试： 777290058110048 生产环境商户号： 802310053110697
		'merId' => '802310053110697',		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数 生产环境商户号： 802310053110697
		'orderId' => $_REQUEST["order_no"],	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
		'txnTime' => $_REQUEST["order_time"],	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
		'txnAmt' => $_REQUEST["order_money"],	//交易金额，单位分，此处默认取demo演示页面传递的参数
		'reqReserved' =>'{"order_type":"'.$_REQUEST['order_type'].'"}',        //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据

		//TODO 其他特殊用法请查看 pages/api_05_app/special_use_purchase.php
	);
	
	AcpService::sign ( $params ); // 签名
	$url = SDK_App_Request_Url;

	$result_arr = AcpService::post ($params,$url);
	$phplog = new PhpLog(SDK_LOG_FILE_PATH, 'PRC', SDK_LOG_LEVEL);
	$phplog->Log(htmlentities(createLinkString( $result_arr , false, true )) . "<br>\n", SDK_LOG_LEVEL, SDK_LOG_FILE_PATH, 1);

	if(count($result_arr)<=0) { //没收到200应答的情况
		exit( json_encode(array('code'=>400, 'data'=>'签名失败')) );
	}
	// ECHO "<pre>";
	// print_r($params);
	// print_r($result_arr);
	// ECHO "</pre>";

	/*params : Array
	(
	    [version] => 5.0.0
	    [encoding] => utf-8
	    [txnType] => 01
	    [txnSubType] => 01
	    [bizType] => 000201
	    [frontUrl] => http://localhost/php/api/unipay/unipay_demo/demo/api_05_app/FrontReceive.php
	    [backUrl] => http://localhost/php/api/unipay/unipay_demo/demo/api_05_app/BackReceive.php
	    [signMethod] => 01
	    [channelType] => 08
	    [accessType] => 0
	    [currencyCode] => 156
	    [merId] => 777290058110048
	    [orderId] => 20160415104705
	    [txnTime] => 20160415104705
	    [txnAmt] => 1000
	    [certId] => 69828314201
	    [signature] => HK8NguBqjxLW2QDF1Cxt9PuCTpQp+EpsHDe95fBe8ZYNn6rOvxN0MQxFWl6NoLpWu+LvmrPlCp4nHnKyPcwWPtZYTwMozxzQWP1JW2Hl1Y46lWsDbuoUCQGKpYzytW+++pGf9YIFc8AKQP/GSF/8/y17gpD1/r2tdJPzY5zUdkA08BszWwzJA1oFeF4O777oFBdb3DHekYMJbjFpLwYIl4X9R78qA3s0om2CafpJuQDn+OLM7g99fa80Yqrnp8MNFjgwUr9mbtqSzp4ooClF0rW85b/H097wuT6p+QxLD85tIRRmQt+OsY02+LFbohKJGHefb5oNkYGj0TCJevtxyg==
	)*/
	/*result_arr : Array
	(
	    [accessType] => 0
	    [bizType] => 000201
	    [certId] => 69597475696
	    [encoding] => utf-8
	    [merId] => 802310053110697
	    [orderId] => 20160414215041
	    [respCode] => 00
	    [respMsg] => 成功[0000000]
	    [signMethod] => 01
	    [tn] => 201604142150419192868
	    [txnSubType] => 01
	    [txnTime] => 20160414215041
	    [txnType] => 01
	    [version] => 5.0.0
	    [signature] => LE6vCFIesU/n5gFUY6uqKbXwgMSJ282x6pw1+PSwcQXyUG8Yw6h9+Be4pUsTbd0EqYeimMefkdzxx2xvEhuV6cnCBsKodxT6sms0Ufee5CxByVUfDp2vNgODmmoXnV/ttxNk1qC/bYzHZrie1Z7spGlvEJT0PMrEraN/oRLiCHoGmAqvV5tLfhdmL9U3Fp32I1q43WM2ffgS4+zPiDdBVUXeVuzRr7gFfZ35tfC5rDDJEdM6jH5z9+kFnYPa+RjcDJWR/rLHnhfFsMg7FhUCe0rd1NLqUdHvdELK2bDUuiiLb4RrQ8kboAnC3RYjbMzn7HBOv6Cjbs7OSenHbd9obQ==
	)*/

	// createLinkString $params : version=5.0.0&encoding=utf-8&txnType=01&txnSubType=01&bizType=000201&frontUrl=http%3A%2F%2Flocalhost%2Fphp%2Fapi%2Funipay%2Funipay_demo%2Fdemo%2Fapi_05_app%2FFrontReceive.php&backUrl=http%3A%2F%2Flocalhost%2Fphp%2Fapi%2Funipay%2Funipay_demo%2Fdemo%2Fapi_05_app%2FBackReceive.php&signMethod=01&channelType=08&accessType=0&currencyCode=156&merId=777290058110048&orderId=20160415104705&txnTime=20160415104705&txnAmt=1000&certId=69828314201&signature=HK8NguBqjxLW2QDF1Cxt9PuCTpQp%2BEpsHDe95fBe8ZYNn6rOvxN0MQxFWl6NoLpWu%2BLvmrPlCp4nHnKyPcwWPtZYTwMozxzQWP1JW2Hl1Y46lWsDbuoUCQGKpYzytW%2B%2B%2BpGf9YIFc8AKQP%2FGSF%2F8%2Fy17gpD1%2Fr2tdJPzY5zUdkA08BszWwzJA1oFeF4O777oFBdb3DHekYMJbjFpLwYIl4X9R78qA3s0om2CafpJuQDn%2BOLM7g99fa80Yqrnp8MNFjgwUr9mbtqSzp4ooClF0rW85b%2FH097wuT6p%2BQxLD85tIRRmQt%2BOsY02%2BLFbohKJGHefb5oNkYGj0TCJevtxyg%3D%3D
	
	// createLinkString $result_arr : txnType=01&respCode=11&frontUrl=http%3A%2F%2Flocalhost%2Fphp%2Fapi%2Funipay%2Funipay_demo%2Fdemo%2Fapi_05_app%2FFrontReceive.php&channelType=08&currencyCode=156&merId=777290058110048&txnSubType=01&txnAmt=1000&version=5.0.0&signMethod=01&backUrl=http%3A%2F%2Flocalhost%2Fphp%2Fapi%2Funipay%2Funipay_demo%2Fdemo%2Fapi_05_app%2FBackReceive.php&certId=69597475696&encoding=utf-8&respMsg=%5B9100004%5DSignature+verification+failed&bizType=000201&signature=jvR7mKOhwAlMi%2BDcBdcwed5coZVQfWbZu6jShr3CBAKJhhm4F%2Flik0wh9VYXfV4LPZq7yZaAOjZbOEXwnZOTORZAXfcQqHEZC75dBTK92GQ3CW7DCnv3Qx4vZ%2FicOVyaEknjfIXqGdKiWlhtiEByLf7dF1YqIOMljTZk6NiZHP1WG%2BUsMEJOgXdNySTqKkxLeKKMpMq3mTN1XdoN2ogbh%2F8865T07yF5PKsWrH%2FkwX%2B4zAwGx7Prdx6D2voveBDXDL8DAaaAlACrpVVr3nne4M1sSJJ%2FgrhnGlt2Ox3dJdCzXWa8mGd8MPkv7k9BFf5rhSSJeaJZ24m8yZQ5EXryBg%3D%3D&orderId=20160415104705&txnTime=20160415104705&accessType=0

	// printResult ($url, $params, $result_arr ); //页面打印请求应答数据
	$phplog = new PhpLog(SDK_LOG_FILE_PATH, 'PRC', SDK_LOG_LEVEL);
	$phplog->Log(htmlentities(createLinkString( $result_arr , false, true )) . "<br>\n", SDK_LOG_LEVEL, SDK_LOG_FILE_PATH, 1);

	if (!AcpService::validate ($result_arr) ){
		exit( json_encode(array('code'=>106, 'data'=>'应答报文签名验证失败')) );
	}

	// echo "应答报文验签成功<br>\n";
	if ($result_arr["respCode"] == "00"){
	    //成功
	    //TODO
	    // echo "成功接收tn：" . $result_arr["tn"] . "<br>\n";
	    // echo "后续请将此tn传给手机开发，由他们用此tn调起控件后完成支付。<br>\n";
	    // echo "手机端demo默认从仿真获取tn，仿真只返回一个tn，如不想修改手机和后台间的通讯方式，【此页面请修改代码为只输出tn】。<br>\n";
		exit( json_encode(array('code'=>200, 'data'=>array('tn'=>$result_arr["tn"]))) );
	} else {
	    //其他应答码做以失败处理
	     //TODO
		$err_code = preg_match('/\[([0-9]+)\]/', $result_arr['respMsg'], $matches);
		exit( json_encode(array('code'=>104, 'data'=>'应答报文签名验证失败，错误码：'.$matches[0])) );
	}


	/**
	 * 打印请求应答
	 *
	 * @param
	 *        	$url
	 * @param
	 *        	$req
	 * @param
	 *        	$resp
	 */
	function printResult($url, $req, $resp) {
		echo "=============<br>\n";
		echo "地址：" . $url . "<br>\n";
		echo "请求：" . str_replace ( "\n", "\n<br>", htmlentities ( createLinkString ( $req, false, true ) ) ) . "<br>\n";
		echo "应答：" . str_replace ( "\n", "\n<br>", htmlentities ( createLinkString ( $resp , false, true )) ) . "<br>\n";
		echo "=============<br>\n";
	}


?>
