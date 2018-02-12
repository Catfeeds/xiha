<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">
    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="支付方式" prop="pay_type" required>
                <el-select v-model="ruleForm.pay_type" placeholder="请选择支付方式">
                    <el-option label="支付宝" value="1"></el-option>
                    <el-option label="线下" value="2"></el-option>
                    <el-option label="微信" value="3"></el-option>
                    <el-option label="银联" value="4"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="配置中文名" prop="account_name" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.account_name" placeholder="请输入配置中文名，如：支付宝" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="配置英文名" prop="account_slug" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.account_slug" placeholder="alipay"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="银行账户否" prop="is_bank">
                <el-switch
                    v-model="ruleForm.is_bank"
                    on-text=""
                    off-text="">
                </el-switch>
            </el-form-item>
            <el-form-item label="是否开启" prop="is_open">
                <el-switch
                    v-model="ruleForm.is_open"
                    on-text=""
                    off-text="">
                </el-switch>
            </el-form-item>
            <el-form-item label="范围" prop="pay_scope">
                <el-col :span="10">
                    <el-input v-model="ruleForm.pay_scope" placeholder="默认3"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="排序" prop="order">
                <el-col :span="10">
                    <el-input v-model="ruleForm.order" rows="" placeholder="0"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="说明" prop="description">
                <el-col :span="10">
                    <el-input   type="textarea" :autosize="{ minRows: 5, maxRows: 10}" v-model="ruleForm.description" placeholder="请输入说明，如：拥有支付宝账号的用户使用"></el-input>
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
    var is_open = "<?php echo $is_open?>";
    var is_bank = "<?php echo $is_bank?>";
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            ruleForm: {
                id: "<?php echo $id; ?>",
                account_name: "<?php echo $account_name; ?>",
                account_slug: "<?php echo $account_slug; ?>",
                description: "<?php echo $account_description;?>",
                pay_type: "<?php echo $pay_type?>",
                is_open: is_open == 1 ? true : false,
                is_bank: is_bank == 1 ? true : false,
                pay_scope: "<?php echo $pay_scope?>",
                order: "<?php echo $order?>"
            },
            rules: {
                account_name: [
                    { required: true, message: '请输入配置中文名', trigger: 'blur' }
                ],
                account_slug: [
                    { required: true, message: '请输入配置英文名', trigger: 'blur' }
                ],
                pay_type: [
                    { required: true, message: '请选择标签类型', trigger: 'blur' }
                ],
            },
            edit_url: "<?php echo base_url('systems/editAjax')?>?type=pay",

        },
        methods: {
            
            editAjax: function(params) {
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