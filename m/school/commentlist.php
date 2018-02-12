<?php
	require_once '../include/config.php';
	$id = htmlspecialchars($_GET['id']);
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
			<h1 class="mui-title" id="school_name">评价列表</h1>
		</header>
		<div class="mui-content" id="comment_list" >
			<ul class="mui-table-view" style="margin-top:10px; height: 70px; line-height: 50px;">
            	<li class="mui-table-view-cell" style="color: #333333; height: 70px;">
            		<span id="comment_num">0</span>条评论 
            		<span id="address" style="color:#888;">
            			<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>	
            		</span>
            		4分
            	</li>
	        </ul>
	        
	        <ul class="mui-table-view" style="margin-top:10px;">
				<li class="" title="" style="padding-top:10px; border-bottom: 1px solid #eee;">
					<div class="mui-loading">
						<div class="mui-spinner">
						</div>
						<p style="text-align: center;">正在加载中</p>
					</div>
				</li>
				
			</ul>
			
		</div>
	</body>
	<script src="../assets/js/mui.min.js"></script>
	<script src="../assets/js/template.js"></script>
	<script id="comment-list-temp" type="text/html">
		<ul class="mui-table-view" style="margin-top:10px; height: 70px; line-height: 50px;">
        	<li class="mui-table-view-cell" style="color: #333333; height: 70px;">
        		<span id="comment_num">{{data.total_comment_num}}</span>条评论 
        		<span id="address" style="color:#888;">
        			{{if data.average_star_num == 5}}
        			<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>	
					{{else if data.average_star_num == 4}}
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					{{else if data.average_star_num == 3}}
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					{{else if data.average_star_num == 2}}
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					{{else if data.average_star_num == 1}}
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					{{else}}
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
					{{/if}}
        		</span>
        		{{data.average_star_num}}分
        	</li>
        </ul>
        {{if data.total_comment_num != 0}}
        <ul class="mui-table-view" style="margin-top:10px;">
        	{{each data.comment_list as value i}}
			<li class="" title="" style="padding-top:10px; border-bottom: 1px solid #eee; position: relative;">
				<img class="mui-pull-left" style="border-radius:50%;" width="80px" height="80px" src="../assets/images/default/{{value.photo_id}}.png">

				<div class="mui-media-body ">
					{{value.s_username}}
					<p class='mui-ellipsis'>
						{{if value.school_star == 5}}
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						{{else if value.school_star == 4}}
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						{{else if value.school_star == 3}}
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						{{else if value.school_star == 2}}
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						{{else if value.school_star == 1}}
						<i class="iconfont" style="color:#EC971F">&#xe600;</i>
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						{{else}}
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						<i class="iconfont" style="color:#EC971F">&#xe601;</i>
						{{/if}}
					</p>
					<p>
						{{if value.school_content}}
							{{value.school_content}}
						{{else}}
							暂无评价
						{{/if}}
					</p>
				</div>
				<div class="mui-media-body" style="position:absolute; top:10px; right:10px;">
					<p class="mui-pull-right" style="height: 100px; ">
						{{value.addtime}}
					</p>									
				</div>
			</li>
			{{/each}}
		</ul>
		{{else}}
			<p style="background: #f5f5f5;text-align: center;padding-top:40px;"><span class="iconfont" style="font-size:5rem; color: #ccc;">&#xe605;</span><br />暂无评价列表</p>
		{{/if}}
		<div class="clearfix"></div>
	</script>
	<script>
		(function($, doc){
			$.ajax({
				type:"post",
				url:"<?php echo HOST; ?>/get_school_comment_list.php",
				data:{id:<?php echo $id; ?>},
				dataType:"json",
				beforSend:function() {
					
				},
				async:true,
				success:function(data) {
					if(data.code == 200) {
						var html = template('comment-list-temp', data);
						doc.getElementById('comment_list').innerHTML = html;
					}
				},
				error:function() {
					$.toast('网络错误，请检查网络');
				}
			});
		})(mui, document);
	</script>
</html>
