<style type="text/css">
	img {width: 100%;}
</style>
<div id="app" class="mui-fullscreen" v-cloak style="overflow: scroll;">
    <header class="mui-bar mui-bar-nav">
        <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
        <h1 class="mui-title">{{info.title}}</h1>
    </header>
    <div class="mui-content" >
        <p style="background: #fff; font-size: 16px; color: #333; padding: 10px; height: 80px;">
        	<span style="font-weight: bold;">
        		{{info.title}}
        	</span>
        	<br />
        	<a style="float:right; color: #333;">
        		<img style="width: 20px;" src="<?php echo base_url('assets/images/seen@2x.png'); ?>"/>&nbsp;{{info.views}}&nbsp;&nbsp;&nbsp;
        		<img style="width: 15px;" src="<?php echo base_url('assets/images/vote@2x.png'); ?>"/>&nbsp;{{info.votes}}
        	</a>
        </p>
 		<p style="background: #fff; padding: 11px; font-size: 16px; color: #000; line-height: 28px;" v-html="info.message"></p>
    </div>
    
</div>
<script type="text/javascript">
    var vm = new Vue({
        el: '#app',
        data: {
        	title: "文章详情",
            base_url: "<?php echo $this->config->item('apiv1_url').'article/detail'; ?>",
            info: [],
            id: "<?php echo $id; ?>",
            
        },
        created: function() {
        	this.detailAjax();
        },
        methods: {
            detailAjax: function() {
				mui.ajax(this.base_url, {
                     data:{
                         id: this.id,
                         device: 1,
                     },
                     dataType:'json',//服务器返回json格式数据
                     type:'get',//HTTP请求类型
                     timeout:10000,//超时时间设置为10秒；
                     success:function(data){
                         if(data.code == 200) {
                             vm.info = data.data;
                         } else {
                             mui.toast(data.msg);
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
