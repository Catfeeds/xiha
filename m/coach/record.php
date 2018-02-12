<?php
	require_once '../include/config.php';
	$type = htmlspecialchars($_GET['type']);
	$id = htmlspecialchars($_GET['id']);
	if($type == "insure_list") {
		$title = "保险记录";
	} elseif($type == "fuel_list") {
		$title = "加油记录";
	} else {
		$title = "年检记录";
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<title></title>
		
		<link rel="stylesheet" href="../assets/css/mui.min.css" />
		<link rel="stylesheet" href="../assets/css/style.css" />
		<link rel="stylesheet" href="../assets/font/iconfont/iconfont.css" />
		<style type="text/css">
			.mui-table-view-cell:after {left:0px;}
			.mui-checkbox input[type=checkbox]:checked:before {content:'xe602'; }
			.mui-checkbox input[type=checkbox]:before {content: '';}
		</style>
		<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
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
	<body>
		<header class="mui-bar mui-bar-nav">
			<a class="mui-action-back mui-icon mui-icon-left iconfont" href="javascript:history.back(-1);" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
			<h1 class="mui-title"><?php echo $title; ?></h1>
		</header>
		<div class="mui-content" id="record">
			<div class="mui-loading" style="background: #f5f5f5; padding-top: 10px;">
				<div class="mui-spinner">
				</div>
				<p style="text-align: center;">正在加载中</p>
			</div>
		</div>
		<script type="text/html" id="record_temp">
			<div class="mui-content">
				{{if data}}
				{{each data as value i}}
		        	{{value}}
		        {{/each}}
		        {{else}}
		        <p style="text-align: center;padding:200px 20px;">
	        		暂无<?php echo $title; ?>记录
	        	</p>		
		        {{/if}}
			</div>
		</script>
		<script src="../assets/js/mui.min.js"></script>
		<script src="../assets/js/template.js"></script>
		<script src="../assets/js/cookie.min.js"></script>
		<script>
			mui.init({
				swipeBack: false
			});
	
			(function($, doc) {
				$.ajax({
					type:"get",
					url:"<?php echo HOST; ?>/get_car_info.php/id/<?php echo $id; ?>",
					dataType:"json",
					async:false,
					timeout:10000,
					success:function(data) {
//						alert(data.code);
						if(data.code == 200) {
							var type = "<?php echo $title; ?>";
							var list = '';
							if(type == 'fuel_list') {
								list = data.data.car_info.fuel_list;
							} else if(type == 'insure_list') {
								list = data.data.car_info.insure_list;
							} else {
								list = data.data.car_info.annual_list;
							}
							var html = template('record_temp', list);
							doc.getElementById('record').innerHTML = html;
						}
					},
					error:function() {
						$.toast('网络错误，请检查网络');
					}
				});
				
				
				
			})(mui, document);
		</script>
	</body>
</html>
