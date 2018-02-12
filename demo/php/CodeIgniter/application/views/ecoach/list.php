<div id="app" class="mui-fullscreen" v-cloak style="overflow: show;">
    <div v-show="seen" @click="hideShade" class="hide-shade" style=""></div>

	<header class="mui-bar mui-bar-nav">
	    <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
	    <h1 class="mui-title">电子教练考试记录</h1>
	</header>
	<div class="mui-content" >
	    <ul class="mui-table-view">
	        <li class="mui-table-view-cell mui-media" v-for="item in list" @click="show(item)">
	            <a href="javascript:;">
	                <img class="mui-media-object mui-pull-right" :src="item.imgurl">
	                <div class="mui-media-body">
						{{ item.name }}
	                    <p class="mui-ellipsis">{{ item.description }}</p>
	                </div>
	            </a>
	        </li>
	    </ul>
		<div class="mui-card-header">
			<div id="info"></div>
			
			<button id='demo4' data-options='{"type":"date"}' class="btn mui-btn mui-btn-block">选择日期 ...</button>
		</div>
	</div>
    <div id="show" style="width: 100%; height: 300px; background: #fff; position: absolute; bottom: -300px; left: 0px; z-index: 12;">
    	{{ content }}
    </div>
	
</div>
<script type="text/javascript">
	var vm = new Vue({
		el: '#app',
		data: {
			seen: false,
			content: '',
			list: [
				{
					name: '列表一',
					description: '列表一描述',
					imgurl: 'http://placehold.it/40x30'
				},
				{
					name: '列表二',
					description: '列表二描述',
					imgurl: 'http://placehold.it/40x30'
				},
				{
					name: '列表三',
					description: '列表三描述',
					imgurl: 'http://placehold.it/40x30'
				},
				{
					name: '列表四',
					description: '列表四描述',
					imgurl: 'http://placehold.it/40x30'
				},
			]
		},
		methods: {
			show: function(item) {
				this.seen = true;
				this.content = item;
				Velocity(document.getElementById("show"), {
					bottom: '0px',
				}, {
				    duration: 200
				});
			},
			hideShade: function() {
				this.seen = false;
				Velocity(document.getElementById("show"), {
					bottom: '-300px',
				}, {
				    duration: 200
				});
			}
		}
	})
	
	var info = document.getElementById("info");
	var dDate = new Date();
	info.innerText = "今天：" + dDate.getFullYear() + "." + (dDate.getMonth()+1) + "." + dDate.getDate();
	var demo4Obj = document.getElementById('demo4');
	demo4Obj.addEventListener('tap', function() {
		var optionsJson = this.getAttribute('data-options') || '{}';
		var options = JSON.parse(optionsJson);
		var id = this.getAttribute('id');
		/*
		 * 首次显示时实例化组件
		 * 示例为了简洁，将 options 放在了按钮的 dom 上
		 * 也可以直接通过代码声明 optinos 用于实例化 DtPicker
		 */
		console.log(JSON.parse(optionsJson));
		var picker = new $.DtPicker(options);
		picker.show(function(rs) {
			/*
			 * rs.value 拼合后的 value
			 * rs.text 拼合后的 text
			 * rs.y 年，可以通过 rs.y.vaue 和 rs.y.text 获取值和文本
			 * rs.m 月，用法同年
			 * rs.d 日，用法同年
			 * rs.h 时，用法同年
			 * rs.i 分（minutes 的第二个字母），用法同年
			 */
			result.innerText = '选择结果: ' + rs.text;
			/* 
			 * 返回 false 可以阻止选择框的关闭
			 * return false;
			 */
			/*
			 * 释放组件资源，释放后将将不能再操作组件
			 * 通常情况下，不需要示放组件，new DtPicker(options) 后，可以一直使用。
			 * 当前示例，因为内容较多，如不进行资原释放，在某些设备上会较慢。
			 * 所以每次用完便立即调用 dispose 进行释放，下次用时再创建新实例。
			 */
			picker.dispose();
		});
	}, false);
</script>
