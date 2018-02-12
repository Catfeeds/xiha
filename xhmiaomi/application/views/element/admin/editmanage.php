<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 30px;">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">

            <el-form-item label="用户名" prop="content" required>
                <el-col :span="8">
                    <el-input v-model="ruleForm.content" placeholder="请输入管理者用户名，如：超级管理员"></el-input>
                <el-col>
            </el-form-item>

            <el-form-item label="登录账号" prop="name" required>
                <el-col :span="8">
                    <el-input v-model="ruleForm.name" placeholder="请输入登录账号，如：admin"></el-input>
                <el-col>
            </el-form-item>

            <el-form-item label="手机号" prop="phone" required>
                <el-col :span="8">
                    <el-input v-model="ruleForm.phone" placeholder="请输入管理者手机号，如：18756004206"></el-input>
                <el-col>
            </el-form-item>
            <el-form-item label="密码" prop="password">
                <el-col :span="8" style="position:relative;">
                    <el-input :type="pass" v-model="ruleForm.password" placeholder="请输入密码，如123456"></el-input>
                    <i class="iconfont" :class="iconfont" style="position:absolute; top: 0px; right: 10px; font-size: 2rem; color: #999;" @click="handleIconClick"></i> 
                </el-col>
                <el-col :span="11" style="margin-left: 10px;">
                    <el-button type="danger" @click="initPass">初始密码</el-button>
                </el-col>
            </el-form-item>

            <el-form-item label="所属角色" prop="role_info" required>
                <el-col :span="8" style="position:relative;">
                    <el-select v-model="ruleForm.role_info" clearable placeholder="请选择角色">
                        <el-option v-for="item in roles_list" :label="item.s_role_name" :value="item.l_role_id+'|'+item.s_role_name"></el-option>
                    </el-select>
                </el-col>
                <el-col :span="11" style="margin-left: 10px;">
                    <el-button type="danger" @click="dialogFormVisible = true">添加角色</el-button>

                    <el-dialog title="添加角色" v-model="dialogFormVisible">
                        <el-form :model="tagForm" :rules="tagRules" ref="tagForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
                            <el-form-item label="角色名称" prop="s_role_name" label-width="100" required>
                                <el-input v-model="tagForm.s_role_name" placeholder="请输入角色名称 如后台编辑"></el-input>
                            </el-form-item>
                            <el-form-item label="角色描述" prop="s_description" label-width="100" required>
                                <el-input v-model="tagForm.s_description" placeholder="请输入角色描述"></el-input>
                            </el-form-item>
                        </el-form>
                        <div slot="footer" class="dialog-footer">
                            <el-button @click="resetForm('tagForm')">取 消</el-button>
                            <el-button type="primary" @click="addRole('tagForm')">确 定</el-button>
                        </div>
                    </el-dialog>
                </el-col>
            </el-form-item>
            
            <el-form-item label="是否开启">
                <el-switch on-text="" off-text="" v-model="ruleForm.is_close"></el-switch>
            </el-form-item>

            <el-form-item>
                <el-button type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
                <el-button @click="resetForm('ruleForm')">重 置</el-button>
            </el-form-item>
        </el-form>
    </div>

</div>

<script>
    var is_close = "<?php echo $is_close;?>";
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            textarea: '',
            ruleForm: {
                id: "<?php echo $id;?>",
                content: "<?php echo $content;?>",
                name: "<?php echo $name;?>",
                password: "",
                phone: "<?php echo $phone;?>",
                is_close: is_close == 1 ? true : false,
                role_info: "<?php echo $s_role_name;?>",
            },
            rules: {
                content: [
                    { required: true, message: '请输入用户名', trigger: 'blur' },
                ],
                name: [
                    { required: true, message: '请输入真实姓名', trigger: 'blur' },
                ],
                phone: [
                    { required: true, message: '请输入管理者手机号', trigger: 'blur' },
                    { min: 11, max: 11, message: '号码格式错误', trigger: 'blur'},
                ],
                role_info: [
                    { required: true, message: '请选择所属角色', trigger: 'change' }
                ],
                
            },
            tagForm: {
                s_role_name: '',
                s_description: '',
            },
            tagRules: {
                s_role_name: [
                    { required: true, message: '请输入角色名称', trigger: 'blur' }
                ],
                s_description: [
                    { required: true, message: '请输入角色描述', trigger: 'blur' }
                ],
            },
            roles_list: [],
            pass: 'password',
            iconfont: 'icon-yanjing2',
            base_url: "<?php echo base_url('admin/editManageAjax'); ?>",
            rolelist_url: "<?php echo base_url('admin/roleAjax'); ?>",
            addrole_url: "<?php echo base_url('admin/addroleajax'); ?>",
            activeName: 'first',
            loading: false,
            dialogFormVisible: false,

        },
        created: function() {
            this.handleRoleListAjax();
        },
        methods: {
            initPass: function() {
                this.ruleForm.password = 'xiha123456';
            },
            handleIconClick: function(ev) {
                if(this.iconfont == "icon-yanjing") {
                    this.iconfont = 'icon-yanjing2';
                    this.pass = 'password';
                } else {
                    this.iconfont = 'icon-yanjing';
                    this.pass = 'text';
                }
            },
            editAjax: function(params) {
                $.ajax({
					type: 'post',
					url: this.base_url,
					dataType:"json",
					data: params,
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
            handleRoleListAjax: function() {
                $.ajax({
					type: 'post',
					url: this.rolelist_url,
					dataType:"json",
					success: function(data) {
                        if(data.code == 200) {
                            vm.roles_list = data.data;
                            // vm.messageNotice('success', data.msg);
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
            addRole: function(formName) {
                this.$refs[formName].validate(function(valid) {
                    if (valid) {
                        $.ajax({
                            type: 'post',
                            url: vm.addrole_url,
                            data: vm.tagForm,
                            dataType: 'json',
                            success:function(data) {
                                vm.dialogFormVisible = false;
                                if(data.code == 200) {
                                    vm.messageNotice('success', data.msg);
                                    var param = {
                                        'l_role_id': data.result,
                                        's_rolename': vm.tagForm.s_rolename,
                                    };
                                    vm.roles_list.unshift(param);
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
           

        }
    });

</script>
