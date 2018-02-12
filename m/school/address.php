<?php
	require_once '../include/config.php';
	$id = htmlspecialchars($_GET['id']);
	$lng = htmlspecialchars($_GET['lng']);
	$lat = htmlspecialchars($_GET['lat']);
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
		<link rel="stylesheet" href="../assets/css/swiper.min.css" />
		<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
		<style type="text/css">
			.mui-table-view-cell:after {left:0px;}
		</style>
		<script type="text/javascript">
            /*
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
            */
		</script>
	</head>
	<body style="background: #f5f5f5;">
		<header class="mui-bar mui-bar-nav">
			<a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
			<h1 class="mui-title" id="school_name">报名点</h1>
		</header>
			
		<div class="mui-content">
			<ul class="mui-table-view" id="address-list" style="margin-top:10px;">
	            <li class="mui-table-view-cell" style="color: #333333; height: 70px; ">
	            	<div class="mui-loading" style="">
						<div class="mui-spinner">
						</div>
						<p style="text-align: center;">正在加载中</p>
					</div>        		
            	</li>
	        </ul>
	        	
		</div>
		<script src="../assets/js/cookie.min.js"></script>
		<script src="../assets/js/mui.min.js"></script>
		<script src="../assets/js/template.js"></script>
		<script id="address-list-temp" type="text/html">
			{{each data as value i}}
			<li class="mui-table-view-cell" style="color: #333333; min-height: 70px; ">
            	<div class="mui-col-xs-9">
	            	<span>{{value.tl_train_address}}<span class="" style="color: #0062CC;">[{{value.distance}}km]</span></span>            		
            	</div>
            	<div class="mui-col-xs-11 mui-address" style="position:absolute; line-height: 50px;">
	            	<a href="tel:{{value.tl_phone}}"><span class="mui-pull-right iconfont" style="color:#0062CC;font-size:1.2rem;">&#xe612;</span></a>
	            	<span class="mui-pull-right iconfont" onclick="addresslocation({{value.tl_location_x}}, {{value.tl_location_y}});" class="address_location" style="color:#2AC845; font-size:1.5rem;">&#xe60b;</span>
            	</div>	            		
        	</li>
        	{{/each}}
		</script>
		<script>
			(function($, doc) {
				var id = "<?php echo $id; ?>";
				var lng = "<?php echo $lng; ?>";
				var lat = "<?php echo $lat; ?>";

				var params = {
					'id':id,
					'lng':lng,
					'lat':lat
				};
				$.ajax({
					type:"post",
					url:"<?php echo HOST; ?>/get_school_address_list.php",
					data:params,
					dataType:"json",
					timeout:10000,
					async:true,
					success:function(data) {
						if(data.code = 200) {
							var html = template('address-list-temp', data);
							doc.getElementById('address-list').innerHTML = html;
						}
					},
					error:function() {
						$.toast('网络错误，请检查网络');
					}
				});
			})(mui, document);
			//点击定位
			function addresslocation(lng, lat) {
				var longitude = Cookies.get('lng');
				var latitude = Cookies.get('lat');

				location.href="http://m.amap.com/navi/?start="+longitude+","+latitude+"&dest="+lng+","+lat+"&destName=合肥&naviBy=car&key=<?php echo GDKEY; ?>";
			}
		</script>
	</body>
</html>
