<style>
	.mui-col-sm-2, .mui-col-xs-2 {width: 20%;}
	.mui-cell-active {color: #61C6C1; border-bottom: 1px solid #61C6C1;}
	.mui-row {background: #fff;}
	.xiha-content {margin-top: 10px; width: 100%; background: #fff; }
	.mui-table-view-cell:after {height: 0px;}
	.license-list {position: fixed; width: 100%; height: 100%; background: #F2F2F2; bottom: 0px; left: 0px; z-index: 30; overflow-y: scroll;}
	.submit-license {margin-top: 10px; height: 60px; width: 100%; background: #10C5BE; text-align: center; color: #fff; line-height: 60px; font-size: 16px;}
	.mui-col-sm-6, .mui-col-xs-6 {border: 0.5px solid #efeff4;}
	.mui-table-view .mui-media-object {line-height: 20px; max-width: 20px; height: 20px;}
	.mui-content>.mui-table-view:first-child {margin-top: 0px;}
	.xiha-no {font-size: 14px; position: absolute; top: 10px; width: 20px; height: 20px; text-align: center; color: #fff;}
</style>
<div id="app" class="mui-fullscreen" v-cloak style="overflow: hidden;">
	<header class="mui-bar mui-bar-nav">
	    <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
	    <h1 class="mui-title">专项练习</h1>
	    <!--<button class="mui-btn mui-btn-blue mui-btn-link mui-pull-right" @tap="showLicense()">{{currentLicensetitle}}</button>-->
	</header>
    <div class="mui-content" style="overflow: scroll; width: 100%; height: 100%;">
	    <!--content-->
	    <div class="xiha-content">
	    	<div class="mui-row">
	            <ul class="mui-table-view mui-row">
				    <li class="mui-table-view-cell mui-media mui-col-sm-6 mui-col-xs-6" v-for="(item, index) in handleList" @tap="handleQues('home/question', 4)">
				        <a href="javascript:;">
				            <img class="mui-media-object mui-pull-left" :src="item.tag_img">
			            	<div class="xiha-no" style="">{{index+1}}</div>
				            <div class="mui-media-body" style="text-align: left;">
			                	{{item.tag}}
				            </div>
				            <div class="mui-badge" style="background: none; color: #999;">{{item.count}}题</div>
				        </a>
				    </li>
				    
			    </ul>
		        
	    	</div>
	    </div>
    	<p style="text-align: center; margin-top: 20px;">
    		嘻哈学车版权所有
    	</p>
    </div>
    
	<!--选择牌照-->
	<div id="license-list" class="license-list" :style="initLicenseStyle">
		<div style="width: 100%; position: relative;">
			<div style="position: absolute; right:10px; top: 10px; color: #fff;" @tap="hideLicense(2)">关闭</div>
			<img style="width: 100%;" src="<?php echo base_url('assets/images/tikuleixingbeijing@2x.png'); ?>" alt="" />
			<div style="position: absolute; width: 100%; height: 50px; text-align: center; top: 100px; color: #fff; line-height: 30px;">
				{{current_time}}更新题库<br />
				共{{current_num}}题
			</div>
		</div>
		<div class="xiha-content">
			<p style="padding-left: 10px; padding-top: 10px; font-size: 16px; ">驾驶证</p>
			<div class="mui-row">
		        <div class="mui-col-sm-3 mui-col-xs-3" v-for="(item, index) in licenseList.driveList" @tap="chooseLicense(index, licenseList.driveList, 1)">
		            <li class="mui-table-view-cell">
		            	<template v-if="!item.isChoose">
		            		<img v-if="item.icon_type == 1" style="width: 40px; height: 20px;" src="<?php echo base_url('assets/images/tikuleixing/xiaoche@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 2" style="width: 40px; height: 20px;" src="<?php echo base_url('assets/images/tikuleixing/huoche@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 3" style="width: 40px; height: 20px;" src="<?php echo base_url('assets/images/tikuleixing/keche@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 4" style="width: 40px; height: 20px;" src="<?php echo base_url('assets/images/tikuleixing/motuoche@2x.png') ?>"/>
		            	</template>
		            	<template v-else>
		            		<img v-if="item.icon_type == 1" style="width: 40px; height: 20px;" src="<?php echo base_url('assets/images/tikuleixing/xiaoche-hover@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 2" style="width: 40px; height: 20px;" src="<?php echo base_url('assets/images/tikuleixing/huoche-hover@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 3" style="width: 40px; height: 20px;" src="<?php echo base_url('assets/images/tikuleixing/keche-hover@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 4" style="width: 40px; height: 20px;" src="<?php echo base_url('assets/images/tikuleixing/motuoche-hover@2x.png') ?>"/>
		            	</template>
	            		<p style="color: #333;">{{item.title}}</p>
	            		<p>{{item.license_info}}</p>
		            </li>
		        </div>
			</div>
		</div>
		<div class="xiha-content">
			<p style="padding-left: 10px; padding-top: 10px; font-size: 16px; ">资格证</p>
			<div class="mui-row">
		        <div class="mui-col-sm-4 mui-col-xs-4" v-for="(item, index) in licenseList.certList" @tap="chooseLicense(index, licenseList.certList, 2)">
		            <li class="mui-table-view-cell">
		            	<template v-if="!item.isChoose">
		            		<img v-if="item.icon_type == 1" style="width: 40px;" src="<?php echo base_url('assets/images/tikuleixing/wang@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 2" style="width: 40px;" src="<?php echo base_url('assets/images/tikuleixing/huo@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 3" style="width: 40px;" src="<?php echo base_url('assets/images/tikuleixing/wei@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 4" style="width: 40px;" src="<?php echo base_url('assets/images/tikuleixing/jiao@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 5" style="width: 40px;" src="<?php echo base_url('assets/images/tikuleixing/zu@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 6" style="width: 40px;" src="<?php echo base_url('assets/images/tikuleixing/ke@2x.png') ?>"/>
		            	</template>
		            	<template v-else>
		            		<img v-if="item.icon_type == 1" style="width: 40px;" src="<?php echo base_url('assets/images/tikuleixing/wang-hover@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 2" style="width: 40px;" src="<?php echo base_url('assets/images/tikuleixing/huo-hover@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 3" style="width: 40px;" src="<?php echo base_url('assets/images/tikuleixing/wei-hover@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 4" style="width: 40px;" src="<?php echo base_url('assets/images/tikuleixing/jiao-hover@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 5" style="width: 40px;" src="<?php echo base_url('assets/images/tikuleixing/zu-hover@2x.png') ?>"/>
		            		<img v-else-if="item.icon_type == 6" style="width: 40px;" src="<?php echo base_url('assets/images/tikuleixing/ke-hover@2x.png') ?>"/>
		            	</template>
	            		<p style="color: #333;">{{item.title}}</p>
		            </li>
		        </div>
		        
			</div>
		</div>
		<div class="submit-license" @tap="submitLicense()" style="">保 存</div>
	</div>
	<div id="loading" style=""></div>
</div>
<script type="text/javascript">
 	var loading = document.getElementById('loading');
    var vm = new Vue({
        el: '#app',
        data: {
        	lessonList: subject_list,
            initLicenseStyle: {
            	bottom: '-'+window.innerHeight+'px' 
            },
            licenseList: license_list,
            current_time: '',
            current_num: 0,
           	currentSubjectid: "<?php echo $subject_type; ?>",
            currentLicenseid: "<?php echo $license_type; ?>",
            currentLicensetitle: localStorage.getItem('licensename') ? localStorage.getItem('licensename') : '题库类型',
            current_temp_licentitle: localStorage.getItem('licensename') ? localStorage.getItem('licensename') : '',
            base_url: "<?php echo $this->config->item('api_url').'student/exam/special'; ?>",
            list: [],
        },
        created: function() {
        	this.listAjax();
        },
        computed: {
        	handleList: function() {
        		this.list.forEach(function(val, key) {
    				var img_url = "assets/images/buttonbg/"+Math.floor(Math.random()*(8-0+1))+"@2x.png";
        			val.tag_img = "<?php echo base_url('"+img_url+"'); ?>";
        		});
        		return this.list;
        	},
        },
        methods: {
        	listAjax: function() {
            	mui.ajax(this.base_url, {
                     data:{
                         car_type: this.currentLicenseid,
                         course: this.currentSubjectid,
                     },
                     dataType:'json',//服务器返回json格式数据
                     type:'get',//HTTP请求类型
                     timeout:10000,//超时时间设置为10秒；
                     beforeSend: function() {
                         var spinner = new Spinner(opts).spin(loading);
                     },
                     success:function(data){
                         if(data.code == 200) {
                             vm.list = data.data.list;
                         } else {
                             mui.toast(data.msg)
                         }
                         var loading = document.getElementById('loading');
                         loading.parentNode.removeChild(loading);
                     },
                     error:function(xhr,type,errorThrown){
                         var loading = document.getElementById('loading');
                         loading.parentNode.removeChild(loading);
                         mui.toast('网络错误，请检查网络');
                         return false;
                     }
             	});
            },
            handleQues: function(url, f) {
           		if(this.currentLicenseid == 0) {
           			this.showLicense();
           			return false;
           		}
           		location.href="<?php echo base_url('"+url+"'); ?>?st="+this.currentSubjectid+"&lt="+this.currentLicenseid+"&f="+f;
           },
           jump: function(url, id) {
           		if(this.currentLicenseid == 0) {
           			this.showLicense();
           			return false;
           		}
           		location.href="<?php echo base_url('"+url+"'); ?>?st="+id+"&lt="+this.currentLicenseid;
           },
           showLicense: function() {
           		Velocity(document.getElementById("license-list"), {
					bottom: '0px',
				}, {
				    duration: 200
				});
           },
           hideLicense: function(type) {
           		if(type == 2) {
//					this.current_temp_licentitle = '';
//					this.currentLicensetitle = '题库类型';
//					this.currentLicenseid = 0;
//         			localStorage.removeItem('licenseid')
           		}
           		Velocity(document.getElementById("license-list"), {
					bottom: '-'+window.innerHeight+'px',
				}, {
				    duration: 200
				});
           },
           chooseLicense: function(index, rows, type) {
//     			rows[index].isChoose = true;
				this.current_num = rows[index].num;
				this.currentLicenseid = rows[index].id;
				this.current_time = rows[index].update_time;
				this.current_temp_licentitle = rows[index].title;
				if(type == 1) {
					this.licenseList.certList.forEach(function(val, key) {
						val.isChoose = false
					})
				} else {
					this.licenseList.driveList.forEach(function(val, key) {
						val.isChoose = false
					})
				}
       			rows.forEach(function(val, key) {
       				if(key == index) {
       					val.isChoose = true;
       				} else {
       					val.isChoose = false;
       				}
       			});
           },
           submitLicense: function() {
           		if(this.currentLicenseid == 0) {
           			mui.toast('请选择题库类型');
           			return false;
           		}
           		this.currentLicensetitle = this.current_temp_licentitle;
           		localStorage.setItem('licenseid', this.currentLicenseid)
           		localStorage.setItem('licensename', this.currentLicensetitle)
           		this.hideLicense(1);	
           },
           
        }
    })
</script>
