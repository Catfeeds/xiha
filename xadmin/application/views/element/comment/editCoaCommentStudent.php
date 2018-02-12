<div id="app" v-cloak>
    <div class="iframe-content">
        <el-form :model="ruleForm" ref="ruleForm" label-width="120px" class="demo-ruleForm" :label-position="labelPosition">
            <el-tabs v-model="activeName" type="card" >
                <el-tab-pane label="基本信息" name="first">

                    <el-form-item label="教练名称" prop="s_coach_name" required >
                        <el-input v-model="ruleForm.s_coach_name"  readonly="readonly"></el-input>
                    </el-form-item>

                    <el-form-item label="学员" prop="s_username" required >
                        <el-input v-model="ruleForm.s_username" placeholder="学员" readonly="readonly"></el-input>
                    </el-form-item>

                    <el-form-item label="订单号" prop="order_no" required>
                        <el-input v-model="ruleForm.order_no" placeholder="订单号"></el-input>
                    </el-form-item>

                    <!-- <el-form-item label="评价入口" prop="type" required >
                        <el-input value="报名班制" placeholder="报名班制" readonly="readonly"></el-input>
                    </el-form-item> -->

                    <el-form-item label="评价星级" prop="star_num"  id="star_num">
                        <el-rate v-model="ruleForm.star_num" :colors="['#99A9BF', '#F7BA2A', '#FF9900']"></el-rate>
                    </el-form-item>

                    <el-form-item label="评价内容" prop="content" required>
                        <el-input type="textarea" v-model="ruleForm.content"></el-input>
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
                order_no: "<?php echo $order_no; ?>",
                star_num: "<?php echo $star_num; ?>",
                content: "<?php echo $content; ?>",
                l_user_id: "<?php echo $l_user_id; ?>",
                s_username: "<?php echo $s_username; ?>",
                s_coach_name: "<?php echo $s_coach_name; ?>"
            },
            base_url: "<?php echo base_url('comment/editajaxCoaCommentStudent'); ?>",
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
