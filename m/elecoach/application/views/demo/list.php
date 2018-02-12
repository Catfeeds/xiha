<div id="app" class="mui-fullscreen" v-cloak style="overflow: hidden;">
    <div v-show="seen" @click="hideShade" class="hide-shade" style=""></div>

	<header class="mui-bar mui-bar-nav">
	    <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
	    <h1 class="mui-title">列表demo</h1>
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
</script>
