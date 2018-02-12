<style type="text/css">
	.xiha-content {text-align: center; width: 100%; padding-top: 25px;}
	.xiha-content-01 {text-align: left; width: 300px; margin: 0px auto; padding: 20px;}
	.xiha-button-01 {background: linear-gradient(135deg,#0094F5,#01D6E8); height: 48px; border: none; border-radius: 30px; color: #fff; width: 100%; margin: 0px auto; font-weight: bold; font-size: 16px;}
	.xiha-button-01:active {background: linear-gradient(135deg,#01D6E8,#0094F5);}
	.xiha-button-02 {background: linear-gradient(135deg,#05BFBA,#6DEED5); height: 48px; border: none; border-radius: 30px; color: #fff; width: 100%; margin: 0px auto; font-weight: bold; font-size: 16px;}
	.xiha-button-02:active {background: linear-gradient(135deg,#6DEED5,#05BFBA);}
</style>
<header class="mui-bar mui-bar-nav">
    <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
    <h1 class="mui-title">模拟考试</h1>
</header>
<div id="app" class="mui-fullscreen" v-cloak style="overflow: hidden; background: #fff;">
    <div class="mui-content" style="background: #fff; margin-top: 44px;">
        <div class="xiha-content">
        	<img style="width: 80px; height: 80px; border-radius: 50%;" src="<?php echo base_url('assets/images/demo_bg.png'); ?>"/>
        	<p style="font-size: 16px; color: #333;">嘻哈学车用户1</p>
        	<p>主人，赶紧开始吧</p>
        	
        	<div class="xiha-content-01">
        		<p style="font-size: 16px; color: #333;">考试类型： <span>全国通用A2/B2</span></p>
        		<p style="font-size: 16px; color: #333;">试题数量： <span>100题</span></p>
        		<p style="font-size: 16px; color: #333;">考试时间： <span>30分钟</span></p>
        		<p style="font-size: 16px; color: #333;">合格标准： <span>满分100分，合格90分</span></p>
        		<p style="font-size: 16px; color: #333;">出题规则： <span>根据交管局出题规则</span></p>
        	</div>
        </div>
        
        <div style="width: 100%; padding: 0px 30px;">
	    	<button class="xiha-button-01" style="">全真模拟</button>
	    </div>
	    <div style="width: 100%; padding: 20px 30px;">
	    	<button class="xiha-button-02" style="">优先考未做题</button>
	    </div>
    </div>
    
</div>
<script type="text/javascript">
    var vm = new Vue({
        el: '#app',
        data: {
            
        },
        methods: {
            
        }
    })
</script>
