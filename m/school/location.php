<?php
	require_once '../include/config.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
		<title></title>
		<link rel="stylesheet" href="../assets/css/mui.min.css" />
		<link rel="stylesheet" href="../assets/css/style.css" />
		<link rel="stylesheet" href="../assets/font/iconfont/iconfont.css" />
		<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
		<script type="text/javascript" src="../assets/js/mui.min.js"></script>
		<script src="../assets/js/cookie.min.js"></script>
		<script src="../assets/js/getlocation.js"></script>
		<script>
			if(!Cookies.get('lng') || !Cookies.get('lat')) {
				location.href="index.php";
			}
		</script>
		<style type="text/css">
			.mui-segmented-control.mui-segmented-control-inverted~.mui-slider-progress-bar {top: 82px; position: fixed;}
			.mui-slider-indicator.mui-segmented-control {position: fixed; z-index: 999; top: 44px; }
		</style>
		<script type="text/javascript">
			//通过config接口注入权限验证配置
			wx.config({
			    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
			    appId: 'wx1baa85b75b6f2d60', // 必填，公众号的唯一标识
			    timestamp: '<?php echo time();?>', // 必填，生成签名的时间戳
			    nonceStr: '<?php echo $nonceStr;?>', // 必填，生成签名的随机串
			    signature: '<?php echo $signature;?>',// 必填，签名
			    jsApiList: [] // 必填，需要使用的JS接口列表
			});
			//通过ready接口处理成功验证
			wx.ready(function(){
				// config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后
			});
		</script>
	</head>
	<body style="background: #f5f5f5;" >
			<iframe src="http://m.amap.com/navi/?start=116.403124,39.940693&dest=116.481488,39.990464&destName=阜通西&naviBy=car&key=d061a6fc0a3f2f63781eb0c4db17dc47" width="100%" height="400px"></iframe>
		<!--<header class="mui-bar mui-bar-nav">
			<a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
			<h1 class="mui-title">教练列表</h1>
		</header>-->
<!--		<div class="mui-content" style="width: 100%; height: 100%;">-->
		<!--</div>-->
	</body>
</html>