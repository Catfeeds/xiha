<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 30px;">
        <el-col :span="16">
            <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
                <el-form-item label="菜单名称" prop="menu_name" required>
                    <el-col :span="10">
                        <el-input v-model="ruleForm.menu_name" placeholder="请输入菜单名称 如管权限管理"></el-input>
                    <el-col>
                </el-form-item>

                <el-form-item label="菜单描述" prop="m_description" required>
                    <el-col :span="10">
                        <el-input v-model="ruleForm.m_description" placeholder="请输入菜单描述 如管理登录者的权限"></el-input>
                    <el-col>
                </el-form-item>
                <el-form-item label="控制器" prop="m_controller" required>
                    <el-col :span="10">
                        <el-input v-model="ruleForm.m_controller" placeholder="请输入控制器 如admin/index"></el-input>
                    <el-col>
                </el-form-item>
                <el-form-item label="排序" prop="i_order">
                    <el-col :span="10">
                        <el-input v-model="ruleForm.i_order" placeholder="请输入排序 如：50"></el-input>
                    <el-col>
                </el-form-item>
                <el-form-item label="父节点" prop="m_parentid">
                    <el-select v-model="ruleForm.m_parentid" placeholder="请选择父节点" >
                        <el-option v-for="item in menu_list" :label="item.name" :value="item.moduleid"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="菜单类型" prop="m_type_name">
                    <el-radio-group v-model="ruleForm.m_type_name">
                        <el-radio-button label="模块" value="1"></el-radio-button>
                        <el-radio-button label="操作" value="2"></el-radio-button>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="开启状态" prop="m_close">
                    <el-switch
                        v-model="ruleForm.m_close"
                        on-text=""
                        off-text="">
                    </el-switch>
                    <span style="color: red">注：绿色开启 | 灰色关闭</span>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
                    <el-button @click="resetForm('ruleForm')">重 置</el-button>
                </el-form-item>
            </el-form>
        </el-col>
    </div>

</div>

<script>
    var m_type = "<?php echo $m_type?>";
    var m_close = "<?php echo $m_close?>";
    var m_parentid = "<?php echo $m_parentid?>";
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            menu_list: [],
            ruleForm: {
                id: "<?php echo $moduleid;?>",
                menu_name: "<?php echo $m_cname;?>",
                m_controller: "<?php echo $m_controller;?>",
                m_parentid: m_parentid == 0 ? '' : m_parentid,
                m_description: "<?php echo $m_description;?>",
                m_type_name: m_type == 1 ? '模块' : "操作",
                m_close: m_close == 1 ? true : false,
                i_order: "<?php echo $i_order;?>",
            },
            rules: {
                menu_name: [
                    { required: true, message: '请输入菜单名称', trigger: 'blur' }
                ],
                m_description: [
                    { required: true, message: '请输入菜单描述', trigger: 'blur' }
                ],
                m_controller: [
                    { required: true, message: '请输入控制器', trigger: 'blur' }
                ],
                m_type_name: [
                    { required: true, message: '请选择菜单类型', trigger: 'blur' }
                ],
                
            },
            base_url: "<?php echo base_url('admin/editMenuAjax'); ?>",
            list_url: "<?php echo base_url('admin/menuAjax'); ?>",
            loading: false,

        },
        created: function() {
            this.menulistAjax();
        },
        methods: {
            messageNotice: function(type, msg) {
				this.$message({
					type: type,
					message: msg
				});
			},
            menulistAjax: function() {
                $.ajax({
                    // type: "post",
                    url: this.list_url,
                    dataType: "json",
                    async: true,
                    success: function (res) {
                        vm.refreshstatus = false;
                        vm.fullscreenLoading = false;
                        isResOk = _.isObject(res) && _.has(res, 'code') && _.get(res, 'code') == 200;
                        if (isResOk) {
                            vm.menu_list = _.get(res, 'data.list');
                        } else {
                            vm.messageNotice('success', _.get(res, 'msg'));
                        }
                    },
                    error: function (e) {
                        vm.messageNotice('error', '加载出错！');
                    } 
                });
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
            addAjax: function(formName) {
                $.ajax({
                    type: 'post',
                    url: this.base_url,
                    data: this.ruleForm,
                    dataType: 'json',
                    success:function(data) {
                        vm.dialogFormVisible = false;
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
           

        }
    });

</script>
