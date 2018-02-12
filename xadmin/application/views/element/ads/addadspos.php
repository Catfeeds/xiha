<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">
    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="标题" prop="title" required>
                <el-col :span="7">
                    <el-input v-model="ruleForm.title" placeholder="请输入标题，如：学员端app启动图片" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="场景" prop="scene" required>
                <el-col :span="7">
                    <el-input v-model="ruleForm.scene" placeholder="请输入场景，如：101"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="描述" prop="description">
                <el-col :span="10">
                <el-input
                    type="textarea"
                    :autosize="{ minRows: 5, maxRows: 10}"
                    placeholder="请输入内容，如：该广告场景是嘻哈学车学员端app启动页面"
                    v-model="ruleForm.description">
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
                title: '',
                description: '',
                scene: ''
            },
            rules: {
                title: [
                    { required: true, message: '请输入标题', trigger: 'blur' }
                ],
                description: [
                    { required: true, message: '请输入描述', trigger: 'blur' }
                ],
                scene: [
                    { required: true, message: '请输入场景', trigger: 'blur' }
                ],
            },
            add_url: "<?php echo base_url('ads/addAjax')?>?type=position",

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