<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px;">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
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
            <!-- <el-form-item label="驾校名称" prop="school_id" v-if="school_id == ''" required>
                <el-select v-model="ruleForm.school_id" clearable placeholder="请选择驾校">
                    <el-option v-for="item in school_list" :label="item.s_school_name" :value="item.l_school_id+'|'+item.s_school_name"></el-option>
                </el-select>
            </el-form-item> -->

            <el-form-item label="评价者" prop="user_id" required>
                <el-select v-model="ruleForm.user_id" clearable placeholder="请选择评价者" @change="orderNoAjax(ruleForm.user_id)">
                    <el-option v-for="item in users_list" :label="item.user_name" :value="item.l_user_id"></el-option>
                </el-select>
                <span style="color: red" v-if="school_id == '' ">亲，您需选择驾校哦</span>
            </el-form-item>

            <el-form-item label="订单号" prop="order_no" required>
                <el-col :span="8">
                    <el-input v-model="ruleForm.order_no" placeholder="请输入订单号"></el-input>
                </el-col>
            </el-form-item>
    
            <el-form-item label="评价入口" prop="type">
                <el-col :span="8">
                    <el-input v-model="ruleForm.type" placeholder="请输入评价入口" :disabled="true"></el-input>
                </el-col>
                <!-- <el-select v-model="ruleForm.type" clearable placeholder="请选择评价入口">
                    <el-option value="1">报名班制</el-option>
                    <el-option value="2">预约学车</el-option>
                </el-select> -->
            </el-form-item>

            <el-form-item label="评价星级" prop="school_star" >
                <el-rate v-model="ruleForm.school_star" :colors="['#99A9BF', '#F7BA2A', '#FF9900']"></el-rate>
            </el-form-item>

            <el-form-item label="评价内容" prop="school_content" required>
                <el-col :span="8">
                    <el-input type="textarea" :autosize="{ minRows: 6, maxRows: 20}" v-model="ruleForm.school_content"></el-input>
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
            loading: false,
            ruleForm: { 
                school_id: school_id,
                user_id: '',
                order_no: '',
                type: '报名班制',
                school_star: '3',
                school_content: '',
            },
            rules: {
                school_id: [
                    { required: true, message: '请选择驾校', trigger: 'blur' }
                ],
                user_id: [
                    { required: true, message: '请选择评价者', trigger: 'blur' }
                ],
                order_no: [
                    { required: true, message: '请输入订单号', trigger: 'blur' }
                ],
                // school_star: [
                //     { required: false, message: '请输入评价星级', trigger: 'blur' }
                // ],
                school_content: [
                    { required: true, message: '请输入评价内容', trigger: 'blur' }
                ],
            },
            dialogFormVisible: false,
            formLabelWidth: '120px',
            content: '',
            showModuleName: false,
            base_url: "<?php echo base_url('comment/addajaxStuCommentSchool'); ?>",
            ssearch_url: "<?php echo base_url('school/search')?>",
            schools_url: "<?php echo base_url('comment/getSchoolList')?>",
            users_url: "<?php echo base_url('comment/getUserList')?>",
            order_url: "<?php echo base_url('comment/getUserOrderNo')?>",
            school_list: [],
            users_list: [],
        },
        created: function() {
            this.usersListAjax({"school_id": this.ruleForm.school_id});
            this.orderNoAjax(this.ruleForm.user_id);
        },
        methods: {
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
                                vm.usersListAjax({"school_id": vm.ruleForm.school_id});
                            }
                        },
                        error: function() {
                            vm.messageNotice('warning', '搜索驾校出现网络错误');
                        }
                    });
                } else {
                    this.usersListAjax({"school_id": this.ruleForm.school_id});
                    this.school_list = [];
                }
            },
            usersListAjax: function(params) {
                $.ajax({
                    type: 'post',
                    url: this.users_url,
                    data: params,
                    dataType: 'json',
                    success: function(data) {
                        if (data.code == 200) {
                            vm.users_list = data.data;
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取用户列表错误');
                    }
                });
            },
            orderNoAjax: function(user_id) {
                $.ajax({
                    type: 'post',
                    url: this.order_url,
                    data: {"user_id": user_id},
                    dataType: 'json',
                    success:function(ret) {
                        if (ret.code == 200) {
                            vm.ruleForm.order_no = ret.data;
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取用户列表错误');
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
            schoolsListAjax: function() {
                $.ajax({
                    type: 'post',
                    url: this.schools_url,
                    dataType: 'json',
                    success:function(ret) {
                        vm.school_list = ret.data.list;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取驾校列表错误');
                    }
                });
            },
            
        }
    });

</script>
