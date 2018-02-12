<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">
    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="驾校名称" prop="school_id" v-if="school_id == 0" required>
                <el-select
                    v-model="ruleForm.school_id"
                    filterable
                    remote
                    clearable
                    placeholder="请输入驾校关键词如嘻哈"
                    :remote-method="remoteSchoolMethod"
                    :loading="loading">
                    <el-option
                    v-for="item in school_list"
                    :key="item.s_school_name"
                    :label="item.s_school_name"
                    :value="item.l_school_id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="报名点" prop="name" required>
                <el-col :span="11">
                    <el-input v-model="ruleForm.name" placeholder="请输入报名点" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="报名点电话" prop="phone" required>
                <el-col :span="11">
                    <el-input v-model="ruleForm.phone" placeholder="请输入报名点电话" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="排序" prop="order">
                <el-col :span="11">
                    <el-input v-model="ruleForm.order" placeholder="请输入排序,如:0" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="经纬度" required>
                <el-col :span="8">
                    <el-form-item prop="location_x">
                        <el-input v-model="ruleForm.location_x" placeholder="请输入经度"></el-input>
                    </el-form-item>
                </el-col>
                <el-col class="line" :span="2" style="text-align:center;">-</el-col>
                <el-col :span="8">
                    <el-form-item prop="location_y">
                        <el-input v-model="ruleForm.location_y" placeholder="请输入维度"></el-input>
                    </el-form-item>
                </el-col>
                <el-button type="primary" @click="showMap($event, 'http://api.map.baidu.com/lbsapi/getpoint/index.html')">获取经纬度</el-button>
            </el-form-item>
            <el-form-item :inline="true" label="报名点图片">
                <el-col :span="5">
                    <el-form-item prop="imgurl_one">
                        <el-upload
                            :action="upload_imgurl_one"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleOneSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.imgurl_one">
                            <img v-if="ruleForm.schooltrainone" :src="ruleForm.schooltrainone" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item prop="imgurl_two">
                        <el-upload
                            :action="upload_imgurl_two"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleTwoSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.imgurl_two">
                            <img v-if="ruleForm.schooltraintwo" :src="ruleForm.schooltraintwo" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item prop="imgurl_three">
                        <el-upload
                            :action="upload_imgurl_three"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleThreeSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.imgurl_three">
                            <img v-if="ruleForm.schooltrainthree" :src="ruleForm.schooltrainthree" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item prop="imgurl_four">
                        <el-upload
                            :action="upload_imgurl_four"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleFourSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.imgurl_four">
                            <img v-if="ruleForm.schooltrainfour" :src="ruleForm.schooltrainfour" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item prop="imgurl_five">
                        <el-upload
                            :action="upload_imgurl_five"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleFiveSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.imgurl_five">
                            <img v-if="ruleForm.schooltrainfive" :src="ruleForm.schooltrainfive" class="avatar">
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
            school_list: [],
            ruleForm: {
                school_id: school_id,
                name: '',
                phone: '',
                order: '50',
                location_x: '',
                location_y: '',
                imgurl_one: '',
                schooltrainone: '',
                imgurl_two: '',
                schooltraintwo: '',
                imgurl_three: '',
                schooltrainthree: '',
                imgurl_four: '',
                schooltrainfour: '',
                imgurl_five: '',
                schooltrainfive: '',
            },
            rules: {
                school_id: [
                    { required: true, message: '请选择驾校', trigger: 'blur' }
                ],
                name: [
                    { required: true, message: '请输入场地名称', trigger: 'blur' }
                ],
                phone: [
                    { required: true, message: '请输入报名点电话', trigger: 'blur' }
                ],
                location_x: [
                    { required: true, message: '请输入经度', trigger: 'blur' }
                ],
                location_y: [
                    { required: true, message: '请选择纬度', trigger: 'blur' }
                ],
                
            },
            add_url: "<?php echo base_url('school/addSignplaceAjax')?>",
            search_url: "<?php echo base_url('school/search')?>",
            upload_imgurl_one: "<?php echo base_url('upload/handle?type=schooltrainone') ?>",
            upload_imgurl_two: "<?php echo base_url('upload/handle?type=schooltraintwo') ?>",
            upload_imgurl_three: "<?php echo base_url('upload/handle?type=schooltrainthree') ?>",
            upload_imgurl_four: "<?php echo base_url('upload/handle?type=schooltrainfour') ?>",
            upload_imgurl_five: "<?php echo base_url('upload/handle?type=schooltrainfive') ?>",
            
        },
        created: function() {
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
            remoteSchoolMethod: function(query) {
                if (query !== '') {
                    this.loading = true;
                    $.ajax({
                        type: 'post',
                        url: this.search_url,
                        data: {key: query},
                        dataType: 'json',
                        success:function(data) {
                            vm.loading = false;
                            vm.school_list = data.data.list;
                        },
                        error: function() {
                            vm.messageNotice('warning', '搜索驾校出现网络错误');
                        }
                    });
                } 
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
            showMap: function(e, content){
                layer.open({
                    title: e.currentTarget.getAttribute('data-title')
                    ,type: 2
                    ,skin: 'layui-layer-rim' //加上边框
                    ,area: ['100%', '100%'] //宽高
                    ,content: content
                    ,yes: function(){
                        layer.closeAll();
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