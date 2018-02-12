<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">
    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <!--<el-form-item label="车型名称" prop="name">
                <el-col :span="10">
                    <el-input v-model="ruleForm.name" placeholder="请输入车型名称，如：大众新捷达" ></el-input>
                </el-col>
            </el-form-item>-->
            <el-form-item label="车品牌" prop="brand" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.brand" placeholder="请输入车品牌，如：大众"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="车型号" prop="subtype" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.subtype" placeholder="请输入车辆型号，如：新捷达"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="上传打点图" prop="point_text_url">
                <el-upload
                    :action="upload_point"
                    class="upload-demo"
                    drag
                    accept
                    :on-success="handleCarsSuccess"
                    :class="[ruleForm.point_text_url ? errorClass : '']"
                    v-model="ruleForm.point_text_url">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                    <div class="el-upload__tip" slot="tip">请上传文件</div>
                </el-upload>
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
            textarea: '',
            errorClass: 'error',
            ruleForm: {
                brand: '',
                subtype: '',
                point_text_url: ''
            },
            rules: {
                brand: [
                    { required: true, message: '请输入车辆品牌', trigger: 'blur' }
                ],
                subtype: [
                    { required: true, message: '请输入车辆型号', trigger: 'blur' }
                ],
            },
            add_url: "<?php echo base_url('cars/addAjax')?>?type=category",
            upload_point: "<?php echo base_url('upload/handle?type=cars') ?>",

        },
        methods: {
            handleCarsSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.point_text_url = response.data.url;
            },
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