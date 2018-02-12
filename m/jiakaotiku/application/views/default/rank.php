<style type="text/css">
	/*.xiha-content {background: #fff; min-width: 300px; margin: 20px; border-radius: 20px; height: 130px; position: absolute; top: 130px; left: 50%; margin-left: -150px; }*/
	.mui-bar-nav {border: none;}
	.xiha-content {background: #fff; width: 100%; margin: 0px auto; border-radius: 20px; padding-bottom: 15px; position: relative; margin-top: 40px;  }
	.mui-table-view {background: none;}
	.mui-table-view:after, .mui-table-view:before {background: none;}
	.mui-table-view-cell:after {background-color: #ddd;}
	.xiha-button {background: linear-gradient(135deg,#17C6BF,#6CDBD5); height: 44px; border: none; border-radius: 30px; color: #fff; width: 100%; margin: 0px auto;}
	.xiha-button:active {background: linear-gradient(135deg,#6CDBD5,#17C6BF);}
	.mui-table-view-cell>a:not(.mui-btn).mui-active {background: none;}
</style>
<div class="mui-fullscreen" style="background-color: #6BEDD5;">
    <header class="mui-bar mui-bar-nav">
        <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
        <h1 class="mui-title">排行榜</h1>
    </header>
	<div id="app" class="mui-fullscreen" v-cloak style="overflow: scroll; background: url('<?php echo base_url('assets/images/paihangbang-bg.png'); ?>') no-repeat; background-size: 100%; background-position: 0px 44px;">
	    <div class="mui-content" style="background: none; position: relative; padding: 20px;">
	    	<p style="font-size: 24px; font-weight: 500; color: #fff; text-align: center; margin-top: 70px;">全国本周TOP100学霸榜</p>
	        <div class="xiha-content xiha-rank-no1">
	        	<ul class="mui-table-view">
				    <li class="mui-table-view-cell mui-media" v-if="token">
				        <a href="javascript:;">
				            <img class="mui-media-object mui-pull-left" style="border-radius: 50%;" src="<?php echo base_url('assets/images/demo_bg.png'); ?>">
				            <div class="mui-media-body" style="text-align: left; line-height: 24px;">
			                	嘻哈车友
				                <p class='mui-ellipsis'>本周最佳成绩：{{info.max_score}}分</p>
				            </div>
				        </a>
				    </li>
				    <li class="mui-table-view-cell mui-media" v-else>
				        <a href="javascript:;">
			    			<button class="xiha-button" style="background: linear-gradient(135deg,#0094F5,#01D6E8);">登录</button>
				        </a>
				    </li>
			    </ul>
			    <div style="width: 100%; padding: 0px 20px;">
			    	<button class="xiha-button" style="">考前冲刺</button>
			    </div>
	        </div>
	        
	        <!--排行列表-->
	        <div class="xiha-content" style="margin-top: 20px; padding: 0px;">
	        	<ul class="mui-table-view">
				    <li class="mui-table-view-cell mui-media">
				        <a href="javascript:;">
				            <img class="mui-media-object mui-pull-left" style="width: 30px; height: 37px; line-height: 35px; padding-top: 6px;" src="<?php echo base_url('assets/images/paihangbang-no1@2x.png'); ?>">
				            <img class="mui-media-object mui-pull-left" style="border-radius: 50%;" src="<?php echo base_url('assets/images/demo_bg.png'); ?>">
				            <div class="mui-media-body" style="text-align: left; line-height: 42px;">
			                	嘻哈车友
				            </div>
				            <div class="mui-badge" style="background: none; color: #333;">
				            	<span style="color: #E64625; font-size: 16px; font-weight: bold;">100</span>分&nbsp;&nbsp;&nbsp;
				            	<span style="color: #E64625; font-size: 16px; font-weight: bold;">3</span>分
				            	<span style="color: #E64625; font-size: 16px; font-weight: bold;">34</span>秒
			            	</div>
				        </a>
				    </li>
				    <li class="mui-table-view-cell mui-media">
				        <a href="javascript:;">
				            <img class="mui-media-object mui-pull-left" style="width: 30px; height: 37px; line-height: 35px; padding-top: 6px;" src="<?php echo base_url('assets/images/paihangbang-no2@2x.png'); ?>">
				            <img class="mui-media-object mui-pull-left" style="border-radius: 50%;" src="<?php echo base_url('assets/images/demo_bg.png'); ?>">
				            <div class="mui-media-body" style="text-align: left; line-height: 42px;">
			                	嘻哈车友
				            </div>
				            <div class="mui-badge" style="background: none; color: #333;">
				            	<span style="color: #FF801F; font-size: 16px; font-weight: bold;">100</span>分&nbsp;&nbsp;&nbsp;
				            	<span style="color: #FF801F; font-size: 16px; font-weight: bold;">3</span>分
				            	<span style="color: #FF801F; font-size: 16px; font-weight: bold;">56</span>秒
			            	</div>
				        </a>
				    </li>
				    <li class="mui-table-view-cell mui-media">
				        <a href="javascript:;">
				            <img class="mui-media-object mui-pull-left" style="width: 30px; height: 37px; line-height: 35px; padding-top: 6px;" src="<?php echo base_url('assets/images/paihangbang-no3@2x.png'); ?>">
				            <img class="mui-media-object mui-pull-left" style="border-radius: 50%;" src="<?php echo base_url('assets/images/demo_bg.png'); ?>">
				            <div class="mui-media-body" style="text-align: left; line-height: 42px;">
			                	嘻哈车友
				            </div>
				            <div class="mui-badge" style="background: none; color: #333;">
				            	<span style="color: #FFB41F; font-size: 16px; font-weight: bold;">100</span>分&nbsp;&nbsp;&nbsp;
				            	<span style="color: #FFB41F; font-size: 16px; font-weight: bold;">3</span>分
				            	<span style="color: #FFB41F; font-size: 16px; font-weight: bold;">34</span>秒
			            	</div>
				        </a>
				    </li>
				    <li class="mui-table-view-cell mui-media">
				        <a href="javascript:;">
			            	<div class="mui-media-object mui-pull-left" style="width: 30px; height: 37px; text-align: center; color: #999;">4</div>
				            <img class="mui-media-object mui-pull-left" style="border-radius: 50%;" src="<?php echo base_url('assets/images/demo_bg.png'); ?>">
				            <div class="mui-media-body" style="text-align: left; line-height: 42px;">
			                	嘻哈车友
				            </div>
				            <div class="mui-badge" style="background: none; color: #666;">
				            	<span style="font-size: 16px; font-weight: bold;">100</span>分&nbsp;&nbsp;&nbsp;
				            	<span style="font-size: 16px; font-weight: bold;">3</span>分
				            	<span style="font-size: 16px; font-weight: bold;">34</span>秒
			            	</div>
				        </a>
				    </li>
				    <li class="mui-table-view-cell mui-media">
				        <a href="javascript:;">
			            	<div class="mui-media-object mui-pull-left" style="width: 30px; height: 37px; text-align: center; color: #999;">5</div>
				            <img class="mui-media-object mui-pull-left" style="border-radius: 50%;" src="<?php echo base_url('assets/images/demo_bg.png'); ?>">
				            <div class="mui-media-body" style="text-align: left; line-height: 42px;">
			                	嘻哈车友
				            </div>
				            <div class="mui-badge" style="background: none; color: #666;">
				            	<span style="font-size: 16px; font-weight: bold;">100</span>分&nbsp;&nbsp;&nbsp;
				            	<span style="font-size: 16px; font-weight: bold;">3</span>分
				            	<span style="font-size: 16px; font-weight: bold;">34</span>秒
			            	</div>
				        </a>
				    </li>
				    <li class="mui-table-view-cell mui-media">
				        <a href="javascript:;">
			            	<div class="mui-media-object mui-pull-left" style="width: 30px; height: 37px; text-align: center; color: #999;">6</div>
				            <img class="mui-media-object mui-pull-left" style="border-radius: 50%;" src="<?php echo base_url('assets/images/demo_bg.png'); ?>">
				            <div class="mui-media-body" style="text-align: left; line-height: 42px;">
			                	嘻哈车友
				            </div>
				            <div class="mui-badge" style="background: none; color: #666;">
				            	<span style="font-size: 16px; font-weight: bold;">100</span>分&nbsp;&nbsp;&nbsp;
				            	<span style="font-size: 16px; font-weight: bold;">3</span>分
				            	<span style="font-size: 16px; font-weight: bold;">34</span>秒
			            	</div>
				        </a>
				    </li>
				    <li class="mui-table-view-cell mui-media">
				        <a href="javascript:;">
			            	<div class="mui-media-object mui-pull-left" style="width: 30px; height: 37px; text-align: center; color: #999;">7</div>
				            <img class="mui-media-object mui-pull-left" style="border-radius: 50%;" src="<?php echo base_url('assets/images/demo_bg.png'); ?>">
				            <div class="mui-media-body" style="text-align: left; line-height: 42px;">
			                	嘻哈车友
				            </div>
				            <div class="mui-badge" style="background: none; color: #666;">
				            	<span style="font-size: 16px; font-weight: bold;">100</span>分&nbsp;&nbsp;&nbsp;
				            	<span style="font-size: 16px; font-weight: bold;">3</span>分
				            	<span style="font-size: 16px; font-weight: bold;">34</span>秒
			            	</div>
				        </a>
				    </li>
			    </ul>
	        </div>
	    </div>
	    
	</div>
</div>
<script type="text/javascript">
    var vm = new Vue({
        el: '#app',
        data: {
            base_url: "<?php echo $this->config->item('api_url').'student/exam/ranking'; ?>",
           	currentSubjectid: "<?php echo $subject_type;?>",
           	currentLicenseid: "<?php echo $license_type;?>",
           	info: [],
           	token: localStorage.getItem('token') ? localStorage.getItem('token') : '',
        },
        created: function() {
        	this.listAjax();
        },
        methods: {
	  		listAjax: function() {
	  			mui.ajax(this.base_url, {
                     data:{
                     	course: this.currentSubjectid,
                     	car_type: this.currentLicenseid,
                     },
                     dataType: 'json',//服务器返回json格式数据
                     type: 'get',//HTTP请求类型
                     timeout: 10000,//超时时间设置为10秒；
                     async: false,
                     success:function(data){
                     	vm.info = data.data;
                     },
                     error:function(xhr,type,errorThrown){
                         mui.toast('网络错误，请检查网络');
                         return false;
                     }
             	});
	  		},
	  		
        }
    })
</script>
