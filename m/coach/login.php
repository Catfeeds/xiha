<?php
	require_once '../include/config.php';
//	echo $_SESSION['loginauth'];
//	exit;
	$id = htmlspecialchars($_GET['id']);
	$sid = htmlspecialchars($_COOKIE['sid']);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
		<title></title>
		<link rel="stylesheet" href="../assets/css/mui.min.css" />
		<link rel="stylesheet" href="../assets/css/style.css" />
		<link rel="stylesheet" href="../assets/font/iconfont/iconfont.css" />
		<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
		<script src="../assets/js/cookie.min.js"></script>
		<style type="text/css">
			.mui-table-view-cell:after {left:0px;}
		</style>
		<script>
			var loginauth = localStorage.getItem('loginauth');
//			alert(loginauth);return false;
			if(loginauth != '{}') {
				location.href = "index.php?id=<?php echo $sid; ?>";
			}
		</script>
		<script type="text/javascript">
			//通过config接口注入权限验证配置
            /*
			wx.config({
			    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
			    appId: 'wx1baa85b75b6f2d60', // 必填，公众号的唯一标识
			    timestamp: '<?php echo time();?>', // 必填，生成签名的时间戳
			    nonceStr: '<?php echo $nonceStr;?>', // 必填，生成签名的随机串
			    signature: '<?php echo $signature;?>',// 必填，签名
			    jsApiList: [] // 必填，需要使用的JS接口列表
			});
			//通过ready接口处理成功验证
			wx.ready(function(){
				// config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后
			})i;
            */
		</script>
	</head>
<body style="background: #f5f5f5;">
	<header class="mui-bar mui-bar-nav">
		<a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
		<h1 class="mui-title" id="school_name">登录注册</h1>
	</header>
	<div id="loginauth" class="mui-content" style="">
		<div id="slider" class="mui-slider">
			<div id="sliderSegmentedControl" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted">
				<a class="mui-control-item" href="#item1mobile">登录</a>
				<a class="mui-control-item" href="#item2mobile">注册</a>
			</div>
			<div id="sliderProgressBar" class="mui-slider-progress-bar mui-col-xs-6"></div>
			<div class="mui-slider-group" style="margin-top: 0px;">
				<div id="item1mobile" class="mui-slider-item mui-control-content mui-active">
					<div id="scroll1" class="mui-scroll-wrapper">
						<div class="mui-scroll">
		                  	<form id='login-form' class="mui-input-group">
				                <div class="mui-input-row">
					              <label>手机</label>
					              <input id='user_phone_1' name="user_phone" type="text" class="mui-input" placeholder="请输入手机号" required>
				                </div>
				                <div class="mui-input-row">
						          <label>密码</label>
					              <input id='user_password_1' type="password" name="pass" class="mui-input" placeholder="请输入密码" required>
				                </div>
								<div class="" id="sendorder" style="margin:10px;">
									<button type="button" id='login_btn' style='padding:10px 0px;border:none; background: #00BD9C;' class='mui-btn mui-btn-block mui-btn-red'>登录</button>
								</div>
	                		</form>
						</div>
					</div>
				</div>

				<div id="item2mobile" class="mui-slider-item mui-control-content">
					<div id="scroll2" class="mui-scroll-wrapper">
						<form id='login-form' class="mui-input-group">
			                <div class="mui-input-row">
				              <label>手机</label>
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
								<button type="button" id='register_btn' style='padding:10px 0px;border:none; background: #00BD9C;' class='mui-btn mui-btn-block mui-btn-red'>注册</button>
							</div>
                		</form>
					</div>

				</div>
			</div>
  		</div>
    </div>
    <script src="../assets/js/jquery.md5.js"></script>
    <script src="../assets/js/mui.min.js"></script>
    <script>
    	mui.init({
			swipeBack: false
		});
		(function($$) {

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
			
			//登录
			login_btn.addEventListener('tap', function(e) {
			  	var user_phone_1 = document.getElementById('user_phone_1').value;
			  	var user_password_1 = document.getElementById('user_password_1').value;
			  	if(user_phone_1.trim() == '' || user_password_1.trim() == '') {
			  		$$.toast('请填写登录信息');
			  		return false;
			  	}
				var params = {
					'user_phone_2':user_phone_1.trim(), 
					'pass':md5(user_password_1.trim()), 
					'type':2
				};
//				alert(JSON.stringify(params))
				$$.ajax({
					type:"post",
					url:"<?php echo HOST; ?>/web_login.php",
					async:true,
					data:params,
					dataType:"json",
					success:function(data) {
						if(data.code == 200) {
							localStorage.setItem('loginauth', JSON.stringify(data.data));
							$$.alert('登录成功');
							location.href = "default.php?id=<?php echo $sid; ?>";
//							alert(JSON.stringify(data.data))
						} else {
							$$.toast(data.data);
						}
							
					},
					error:function() {
						$$.toast('网络错误，请检查网络');
					}
				});	
			});
			
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
			  	var sid = "<?php echo $sid; ?>";
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
				    url:"<?php echo HOST; ?>/v2/ucenter/register.php",
				    dataType:"json",
				    data:params,
				    success:function(data) {
				      if(data.code == 200) {
				       	$$.alert('注册成功，请登录');
						location.reload();
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
			        validate_code.value = data.data;
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
