<style>
    .withdraw-sub-menu {
        width: 600px;
        margin: 0 auto;
    }
    .box-card {
        margin: 10px;
    }
    .clearfix:before,
    .clearfix:after {
        display: table;
        content: "";
    }
    .clearfix:after {
        clear: both;
    }
</style>
<div id="app" v-cloak>
    <div class="iframe-content">
        <div class="withdraw-sub-menu">
            <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span style="line-height: 36px;">提现个人中心</span>
                <el-button style="float: right;" type="primary" data-title="提现个人中心" @click="enterWithdrawCenter">点击进入</el-button>
            </div>
            <div class="box-content">
                提现的个人中心，都有哪些内容呢？
            </div>
            </el-card>

            <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span style="line-height: 36px;">提现审核</span>
                <el-button style="float: right;" type="primary" data-title="提现审核" @click="enterWithdrawAdmin">点击进入</el-button>
            </div>
            <div class="box-content">
                审核的介绍性的文字，稍后添加。
            </div>
            </el-card>
        </div>
    </div>
</div>
<script>
    Vue.config.devtools = true;
    var vm = new Vue({
        el: '#app',
        data: {
            withdraw_center_url: "<?php echo base_url('withdraw/my'); ?>",
            withdraw_admin_url: "<?php echo base_url('withdraw/admin'); ?>"
        },
        created: function() {
        },
        methods: {
            enterWithdrawCenter: function(e) {
                window.location.href = this.withdraw_center_url;
            },
            enterWithdrawAdmin: function(e) {
                window.location.href = this.withdraw_admin_url;
            },
            messageNotice: function(type, msg) {
                this.$message({
                    type: type,
                    message: msg
                });
            },
            showLayer: function(e, width, offset, content) {
                layer.closeAll();
                layer.open({
                    title: e.currentTarget.getAttribute('data-title')
                    ,offset: offset //具体配置参考：offset参数项
                    ,anim: -1
                    ,type: 2
                    ,area: [width ,'100%']
                    ,content: content
                    ,shade: 0.4 //不显示遮罩
                    ,shadeClose: false //不显示遮罩
                    ,maxmin: true
                    ,move: false
                    ,yes: function(){
                        layer.closeAll();
                    }
                });
            },
        }
    });
</script>
