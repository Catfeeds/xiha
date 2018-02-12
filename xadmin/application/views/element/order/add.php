<div id="app" v-cloak>
    <div class="iframe-content">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="用户名" prop="user_name" required>
                <el-col :span="11">
                    <el-input v-model="ruleForm.user_name" placeholder="请输入用户名"></el-input>
                </el-col>
            </el-form-item>

            <el-form-item label="手机号" prop="user_phone" required>
                <el-col :span="11">
                    <el-input v-model="ruleForm.user_phone" placeholder="请输入用户手机号"></el-input>
                </el-col>
            </el-form-item>

            <el-form-item label="身份证号码" prop="identity_id" required>
                <el-col :span="11">
                    <el-input v-model="ruleForm.identity_id" placeholder="请输入身份证号码"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="牌照" prop="license" required>
                <el-select v-model="ruleForm.license" clearable placeholder="请选择牌照">
                    <el-option v-for="item in license_list" :label="item.license_name" :value="item.license_id"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item prop="so_school_id" label="所属驾校" v-if="school_id == ''" required>
                <el-select
                    v-model="ruleForm.so_school_id"
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
            <el-form-item label="班制选择" prop="so_shifts_id" required>
                <el-select
                    v-model="ruleForm.so_shifts_id"
                    filterable
                    remote
                    clearable
                    placeholder="请输入选择班制">
                    <el-option
                    v-for="item in shifts_list"  
                    :key="item.sh_title"
                    :label="item.sh_title"
                    :value="item.id">
                    </el-option>
                </el-select>
                <span v-if="school_id ==''" style="color: red">（ 注：亲需选择驾校才会出现对应驾校下的班制哦 ）</span>
            </el-form-item>
            <el-form-item label="订单状态" prop="pay_type">
                <el-radio-group size="small" v-model="ruleForm.pay_type">
                    <el-radio-button  label="线下"></el-radio-button>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="支付状态" prop="order_status">
                <el-radio-group size="small" v-model="ruleForm.order_status">
                    <el-radio-button label="已付款"></el-radio-button>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="原始价格" prop="original_price" required>
                <template>
                    <el-input-number v-model="ruleForm.original_price" :step="100"></el-input-number> 元
                </template>
            </el-form-item>
            <el-form-item label="最终价格" prop="final_price" required>
                <template>
                    <el-input-number v-model="ruleForm.final_price" :step="100"></el-input-number> 元
                </template>
            </el-form-item>
            <el-form-item label="实付价格" prop="total_price" required>
                <template>
                    <el-input-number v-model="ruleForm.total_price" :step="100"></el-input-number> 元
                </template>
            </el-form-item>
            <el-form-item label="优惠计时" prop="free_study_hour" required>
                <template>
                    <el-input-number v-model="ruleForm.free_study_hour" :step="1"  :min="-1" ></el-input-number> 小时
                    <span style="color: red"> ( tips:若值为10代表前10个学时免费预约，第11个学时起正常收费；默认值-1代表不限制优惠学时数)</span>
                </template>
            </el-form-item>
            <el-form-item label="订单号" prop="order_no" required>
                <el-col :span="6">
                    <el-input v-model="ruleForm.order_no" ></el-input>
                </el-col>
                <el-col :span="5" style="margin-left: 10px;">
                    <el-button type="primary" icon="edit" @click="orderCreate">自动生成</el-button>
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
    var school_id = "<?php echo $school_id?>";
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            textarea: '',
            license_list: [],
            lesson_list: [],
            school_list: [],
            shifts_list: [],
            ruleForm: {
                user_name: '',
                user_phone: '',
                license: '',
                identity_id: '',
                so_school_id: school_id,
                so_shifts_id: '',
                free_study_hour: '-1',
                original_price: '3000',
                final_price: '3000',
                total_price: '3000',
                pay_type: '线下',
                order_status: '已付款',
                order_no: '',
            },
            rules: {
                user_name: [
                    { required: true, message: '请输入用户名', trigger: 'blur' },
                ],
                user_phone: [
                    { required: true, message: '请输入联系方式', trigger: 'blur' },
                ],
                identity_id: [
                    { required: true, message: '请输入身份证号', trigger: 'blur' }
                ],
                license: [
                    { required: true, message: '请选择牌照', trigger: 'change' }
                ],
                so_school_id: [
                    { required: true, message: '请输入驾校', trigger: 'change' }
                ],
                so_shifts_id: [
                    { required: true, message: '请选择班制', trigger: 'change' }
                ],
                so_school_id: [
                    { required: true, message: '请输入驾校', trigger: 'change' }
                ],
            },
            errorClass: 'error',
            base_url: "<?php echo base_url('order/addajax'); ?>",
            province_url: "<?php echo base_url('school/provinceajax'); ?>",
            city_url: "<?php echo base_url('school/cityajax'); ?>",
            area_url: "<?php echo base_url('school/areaajax'); ?>",
            ssearch_url: "<?php echo base_url('school/search')?>",
            liceconfig_url: "<?php echo base_url('school/liceconfigajax')?>",
            shifts_url: "<?php echo base_url('order/schoolShiftsAjax')?>",
            create_url: "<?php echo base_url('ads/orderCreateAjax')?>",
            loading: false,
        },
        created: function() {
            this.liceConfigAjax();
            this.shiftsAjax({'school_id': school_id});
        },
        methods: {
            orderCreate: function() {
                $.ajax({
                    url: this.create_url,
                    dataType: 'json',
                    success:function(res) {
                        vm.ruleForm.order_no = res.data;
                    },
                    error: function() {
                        vm.messageNotice('warning', '网络异常');
                    }
                });
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
                    // 获取班制列表
                    if(this.school_list.length > 0) {
                        var param = {
                            'school_id': vm.ruleForm.so_school_id,
                        };
                        this.shiftsAjax(param);
                    }
                } else {
                    this.school_list = [];
                    this.shifts_list = [];
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
            shiftsAjax: function(param) {
               $.ajax({
                    type: 'post',
                    url: this.shifts_url,
                    data: param,
                    dataType: 'json',
                    success:function(data) {
                        if (data.code == 200) {
                            vm.shifts_list = data.data.list;
                        } else {
                            vm.messageNotice('warning', data.msg);
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取牌照列表错误');
                    }
                }); 
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
                        vm.addAjax(vm.ruleForm);
                    } else {
                        return false;
                    }
                });
            },
            resetForm: function(formName) {
                this.$refs[formName].resetFields();
            },

        }
    });

</script>
