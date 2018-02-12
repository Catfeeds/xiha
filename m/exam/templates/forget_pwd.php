<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
        <title>忘记密码</title>
        <meta name="Keywords" content="嘻哈学车,科目一,科目四,科目一模拟考试,科目四模拟考试,模拟考试,驾照,考驾照,驾驶员模拟考试">
        <meta name="description" content="嘻哈学车提供2016最新科目一考试和科目四模拟考试，采用公安部2016最先驾校模拟考试，考驾照模拟试题2016，驾校一点通模拟考试c1，驾驶员考试科目一，考驾照、做驾驶员模拟考试试题就来嘻哈学车！">
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/mui.min.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/style.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/font/iconfont/iconfont.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/swiper.min.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/exam/exercise.css" />
        <script src="<?php echo $root_path; ?>/assets/js/mui.min.js"></script>
        <script src="<?php echo $root_path; ?>/assets/js/exam/functions.js"></script>
        <style type="text/css">
		</style>
    </head>

<body style="background: #f5f5f5;">
	<?php if($os == 'web') { ?>
	<header class="mui-bar mui-bar-nav">
		<a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
		<h1 class="mui-title" id="school_name">忘记密码</h1>
	</header>
	<?php } ?>
	<div id="loginauth" class="mui-content" style="">
		<div id="slider" class="mui-slider">
			<!--<div id="sliderSegmentedControl" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
				<a class="mui-control-item" href="#item1mobile">登录</a>
				<a class="mui-control-item" href="#item2mobile">注册</a>
			</div>-->
			<div id="sliderProgressBar" class="mui-slider-progress-bar mui-col-xs-6"></div>
			<div class="mui-slider-group" style="margin-top: 0px;">

				<div id="item2mobile" class="mui-slider-item mui-control-content">
					<div id="scroll2" class="mui-scroll-wrapper">
						<form id='login-form' class="mui-input-group">
			                <div class="mui-input-row">
				              <label>手机号</label>
				              <input id='user_phone_2' name="user_phone" type="text" class="mui-input" placeholder="请输入手机号" required>
			                </div>
			                <div class="mui-input-row">
					          <label>验证码</label>
				              <input id='validate_code' type="text" name="validate_code" class="mui-input" placeholder="请输入验证码" required>
				              <button type="button" id="getcode" style="position: absolute; height: 40px; border:none; right: 0px;">获取验证码</button>
			                </div>
			                <div class="mui-input-row">
					          <label>密码</label>
				              <input id='user_password_2' type="password" name="pass" class="mui-input" placeholder="请输入密码" required>
			                </div>
			                <div class="mui-input-row">
					          <label>再次密码</label>
				              <input id='user_password_repeat_2' type="password" name="pass" class="mui-input" placeholder="请再次输入密码" required>
			                </div>
							<div class="" id="sendorder" style="margin:10px;">
								<button type="button" id='register_btn' style='padding:10px 0px;border:none; background: #00BD9C;' class='mui-btn mui-btn-block mui-btn-red'>重置密码</button>
							</div>
                		</form>
					</div>

				</div>
			</div>
  		</div>
    </div>
    <script>
    	var root_path = "<?php echo $root_path; ?>";
        var host = "<?php echo HOST; ?>";
        var ctype = "<?php echo $ctype; ?>";
        var stype = "<?php echo $stype; ?>";
        var t = "<?php echo $t; ?>";
        var sid = "<?php echo $sid; ?>";
        var os = "<?php echo $os; ?>";
    </script>
    <script src="<?php echo $root_path; ?>/assets/js/jquery.md5.js"></script>
    <script src="<?php echo $root_path; ?>/assets/js/mui.min.js"></script>
    <script src="<?php echo $root_path; ?>/assets/js/cookie.min.js"></script>
    <script>
    	mui.init({
			swipeBack: false
		});
		(function($$) {
			var loginauth = sessionStorage.getItem('loginauth');
	        if(loginauth) {
	        	location.href = root_path+'exam/default-'+sid+'-'+ctype+'-'+stype+'-'+os+'.html';
	        	return false;
	        }
	        
			$$('.mui-scroll-wrapper').scroll({
				indicators: true //是否显示滚动条
			});
			var item2 = document.getElementById('item2mobile');
			var item3 = document.getElementById('item3mobile');
			document.getElementById('slider').addEventListener('slide', function(e) {
				if (e.detail.slideNumber === 1) {
					if (item2.querySelector('.mui-loading')) {
						setTimeout(function() {
							item2.querySelector('.mui-scroll').innerHTML = html2;
						}, 500);
					}
				} else if (e.detail.slideNumber === 2) {
					if (item3.querySelector('.mui-loading')) {
						setTimeout(function() {
							item3.querySelector('.mui-scroll').innerHTML = html3;
						}, 500);
					}
				}
			});
			var sliderSegmentedControl = document.getElementById('sliderSegmentedControl');
			$$('.mui-input-group').on('change', 'input', function() {
				if (this.checked) {
					sliderSegmentedControl.className = 'mui-slider-indicator mui-segmented-control mui-segmented-control-inverted mui-segmented-control-' + this.value;
					//force repaint
					sliderProgressBar.setAttribute('style', sliderProgressBar.getAttribute('style'));
				}
			});
			
			var getcode = document.getElementById('getcode');
		  	var login_btn = document.getElementById('login_btn');
			
			
			//获取验证码
			getcode.addEventListener('tap', function(e) {
				var user_phone_2 = document.getElementById('user_phone_2').value;
				getvalidatecode(this, user_phone_2.trim());
			});
			
			//提交注册
			var register_btn = document.getElementById('register_btn');
			register_btn.addEventListener('tap', function(e) {
			  	var user_phone_2 = document.getElementById('user_phone_2').value;
			  	var validate_code = document.getElementById('validate_code');
			  	var user_password_2 = document.getElementById('user_password_2').value;
			  	var user_password_repeat_2 = document.getElementById('user_password_repeat_2').value;
			  	var sid = Cookies.get('sid') ? Cookies.get('sid') : 0;

				if(!phonevalidate(user_phone_2.trim())) {
				    $$.toast('请输入正确的手机号');
				    return false;
			  	}
			  	if(user_phone_2.trim() == '') {
				    $$.toast('请先填写手机号！');
				    return false; 
			  	}
				if(user_password_2.trim() == '' || user_password_repeat_2.trim() == '') {
					$$.toast('密码不能为空');
					return false;
				}
				if(user_password_repeat_2.trim() != user_password_2.trim()) {
					$$.toast('两次密码输入不相同');
					return false;
				}
				//ajax的使用
				var params = {
					'phone':user_phone_2.trim(), 
					//'pass':md5(user_password_2.trim()), 
					'pass':user_password_2.trim(), 
					'code':validate_code.value,
					'sid':sid
				};
//				alert(JSON.stringify(params))
				$$.ajax({
				    type:"post",
				    url:"<?php echo HOST; ?>/v2/ucenter/resetPass.php",
				    dataType:"json",
				    data:params,
				    success:function(data) {
				      if(data.code == 200) {
				       	$$.alert('修改成功，请返回登录处登录');
						//location.reload();
                    	location.href = root_path+"exam/u/r="+sid+','+ctype+','+stype+','+t+',1,'+os;

						// location.assign('login.php');
				      } else {
				        $$.alert(data.data);
				      }
				    },
				    error:function(XMLHttpRequest, textStatus, errorThrown) {
						document.write(XMLHttpRequest.responseText)
					}
				    // error:function() {
				    //   $$.toast('网络错误,请检查网络！');
				    // }
			  	});

			});
			
			// 获取验证码
			function getvalidatecode(o, user_phone) {
			  	if(!phonevalidate(user_phone)) {
				    $$.toast('请输入正确的手机号');
				    return false;
			  	}
			  	if(user_phone == '') {
				    $$.toast('请先填写手机号！');
				    return false; 
			  	}
			  // 倒计时
			  time(o);
			  $$.ajax({
			    type:"get",
			    url:"<?php echo HOST; ?>/_get_verification_code.php/phone/"+user_phone+"/type/0",
			    dataType:"json",
			    success:function(data) {
			      if(data.code == 200) {
			      	$$.toast('获取成功，请耐心等待');
//			        validate_code.value = data.data;
			      } else {
			        $$.toast(data.data);
			      }
			    },
			    error:function() {
			      $$.toast('网络错误,请重新获取！');
			      getcode.removeAttribute('disabled');
			    }
			  })
			}
			
			// 倒计时
			var wait = 60;
			function time(o) {
			  if (wait == 0) {
			    o.removeAttribute("disabled"); 
			    o.innerHTML="重新获取验证码";
			    wait = 60;
			  } else {
			    o.setAttribute("disabled", true);
			    o.innerHTML="重新发送(" + wait + ")";
			    wait--;
			    setTimeout(function() {
			        time(o);
			    },
			    1000);
			  }
			}
		})(mui);
		
		// 手机号验证
		function phonevalidate(phone) {
		  var reg = /^0?1[3|4|5|8|7|9][0-9]\d{8}$/;
		  if(!reg.test(phone)) {
		    return false;
		  } else {
		    return true;
		  }
		}
		
    </script>
  </body> 
  </html>     
