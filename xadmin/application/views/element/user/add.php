<div id="app" v-cloak>
    <div class="iframe-content">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
        
            <el-tabs v-model="activeName" type="card" @tab-click="handleClick">
                <el-tab-pane label="基本信息" name="first">
                    <el-form-item label="手机号" prop="s_phone" required>
                        <el-input v-model="ruleForm.s_phone" @change="handleUsername" placeholder="请输入用户手机号"></el-input>
                    </el-form-item>

                    <el-form-item label="昵称" prop="s_username" required>
                        <el-input v-model="ruleForm.s_username" placeholder="请输入昵称"></el-input>
                    </el-form-item>

                    <el-form-item label="真实名" prop="s_real_name" required>
                        <el-input v-model="ruleForm.s_real_name" placeholder="请输入真实名"></el-input>
                    </el-form-item>

                    <el-form-item label="学员头像" prop="user_photo">
                        <el-upload
                            :action="upload_thumb_url"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleThumbSuccess"
                            v-model="ruleForm.user_photo">
                            <img v-if="ruleForm.userthumb" :src="ruleForm.userthumb" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>

                    <el-form-item label="性别" prop="sex">
                        <el-radio-group v-model="ruleForm.sex">
                            <el-radio :label="1">男</el-radio>
                            <el-radio :label="2">女</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    
                    <el-form-item label="年龄" prop="age">
                        <el-input-number v-model="ruleForm.age" @change="handleAgeChange" :min="16" :max="70"></el-input-number>
                    </el-form-item>

                    <el-form-item label="密码" prop="s_password">
                        <el-col :span="8" style="position:relative;">
                            <el-input :type="pass" v-model="ruleForm.s_password" placeholder="请输入密码"></el-input>
                            <i class="iconfont" :class="iconfont" style="position:absolute; top: 0px; right: 10px; font-size: 2rem; color: #999;" @click="handleIconClick"></i> 
                        </el-col>
                        <el-col :span="11" style="margin-left: 10px;">
                            <el-button type="danger" @click="initPass">初始密码</el-button>
                        </el-col>
                    </el-form-item>

                    <el-form-item label="身份证号码" prop="identity_id" required>
                        <el-input v-model="ruleForm.identity_id" placeholder="请输入身份证号码"></el-input>
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
                        <el-input v-model="ruleForm.address" placeholder="请输入详细地址"></el-input>
                    </el-form-item>
                    
                </el-tab-pane>
                
                <el-tab-pane label="详细信息" name="second">
                    <el-form-item label="领证次数">
                        <el-input-number v-model="ruleForm.license_num" @change="handleLicenseNameChange" :min="0" :max="10"></el-input-number>
                    </el-form-item>

                    <el-form-item label="报名驾校" prop="school_id">
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
                        </el-form-item>
                    </el-form-item>
                    <el-form-item label="牌照" prop="license_info" required>
                        <el-select v-model="ruleForm.license_info" clearable placeholder="请选择牌照">
                            <el-option v-for="item in license_list" :label="item.license_name" :value="item.license_id+'|'+item.license_name"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="科目" prop="lesson_info" required>
                        <el-select v-model="ruleForm.lesson_info" clearable placeholder="请选择科目">
                            <el-option label="科目一" value="1|科目一"></el-option>
                            <el-option label="科目二" value="2|科目二"></el-option>
                            <el-option label="科目三" value="3|科目三"></el-option>
                            <el-option label="科目四" value="4|科目四"></el-option>
                        </el-select>
                    </el-form-item>
                    
                    <el-form-item label="钱包余额" prop="balance">
                        <el-input v-model="ruleForm.balance" placeholder="请输入钱包余额"></el-input>
                    </el-form-item>
                    
                    <el-form-item label="激活支付密码" prop="is_paypass_activated">
                        <el-switch on-text="" off-text="" v-model="ruleForm.is_paypass_activated"></el-switch>
                    </el-form-item>

                    <el-form-item label="手势密码" prop="wallet_access_pass">
                        <el-col :span="11" style="position:relative;">
                            <el-input :type="pass" v-model="ruleForm.wallet_access_pass" placeholder="请输入钱包手势访问密码"></el-input>
                            <i class="iconfont" :class="iconfont" style="position:absolute; top: 0px; right: 10px; font-size: 2rem; color: #999;" @click="handleIconClick"></i> 
                        </el-col>
                    </el-form-item>
                    
                    <el-form-item label="支付密码" prop="pay_pass">
                        <el-col :span="11" style="position:relative;">
                            <el-input :type="pass" v-model="ruleForm.pay_pass" placeholder="请输入支付密码 6位数字"></el-input>
                            <i class="iconfont" :class="iconfont" style="position:absolute; top: 0px; right: 10px; font-size: 2rem; color: #999;" @click="handleIconClick"></i> 
                        </el-col>
                    </el-form-item>

                    <el-form-item label="嘻哈币" prop="xiha_coin">
                        <el-input-number v-model="ruleForm.xiha_coin" :min="0" :max="99999999"></el-input-number>
                    </el-form-item>

                    <el-form-item label="科目学习中" prop="learncar_status">
                        <el-select v-model="ruleForm.learncar_status" clearable placeholder="请选择科目学习状态">
                            <el-option label="科目一学习中" value="科目一学习中"></el-option>
                            <el-option label="科目二学习中" value="科目二学习中"></el-option>
                            <el-option label="科目三学习中" value="科目三学习中"></el-option>
                            <el-option label="科目四学习中" value="科目四学习中"></el-option>
                        </el-select>
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
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            textarea: '',
            ruleForm: {
                s_username: '',
                s_real_name: '',
                user_photo: '',
                s_phone: '',
                s_password: '',
                userthumb: '',
                identity_id: '',
                sex: 1,
                age: 0,
                identity_id: '',
                address: '',
                license_num: 0,
                school_id: 0,
                lesson_info: '',
                license_info: '',
                exam_license_name: '',
                balance: 0,
                is_paypass_activated: false,
                wallet_access_pass: '',
                pay_pass: '',
                xiha_coin: 0,
                signin_num: 0,
                signin_lasttime: 0,
                province_id: '',
                city_id: '',
                area_id: '',
                learncar_status: '',
            },
            pass: 'password',
            iconfont: 'icon-yanjing2',
            place: {
                province_list: [],
                city_list: [],
                area_list: []
            },
            rules: {
                s_username: [
                    { required: true, message: '请输入昵称', trigger: 'blur' },
                ],
                s_real_name: [
                    { required: true, message: '请输入真实姓名', trigger: 'blur' },
                ],
                s_password: [
                    { required: true, message: '请输入密码', trigger: 'blur' }
                ],
                s_phone: [
                    { required: true, message: '请输入用户手机号', trigger: 'blur' }
                ],
                identity_id: [
                    { required: true, message: '请输入身份证号', trigger: 'blur' }
                ],
                address: [
                    { required: true, message: '请输入详细地址', trigger: 'blur' }
                ],
                license_info: [
                    { required: true, message: '请选择牌照', trigger: 'change' }
                ],
                lesson_info: [
                    { required: true, message: '请选择科目', trigger: 'change' }
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
                
            },
            errorClass: 'error',
            base_url: "<?php echo base_url('user/addajax'); ?>",
            upload_thumb_url: "<?php echo base_url('upload/handle?type=userthumb') ?>",
            province_url: "<?php echo base_url('school/provinceajax'); ?>",
            city_url: "<?php echo base_url('school/cityajax'); ?>",
            area_url: "<?php echo base_url('school/areaajax'); ?>",
            ssearch_url: "<?php echo base_url('school/search')?>",
            liceconfig_url: "<?php echo base_url('school/liceconfigajax')?>",
            activeName: 'first',
            school_list: [],
            license_list: [],
            lesson_list: [],
            loading: false,
        },
        created: function() {
            this.provinceAjax();
            this.liceConfigAjax();
        },
        methods: {
            handleClick: function(tab, event) {
                // console.log(tab, event);
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
            messageNotice: function(type, msg) {
				this.$message({
					type: type,
					message: msg
				});
			},
            handleThumbSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.user_photo = response.data.url;
            },
            submitForm: function(formName) {
                this.$refs[formName].validate(function(valid) {
                    if (valid) {
                        vm.addAjax(this.ruleForm);
                    } else {
                        return false;
                    }
                });
            },
            resetForm: function(formName) {
                this.$refs[formName].resetFields();
            },
            handleProvince: function(event) {
                this.cityAjax(this.ruleForm.province_id);
                this.ruleForm.city_id = '';
                // this.ruleForm.s_address += event;
            },
            handleCity: function(event) {
                this.areaAjax(this.ruleForm.city_id);
                this.ruleForm.area_id = '';
                // this.ruleForm.s_address += event;
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
            initPass: function() {
                this.ruleForm.s_password = 'xiha123456';
            },
            handleIconClick: function(ev) {
                // console.log(ev)
                if(this.iconfont == "icon-yanjing") {
                    this.iconfont = 'icon-yanjing2';
                    this.pass = 'password';
                } else {
                    this.iconfont = 'icon-yanjing';
                    this.pass = 'text';
                }
            },
            handleAgeChange: function(val) {
                // console.log(val)
            },
            handleLicenseNameChange: function(val) {
                // console.log(val)
            },
            handleUsername: function() {
                this.ruleForm.s_username = 'xiha_'+this.ruleForm.s_phone.substr(5);
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
                    })
                } else {
                    this.school_list = [];
                }
            },
            liceConfigAjax: function() {
                $.ajax({
                    type: 'post',
                    url: this.liceconfig_url,
                    dataType: 'json',
                    success:function(data) {
                        vm.license_list = data.data.list;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取牌照列表错误');
                    }
                });
            },

        }
    });

</script>
