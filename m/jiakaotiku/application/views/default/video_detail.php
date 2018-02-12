<link rel="stylesheet" href="<?php echo base_url('assets/css/video.min.css');?>" />
<style type="text/css">
	@font-face {
	  font-family: 'iconfont';  /* project id 400937 */
	  src: url('//at.alicdn.com/t/font_400937_m4ja4p0muvt8d7vi.eot');
	  src: url('//at.alicdn.com/t/font_400937_m4ja4p0muvt8d7vi.eot?#iefix') format('embedded-opentype'),
	  url('//at.alicdn.com/t/font_400937_m4ja4p0muvt8d7vi.woff') format('woff'),
	  url('//at.alicdn.com/t/font_400937_m4ja4p0muvt8d7vi.ttf') format('truetype'),
	  url('//at.alicdn.com/t/font_400937_m4ja4p0muvt8d7vi.svg#iconfont') format('svg');
	}
	.iconfont{
	  font-family:"iconfont" !important;
	  font-size:16px;font-style:normal;
	  -webkit-font-smoothing: antialiased;
	  -webkit-text-stroke-width: 0.2px;
	  -moz-osx-font-smoothing: grayscale;
	}
	.my-player-dimensions {width: 100% !important; height: 200px !important;}
	.mui-table-view-cell>a:not(.mui-btn).mui-active {background: none !important;}
	.xiha-button {background: linear-gradient(135deg,#0094F5,#01D6E8); display: block; height: 38px; border: none; border-radius: 30px; color: #fff; width: 130px; margin: 0px auto;}
	.xiha-button:active {background: linear-gradient(135deg,#01D6E8,#0094F5);;}
	/*.xiha-button {width: 130px; height: 38px;}*/
	.login-form {position: fixed; width: 100%; height: 100px; background: #fff;  left: 0px; z-index: 3; padding: 10px; overflow-y: scroll;}
	.mui-input-group:before, .mui-input-group:after {background-color: #fff;}
	.mui-input-row label {width: 20%;}
	.mui-input-row label~input, .mui-input-row label~select, .mui-input-row label~textarea {width: 80%;}
	.mui-table-view-cell:after {background-color: #eee;}
</style>

<div id="app" class="mui-fullscreen" v-cloak style="overflow: scroll;">
     <div v-show="seen" @click="hideShade" class="hide-shade" style="z-index: 2;"></div> 
    <header class="mui-bar mui-bar-nav">
        <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
        <h1 class="mui-title">{{info.video_title}}</h1>
    </header>
    <div class="mui-content" style="margin-bottom: 70px;">
		<video
		    id="my-player"
		    class="video-js vjs-big-play-centered"
		    width="100%"
		    height="200px"
		    controls
		    preload="auto"
		    :poster="pic_url"
		    data-setup='{}'
		    webkit-playsinline>
		  <source :src="video_url" type="video/mp4"></source>
		  <source :src="video_url" type="video/webm"></source>
		  <source :src="video_url" type="video/ogg"></source>
		  <p class="vjs-no-js">
		    To view this video please enable JavaScript, and consider upgrading to a
		    web browser that
		    <a href="http://videojs.com/html5-video-support/" target="_blank">
		      supports HTML5 video
		    </a>
		  </p>
		</video>
		
		<div class="clearfix"></div>
		<ul class="mui-table-view">
		    <li class="mui-table-view-cell mui-media">
		        <a href="http://m.xihaxueche.com:8001/html_h5/index.html" target="_blank">
		            <img class="mui-media-object mui-pull-right" style="margin:0px 10px;" src="<?php echo base_url('assets/images/share@2x.png'); ?>">
		            <img class="mui-media-object mui-pull-right" style="margin:0px 20px;" src="<?php echo base_url('assets/images/download@2x.png'); ?>">
		            <div class="mui-media-body" style="text-align: left;">
	                	{{info.video_title}}<span style="font-size: 14px; color: #999; padding-left: 5px;">{{info.video_desc}}</span>
		                <p class='mui-ellipsis'>
		                	<img style="width: 15px;" src="<?php echo base_url('assets/images/commit@2x.png'); ?>"/>&nbsp;{{info.count}}&nbsp;&nbsp;&nbsp;
		                	<img style="width: 20px;" src="<?php echo base_url('assets/images/seen@2x.png'); ?>"/>&nbsp;{{info.views}}
	                	</p>
		            </div>
		        </a>
		    </li>
	    </ul>
		<div class="xiha-content xiha-jiashizheng" style="">
			<p style="padding-left: 10px; padding-top: 10px; font-size: 16px; ">大家都在说（{{info.count}}）</p>
			<ul class="mui-table-view xiha-options" v-if="info.list">
				<li class="mui-table-view-cell mui-media" style="" v-for="(item, index) in info.list">
			        <a style="white-space: initial;">
			            <img class="mui-media-object mui-pull-left" style="border-radius: 50%; width: 80px; max-height: 80px;" :src="item.photo_url">
			            <div class="mui-media-body" style="text-align: left; line-height: 25px;">
			            	<span style="color: #999; font-size: 14px;" v-if="item.user_name">{{item.user_name}}</span>
			            	<span style="color: #999; font-size: 14px;" v-else>匿名</span>
		                	<div class='mui-ellipsis' style="color: #333; font-size: 16px; white-space: initial;">{{item.content}}</div>
			            </div>
			            <div class="mui-pull-right" style="position: absolute; right: 20px; top: 10px; color: #999;" >
							<span class="iconfont">&#xe67f;</span>&nbsp;<span style="font-size: 12px;">{{item.votes}}</span>
						</div>	 
			        </a>
			    </li>
			    <!--<li class="mui-table-view-cell mui-media" v-for="(item, index) in info.list">
			        <a href="javascript:;">
			            <img class="mui-media-object mui-pull-left" style="width: 100px;" src="<?php echo base_url('assets/images/demo_bg.png'); ?>">
			            <div class="mui-media-body" style="text-align: left;">
			            	{{item.user_name}}
			                <p class='mui-ellipsis' style="white-space: initial;">
			                	{{item.content}}
		                	</p>
							<div class="mui-pull-right" style="position: absolute; right: 10px; top: 10px; " >
								<span class="iconfont">&#xe67f;</span>{{item.votes}}
							</div>	                	
			            </div>
			        </a>
			    </li>-->
		    </ul>
		    <p v-else style="height: 50px; text-align: center; margin-top: 50px;">
		    	暂无评价，点击抢沙发<br /><br />
		    	<!--<button class="xiha-button" style="width: 150px;" @tap="submitComment()">快来点评吧</button>-->
	    	</p>
    	</div>
    	
    </div>
	<div id="login-form" class="login-form" :style="initLicenseStyle">
		<div class="mui-input-group">
		    <div class="mui-input-row">
		        <label><i class="iconfont">&#xe603;</i></label>
		        <input type="text" v-model="phone" class="mui-input-clear" @keyup.enter="submitLogin" placeholder="请输入手机号">
		    </div>
		    <div class="mui-input-row">
		        <label><i class="iconfont">&#xe617;</i></label>
		        <input type="password" v-model="password" class="mui-input-password" @keyup.enter="submitLogin" placeholder="请输入密码">
		    </div>
		</div>
    </div>
	<div style="position: fixed; width: 100%; height: 40px; background: #fff; bottom: 0px; left: 0px; box-shadow: #ccc 0px -1px 4px;">
		<div class="mui-input-row">
	        <label style="width: 13%;"><i class="iconfont" style="color: #999; font-size: 1.2rem;">&#xe690;</i></label>
    		<input type="text" v-model="commentContent" @keyup.enter="submitComment" class="mui-input" placeholder="写评论" style="width: 87%; background: #fff; border-radius: 0px; border-bottom: 0px; border-top-color: #eee;">
	    </div>
	</div>
    
</div>
<script src="<?php echo base_url('assets/js/video.min.js'); ?>" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    var vm = new Vue({
        el: '#app',
        data: {
        	initLicenseStyle: {
            	bottom: window.innerHeight+'px' 
            },
            seen: false,
           	title: "<?php echo $title; ?>", 
           	id: "<?php echo $id; ?>",
        	base_url: "<?php echo $this->config->item('api_url').'student/exam/videodetail'; ?>",
        	comment_url: "<?php echo $this->config->item('api_url').'student/exam/videocomments'; ?>",
        	login_url: "<?php echo $this->config->item('api_url').'student/ucenter/login'; ?>",
        	token: localStorage.getItem('token') ? localStorage.getItem('token') : '',
        	video_url: localStorage.getItem('video_url'),
        	pic_url: localStorage.getItem('pic_url'),
        	info: [],
        	commentContent: '',
        	phone: '',
        	password: '',
        },
        created: function() {
        	this.detailAjax();
        	this.video_url = localStorage.getItem('video_url') ? localStorage.getItem('video_url') : this.info.video_url;
        	this.pic_url = localStorage.getItem('pic_url') ? localStorage.getItem('pic_url') : this.info.pic_url;
        },
        methods: {
            detailAjax: function() {
            	mui.ajax(this.base_url, {
                     data:{
                         video_id: this.id
                     },
                     dataType:'json',//服务器返回json格式数据
                     type:'get',//HTTP请求类型
                     timeout:10000,//超时时间设置为10秒；
                     success:function(data){
                         if(data.code == 200) {
                         	if(data.data.list.length > 0) {
	                         	data.data.list.forEach(function(val, key) {
	                     			val.photo_url = "<?php echo base_url('assets/images/photo/'); ?>"+val.photo_id+".png";
	                         	});
                         	}
                             vm.info = data.data;
                         } else {
                             mui.toast(data.msg)
                         }
                     },
                     error:function(xhr,type,errorThrown){
                         mui.toast('网络错误，请检查网络');
                         return false;
                     }
             	});
            },
            submitComment: function() {
            	if(this.commentContent == '') {
            		mui.toast('请输入评价内容');
            		return false;
            	}
            	if(this.token == '') {
            		this.seen = true;
            		Velocity(document.getElementById("login-form"), {
						top: '44px',
					}, {
					    duration: 200
					});
            		return false;
            	}
            	mui.ajax(this.comment_url, {
                     data:{
                         token: this.token,
                         video_id: this.id,
                         content: this.commentContent,
                     },
                     dataType:'json',//服务器返回json格式数据
                     type:'post',//HTTP请求类型
                     timeout:10000,//超时时间设置为10秒；
                     success:function(data){
                 		 mui.toast(data.msg)
	                     if(data.code == 200) {
	                     	vm.commentContent = '';
                         }
                     },
                     error:function(xhr,type,errorThrown){
                         mui.toast('网络错误，请检查网络');
                         return false;
                     }
             	});
            },
            hideShade: function() {
            	this.seen = false;
            	Velocity(document.getElementById("login-form"), {
					top: '-100px',
				}, {
				    duration: 200
				});
            },
            submitLogin: function() {
            	if(this.phone == '' || this.password == '') {
            		mui.toast('请完善登录信息');
            		return false;
            	}
            	mui.ajax(this.login_url, {
                     data:{
                         phone: this.phone,
                         pass: this.password,
                     },
                     dataType:'json',//服务器返回json格式数据
                     type:'post',//HTTP请求类型
                     timeout:10000,//超时时间设置为10秒；
                     success:function(data){
                         mui.toast(data.msg)
                         if(data.code == 200) {
                         	vm.hideShade();
                         	localStorage.setItem('token', data.data.token);
                         	vm.token = data.data.token;
                         }
                     },
                     error:function(xhr,type,errorThrown){
                         mui.toast('网络错误，请检查网络');
                         return false;
                     }
             	});
            }
        }
    })
</script>
