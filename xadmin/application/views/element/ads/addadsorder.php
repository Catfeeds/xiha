<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">

    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="200px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="买家" required>
                <el-form-item prop="school_id" >
                    <el-select @change="getBuyerPhone"
                        v-model="ruleForm.school_id"
                        filterable
                        remote
                        clearable
                        placeholder="请输入买家，如嘻哈学车"
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
            </el-form-item>
            <el-form-item label="买家号码" prop="buyer_phone" required>
                <el-col :span="5">
                    <el-input v-model="ruleForm.buyer_phone" placeholder="请输入买家号码" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="有效期至" prop="over_time" >
                <div class="block">
                    <el-date-picker
                        v-model="ruleForm.over_time"
                        type="datetime"
                        placeholder="选择日期时间">
                    </el-date-picker>
                </div>
            </el-form-item> 
            <el-form-item label="广告内容标题" prop="title" required>
                <el-col :span="5">
                    <el-input v-model="ruleForm.title" placeholder="请输入广告内容标题" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="广告" prop="ads_id" required>
                <el-select v-model="ruleForm.ads_id" placeholder="请选择广告">
                    <el-option v-for="item in ads_list" :label="item.ads_title" :value="item.ads_id"></el-option>
                </el-select>
            </el-form-item>
            
            <el-form-item label="资源类型" prop="resource_type" required>
                <el-select v-model="ruleForm.resource_type" placeholder="请选择资源类型">
                    <el-option label="图片" value="1"></el-option>
                    <el-option label="视频" value="2"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="设备类型" prop="device" required>
                <el-select v-model="ruleForm.device" placeholder="请选择设备类型">
                    <el-option label="苹果" value="1"></el-option>
                    <el-option label="安卓" value="2"></el-option>
                    <el-option label="安卓 | 苹果" value="1,2"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="支付方式" prop="pay_type" required>
                <el-select v-model="ruleForm.pay_type" placeholder="请选择方式" >
                    <el-option v-for="item in pt_options" :label="item.label" :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="订单状态" prop="order_status" required>
                <el-select v-model="ruleForm.order_status" placeholder="请选择订单状态" >
                    <el-option v-for="item in order_options" :label="item.label" :value="item.value"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="轮播时间" prop="loop_time">
                <el-col :span="5">
                    <el-input v-model="ruleForm.loop_time" placeholder="请输入轮播时间，如：5" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="原始价格" prop="original_price" required>
                <el-col :span="5">
                    <el-input v-model="ruleForm.original_price" placeholder="请输入原始价格，如：3000" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="最终价格" prop="final_price" required>
                <el-col :span="5">
                    <el-input v-model="ruleForm.final_price" placeholder="请输入最终价格，如：2500" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="广告跳转" prop="ads_url">
                <el-col :span="5">
                    <el-input v-model="ruleForm.ads_url" placeholder="请输入广告跳转的页面" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="是否打折" prop="is_promote" required>
                <el-switch
                    v-model="ruleForm.is_promote"
                    on-text=""
                    off-text="">
                </el-switch>
            </el-form-item>
            <el-form-item label="订单号" prop="order_no" required>
                <el-col :span="6">
                    <el-input v-model="ruleForm.order_no" ></el-input>
                </el-col>
                <el-col :span="5" style="margin-left: 10px;">
                    <el-button type="primary" icon="edit" @click="orderCreate">自动生成</el-button>
                </el-col>
            </el-form-item>
            <el-form-item label="图片上传" prop="resource_url">
                <el-upload
                    :action="adsUpload"
                    drag
                    accept
                    :on-success="handleAdsOrderSuccess"
                    :class="[ruleForm.resource_url ? errorClass : '']"
                    v-model="ruleForm.resource_url">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                    <div class="el-upload__tip" slot="tip">只能上传图片</div>
                </el-upload>
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
            loading: false,
            ads_list: [],
            school_list: [],
            errorClass: "error",
            pt_options: [
                {value: '1', label: "支付宝"},
                {value: '2', label: "线下"},
                {value: '3', label: "微信"},
                {value: '4', label: "银联"}
            ],
            order_options: [
                {value: '1001', label: "付款中"},
                {value: '1002', label: "已付款"},
                {value: '1003', label: "未付款"},
                {value: '1004', label: "取消中"},
                {value: '1005', label: "已取消"},
                {value: '1006', label: "退款中"},
                {value: '1007', label: "已退款"}
            ],
            ruleForm: {
                school_id: '',
                buyer_phone: '',
                ads_id: '',
                title: '',
                over_time: "",
                loop_time: 5,
                original_price: '',
                final_price: '',
                is_promote: false,
                ads_url: "http://www.xihaxueche.com",
                order_status: '',
                pay_type: '',
                device: '',
                resource_type: '',
                resource_url: '',
                order_no: '',
            },
            rules: {
                school_id: [
                    { required: true, message: '请选择买家', trigger: 'change' }
                ],
                ads_id: [
                    { required: true, message: '请选择广告', trigger: 'change' }
                ],
                title: [
                    { required: true, message: '请输入广告内容标题', trigger: 'blur' }
                ],
                buyer_phone: [
                    { required: true, message: '请输入买家号码', trigger: 'blur'}
                ],
                order_status: [
                    { required: true, message: '请选择订单状态', trigger: 'change' }
                ],
                pay_type: [
                    { required: true, message: '请选择支付方式', trigger: 'change' }
                ],
                original_price: [
                    { required: true, message: '请输入原始价格', trigger: 'blur' }
                ],
                final_price: [
                    { required: true, message: '请输入最终价格', trigger: 'blur' }
                ],
                device: [
                    { required: true, message: '请选择设备类型', trigger: 'change' }
                ],
                resource_type: [
                    { required: true, message: '请选择资源类型', trigger: 'change' }
                ],
                order_no: [
                    { required: true, message: '请输入订单号', trigger: 'blur' }
                ],
            },
            add_url: "<?php echo base_url('ads/addAjax')?>?type=order",
            ads_url: "<?php echo base_url('ads/adsListAjax')?>",
            ssearch_url: "<?php echo base_url('systems/searchAjax')?>?type=school",
            phone_url: "<?php echo base_url('ads/getPhoneAjax')?>",
            create_url: "<?php echo base_url('ads/orderCreateAjax')?>",
            adsUpload: "<?php echo base_url('upload/handle?type=xihaApp') ?>",
        },
        created: function() {
            this.adsListAjax();
        },
        methods: {
            handleAdsOrderSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.resource_url = response.data.url;
            },
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
            getBuyerPhone: function() {
                this.ruleForm.buyer_phone = '',
                $.ajax({
                    type: 'post',
                    url: this.phone_url,
                    data: {school_id: this.ruleForm.school_id},
                    dataType: 'json',
                    success:function(data) {
                        if (data.code == 200) {
                            vm.ruleForm.buyer_phone = data.data;
                        }
                    },
                    // error: function() {
                    //     vm.messageNotice('warning', '网络异常');
                    // }
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
                            if (data.code == 200) {
                                vm.school_list = data.data.list;
                            }
                        },
                        error: function() {
                            vm.messageNotice('warning', '搜索驾校出现网络错误');
                        }
                    });
                } 
            },
            adsListAjax: function(){
                $.ajax({
                    type: 'post',
                    url: this.ads_url,
                    dataType: 'json',
                    success: function(data) {
                        is_ok = _.isObject(data) && _.has(data, 'code') && _.get(data, 'code') == 200
                        if (is_ok) {
                            vm.ads_list = data.data;
                        } else {
                            parent.vm.messageNotice('warning', _.get(data, 'msg'));
                        }
                    },
                    error: function(e) {
                        vm.messageNotice('error', '网络出错！');
                    }
                });
            },
            addAjax: function(params) {
                if (isNaN(parseInt(params.over_time))) {
                    params.over_time = parseInt(params.over_time.getTime()/1000);
                }  
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
                        } else if (_.get(data, 'code') == 102) {
                            parent.layer.closeAll();
                            parent.vm.messageNotice('warning', _.get(data, 'msg'));
                            parent.vm.listAjax(parent.vm.currentPage);
                        } else {
                            parent.vm.messageNotice('warning', _.get(data, 'msg'));
                        }
                    },
                    error: function(e) {
                        vm.messageNotice('error', '网络出错！');
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