<div id="app" v-cloak>
    <div class="iframe-content">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <!-- <el-tabs v-model="activeName" type="card" @tab-click="handleClick"> -->
                <!-- <el-tab-pane label="添加学员" name="first"> -->
                    <el-form-item prop="school_id" v-if="school_id == ''" label="驾校名称" prop="school_id">
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
                    <el-form-item label="真实姓名" prop="s_real_name" required>
                        <el-col :span="8">
                            <el-input v-model="ruleForm.s_real_name" placeholder="请填写真实姓名"></el-input>
                        </el-col>
                    </el-form-item>
                    <el-form-item label="手机号码" prop="s_phone" required>
                        <el-col :span="8">
                            <el-input v-model="ruleForm.s_phone" placeholder="请填写手机号码" v-on:blur="checkPhone()"></el-input>
                        </el-col>
                    </el-form-item>
                    <el-form-item label="身份证" prop="identity_id" required>
                        <el-col :span="8">
                            <el-input v-model="ruleForm.identity_id" placeholder="请填写身份证" v-on:blur="checkIdentity()"></el-input>
                        </el-col>
                    </el-form-item>
                    <el-form-item label="学员年龄" prop="age" required>
                        <template>
                            <el-input-number v-model="ruleForm.age" :step="1"  :min="18" :max="70"></el-input-number>
                        </template>
                    </el-form-item>
                    <el-form-item label="学员头像" prop="user_photo" >
                        <el-upload
                            :action="upload_user_photo_url"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleThumbSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']"
                            v-model="ruleForm.user_photo">
                            <img v-if="ruleForm.userthumb" :src="ruleForm.userthumb" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                    <el-form-item label="性别" prop="sex" >
					  	<el-radio class="radio" v-model="ruleForm.sex" label="1" value="1">男</el-radio>
  						<el-radio class="radio" v-model="ruleForm.sex" label="2" value="2">女</el-radio>
				  	</el-form-item>
                    <el-form-item label="领证次数" prop="license_num" required>
                        <template>
                            <el-input-number v-model="ruleForm.license_num" :step="1"  :min="0"></el-input-number>
                        </template>
                        <!-- <el-button type="primary" value="-" @click="decNum" class="btn btn-warning">-</el-button>
                        <el-input v-model="ruleForm.license_num" placeholder="请填写领证次数"></el-input>
                        <el-button type="primary" value="+" @click="incNum" class="btn btn-warning">+</el-button> -->
                    </el-form-item>
                    <el-form-item label="牌照" prop="license_id" required>
                        <el-select v-model="ruleForm.license_id" clearable placeholder="请选择牌照">
                            <el-option v-for="item in license_options" :label="item.license_name" :value="item.license_id"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="科目" prop="lesson_id" required>
                        <el-select v-model="ruleForm.lesson_id" clearable placeholder="请选择科目">
                            <el-option v-for="item in lesson_options" :label="item.lesson_name" :value="item.lesson_id"></el-option>
                        </el-select>
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
                    <el-form-item label="详细地址" prop="address" required>
                        <el-col :span="8">
                            <el-input v-model="ruleForm.address" placeholder="请填写详细地址"></el-input>
                        </el-col>
                    </el-form-item>
                   
                <!-- </el-tab-pane> -->
            <!-- </el-tabs> -->
            <el-form-item>
                <el-button type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
                <el-button @click="resetForm('ruleForm')">重置</el-button>
            </el-form-item>
        </el-form>
    </div>
</div>

<script>
	Vue.config.devtools = true;
    var school_id = "<?php echo $school_id?>";
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            textarea: '',
            loading: false,
            license_options: '',
            lesson_options: '',
            province_options: '',
            city_options: '',
            area_options: '',
            school_list: [],
            ruleForm: {
                school_id: school_id,
                s_real_name: '',
                s_phone: '',
                age: '18',
                identity_id: '',
                license_num: '0',
                license_id: '',
                lesson_id: '',
                province_id: '',
                city_id: '',
                area_id: '',
                address: '',
                sex: '1',
                user_photo: '',
                userthumb: '',
                
            },
            place: {
                province_list: [],
                city_list: [],
                area_list: []
            },
            rules: {
                s_real_name: [
                    { required: true, message: '请填写真实姓名'}
                ],
                s_phone: [
                    { required: true, message: '请填写手机号码'}
                ],
                age: [
                    { required: true, message: '请填写年龄'}
                ],
                identity_id: [
                    { required: true, message: '请填写身份证'}
                ],
                license_id: [
                    { required: false, message: '请选择牌照'}
                ],
                lesson_id: [
                    { required: true, message: '请选择科目'}
                ],
                province_id: [
                    { required: true, message: '请选择省份'}
                ],
                city_id: [
                    { required: true, message: '请选择城市'}
                ],
                area_id: [
                    { required: true, message: '请选择区域'}
                ],
                address: [
                    { required: true, message: '请填写详细地址'}
                ],
                sex: [
                    { required: true, message: '请选择性别'}
                ]
            },
            dialogFormVisible: false,
            formLabelWidth: '120px',
            content: '',
            showModuleName: false,
            base_url: "<?php echo base_url('student/addajax')?>",
            ssearch_url: "<?php echo base_url('school/search')?>",
            upload_user_photo_url: "<?php echo base_url('upload/handle?type=userthumb') ?>",
            license_url: "<?php echo base_url('student/getLicenseInfo')?>",
            lesson_url: "<?php echo base_url('student/getLessonInfo')?>",
            province_url: "<?php echo base_url('school/provinceajax'); ?>",
            city_url: "<?php echo base_url('school/cityajax'); ?>",
            area_url: "<?php echo base_url('school/areaajax'); ?>",
            checkIdentity_url: "<?php echo base_url('student/checkIdentity')?>",
            checkPhone_url: "<?php echo base_url('student/checkPhone')?>",
            activeName: 'first',
            school_list: [],
            users_list: [],
        },
        created: function() {
            this.provinceAjax();
        },
        methods: {
            handleThumbSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.user_photo = response.data.url;
            },
            remoteSchoolMethod: function(query) {
                if (query !== '') {
                    this.loading = true;
                    $.ajax({
                        type: 'post',
                        url: this.ssearch_url,
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
                } else {
                    this.school_list = [];
                }
            },
            addAjax: function(params) {
                $.ajax({
					type: 'post',
					url: this.base_url,
					data: params,
					dataType:"json",
					success: function(data) {
                        if(data.code == 200) {
                            parent.layer.closeAll();
                            parent.vm.messageNotice('success', data.msg);
                            parent.vm.listAjax();
                        } else {
                            vm.messageNotice('warning', data.msg);
                        }
					},
					error: function() {
                        vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
            },
            checkIdentity: function () {
                $.ajax({
                    type: 'post',
                    url: this.checkIdentity_url,
                    data: {"identity_id": vm.ruleForm.identity_id},
                    dataType: 'json',
                    success:function(ret) {
                        if(ret.code == 200) {
                            vm.messageNotice('success', ret.msg);
                        } else {
                            vm.messageNotice('warning', ret.msg);
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '身份证检测错误');
                    }
                });
            },
            checkPhone: function () {
                $.ajax({
                    type: 'post',
                    url: this.checkPhone_url,
                    data: {"s_phone": vm.ruleForm.s_phone},
                    dataType: 'json',
                    success:function(ret) {
                        if(ret.code == 200) {
                            vm.messageNotice('success', ret.msg);
                        } else {
                            vm.messageNotice('warning', ret.msg);
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '手机号码检测错误');
                    }
                });
            },
            updateData: function (data) {
                this.content = data;
            },
            getLicenseInfo: function() {
                $.ajax({
                    type: 'post',
                    url: this.license_url,
                    dataType: 'json',
                    success:function(ret) {
                        vm.license_options = ret.data;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取牌照列表错误');
                    }
                });
            },
            getLessonInfo: function() {
                $.ajax({
                    type: 'post',
                    url: this.lesson_url,
                    dataType: 'json',
                    success:function(ret) {
                        vm.lesson_options = ret.data;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取科目列表错误');
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
            messageNotice: function(type, msg) {
				this.$message({
					type: type,
					message: msg
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
                this.dialogFormVisible = false;
                this.$refs[formName].resetFields();
            },
        }
    });

</script>
