<div id="app" v-cloak>
    <div class="iframe-content">
        <div>
            <!-- 刷新 -->
            <div class="gx-iframe-operation" style="margin: 5px 0; border-radius: 4px;">
                <el-select v-model="filters.process_status" placeholder="选择提现进度">
                    <el-option v-for="item in process_status_options" :label="item.label" :value="item.value">
                    </el-option>
                </el-select>
                <el-button type="success" type="small" style="float:right; line-height: 38px;" @click="handleRefresh"> 刷新本页 </el-button>
            </div>

            <el-card>
                <!-- 表格 -->
                <el-table :data="list" border>
                    <el-table-column prop="created_at" label="创建时间"></el-table-column>
                    <el-table-column prop="money" label="金额 (单位 元)"></el-table-column>
                    <el-table-column prop="name" label="提现人"></el-table-column>
                    <el-table-column label="操作" width="180">
                        <template scope="scope">
                            <el-button size="small" type="text" :disabled="scope.row.created == 1">创建</el-button>
                            <el-button size="small" type="text" :disabled="scope.row.reviewed == 1">审核</el-button>
                            <el-button size="small" type="text" :disabled="scope.row.transferred == 1">打款</el-button>
                            <el-button size="small" type="text" :disabled="scope.row.completed == 1">完成</el-button>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- 分页 -->
                <div class="block" style="float: right; margin: 5px;">
                    <el-pagination
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                    :current-page="current_page"
                    :page-sizes="page_sizes"
                    :page-size="page_size"
                    layout="total, sizes, prev, pager, next, jumper"
                    :total="total">
                    </el-pagination>
                <div>
            </el-card>
        </div>
    </div>
</div>
<script>
    Vue.config.devtools = true;
    var vm = new Vue({
        el: '#app',
        data: {
            process_status_options: [
                { 'label': '选择进度', 'value': ''},
                { 'label': '待审核', 'value': 'created'},
                { 'label': '待打款', 'value': 'reviewed'},
                { 'label': '待完成', 'value': 'transferred'},
                { 'label': '已完成', 'value': 'completed'}
            ],
            filters: {
                process_status:'',
            },
            list_url: "<?php echo base_url('withdraw/adminAjax'); ?>",
            current_page:1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            total:0,
            list: [
                {
                    'created_at':'06-28 18:20',
                    'money': 100,
                    'name': '张先生'
                }
            ]
        },
        created: function() {
            console.log('created');
            this.listAjax({});
        },
        methods: {
            getFilterParams: function() {
                return {
                    'p': this.current_page,
                    's': this.page_size,
                    'process_status': this.filters.process_status
                };
            },
            handleCurrentChange: function (current_page) {
                this.current_page = current_page;
                this.listAjax(this.getFilterParams());
            },
            handleSizeChange: function (size) {
                this.page_size = size;
                this.listAjax(this.getFilterParams());
            },
            handleRefresh: function() {
                //this.suminfoAjax({});
                this.listAjax(this.getFilterParams());
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
            // 展示消息
            messageNotice: function(type, msg) {
                this.$message({
                    type: type,
                    message: msg
                });
            },
            //打开图层
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
