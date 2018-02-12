<style type="text/css">
	.mui-table-view .mui-media-object {line-height: 25px; max-width: 25px; height: 25px;}
	.mui-content>.mui-table-view:first-child {margin-top: 0px;}
</style>
<div id="app" class="mui-fullscreen" v-cloak style="overflow: hidden;">
    <header class="mui-bar mui-bar-nav">
        <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
        <h1 class="mui-title">章节练习</h1>
    </header>
    <div class="mui-content" >
        <ul class="mui-table-view">
		    <li class="mui-table-view-cell mui-media" v-for="(item, index) in list" @tap="handleQues('home/question', 3)">
		        <a href="javascript:;">
		            <img class="mui-media-object mui-pull-left" src="<?php echo base_url('assets/images/xuhaobg@2x.png'); ?>">
	            	<div style="position: absolute; top: 12.5px; width: 25px; height: 25px; text-align: center; color: #fff;">{{index+1}}</div>
		            <div class="mui-media-body" style="text-align: left;">
	                	{{item.title}}
		                <p class='mui-ellipsis'>{{item.count}}</p>
		            </div>
		            <div class="mui-navigate-right"></div>
		        </a>
		    </li>
	    </ul>
	    
    </div>
	<div id="loading" style=""></div>
    
</div>
<script type="text/javascript">
 	var loading = document.getElementById('loading');
    var vm = new Vue({
        el: '#app',
        data: {
            base_url: "<?php echo $this->config->item('api_url').'student/exam/chapters'; ?>",
//          currentSubjectid: "<?php echo $subject_type; ?>",
//          currentLicenseid: "<?php echo $license_type; ?>",
            currentSubjectid: "kemu1",
            currentLicenseid: "car",
            list: [],
        },
        created: function() {
        	this.listAjax();
        	this.quesidAjax();
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
            quesidAjax: function() {
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
        }
    })
</script>
