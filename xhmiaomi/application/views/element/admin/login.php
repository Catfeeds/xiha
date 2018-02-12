<div id="app" v-cloak>
	<p style="margin: 10px 150px;">
		<img style="position: relative; width: 200px;" src="<?php echo base_url('assets/element/images/logo.png'); ?>" alt="">
	</p>
	<div class="" style="position: relative; height: 600px; ">
		<img style="position: relative; width: 100%; height: 600px;" src="<?php echo base_url('assets/element/images/login-bg.png'); ?>" alt="">
		<el-row style="position: absolute; height: 600px; top: 0px; left: 0px; width: 100%;">
			<el-col :span="14" style="text-align:center; height: 600px;">
				<a href="http://www.xihaxueche.com" target="_blank"><img style="margin-top: 60px;border: 1px solid #ccc; border-radius: 100%; width: 570px; height: 474px" src="<?php echo base_url('assets/element/images/login-banner-01.png'); ?>" alt=""></a>
			</el-col>
			<el-col :span="10" style="height: 600px;">
				<el-card class="box-card" style="margin-top: 125px; height: 350px; width: 400px; border: none;">
					<p style="font-size:26px; text-align:center; margin-bottom: 30px;">喵咪鼠标管理平台</p>
					<el-col :span="22">
						<el-form ref="form" :model="form" :rules="rules" label-width="100px">
						<el-form-item label="登录名：" prop="name">
							<el-input v-model="form.name" placeholder="请输入登录名称"></el-input>
						</el-form-item>
						<el-form-item label="密　码：" prop="pass">
							<el-input type="password" v-model="form.pass" placeholder="请输入登录密码"></el-input>
						</el-form-item>
						<el-form-item label="验证码：" prop="code">
							<el-input type="验证码" v-model="form.code" placeholder="请输入验证码"></el-input>
							<img :src="captcha_src" style="position:absolute; top: 3px; right: 3px; z-index:9" @click="handleCaptchaClick" alt="">
						</el-form-item>
						<el-form-item>
							<el-button type="primary" :loading="refreshstatus" @click="submitForm('form')">登 录</el-button>
						</el-form-item>
						</el-form>
					</el-col>
				</el-card>
			</el-col>
		</el-row>
	</div>
	<div class="footer">  
		<p>©2015 安徽嘻哈网络技术有限公司　 皖ICP备15016679号 　公司地址 ： 合肥市高新区望江西路800号国家创新产业园C3栋</p>
		<p>客服热线：0551-65610256 0551-65610257 0551-65610258</p>
	</div>
</div>
<script>
	var vm = new Vue({
		el: '#app',
		data: {
			captcha_url: "<?php echo base_url('admin/captchaajax')?>",
			captcha_src: "<?php echo $img_url; ?>",
			login_url: "<?php echo base_url('admin/loginajax') ?>",
			form: {
				name: '',
				pass: '',
				code: '',
			},
			refreshstatus: false,
			iconfont: 'icon-yanjing2',
			rules: {
				name: [
                    { required: true, message: '请输入登录名称', trigger: 'blur' },
                ],
                pass: [
                    { required: true, message: '请输入登录密码', trigger: 'blur' },
                ],
                code: [
                    { required: true, message: '请输入验证码', trigger: 'blur' },
                ],
			}
		},
		methods: {
			submitForm: function(formName) {
				this.refreshstatus = true;
				this.$refs[formName].validate(function(valid) {
                    if (valid) {
                        vm.loginAjax();
                    } else {
						vm.refreshstatus = false;
                        return false;
                    }
                });
			},
			loginAjax: function() {
				$.ajax({
					type: 'post',
					url: this.login_url,
					data: this.form,
					dataType: 'json',
					success: function(data) {
						vm.refreshstatus = false;
						if(data.code == 200) {
	                        vm.messageNotice('success', data.msg);
	                        location.reload();
						} else {
                	        vm.messageNotice('warning', data.msg);
						}
					},
					error: function() {
						vm.refreshstatus = false;
                        vm.messageNotice('warning', '登录失败');
					}
				})
			},
			handleCaptchaClick: function() {
				this.captchaAjax();
			},
			captchaAjax: function() {
				$.ajax({
					type: 'get',
					url: this.captcha_url,
					dataType: 'json',
					success: function(data) {
						if(data.code == 200) {
							vm.captcha_src = data.data.img_url;
						}
					},
					error: function() {
                        vm.messageNotice('warning', '网络错误，获取验证码错误');
					}
				})
			},
			messageNotice: function(type, msg) {
				this.$message({
					type: type,
					message: msg
				});
			},

		}
	})
</script>