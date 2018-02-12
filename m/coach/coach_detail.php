<?php
	require_once '../include/config.php';
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
	<body style="background: #f5f5f5;">
		<header class="mui-bar mui-bar-nav">
			<a class="mui-action-back mui-icon mui-icon-left iconfont" href="javascript:history.back(-1);" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
			<h1 class="mui-title">预约详情</h1>
		</header>
		<div class="mui-content" id="coach_detail">
			<!--<div class="mui-loading" style="background: #f5f5f5; padding-top: 10px;">
				<div class="mui-spinner">
				</div>
				<p style="text-align: center;">正在加载中</p>
			</div>-->
			<ul class="mui-table-view" id="coach_detail" style="margin-top:0px;">
				<li class="mui-table-view-cell mui-media" title="">
					<a href="javascript:;">
						<img class="mui-media-object mui-pull-left" style="border-radius:50%;" width="100px" height="100px" src="{{data.s_coach_imgurl}}">
						<div class="mui-media-body ">
							daichi （金牌教练）
							<p class='mui-ellipsis'>
								
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
				
							</p>
							<p>
								<span class="">教龄</span> <span class="">12年</span>
								<span class="">已带学员</span> <span class="">1人</span>
							</p>
						</div>
						<div class="mui-media-body" style="position:absolute; top:10px; right:10px;">
							<p class="mui-pull-right" style="height: 100px; padding-top:23px">
								<span class="iconfont" style="font-size:3.5rem; color: #00BD9C;">&#xe60a;</span>
							</p>
						</div>
					</a>
				</li>
			</ul>
			
			<ul class="mui-table-view" style="margin-top:10px;">
	            <li class="mui-table-view-cell" id="addresslocation" lng="{{data.dc_coach_distance_x}}" lat="{{data.dc_coach_distance_y}}" style="color: #333333;">地址 <span id="address" style="color:#888">{{data.s_coach_address}}</span><span class="mui-pull-right iconfont" style="color:#2AC845; font-size:1.5rem;">&#xe60d;</span></li>
	            <li class="mui-table-view-cell" id="carinfo" title="26" style="color: #333333;">车辆 <span id="address" style="color:#888">pusang普通型 sadfaasdasdsdasds</span><span class="mui-pull-right iconfont" style="color:#888; font-size:1.5rem;">&#xe60f;</span></li>
            </ul>
			
            <div id="slider" class="mui-slider" style="margin-top:10px;">
				<div id="sliderSegmentedControl" style="background: #fff;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">

						<a class="mui-control-item" href="#item1mobile">
							3-5
						</a>
						<a class="mui-control-item" href="#item2mobile">
							3-5
						</a>
						<a class="mui-control-item" href="#item3mobile">
							3-5
						</a>
						<a class="mui-control-item" href="#item4mobile">
							3-5
						</a>
						<a class="mui-control-item" href="#item5mobile">
							3-5
						</a>
						<a class="mui-control-item" href="#item6mobile">
							3-5
						</a>
						<a class="mui-control-item" href="#item7mobile">
							3-5
						</a>
				</div>
				<div id="sliderProgressBar" class="mui-slider-progress-bar mui-col-xs-4" style="width:14.3%"></div>
				<div class="mui-slider-group" style="margin-top:0px;">
					<!--1-->
					<div id="item1mobile" class="mui-slider-item mui-control-content mui-active">
						<div id="scroll1" class="mui-scroll-wrapper">
							<div class="mui-scroll">

								<!--时间段-->
								<div style="background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
									<a class="mui-control-item" href="javascript:;">
										时间段
									</a>
									<a class="mui-control-item" href="javascript:;">
										牌照
									</a>
									<a class="mui-control-item" href="javascript:;">
										科目
									</a>
									<a class="mui-control-item" href="javascript:;">
										价格
									</a>
									<a class="mui-control-item" href="javascript:;">
										选择
									</a>
								</div>
								
								<div id="date_time_1" style="overflow-y:scroll !important; height: 300px; width: 100%;">
									<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
										<a class="mui-control-item" style="" href="javascript:;">
											9:00-10:00
										</a>
										<a class="mui-control-item" href="javascript:;">
											C1
										</a>
										<a class="mui-control-item" href="javascript:;">
											科目一
										</a>
										<a class="mui-control-item" href="javascript:;">
											￥130
										</a>
										<a class="mui-control-item" href="javascript:;">
											
											<span class="iconfont" style="color:#888">&#xe603;</span>
											
											<!--<span class="iconfont" style="color:#245269">&#xe602;</span>-->
										</a>
									</div>
									<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
										<a class="mui-control-item" style="" href="javascript:;">
											9:00-10:00
										</a>
										<a class="mui-control-item" href="javascript:;">
											C1
										</a>
										<a class="mui-control-item" href="javascript:;">
											科目一
										</a>
										<a class="mui-control-item" href="javascript:;">
											￥130
										</a>
										<a class="mui-control-item" href="javascript:;">
											
											<span class="iconfont" style="color:#888">&#xe603;</span>
											
											<!--<span class="iconfont" style="color:#245269">&#xe602;</span>-->
										</a>
									</div>
									<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
										<a class="mui-control-item" style="" href="javascript:;">
											9:00-10:00
										</a>
										<a class="mui-control-item" href="javascript:;">
											C1
										</a>
										<a class="mui-control-item" href="javascript:;">
											科目一
										</a>
										<a class="mui-control-item" href="javascript:;">
											￥130
										</a>
										<a class="mui-control-item" href="javascript:;">
											
											<span class="iconfont" style="color:#888">&#xe603;</span>
											
											<!--<span class="iconfont" style="color:#245269">&#xe602;</span>-->
										</a>
									</div>
									<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
										<a class="mui-control-item" style="" href="javascript:;">
											9:00-10:00
										</a>
										<a class="mui-control-item" href="javascript:;">
											C1
										</a>
										<a class="mui-control-item" href="javascript:;">
											科目一
										</a>
										<a class="mui-control-item" href="javascript:;">
											￥130
										</a>
										<a class="mui-control-item" href="javascript:;">
											
											<span class="iconfont" style="color:#888">&#xe603;</span>
											
											<!--<span class="iconfont" style="color:#245269">&#xe602;</span>-->
										</a>
									</div>
									<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
										<a class="mui-control-item" style="" href="javascript:;">
											9:00-10:00
										</a>
										<a class="mui-control-item" href="javascript:;">
											C1
										</a>
										<a class="mui-control-item" href="javascript:;">
											科目一
										</a>
										<a class="mui-control-item" href="javascript:;">
											￥130
										</a>
										<a class="mui-control-item" href="javascript:;">
											
											<span class="iconfont" style="color:#888">&#xe603;</span>
											
											<!--<span class="iconfont" style="color:#245269">&#xe602;</span>-->
										</a>
									</div>
									<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
										<a class="mui-control-item" style="" href="javascript:;">
											9:00-10:00
										</a>
										<a class="mui-control-item" href="javascript:;">
											C1
										</a>
										<a class="mui-control-item" href="javascript:;">
											科目一
										</a>
										<a class="mui-control-item" href="javascript:;">
											￥130
										</a>
										<a class="mui-control-item" href="javascript:;">
											
											<span class="iconfont" style="color:#888">&#xe603;</span>
											
											<!--<span class="iconfont" style="color:#245269">&#xe602;</span>-->
										</a>
									</div>
									<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
										<a class="mui-control-item" style="" href="javascript:;">
											9:00-10:00
										</a>
										<a class="mui-control-item" href="javascript:;">
											C1
										</a>
										<a class="mui-control-item" href="javascript:;">
											科目一
										</a>
										<a class="mui-control-item" href="javascript:;">
											￥130
										</a>
										<a class="mui-control-item" href="javascript:;">
											
											<span class="iconfont" style="color:#888">&#xe603;</span>
											
											<!--<span class="iconfont" style="color:#245269">&#xe602;</span>-->
										</a>
									</div>
									<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
										<a class="mui-control-item" style="" href="javascript:;">
											9:00-10:00
										</a>
										<a class="mui-control-item" href="javascript:;">
											C1
										</a>
										<a class="mui-control-item" href="javascript:;">
											科目一
										</a>
										<a class="mui-control-item" href="javascript:;">
											￥130
										</a>
										<a class="mui-control-item" href="javascript:;">
											
											<span class="iconfont" style="color:#888">&#xe603;</span>
											
											<!--<span class="iconfont" style="color:#245269">&#xe602;</span>-->
										</a>
									</div>
									<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
										<a class="mui-control-item" style="" href="javascript:;">
											9:00-10:00
										</a>
										<a class="mui-control-item" href="javascript:;">
											C1
										</a>
										<a class="mui-control-item" href="javascript:;">
											科目一
										</a>
										<a class="mui-control-item" href="javascript:;">
											￥130
										</a>
										<a class="mui-control-item" href="javascript:;">
											
											<span class="iconfont" style="color:#888">&#xe603;</span>
											
											<!--<span class="iconfont" style="color:#245269">&#xe602;</span>-->
										</a>
									</div>
									<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
										<a class="mui-control-item" style="" href="javascript:;">
											9:00-10:00
										</a>
										<a class="mui-control-item" href="javascript:;">
											C1
										</a>
										<a class="mui-control-item" href="javascript:;">
											科目一
										</a>
										<a class="mui-control-item" href="javascript:;">
											￥130
										</a>
										<a class="mui-control-item" href="javascript:;">
											
											<span class="iconfont" style="color:#888">&#xe603;</span>
											
											<!--<span class="iconfont" style="color:#245269">&#xe602;</span>-->
										</a>
									</div>
									<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
										<a class="mui-control-item" style="" href="javascript:;">
											9:00-10:00
										</a>
										<a class="mui-control-item" href="javascript:;">
											C1
										</a>
										<a class="mui-control-item" href="javascript:;">
											科目一
										</a>
										<a class="mui-control-item" href="javascript:;">
											￥130
										</a>
										<a class="mui-control-item" href="javascript:;">
											
											<span class="iconfont" style="color:#888">&#xe603;</span>
											
											<!--<span class="iconfont" style="color:#245269">&#xe602;</span>-->
										</a>
									</div>
									<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
										<a class="mui-control-item" style="" href="javascript:;">
											9:00-10:00
										</a>
										<a class="mui-control-item" href="javascript:;">
											C1
										</a>
										<a class="mui-control-item" href="javascript:;">
											科目一
										</a>
										<a class="mui-control-item" href="javascript:;">
											￥130
										</a>
										<a class="mui-control-item" href="javascript:;">
											
											<span class="iconfont" style="color:#888">&#xe603;</span>
											
											<!--<span class="iconfont" style="color:#245269">&#xe602;</span>-->
										</a>
									</div>
									
								</div>
								
							</div>

						</div>
					</div>
					<!--2-->
					<div id="item2mobile" class="mui-slider-item mui-control-content">
						<div id="scroll2" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<!--时间段-->
								<div style="background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
									<a class="mui-control-item" href="javascript:;">
										时间段
									</a>
									<a class="mui-control-item" href="javascript:;">
										牌照
									</a>
									<a class="mui-control-item" href="javascript:;">
										科目
									</a>
									<a class="mui-control-item" href="javascript:;">
										价格
									</a>
									<a class="mui-control-item" href="javascript:;">
										选择
									</a>
								</div>
								
								<div id="date_time_2">
									<div class="mui-loading">
										<div class="mui-spinner">
										</div>
									</div>
								</div>
									
							</div>
						</div>
					</div>
					<!--3-->
					<div id="item3mobile" class="mui-slider-item mui-control-content">
						<div id="scroll3" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<!--时间段-->
								<div style="background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
									<a class="mui-control-item" href="javascript:;">
										时间段
									</a>
									<a class="mui-control-item" href="javascript:;">
										牌照
									</a>
									<a class="mui-control-item" href="javascript:;">
										科目
									</a>
									<a class="mui-control-item" href="javascript:;">
										价格
									</a>
									<a class="mui-control-item" href="javascript:;">
										选择
									</a>
								</div>
								
								<div id="date_time_3">
									<div class="mui-loading">
										<div class="mui-spinner">
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					
					<!--4-->
					<div id="item4mobile" class="mui-slider-item mui-control-content">
						<div id="scroll3" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<!--时间段-->
								<div style="background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
									<a class="mui-control-item" href="javascript:;">
										时间段
									</a>
									<a class="mui-control-item" href="javascript:;">
										牌照
									</a>
									<a class="mui-control-item" href="javascript:;">
										科目
									</a>
									<a class="mui-control-item" href="javascript:;">
										价格
									</a>
									<a class="mui-control-item" href="javascript:;">
										选择
									</a>
								</div>
								
								<div id="date_time_4">
									<div class="mui-loading">
										<div class="mui-spinner">
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!--5-->
					<div id="item5mobile" class="mui-slider-item mui-control-content">
						<div id="scroll3" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<!--时间段-->
								<div style="background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
									<a class="mui-control-item" href="javascript:;">
										时间段
									</a>
									<a class="mui-control-item" href="javascript:;">
										牌照
									</a>
									<a class="mui-control-item" href="javascript:;">
										科目
									</a>
									<a class="mui-control-item" href="javascript:;">
										价格
									</a>
									<a class="mui-control-item" href="javascript:;">
										选择
									</a>
								</div>
								
								<div id="date_time_5">
									<div class="mui-loading">
										<div class="mui-spinner">
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!--6-->
					<div id="item6mobile" class="mui-slider-item mui-control-content">
						<div id="scroll3" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<!--时间段-->
								<div style="background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
									<a class="mui-control-item" href="javascript:;">
										时间段
									</a>
									<a class="mui-control-item" href="javascript:;">
										牌照
									</a>
									<a class="mui-control-item" href="javascript:;">
										科目
									</a>
									<a class="mui-control-item" href="javascript:;">
										价格
									</a>
									<a class="mui-control-item" href="javascript:;">
										选择
									</a>
								</div>
								
								<div id="date_time_6">
									<div class="mui-loading">
										<div class="mui-spinner">
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!--7-->
					<div id="item7mobile" class="mui-slider-item mui-control-content">
						<div id="scroll3" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<!--时间段-->
								<div style="background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
									<a class="mui-control-item" href="javascript:;">
										时间段
									</a>
									<a class="mui-control-item" href="javascript:;">
										牌照
									</a>
									<a class="mui-control-item" href="javascript:;">
										科目
									</a>
									<a class="mui-control-item" href="javascript:;">
										价格
									</a>
									<a class="mui-control-item" href="javascript:;">
										选择
									</a>
								</div>
								
								<div id="date_time_7">
									<div class="mui-loading">
										<div class="mui-spinner">
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		
		<script src="../assets/js/cookie.min.js"></script>
		<script src="../assets/js/mui.min.js"></script>
		<script src="../assets/js/template.js"></script>

		<script type="text/html" id="coach_detail_temp">
			<ul class="mui-table-view" id="coach_detail" style="margin-top:0px;">
				<li class="mui-table-view-cell mui-media" title="">
					<a href="javascript:;">
						<img class="mui-media-object mui-pull-left" style="border-radius:50%;" width="100px" height="100px" src="{{data.s_coach_imgurl}}">
						<div class="mui-media-body ">
							{{data.s_coach_name}}（{{if data.i_type == 0}}金牌教练{{else}}普通教练{{/if}}）
							<p class='mui-ellipsis'>
								{{if data.i_coach_star == 5}}
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
								{{else if data.i_coach_star == 4}}
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
								{{else if data.i_coach_star == 3}}
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
								{{else if data.i_coach_star == 2}}
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
									<i class="iconfont" style="color:#EC971F">&#xe601;</i>
								{{else if data.i_coach_star == 1}}
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
								<span class="">教龄</span> <span class="">{{data.s_teach_age}}年</span>
								<span class="">已带学员</span> <span class="">{{data.students_num}}人</span>
							</p>
						</div>
						<div class="mui-media-body" style="position:absolute; top:10px; right:10px;">
							<p class="mui-pull-right" style="height: 100px; padding-top:23px">
								<span class="iconfont" style="font-size:3.5rem; color: #00BD9C;">&#xe60a;</span>
							</p>									
						</div>
					</a>
				</li>
			</ul>
			
			<ul class="mui-table-view" style="margin-top:10px;">
	            <li class="mui-table-view-cell" id="addresslocation" lng="{{data.dc_coach_distance_x}}" lat="{{data.dc_coach_distance_y}}" style="color: #333333;">地址 <span id="address" style="color:#888">{{data.s_coach_address}}</span><span class="mui-pull-right iconfont" style="color:#2AC845; font-size:1.5rem;">&#xe60d;</span></li>
	            <li class="mui-table-view-cell" id="carinfo" title="{{data.l_coach_id}}" style="color: #333333;">车辆 <span id="address" style="color:#888">{{data.car_info[0].name}}{{ if data.car_info[0].car_type == 1}}普通型{{else if data.car_info[0].car_type == 2}}加强型{{else}}模拟型{{/if}} {{data.car_info[0].car_no}}</span><span class="mui-pull-right iconfont" style="color:#888; font-size:1.5rem;">&#xe60f;</span></li>
            </ul>
			
            <div id="slider" class="mui-slider" style="margin-top:10px;">
				<div id="sliderSegmentedControl" style="background: #fff;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
					{{each data.time_config_list as value i}}
						<a class="mui-control-item" href="#item{{i+1}}mobile">
							{{value.date}}
						</a>
					{{/each}}
				</div>
				<div id="sliderProgressBar" class="mui-slider-progress-bar mui-col-xs-4" style="width:14.3%"></div>
				<div class="mui-slider-group" style="margin-top:0px;">
					<!--1-->
					<div id="item1mobile" class="mui-slider-item mui-control-content mui-active">
						<div id="scroll1" class="mui-scroll-wrapper">
							<div class="mui-scroll" style="">

								<!--时间段-->
								<div style="background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
									<a class="mui-control-item" href="javascript:;">
										时间段
									</a>
									<a class="mui-control-item" href="javascript:;">
										牌照
									</a>
									<a class="mui-control-item" href="javascript:;">
										科目
									</a>
									<a class="mui-control-item" href="javascript:;">
										价格
									</a>
									<a class="mui-control-item" href="javascript:;">
										选择
									</a>
								</div>
								
								<div id="date_time_1" style="overflow-y:scroll; color:# width: 100%;">
									{{each data.time_config_list[0].time_list as val k}}

									<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
										<a class="mui-control-item" style="" href="javascript:;">
											{{val.start_time}}:{{val.start_minute}}-{{val.end_time}}:{{val.end_minute}}
										</a>
										<a class="mui-control-item" href="javascript:;">
											{{val.license_no}}
										</a>
										<a class="mui-control-item" href="javascript:;">
											{{val.subjects}}
										</a>
										<a class="mui-control-item" href="javascript:;">
											￥{{val.final_price}}
										</a>
										<a class="mui-control-item" href="javascript:;">
											{{if val.is_appointed == 2}}
												<span class="iconfont" style="color:#888">&#xe603;</span>
											{{else}}
												<span class="iconfont" style="color:#245269">&#xe602;</span>
											{{/if}}
										</a>
									</div>
				
									{{/each}}
								</div>
								
							</div>

						</div>
					</div>
					<!--2-->
					<div id="item2mobile" class="mui-slider-item mui-control-content">
						<div id="scroll2" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<!--时间段-->
								<div style="background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
									<a class="mui-control-item" href="javascript:;">
										时间段
									</a>
									<a class="mui-control-item" href="javascript:;">
										牌照
									</a>
									<a class="mui-control-item" href="javascript:;">
										科目
									</a>
									<a class="mui-control-item" href="javascript:;">
										价格
									</a>
									<a class="mui-control-item" href="javascript:;">
										选择
									</a>
								</div>
								
								<div id="date_time_2">
									<div class="mui-loading">
										<div class="mui-spinner">
										</div>
									</div>
								</div>
									
							</div>
						</div>
					</div>
					<!--3-->
					<div id="item3mobile" class="mui-slider-item mui-control-content">
						<div id="scroll3" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<!--时间段-->
								<div style="background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
									<a class="mui-control-item" href="javascript:;">
										时间段
									</a>
									<a class="mui-control-item" href="javascript:;">
										牌照
									</a>
									<a class="mui-control-item" href="javascript:;">
										科目
									</a>
									<a class="mui-control-item" href="javascript:;">
										价格
									</a>
									<a class="mui-control-item" href="javascript:;">
										选择
									</a>
								</div>
								
								<div id="date_time_3">
									<div class="mui-loading">
										<div class="mui-spinner">
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					
					<!--4-->
					<div id="item4mobile" class="mui-slider-item mui-control-content">
						<div id="scroll3" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<!--时间段-->
								<div style="background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
									<a class="mui-control-item" href="javascript:;">
										时间段
									</a>
									<a class="mui-control-item" href="javascript:;">
										牌照
									</a>
									<a class="mui-control-item" href="javascript:;">
										科目
									</a>
									<a class="mui-control-item" href="javascript:;">
										价格
									</a>
									<a class="mui-control-item" href="javascript:;">
										选择
									</a>
								</div>
								
								<div id="date_time_4">
									<div class="mui-loading">
										<div class="mui-spinner">
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!--5-->
					<div id="item5mobile" class="mui-slider-item mui-control-content">
						<div id="scroll3" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<!--时间段-->
								<div style="background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
									<a class="mui-control-item" href="javascript:;">
										时间段
									</a>
									<a class="mui-control-item" href="javascript:;">
										牌照
									</a>
									<a class="mui-control-item" href="javascript:;">
										科目
									</a>
									<a class="mui-control-item" href="javascript:;">
										价格
									</a>
									<a class="mui-control-item" href="javascript:;">
										选择
									</a>
								</div>
								
								<div id="date_time_5">
									<div class="mui-loading">
										<div class="mui-spinner">
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!--6-->
					<div id="item6mobile" class="mui-slider-item mui-control-content">
						<div id="scroll3" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<!--时间段-->
								<div style="background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
									<a class="mui-control-item" href="javascript:;">
										时间段
									</a>
									<a class="mui-control-item" href="javascript:;">
										牌照
									</a>
									<a class="mui-control-item" href="javascript:;">
										科目
									</a>
									<a class="mui-control-item" href="javascript:;">
										价格
									</a>
									<a class="mui-control-item" href="javascript:;">
										选择
									</a>
								</div>
								
								<div id="date_time_6">
									<div class="mui-loading">
										<div class="mui-spinner">
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!--7-->
					<div id="item7mobile" class="mui-slider-item mui-control-content">
						<div id="scroll3" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<!--时间段-->
								<div style="background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
									<a class="mui-control-item" href="javascript:;">
										时间段
									</a>
									<a class="mui-control-item" href="javascript:;">
										牌照
									</a>
									<a class="mui-control-item" href="javascript:;">
										科目
									</a>
									<a class="mui-control-item" href="javascript:;">
										价格
									</a>
									<a class="mui-control-item" href="javascript:;">
										选择
									</a>
								</div>
								
								<div id="date_time_7">
									<div class="mui-loading">
										<div class="mui-spinner">
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</script>

		<script>
			mui.init({
				swipeBack: false
			});
	
			(function($, doc) {
				
				var longitude = Cookies.get('lng');
				var latitude = Cookies.get('lat');
				
				$('.mui-scroll-wrapper').scroll({
					indicators: true //是否显示滚动条
				});
				var params = {
					'id':"<?php echo $_GET['id']; ?>",
					'lesson_type':"科目二",
					'licence_type':"C1"
				};
				$.ajax({
					type:"post",
					url:"<?php echo HOST; ?>/_get_coach_detail.php",
					data:params,
					dataType:"json",
					async:false,
					timeout:10000,
					success:function(data) {
						if(data.code == 200) {
							var html = template('coach_detail_temp', data);
							doc.getElementById('coach_detail').innerHTML = html;
							var html = '';
//							alert(JSON.stringify(data.data.time_config_list));			
							$.each(data.data.time_config_list, function(e, t) {
								$.each(t, function(k, v) {
//									$.alert(v)
								});
//								html += '<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted"><a class="mui-control-item" href="javascript:;">8:00-9:00</a><a class="mui-control-item" href="javascript:;">C1</a><a class="mui-control-item" href="javascript:;">科目二</a><a class="mui-control-item" href="javascript:;">￥130</a><a class="mui-control-item" href="javascript:;"><span class="iconfont" style="color:#888">&#xe603;</span></a></div>';							
							});									
							
							//点击定位
							var addresslocation = doc.getElementById('addresslocation');
							addresslocation.addEventListener('tap', function(e) {
								var lng = this.getAttribute('lng');
								var lat = this.getAttribute('lat');
								var gdkey = "<?php echo GDKEY; ?>";
								location.href="http://m.amap.com/navi/?start="+longitude+","+latitude+"&dest="+lng+","+lat+"&destName=合肥&naviBy=car&key="+gdkey;	
							
							});
							//点击进入车辆详情
							var carinfo = doc.getElementById('carinfo');
							carinfo.addEventListener('tap', function(e) {
								var id = this.getAttribute('title');
								location.href="carinfo.php?id="+id;
							})
							
							var item2 = doc.getElementById('item2mobile');
							var item3 = doc.getElementById('item3mobile');
							var item4 = doc.getElementById('item4mobile');
							var item5 = doc.getElementById('item5mobile');
							var item6 = doc.getElementById('item6mobile');
							var item7 = doc.getElementById('item7mobile');
							var slider = doc.getElementById('slider');
							slider.addEventListener('slide', function(e) {
	
								var _date = new Date();
								var timestamp=_date.getTime();
								var date_time_timestamp = timestamp + e.detail.slideNumber * 24 * 3600 * 1000;
								var date_time = formatDate(new Date(date_time_timestamp));
	
								//1
								if (e.detail.slideNumber === 1) {
									if (item2.querySelector('.mui-loading')) {
										var html = '';
										$.each(data.data.time_config_list[1].time_list, function(e, t) {
											alert(t.start_time)
											html += '<div style="background: #fff; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted"><a class="mui-control-item" href="javascript:;">'+t.start_time+':'+t.start_minute+'-9:00</a><a class="mui-control-item" href="javascript:;">C1</a><a class="mui-control-item" href="javascript:;">科目二</a><a class="mui-control-item" href="javascript:;">￥130</a><a class="mui-control-item" href="javascript:;"><span class="iconfont" style="color:#888">&#xe603;</span></a></div>';							
										});
										doc.getElementById('date_time_2').innerHTML = html; 
									}
									
								//2
								} else if (e.detail.slideNumber === 2) {
									if (item3.querySelector('.mui-loading')) {
										
									}
								} else if (e.detail.slideNumber === 3) {
									if (item4.querySelector('.mui-loading')) {
										
									}
								}
							});
							
							var sliderSegmentedControl = doc.getElementById('sliderSegmentedControl');
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
								
							});
						}
					},
					error:function() {
						$.toast('网络错误，请检查网络');
					}
				});
				
				
				
			})(mui, document);
			function formatDate(now)   {     
				var year=now.getYear();     
				var month=now.getMonth()+1;     
				var date=now.getDate();     
				var hour=now.getHours();     
				var minute=now.getMinutes();     
				var second=now.getSeconds();     
              	return month + "-" + date;     
          	}  
		</script>
	</body>
</html>