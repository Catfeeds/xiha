<div id="app" v-cloak>
    <div class="iframe-content">
        <el-form :model="ruleForm" ref="ruleForm" label-width="120px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="驾校名称" prop="s_school_name" required v-if="school_id == ''">
                <el-col span="8">
                    <el-input v-model="ruleForm.s_school_name" placeholder="请输入驾校名称" :disabled="true"></el-input>
                </el-col>
            </el-form-item>

            <el-form-item label="评价者" prop="s_username" required >
                <el-col span="8">
                    <el-input v-model="ruleForm.s_username" placeholder="评价者" :disabled="true"></el-input>
                </el-col>
            </el-form-item>

            <el-form-item label="订单号" prop="order_no" required>
                <el-col span="8">
                    <el-input v-model="ruleForm.order_no" placeholder="订单号" :disabled="true"></el-input>
                </el-col>
            </el-form-item>

            <el-form-item label="评价入口" prop="type" required >
                <el-col span="8">
                    <el-input value="报名班制" placeholder="报名班制" :disabled="true"></el-input>
                </el-col>
            </el-form-item>

            <el-form-item label="评价星级" prop="school_star"  id="school_star">
                <el-col span="8">
                    <el-rate v-model="ruleForm.school_star" :colors="['#99A9BF', '#F7BA2A', '#FF9900']"></el-rate>
                </el-col>
            </el-form-item>

            <el-form-item label="评价内容" prop="school_content" required>
                <el-col span="8">
                    <el-input type="textarea" v-model="ruleForm.school_content" :autosize="{ minRows: 6, maxRows: 20}"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="submitForm('ruleForm')">编辑保存</el-button>
            </el-form-item>
        </el-form>
    </div>
</div>

<script>
    var school_id = "<?php echo $sid?>";
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            textarea: '',
            ruleForm: {
                id: "<?php echo $id; ?>",
                school_id: "<?php echo $l_school_id; ?>",
                user_id: "<?php echo $l_user_id; ?>",
                s_school_name: "<?php echo $s_school_name; ?>",
                s_username: "<?php echo $user_name; ?>",
                order_no: "<?php echo $order_no; ?>",
                type: "<?php echo $type; ?>",
                school_star: "<?php echo $school_star; ?>",
                school_content: "<?php echo $school_content; ?>",
            },
            base_url: "<?php echo base_url('comment/editajaxStuCommentSchool'); ?>",
            activeName: 'first',
        },
        created: function() {
        },
        methods: {
            editAjax: function(params) {
                $.ajax({
					type: 'post',
					url: this.base_url,
					data: params,
					dataType:"json",
					success: function(data) {
                        if(data.code == 200) {
                            parent.layer.closeAll();
                            parent.vm.messageNotice('success', data.msg);
                            parent.vm.listAjax(parent.vm.currentPage);
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
            handleThumbSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.s_thumb = response.data.url;
            },
            handleLicenseSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.s_yyzz = response.data.url;
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
        }
    });

</script>
