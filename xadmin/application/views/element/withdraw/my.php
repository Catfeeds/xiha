<style>
    .box-card {
        margin: 3px;
    }
</style>
<div id="app" v-cloak>
    <div class="iframe-content">

        <!--  三栏式，个人中心，上部快捷搜索及结果概览 -->
        <el-row>

        <el-col :lg="4" :md="6" :sm="8" :xs="24">
        <el-card class="box-card">
        <div slot="header">
            <span style="line-height: 36px;">申请提现</span>
        </div>
        <div>提现请求总数{{ suminfo.all.num }}条。</div>
        <div>提现总金额为{{ suminfo.all.money }}元。</div>
        </el-card>
        </el-col>

        <el-col :lg="4" :md="6" :sm="8" :xs="24">
        <el-card class="box-card">
        <div slot="header">
            <span style="line-height: 36px;">提现已完成</span>
        </div>
        <div>提现已完成到账{{ suminfo.done.num }}笔。</div>
        <div>提现已完成到账金额{{ suminfo.done.money }}元。</div>
        </el-card>
        </el-col>

        <el-col :lg="4" :md="6" :sm="8" :xs="24">
        <el-card class="box-card">
        <div slot="header">
            <span style="line-height: 36px;">最新动态</span>
        </div>
        <div v-for="item in notify_list">{{ item.notice }}</div>
        </el-card>
        </el-col>

        </el-row>

        <!-- 表格展示列表，单行可以展开，显示审批处理进度，纵向或横向 -->
        <div>
            <!-- 刷新 -->
            <div class="gx-iframe-operation" style="margin: 5px 0; border-radius: 4px;">
                <el-button type="primary" type="small" style="float:left; line-height: 38px;" @click="handleRequest"> 申请提现 </el-button>
                <el-button type="success" type="small" style="float:right; line-height: 38px;" @click="handleRefresh"> 刷新本页 </el-button>
            </div>

            <!-- 表格 -->
            <el-table :data="list" border>
                <el-table-column type="expand">
                    <template scope="props">
                        <el-steps :space="100" direction="vertical" :active="props.row.process_active" finish-status="success">
                            <el-step v-for="item in props.row.process" :title="item.title" :description="item.description"></el-step>
                        </el-steps>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" label="时间"></el-table-column>
                <el-table-column prop="money" label="金额：元"></el-table-column>
                <el-table-column prop="process_active_description" label="进度"></el-table-column>
                <el-table-column prop="beizhu" label="备注"></el-table-column>
            </el-table>

            <!-- 分页 -->
            <div class="block" style="float: right; margin-top: 10px;">
                <el-pagination
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                :current-page="current_page"
                :page-sizes="page_sizes"
                :page-size="page_size"
                layout="total, sizes, prev, pager, next, jumper"
                :total="total">
                </el-pagination>
            </div>
        </div>
    </div>
</div>
<script>
    Vue.config.devtools = true;
    var vm = new Vue({
        el: '#app',
        data: {
            list: [],
            notify_list: [
                {'notice': '暂无消息'}
            ],
            suminfo:{
                'all': {
                    'num': 0,
                    'money': 0,
                },
                'done': {
                    'num': 0,
                    'money': 0,
                }
            },
            current_page: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            total: 0,
            list_url: "<?php echo base_url('withdraw/myAjax'); ?>",
            suminfo_url: "<?php echo base_url('withdraw/mySuminfoAjax'); ?>"
        },
        created: function() {
            this.suminfoAjax({});
            this.listAjax({'p': this.current_page, 's': this.page_size});
        },
        methods: {
            handleRequest: function() {
                window.location.href = "<?php echo base_url('withdraw/request'); ?>";
            },
            // 列表数据
            listAjax: function(param) {
                $.ajax({
                    type:'post',
                    url:this.list_url,
                    data: param,
                    dataType: 'json',
                    async: true,
                    success: function (response) {
                        isResponseOK = _.isObject(response) && _.has(response, 'code') && _.get(response, 'code') == 200
                        if (isResponseOK) {
                            vm.list = _.get(response, 'data.list');
                            vm.total = _.get(response, 'data.count');
                            vm.messageNotice('success', _.get(response, 'msg'));
                        } else {
                            vm.messageNotice('success', _.get(response, 'msg'));
                        }
                    },
                    error: function (e) {
                        vm.messageNotice('error', '加载数据出错');
                    }
                });
            },
            // 统计数据
            suminfoAjax: function(param) {
                $.ajax({
                    type:'post',
                    url:this.suminfo_url,
                    data: param,
                    dataType: 'json',
                    async: true,
                    success: function (response) {
                        isResponseOK = _.isObject(response) && _.has(response, 'code') && _.get(response, 'code') == 200
                        if (isResponseOK) {
                            vm.suminfo = _.get(response, 'data.suminfo');
                        } else {
                            vm.messageNotice('success', _.get(response, 'msg'));
                        }
                    },
                    error: function (e) {
                        vm.messageNotice('error', '加载数据出错');
                    }
                });
            },
            handleCurrentChange: function (current_page) {
                this.current_page = current_page;
                this.listAjax({'p': this.current_page, 's': this.page_size});
            },
            handleSizeChange: function (size) {
                this.page_size = size;
                this.listAjax({'p': this.current_page, 's': this.page_size});
            },
            handleRefresh: function() {
                this.suminfoAjax({});
                this.listAjax({'p': this.current_page, 's': this.page_size});
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
