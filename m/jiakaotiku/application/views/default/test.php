<div id="app" class="mui-fullscreen" v-cloak style="overflow: hidden;">
    <!-- <div v-show="seen" @click="hideShade" class="hide-shade" style=""></div> -->

    <header class="mui-bar mui-bar-nav">
        <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
        <h1 class="mui-title">{{title}}</h1>
	    <button class="mui-btn mui-btn-blue mui-btn-link mui-pull-right">编辑</button>
    </header>
    <div class="mui-content" >
        
    </div>
    
</div>
<script type="text/javascript">
    var vm = new Vue({
        el: '#app',
        data: {
        	title: "列表demo",
            base_url: "<?php echo $this->config->item('api_url').'student/exam/test'; ?>",
        },
        methods: {
            
        }
    })
</script>
