<style>
	.mui-grid-view.mui-grid-9 .mui-table-view-cell {
		border: none;
	}
	.mui-grid-view.mui-grid-9 {
		background-color: #fffefd;
	}
	.mui-popover {
		height: 50%;
	}
</style>
<div id="app"  v-cloak >
    <div v-show="seen" @click="hideShade" class="hide-shade" style=""></div>
	<header class="mui-bar mui-bar-nav" style="background: #65f3ad;">
		<!-- <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a> -->
		<h1 class="mui-title">学车报告</h1>
	</header>
	<div class="mui-content">
		<div class="mui-card-content content1">
			<div class="mui-card-content-inner content1-inner1" style="height:100%;">
				<div class="mui-card-header mui-card-header1">
					<div id="info" class="ui-alert"></div>
					<button id='demo_date' data-options='date' class="mui-btn mydate-btn" @click="selectDate"></button>
				</div>
				<div id="content1-main">
					<div class="mui-media-body profile">
						<img class="mui-media-object mui-pull-left avatar" :src="form.photo">
						<span class="username">{{form.user_name}}</span>
						<p class='mui-ellipsis idcard'>{{form.card_id}}</p>
					</div>
					<div class="content1-table">
						<table id="tbl" border="1" bordercolor="#e8e7e3" style="border-collapse:collapse;" width="100%">
							<thead id="tbl-thead"><td>时间段</td><td>场地</td><td>平均速度</td><td>里程</td></thead>
							<tr id="tbl-tr" rowspan="1" colspan="1">
								<td width="28%">{{form.begin_time}}~{{form.end_time}}</td>
								<td width="26%">{{form.address}}</td>
								<td width="26%" >{{form.av_speed}}</td>
								<td width="26%" >{{form.distance}}</td>
							</tr>
						</table>
					</div>
					<div class="content1-li">
						<ul class="mui-table-view mui-grid-view mui-grid-9 pie-chart">
				            <li class="mui-table-view-cell mui-media mui-col-xs-4 mui-col-sm-4">
				            	<a href="javascript:;">
				            		<div class="times times1"><div>{{form.appoint_time}}h</div></div>
				                    <div class="mui-media-body">预约时间</div>
			                    </a>
				            </li>
				            <li class="mui-table-view-cell mui-media mui-col-xs-4 mui-col-sm-4">
				           		<a href="javascript:;">
					            	<div class="times times1"><div>{{form.fact_time}}h</div></div>
				                    <div class="mui-media-body">实际时间</div>
			                    </a>
				            </li>
				            <li class="mui-table-view-cell mui-media mui-col-xs-4 mui-col-sm-4" style="border-right:none;">
				            	<a href="javascript:;">
				                    <div class="times times1"><div>{{form.valid_time}}h</div></div>
				                    <div class="mui-media-body">有效时间</div>
			                    </a>
				            </li>
			        	</ul>
					</div>
	        	</div>
			</div>
		</div>
		<div class="mui-card-content t-content2">
			<div class="mui-card-content-inner t-content2-inner2" style="border-bottom: 1px solid #c8c7cc;">
				<ul class="mui-table-view mui-grid-view mui-grid-9 pie-chart">
		            <li class="mui-table-view-cell mui-media mui-col-xs-4 mui-col-sm-4">
		            	<a href="#common_train_time_list" style="padding-top:1.25rem;">
		            		<img src="<?php echo base_url('assets/images/totaltime.png'); ?>" alt="" width="40" heigt="40">
		            		<div class="mui-media-body">总时间</div>
		                    <div class="mui-media-body">{{form.common_train.total_time}}</div>
	                    </a>
		            </li>
		            <li class="mui-table-view-cell mui-media mui-col-xs-4 mui-col-sm-4">
		           		<a href="javascript:;">
		                    <img src="<?php echo base_url('assets/images/training.png'); ?>" alt="" width="100" heigt="100">
	                    </a>
		            </li>
		            <li class="mui-table-view-cell mui-media mui-col-xs-4 mui-col-sm-4" style="border-right:none;">
		            	<a href="javascript:;" style="padding-top:1.25rem;">
		                    <img src="<?php echo base_url('assets/images/avgspeed.png'); ?>" alt="" width="40" heigt="40">
		                    <div class="mui-media-body">平均速度</div>
		                    <div class="mui-media-body">{{form.common_train.av_speed}}km/h</div>
	                    </a>
		            </li>
	        	</ul>
			</div>
			<div class="mui-card-content-inner t-content2-inner2">
				<ul class="mui-table-view mui-grid-view mui-grid-9 pie-chart">
		            <li class="mui-table-view-cell mui-media mui-col-xs-4 mui-col-sm-4">
		            	<a href="#exam_time_list" style="padding-top:1.25rem;">
		            		<img src="<?php echo base_url('assets/images/totaltime.png'); ?>" alt="" width="40" heigt="40">
		            		<div class="mui-media-body">总时间</div>
		                    <div class="mui-media-body">{{form.exam.total_time}}</div>
	                    </a>
		            </li>
		            <li class="mui-table-view-cell mui-media mui-col-xs-4 mui-col-sm-4">
		           		<a href="javascript:;">
			            	<img src="<?php echo base_url('assets/images/exam.png'); ?>" alt="" width="100" heigt="100">
	                    </a>
		            </li>
		            <li class="mui-table-view-cell mui-media mui-col-xs-4 mui-col-sm-4" style="border-right:none;">
		            	<a href="javascript:;" style="padding-top:1.25rem;">
		                    <img src="<?php echo base_url('assets/images/avgspeed.png'); ?>" alt="" width="40" heigt="40">
		                    <div class="mui-media-body">平均速度</div>
		                    <div class="mui-media-body">{{form.exam.av_speed}}km/h</div>
	                    </a>
		            </li>
	        	</ul>
			</div>
			<div class="mui-card-content-inner" style="padding:0;margin:0 auto;color:#fffefd;text-align: center;font-size:1rem;">
				-嘻哈学车技术支持-
			</div>
		</div>
	</div>
	<div id="common_train_time_list" class="mui-popover">
		<div class="mui-popover-arrow"></div>
		<div class="mui-scroll-wrapper">
			<div class="mui-scroll">
				<ul class="mui-table-view">
					<li class="mui-table-view-cell" v-for="item in form.common_train.time_list">
						<span style="margin-right:50%;">{{item.item}}</span><span>{{item.value}}</span>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div id="exam_time_list" class="mui-popover">
		<div class="mui-popover-arrow"></div>
		<div class="mui-scroll-wrapper">
			<div class="mui-scroll">
				<ul class="mui-table-view">
					<li class="mui-table-view-cell" v-for="item in form.exam.time_list">
						<span style="margin-right:50%;">{{item.item}}</span><span>{{item.value}}</span>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<script>
    var _list_url = 'http://api2.xihaxueche.com:8001/api2/public/v1/ecoach/report';
    var _token = "<?php echo $token; ?>";

	Vue.config.devtools = true;
	var vm = new Vue({
		el: '#app',
		data: {
			seen: false,
			content: '',
			list_url: _list_url,
			token: _token,
			form: {
				// user_name: "马路杀手",
				// card_id: "341234199105011234",
				// photo: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSI9Y9ItwPJ0AwzpzZd3_4Ddr0k3PK7-G3RTLm72Ghb5SdrxhZskA",
				// date: "2017.7.15",
				// begin_time: "12:00",
				// end_time: "14:00",
				// address: "八一驾校南岗22",
				// av_speed: 12.1,
				// distance: 8.3,
				// appoint_time: 2,
				// fact_time: 2,
				// valid_time: 1.8,
				// common_train: {
				// 	total_time: "1小时48分钟",
				// 	av_speed: 1,
				// 	list: [
				// 		{
				// 			item: "倒车入库",
				// 			value: "24分钟"
				// 		},
				// 		{
				// 			item: "倒车入库",
				// 			value: "24分钟"
				// 		},
				// 		{
				// 			item: "倒车入库",
				// 			value: "24分钟"
				// 		},
				// 		{
				// 			item: "倒车入库",
				// 			value: "24分钟"
				// 		},
				// 		{
				// 			item: "倒车入库",
				// 			value: "24分钟"
				// 		}
				// 	]
				// },
				// exam: {
				// 	total_time: "1小时48分钟",
				// 	av_speed: 1,
				// 	list: [
				// 		{
				// 			item: "倒车入库",
				// 			value: "24分钟"
				// 		},
				// 		{
				// 			item: "倒车入库",
				// 			value: "24分钟"
				// 		},
				// 		{
				// 			item: "倒车入库",
				// 			value: "24分钟"
				// 		},
				// 		{
				// 			item: "倒车入库",
				// 			value: "24分钟"
				// 		},
				// 		{
				// 			item: "倒车入库",
				// 			value: "24分钟"
				// 		}
				// 	]
				// }
           	}
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
						vm.refreshstatus = false;
						if(ret.code == 200) {
							if(!ret.data.user) {
								vm.form = ret.data;
							}else {
								//	数据绑定
								vm.form = ret.data;
							}
						}
					},
					error: function() {
						vm.fullscreenLoading = false;
						vm.refreshstatus = false;
					}
				})
			},
			selectDate: function() {
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
		}
	});
</script>