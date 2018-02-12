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
            <el-form-item label="车辆类型" prop="car_type" required>
                <el-select v-model="ruleForm.car_type" placeholder="请选择车辆类型">
                    <el-option value="1" label="普通车型"></el-option>
                    <el-option value="2" label="加强车型"></el-option>
                    <el-option value="3" label="模拟车型"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="车的种类" prop="car_cate_id" required>
                <el-col :span="11" style="position:relative;">
                    <el-select
                        v-model="ruleForm.car_cate_id"
                        filterable
                        remote
                        clearable
                        placeholder="请输入车种类关键词如大众"
                        :remote-method="remoteCarCateMethod"
                        :loading="loading">
                        <el-option
                        v-for="item in carcate_list"
                        :key="item.name"
                        :label="item.name"
                        :value="item.car_cate_id">
                        </el-option>
                    </el-select>
                </el-col>
                <el-col :span="3" style="margin-left: 10px;">
                    <el-button type="primary" @click="dialogFormVisible = true">添加车种类</el-button>
                    <el-dialog title="添加车种类" v-model="dialogFormVisible">
                        <el-form :model="tagForm" :rules="tagRules" ref="tagForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
                            <el-form-item label="品牌" prop="brand" label-width="100" required>
                                <el-input v-model="tagForm.brand" placeholder="请输入车品牌"></el-input>
                            </el-form-item>
                            <el-form-item label="型号" prop="subtype" label-width="100" required>
                                <el-input v-model="tagForm.subtype" placeholder="请输入车型号"></el-input>
                            </el-form-item>
                        </el-form>
                        <div slot="footer" class="dialog-footer">
                            <el-button @click="resetForm('tagForm')">取 消</el-button>
                            <el-button type="primary" @click="addCarCate('tagForm')">确 定</el-button>
                        </div>
                    </el-dialog>
                </el-col>
            </el-form-item>
            <el-form-item label="车名称" prop="name" required>
                <el-col :span="11">
                    <el-input v-model="ruleForm.name" placeholder="请输入车名称，如：中兴皮卡" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="车牌号" prop="car_no" required>
                <el-col :span="11">
                    <el-input v-model="ruleForm.car_no" placeholder="请输入车牌号，如：贵J1992学"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item :inline="true" label="车辆图片">
                <el-col :span="5">
                    <el-form-item prop="imgurl_one">
                        <el-upload
                            :action="coachcar_imgurl_one"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleOneSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.imgurl_one">
                            <img v-if="ruleForm.coachCarsOne" :src="ruleForm.coachCarsOne" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item prop="imgurl_two">
                        <el-upload
                            :action="coachcar_imgurl_two"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleTwoSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.imgurl_two">
                            <img v-if="ruleForm.coachCarsTwo" :src="ruleForm.coachCarsTwo" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <el-col :span="5">
                    <el-form-item prop="imgurl_three">
                        <el-upload
                            :action="coachcar_imgurl_three"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleThreeSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.imgurl_three">
                            <img v-if="ruleForm.coachCarsThree" :src="ruleForm.coachCarsThree" class="avatar">
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
            errorClass: 'error',
            loading: false,
            dialogFormVisible: false,
            school_list: [],
            ctype_options: [
                {value: 1, label: "普通车型"},
                {value: 2, label: "加强车型"},
                {value: 3, label: "模拟车型"},
            ],
            ruleForm: {
                school_id: school_id,
                name: '',
                car_no: '',
                car_type: '',
                car_cate_id: '',
                imgurl_one: '',
                coachCarsOne: '',
                imgurl_two: '',
                coachCarsTwo: '',
                imgurl_three: '',
                coachCarsThree: '',
            },
            rules: {
                school_id: [
                    { required: true, message: '请选择驾校', trigger: 'blur' }
                ],
                name: [
                    { required: true, message: '请输入车名称', trigger: 'blur' }
                ],
                car_no: [
                    { required: true, message: '请输入车牌号', trigger: 'blur' }
                ],
                car_type: [
                    { required: true, message: '请输入车辆型号', trigger: 'blur' }
                ],
                car_cate_id: [
                    { required: true, message: '请输入车种类', trigger: 'blur' }
                ]
                
            },
            tagForm: { 
                'brand' : '',
                'subtype': ''
            },
            tagRules: { 
                brand: [
                    { required: true, message: '请输入车辆品牌', trigger: 'blur' }
                ],
                subtype: [
                    { required: true, message: '请输入车辆型号', trigger: 'blur' }
                ],
            },
            carcate_list: [],
            add_url: "<?php echo base_url('cars/addAjax')?>?type=coachcar",
            search_url: "<?php echo base_url('school/search')?>",
            searchcate_url: "<?php echo base_url('cars/searchAjax')?>",
            addcarcate_url: "<?php echo base_url('cars/addAjax')?>?type=category",
            coachcar_imgurl_one: "<?php echo base_url('upload/handle?type=coachCarsOne') ?>",
            coachcar_imgurl_two: "<?php echo base_url('upload/handle?type=coachCarsTwo') ?>",
            coachcar_imgurl_three: "<?php echo base_url('upload/handle?type=coachCarsThree') ?>",

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
            remoteCarCateMethod: function(query) {
                if (query !== '') {
                    this.loading = true;
                    $.ajax({
                        type: 'post',
                        url: this.searchcate_url,
                        data: {key: query},
                        dataType: 'json',
                        success:function(data) {
                            vm.loading = false;
                            vm.carcate_list = data.data;
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
            addCarCate: function(formName) {
                this.$refs[formName].validate(function(valid) {
                    if (valid) {
                        $.ajax({
                            type: 'post',
                            url: vm.addcarcate_url,
                            data: vm.tagForm,
                            dataType: 'json',
                            success:function(data) {
                                vm.dialogFormVisible = false;
                                if(data.code == 200) {
                                    vm.messageNotice('success', data.msg);
                                    // var param = {
                                    //     'l_role_id': data.result,
                                    //     's_rolename': vm.tagForm.s_rolename,
                                    // };
                                    // vm.carcate_list.unshift(param);
                                } else {
                                    vm.messageNotice('warning', data.msg);
                                }
                            },
                            error: function() {
                                vm.messageNotice('warning', '网络错误，请检查网络');
                            }
                        });
                    } else {
                        return false;
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