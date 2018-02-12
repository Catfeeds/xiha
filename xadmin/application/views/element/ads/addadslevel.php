<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">
    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="等级ID" prop="level_id" required>
                <el-col :span="7">
                    <el-input v-model="ruleForm.level_id" placeholder="请输入等级ID，如：1" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="展示时间" prop="loop_time" required>
                <el-col :span="7">
                    <el-input v-model="ruleForm.loop_time" placeholder="请输入展示时间，如：5" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="等级名称" prop="level_title" required>
                <el-col :span="7">
                    <el-input v-model="ruleForm.level_title" placeholder="请输入等级名称，如：Vip" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="所需钱数" prop="level_money" required>
                <el-col :span="7">
                    <el-input v-model="ruleForm.level_money" placeholder="请输入等级所需的钱，如：100" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="等级介绍" prop="level_intro" required>
                <el-col :span="10">
                <el-input
                    type="textarea"
                    :autosize="{ minRows: 5, maxRows: 10}"
                    placeholder="请输入等级介绍，如：一级广告"
                    v-model="ruleForm.level_intro">
                    </el-input>
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
                level_id: '1',
                level_title: '',
                level_intro: '',
                loop_time: '5',
                level_money: '100'
            },
            rules: {
                level_id: [
                    { required: true, message: '请输入广告等级ID', trigger: 'blur' }
                ],
                level_title: [
                    { required: true, message: '请输入广告等级标题', trigger: 'blur' }
                ],
                level_intro: [
                    { required: true, message: '请输入广告介绍', trigger: 'blur' }
                ],
                level_money: [
                    { required: true, message: '请输入广告等级所需钱数', trigger: 'blur' }
                ],
                loop_time: [
                    { required: true, message: '请输入广告展示的时间', trigger: 'blur' }
                ]
            },
            add_url: "<?php echo base_url('ads/addAjax')?>?type=level",

        },
        created: function() {

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
                        } else if (_.get(data, 'code') == 102) {
                            parent.layer.closeAll();
                            parent.vm.messageNotice('warning', _.get(data, 'msg'));
                            parent.vm.listAjax(parent.vm.currentPage);
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