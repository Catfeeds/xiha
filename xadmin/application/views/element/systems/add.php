<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">
    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="标签类型" prop="user_type" required>
                <el-select v-model="ruleForm.user_type" placeholder="请选择标签类型">
                    <el-option label="学员" value="1"></el-option>
                    <el-option label="教练" value="2"></el-option>
                    <el-option label="驾校" value="3"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="标签中文名" prop="tag_name" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.tag_name" placeholder="请输入标签中文名，如：通过率高" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="标签英文名" prop="tag_slug" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.tag_slug" placeholder="请输入标签英文名，如：tongguoxiaolvgao"></el-input>
                </el-col>
            </el-form-item>
            
            <el-form-item label="排序" prop="order">
                <el-col :span="10">
                    <el-input v-model="ruleForm.order" rows="" placeholder="0"></el-input>
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
            ruleForm: {
                tag_name: '',
                tag_slug: '',
                user_type: '',
                order: 0
            },
            rules: {
                tag_name: [
                    { required: true, message: '请输入标签中文名', trigger: 'blur' }
                ],
                tag_slug: [
                    { required: true, message: '请输入标签英文名', trigger: 'blur' }
                ],
                user_type: [
                    { required: true, message: '请选择标签类型', trigger: 'blur' }
                ],
            },
            add_url: "<?php echo base_url('systems/addAjax')?>?type=tag",


        },
        methods: {
            
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