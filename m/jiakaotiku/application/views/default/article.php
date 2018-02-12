<style type="text/css">
	.mui-table-view-cell>a:not(.mui-btn) {white-space: initial;}
</style>
<div id="app" class="mui-fullscreen" v-cloak style="overflow: scroll;">
    <!-- <div v-show="seen" @click="hideShade" class="hide-shade" style=""></div> -->
    <header class="mui-bar mui-bar-nav">
        <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
        <h1 class="mui-title">{{title}}</h1>
	    <!--<button class="mui-btn mui-btn-blue mui-btn-link mui-pull-right">编辑</button>-->
    </header>
    <div class="mui-content">
        <ul class="mui-table-view">
		    <li class="mui-table-view-cell mui-media" v-for="(item, index) in article_list">
		        <a href="javascript:;" @tap="redirect('article/detail', item.id, 1)">
		            <img v-if="item.article_thumb != ''" class="mui-media-object mui-pull-left" style="width: 100px; max-width: 100px; height: 80px;" :src="item.article_thumb">
		            <div class="mui-media-body" style="text-align: left;">
	                	{{item.title}}
		                <p class='mui-ellipsis'>{{item.skill_intro}}</p>
		                <p class='mui-ellipsis' style="margin-top: 10px;">
		                	<img style="width: 20px;" src="<?php echo base_url('assets/images/seen@2x.png'); ?>"/>&nbsp;{{item.views}}&nbsp;&nbsp;&nbsp;
		                	<img style="width: 15px;" src="<?php echo base_url('assets/images/vote@2x.png'); ?>"/>&nbsp;{{item.votes}}
	                	</p>
		            </div>
		            <div class="mui-navigate-right"></div>
		        </a>
		    </li>
		    
	    </ul>
	    <div v-if="page < totalPage" style="background: #fff; height: 48px; width: 100%; text-align: center; line-height: 48px;" @tap="nextPage()">
	    	加载更多
	    </div>
    </div>
    
</div>
<script type="text/javascript">
    var vm = new Vue({
        el: '#app',
        data: {
            title: "<?php echo $title; ?>",
            totalPage: 1,
            article_list: [],
            base_url: "<?php echo $this->config->item('apiv1_url').'category'; ?>",
            page: 1,
           	currentSubjectid: "<?php echo $subject_type;?>",
            currentLicenseid: "<?php echo $license_type;?>",
            cate_id: "<?php echo $cate_id;?>",
        },
        created: function() {
        	this.listAjax();
        },
        methods: {
          	listAjax: function() {
      			mui.ajax(this.base_url, {
                     data:{
                         cate_id: this.cate_id,
                         page: this.page,
                         type: 1,
                         device: 1,
                     },
                     dataType:'json',//服务器返回json格式数据
                     type:'get',//HTTP请求类型
                     timeout:10000,//超时时间设置为10秒；
                     success:function(data){
                         if(data.code == 200) {
                         	data.data.article_list.forEach(function(val, key) {
                         		vm.article_list.push(val);
                         	});
                             vm.totalPage = data.data.last_page;
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
          	redirect: function(url, id, f) {
       			location.href="<?php echo base_url('"+url+"'); ?>?st="+this.currentSubjectid+"&lt="+this.currentLicenseid+"&id="+id+"&f="+f;
          	},
          	nextPage: function() {
          		this.page++;
          		this.listAjax();
          	}
        }
    })
</script>
