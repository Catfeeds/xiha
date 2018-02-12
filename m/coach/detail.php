<?php
	require_once '../include/config.php';
	$coach_id = htmlspecialchars($_GET['id']);
	$sid = htmlspecialchars($_GET['sid']);
	$sid = isset($sid) ? $sid : htmlspecialchars($_COOKIE['sid']);
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
			.time-config-list-ul .mui-table-view-cell:after {background-color: #fff;}
			.mui-date-time-list .mui-date-time-cell {padding:0px;}
			.choosed {color: #00bd9c !important;}
		</style>
		<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
		<script type="text/javascript">
			//通过config接口注入权限验证配置
//			wx.config({
//			    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
//			    appId: 'wx1baa85b75b6f2d60', // 必填，公众号的唯一标识
//			    timestamp: '<?php echo time();?>', // 必填，生成签名的时间戳
//			    nonceStr: '<?php echo $nonceStr;?>', // 必填，生成签名的随机串
//			    signature: '<?php echo $signature;?>',// 必填，签名
//			    jsApiList: [] // 必填，需要使用的JS接口列表
//			});
//			//通过ready接口处理成功验证
//			wx.ready(function(){
//				// config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后
//			});
		</script>
	</head>
	<body style="background: #f5f5f5;">
		<header class="mui-bar mui-bar-nav">
			<a class="mui-action-back mui-icon mui-icon-left iconfont" href="index.php?id=<?php echo $sid; ?>" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
			<h1 class="mui-title">预约详情</h1>
		</header>
		<div class="mui-content" id="coach_detail">
			<div class="mui-loading" style="background: #f5f5f5; padding-top:10px;">
				<div class="mui-spinner">
				</div>
				<p style="text-align: center;">正在加载中</p>
			</div>
		</div>
		<!--简介-->
		<script type="text/html" id="coach_detail_temp">
			<ul class="mui-table-view">
				<li class="mui-table-view-cell mui-media" title="">
					<a href="javascript:;">
						<img class="mui-media-object mui-pull-left" style="border-radius:50%;" width="100px" height="100px" src="{{data.s_coach_imgurl}}">
						<div class="mui-media-body ">
							{{data.s_coach_name}}({{if data.i_type == 0}}金牌教练{{else}}普通教练{{/if}})
							<p class='mui-ellipsis'>
								{{if data.i_coach_star == 5}}
				        			<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>	
									{{else if data.i_coach_star == 4}}
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
									{{else if data.i_coach_star == 3}}
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
									{{else if data.i_coach_star == 2}}
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe600;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
									<i class="iconfont" style="color:#EC971F; font-size: 1.2rem;">&#xe601;</i>
									{{else if data.i_coach_star == 1}}
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
							</p>
							<p>
								<span class="">教龄</span> <span class="">{{data.s_teach_age}}年</span>
								<span class="">已带学员</span> <span class="">{{data.students_num}}人</span>
							</p>
						</div>
						<!--电话按钮-->
						<div class="mui-media-body" style="position:absolute; top:10px; right:10px;">
							<p class="mui-pull-right" style="height: 100px; padding-top:23px">
								<a href="tel:{{data.s_coach_phone}}"><span class="iconfont" style="font-size:3.5rem; color: #00BD9C;">&#xe60a;</span></a>
							</p>									
						</div>
					</a>
				</li>
			</ul>
			<!--地址 车辆-->
			<ul class="mui-table-view" style="margin-top:10px;">
	            <li class="mui-table-view-cell" id="addresslocation" lng="{{data.dc_coach_distance_x}}" lat="{{data.dc_coach_distance_y}}" style="color: #333333;">地址 <span id="address" style="color:#888">{{data.s_coach_address}}</span><span class="mui-pull-right iconfont" style="color:#2AC845; font-size:1.5rem;">&#xe60d;</span></li>
	            <li class="mui-table-view-cell" id="carinfo" title="{{data.s_coach_car_id}}" style="color: #333333;">车辆 <span id="address" style="color:#888">{{data.car_info[0].name}}({{if data.car_info.car_type == 1}}普通型{{else if data.car_info.car_type == 2}}模拟型{{else}}加强型{{/if}})&nbsp;&nbsp;&nbsp;{{data.car_info[0].car_no}}</span><span class="mui-pull-right iconfont" style="color:#888; font-size:1.5rem;">&#xe60f;</span></li>
            </ul>
			
			<!--时间配置-->
			<div class="mui-content" style="padding-top:10px; margin-bottom: 60px; ">
				<div id="slider" class="mui-slider">
					<div id="sliderSegmentedControl" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
						{{each data.time_config_list as value i}}
						  <a class="mui-control-item" href="#item{{i+1}}mobile" >{{value.date}}</a>						
						{{/each}}
					</div>
					<div id="sliderProgressBar" class="mui-slider-progress-bar mui-col-xs-4" style="width:14.28%"></div>
					
					<div class="mui-slider-group" style="margin-top:0px;">

						<div id="item1mobile" class="mui-slider-item mui-control-content mui-active">
							<div id="scroll1" >
								<div>
									<!--时间段-->
									<div style="position:absolute; background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
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
									<div id="date_time_1">
										<ul class="mui-table-view mui-date-time-list" style="padding-top:39px">	
											{{if data.time_config_list[0].time_list.length > 0}}								
										    {{each data.time_config_list[0].time_list as v j}}
											<li class="mui-table-view-cell mui-date-time-cell">
												<ul class="mui-table-view time-config-list-ul">
													<li class="mui-table-view-cell time_list" style="float: left; width: 20%;text-align: center;">
														{{if v.start_minute && v.end_time}}
															{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}
														{{else}}
															{{v.start_time}}:00-{{v.end_time}}:00
														{{/if}}
													</li>
													<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
														{{v.license_no}}
													</li>
													<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
														{{v.subjects}}
													</li>
													<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
														{{v.price}}
													</li>
													<li class="mui-table-view-cell" style="float: right; padding-right: 30px;width: 20%;text-align: center;">
                                                    {{if v.is_appointed == 2}}
													<span class="iconfont check-time" id="{{v.id}}" title="{{if v.start_minute && v.end_time}}{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}{{else}}{{v.start_time}}:00-{{v.end_time}}:00{{/if}}" style="color:#888; font-size: 1.2rem;">&#xe61a;</span>
                                                    {{else}}
                                                    <span class="iconfont" style="color: #ddd; font-size: 1.2rem;">&#xe603</span>
                                                    {{/if}}
													</li>
												</ul>
											</li>
	                                        {{/each}}
											{{else}}
	                                        <p style="padding: 50px; text-align: center;">
	                                        <br />
											<span class="iconfont" style="font-size: 5rem; color: #ddd;">&#xe605;</span>
	                                        <br />
	                                        <br />
	                                       		 暂无可预约时间  :(	
	                                        </p>
											{{/if}}														
										</ul>									
									</div>																	
								</div>
							</div>
						</div> <!-- #item1mobile-->
						
						<div id="item2mobile" class="mui-slider-item mui-control-content">
							<div id="scroll2" >
								<div style="position:absolute; background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
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
									<ul class="mui-table-view mui-date-time-list" style="padding-top:39px">	
										{{if data.time_config_list[1].time_list.length > 0}}								
									    {{each data.time_config_list[1].time_list as v j}}
										<li class="mui-table-view-cell mui-date-time-cell">
											<ul class="mui-table-view time-config-list-ul">
												<li class="mui-table-view-cell time_list" style="float: left; width: 20%;text-align: center;">
													{{if v.start_minute && v.end_time}}
														{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}
													{{else}}
														{{v.start_time}}:00-{{v.end_time}}:00
													{{/if}}
												</li>

                                                <!--牌照-->
												<li class="mui-table-view-cell" style="float: left; width: 20%;text-align: center;">
													{{v.license_no}}
												</li>

                                                <!--科目-->
												<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
													{{v.subjects}}
												</li>

                                                <!--价格-->
												<li class="mui-table-view-cell" style="float: left; width: 20%;text-align: center;">
													{{v.price}}
												</li>
												<li class="mui-table-view-cell" style="float: right; padding-right: 30px;width: 20%;text-align: center;">
                                                    {{if v.is_appointed == 2}}
													<span class="iconfont check-time" id="{{v.id}}" title="{{if v.start_minute && v.end_time}}{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}{{else}}{{v.start_time}}:00-{{v.end_time}}:00{{/if}}" style="color:#888; font-size: 1.2rem;">&#xe61a;</span>
                                                    {{else}}
                                                    <span class="iconfont" style="color: #ddd; font-size: 1.2rem;">&#xe603</span>
                                                    {{/if}}
												</li>
											</ul>
										</li>
                                        {{/each}}
										{{else}}
                                        <p style="padding: 50px; text-align: center;">
                                        <br />
										<span class="iconfont" style="font-size: 5rem; color: #ddd;">&#xe605;</span>
                                        <br />
                                        <br />
                                       		 暂无可预约时间  :(
                                        </p>
										{{/if}}														
									</ul>
									<!--<div class="mui-loading" style="background: #fff; margin-top:0px;padding-top:50px;">
										<div class="mui-spinner">
										</div>
										<p style="text-align: center;">正在加载中</p>
									</div>-->
								</div>
							</div>
						</div> <!-- #item2mobile-->
						
						<div id="item3mobile" class="mui-slider-item mui-control-content">
							<div id="scroll3" >
								<div style="position:absolute; background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
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
									<ul class="mui-table-view mui-date-time-list" style="padding-top:39px">	
										{{if data.time_config_list[2].time_list.length > 0}}								
									    {{each data.time_config_list[2].time_list as v j}}
										<li class="mui-table-view-cell mui-date-time-cell">
											<ul class="mui-table-view time-config-list-ul">
												<li class="mui-table-view-cell time_list" style="float: left; width: 20%; text-align: center;">
													{{if v.start_minute && v.end_time}}
														{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}
													{{else}}
														{{v.start_time}}:00-{{v.end_time}}:00
													{{/if}}
												</li>
												<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
													{{v.license_no}}
												</li>
												<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
													{{v.subjects}}
												</li>
												<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
													{{v.price}}
												</li>
												<li class="mui-table-view-cell" style="float: right; padding-right: 30px;width: 20%;text-align: center;">
                                                    {{if v.is_appointed == 2}}
													<span class="iconfont check-time" id="{{v.id}}" title="{{if v.start_minute && v.end_time}}{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}{{else}}{{v.start_time}}:00-{{v.end_time}}:00{{/if}}" style="color:#888; font-size: 1.2rem;">&#xe61a;</span>
                                                    {{else}}
                                                    <span class="iconfont" style="color: #ddd; font-size: 1.2rem;">&#xe603</span>
                                                    {{/if}}
												</li>
											</ul>
										</li><!--一条项目结束-->
                                        {{/each}}
										{{else}}
                                        <p style="padding: 50px; text-align: center;">
                                        <br />
										<span class="iconfont" style="font-size: 5rem; color: #ddd;">&#xe605;</span>
                                        <br />
                                        <br />
                                       		 暂无可预约时间  :(	
                                        </p>
										{{/if}}														
									</ul>
									<!--<div class="mui-loading" style="background: #fff; margin-top:0px;padding-top:50px;">
										<div class="mui-spinner">
										</div>
										<p style="text-align: center;">正在加载中</p>
									</div>-->
								</div>
							</div>
						</div> <!-- #item3mobile-->
						
						<div id="item4mobile" class="mui-slider-item mui-control-content">
							<div id="scroll4" >
								<div style="position:absolute; background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
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
									<ul class="mui-table-view mui-date-time-list" style="padding-top:39px">	
										{{if data.time_config_list[3].time_list.length > 0}}								
									    {{each data.time_config_list[3].time_list as v j}}
										<li class="mui-table-view-cell mui-date-time-cell">
											<ul class="mui-table-view time-config-list-ul">
												<li class="mui-table-view-cell time_list" style="float: left;width: 20%;text-align: center; ">
													{{if v.start_minute && v.end_time}}
														{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}
													{{else}}
														{{v.start_time}}:00-{{v.end_time}}:00
													{{/if}}
												</li>
												<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
													{{v.license_no}}
												</li>
												<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
													{{v.subjects}}
												</li>
												<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
													{{v.price}}
												</li>
												<li class="mui-table-view-cell" style="float: right; padding-right: 30px;width: 20%;text-align: center;">
                                                    {{if v.is_appointed == 2}}
													<span class="iconfont check-time" id="{{v.id}}" title="{{if v.start_minute && v.end_time}}{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}{{else}}{{v.start_time}}:00-{{v.end_time}}:00{{/if}}" style="color:#888; font-size: 1.2rem;">&#xe61a;</span>
                                                    {{else}}
                                                    <span class="iconfont" style="color: #ddd; font-size: 1.2rem;">&#xe603</span>
                                                    {{/if}}
												</li>
											</ul>
										</li>
                                        {{/each}}
										{{else}}
                                        <p style="padding: 50px; text-align: center;">
                                        <br />
										<span class="iconfont" style="font-size: 5rem; color: #ddd;">&#xe605;</span>
                                        <br />
                                        <br />
                                       		 暂无可预约时间  :(	
                                        </p>
										{{/if}}														
									</ul>
									<!--<div class="mui-loading" style="background: #fff; margin-top:0px;padding-top:50px;">
										<div class="mui-spinner">
										</div>
										<p style="text-align: center;">正在加载中</p>
									</div>-->
								</div>
							</div>
						</div> <!-- #item4mobile-->
						
						<div id="item5mobile" class="mui-slider-item mui-control-content">
							<div id="scroll5" >
								<div style="position:absolute; background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
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
									<ul class="mui-table-view mui-date-time-list" style="padding-top:39px">	
										{{if data.time_config_list[4].time_list.length > 0}}								
									    {{each data.time_config_list[4].time_list as v j}}
										<li class="mui-table-view-cell mui-date-time-cell">
											<ul class="mui-table-view time-config-list-ul">
												<li class="mui-table-view-cell time_list" style="float: left; width: 20%;text-align: center;">
													{{if v.start_minute && v.end_time}}
														{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}
													{{else}}
														{{v.start_time}}:00-{{v.end_time}}:00
													{{/if}}
												</li>
												<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
													{{v.license_no}}
												</li>
												<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
													{{v.subjects}}
												</li>
												<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
													{{v.price}}
												</li>
												<li class="mui-table-view-cell" style="float: right; padding-right: 30px;width: 20%;text-align: center;">
                                                    {{if v.is_appointed == 2}}
													<span class="iconfont check-time" id="{{v.id}}" title="{{if v.start_minute && v.end_time}}{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}{{else}}{{v.start_time}}:00-{{v.end_time}}:00{{/if}}" style="color:#888; font-size: 1.2rem;">&#xe61a;</span>
                                                    {{else}}
                                                    <span class="iconfont" style="color: #ddd; font-size: 1.2rem;">&#xe603</span>
                                                    {{/if}}
												</li>
											</ul>
										</li>
                                        {{/each}}
										{{else}}
                                        <p style="padding: 50px; text-align: center;">
                                        <br />
										<span class="iconfont" style="font-size: 5rem; color: #ddd;">&#xe605;</span>
                                        <br />
                                        <br />
                                       		 暂无可预约时间  :(	
                                        </p>
										{{/if}}														
									</ul>
									<!--<div class="mui-loading" style="background: #fff; margin-top:0px;padding-top:50px;">
										<div class="mui-spinner">
										</div>
										<p style="text-align: center;">正在加载中</p>
									</div>-->
								</div>
							</div>
						</div> <!-- #item5mobile-->
						
						<div id="item6mobile" class="mui-slider-item mui-control-content">
							<div id="scroll6" >
								<div style="position:absolute; background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
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
									<ul class="mui-table-view mui-date-time-list" style="padding-top:39px">	
										{{if data.time_config_list[5].time_list.length > 0}}								
									    {{each data.time_config_list[5].time_list as v j}}
										<li class="mui-table-view-cell mui-date-time-cell">
											<ul class="mui-table-view time-config-list-ul">
												<li class="mui-table-view-cell time_list" style="float: left;width: 20%;text-align: center; ">
													{{if v.start_minute && v.end_time}}
														{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}
													{{else}}
														{{v.start_time}}:00-{{v.end_time}}:00
													{{/if}}
												</li>
												<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
													{{v.license_no}}
												</li>
												<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
													{{v.subjects}}
												</li>
												<li class="mui-table-view-cell" style="float: left;width: 20%;text-align: center;">
													{{v.price}}
												</li>
												<li class="mui-table-view-cell" style="float: right; padding-right: 30px;width: 20%;text-align: center;">
                                                    {{if v.is_appointed == 2}}
													<span class="iconfont check-time" id="{{v.id}}" title="{{if v.start_minute && v.end_time}}{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}{{else}}{{v.start_time}}:00-{{v.end_time}}:00{{/if}}" style="color:#888; font-size: 1.2rem;">&#xe61a;</span>
                                                    {{else}}
                                                    <span class="iconfont" style="color: #ddd; font-size: 1.2rem;">&#xe603</span>
                                                    {{/if}}
												</li>
											</ul>
										</li>
                                        {{/each}}
										{{else}}
                                        <p style="padding: 50px; text-align: center;">
                                        <br />
										<span class="iconfont" style="font-size: 5rem; color: #ddd;">&#xe605;</span>
                                        <br />
                                        <br />
                                       		 暂无可预约时间  :(	
                                        </p>
										{{/if}}														
									</ul>
									<!--<div class="mui-loading" style="background: #fff; margin-top:0px;padding-top:50px;">
										<div class="mui-spinner">
										</div>
										<p style="text-align: center;">正在加载中</p>
									</div>-->
								</div>
							</div>
						</div> <!-- #item6mobile-->
						
						<div id="item7mobile" class="mui-slider-item mui-control-content">
							<div id="scroll7" >
								<div style="position:absolute; background: #fff; border-bottom: 1px solid #eee; color:#666;" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
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
									<ul class="mui-table-view mui-date-time-list" style="padding-top:39px">	
										{{if data.time_config_list[6].time_list.length > 0}}								
									    {{each data.time_config_list[6].time_list as v j}}
										<li class="mui-table-view-cell mui-date-time-cell">
											<ul class="mui-table-view time-config-list-ul">
												<li class="mui-table-view-cell time_list" style="float: left; width: 20%; text-align: center;">
													{{if v.start_minute && v.end_time}}
														{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}
													{{else}}
														{{v.start_time}}:00-{{v.end_time}}:00
													{{/if}}
												</li>
												<li class="mui-table-view-cell" style="float: left; width: 20%;text-align: center;">
													{{v.license_no}}
												</li>
												<li class="mui-table-view-cell" style="float: left; width: 20%;text-align: center;">
													{{v.subjects}}
												</li>
												<li class="mui-table-view-cell" style="float: left; width: 20%;text-align: center;">
													{{v.price}}
												</li>
												<li class="mui-table-view-cell" style="float: right; padding-right: 30px; width: 20%;text-align: center; ">
                                                    {{if v.is_appointed == 2}}
													<span class="iconfont check-time" id="{{v.id}}" title="{{if v.start_minute && v.end_time}}{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}{{else}}{{v.start_time}}:00-{{v.end_time}}:00{{/if}}" style="color:#888; font-size: 1.2rem;">&#xe61a;</span>
                                                    {{else}}
                                                    <span class="iconfont" style="color: #ddd; font-size: 1.2rem;">&#xe603</span>
                                                    {{/if}}
												</li>
											</ul>
										</li>
                                        {{/each}}
										{{else}}
                                        <p style="padding: 50px; text-align: center;">
                                        <br />
										<span class="iconfont" style="font-size: 5rem; color: #ddd;">&#xe605;</span>
                                        <br />
                                        <br />
                                       		 暂无可预约时间  :(	
                                        </p>
										{{/if}}														
									</ul>
									<!--<div class="mui-loading" style="background: #fff; margin-top:0px;padding-top:50px;">
										<div class="mui-spinner">
										</div>
										<p style="text-align: center;">正在加载中</p>
									</div>-->
								</div>
							</div>
						</div> <!-- #item7mobile-->
						
					</div>
				</div>
			</div>
		</div>
		<div class="" id="sendorder" style="width: 100%; position: fixed; bottom: 0px;">
			<button id='submit-btn' class="mui-btn mui-btn-block mui-btn-red" style='margin: 0px; padding:10px 0px;border-radius: 0px;'>提交订单</button>
		</div>
		</script>
		
		<script src="../assets/js/mui.min.js"></script>
		<script src="../assets/js/cookie.min.js"></script>
		<script src="../assets/js/template.js"></script>
		<script>
			mui.init({
				swipeBack: false
			});
			var _date = new Date();
			var timestamp=_date.getTime();
			var current_date_time = formatDate(new Date(timestamp));
			Cookies.set('timelist', '');
			Cookies.set('timeconfigids', '');
			Cookies.set('car_type', '');
			Cookies.set('date', current_date_time);
			(function($, doc) {
				
                //get uid
                var loginauth = localStorage.getItem('loginauth');
                if (loginauth != '{}' || loginauth) {
                    var loginauth_json = JSON.parse(loginauth);
                    var uid = loginauth_json.l_user_id;
                } else {
                    localStorage.setItem('loginauth','{}');
                }

				var longitude = Cookies.get('lng');
				var latitude = Cookies.get('lat');
				
				$('.mui-scroll-wrapper').scroll({
					indicators: true //是否显示滚动条
				});
				var params = {
					'id':"<?php echo $coach_id; ?>",
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
//							alert(JSON.stringify(data.data.time_config_list));
							Cookies.set('coach_name', data.data.s_coach_name);
							var car_type = '';
							if(data.data.car_info[0].car_type == 1) {
								car_type = data.data.car_info[0].name + '普通型' ;
							} else if(data.data.car_info[0].car_type == 2) {
								car_type = data.data.car_info[0].name + '模拟型' ;	
							} else {
								car_type = data.data.car_info[0].name + '加强型' ;
							}
							Cookies.set('car_type', car_type);
							var html = template('coach_detail_temp', data);
							doc.getElementById('coach_detail').innerHTML = html;
							var html = '';							
							
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
							//左右滑动
							slider.addEventListener('slide', function(e) {
								
								var date_time_timestamp = timestamp + e.detail.slideNumber * 24 * 3600 * 1000;
								var date_time = formatDate(new Date(date_time_timestamp));
								Cookies.set('date', date_time);
//								alert(e.detail.slideNumber)

								//1
								Cookies.set('timelist', '');
								Cookies.set('timeconfigids', '');
								var iconfont_check= doc.querySelectorAll('#slider .check-time');
//								console.log(iconfont_check.length);
								for(var i = 0; i < iconfont_check.length; i++) {
									iconfont_check[i].className = 'iconfont check-time';
									iconfont_check[i].innerHTML = '&#xe61a;';
								}
								if (e.detail.slideNumber === 1) {
//									if (item2.querySelector('.mui-loading')) {
//										var html = getDateTimeList(data.data.time_config_list[1].time_list);
//										doc.getElementById('date_time_2').innerHTML = html; 
//									}

								//2
								} else if (e.detail.slideNumber === 2) {
//									if (item3.querySelector('.mui-loading')) {
//										var html = getDateTimeList(data.data.time_config_list[2].time_list);
//										doc.getElementById('date_time_3').innerHTML = html; 
//									}

								} else if (e.detail.slideNumber === 3) {
//									if (item4.querySelector('.mui-loading')) {
//										var html = getDateTimeList(data.data.time_config_list[3].time_list);
//										doc.getElementById('date_time_4').innerHTML = html; 
//									}

								} else if (e.detail.slideNumber === 4) {
//									if (item5.querySelector('.mui-loading')) {
//										var html = getDateTimeList(data.data.time_config_list[4].time_list);
//										doc.getElementById('date_time_5').innerHTML = html; 
//									}

								} else if (e.detail.slideNumber === 5) {
//									if (item6.querySelector('.mui-loading')) {
//										var html = getDateTimeList(data.data.time_config_list[5].time_list);
//										doc.getElementById('date_time_6').innerHTML = html; 
//									}

								} else if (e.detail.slideNumber === 6) {
//									if (item7.querySelector('.mui-loading')) {
//										var html = getDateTimeList(data.data.time_config_list[6].time_list);
//										doc.getElementById('date_time_7').innerHTML = html; 
//									}
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
							$(".mui-date-time-list").on('tap','.mui-date-time-cell',function(e){
								checktime(this);
							});
							//提交订单
							doc.getElementById('sendorder').addEventListener('tap', function(e) {
                                //登陆才能下单
                                if (loginauth != '{}') {
                                    //可以下单
                                } else {
                                    location.href = "login.php?id=<?php echo $coach_id;?>&from=detail";
                                }

								if(Cookies.get('timeconfigids') == '' && (Cookies.get('timelist') == '[]' || Cookies.get('timelist') == '')) {
									$.toast('请选择时间段');
									return false;
								}
//								console.log(Cookies.get('timelist'));
//								return false;
								var params = {
									'user_id':uid,
									'coach_id':"<?php echo $coach_id; ?>",
									'time_config_id':Cookies.get('timeconfigids'),
									'date':Cookies.get('date'),
									'coupon_id':1,
									'param_1':1,
									'param_2':1,
									'param_3':1,
									'param_4':1
								};
								$.ajax({
									type:"post",
									url:"<?php echo HOST; ?>/order_check.php",
									data:params,
									dataType:"json",
									timeout:10000,
									async:true,
									beforeSend:function() {
										this.innerHTML = '正在提交';
									},
									success:function(data) {
										this.innerHTML = '提交订单';
										if(data.code == 200) {
											location.href = "order.php?id=<?php echo $coach_id; ?>";
										} else {
											$.toast(data.data);
										}
									},
									error:function() {
										this.innerHTML = '提交订单';
										$.toast('网络错误，请检查网络');
									}
								});
							});
						}
					},
					error:function() {
						$.toast('网络错误，请检查网络');
					}
				});
				
				function getDateTimeList(data) {
	           		var html = '';
	           		if(data.length > 0) {								
						html += '<ul class="mui-table-view mui-date-time-list" style="padding-top:39px">'
						$.each(data, function(e, t) {
							var start_minute = '00';
							var end_minute = '00';
							if(t.start_minute && t.end_minute) {
								start_minute = t.start_minute;
								end_minute = t.end_minute;
							}
							html += '<li class="mui-table-view-cell mui-date-time-cell"><ul class="mui-table-view time-config-list-ul"><li class="mui-table-view-cell time_list" style="float: left; ">'+t.start_time+':'+start_minute+'-'+t.end_time+':'+end_minute+'</li><li class="mui-table-view-cell" style="float: left;">'+t.license_no+'</li><li class="mui-table-view-cell" style="float: left;">'+t.subjects+'</li><li class="mui-table-view-cell" style="float: left;">'+t.price+'</li><li class="mui-table-view-cell" style="float: right; padding-right: 30px;"><span class="iconfont"  id="{{v.id}}" title="{{if v.start_minute && v.end_time}}{{v.start_time}}:{{v.start_minute}}-{{v.end_time}}:{{v.end_minute}}{{else}}{{v.start_time}}:00-{{v.end_time}}:00{{/if}}" style="color:#888; font-size:1.2rem;">&#xe61a;</span></li></ul></li>';
						});
						html += '</ul>';
					} else {
						html += '<p style="padding: 60px; text-align: center;"><br /><span class="iconfont" style="font-size: 5rem; color: #ddd;">&#xe605;</span><br />暂无可预约时间  :(	</p>';		
					}
					return html;
	           	}
				
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
           	
           	function checktime(obj) {
           		//获取id
				var checked = obj.querySelector('.check-time');
				//未选择
                if (!checked) {
                    return false;
                }
				if(checked.innerHTML == "") {
					checked.className = 'iconfont check-time';
					checked.innerHTML = '&#xe61a;';
				} else {
					//选择
					time_list += obj.querySelector('.time_list').innerHTML+'|';
					Cookies.set('timelist', time_list);
					checked.className += ' choosed';
					checked.innerHTML = '&#xe602;';
				}
				var time_list = [];
				var time_config_ids = '';
				for(var i = 0; i < document.getElementsByClassName('choosed').length; i++) {
					time_list[i] = document.getElementsByClassName('choosed')[i].getAttribute('title');
					time_config_ids += document.getElementsByClassName('choosed')[i].getAttribute('id') + ',';
				}
				Cookies.set('timelist', JSON.stringify(time_list));
				Cookies.set('timeconfigids', time_config_ids);
           	}
		</script>
	</body>
</html>
