<div id="app" v-cloak>
    <div class="iframe-content">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px" class="demo-ruleForm" :label-position="labelPosition">
            <el-tabs v-model="activeName" type="card" @tab-click="handleClick">
                <!-- <el-tab-pane label="添加优惠券种类" name="first"> -->
                    <el-form-item label="券种类的名称" prop="cate_name" required >
                        <el-input v-model="ruleForm.cate_name" ></el-input>
                    </el-form-item>
                    <el-form-item label="券种类描述" prop="cate_desc" required >
                        <el-input type="textarea" :rows="3" v-model="ruleForm.cate_desc" ></el-input>
                    </el-form-item>
                    <el-form-item label="券的规则描述" prop="coupon_rule" required >
                        <el-input type="textarea" :rows="3" v-model="ruleForm.coupon_rule" ></el-input>
                    </el-form-item>
                <!-- </el-tab-pane> -->
            </el-tabs>
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
            ruleForm: {cate_name:'',cate_desc:'',coupon_rule:''},
            rules: {
                cate_name: [
                    { required: true, message: '请填写券种类的名称'}
                ],
                cate_desc: [
                    { required: true, message: '请填写券种类描述'}
                ],
                coupon_rule: [
                    { required: true, message: '请填写券的规则描述'}
                ],
            },
            errorClass: 'error',
            base_url: "<?php echo base_url('coupon/addCate'); ?>",
            activeName: 'first',
            loading: false,
        },
        created: function() {
        },
        methods: {
            handleClick(tab, event) {
                // console.log(tab, event);
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
                            var filter = {"p":parent.vm.currentPage,"value":parent.vm.search.value};
                            parent.vm.listAjax(filter);
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
				this.$message({type: type,message: msg});
			},
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.addAjax(this.ruleForm);
                    } else {
                        return false;
                    }
                });
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();
            },
        }
    });

</script>
