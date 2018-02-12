<div id="app" v-cloak>
    <div class="iframe-content">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="优惠券" prop="coupon_id" required>
                <el-col span="6">
                    <el-input v-model="ruleForm.coupon_name" :disabled="true"></el-input>
                <el-col>
                <!-- <el-select v-model="ruleForm.coupon_id" >
                    <el-option v-for="item in coupon_name_options" :label="item.coupon_name" :value="item.coupon_id"></el-option>
                </el-select> -->
            </el-form-item>
            <el-form :inline="true" style="margin-left:24px;">
                <el-form-item label="券兑换码" prop="coupon_code" required >
                    <el-input v-model="ruleForm.coupon_code" ></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" icon="edit" @click="codecreate">自动生成</el-button>
                </el-form-item>
            </el-form>
            <el-form-item>
                <el-button type="primary" @click="submitForm('ruleForm')">编辑保存</el-button>
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
            errorClass: 'error',
			coupon_name_options:'',
            ruleForm: {
				id:"<?php echo $id;?>",
				coupon_id: "<?php echo $coupon_id;?>",
				coupon_name: "<?php echo $coupon_name;?>",
				coupon_code: "<?php echo $coupon_code;?>",
            },
            rules: {
                coupon_code: [
                    { required: true, message: '券兑换码不能为空！'}
                ],
            },
            base_url: "<?php echo base_url('coupon/editCodeAjax'); ?>",
			coupon_name_url:"<?php echo base_url('coupon/getCouponName')?>",
            createcode_url: "<?php echo base_url('coupon/createCode')?>",
            loading: false,
        },
        created: function() {
            // this.getCouponName();
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
							var filter = {"p":parent.vm.currentPage,"kwords":parent.vm.search.kwords,"value":parent.vm.search.value};
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
            codecreate: function() {
                $.ajax({
                    type: 'post',
                    url: this.createcode_url,
                    data: {'code_num': 1},
                    dataType: 'json',
                    success:function(ret) {
                        vm.ruleForm.coupon_code = ret.data;
                    },
                    error: function() {
                        vm.messageNotice('warning', '生成兑换码错误');
                    }
                });
            },
            getCouponName: function() {
                $.ajax({
                    type: 'post',
                    url: this.coupon_name_url,
                    dataType: 'json',
                    success:function(ret) {
                        vm.coupon_name_options = ret.data;
						console.log(ret.data);
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取优惠券列表错误');
                    }
                });
            },
            messageNotice: function(type, msg) {
				this.$message({
					type: type,
					message: msg
				});
			},
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.editAjax(this.ruleForm);
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
