<div id="app" v-cloak>
    <div class="iframe-content" style="margin: 30px;">
        <el-steps :active="active" center="true" finish-status="success" style="margin-bottom: 40px;">
            <el-step title="步骤 1"></el-step>
            <el-step title="步骤 2"></el-step>
            <el-step title="步骤 3"></el-step>
            <el-step title="完成"></el-step>
        </el-steps>
        <el-form :model="ruleForm" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <template v-if="active == 1">
                <el-form-item label="">
                    <div id="payPassword_container" class="alieditContainer clearfix" data-busy="0">
                        <div class="i-block" data-error="i_error">
                            <div class="i-block six-password">
                                <input class="i-text sixDigitPassword" id="payPassword_rsainput" v-model="password" type="password" autocomplete="off" required="required" value="" name="payPassword_rsainput" data-role="sixDigitPassword" tabindex="" maxlength="6" minlength="6" aria-required="true">
                                <div tabindex="0" class="sixDigitPassword-box" style="width: 180px;">
                                    <i style="width: 29px; border-color: transparent;" class=""><b style="visibility: hidden;"></b></i>
                                    <i style="width: 29px;"><b style="visibility: hidden;"></b></i>
                                    <i style="width: 29px;"><b style="visibility: hidden;"></b></i>
                                    <i style="width: 29px;"><b style="visibility: hidden;"></b></i>
                                    <i style="width: 29px;"><b style="visibility: hidden;"></b></i>
                                    <i style="width: 29px;"><b style="visibility: hidden;"></b></i>
                                    <span style="width: 29px; left: 0px; visibility: hidden;" id="cardwrap" data-role="cardwrap"></span>
                                </div>
                            </div>
                            <span>请输入6位提现密码( 数字！)</span>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleNextStep">下一步</el-button>
                </el-form-item>
            </template>
            <template v-else-if="active == 2">
                <el-col :span="18">
                    <el-form-item label="">
                        请绑定持卡人本人的银行卡
                    </el-form-item>
                    <el-form-item label="开户名">
                        <el-input v-model="ruleForm.account_user_name" placeholder="请输入开户名"></el-input>
                    </el-form-item>
                    <el-form-item label="卡号">
                        <el-input v-model="ruleForm.account_no" placeholder="请输入卡号"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <!--<el-button type="gray" @click="handlePrevStep(1)" :loading="refreshstatus">上一步</el-button>-->
                        <el-button type="primary" @click="handleNextStep" :loading="refreshstatus">下一步</el-button>
                    </el-form-item>
                </el-col>
            </template>
            <template v-else-if="active == 3">
                <el-col :span="18">
                    <el-form-item label="">
                        请选择银行卡类型
                    </el-form-item>
                    <el-form-item label="卡类型">
                        <el-input v-model="ruleForm.bank_name" placeholder="请输入卡类型 如中国银行（储蓄卡）"></el-input>
                    </el-form-item>
                    <el-form-item label="手机号">
                        <el-input v-model="ruleForm.account_phone" placeholder="请输入银行预留手机号"></el-input>
                    </el-form-item>
                    <el-form-item label="身份证">
                        <el-input v-model="ruleForm.account_identifyid" placeholder="请输入身份证号"></el-input>
                    </el-form-item>
                    <el-form-item label="开户行地址">
                        <el-input v-model="ruleForm.account_address" placeholder="请输入开户行地址"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-checkbox v-model="checked">同意<a href="#">《用户协议》</a></el-checkbox>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="gray" @click="handlePrevStep(2)" :loading="refreshstatus">上一步</el-button>
                        <el-button type="primary" @click="handleNextStep" :loading="refreshstatus">下一步</el-button>
                    </el-form-item>
                </el-col>
            </template>
            <template v-else-if="active == 4">
                <el-col :span="18">
                    <el-form-item label="">
                        请选择获取验证码
                    </el-form-item>
                    <el-form-item label="验证码：" prop="code">
                        <el-input type="验证码" v-model="ruleForm.validate_code" placeholder="输入手机验证码"></el-input>
                        <el-button type="primary" style="position:absolute; top: 0px; right: 0px; z-index:9" @click="handleGetCode" :disabled="disabled">{{ code_text }}</el-button>
                    </el-form-item>
                   
                    <el-form-item>
                        <el-button type="gray" @click="handlePrevStep(3)" :loading="refreshstatus">上一步</el-button>
                        <el-button type="primary" @click="submitForm('ruleForm')" :loading="refreshstatus">完成</el-button>
                    </el-form-item>
                </el-col>
            </template>
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
                account_user_name: '',
                account_no: '',
                bank_name: '',
                account_phone: '',
                validate_code: '',
                account_identifyid: '',
                account_address: '',
            },
            code_text: '获取验证码',
            count_down: 60,
            checked: true, 
            disabled: false,
            active: "<?php echo isset($this->session->addaccount_step) ? $this->session->addaccount_step : 1; ?>",
            password: '',
            refreshstatus: false,
            bank_list: [],            
            base_url: "<?php echo base_url('school/addaccountajax'); ?>",
            pass_url: "<?php echo base_url('school/validatepassajax')?>",
            bankinfo_url: "<?php echo base_url('school/validatebankajax'); ?>",
            phone_url: "<?php echo base_url('school/validatephoneajax'); ?>",
            getcode_url: "<?php echo base_url('school/getcodeajax'); ?>",
            banklist_url: "<?php echo base_url('school/banklistajax'); ?>",
            loading: false,
        },
        created: function() {
            // this.provinceAjax();
            // this.bankconfigAjax();
        },
        methods: {
            addAjax: function(params) {
                if(this.ruleForm.validate_code.trim() == '') {
                    this.messageNotice('warning', '请输入验证码');
                    return false;
                }
                $.ajax({
					type: 'post',
					url: this.base_url,
					data: params,
					dataType:"json",
					success: function(data) {
                        if(data.code == 200) {
                            parent.layer.closeAll();
                            parent.vm.messageNotice('success', data.msg);
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
                        vm.addAjax(this.ruleForm);
                    } else {
                        return false;
                    }
                });
            },
            handlePrevStep: function(active) {
                this.active = active;
            },
            handleNextStep: function() {
                if(this.active == 1) {
                    this.handleValidatePass();
                } else if(this.active == 2) {
                    this.handleBankInfo();
                } else if(this.active == 3) {
                    this.handleValidatePhone();
                }
            },
            handleValidatePass: function() {
                this.refreshstatus = true;
                if(this.password.trim() == '') {
                    this.messageNotice('warning', '请输入提现密码');
                    return false;
                }
                $.ajax({
                    type: 'post',
                    url: this.pass_url,
                    data: {pass: this.password},
                    dataType: 'json',
                    success:function(data) {
                        vm.refreshstatus = false;
                        if(data.code == 200) {
                            vm.active = 2;
                        } else {
                            vm.messageNotice('warning', data.msg);
                            return false;
                        }
                    },
                    error: function() {
                        vm.refreshstatus = false;
                        vm.messageNotice('warning', '网络错误，请检查网络');
                        return false;
                    }
                })
            },
            handleBankInfo: function() {
                this.refreshstatus = true;
                if(this.ruleForm.account_user_name.trim() == '' || this.ruleForm.account_no.trim() == '') {
                    this.refreshstatus = false;
                    this.messageNotice('warning', '请将信息填写完整');
                    return false;
                }
                $.ajax({
                    type: 'post',
                    url: this.bankinfo_url,
                    data: {cardNo: this.ruleForm.account_no},
                    dataType: 'json',
                    success:function(data) {
                        vm.refreshstatus = false;
                        vm.active = 3;
                        if(data.code == 200) {
                            vm.ruleForm.bank_name = data.data.bank_name;
                        } else {
                            vm.messageNotice('warning', data.msg);
                            // return false;
                        }
                    },
                    error: function() {
                        vm.refreshstatus = false;
                        vm.messageNotice('warning', '网络错误，请检查网络');
                    }
                })
            },
            bankconfigAjax: function() {
                this.refreshstatus = true;
                $.ajax({
                    type: 'post',
                    url: this.banklist_url,
                    dataType: 'json',
                    success:function(data) {
                        vm.refreshstatus = false;
                        if(data.code == 200) {
                            vm.bank_list = data.data.list;
                        } else {
                            vm.messageNotice('warning', data.msg);
                            return false;
                        }
                    },
                    error: function() {
                        vm.refreshstatus = false;
                        vm.messageNotice('warning', '网络错误，请检查网络');
                    }
                })
            },
            handleValidatePhone: function() {
                this.refreshstatus = true;
                if(this.ruleForm.bank_name.trim() == '' || this.ruleForm.account_phone.trim() == '' || this.ruleForm.account_identifyid.trim() == '' || this.ruleForm.account_address.trim() == '') {
                    this.refreshstatus = false;
                    this.messageNotice('warning', '请将信息填写完整');
                    return false;
                }
                if(this.checked == false) {
                    this.refreshstatus = false;
                    this.messageNotice('warning', '你未同意用户协议');
                    return false;
                }
                // if(this.ruleForm.account_phone == false) {
                //     vm.refreshstatus = false;
                //     vm.messageNotice('warning', '你未同意用户协议');
                //     return false;
                // }
                this.refreshstatus = false;
                this.active = 4;
            },
            handleGetCode: function() {
                this.code_text = this.count_down+'秒';
                this.disabled = true;
                var timer = setInterval(function() { 
                    if(vm.count_down > 0) { 
                        vm.count_down--;
                        vm.disabled = true;
                        vm.code_text = vm.count_down+'秒';
                    } else {
                        vm.disabled = false;
                        clearInterval(timer);
                        vm.code_text = '重新获取';
                    }
                }, 1000);
                $.ajax({
                    type: 'post',
                    url: this.getcode_url,
                    data: {phone: this.ruleForm.account_phone},
                    dataType: 'json',
                    success:function(data) {
                        vm.refreshstatus = false;
                        if(data.code == 200) {
                            vm.messageNotice('success', data.msg);
                        } else {
                            vm.messageNotice('warning', data.msg);
                        }
                    },
                    error: function() {
                        vm.refreshstatus = false;
                        vm.messageNotice('warning', '网络错误，请检查网络');
                    }
                });  
            },

        }
    });

    // 提现密码
	var payPassword = $("#payPassword_container"),
    _this = payPassword.find('i'),	
	k=0,j=0,
	password = '' ,
	_cardwrap = $('#cardwrap');
	//点击隐藏的input密码框,在6个显示的密码框的第一个框显示光标
	payPassword.on('focus',"input[name='payPassword_rsainput']",function(){
	
		var _this = payPassword.find('i');
		if(payPassword.attr('data-busy') === '0'){ 
		//在第一个密码框中添加光标样式
		   _this.eq(k).addClass("active");
		   _cardwrap.css('visibility','visible');
		   payPassword.attr('data-busy','1');
		}
		
	});	
	//change时去除输入框的高亮，用户再次输入密码时需再次点击
	payPassword.on('change',"input[name='payPassword_rsainput']",function(){
		_cardwrap.css('visibility','hidden');
		_this.eq(k).removeClass("active");
		payPassword.attr('data-busy','0');
	}).on('blur',"input[name='payPassword_rsainput']",function(){
		
		_cardwrap.css('visibility','hidden');
		_this.eq(k).removeClass("active");					
		payPassword.attr('data-busy','0');
		
	});
	
	//使用keyup事件，绑定键盘上的数字按键和backspace按键
	payPassword.on('keyup',"input[name='payPassword_rsainput']",function(e){
	
	var  e = (e) ? e : window.event;
	
	//键盘上的数字键按下才可以输入
	if(e.keyCode == 8 || (e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105)){
			k = this.value.length;//输入框里面的密码长度
			l = _this.size();//6
			
			for(;l--;){
			
			//输入到第几个密码框，第几个密码框就显示高亮和光标（在输入框内有2个数字密码，第三个密码框要显示高亮和光标，之前的显示黑点后面的显示空白，输入和删除都一样）
				if(l === k){
					_this.eq(l).addClass("active");
					_this.eq(l).find('b').css('visibility','hidden');
					
				}else{
					_this.eq(l).removeClass("active");
					_this.eq(l).find('b').css('visibility', l < k ? 'visible' : 'hidden');
					
				}				
			
			if(k === 6){
				j = 5;
			}else{
				j = k;
			}
			$('#cardwrap').css('left',j*30+'px');
		
			}
		}else{
		//输入其他字符，直接清空
			var _val = this.value;
			this.value = _val.replace(/\D/g,'');
		}
	});	
</script>
