<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">
    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="标题" prop="s_beizhu" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.s_beizhu" placeholder="请输入标题，如：取消订单" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="内容" prop="s_content" required>
                <el-col :span="10">
                    <el-input type="textarea" :autosize="{ minRows: 5, maxRows: 10}" v-model="ruleForm.s_content" placeholder="请输入内容，500字以内"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="用户类型">
                <el-col style="width:50px;">
                    <el-form-item prop="is_coach" >
                        <el-switch on-text="" off-text="" v-model="ruleForm.is_coach" @change="memberType"></el-switch>
                    </el-form-item>
                </el-col>
                <el-col :span="15">
                    <span style="color: #FF4949;">切换到教练或学员(灰色学员，蓝色教练)</span>
                </el-col>
            </el-form-item>

             <el-form-item :label="ruleForm.is_coach ? coachlabel : stulabel" required>
                <el-form-item prop="user_id" v-if="!ruleForm.is_coach">
                    <el-select
                        v-model="ruleForm.user_id"
                        filterable
                        remote
                        clearable
                        placeholder="请输入学员手机号"
                        :remote-method="remoteStudentMethod"
                        :loading="loading">
                        <el-option
                        v-for="item in student_list"
                        :key="item.s_phone"
                        :label="item.s_phone"
                        :value="item.l_user_id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item prop="coach_id" v-else>
                    <el-select
                        v-model="ruleForm.coach_id"
                        filterable
                        remote
                        clearable
                        placeholder="请输入教练手机"
                        :remote-method="remoteCoachMethod"
                        :loading="loading">
                        <el-option
                        v-for="item in coach_list"
                        :key="item.s_phone"
                        :label="item.s_phone"
                        :value="item.l_coach_id">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-form-item>
            <el-form-item label="来源" prop="s_from">
                <el-col :span="10">
                    <el-input v-model="ruleForm.s_from" placeholder="请输入来源"></el-input>
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
            coach_list: [],
            student_list: [],
            ruleForm: {
                s_beizhu: '',
                s_content: '',
                is_coach: false, // memeber_type
                user_id: '',
                coach_id: '',
                s_from: '嘻哈学车',
            },
            rules: {
                s_beizhu: [
                    { required: true, message: '请输入标题', trigger: 'blur' }
                ],
                s_content: [
                    { required: true, message: '请输入内容', trigger: 'blur' }
                ],
                member_id: [
                    { required: true, message: '请输入用户手机', trigger: 'blur' }
                ],
            },
            add_url: "<?php echo base_url('systems/addAjax')?>?type=message",
            ssearch_url: "<?php echo base_url('systems/searchAjax')?>?type=stu",
            csearch_url: "<?php echo base_url('systems/searchAjax')?>?type=coach",
            stulabel: "学员手机",
            coachlabel: "教练手机",
        },
        methods: {
            memberType: function() {
                if(this.ruleForm.is_coach) {
                    this.ruleForm.coach_id = '';
                } else {
                    this.ruleForm.user_id = '';
                }
            },
            remoteStudentMethod: function(query) {
                if (query !== '') {
                    this.loading = true;
                    $.ajax({
                        type: 'post',
                        url: this.ssearch_url,
                        data: {key: query},
                        dataType: 'json',
                        success:function(data) {
                            vm.loading = false;
                            vm.student_list = data.data;
                        },
                        error: function() {
                            vm.messageNotice('warning', '搜索手机号出现网络错误');
                        }
                    });
                } 
            },
            remoteCoachMethod: function(query) {
                if (query !== '') {
                    this.loading = true;
                    $.ajax({
                        type: 'post',
                        url: this.csearch_url,
                        data: {key: query},
                        dataType: 'json',
                        success:function(data) {
                            vm.loading = false;
                            vm.coach_list = data.data;
                        },
                        error: function() {
                            vm.messageNotice('warning', '搜索手机号出现网络错误');
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