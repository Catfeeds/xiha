<style>
	.mui-col-sm-2, .mui-col-xs-2 {width: 20%;}
	.mui-cell-active {color: #61C6C1; border-bottom: 1px solid #61C6C1;}
	.mui-row {background: #fff;}
	.xiha-content {margin-top: 10px; width: 100%; background: #fff; }
	.mui-table-view-cell:after {height: 0px;}
	.license-list {position: fixed; width: 100%; height: 100%; background: #F2F2F2; bottom: 0px; left: 0px; z-index: 30; overflow-y: scroll;}
	.submit-license {margin-top: 10px; height: 60px; width: 100%; background: #10C5BE; text-align: center; color: #fff; line-height: 60px; font-size: 16px;}
</style>
<div id="app" class="mui-fullscreen" v-cloak style="overflow: hidden;">
	<header class="mui-bar mui-bar-nav">
	    <h1 class="mui-title">驾考</h1>
	    <button class="mui-btn mui-btn-blue mui-btn-link mui-pull-right" @tap="showLicense()">{{currentLicensetitle}}</button>
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
	    <p style="text-align: center; margin-top: 44px; padding: 0px; height: 44px; line-height: 50px;">2017年最新题库 等你来战哦~.~</p>
	    <div class="xiha-content" style="margin-top: 0px;">
	    	<div class="mui-row">
		        <div class="mui-col-sm-6 mui-col-xs-6" @tap="redirect('article', 1)">
		            <li class="mui-table-view-cell">
	            		<img style="width: 155px;" src="<?php echo base_url('assets/images/kesikaogui@2x.png') ?>"/> 
		            </li>
		        </div>
		        <div class="mui-col-sm-6 mui-col-xs-6" @tap="redirect('article', 2)">
		            <li class="mui-table-view-cell">
	            		<img style="width: 155px;" src="<?php echo base_url('assets/images/datijiqiao@2x.png') ?>"/> 
		            </li>
		        </div>
	    	</div>
	    </div>
	    
	    <div class="xiha-content" style="padding: 10px 0px;">
	    	<div class="mui-row">
		        <div class="mui-col-sm-4 mui-col-xs-4">
		        	<ui>
			            <li class="mui-table-view-cell">
		            		<img style="height: 45px; width: 45px;" src="<?php echo base_url('assets/images/suijilianxi@2x.png') ?>"/> 
		            		<p>随机练习</p>
			            </li>
			            <li class="mui-table-view-cell">
		            		<img style="height: 45px; width: 45px;" src="<?php echo base_url('assets/images/cuotishoucang@2x.png') ?>"/> 
		            		<p>错题收藏</p>
			            </li>
		        	</ui>
		        </div>
		        <div class="mui-col-sm-4 mui-col-xs-4">
		            <li class="mui-table-view-cell" style="margin-top: 35px; padding: 0px;"  @click="handleQues('home/question', 1)">
	            		<img style="width: 100px; height: 100px;" src="<?php echo base_url('assets/images/shunxulianxi@2x.png') ?>"/> 
	            		<div style="position:absolute; top: 30px; left: 0px; text-align: center; width: 100%; height: 100px; color: #fff;">
	            			<p style="color: #fff;">顺序练习</p>
	            			<p style="color: #fff;">{{tiku_current_num}}/{{tiku_num}}</p>
            			</div>
		            </li>
		        </div>
		        <div class="mui-col-sm-4 mui-col-xs-4">
		            <ui>
			            <li class="mui-table-view-cell" @click="jump('home/special', currentSubjectid, 3)">
		            		<img style="height: 45px; width: 45px;" src="<?php echo base_url('assets/images/zhuanxianglianxi@2x.png') ?>"/> 
		            		<p>专项练习</p>
			            </li>
			            <li class="mui-table-view-cell" @click="jump('home/chapter', currentSubjectid, 3)">
		            		<img style="height: 45px; width: 45px;" src="<?php echo base_url('assets/images/zhangjielianxi@2x.png') ?>"/> 
		            		<p>章节练习</p>
			            </li>
		        	</ui>
		        </div>
	    	</div>
    	</div>
    	<div class="xiha-content" style="margin-top: 1px; padding: 10px 0px;">
	    	<div class="mui-row">
		        <div class="mui-col-sm-4 mui-col-xs-4">
		        	<ui>
			            <li class="mui-table-view-cell" @click="jump('home/rank', currentSubjectid, 3)">
		            		<img style="height: 45px; width: 45px;" src="<?php echo base_url('assets/images/chengjipaihang@2x.png') ?>"/> 
		            		<p>成绩排行</p>
			            </li>
			            <li class="mui-table-view-cell">
		            		<img style="height: 45px; width: 45px;" src="<?php echo base_url('assets/images/kaoshijilu@2x.png') ?>"/> 
		            		<p>考试记录</p>
			            </li>
		        	</ui>
		        </div>
		        <div class="mui-col-sm-4 mui-col-xs-4">
		            <li class="mui-table-view-cell" style="margin-top: 35px; padding: 0px;" @click="jump('home/exam', currentSubjectid, 3)">
	            		<img style="width: 100px; height: 100px;" src="<?php echo base_url('assets/images/monikaoshi@2x.png') ?>"/> 
	            		<div style="position:absolute; top: 38px; left: 0px; text-align: center; width: 100%; height: 100px; color: #fff;">
	            			<p style="color: #fff;">模拟考试</p>
            			</div>
		            </li>
		        </div>
		        <div class="mui-col-sm-4 mui-col-xs-4">
		            <ui>
			            <li class="mui-table-view-cell" @click="jump('home/sprint', currentSubjectid, 3)">
		            		<img style="height: 45px; width: 45px;" src="<?php echo base_url('assets/images/kaoqianchongci@2x.png') ?>"/> 
		            		<p>考前冲刺</p>
			            </li>
			            <li class="mui-table-view-cell">
		            		<img style="height: 45px; width: 45px;" src="<?php echo base_url('assets/images/yuyuekaoshi@2x.png') ?>"/> 
		            		<p>预约考试</p>
			            </li>
		        	</ui>
		        </div>
	    	</div>
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
			<div style="position: absolute; width: 100%; height: 50px; text-align: center; top: 100px; color: #34D7C6; line-height: 25px;">
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
		            		<img v-show="item.icon_type == 1" style="width: 40px; height: 25px;" src="<?php echo base_url('assets/images/tikushezhi/xiaoche@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 2" style="width: 40px; height: 25px;" src="<?php echo base_url('assets/images/tikushezhi/huoche@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 3" style="width: 40px; height: 25px;" src="<?php echo base_url('assets/images/tikushezhi/keche@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 4" style="width: 40px; height: 25px;" src="<?php echo base_url('assets/images/tikushezhi/motuoche@2x.png') ?>"/>
		            	</template>
		            	<template v-else>
		            		<img v-if="item.icon_type == 1" style="width: 40px; height: 25px;" src="<?php echo base_url('assets/images/tikushezhi/xiaoche-hover@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 2" style="width: 40px; height: 25px;" src="<?php echo base_url('assets/images/tikushezhi/huoche-hover@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 3" style="width: 40px; height: 25px;" src="<?php echo base_url('assets/images/tikushezhi/keche-hover@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 4" style="width: 40px; height: 25px;" src="<?php echo base_url('assets/images/tikushezhi/motuoche-hover@2x.png') ?>"/>
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
		            		<img v-if="item.icon_type == 1" style="width: 40px;" src="<?php echo base_url('assets/images/tikushezhi/wang@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 2" style="width: 40px;" src="<?php echo base_url('assets/images/tikushezhi/huo@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 3" style="width: 40px;" src="<?php echo base_url('assets/images/tikushezhi/wei@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 4" style="width: 40px;" src="<?php echo base_url('assets/images/tikushezhi/jiao@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 5" style="width: 40px;" src="<?php echo base_url('assets/images/tikushezhi/zu@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 6" style="width: 40px;" src="<?php echo base_url('assets/images/tikushezhi/ke@2x.png') ?>"/>
		            	</template>
		            	<template v-else>
		            		<img v-if="item.icon_type == 1" style="width: 40px;" src="<?php echo base_url('assets/images/tikushezhi/wang-hover@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 2" style="width: 40px;" src="<?php echo base_url('assets/images/tikushezhi/huo-hover@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 3" style="width: 40px;" src="<?php echo base_url('assets/images/tikushezhi/wei-hover@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 4" style="width: 40px;" src="<?php echo base_url('assets/images/tikushezhi/jiao-hover@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 5" style="width: 40px;" src="<?php echo base_url('assets/images/tikushezhi/zu-hover@2x.png') ?>"/>
		            		<img v-show="item.icon_type == 6" style="width: 40px;" src="<?php echo base_url('assets/images/tikushezhi/ke-hover@2x.png') ?>"/>
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
            currentLicenseid: localStorage.getItem('licenseid') ? localStorage.getItem('licenseid') : '',
            currentLicensetitle: localStorage.getItem('licensename') ? localStorage.getItem('licensename') : '题库类型',
            current_temp_licentitle: localStorage.getItem('licensename') ? localStorage.getItem('licensename') : '',
            tiku_url: "<?php echo $this->config->item('api_url').'student/exam/setting'; ?>",
            init_url: "<?php echo $this->config->item('api_url').'student/exam/questioncount'; ?>",
            tiku_list: [],
            tiku_current_num: 0,
            tiku_num: 0,
        },
        created: function() {
        	this.lessonList[3].isChoose = true;
    		this.initAjax();
        },
        methods: {
        	initAjax: function() {
        		if(this.currentLicenseid == '') {
        			mui.toast('请选择题库类型');
        			return false;
        		}
        		mui.ajax(this.init_url, {
                     data:{
                         car_type: this.currentLicenseid,
                     },
                     dataType:'json',//服务器返回json格式数据
                     type:'get',//HTTP请求类型
                     timeout:10000,//超时时间设置为10秒；
                     success:function(data){
                         if(data.code == 200) {
                             vm.tiku_num = data.data.kemu4_count;
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
           		if(this.currentLicenseid == '') {
           			this.showLicense();
           			return false;
           		} else {
           			this.lessonList[index].isChoose = true;
           			location.href="<?php echo base_url('"+url+"'); ?>?st="+st+"&lt="+this.currentLicenseid;
           		}
           },
           redirect: function(url, f) {
       			location.href="<?php echo base_url('"+url+"'); ?>?st="+this.currentSubjectid+"&lt="+this.currentLicenseid+"&f="+f;
           },
           handleQues: function(url, f) {
           		if(this.currentLicenseid == '') {
           			this.showLicense();
           			return false;
           		} else {
           			location.href="<?php echo base_url('"+url+"'); ?>?st="+this.currentSubjectid+"&lt="+this.currentLicenseid+"&f="+f;
           		}
           },
           showLicense: function() {
           		Velocity(document.getElementById("license-list"), {
					bottom: '0px',
				}, {
				    duration: 200
				});
				this.tikuAjax();
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
				this.currentLicenseid = rows[index].car_type;
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
           		if(this.currentLicenseid == '') {
           			mui.toast('请选择题库类型');
           			return false;
           		}
//         		if(this.current_num != 0) {
//         			this.tiku_num = this.current_num;
//         		}
       			this.tiku_num = this.current_num;
           		this.currentLicensetitle = this.current_temp_licentitle;
           		localStorage.setItem('licenseid', this.currentLicenseid)
           		localStorage.setItem('licensename', this.currentLicensetitle)
           		this.hideLicense(1);	
           },
           
        }
    })
</script>
