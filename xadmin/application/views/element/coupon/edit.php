<div id="app" v-cloak>
<div class="iframe-content">
    <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px" class="demo-ruleForm" :label-position="labelPosition">
        <el-form-item label="优惠券名称" prop="coupon_name" required >
            <el-col span="8">
                <el-input v-model="ruleForm.coupon_name" placeholder="优惠券名称"></el-input>
            </el-col>
        </el-form-item>
        <!-- <el-form-item label="角色类别" prop="owner_type" required >
            <el-select  v-model="ruleForm.owner_type" >
                <el-option v-for="item in owner_type_options" :label="item.label" :value="item.value" v-if="school_id == ''"></el-option>
                <el-option v-for="item in owner_type_option" :label="item.label" :value="item.value" v-if="school_id != ''"></el-option>
            </el-select>
        </el-form-item> -->
        <el-form-item label="角色类别" prop="owner_type_name" required>
            <el-col span="8">
                <el-input v-model="ruleForm.owner_type_name" :disabled="true"></el-input>
            </el-col>
        </el-form-item>
        <el-form-item label="角色名称" prop="owner_id" required>
            <el-col span="8">
                <el-input v-model="ruleForm.owner_name" :disabled="true"></el-input>
            </el-col>
        </el-form-item>
        <!-- <el-form-item prop="owner_id" label="角色名称" prop="owner_id" required>
            <el-select
                v-model="ruleForm.owner_id"
                filterable
                remote
                clearable
                placeholder="请输入角色名称，如：嘻哈"
                :remote-method="searchOwnerList"
                :loading="loading">
                <el-option
                v-for="item in owner_list"
                :key="item.owner_name"
                :label="item.owner_name"
                :value="item.owner_id">
                </el-option>
            </el-select>
        </el-form-item> -->
        <el-form-item label="券的场景类别" prop="scene" required>
            <el-select v-model="ruleForm.scene" >
                <el-option v-for="item in scene_options" :label="item.label" :value="item.value"></el-option>
            </el-select>
        </el-form-item>
        <el-form-item label="券的种类" prop="coupon_category_id" required>
            <el-select v-model="ruleForm.coupon_category_id" >
                <el-option v-for="item in cate_options" :label="item.label" :value="item.value"></el-option>
            </el-select>
        </el-form-item>
        <el-form-item label="券的面值" prop="coupon_value" required >
            <el-input-number v-model="ruleForm.coupon_value" :min="1" :step="10"></el-input-number>
        </el-form-item>
        <!-- <el-form-item label="券总数" prop="coupon_total_num" required >
            <el-input-number v-model="ruleForm.coupon_total_num" :min="1" :step="10"></el-input-number>
        </el-form-item> -->
        <el-form-item label="个人限领数" prop="coupon_limit_num" required >
            <el-input-number v-model="ruleForm.coupon_limit_num" :min="1" :step="1"></el-input-number>
        </el-form-item>
        <el-form-item label="排序" prop="order">
            <el-input-number v-model="ruleForm.order" :min="0" :step="5"></el-input-number>
        </el-form-item>
        <!-- <el-form-item label="添加时间" prop="addtime" required >
            <div class="block">
                <el-date-picker v-model="ruleForm.addtime" type="datetime" placeholder="选择日期时间"></el-date-picker>
            </div>
        </el-form-item> -->
        <el-form-item label="过期时间" prop="expiretime" required >
            <div class="block">
                <el-date-picker v-model="ruleForm.expiretime" type="datetime" placeholder="选择日期时间"></el-date-picker>
            </div>
        </el-form-item>
        <el-form-item label="是否开启" prop="is_open">
            <el-radio class="radio" v-model="ruleForm.is_open" label="1">是</el-radio>
            <el-radio class="radio" v-model="ruleForm.is_open" label="2">否</el-radio>
        </el-form-item>
        <el-form-item label="是否展示" prop="is_show">
            <el-radio class="radio" v-model="ruleForm.is_show" label="1">是</el-radio>
            <el-radio class="radio" v-model="ruleForm.is_show" label="0">否</el-radio>
        </el-form-item>

        <el-form-item :inline="true" label="券总数" >
            <el-col span="8">
                <el-input-number v-model="ruleForm.code_num" :min="1" :max="500" :step="10"></el-input-number>
            </el-col>
            <el-button type="primary" icon="edit" @click="codecreate">自动生成</el-button>
            <span style="color: red">（注：券总数亦是兑换码生成数，最大不超过500）</span>
        </el-form-item>
        <el-form-item label="兑换码" prop="coupon_code_list"  >
            <el-input type="textarea"  :autosize="{ minRows: 6, maxRows: 20}" v-model="ruleForm.coupon_code_list" ></el-input>
        </el-form-item>
        <el-form-item label="券的范围" prop="coupon_scope" required>
            <el-select v-model="ruleForm.coupon_scope" >
                <el-option v-for="item in coupon_scope_options" :label="item.label" :value="item.value"></el-option>
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
        <el-form-item label="券的描述" prop="coupon_desc" required>
            <el-input type="textarea" :autosize="{ minRows: 6, maxRows: 20}" v-model="ruleForm.coupon_desc" ></el-input>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
            <el-button @click="resetForm('ruleForm')">重置</el-button>
        </el-form-item>
    </el-form>
</div>
</div>
<script>
var school_id = "<?php echo $sid;?>";
var vm = new Vue({
    el: "#app",
    data: {
        labelPosition: 'right',
        loading: false,
        scene_options: [
            {value: '', label: '请选择券的场景类别'},
            {value: '1', label: '报名班制'},
            {value: '2', label: '预约学车'}
        ],
        cate_options: [
            {value: '', label: '请选择券的种类'},
            {value: '1', label: '学车券现金券'},
            {value: '2', label: '学车券打折券'}
        ],
        coupon_scope_options: [
            {value: '', label: '请选择券的范围'},
            {value: '0', label: '全国'},
            {value: '1', label: '全省'},
            {value: '2', label: '全市'},
            {value: '3', label: '地区'}
        ],
        owner_type_options: [
            {value: '1', label: '教练'},
            {value: '2', label: '驾校'},
            {value: '3', label: '嘻哈'}
        ],
        owner_type_option: [
            {value: '1', label: '教练'},
            {value: '2', label: '驾校'},
        ],
        owner_name_options:'',
        owner_list: [],
        ruleForm: {
            id: "<?php echo $id; ?>",
            coupon_name: "<?php echo $coupon_name; ?>",
            owner_type_name: "<?php echo isset($owner_type_name) ? $owner_type_name : ''; ?>",
            owner_type: "<?php echo $owner_type;?>",
            owner_id: "<?php echo $owner_id;?>",
            owner_name: "<?php echo $owner_name; ?>",
            scene: "<?php echo $scene; ?>",
            coupon_category_id: "<?php echo $coupon_category_id; ?>",
            coupon_value: "<?php echo $coupon_value; ?>",
            coupon_limit_num: "<?php echo $coupon_limit_num; ?>",
            expiretime:"<?php echo $expiretime; ?>",
            is_open:"<?php echo $is_open;?>",
            is_show:"<?php echo $is_show;?>",
            order:"<?php echo $order;?>",
            code_num: "<?php echo $coupon_total_num;?>",
            coupon_code_list:'',
            coupon_code:"<?php echo $coupon_code;?>",
            coupon_scope:"<?php echo $coupon_scope;?>",
            coupon_desc:"<?php echo $coupon_desc;?>",
            province_id:"<?php echo $province_id;?>",
            city_id:"<?php echo $city_id;?>",
            area_id:"<?php echo $area_id;?>",
        },
        place: {
            province_list: [],
            city_list: [],
            area_list: []
        },
        rules: {
            coupon_name: [
                { required: true, message: '请填写优惠券名称'}
            ],
            owner_type: [
                { required: true, message: '请选择角色类别'}
            ],
            owner_id: [
                { required: true, message: '请选择角色名称'}
            ],
            scene: [
                { required: true, message: '请选择券的场景类别'}
            ],
            coupon_category_id: [
                { required: true, message: '请选择券的种类'}
            ],
            coupon_value: [
                { required: true, message: '请填写券的面值'}
            ],
            // coupon_total_num: [
            //     { required: true, message: '请填写券的总数量'}
            // ],
            coupon_limit_num: [
                { required: true, message: '请填写个人限领取数量'}
            ],
            expiretime: [
                { required: true, message: '请选择过期时间'}
            ],
            code_num: [
                { required: true, message: '请填写生成兑换码数量'}
            ],
            // coupon_code_list: [
            //     { required: true, message: '请填写兑换码'}
            // ],
            coupon_scope: [
                { required: true, message: '请选择券的范围'}
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
            coupon_desc: [
                { required: true, message: '请填写券的描述'}
            ],
        },
        errorClass: 'error',
        base_url: "<?php echo base_url('coupon/editCouponAjax'); ?>",
        province_url: "<?php echo base_url('school/provinceajax'); ?>",
        city_url: "<?php echo base_url('school/cityajax'); ?>",
        area_url: "<?php echo base_url('school/areaajax'); ?>",
        osearch_url: "<?php echo base_url('coupon/getOwnerAjax'); ?>",
        createcode_url: "<?php echo base_url('coupon/createCode')?>",
    },
    created: function() {
        this.provinceAjax();
        this.cityAjax(this.ruleForm.province_id);
        this.areaAjax(this.ruleForm.city_id);
    },
    methods: {
        editAjax: function(params) {
            if (isNaN(parseInt(params.expiretime))) {
                params.expiretime = parseInt(params.expiretime.getTime()/1000);
            } 
            // if(params.addtime) {
            //     params.addtime = parseInt(params.addtime.getTime()/1000);
            // }
            // if(params.expiretime) {
            //     params.expiretime = parseInt(params.expiretime.getTime()/1000);
            // }
            $.ajax({
                type: 'post',
                url: this.base_url,
                data: params,
                dataType:"json",
                success: function(data) {
                    if(data.code == 200) {
                        parent.layer.closeAll();
                        parent.vm.messageNotice('success', _.get(data, 'msg'));
                        parent.vm.listAjax({"p": parent.vm.currentPage, 's': parent.vm.page_size});
                    } else {
                        vm.messageNotice('warning', data.msg);
                    }
                },
                error: function() {
                    vm.messageNotice('warning', '网络错误，请检查网络');
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
        searchOwnerList: function(query) {
            if (query !== '') {
                this.loading = true;
                $.ajax({
                    type: 'post',
                    url: this.osearch_url,
                    data: {"key": query, 'type': this.ruleForm.owner_type, 'school_id': school_id},
                    dataType: 'json',
                    success:function(data) {
                        vm.loading = false;
                        vm.owner_list = data.data.list;
                    },
                    error: function() {
                        vm.messageNotice('warning', '搜索驾校出现网络错误');
                    }
                });
            } else {
                this.owner_list = [];
            }
        },
        codecreate: function() {
            $.ajax({
                type: 'post',
                url: this.createcode_url,
                data: {"code_num": vm.ruleForm.code_num},
                dataType: 'json',
                success:function(ret) {
                    vm.ruleForm.coupon_code_list = ret.data;
                },
                error: function() {
                    vm.messageNotice('warning', '生成兑换码错误');
                }
            });
        },
        messageNotice: function(type, msg) {
            this.$message({type: type,message: msg});
        },
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    vm.editAjax(vm.ruleForm);
                } else {
                    return false;
                }
            });
        },
        resetForm(formName) {
            this.$refs[formName].resetFields();
        },
    }
});

</script>
