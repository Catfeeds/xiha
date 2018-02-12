<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<!--<div class="gx-breadcrumb gx-line">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
				<el-breadcrumb-item>订单管理</el-breadcrumb-item>
				<el-breadcrumb-item>提现申请</el-breadcrumb-item>
			</el-breadcrumb>
		</div>-->

		<el-col :span="12">

			<el-card class="box-card" style="margin-top: 20px; box-shadow: none;">
				<div slot="header" class="clearfix">
					<span style="line-height: 10px;">账户资金<span style="color:#a94442">（当前账户余额小于最低提现金额(0元)限制,不能提现）</span></span>
				</div>
				<div class="text item">
					<el-form label-position="left" inline class="demo-table-expand">
						<el-form-item label="账户名称">
							<span>{{ content }}</span>
						</el-form-item>
						<el-form-item label="可用余额">
							<span style="color: #FF6600 !important; font-size: 20px;">{{ total_price }}</span> 元
						</el-form-item>
					</el-form>
				</div>
			</el-card>

            <el-card class="box-card" style="margin-top: 20px; box-shadow: none;">
				<div slot="header" class="clearfix">
					<span style="line-height: 10px;">结算账户</span>
					<el-button style="float: right; margin-right: 10px;" type="default" @click="myBankAccount">{{ my_account }}</el-button>
					<el-button style="float: right; margin-right: 10px;" type="default" @click="handleAdd">添加银行账户</el-button>
					<!-- <el-button style="float: right; margin-right: 10px;" type="primary" @click="chooseAccount">选择结算账户</el-button> -->
                    <template>
                      <el-select v-model="choosedAccountIndex" @change="chooseBankAccount" @visible-change="handleVisibleChange" placeholder="请选择">
                        <el-option
                          v-for="(item,index) in account_list"
                          :label="item.bank_name"
                          :value="index">
                          <span style="float: left">{{ item.bank_name }}</span>
                          <span style="float: right; color: #8492a6; font-size: 13px">{{ ' ××× '+item.account_no.substr(15) }}</span>
                        </el-option>
                      </el-select>
                    </template>
				</div>
                <div class="text item">
                    <template v-if="hasChoosedAccount">
						<el-form label-position="left" label-width="100px" class="demo-table-expand">
							<el-form-item label="开户名称">
								<span>{{ choosedAccount.account_user_name }}</span>
							</el-form-item>
							<el-form-item label="开户银行">
								<span>{{ choosedAccount.bank_name }}</span>
							</el-form-item>
							<el-form-item label="开户银行省市">
								<span>{{ choosedAccount.account_address }}</span>
							</el-form-item>
                            <el-form-item label="银行账户">
                                <span>{{ choosedAccount.account_no.substr(0,4)+' ×××× ×××× ××× '+choosedAccount.account_no.substr(15) }}</span>
							</el-form-item>
                            <el-form-item label="预留手机">
                                <span>{{ choosedAccount.account_phone.substr(0,3)+' ×××× '+choosedAccount.account_phone.substr(7) }}</span>
							</el-form-item>
						</el-form>
					</template>
					<template v-else>
请从上述列表中选择一个银行账户作为结算账户，或添加一个新的账户
					</template>
				</div>
			</el-card>

			<el-card class="box-card" style="margin-top: 20px; box-shadow: none;">
				<div slot="header" class="clearfix">
					<span style="line-height: 10px;">提现信息</span>
				</div>
				<div class="text item">
					<el-col :span="22">
						<el-form :model='form' :rules="rules" ref="form" label-position="left" label-width="100px">
							<el-form-item label="提现金额" prop="money">
								<el-input placeholder="请输入金额" type="number" min="0" max="10000" step="50" v-model="form.money">
									<template slot="append">元</template>
								</el-input>
								支持输入小数点后两位的数字
							</el-form-item>
							<el-form-item label="备注" prop="beizhu">
								<el-input type="textarea" :rows="1" placeholder="还可输入20字" v-model="form.beizhu"></el-input>
							</el-form-item>
							<el-form-item label="提现密码" prop="password">
								<div id="payPassword_container" class="alieditContainer clearfix" data-busy="0">
									<!--<label for="i_payPassword" class="i-block">支付密码：</label>-->
									<div class="i-block" data-error="i_error">
										<div class="i-block six-password">
											<input class="i-text sixDigitPassword" id="payPassword_rsainput" v-model="form.password" type="password" autocomplete="off" required="required" value="" name="payPassword_rsainput" data-role="sixDigitPassword" tabindex="" maxlength="6" minlength="6" aria-required="true">
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
								<el-button type="primary" @click="onSubmit('form')">提交</el-button>
							</el-form-item>
						</el-form>
					</el-col>
				</div>
			</el-card>

			<!--<el-popover ref="popover4" placement="top-start" width="700" trigger="click">
				<el-card class="box-card" style="margin-top: 20px; box-shadow: none;">
					<div slot="header" class="clearfix">
						<span style="line-height: 10px;">服务费</span>
					</div>
					<div class="text item">
						<el-form label-position="left" label-width="100px" class="demo-table-expand">
							<el-form-item label="当日到账费率">
								单笔金额<br>
								0-10万元(含10万元):0.2% (最低2元，最高25元)
								10万元-500万元(不含10万元):0.025% (无上、下限)
							</el-form-item>
							<el-form-item label="次日到账费率">
								0元 (无上、下限)
							</el-form-item>
							<el-form-item label="限额">
								单笔：5万元
								当日：5万元
							</el-form-item>
						</el-form>
					</div>
				</el-card>
			</el-popover>-->

			<!--<el-card class="box-card" style="margin-top: 20px; box-shadow: none;">
				<el-form label-position="left" label-width="100px" class="demo-table-expand">
					<el-form-item label="服务费">
						<span style="color:#39f; cursor: pointer;" v-popover:popover4>查看收费标准</span>
					</el-form-item>
				</el-form>
			</el-card>-->
		</el-col>
        <template v-if="account_seen">
            <el-col :span="12" style="padding:0px 20px;">
				<el-card class="box-card" style="margin-top: 20px; box-shadow: none;">
					<div class="text item" style="text-align:center; cursor: pointer;" @click="handleAdd">
						<i class="el-icon-plus"></i>
						<span style="font-size: 16px; padding-bottom: 20px;">添加银行账户</span>
					</div>
				</el-card>
                <el-card v-for="item in account_list" class="box-card" style="margin-top: 20px; box-shadow: none; background: #EFF2F7;">
					<div slot="header" class="clearfix">
						<span style="line-height: 10px; font-size: 18px; font-weight: bold;">{{ item.bank_name }}</span>
					</div>
					<div class="text item">
						<span style="font-size: 16px; float:right; padding-bottom: 20px;">{{ item.account_no.substr(0,4)+' ×××× ×××× ××× '+item.account_no.substr(15) }}</span>
                    </div>
                </el-card>
			</el-col>
		</template>
	</div>
</div>
<script>
	Vue.config.devtools = true;
	var vm = new Vue({
		el: '#app',
		data: {
			refreshstatus: false,
			fullscreenLoading: false,
			input: '',
			value: '',
			list_url: "<?php echo base_url('user/listajax'); ?>",
			addaccount_url: "<?php echo base_url('school/addaccount'); ?>",
            accountlist_url: "<?php echo base_url('school/accountlistajax'); ?>",
            accountlist_url: "<?php echo base_url('school/accountlistajax'); ?>",
            withdraw_url: "<?php echo base_url('order/withdrawajax'); ?>",
			account_list: [],
			account_seen: false,
			my_account: '所有账户',
			admin_name: "<?php echo $admin_name; ?>",
			content: "<?php echo $content; ?>",
            total_price: "<?php echo $total_price; ?>",
            hasChoosedAccount: false,
            choosedAccountIndex: '',
            choosedAccount: {
                account_user_name: '',
                bank_name: '',
                account_no: '',
                account_phone: '',
                account_identifyid: '',
                account_address: ''
            },
            form: {
                money: 0,
                beizhu: '提现申请',
                password: '',
                account_user_name: '',
                bank_name: '',
                account_no: '',
                account_phone: '',
                account_identifyid: '',
                account_address: ''
            },
            rules: {
                money: [
                    { required: true, message: '您还没有输入金额', trigger: 'blur' }
                ],
                password: [
                    { required: true, message: '您还没有输入密码', trigger: 'blur' },
                    { len: 6, message: '请输入6位数字密码' }
                ]
            }
		},
		created: function() {
			this.accountlistAjax();
		},
		methods: {
			handleAdd: function(e) {
				this.showLayer(e, '50%', 'lb', this.addaccount_url);
			},
            handleVisibleChange: function(visible) {
                if (visible) {
                    this.accountlistAjax();
                }
            },
			myBankAccount: function(e) {
				this.my_account = '隐藏账户';
                if(this.account_seen) {
                    this.my_account = '所有帐户';
                } else {
                    // 加载获取最新的银行帐户列表
                    this.accountlistAjax();
                }
				this.account_seen = !(this.account_seen);
			},
            chooseBankAccount: function(i) {
                this.choosedAccount = this.account_list[i];
                this.hasChoosedAccount = true;
                // 将选择后的银行账户数据转给form
                this.form.account_address = this.choosedAccount.account_address;
                this.form.account_identifyid = this.choosedAccount.account_identifyid;
                this.form.account_no = this.choosedAccount.account_no;
                this.form.account_phone = this.choosedAccount.account_phone;
                this.form.account_user_name = this.choosedAccount.account_user_name;
                this.form.bank_name = this.choosedAccount.bank_name;
            },
			listAjax: function(page) {
				$.ajax({
					type: 'post',
					url: this.list_url,
					data: {p: page},
					dataType:"json",
					async: true,
                    success: function(data) {
						// setTimeout(function() {
						vm.fullscreenLoading = false;
						// }, 500);
						vm.refreshstatus = false;
						if(data.code == 200) {
							vm.list = data.data.list;
							vm.pagenum = data.data.pagenum;
							vm.count = data.data.count;
							vm.currentPage = page;
						}
					},
					error: function() {
						vm.fullscreenLoading = false;
						vm.refreshstatus = false;
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},

			messageNotice: function(type, msg) {
				this.$message({
					type: type,
					message: msg
				});
			},
			showLayer: function(e, width, offset, content, shade, shadeClose) {
				layer.closeAll();
				if(!arguments[4]) shade = 0.4;
				if(!arguments[5]) shadeClose = false;

				layer.open({
					title: e.currentTarget.getAttribute('data-title')
					,offset: offset //具体配置参考：offset参数项
					,anim: -1
					,type: 2
					,area: [width ,'100%']
					,content: content
					,shade: shade //不显示遮罩
					,shadeClose: shadeClose //不显示遮罩
					,maxmin: true
					,move: false
					,yes: function(){
						layer.closeAll();
					}
				});
			},
			onSubmit: function(formName) {
                this.$refs[formName].validate(function(valid) {
                    if (vm.hasChoosedAccount) {
                        if (valid) {
                            vm.withdrawAjax();
                        } else {
                            return false;
                        }
                    } else {
                        vm.messageNotice('error', '您还没有选择结算账户');
                        return false;
                    }
                });
			},
			withdrawAjax: function() {
                $.ajax({
                    type: 'post',
                    url: this.withdraw_url,
                    dataType: 'json',
                    data: vm.form,
                    async: true,
                    success:function(data) {
                        if(data.code == 200) {
                            vm.messageNotice('success', data.msg);
                        } else {
                            vm.messageNotice('warning', data.msg);
                            return false;
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '网络错误，请检查网络');
                    }
                })
            },
			accountlistAjax: function() {
                $.ajax({
                    type: 'post',
                    url: this.accountlist_url,
                    dataType: 'json',
                    async: true,
                    success:function(data) {
                        if(data.code == 200) {
                            vm.account_list = data.data.list;
                        } else {
                            vm.messageNotice('warning', data.msg);
                            return false;
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '网络错误，请检查网络');
                    }
                })
            },
            chooseBankCard: function(command) {
                console.log(command)
				if(command == 'a') {
					// console.log(e.currentTarget)
				}
			}
		}
	});

	// 密码
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
