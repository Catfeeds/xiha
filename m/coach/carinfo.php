<?php
	require_once '../include/config.php';
	$id = htmlspecialchars($_GET['id']);
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
	<body style="background: #f5f5f5;">
		<header class="mui-bar mui-bar-nav">
			<a class="mui-action-back mui-icon mui-icon-left iconfont" href="javascript:history.back(-1);" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
			<h1 class="mui-title">车辆详情</h1>
		</header>
		<div class="mui-content" id="carinfo">
			<div class="mui-loading" style="background: #f5f5f5; padding-top: 10px;">
				<div class="mui-spinner">
				</div>
				<p style="text-align: center;">正在加载中</p>
			</div>
		</div>
		<script type="text/html" id="carinfo_temp">
			<div class="mui-content">
		        <ul class="mui-table-view">	            
		            <li class="mui-table-view-cell" style="color: #333333;">车型 &nbsp;&nbsp; {{data.name}}</li>
		            <li class="mui-table-view-cell" style="color: #333333;">车牌号  &nbsp;&nbsp;{{data.car_no}}</li>
		            <li class="mui-table-view-cell" style="color: #333333;">车辆图片  </li>
				<div class="ucenterbg" style="height: 160px; width:100%; position:relative; text-align: center;">
					<img id="s_bg_thumb" id="stackblur" class="blur" src="{{data.imgurl[0]}}" width="320px" height="160px" alt="" />
				</div>
				<li class="mui-table-view-cell record-list" title="insure_list" style="color: #333333;">保险记录 <span class="mui-pull-right iconfont" style="color:#888; font-size:1.5rem;">&#xe60f;</span></li>
				<li class="mui-table-view-cell record-list" title="annual_list" style="color: #333333;">年检记录 <span class="mui-pull-right iconfont" style="color:#888; font-size:1.5rem;">&#xe60f;</span></li>
				<li class="mui-table-view-cell record-list" title="fuel_list" style="color: #333333;">加油记录 <span class="mui-pull-right iconfont" style="color:#888; font-size:1.5rem;">&#xe60f;</span></li>
	            </ul>
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
				var id = "<?php echo $id; ?>";
				$.ajax({
					type:"get",
					url:"<?php echo HOST; ?>/get_car_info.php/id/"+id,
					dataType:"json",
					async:false,
					timeout:10000,
					success:function(data) {
//						alert(data.code);
						if(data.code == 200) {
							var html = template('carinfo_temp', data);
							doc.getElementById('carinfo').innerHTML = html;
						}
						$('.mui-table-view').on('tap', '.record-list', function(e) {
							var title = this.getAttribute('title');
							location.href="record.php?type="+title+"&id=<?php echo $id; ?>";
						})
					},
					error:function() {
						$.toast('网络错误，请检查网络');
					}
				});
				
				
				
			})(mui, document);
		</script>
	</body>
</html>
