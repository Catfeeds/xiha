<style>
	.mui-col-sm-2, .mui-col-xs-2 {width: 20%;}
	.mui-cell-active {color: #61C6C1; border-bottom: 1px solid #61C6C1;}
	.mui-row {background: #fff;}
	.xiha-content {margin-top: 10px; width: 100%; background: #fff; }
	.license-list {position: fixed; width: 100%; height: 100%; background: #F2F2F2; bottom: 0px; left: 0px; z-index: 30; overflow-y: scroll;}
	.submit-license {margin-top: 10px; height: 60px; width: 100%; background: #10C5BE; text-align: center; color: #fff; line-height: 60px; font-size: 16px;}
	.swiper-container {
	    width: 100%;
	    height: 150px;
	}
	.mui-table-view .mui-media-object {max-width: 100px; max-height: 70px; height: 70px;}
	.mui-table-view:before {background-color: #ddd;}
	.mui-table-view-cell:after {background-color: #ddd; height: 1px;}
	.mui-table-view:after {background-color: #ddd;}
	.xiha-jiashizheng .mui-ellipsis {margin: 5px 0px;}
	.xiha-jiashizheng .mui-table-view-cell {padding: 15px !important;}
	.xiha-jiashizheng .mui-table-view-cell>a:not(.mui-btn) {margin: -15px;}
</style>
<link rel="stylesheet" href="<?php echo base_url('assets/css/swiper.min.css'); ?>" />
<script src="<?php echo base_url('assets/js/swiper.min.js'); ?>"></script>

<div id="app" class="mui-fullscreen" v-cloak style="overflow: hidden;">
	<header class="mui-bar mui-bar-nav">
	    <h1 class="mui-title">驾考</h1>
	    <!--<button class="mui-btn mui-btn-blue mui-btn-link mui-pull-right" @tap="showLicense()">{{currentLicensetitle}}</button>-->
	</header>
    <div class="mui-row" style="position: fixed; background: #fff; left: 0px; top: 44px; width: 100%; z-index: 24;">
        <div class="mui-col-sm-2 mui-col-xs-2" v-for="(item, index) in lessonList" @tap="jump('home/index', item.course, index)">
        	<template v-if="item.isChoose">
	            <li class="mui-table-view-cell mui-cell-active">
            		{{item.title}}
	            </li>
        	</template>
        	<template v-else>
        		<li class="mui-table-view-cell">
            		{{item.title}}   
	            </li>
        	</template>
        </div>
    </div>
    <div class="mui-content" style="padding-top: 0px; overflow: scroll; width: 100%; height: 100%;">
	    <!--content-->
	    <p style="text-align: center; margin-top: 34px; padding: 0px; height: 44px; line-height: 50px;">2017年最新题库 等你来战哦~.~</p>
	    
	    <div class="xiha-content" style="">
	    	<div class="swiper-container">
			    <div class="swiper-wrapper">
			        <div class="swiper-slide"><img style="width: 100%;" src="<?php echo base_url('assets/images/banner/21e27879f81691816816990af192a6706e4582d8.jpg'); ?>"/></div>
			        <div class="swiper-slide"><img style="width: 100%;" src="<?php echo base_url('assets/images/banner/ce99e74a83291c96f2d9057e23fc30d586736e32.png'); ?>"/></div>
			        <div class="swiper-slide"><img style="width: 100%;" src="<?php echo base_url('assets/images/banner/ef725bd024a47b4e8cce8dabfd50046036f2a673.jpg'); ?>"/></div>
			    </div>
			    <!-- 如果需要分页器 -->
			    <div class="swiper-pagination"></div>
			</div>
	    </div>
	    <div class="xiha-content" style="margin-top: 0px;">
	    	<div class="mui-row">
		        <div class="mui-col-sm-6 mui-col-xs-6" @tap="articleRedirect('article', 3)">
		            <li class="mui-table-view-cell" style="padding-bottom: 5px;">
	            		<img style="width: 155px;" src="<?php echo base_url('assets/images/xuechejiqiao@2x.png') ?>"/> 
		            </li>
		        </div>
		        <div class="mui-col-sm-6 mui-col-xs-6" @tap="articleRedirect('article', 4)">
		            <li class="mui-table-view-cell" style="padding-bottom: 5px;">
	            		<img style="width: 155px;" src="<?php echo base_url('assets/images/keermiji@2x.png') ?>"/> 
		            </li>
		        </div>
		        <div class="mui-col-sm-6 mui-col-xs-6">
		            <li class="mui-table-view-cell">
	            		<a href="http://m.xihaxueche.com:8001/html_h5/index.html" target="_blank"><img style="width: 155px;" src="<?php echo base_url('assets/images/xuechebaogao@2x.png') ?>"/> </a>
		            </li>
		        </div>
		        <div class="mui-col-sm-6 mui-col-xs-6">
		            <li class="mui-table-view-cell">
	            		<a href="http://m.xihaxueche.com:8001/html_h5/index.html" target="_blank"><img style="width: 155px;" src="<?php echo base_url('assets/images/dianzijiaolian@2x.png') ?>"/> </a>
		            </li>
		        </div>
	    	</div>
	    </div>
	    
	    <div class="xiha-content xiha-jiashizheng" style="">
			<p style="padding-left: 10px; padding-top: 10px; font-size: 16px; ">驾驶证</p>
	    	<ul class="mui-table-view" v-if="video_list.length > 0">
			    <li class="mui-table-view-cell mui-media" v-for="(item, index) in video_list">
			        <a href="javascript:;" @click="redirect('article/detail', 7, item.id, item.video_url, item.pic_url)">
			            <img class="mui-media-object mui-pull-left" style="width: 100px;" :src="item.pic_url">
			            <div class="mui-media-body" style="text-align: left;">
		                	{{item.title}}
			                <p class='mui-ellipsis'>{{item.skill_intro}}</p>
			                <p class='mui-ellipsis'>
			                	<!--<img style="width: 15px;" src="<?php echo base_url('assets/images/commit@2x.png'); ?>"/>&nbsp;0&nbsp;&nbsp;&nbsp;-->
			                	<img style="width: 20px;" src="<?php echo base_url('assets/images/seen@2x.png'); ?>"/>&nbsp;{{item.views}}
			                	
		                	</p>
			            </div>
			            <div class="mui-navigate-right"></div>
			        </a>
			    </li>
			    
		    </ul>
		    <p v-else style="height: 50px; text-align: center;">暂无列表</p>
		    
    	</div>
    	<p style="text-align: center; margin-top: 20px;">
    		嘻哈学车版权所有
    	</p>
    </div>
    
	<!--选择牌照-->
	<div id="license-list" class="license-list" :style="initLicenseStyle">
		<div style="width: 100%; position: relative;">
			<div style="position: absolute; right:10px; top: 10px; color: #333;" @tap="hideLicense(2)">关闭</div>
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
    
</div>
<script type="text/javascript">
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
           	currentSubjectid: "<?php echo $subject_type;?>",
            currentLicenseid: localStorage.getItem('licenseid') ? localStorage.getItem('licenseid') : 'car',
            currentLicensetitle: localStorage.getItem('licensename') ? localStorage.getItem('licensename') : '题库类型',
            current_temp_licentitle: localStorage.getItem('licensename') ? localStorage.getItem('licensename') : '',
            tiku_url: "<?php echo $this->config->item('api_url').'student/exam/setting'; ?>",
            base_url: "<?php echo $this->config->item('api_url').'student/exam/video'; ?>",
            tiku_list: [],
            video_list: [],
        },
     	created: function() {
         	this.lessonList[1].isChoose = true;
         	this.videoAjax();
        },
        methods: {
        	videoAjax: function() {
        		mui.ajax(this.base_url, {
                     data:{
                         car_type: this.currentLicenseid,
                         course: this.currentSubjectid,
                     },
                     dataType:'json',//服务器返回json格式数据
                     type:'get',//HTTP请求类型
                     timeout:10000,//超时时间设置为10秒；
                     success:function(data){
                         if(data.code == 200) {
                             vm.video_list = data.data.list;
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
        	tikuAjax: function() {
        		mui.ajax(this.tiku_url, {
                     data:{
                         course: this.currentSubjectid,
                     },
                     dataType:'json',//服务器返回json格式数据
                     type:'get',//HTTP请求类型
                     timeout:10000,//超时时间设置为10秒；
                     success:function(data){
                         if(data.code == 200) {
                             vm.tiku_list = data.data;
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
           	jump: function(url, st, index) {
           		if(this.currentLicenseid == 0) {
           			this.showLicense();
           			return false;
           		}
           		this.lessonList[index].isChoose = true;
           		location.href="<?php echo base_url('"+url+"'); ?>?st="+st+"&lt="+this.currentLicenseid;
           },
           redirect: function(url, f, id, vurl, purl) {
       			location.href="<?php echo base_url('"+url+"'); ?>?st="+this.currentSubjectid+"&lt="+this.currentLicenseid+"&id="+id+"&f="+f;
       			localStorage.setItem('video_url', vurl);
       			localStorage.setItem('pic_url', purl);
           },
           articleRedirect: function(url, f) {
       			location.href="<?php echo base_url('"+url+"'); ?>?st="+this.currentSubjectid+"&lt="+this.currentLicenseid+"&f="+f;
           },
           showLicense: function() {
           		this.tikuAjax();
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
       					vm.tiku_list.forEach(function(v, k) {
							if(val.car_type == v.car_type) {
								vm.current_time = v.updatetime;
								vm.current_num = v.count;
							}   						
       					});
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
    });
 	var mySwiper = new Swiper ('.swiper-container', {
	    loop: true,
	    // 如果需要分页器
	    pagination: '.swiper-pagination',
  	})  
</script>
