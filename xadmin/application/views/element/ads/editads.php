<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">

    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="200px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="广告位" prop="scene_id" required>
                <el-select v-model="ruleForm.scene_id" placeholder="请选择广告位">
                    <el-option v-for="item in scene_list" :label="item.title" :value="item.scene"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="广告等级" prop="level_id" required>
                <el-select v-model="ruleForm.level_id" placeholder="请选择广告等级">
                    <el-option v-for="item in level_list" :label="item.level_title" :value="item.level_id"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="广告状态" prop="ads_status" required>
                <el-select v-model="ruleForm.ads_status" placeholder="请选择广告状态">
                    <el-option label="开启" value="1"></el-option>
                    <el-option label="不招租" value="2"></el-option>
                    <el-option label="失效" value="3"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="设备类型" prop="device" required>
                <el-select v-model="ruleForm.device" placeholder="请选择设备类型">
                    <el-option label="苹果" value="1"></el-option>
                    <el-option label="安卓" value="2"></el-option>
                    <el-option label="苹果,安卓" value="3"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="展示类型" prop="resource_type" required>
                <el-select v-model="ruleForm.resource_type" placeholder="请选择展示类型">
                    <el-option label="图片" value="1"></el-option>
                    <el-option label="视频" value="2"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="有效期至" prop="limit_time" required>
                <div class="block">
                    <el-date-picker
                    v-model="ruleForm.limit_time"
                    type="datetime"
                    placeholder="选择日期时间">
                    </el-date-picker>
                </div>
            </el-form-item>
            <el-form-item label="广告名称" prop="title" required>
                <el-col :span="5">
                    <el-input v-model="ruleForm.title" placeholder="如:学员端app启动页面" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="招租广告数" prop="limit_num">
                <el-col :span="5">
                    <el-input v-model="ruleForm.limit_num" placeholder="请输入可招租的广告数，如：5" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="排序" prop="sort_order">
                <el-col :span="5">
                    <el-input v-model="ruleForm.sort_order" placeholder="请输入排序，如：1" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="省" prop="province_id" required>
                <el-select @change="selectedProvince" v-model="ruleForm.province" placeholder="请选择省份">
                    <el-option v-for="item in province_options" :label="item.province" :value="item.provinceid"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="市" prop="city" required>
                <el-select @change="selectedCity" v-model="ruleForm.city" placeholder="请选择城市">
                    <el-option v-for="item in city_options" :label="item.city" :value="item.cityid"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="区" prop="area_id" required>
                <el-select v-model="ruleForm.area" placeholder="请选择区域">
                    <el-option v-for="item in area_options" :label="item.area" :value="item.areaid"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="广告介绍" prop="intro" required>
                <el-col :span="8">
                    <el-input type="textarea" :autosize="{ minRows: 5, maxRows: 10}" v-model="ruleForm.intro" placeholder="请输入广告名称，如：学员端app启动页面" ></el-input>
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
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            loading: false,
            scene_list: [],
            level_list: [],
            province_options: [],
            city_options: [],
            area_options: [],
            ruleForm: {
                id: "<?php echo $id;?>",
                info_id: "<?php echo $info_id;?>",
                scene_id: "<?php echo $scene_id;?>",
                level_id: "<?php echo $level_id;?>", 
                limit_time: "",
                title: "<?php echo $title;?>",
                intro: "<?php echo $intro;?>",
                ads_status: "<?php echo $ads_status;?>",
                limit_num: "<?php echo $limit_num;?>",
                limit_time: "<?php echo $limit_time;?>",
                sort_order: "<?php echo $sort_order;?>",
                province_id: "<?php echo $province_id;?>",
                province: "<?php echo $province;?>",
                city_id: "<?php echo $city_id;?>",
                city: "<?php echo $city;?>",
                area_id: "<?php echo $area_id;?>",
                area: "<?php echo $area;?>",
                device: "<?php echo $device;?>",
                resource_type: "<?php echo $resource_type;?>",
            },
            rules: {
                scene_id: [
                    { required: true, message: '请选择广告位', trigger: 'change' }
                ],
                level_id: [
                    { required: true, message: '请选择广告等级', trigger: 'change' }
                ],
                limit_time: [
                    { required: true, message: '请选择有效期'}
                ],
                title: [
                    { required: true, message: '请输入广告名称', trigger: 'blur' }
                ],
                intro: [
                    { required: true, message: '请输入广告简介', trigger: 'change' }
                ],
                ads_status: [
                    { required: true, message: '请选择广告状态', trigger: 'change' }
                ],
                province_id: [
                    { required: true, message: '请选择省份', trigger: 'change' }
                ],
                city: [
                    { required: true, message: '请选择市', trigger: 'change' }
                ],
                area: [
                    { required: true, message: '请选择地区', trigger: 'change' }
                ],
                device: [
                    { required: true, message: '请选择设备类型', trigger: 'change' }
                ],
                resource_type: [
                    { required: true, message: '请选择展示类型', trigger: 'change' }
                ],
            },
            edit_url: "<?php echo base_url('ads/editAjax')?>?type=ads",
            scene_url: "<?php echo base_url('ads/sceneListAjax')?>",
            level_url: "<?php echo base_url('ads/levelListAjax')?>",
            province_url: "<?php echo base_url('student/getProvince')?>",
            city_url: "<?php echo base_url('student/getCity')?>",
            area_url: "<?php echo base_url('student/getArea')?>",
        },
        created: function() {
            this.sceneListAjax();
            this.levelListAjax();
            this.getProvince();
        },
        methods: {
             getProvince: function() {
                $.ajax({
                    type: 'post',
                    url: this.province_url,
                    dataType: 'json',
                    success:function(ret) {
                        vm.province_options = ret.data.list;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取省份列表错误');
                    }
                });
            },
            getCity: function(province_id) {
                $.ajax({
                    type: 'post',
                    url: this.city_url,
                    data: {"province_id": province_id},
                    dataType: 'json',
                    success:function(ret) {
                        vm.city_options = ret.data.list;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取城市列表错误');
                    }
                });
            },
            getArea: function(city_id) {
                $.ajax({
                    type: 'post',
                    url: this.area_url,
                    data: {"city_id": city_id},
                    dataType: 'json',
                    success:function(ret) {
                        vm.area_options = ret.data.list;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取区域列表错误');
                    }
                });
            },
            selectedProvince: function (province_id) {
                vm.ruleForm.city_id = null;
                this.getCity(province_id);
            },
            selectedCity: function (city_id) {
                vm.ruleForm.area_id = null;
                this.getArea(city_id);
            },
            sceneListAjax: function(){
                $.ajax({
                    type: 'post',
                    url: this.scene_url,
                    dataType: 'json',
                    success: function(data) {
                        is_ok = _.isObject(data) && _.has(data, 'code') && _.get(data, 'code') == 200
                        if (is_ok) {
                            vm.scene_list = data.data;
                        } else {
                            parent.vm.messageNotice('warning', _.get(data, 'msg'));
                        }
                    },
                    error: function(e) {
                        vm.messageNotice('error', '网络出错！');
                    }
                });
            },
            levelListAjax: function(){
                $.ajax({
                    type: 'post',
                    url: this.level_url,
                    dataType: 'json',
                    success: function(data) {
                        is_ok = _.isObject(data) && _.has(data, 'code') && _.get(data, 'code') == 200
                        if (is_ok) {
                            vm.level_list = data.data;
                        } else {
                            parent.vm.messageNotice('warning', _.get(data, 'msg'));
                        }
                    },
                    error: function(e) {
                        vm.messageNotice('error', '网络出错！');
                    }
                });
            },
            editAjax: function(params) {
                if (isNaN(parseInt(params.limit_time))) {
                    params.limit_time = parseInt(params.limit_time.getTime()/1000);
                } 
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