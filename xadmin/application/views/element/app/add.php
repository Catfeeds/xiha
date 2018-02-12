<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">
    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="app名称" prop="app_name" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.app_name" placeholder="请输入app名称" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="版本号" prop="version" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.version" placeholder="请输入app版本号，如：6.6"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="版本代号" prop="version_code" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.version_code" placeholder="请输入版本代号，如：66"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="最低限制" prop="force_least_updateversion">
                <el-col :span="10">
                    <el-input v-model="ruleForm.force_least_updateversion" rows="" placeholder="请输入版本最低限制，如：5.5"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="系统类型" prop="os_type" required>
                <el-select v-model="ruleForm.os_type" placeholder="请选择系统类型" >
                    <el-option label="android" value="1"></el-option>
                    <el-option label="ios" value="2"></el-option>
                    <el-option label="windows" value="3"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="客户端类型" prop="app_client" required>
                <el-select v-model="ruleForm.app_client" placeholder="请选择客户端类型" >
                    <el-option label="学员端" value="1"></el-option>
                    <el-option label="教练端" value="2"></el-option>
                    <el-option label="校长端" value="3"></el-option>
                    <el-option label="喵咪鼠标" value="4"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="强制升级？" prop="is_force">
                <el-switch
                    v-model="ruleForm.is_force"
                    on-text=""
                    off-text="">
                </el-switch>
            </el-form-item>
            <el-form-item label="上传app" prop="app_download_url">
                <el-upload
                    :action="upload_app"
                    class="upload-demo"
                    drag
                    accept
                    :on-success="handleAppSuccess"
                    :class="[ruleForm.app_download_url ? errorClass : '']"
                    v-model="ruleForm.app_download_url">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                    <div class="el-upload__tip" slot="tip">只能上传apk文件</div>
                </el-upload>
            </el-form-item> 
            <el-form-item label="更新日志" prop="app_update_log">
                <el-col :span="15">
                    <vue-html5-editor :content="ruleForm.app_update_log"  :height="500" :show-module-name="showModuleName" @change="updateData" >
                    </vue-html5-editor>
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
    Vue.use(VueHtml5Editor, {
        showModuleName: true,
        image: {
            sizeLimit: 512 * 1024,
            compress: true,
            width: 500,
            height: 500,
            quality: 80
        }
    });
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            showModuleName: false,
            errorClass: "error",
            ruleForm: {
                app_name: '',
                version: '',
                version_code: '',
                force_least_updateversion: 0,
                os_type: '',
                app_client: '',
                is_force: false,
                app_update_log: '',
                app_download_url: ''
            },
            rules: {
                app_name: [
                    { required: true, message: '请输入app名称', trigger: 'blur' }
                ],
                version: [
                    { required: true, message: '请输入版本号', trigger: 'blur' }
                ],
                version_code: [
                    { required: true, message: '请输入版本代号', trigger: 'blur' }
                ],
                os_type: [
                    { required: true, message: '请选择系统类型', trigger: 'change' }
                ],
                app_client: [
                    { required: true, message: '请选择客户端类型', trigger: 'change' }
                ],
            },
            add_url: "<?php echo base_url('app/addAjax')?>",
            upload_app: "<?php echo base_url('upload/handle?type=xihaApp') ?>",

        },
        methods: {
            handleAppSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.app_download_url = response.data.url;
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
            updateData: function (data) {
                this.ruleForm.app_update_log = data;
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