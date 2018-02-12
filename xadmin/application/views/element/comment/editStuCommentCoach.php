<div id="app" v-cloak>
    <div class="iframe-content">
        <el-form :model="ruleForm" ref="ruleForm" label-width="120px" class="demo-ruleForm" :label-position="labelPosition">
            <el-tabs v-model="activeName" type="card" >
                <el-tab-pane label="基本信息" name="first">

                    <el-form-item label="驾校名称" prop="s_school_name" required >
                        <el-input v-model="ruleForm.s_school_name" placeholder="请输入驾校名称" readonly="readonly"></el-input>
                    </el-form-item>

                    <el-form-item label="评价者" prop="s_username" required >
                        <el-input v-model="ruleForm.s_username" placeholder="评价者" readonly="readonly"></el-input>
                    </el-form-item>

                    <el-form-item label="订单号" prop="order_no" required>
                        <el-input v-model="ruleForm.order_no" placeholder="订单号"></el-input>
                    </el-form-item>

                    <el-form-item label="评价入口" prop="type" required >
                        <el-input value="报名班制" placeholder="报名班制" readonly="readonly"></el-input>
                    </el-form-item>

                    <el-form-item label="评价星级" prop="coach_star"  id="coach_star">
                        <el-rate v-model="ruleForm.coach_star" :colors="['#99A9BF', '#F7BA2A', '#FF9900']"></el-rate>
                    </el-form-item>

                    <el-form-item label="评价内容" prop="coach_content" required>
                        <el-input type="textarea" v-model="ruleForm.coach_content"></el-input>
                    </el-form-item>
                    
                </el-tab-pane>
            </el-tabs>
            
            <el-form-item>
                <el-button type="primary" @click="submitForm('ruleForm')">编辑保存</el-button>
            </el-form-item>
        </el-form>
    </div>
</div>

<script>
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            textarea: '',
            ruleForm: {
                id: "<?php echo $id; ?>",
                s_school_name: "<?php echo $s_school_name; ?>",
                s_username: "<?php echo $s_username; ?>",
                order_no: "<?php echo $order_no; ?>",
                type: "<?php echo $type; ?>",
                coach_star: "<?php echo $coach_star; ?>",
                coach_content: "<?php echo $coach_content; ?>",
            },
            base_url: "<?php echo base_url('comment/editajaxStuCommentCoach'); ?>",
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
