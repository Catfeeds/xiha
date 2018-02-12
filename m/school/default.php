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
		<script src="../assets/js/cookie.min.js"></script>
		<script src="../assets/js/layer/layer.js"></script>
		<style type="text/css">
			.mui-table-view-cell:after {left:0px;}
		</style>
		<script type="text/javascript">
			//通过config接口注入权限验证配置
            /*
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
			})i;
            */
		</script>
	</head>
	<body style="background: #f5f5f5;">
		<!--OK (appsercret:d9f6d541de1f95cfb1eb33f5c4bb27eb)-->
		<header class="mui-bar mui-bar-nav">
			<!--<a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>-->
			<h1 class="mui-title" id="school_name">驾校</h1>
		</header>
		<div class="mui-content" id="school-detail">
			<div class="mui-loading" style="background: #f5f5f5; padding-top: 10px;">
				<div class="mui-spinner">
				</div>
				<p style="text-align: center;">正在加载中</p>
			</div>
		</div>

		<div id="showshifts" class="mui-popover mui-popover-action mui-popover-bottom" style="height: 500px; width:100%; overflow-y: scroll;">
			<span class="iconfont" onclick="layer.closeAll()" id="shifts-hide" style="position: absolute; right: 10px; top: 10px; color:#666; font-size: 2rem;">&#xe614;</span>
			<div id="shifts_content" style="background: #fff;">
				<!--班制简介-->	
			</div>
		</div>
		<script src="../assets/js/mui.min.js"></script>
		<script src="../assets/js/swiper.min.js"></script>
		<script src="../assets/js/template.js"></script>
		
		<script id="school_detail_temp" type="text/html">
			<div class="container">
		        <div class="swiper-container" >
		          <div class="swiper-wrapper" style="height: 200px; width:100%;">
		          	
		          	{{if data.s_imgurl.length > 0}}
	          			{{each data.s_imgurl as value i}}
		              	<div class="swiper-slide" style="background: #f5f5f5; width:100%; height: 200px;"><img src="{{value}}" height="200px" width="100%" alt="" /></div>
		              	{{/each}}
	              	{{/if}}
		          </div>
		          <div class="swiper-pagination"></div>
		        </div>
	      	</div>
			
			<ul class="mui-table-view" style="margin-top:10px; height: 70px;">
	            <li class="mui-table-view-cell" style="color: #333333; height: 70px; ">
	            	<div class="mui-col-xs-7">
		            	<span>{{data.tl_train_address}}<span class="" style="color: #0062CC;">[{{data.min_distance}}km]</span></span>            		
	            	</div>
	            	<div class="mui-col-xs-12 mui-address" style="line-height: 50px;">
		            	<span class="mui-pull-right" id="more_address" style="color:#0062CC; margin-right: 20px;">更多</span>
		            	<a href="tel:{{data.tl_phone}}"><span class="mui-pull-right iconfont" style="color:#0062CC;font-size:1.2rem; margin-right: 10px;">&#xe612;</span></a>
		            	<span class="mui-pull-right iconfont" lng="{{data.tl_location_x}}" lat="{{data.tl_location_y}}" id="address_location" style="color:#2AC845; font-size:1.5rem; margin-right: 10px;">&#xe60b;</span>
	            	</div>	            		
            	</li>
	        </ul>
	        <ul class="mui-table-view mui-ul-click" style="margin-top:10px; height: 70px; line-height: 50px;">
            	<li class="mui-table-view-cell" id="mui-li-click" title="{{data.l_school_id}}" style="color: #333333; height: 70px;">
            		{{data.total_comment_num}}条评论 
            		<span id="address" style="color:#888;">
            			{{if data.average_star_num == 1}}
	            			<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>	
						{{else if data.average_star_num == 2}}
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>	
						{{else if data.average_star_num == 3}}
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
						{{else if data.average_star_num == 4}}
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
						{{else if data.average_star_num == 5}}
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
						{{else}}
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
							<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
						{{/if}}
            		</span>
            		{{data.average_star_num}}分
            		<span class="mui-pull-right iconfont" style="color:#888; font-size:1.5rem;">&#xe60f;</span>
        		</li>
	        </ul>
        	<p style="margin:10px;">
        		班制 
        		<span id="shifts_tips" class="mui-pull-right iconfont" style="color:#0062CC; font-size: 1.4rem;">&#xe613;</span>
    		</p>
			
	        <ul class="mui-table-view shifts-list" style="margin-top:10px; line-height: 50px;">
	        	{{if data.shifts_list.length > 0}}
		        	{{each data.shifts_list as value i}}
	            	<li class="mui-table-view-cell" style="color: #333333;">
		            	<div class="" style="height: 20px;">
			            	<p class="mui-pull-left" style="line-height:30px; width: 65%; color: #555;font-weight: bold;">{{value.sh_title}}</p> 
			            	<p class="mui-pull-right" style="line-height:30px; color:#8B211E;">{{value.sh_money}}元</p>
		            	</div>
		            	<div class="clearfix"></div>          		
		            	<div class="" style="height: 20px;">
			            	<p class="mui-pull-left" style="line-height:30px; width: 35%;">{{value.sh_description_1}}</p>           		
			            	<p class="mui-pull-right" style="line-height:30px;">{{value.sh_description_2}}</p>
		            	</div>
	            		<div class="clearfix"></div> 		
	            		<div class="mui-pull-right"style="line-height:30px;">
		            		<button type="button" title="{{value.sh_money}}" id="{{value.id}}" class="mui-btn mui-btn-danger order-button">
								立即报名
							</button>
	            		</div>
	            	</li>
	            	{{/each}}
            	{{else}}
            		暂无班制信息
            	{{/if}}

	        </ul>
        	<p style="margin-top:10px; margin-left:10px;">驾校简介</p> 
       	 	<ul class="mui-table-view" >
	       	 	<li class="mui-table-view-cell" style="height:250px; overflow-y:scroll;">
	       	 		<p>
	       	 			{{if data.s_school_intro}}
	       	 				{{data.s_school_intro}}
	       	 			{{else}}
	       	 				暂无驾校简介
	       	 			{{/if}}
       	 			</p>
	       	 	</li>
       	 	</ul>
	        
		</script>
		<script type="text/javascript" charset="utf-8">
			
			mui.init({
				swipeBack:true //启用右滑关闭功能
			});
         	
         	(function($, doc){
				var loginauth = localStorage.getItem('loginauth');
//				localStorage.setItem('loginauth', '{}');
				if(loginauth != '{}' || loginauth) {
					var loginauth_json = JSON.parse(loginauth);
					var uid = loginauth_json.l_user_id;
				} else {
					localStorage.setItem('loginauth', '{}');
				}

         		//ajax获取驾校详情
         		var longitude = Cookies.get('lng');
				var latitude = Cookies.get('lat');
				
         		var config = {
         			'id':"<?php echo $id; ?>",
         			'lng':longitude,
         			'lat':latitude,
         			'uid':1
         		};

         		$.ajax({
         			type:"post",
         			url:"<?php echo HOST; ?>/get_school_detail.php",
         			data:config,
         			dataType:"json",
         			timeout:10000,
         			async:true,
         			success:function(data) {
//       				alert(data.code);
         				if(data.code == 200) {
         					doc.getElementById('school_name').innerHTML = data.data.s_school_name;
//       					doc.write(data.data.s_imgurl[0])
							data.data.s_school_intro = eval('"'+data.data.s_school_intro+'"');

	         				var html = template('school_detail_temp', data);
							doc.getElementById('school-detail').innerHTML = html;
							doc.getElementById('shifts_content').innerHTML = data.data.s_shifts_intro;
         				} else {
							$.toast(data.data);
							return false;
						}

						// 轮播图
			         	var mySwiper = new Swiper('.swiper-container',{
				           loop: true,
				           autoplay: 3000,
				           pagination: '.swiper-pagination',
			         	});

	         			$(".mui-ul-click").on('tap','.mui-table-view-cell',function(){
						  	//获取id
						 	var title = this.getAttribute("title");
						 	if(title != null) {
								location.href = "commentlist.php?id="+title;
						 	}
						});
						//点击坐标
						var address_location = doc.getElementById('address_location');
						address_location.addEventListener('tap', function(e) {
							var lng = this.getAttribute('lng');
							var lat = this.getAttribute('lat');
							location.href="http://m.amap.com/navi/?start="+longitude+","+latitude+"&dest="+lng+","+lat+"&destName=合肥&naviBy=car&key=<?php echo GDKEY; ?>";
//							location.href="location.php?l="+lnglat;
						});
						//点击更多
						var more_address = doc.getElementById('more_address');
						more_address.addEventListener('tap', function(e) {
							location.href="address.php?id=1&lng="+longitude+"&lat="+latitude;
						});
						
						//点击报名
						$('.shifts-list').on('tap', '.order-button', function(e) {
							var money = this.getAttribute('title');
							var shifts_id = this.getAttribute('id');
//							alert(loginauth);return false;
//							if(loginauth != '' || loginauth !== null) {
							if(loginauth != '{}') {
								location.href="apply.php?id="+shifts_id+"&sid=<?php echo $id; ?>&uid="+uid+"&m="+money;
							} else {
								location.href="login.php?id=<?php echo $id; ?>";
							}
								
						});
						
						//班制简介
		         		var shifts_click = doc.getElementById('shifts_tips');
						shifts_click.addEventListener('tap', function(e) {
							var html = doc.getElementById('showshifts').innerHTML;
		         			var pageii = layer.open({
							    type: 1,
							    content: html,
							    style: 'position:fixed; left:0; top:0; padding:20px; width:100%; height:100%; border:none; overflow-y:scroll'
							});	
		         		});
		         		
         			},
         			error:function() {
         				$.toast('网络错误,请检查网络');
         			}
         		});
                
         	})(mui, document);

		</script>
	</body>
</html>
