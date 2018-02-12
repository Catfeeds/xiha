<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">
    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="驾校名称" prop="school_id" v-if="school_id == 0" required>
                <el-col :span="8">
                    <el-input v-model="ruleForm.school_name" placeholder="请输入驾校" :disabled="true"></el-input>
                </el-col>
            </el-form-item>
            <!-- <el-form-item :inline="true" label="轮播图">
                <el-upload
                    class="upload-demo"
                    action="upload_imgurl"
                    :on-preview="handlePreview"
                    :on-remove="handleRemove"
                    :file-list="fileList2"
                    list-type="picture">
                    <el-button size="small" type="primary">点击上传</el-button>
                    <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
                </el-upload>
            </el-form-item> -->
            <el-form-item :inline="true" label="轮播图">
                <el-col :span="6">
                    <el-form-item prop="imgurl_one">
                        <el-upload
                            :action="upload_imgurl_one"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleOneSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.imgurl_one">
                            <img v-if="ruleForm.schoolBannerOne" :src="ruleForm.schoolBannerOne" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item prop="imgurl_two">
                        <el-upload
                            :action="upload_imgurl_two"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleTwoSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.imgurl_two">
                            <img v-if="ruleForm.schoolBannerTwo" :src="ruleForm.schoolBannerTwo" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item prop="imgurl_three">
                        <el-upload
                            :action="upload_imgurl_three"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleThreeSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.imgurl_three">
                            <img v-if="ruleForm.schoolBannerThree" :src="ruleForm.schoolBannerThree" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item prop="imgurl_four">
                        <el-upload
                            :action="upload_imgurl_four"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleFourSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.imgurl_four">
                            <img v-if="ruleForm.schoolBannerFour" :src="ruleForm.schoolBannerFour" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <el-col :span="6">
                    <el-form-item prop="imgurl_five">
                        <el-upload
                            :action="upload_imgurl_five"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleFiveSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.imgurl_five">
                            <img v-if="ruleForm.schoolBannerFive" :src="ruleForm.schoolBannerFive" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
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
    var school_id = "<?php echo $school_id; ?>";
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            textarea: '',
            loading: false,
            errorClass: 'error',
            ruleForm: {
                id: "<?php echo $l_school_id;?>",
                school_name: "<?php echo $school_name;?>",
                imgurl_one: "<?php echo $imgurl_one;?>",
                schoolBannerOne: "<?php echo $http_imgurl_one;?>",
                imgurl_two: "<?php echo $imgurl_two;?>",
                schoolBannerTwo: "<?php echo $http_imgurl_two;?>",
                imgurl_three: "<?php echo $imgurl_three;?>",
                schoolBannerThree: "<?php echo $http_imgurl_three;?>",
                imgurl_four: "<?php echo $imgurl_four;?>",
                schoolBannerFour: "<?php echo $http_imgurl_four;?>",
                imgurl_five: "<?php echo $imgurl_five;?>",
                schoolBannerFive: "<?php echo $http_imgurl_five;?>",
            },
            rules: {},
            edit_url: "<?php echo base_url('school/editBannerAjax')?>",
            search_url: "<?php echo base_url('school/search')?>",
            upload_imgurl_one: "<?php echo base_url('upload/handle?type=schoolBannerOne') ?>",
            upload_imgurl_two: "<?php echo base_url('upload/handle?type=schoolBannerTwo') ?>",
            upload_imgurl_three: "<?php echo base_url('upload/handle?type=schoolBannerThree') ?>",
            upload_imgurl_four: "<?php echo base_url('upload/handle?type=schoolBannerFour') ?>",
            upload_imgurl_five: "<?php echo base_url('upload/handle?type=schoolBannerFive') ?>",
        },
        methods: {
            handleOneSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.imgurl_one = response.data.url;
            },
            handleTwoSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.imgurl_two = response.data.url;
            },
            handleThreeSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.imgurl_three = response.data.url;
            },
            handleFourSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.imgurl_four = response.data.url;
            },
            handleFiveSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.imgurl_five = response.data.url;
            },
            handleOneSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.imgurl_one = response.data.url;
            },
            editAjax: function(params) {
                $.ajax({
                    type: 'post',
                    url: this.edit_url,
                    data: params,
                    dataType: 'json',
                    success: function(data) {
                        is_ok = _.isObject(data) && _.has(data, 'code') && _.get(data, 'code') == 200
                        if (is_ok) {
                            parent.layer.closeAll();
                            parent.vm.messageNotice('success', _.get(data, 'msg'));
                            parent.vm.bannerlist(parent.vm.currentPage);
                        } else {
                            parent.vm.messageNotice('success', _.get(data, 'msg'));
                        }
                    },
                    error: function(e) {
                        vm.messageNotice('warning', '网络出错！');
                    }
                });
            },
            submitForm: function(formName) {
                this.$refs[formName].validate(function(valid) {
                    if (valid) {
                        vm.editAjax(vm.ruleForm);
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