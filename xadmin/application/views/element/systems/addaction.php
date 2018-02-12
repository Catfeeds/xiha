<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">
    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="唯一标识" prop="name" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.name" placeholder="请输入唯一标识，如：set_coingoods_deleted" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="行为说明" prop="title" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.title" placeholder="请输入行为说明，如：设置金币商城商品的上下架状态"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="是否开启" prop="status">
                <el-switch
                    v-model="ruleForm.status"
                    on-text=""
                    off-text="">
                </el-switch>
            </el-form-item>
            <el-form-item label="行为描述" prop="remark" required>
                <el-col :span="10">
                    <el-input type="textarea" :autosize="{ minRows: 5, maxRows: 10}" v-model="ruleForm.remark" placeholder="请输入行为描述，如：设置金币商城商品的上下架状态属性"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="行为规则" prop="rule">
                <el-col :span="10">
                    <el-input type="textarea" :autosize="{ minRows: 5, maxRows: 10}" v-model="ruleForm.rule" placeholder="请输入行为规则"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="日志规则" prop="log">
                <el-col :span="10">
                    <el-input type="textarea" :autosize="{ minRows: 5, maxRows: 10}" v-model="ruleForm.log" placeholder="请输入日志规则，如：[user|getOperator]在[time|time_format]设置了金币商城商品的上下架状态"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
                <el-button @click="resetForm('ruleForm')">重置</el-button>
            </el-form-item>
                
        </el-form>
    </div>
</div>

<script>
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            ruleForm: {
                name: '',
                title: '',
                remark: '',
                rule: '',
                status: false,
                log: "[user|getOperator]在[time|time_format][intro|specify_intro]",
            },
            rules: {
                name: [
                    { required: true, message: '请输入唯一标识', trigger: 'blur' }
                ],
                title: [
                    { required: true, message: '请输入行为说明', trigger: 'blur' }
                ],
                remark: [
                    { required: true, message: '请输入行为描述', trigger: 'blur' }
                ],
                log: [
                    { required: true, message: '请输入日志规则', trigger: 'blur' }
                ],
            },
            add_url: "<?php echo base_url('systems/addAjax')?>?type=action",

        },
        methods: {
            
            addAjax: function(params) {
                $.ajax({
                    type: 'post',
                    url: this.add_url,
                    data: params,
                    dataType: 'json',
                    success: function(data) {
                        is_ok = _.isObject(data) && _.has(data, 'code') && _.get(data, 'code') == 200
                        if (is_ok) {
                            parent.layer.closeAll();
                            parent.vm.messageNotice('success', _.get(data, 'msg'));
                            parent.vm.listAjax(parent.vm.currentPage);
                        } else {
                            parent.vm.messageNotice('success', _.get(data, 'msg'));
                        }
                    },
                    error: function(e) {
                        vm.messageNotice('error', '网络出错！');
                    }
                });
            },
            submitForm: function(formName) {
                this.$refs[formName].validate(function(valid) {
                    if (valid) {
                        vm.addAjax(vm.ruleForm);
                    } else {
                        return false;
                    }
                });
            },
            resetForm: function(formName) {
                this.$refs[formName].resetFields();
            },
            messageNotice: function(type, msg) {
                this.$message({
                    type: type,
                    message: msg
                })
            }

        }

    });
</script>