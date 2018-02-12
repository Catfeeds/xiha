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
            <el-form-item label="场地名称" prop="name" required>
                <el-col :span="11">
                    <el-input v-model="ruleForm.name" placeholder="请输入场地名称" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="开放否" prop="open_status">
                <el-switch
                    v-model="ruleForm.open_status"
                    on-text=""
                    off-text="">
                </el-switch>
            </el-form-item>
            <el-form-item label="打点文件1" prop="point_url_one">
                 <el-upload
                    :action="upload_point_one"
                    drag
                    accept
                    :on-success="handlePuoSuccess"
                    :class="[ruleForm.point_url_one ? errorClass : '']"
                    v-model="ruleForm.point_url_one">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                    <div class="el-upload__tip" slot="tip">请上传txt文件</div>
                </el-upload> 
            </el-form-item>
            <el-form-item label="打点文件2" prop="point_url_two">
                 <el-upload
                    :action="upload_point_two"
                    drag
                    accept
                    :on-success="handlePutSuccess"
                    :class="[ruleForm.point_url_two ? errorClass : '']"
                    v-model="ruleForm.point_url_two">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                    <div class="el-upload__tip" slot="tip">请上传txt文件</div>
                </el-upload> 
            </el-form-item>
            <el-form-item label="3D资源" prop="resource_url">
                 <el-upload
                    :action="upload_resource"
                    drag
                    accept
                    :on-success="handleResourceSuccess"
                    :class="[ruleForm.resource_url ? errorClass : '']"
                    v-model="ruleForm.resource_url">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                    <div class="el-upload__tip" slot="tip">请上传rar|zip文件</div>
                </el-upload> 
            </el-form-item>
            <!-- <el-form-item label="场地风采图" prop="site_imgurl">
                 <el-upload
                    :action="upload_site_imgurl"
                    drag
                    accept
                    :on-success="handleImgurlSuccess"
                    :class="[ruleForm.site_imgurl ? errorClass : '']"
                    v-model="ruleForm.site_imgurl">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                    <div class="el-upload__tip" slot="tip">请上传图片</div>
                </el-upload> 
            </el-form-item> -->
            <el-form-item label="场地风采图" prop="site_imgurl">
                <el-upload
                    :action="upload_site_imgurl"
                    list-type="picture-card"
                    :show-file-list="false"
                    :on-success="handleImgurlSuccess"
                    :class="[ruleForm.isNull ? errorClass : '']" 
                    v-model="ruleForm.site_imgurl">
                    <img v-if="ruleForm.siteimgurl" :src="ruleForm.siteimgurl" class="avatar">
                    <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                </el-upload>
            </el-form-item>
            <el-form-item :inline="true" label="地址" required>
                <el-col :span="5">
                    <el-form-item prop="province_id">
                        <el-select v-model="ruleForm.province_id" @change="handleProvince" placeholder="选择省份">
                        <el-option v-for="item in place.province_list" :label="item.province" :value="item.provinceid"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col class="line" :span="1" style="text-align:center;">-</el-col>
                <el-col :span="5">
                    <el-form-item prop="city_id">
                        <el-select v-model="ruleForm.city_id" @change="handleCity" placeholder="选择城市">
                        <el-option v-for="item in place.city_list" :label="item.city" :value="item.cityid"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col class="line" :span="1" style="text-align:center;">-</el-col>
                <el-col :span="5">
                    <el-form-item prop="area_id">
                        <el-select v-model="ruleForm.area_id" placeholder="选择区域">
                        <el-option v-for="item in place.area_list" :label="item.area" :value="item.areaid"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
            </el-form-item>
            <el-form-item label="详细地址" prop="s_address" required>
                <el-col :span="18">
                    <el-input v-model="ruleForm.s_address" placeholder="请输入街道信息"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="场地描述" prop="site_desc">
                <el-col :span="18">
                    <vue-html5-editor :content="ruleForm.site_desc"  :height="300" :show-module-name="showModuleName" @change="updateData" >
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
    var school_id = "<?php echo $school_id; ?>";
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            textarea: '',
            errorClass: 'error',
            showModuleName: false,
            loading: false,
            dialogFormVisible: false,
            school_list: [],
            place: {
                province_list: [],
                city_list: [],
                area_list: []
            },
            ruleForm: {
                school_id: school_id,
                name: '',
                open_status: false,
                province_id: '',
                city_id: '',
                area_id: '',
                s_address: '',
                point_url_one: '',
                point_url_two: '',
                resource_url: '',
                site_imgurl: '',
                siteimgurl: '',
                site_desc: '',
            },
            rules: {
                school_id: [
                    { required: true, message: '请选择驾校', trigger: 'blur' }
                ],
                name: [
                    { required: true, message: '请输入场地名称', trigger: 'blur' }
                ],
                province_id: [
                    { required: true, message: '请选择省份', trigger: 'blur' }
                ],
                city_id: [
                    { required: true, message: '请选择城市', trigger: 'blur' }
                ],
                area_id: [
                    { required: true, message: '请选择区域', trigger: 'blur' }
                ],
                s_address: [
                    { required: true, message: '请输入详细地址', trigger: 'blur' }
                ],
                
            },
            add_url: "<?php echo base_url('school/addSiteAjax')?>",
            search_url: "<?php echo base_url('school/search')?>",
            province_url: "<?php echo base_url('school/provinceajax'); ?>",
            city_url: "<?php echo base_url('school/cityajax'); ?>",
            area_url: "<?php echo base_url('school/areaajax'); ?>",
            upload_point_one: "<?php echo base_url('upload/handle?type=sitePointOne') ?>",
            upload_point_two: "<?php echo base_url('upload/handle?type=sitePointTwo') ?>",
            upload_resource: "<?php echo base_url('upload/handle?type=siteResoure') ?>",
            upload_site_imgurl: "<?php echo base_url('upload/handle?type=siteimgurl') ?>",
        },
        created: function() {
            this.provinceAjax();
        },
        methods: {
            handlePuoSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.point_url_one = response.data.url;
            },
            handlePutSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.point_url_two = response.data.url;
            },
            handleResourceSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.resource_url = response.data.url;
            },
            handleImgurlSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.site_imgurl = response.data.url;
            },
            updateData: function (data) {
                this.ruleForm.site_desc = data;
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
            handleProvince: function() {
                this.cityAjax(this.ruleForm.province_id);
                this.ruleForm.city_id = '';
            },
            handleCity: function() {
                this.areaAjax(this.ruleForm.city_id);
                this.ruleForm.area_id = '';                
            },
            provinceAjax: function() {
                $.ajax({
                    type: 'post',
                    url: this.province_url,
                    dataType: 'json',
                    success: function(data) {
                        if(data.code == 200) {
                            vm.place.province_list = data.data.list;
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '网络错误，请检查网络');
                    }
                })
            },
            cityAjax: function(pid) {
                $.ajax({
                    type: 'post',
                    url: this.city_url,
                    dataType: 'json',
                    data: {pid: pid},
                    success: function(data) {
                        if(data.code == 200) {
                            vm.place.city_list = data.data.list;
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '网络错误，请检查网络');
                    }
                })
            },
            areaAjax: function(cid) {
                $.ajax({
                    type: 'post',
                    url: this.area_url,
                    dataType: 'json',
                    data: {cid: cid},
                    success: function(data) {
                        if(data.code == 200) {
                            vm.place.area_list = data.data.list;
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '网络错误，请检查网络');
                    }
                })
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