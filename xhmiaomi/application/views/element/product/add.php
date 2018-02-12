<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">
    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="名称" prop="client_name" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.client_name" placeholder="请输入客户端名称,如:喵咪鼠标" ></el-input>
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
            <!-- <el-form-item label="系统类型" prop="os_type" required>
                <el-select v-model="ruleForm.os_type" placeholder="请选择系统类型" >
                    <el-option label="windows" value="1"></el-option>
                    <el-option label="android" value="2"></el-option>
                    <el-option label="ios" value="3"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="客户端类型" prop="client_type" required>
                <el-select v-model="ruleForm.client_type" placeholder="请选择客户端类型" >
                    <el-option label="喵咪鼠标" value="1"></el-option>
                    <el-option label="学员端" value="2"></el-option>
                    <el-option label="教练端" value="3"></el-option>
                    <el-option label="校长端" value="4"></el-option>
                </el-select>
            </el-form-item> -->
            <el-form-item label="上传软件" prop="download_url">
                <el-upload
                    :action="upload_url"
                    class="upload-demo"
                    drag
                    accept
                    :on-success="handleAppSuccess"
                    :class="[ruleForm.download_url ? errorClass : '']"
                    v-model="ruleForm.download_url">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                    <div class="el-upload__tip" slot="tip">只能上传apk文件</div>
                </el-upload>
            </el-form-item>
            <el-form-item label="更新日志" prop="update_log">
                <el-col :span="15">
                    <vue-html5-editor :content="ruleForm.update_log"  :height="500" :show-module-name="showModuleName" @change="updateData" >
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
            errorClass: "error",
            labelPosition: 'right',
            showModuleName: false,
            ruleForm: {
                client_name: '',
                version: '',
                version_code: '',
                update_log: '',
                download_url: '',
            },
            rules: {
                client_name: [
                    { required: true, message: '请输入客户端名称', trigger: 'blur' }
                ],
                version: [
                    { required: true, message: '请输入版本号', trigger: 'blur' }
                ],
                version_code: [
                    { required: true, message: '请输入版本代号', trigger: 'blur' }
                ],
                update_log: [
                    { required: true, message: '请输入更新日志', trigger: 'blur' }
                ],
            },
            add_url: "<?php echo base_url('product/addAjax')?>",
            upload_url: "<?php echo base_url('upload/handle?type=miaomi') ?>",

        },
        created: function() {

        },
        methods: {
            handleAppSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.download_url = response.data.url;
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
                this.ruleForm.update_log = data;
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