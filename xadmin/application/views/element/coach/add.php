<div id="app" v-cloak>
    <div class="iframe-content">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px" class="demo-ruleForm" :label-position="labelPosition">
            <el-tabs v-model="activeName" type="card" @tab-click="handleClick">
                <el-tab-pane label="基本信息" name="first">
                    <el-form-item label="姓名" prop="s_coach_name" required >
                        <el-col span="8">
                            <el-input v-model="ruleForm.s_coach_name" placeholder="请填写姓名"></el-input>
                        </el-col>
                    </el-form-item>

                    <el-form-item label="手机号码" prop="s_coach_phone" required >
                        <el-col span="8">
                            <el-input v-model="ruleForm.s_coach_phone" placeholder="请填写手机号码" v-on:blur="checkPhone()"></el-input>
                        </el-col>
                    </el-form-item>

                    <el-form-item label="教练头像" prop="s_coach_imgurl" required>
                        <el-upload
                            :action="upload_coach_photo_url"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleThumbSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']"
                            v-model="ruleForm.s_coach_imgurl">
                            <img v-if="ruleForm.coachimg" :src="ruleForm.coachimg" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                    
                    <el-form-item label="教练性别" prop="s_coach_sex" required>
                        <el-radio class="radio" v-model="ruleForm.s_coach_sex" label="1">男</el-radio>
                        <el-radio class="radio" v-model="ruleForm.s_coach_sex" label="2">女</el-radio>
                    </el-form-item>

                    <el-form-item label="教龄" prop="s_teach_age" required>
                        <el-input-number v-model="ruleForm.s_teach_age" :step="1" :min="0"></el-input-number>
                    </el-form-item>

                    <el-form-item label="教练平均星级" prop="i_coach_star" required >
                        <el-input-number v-model="ruleForm.i_coach_star" :step="1" :min="1" :max="5"></el-input-number>
                    </el-form-item>

                    <el-form-item :inline="true"  label="平均拿证时间" prop="average_license_time" required>
                        <el-col span="9">
                            <el-input-number v-model="ruleForm.average_license_time" :step="5" :min="30"></el-input-number>
                        </el-col>
                        <el-col span="5">
                            <span>天</span>
                        </el-col>
                    </el-form-item>

                    <el-form-item label="科目二通过率" prop="lesson2_pass_rate" required >
                        <el-col span="9">
                            <el-input-number v-model="ruleForm.lesson2_pass_rate" :step="10" :min="50" :max="100"></el-input-number>
                        </el-col>
                        <el-col span="5">
                            <span>%</span>
                        </el-col>
                    </el-form-item>

                    <el-form-item label="科目三通过率" prop="lesson3_pass_rate" required >
                        <el-col span="9">
                            <el-input-number v-model="ruleForm.lesson3_pass_rate" :step="10" :min="50" :max="100"></el-input-number>
                        </el-col>
                        <el-col span="5">
                            <span>%</span>
                        </el-col>
                    </el-form-item>
                    <el-form-item label="排序" prop="i_order" required>
                        <el-input-number v-model="ruleForm.i_order" :step="10" :min="0"></el-input-number>
                    </el-form-item>

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
                    <!-- <el-form-item label="所属驾校" prop="s_school_name_id" required>
                        <el-select @change="selectedSchool" v-model="ruleForm.s_school_name_id" placeholder="请选择驾校">
                            <el-option v-for="item in school_options" :label="item.s_school_name" :value="item.l_school_id"></el-option>
                        </el-select>
                    </el-form-item> -->

                    <el-form-item label="所属车辆" prop="s_coach_car_id" required>
                        <el-select v-model="ruleForm.s_coach_car_id" placeholder="请选择所属车辆">
                            <el-option v-for="item in cars_options" :label="item.name+'|'+item.car_no" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="教练类型" prop="i_type" required>
                        <el-select v-model="ruleForm.i_type" placeholder="请选择教练类型">
                            <el-option v-for="item in itype_options" :label="item.name" :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="培训课程" prop="s_coach_lesson_id" required>
                        <!-- <el-select v-model="ruleForm.s_coach_lesson_id" multiple placeholder="请选择">
                            <el-option v-for="item in lesson_options" :key="item.lesson_id" :label="item.lesson_name" :value="item.lesson_id"></el-option>
                        </el-select> -->
                        <el-checkbox-group v-model="ruleForm.s_coach_lesson_id" >
                            <el-checkbox v-for="item in lesson_options" :label="item.lesson_name" :key="item.lesson_id">{{item.lesson_name}}</el-checkbox>
                        </el-checkbox-group>
                    </el-form-item>

                    <el-form-item label="培训牌照" prop="s_coach_lisence_id" required>
                        <el-checkbox-group v-model="ruleForm.s_coach_lisence_id" >
                            <el-checkbox v-for="item in license_options" :label="item.license_name" :key="item.license_id">{{item.license_name}}</el-checkbox>
                        </el-checkbox-group>
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

                    <el-form-item label="详细地址" prop="s_coach_address" required>
                        <el-col span="16">
                            <el-input v-model="ruleForm.s_coach_address" placeholder="请填写详细地址信息"></el-input>
                        </el-col>
                    </el-form-item>
                </el-tab-pane>

                <el-tab-pane label="状态配置" name="second">
                    <el-form-item label="热门否" prop="is_hot">
                        <el-switch v-model="ruleForm.is_hot" on-text="是" off-text="否"></el-switch>
                    </el-form-item>
                    <el-form-item label="在线否" prop="order_receive_status">
                        <el-switch v-model="ruleForm.order_receive_status" on-text="是" off-text="否"></el-switch>
                        <!-- <el-radio class="radio" v-model="ruleForm.order_receive_status" label="1">在线</el-radio>
                        <el-radio class="radio" v-model="ruleForm.order_receive_status" label="0">不在线</el-radio> -->
                    </el-form-item>
                    <el-form-item label="支持电子教练否" prop="is_elecoach">
                        <el-switch v-model="ruleForm.is_elecoach" on-text="是" off-text="否"></el-switch>
                        <!-- <el-radio class="radio" v-model="ruleForm.is_elecoach" label="1">是</el-radio>
                        <el-radio class="radio" v-model="ruleForm.is_elecoach" label="0">否</el-radio> -->
                    </el-form-item>
                    <el-form-item label="支持券否" prop="coupon_supported">
                        <el-switch v-model="ruleForm.coupon_supported" on-text="是" off-text="否"></el-switch>
                    </el-form-item>
                    <el-form-item label="支持计时否" prop="timetraining_supported">
                        <el-switch v-model="ruleForm.timetraining_supported" on-text="是" off-text="否"></el-switch>
                    </el-form-item>
                    <el-form-item label="学员需绑定否" prop="must_bind">
                        <el-switch v-model="ruleForm.must_bind" on-text="是" off-text="否"></el-switch>
                    </el-form-item>
                </el-tab-pane>
            </el-tabs>
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
            errorClass: 'error',
            labelPosition: 'right',
            textarea: '',
            loading: false,
            license_options: [],
            lesson_options: [],
            cars_options: [],
            itype_options: [],
            school_list: [],
            place: {
                province_list: [],
                city_list: [],
                area_list: []
            },
            ruleForm: {
                school_id: school_id,
                s_coach_name: '',
                s_coach_phone: '',
                s_teach_age: '',
                i_coach_star: '3',
                average_license_time: '',
                lesson2_pass_rate: '80',
                lesson3_pass_rate: '80',
                s_coach_sex: '1',
                s_coach_imgurl: '',
                coachimg: '',
                s_school_name: '',
                s_coach_car_id: '',
                s_coach_lesson_id: ['科目一'],
                s_coach_lisence_id: ['C1'],
                province_id: '',
                city_id: '',
                area_id: '',
                s_coach_address:'',
                i_type: '',
                is_hot: true,
                order_receive_status: true,
                is_elecoach: true,
                coupon_supported: true,
                timetraining_supported: false,
                must_bind: true,
                i_order: 50,
            },
            rules: {
                s_coach_name: [
                    { required: true, message: '请填写姓名'}
                ],
                s_coach_phone: [
                    { required: true, message: '请填写手机号码'}
                ],
                s_teach_age: [
                    { required: true, message: '请填写教龄'}
                ],
                i_coach_star: [
                    { required: true, message: '请填写教练平均星级'}
                ],
                average_license_time: [
                    { required: true, message: '请填写平均拿证时间'}
                ],
                lesson2_pass_rate: [
                    { required: false, message: '请填写科目二通过率'}
                ],
                lesson3_pass_rate: [
                    { required: false, message: '请填写科目三通过率'}
                ],
                s_coach_sex: [
                    { required: false, message: '请选择教练性别'}
                ],
                s_coach_imgurl: [
                    { required: true, message: '请上传教练头像'}
                ],
                s_school_name_id: [
                    { required: true, message: '请选择所属驾校'}
                ],
                s_coach_car_id: [
                    { required: true, message: '请选择所属车辆'}
                ],
                s_coach_lesson_id: [
                    { required: true, message: '请选择培训课程'}
                ],
                s_coach_lisence_id: [
                    { required: true, message: '请选择培训牌照'}
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
                s_coach_address: [
                    { required: true, message: '请填写详细地址信息'}
                ],
                i_type: [
                    { required: true, message: '请选择教练类型'}
                ],
            },
            base_url: "<?php echo base_url('coach/addAjax')?>",
            cars_url: "<?php echo base_url('coach/carsList')?>",
            license_url: "<?php echo base_url('coach/getLicenseInfo')?>",
            lesson_url: "<?php echo base_url('coach/getLessonInfo')?>",
            province_url: "<?php echo base_url('school/provinceajax'); ?>",
            city_url: "<?php echo base_url('school/cityajax'); ?>",
            area_url: "<?php echo base_url('school/areaajax'); ?>",
            school_url: "<?php echo base_url('coach/schoolList')?>",
            search_url: "<?php echo base_url('school/search')?>",
            upload_coach_photo_url: "<?php echo base_url('upload/handle?type=coachimg') ?>",
            checkPhone_url: "<?php echo base_url('coach/checkPhone')?>",
            itype_url: "<?php echo base_url('coach/iType')?>",
            activeName: 'first',
        },
        created: function() {
            this.provinceAjax();
            this.getCars({"school_id": this.ruleForm.school_id});
            this.getiType();
            this.schoolList();
            this.getLicenseInfo(0);
            this.getLessonInfo(0);
        },
        methods: {
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
                            parent.vm.listAjax(parent.vm.currentPage);
                        } else {
                            vm.messageNotice('warning', data.msg);
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '网络错误，请检查网络');
                    }
                });
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
                            vm.getCars({"school_id": vm.ruleForm.school_id});
                        },
                        error: function() {
                            vm.messageNotice('warning', '搜索驾校出现网络错误');
                        }
                    });
                } else {
                    vm.school_list = [];
                    vm.getCars({"school_id": vm.ruleForm.school_id});
                }
            },
            getCars: function(param) {
                $.ajax({
                    type: 'post',
                    url: this.cars_url,
                    data: param,
                    dataType: 'json',
                    success:function(ret) {
                        vm.cars_options = ret.data;

                    },
                    error: function() {
                        vm.messageNotice('warning', '获取车辆列表错误');
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
            handleThumbSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.s_coach_imgurl = response.data.url;
            },
            getLicenseInfo: function(l_coach_id) {
                $.ajax({
                    type: 'post',
                    url: this.license_url,
                    data: {id:l_coach_id},
                    dataType: 'json',
                    success:function(ret) {
                        vm.license_options = ret.data;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取牌照列表错误');
                    }
                });
            },
            getLessonInfo: function(l_coach_id) {
                $.ajax({
                    type: 'post',
                    url: this.lesson_url,
                    data: {id:l_coach_id},
                    dataType: 'json',
                    success:function(ret) {
                        vm.lesson_options = ret.data;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取科目列表错误');
                    }
                });
            },
            
            getiType: function() {
                $.ajax({
                    type: 'post',
                    url: this.itype_url,
                    dataType: 'json',
                    success:function(ret) {
                        vm.itype_options = ret.data;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取教练等级列表错误');
                    }
                });
            },
            schoolList: function() {
                $.ajax({
                    type: 'post',
                    url: this.school_url,
                    dataType: 'json',
                    success:function(ret) {
                        vm.school_options = ret.data;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取教练等级列表错误');
                    }
                });
            },
            checkPhone: function () {
                $.ajax({
                    type: 'post',
                    url: this.checkPhone_url,
                    data: {"coach_phone": vm.ruleForm.s_coach_phone},
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
            handleClick: function(tab, event) {
                // console.log(tab, event);
            },
            messageNotice: function(type, msg) {
                this.$message({
                    type: type,
                    message: msg
                });
            },
            resetForm: function(formName) {
                this.$refs[formName].resetFields();
            },
            submitForm: function(formName) {
                this.$refs[formName].validate(function(valid) {
                    if (valid) {
                        vm.addAjax(vm.ruleForm);
                    } else {
                        return false;
                    }
                });
            }
        }
    });

</script>
