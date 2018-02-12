<div id="app" v-cloak>
    <div class="iframe-content">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
        
            <el-tabs v-model="activeName" type="card" @tab-click="handleClick">
                <el-tab-pane label="基本信息" name="first">
                    <el-form-item label="驾校名称" prop="s_school_name" required>
                        <el-input v-model="ruleForm.s_school_name" placeholder="请输入驾校名称"></el-input>
                    </el-form-item>
                    <el-form-item label="驾校头像" prop="s_thumb" required>
                        <el-upload
                            :action="upload_thumb_url"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleThumbSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.s_thumb">
                            <img v-if="ruleForm.schoolthumb" :src="ruleForm.schoolthumb" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                    <el-form-item label="法人代表" prop="s_frdb" required>
                        <el-input v-model="ruleForm.s_frdb" placeholder="请输入法人代表"></el-input>
                    </el-form-item>
                    <el-form-item label="法人手机" prop="s_frdb_mobile" required>
                        <el-input v-model="ruleForm.s_frdb_mobile" placeholder="请输入法人手机"></el-input>
                    </el-form-item>
                    <el-form-item label="驾校固话" prop="s_frdb_tel">
                        <el-input v-model="ruleForm.s_frdb_tel" placeholder="请输入驾校固话"></el-input>
                    </el-form-item>
                    <el-form-item label="组织机构代码" prop="s_zzjgdm">
                        <el-input v-model="ruleForm.s_zzjgdm" placeholder="请输入组织机构代码"></el-input>
                    </el-form-item>
                    <el-form-item label="驾校性质" prop="i_dwxz" required>
                        <el-select v-model="ruleForm.i_dwxz" placeholder="请选择驾校性质">
                        <el-option label="一类驾校" value="1"></el-option>
                        <el-option label="二类驾校" value="2"></el-option>
                        <el-option label="三类驾校" value="3"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="品牌标识" prop="brand" required>
                        <el-select v-model="ruleForm.brand" placeholder="请选择品牌标识">
                        <el-option label="普通驾校" value="1"></el-option>
                        <el-option label="品牌驾校" value="2"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="经纬度" required>
                        <el-col :span="11">
                        <el-form-item prop="s_location_x">
                            <el-input v-model="ruleForm.s_location_x" placeholder="请输入经度"></el-input>
                        </el-form-item>
                        </el-col>
                        <el-col class="line" :span="2" style="text-align:center;">-</el-col>
                        <el-col :span="11">
                        <el-form-item prop="s_location_y">
                            <el-input v-model="ruleForm.s_location_y" placeholder="请输入维度"></el-input>
                        </el-form-item>
                        </el-col>
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
                        <el-input v-model="ruleForm.s_address" placeholder="请输入详细地址"></el-input>
                    </el-form-item>
                    <el-form-item label="营业执照" prop="s_yyzz">
                        <el-upload
                            :action="upload_license_url"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleLicenseSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.s_yyzz">
                            <img v-if="ruleForm.schoollicence" :src="ruleForm.schoollicence" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                </el-tab-pane>
                
                <el-tab-pane label="银行配置" name="second">
                    <el-form-item label="收费标准" prop="dc_base_je">
                        <el-input v-model="ruleForm.dc_base_je" placeholder="请输入收费标准如：4000.00"></el-input>
                    </el-form-item>
                    <el-form-item label="上浮最高比例" prop="dc_bili">
                        <el-input v-model="ruleForm.dc_bili" placeholder="请输入上浮最高比例"></el-input>
                    </el-form-item>
                    <el-form-item label="收款银行名称" prop="s_yh_name">
                        <el-input v-model="ruleForm.s_yh_name" placeholder="请输入收款银行名称"></el-input>
                    </el-form-item>
                    <el-form-item label="收款银行账号" prop="s_yh_zhanghao">
                        <el-input v-model="ruleForm.s_yh_zhanghao" placeholder="请输入收款银行账号"></el-input>
                    </el-form-item>
                    <el-form-item label="银行账户户名" prop="s_yh_huming">
                        <el-input v-model="ruleForm.s_yh_huming" placeholder="请输入银行账户户名"></el-input>
                    </el-form-item>
                </el-tab-pane>
            </el-tabs>

            <el-form-item label="是否在线" prop="is_show">
                <el-switch on-text="" off-text="" v-model="ruleForm.is_show"></el-switch>
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
            ruleForm: {
                s_school_name: '',
                s_thumb: '',
                s_frdb: '',
                s_frdb_mobile: '',
                s_frdb_tel: '',
                s_zzjgdm: '',
                dc_base_je: '',
                dc_bili: '',
                s_yh_name: '',
                s_yh_zhanghao: '',
                s_yh_huming: '',
                i_dwxz: '',
                brand: '',
                s_location_x: '',
                s_location_y: '',
                province_id: '',
                city_id: '',
                area_id: '',
                s_address: '',
                s_yyzz: '',
                is_show: false,
                schoolthumb: '',
                schoollicence: '',
            },
            place: {
                province_list: [],
                city_list: [],
                area_list: []
            },
            rules: {
                s_school_name: [
                    { required: true, message: '请输入驾校名称', trigger: 'blur' },
                ],
                s_thumb: [
                    { required: true, message: '请选择驾校头像', trigger: 'blur' }
                ],
                s_frdb: [
                    { required: true, message: '请输入法人代表', trigger: 'blur' }
                ],
                s_frdb_mobile: [
                    { required: true, message: '请输入法人手机', trigger: 'blur' }
                ],
                s_zzjgdm: [
                    { required: true, message: '请输入组织机构代码', trigger: 'blur' }
                ],
                dc_base_je: [
                    { required: true, message: '请输入收费标准', trigger: 'blur' }
                ],
                i_dwxz: [
                    { required: true, message: '请选择驾校性质', trigger: 'change' }
                ],
                brand: [
                    { required: true, message: '请选择品牌标识', trigger: 'change' }
                ],
                s_location_x: [
                    { required: true, message: '请输入经度', trigger: 'blur' }
                ],
                s_location_y: [
                    { required: true, message: '请输入纬度', trigger: 'blur' }
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
            errorClass: 'error',
            base_url: "<?php echo base_url('school/addajax'); ?>",
            upload_thumb_url: "<?php echo base_url('upload/handle?type=schoolthumb') ?>",
            upload_license_url: "<?php echo base_url('upload/handle?type=schoollicence') ?>",
            province_url: "<?php echo base_url('school/provinceajax'); ?>",
            city_url: "<?php echo base_url('school/cityajax'); ?>",
            area_url: "<?php echo base_url('school/areaajax'); ?>",
            activeName: 'first',
        },
        created: function() {
            this.provinceAjax();
        },
        methods: {
            handleClick(tab, event) {
                console.log(tab, event);
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
                            parent.vm.messageNotice('warning', data.msg);
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
                this.ruleForm.s_thumb = response.data.url;
            },
            handleLicenseSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.s_yyzz = response.data.url;
            },
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.addAjax(this.ruleForm);
                    } else {
                        return false;
                    }
                });
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();
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
            }

        }
    });

</script>
