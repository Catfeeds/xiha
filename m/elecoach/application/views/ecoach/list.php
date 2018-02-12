<div id="app"  v-cloak >
    <div v-show="seen" @click="hideShade" class="hide-shade" style=""></div>
	<header class="mui-bar mui-bar-nav" style="background: #65f3ad;">
		<!-- <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a> -->
		<h1 class="mui-title">电子教练考试记录</h1>
	</header>
	<div class="mui-content">
		<div class="mui-card-content content1">
			<div class="mui-card-content-inner content1-inner1">
				<div class="mui-card-header mui-card-header1">
					<div id="info" class="ui-alert"></div>
					<button id='demo_date' data-options='date' class="mui-btn mydate-btn" @click="selectDate"></button>
				</div>
				<div id="content1-main">
					<div class="mui-media-body profile">
						<img class="mui-media-object mui-pull-left avatar" :src="form.avatar">
						<span class="username">{{form.name}}</span>
						<p class='mui-ellipsis idcard'>{{form.idcard}}</p>
					</div>
					<div class="content1-table">
						<table id="tbl" border="1" bordercolor="#e8e7e3" style="border-collapse:collapse;" width="100%">
							<thead id="tbl-thead"><td>名称</td><td>开始时间</td><td>结束时间</td></thead>
							<tr id="tbl-tr" rowspan="1" colspan="1">
								<td width="33.3333%">{{form.trainsub}}</td>
								<td width="33.3333%">{{form.begin}}</td>
								<td width="33.3333%" >{{form.end}}</td>
							</tr>
						</table>
					</div>
					<div class="content1-li">
						<ul class="mui-table-view mui-grid-view mui-grid-9 pie-chart">
				            <li class="mui-table-view-cell mui-media mui-col-xs-4 mui-col-sm-4">
				            	<a href="javascript:;">
				            		<div class="times times1"><div>{{form.total}}次</div></div>
				                    <div class="mui-media-body">总次数</div>
			                    </a>
				            </li>
				            <li class="mui-table-view-cell mui-media mui-col-xs-4 mui-col-sm-4">
				           		<a href="javascript:;">
					            	<div class="times times2"><div>{{form.pass}}次</div></div>
				                    <div class="mui-media-body">合格次数</div>
			                    </a>
				            </li>
				            <li class="mui-table-view-cell mui-media mui-col-xs-4 mui-col-sm-4" style="border-right:none;">
				            	<a href="javascript:;">
					            	<div id="passrate">{{form.passrate}}</div>
				                    <div class="mui-media-body">合格率</div>
			                    </a>
				            </li>
			        	</ul>
					</div>
	        	</div>
			</div>
		</div>
		<div class="mui-card-content content2">
			<div class="mui-card-content-inner content2-inner2">
				<div><span id="chart-title1">出错统计</span><span id="chart-title2">&nbsp;单位：次</span></div>
				<div class="chart" id="barChart"></div>
			</div>
		</div>
		<div class="mui-card-content content3">
			<div class="mui-card-content-inner content3-inner3">
				<ul class="mui-table-view mui-table-view-striped mui-table-view-condensed">
			        <li class="mui-table-view-cell" v-for="item in form.traindetail" @click="show(item)">
			            <div class="mui-table">
			                <div class="mui-table-cell mui-col-xs-10">
			                	<div><span class="score">成绩<b>&nbsp;{{item.score}}&nbsp;</b>分</span><span class="nums"><b>&nbsp;{{item.nums}}&nbsp;</b>次模拟考试</span></div>
			                    <table id="tbl2"  width="100%" cellspacing="5">
									<tr class="tbl2-tr q"><td style="width:25%;">开始时间</td><td>{{item.begin}}</td></tr>
									<tr class="tbl2-tr"><td style="width:25%;">结束时间</td><td>{{item.end}}</td></tr>
									<tr class="tbl2-tr q"><td style="width:25%;">用时时长</td><td>{{item.total_time}}</td></tr>
									<tr class="tbl2-tr"><td>平均速度</td><td>{{item.avspeed}}km/h</td></tr>
									<tr class="tbl2-tr q"><td>扣分值</td><td>{{item.lostscore}}分</td></tr>
									<tr class="tbl2-tr"><td>扣分原因</td><td><p v-for="(value, index) in item.lostreasons">{{index+1}}.&nbsp;{{value}}</p></td></tr>
								</table>
			                </div>
			            </div>
			        </li>
			    </ul>
			</div>
		</div>
	</div>
</div>
<script>
    var _list_url = 'http://api2.xihaxueche.com:8001/api2/public/v1/ecoach/examresults';
    var _token = "<?php echo $token; ?>";

    Vue.directive('tap',{
	    bind:function(el,binding){
	        var startTx, startTy,endTx,endTy;
	        el.addEventListener("touchstart",function(e){
	            var touch=e.touches[0];
	            startTx = touch.clientX;
	            startTy = touch.clientY;
	            el.addEventListener("touchend",function(e){
	                    var touch = e.changedTouches[0];
	                    endTx = touch.clientX;
	                    endTy = touch.clientY;
	                    if( Math.abs(startTx - endTx) < 6 && Math.abs(startTy - endTy) < 6){
	                        var method = binding.value.method;
	                        var params = binding.value.params;
	                        method(params);
	                    }
	                },false);
	        },false );
	    }
	})
	//Vue.config.devtools = true;
	var vm = new Vue({
		el: '#app',
		data: {
			seen: false,
			content: '',
			list_url: _list_url,
			token: _token,
			form: {
				"date": "",
		        "name": "",
		        "avatar": "",
		        "idcard": "",
		        "trainsub": "",
		        "begin": "",
		        "end": "",
		        "total": 0,
		        "pass": 0,
		        "passrate": 0,
		        "failstat": {
		            "label": [
		            ],
		            "value": [
		            ]
		        },
		        "traindetail": [
		            {
		                "score": 0,
		                "nums": 0,
		                "begin": "",
		                "avspeed": "",
		                "lostscore": 0,
		                "lostreasons": ""
		            }
		        ]
			},
		},
		created: function() {
			//	默认显示今天日期
			var info = document.getElementById("info");
			var dDate = new Date();
			var infovalue = dDate.getFullYear() + "/" + (dDate.getMonth()+1) + "/" + dDate.getDate();
			info.innerText = "今天：" + infovalue;

			//	请求今天的数据
			var todayDate = new Date(Date.parse(infovalue.replace(/-/g, "/")));
		   	todayDate = todayDate.getTime()/1000;
			this.listAjax({"datetime":todayDate, "token":this.token});
		},
		methods: {
			show: function(item) {
				// this.seen = true;
				// this.content = item;
				// Velocity(document.getElementById("show"), {
				// 	bottom: '0px',
				// }, {
				//     duration: 200
				// });
			},
			hideShade: function() {
				// this.seen = false;
				// Velocity(document.getElementById("show"), {
				// 	bottom: '-300px',
				// }, {
				//     duration: 200
				// });
			},
			listAjax: function(param) {
				$.ajax({
					type: 'get',
					url: this.list_url,
					data: param,
					dataType:"json",
					async: true,
					success: function(ret) {
						setTimeout(function() {vm.fullscreenLoading = false;}, 500);
						vm.refreshstatus = true;
						if(ret.code == 200) {
							if(!ret.data.name) {
								// 数据渲染
								vm.form = ret.data;
								$('.profile').html('暂无学员信息');
								$('#tbl-tr').html("<td width='33.3333%'>暂无记录</td><td width='33.3333%'>暂无记录</td><td width='33.3333%'>暂无记录</td>");
								$('.profile').css('text-align','center');
								$('.times').html('<div>0次</div>');
								//	合格率为百分之零
								vm.circleDraw({id: "passrate",width: "50",height: "50",percent: 0,border: "1",bgColor: "#eeeeee",barColor: "#88f2c5",textColor: "black"});
								var barChart = echarts.init(document.getElementById('barChart'));
								barChart.setOption({});
								$('#barChart').css({
									'background-image':'url(<?php echo base_url('assets/images/chartnodata.png'); ?>)',
									'background-repeat':'no-repeat',
									'background-size':'40%',
									'background-position':'center center'
								});
							}else {
								// 	数据渲染
								vm.form = ret.data;
								$('.profile').html('<img class="mui-media-object mui-pull-left avatar"><span class="username">'+vm.form.name+'</span><p class="mui-ellipsis idcard">'+vm.form.idcard+'</p>');
								$('#tbl-tr').html("<td width='33.3333%'>"+vm.form.trainsub+"</td><td width='33.3333%'>"+vm.form.begin+"</td><td width='33.3333%'>"+vm.form.end+"</td>");
								$('.times1').html('<div>'+vm.form.total+'次</div>');
								$('.times2').html('<div>'+vm.form.pass+'次</div>');
								//	合格率更新数据
								vm.circleDraw({id: "passrate",width: "50",height: "50",percent: ret.data.passrate,border: "1",bgColor: "#eeeeee",barColor: "#88f2c5",textColor: "black"});
								//	出错统计更新数据
								if(!ret.data.failstat) {
									mui.toast('暂无相关数据');
								}else {
									var chartOption = {
										grid: {x: 17,x2: 5,y: 20,y2: 100,},
										toolbox: {
											show: false,
											feature: {mark: {show: true},dataView: {show: true,readOnly: false},magicType: {show: true,type: ['line', 'bar']},restore: {show: true},saveAsImage: {show: true}}
										},
										color:['#77e9c7', '#77e9c7','#77e9c7','#77e9c7','#77e9c7','#77e9c7','#77e9c7','#77e9c7','#77e9c7'],
										calculable: false,
										lable:{
									        normal:{
									            textStyle:{
									                fontsize:'0.5rem'
									            }
									        }
									    },
										xAxis: [{type: 'category',data: vm.form.failstat.label,axisLine:{lineStyle:{color:'#cccccc'}},axisLabel: {
											interval: 0,
											textStyle:{
								                fontSize:'0.1rem',
								            },
		                                    formatter:function(value){ // x轴文本竖直输出
												var ret = "";
												var maxLength = 1;
												var valLength = value.length;
												var rowN = Math.ceil(valLength / maxLength);
												if (rowN > 1) {
												 for (var i = 0; i < rowN; i++) {
												     var temp = "";
												     var start = i * maxLength;
												     var end = start + maxLength;
												     temp = value.substring(start, end) + "\n";
												     ret += temp;
												 }
												 return ret;
												} else {return value;}
		                                     }
										}}],
										yAxis: [{type: 'value',axisLine:{lineStyle:{color:'#cccccc'}}}],
										splitLine:{show: false},
										series: [{name: 'height',type: 'bar',label: {normal: {show: true,position: 'top'},splitLine:{show: false}},
											data: vm.form.failstat.value
										}],
									};
									var barChart = echarts.init(document.getElementById('barChart'));
									barChart.setOption(chartOption);
									$('#barChart').css({
										'background-image':'none',
									});
								}
							}
						}
					},
					error: function() {
						vm.fullscreenLoading = false;
						vm.refreshstatus = false;
					}
				})
			},
			selectDate: function () {  // 选择日期按钮获取焦点事件
				var dDate = new Date();
				$('#demo_date').mobiscroll().date({
		            theme: 'ios',
		            mode: 'mixed',
		            display: 'top',
		            lang: 'zh',
		            setText: "确定",
					cancelText: "取消",
					minDate: new Date(dDate.getFullYear() - 1, 0, 1),
					maxDate: new Date(dDate.getFullYear(), dDate.getMonth(), dDate.getDate()),
					onClose:function(textVale,inst){ //插件效果退出时执行 inst:表示点击的状态反馈：set/cancel
						if(inst === 'set') {
							var result = $('#info')[0];
							result.innerText = textVale;
						   	var datetime = new Date(Date.parse(textVale.replace(/-/g, "/")));
						   	datetime = datetime.getTime()/1000;
							if(Math.round(datetime) === datetime) {
								vm.listAjax({"datetime":datetime, "token":vm.token})
							}
						}
				  	}
		        });
			},
			circleDraw: function (options) {  // 合格率百分比画圆
				this.id = options.id;
				this.width = options.width;
				this.height = options.height;
				this.percent = options.percent;
				this.border = options.border;
				this.bgColor = options.bgColor;
				this.barColor = options.barColor;
				this.textColor = options.textColor;
				var html = "<canvas id='canvas_" + this.id + "' width='" + this.width + "' height='" + this.height + "'></canvas>";
				if(this.id) {
					document.getElementById(this.id).innerHTML = html;
					var degree = this.percent;
					var canvas = document.getElementById('canvas_' + this.id);
					var context = canvas.getContext('2d');
					context.clearRect(0, 0, this.width, this.height);
					//开始绘画
					context.beginPath();
					context.lineWidth = this.border;
					context.strokeStyle = this.bgColor;
					context.arc(this.width / 2, this.height / 2, (this.width / 2 - this.border / 2), 0, 2 * Math.PI);
					context.stroke();
					var deg = degree * 3.6 / 180 * Math.PI
					context.beginPath();
					context.lineWidth = this.border;
					context.strokeStyle = this.barColor;
					context.arc(this.width / 2, this.height / 2, (this.width / 2 - this.border / 2), 0 - Math.PI / 2, deg - Math.PI / 2);
					context.stroke();
					context.beginPath();
					context.fillStyle = this.textColor;
					context.font = "18px 微软雅黑";
					var text = degree + "%";
					var textWidth = context.measureText(text).width;
					context.fillText(text, this.width / 2 - textWidth / 2, this.height / 2 + 9);
				}
			}
		}
	});
</script>