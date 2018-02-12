<?php
	require_once '../include/config.php';
	$sid = htmlspecialchars($_GET['id']);
	setcookie('sid', $sid);
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
	<body style="background: #f5f5f5;" >
		<header class="mui-bar mui-bar-nav">
			<!--<a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>-->
			<h1 class="mui-title"  id="coach-title">教练列表</h1>
		</header>
		<!--OK (appsercret:d9f6d541de1f95cfb1eb33f5c4bb27eb)-->
		<!-- <div class="mui-content" style="border:1px solid red;"> -->
		<div class="mui-content">
			<div id="slider" class="mui-slider">
				<div id="sliderSegmentedControl" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
					<a class="mui-control-item" href="#item1mobile">
							综合最优
						</a>
					<a class="mui-control-item" href="#item2mobile">
							评分最优
						</a>
					<a class="mui-control-item" href="#item3mobile">
							距离最近
						</a>
				</div>
				<div id="sliderProgressBar" class="mui-slider-progress-bar mui-col-xs-4"></div>
				<div class="mui-slider-group">
					<!--综合最优-->
					<div id="item1mobile" class="mui-slider-item mui-control-content mui-active">
						<div id="scroll1" class="mui-scroll-wrapper">
							<div class="mui-scroll" style="">
								<ul class="mui-table-view" id="default_coach_list" >	

								</ul>
							</div>

						</div>
					</div>
					<!--评分最优-->
					<div id="item2mobile" class="mui-slider-item mui-control-content">
						<div id="scroll2" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<ul class="mui-table-view" id="star_coach_list" >	
									<li class="mui-table-view-cell mui-media">
										<div class="mui-loading">
											<div class="mui-spinner">
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<!--距离最近-->
					<div id="item3mobile" class="mui-slider-item mui-control-content">
						<div id="scroll3" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<ul class="mui-table-view" id="distance_coach_list" >	
									<li class="mui-table-view-cell mui-media">
										<div class="mui-loading">
											<div class="mui-spinner">
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" value="" name="latitude" id="latitude"/>
			<input type="hidden" value="" name="longitude" id="longitude"/>
		</div>
		
		<script type="text/javascript" src="../assets/js/template.js"></script>
		
		<!--模板-->
		<script id="coachlist" type="text/html">
						
				{{if data.length > 0}}
				{{each data as value i}}
				<li class="mui-table-view-cell mui-media" title="{{value.l_coach_id}}">
					<!--<a href="coach_detail.php?id={{value.l_coach_id}}&sid=<?php echo $sid; ?>">-->
					<a href="javascript:;">
						<img class="mui-media-object mui-pull-left" style="border-radius:50%;" width="100px" height="100px" src="{{value.s_coach_imgurl}}">
						<div class="mui-media-body ">
							{{value.s_school_name}}-{{value.s_coach_name}}
							<p class='mui-ellipsis'>
								<!--{{value.i_coach_star}}-->
								{{if value.i_coach_star == 5}}
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
								{{else if value.i_coach_star == 4}}
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
								{{else if value.i_coach_star == 3}}
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
								{{else if value.i_coach_star == 2}}
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
								{{else if value.i_coach_star == 1}}
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
								{{value.s_coach_car_name}}普通型
							</p>
						</div>
						<div class="mui-media-body" style="position:absolute; top:10px; right:10px;">
							<p class="mui-pull-right" style="">
								<span style="color: #CF2D28;">人均{{value.total_price}}元</span>
								<br />
								<span class="iconfont" style ="color:#2AC845;">&#xe60b;</span>
								{{(value.coach_student_distance/1000).toFixed(2)}}km
							</p>										
						</div>
					</a>
				</li>
				{{/each}}
				<li onclick="getNextpage('{{data.orderstyle}}', '{{data.page}}', '{{data.id}}')" class="{{data.orderstyle}}" id="{{data.orderstyle}}" style="color: rgb(153, 153, 153); text-align: center; padding: 10px 0 ;">查看更多&gt;&gt;</li>
				{{else}}
				<p style="padding: 50px; text-align: center;">
                <br />
				<span class="iconfont" style="font-size: 5rem; color: #ddd;">&#xe605;</span>
                <br />
                <br />
               		 暂无教练列表  :(
                </p>
				{{/if}}
			<!--</ul>-->
		</script>

		<script>
			(function($, doc) {
				var longitude = Cookies.get('lng');
				var latitude = Cookies.get('lat');
				var params = {
					'lng':longitude,
					'lat':latitude,
					'lisence_id':1,
					'lesson_id':2,
					'sid': "<?php echo $sid;?>",
					'page':1,
					'type':'default' 
				};
				//获取综合最优
				$.ajax({
					type:"post",
					url:"<?php echo HOST; ?>/get_coach_list.php",
					data:params,
					dataType:"json",
					async:true,
					timeout:10000,
					success:function(data) {
						if(data.code == 200) {
							data.data.orderstyle = params.type;
							data.data.page = 'dpage';
							data.data.id = 'default_coach_list';
							var html = template('coachlist', data);
//							html += '<li class="'+data.data.orderstyle+'" style="text-align: center; color: #999;" id="'+data.data.orderstyle+'">查看更多&gt;&gt;</li>';
							doc.getElementById('default_coach_list').innerHTML = html;
						}
						Cookies.set('dpage', 1);
					},
					error:function() {
						$.toast('网络错误，请检查网络');
					}
                    /*
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        document.write(XMLHttpRequest.readyState + XMLHttpRequest.status + XMLHttpRequest.responseText);
                    }
                    */
				});

				$('.mui-scroll-wrapper').scroll({
					indicators: true //是否显示滚动条
				});
				var item2 = document.getElementById('item2mobile');
				var item3 = document.getElementById('item3mobile');
				
				//转动ajax获取教练列表
				document.getElementById('slider').addEventListener('slide', function(e) {
					//评分最优
					if (e.detail.slideNumber === 1) {
						if (item2.querySelector('.mui-loading')) {
							
							params.type = 'star';
							$.ajax({
								type:"post",
								url:"<?php echo HOST; ?>/get_coach_list.php",
								data:params,
								dataType:"json",
								async:true,
								timeout:10000,
								success:function(data) {
									if(data.code == 200) {
										data.data.orderstyle = params.type;
										data.data.page = 'spage';
										data.data.id = 'star_coach_list';
										var html = template('coachlist', data);
										document.getElementById('star_coach_list').innerHTML = html;
									}
									Cookies.set('spage', 1);
								},
								error:function() {
									$.toast('网络错误，请检查网络');
								}
							});
						}
						
					//距离最近
					} else if (e.detail.slideNumber === 2) {
						if (item3.querySelector('.mui-loading')) {
//							getLocation();
							params.type = 'distance';
							$.ajax({
								type:"post",
								url:"<?php echo HOST; ?>/get_coach_list.php",
								data:params,
								dataType:"json",
								async:true,
								timeout:10000,
								success:function(data) {
									if(data.code == 200) {
										data.data.orderstyle = params.type;
										data.data.page = 'lpage';
										data.data.id = 'distance_coach_list';
										var html = template('coachlist', data);
										document.getElementById('distance_coach_list').innerHTML = html;
									}
									Cookies.set('lpage', 1);
								},
								error:function() {
									$.toast('网络错误，请检查网络');
								}
							});
						}
					}
				});
				var sliderSegmentedControl = document.getElementById('sliderSegmentedControl');
				$('.mui-input-group').on('change', 'input', function() {
					if (this.checked) {
						sliderSegmentedControl.className = 'mui-slider-indicator mui-segmented-control mui-segmented-control-inverted mui-segmented-control-' + this.value;
						//force repaint
						sliderProgressBar.setAttribute('style', sliderProgressBar.getAttribute('style'));
					}
				});
				
				//点击事件
				$(".mui-table-view").on('tap','.mui-table-view-cell',function(){
				  	//获取id
				 	var title = this.getAttribute("title");
					location.href = "detail.php?id="+title+"&sid=<?php echo $sid; ?>";
				});
				
				
			})(mui, document);

			function getNextpage(t,p,id) {
				var params = {
					'lng':Cookies.get('lng'),
					'lat':Cookies.get('lat'),
					'lisence_id':1,
					'lesson_id':2,
					'sid': "<?php echo $sid;?>",
					'page':parseInt(Cookies.get(p)) + 1,
					'type':t 
				};
				mui.ajax({
					type:"post",
					url:"<?php echo HOST; ?>/get_coach_list.php",
					data:params,
					dataType:"json",
					beforeSend:function() {
						document.getElementById(params.type).innerHTML = '<div class="mui-loading" style="margin-top:0px;"><div class="mui-spinner"></div></div>';
					},
					async:true,
					timeout:10000,
					success:function(data) {
						if(data.code == 200) {
							document.getElementById(params.type).parentNode.removeChild(document.getElementById(params.type));
							data.data.orderstyle = params.type;
							mui.each(data.data, function(e, t) {											
								var content = '';
								var li = document.createElement('li');
								li.className = 'mui-table-view-cell';
								li.setAttribute('title', t.l_coach_id);
								content += '<a href="javascript:;"><img class="mui-media-object mui-pull-left" style="border-radius:50%;" width="100px" height="100px" src="'+t.s_coach_imgurl+'"><div class="mui-media-body ">'+t.s_school_name+'-'+t.s_coach_name+'<p class="mui-ellipsis">';
				
								if(t.i_coach_star <= 5) {
									for(var i = 0; i < t.i_coach_star; i++) {
									 content += '<i class="iconfont" style="color:#EC971F">&#xe600;</i>';
									}
								} else {
									for(var i = 0; i < 5-t.i_coach_star; i++) {
									 content += '<i class="iconfont" style="color:#EC971F">&#xe601;</i>';
									}
								}
								content += '</p><p>'+t.s_coach_car_name+'普通型</p></div><div class="mui-media-body" style="position:absolute; top:10px; right:10px;"><p class="mui-pull-right" style=""><span style="color: #CF2D28;">人均'+t.total_price+'元</span><br /><span class="iconfont" style ="color:#2AC845;">&#xe60b;</span>'+(t.coach_student_distance/1000).toFixed(2)+'km</p></div></a>';
								li.innerHTML = content;
								document.getElementById(id).appendChild(li);
							});
							var more = document.createElement('li');
							more.className = data.data.orderstyle;
							more.setAttribute('id', data.data.orderstyle);
							more.style.color = '#999';
							more.style.textAlign = 'center';
							more.style.paddingTop = '10px';
							more.style.paddingBottom = '10px';
							if ( data.data.length > 0 ) {	
								more.innerHTML = '查看更多&gt;&gt;';
								more.setAttribute('onclick', 'javascript:getNextpage(\''+t+'\', \''+p+'\', \''+id+'\')');
							} else {
								more.innerHTML = '没有更多了';
							}
//							console.log(more)
							document.getElementById(id).appendChild(more);
						}
					},
					error:function() {
						document.getElementById(params.type).innerHTML = '查看更多&gt;&gt;';
						mui.toast('网络错误，请检查网络');
					}
				});
				Cookies.set(p, parseInt(Cookies.get(p)) + 1);
			}

			
		</script>
	</body>
</html>
