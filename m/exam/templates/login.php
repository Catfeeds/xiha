<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
        <title>登录</title>
        <meta name="Keywords" content="嘻哈学车,科目一,科目四,科目一模拟考试,科目四模拟考试,模拟考试,驾照,考驾照,驾驶员模拟考试">
        <meta name="description" content="嘻哈学车提供2016最新科目一考试和科目四模拟考试，采用公安部2016最先驾校模拟考试，考驾照模拟试题2016，驾校一点通模拟考试c1，驾驶员考试科目一，考驾照、做驾驶员模拟考试试题就来嘻哈学车！">
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/mui.min.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/style.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/font/iconfont/iconfont.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/swiper.min.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/exam/exercise.css" />
        <script src="<?php echo $root_path; ?>/assets/js/mui.min.js"></script>
        <script>
            var root_path = "<?php echo $root_path; ?>";
            var host = "<?php echo HOST; ?>";
            var ctype = "<?php echo $ctype; ?>";
            var stype = "<?php echo $stype; ?>";
            var t = "<?php echo $t; ?>";
            var sid = "<?php echo $sid; ?>";
            var os = "<?php echo $os; ?>";
        	var loginauth = sessionStorage.getItem('loginauth');
		    if(loginauth) {
        		location.href = root_path+"exam/default-"+sid+"-"+ctype+"-"+stype+"-"+os+".html";
		    }
        </script>
        <style type="text/css">
		</style>
    </head>
	<body style="background: #f5f5f5;">
		<?php if($os == 'web') { ?>
		<header class="mui-bar mui-bar-nav">
			<a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
			<h1 class="mui-title" id="school_name">登录注册</h1>
		</header>
		<?php } ?>
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
		                		<div id="forget_pass" class="mui-input-row" style="float:right;margin-right:10px;">					         
					         		忘记密码？
							    </div>
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

	    <script src="<?php echo $root_path; ?>/assets/js/jquery.md5.js"></script>
        <script src="<?php echo $root_path; ?>/assets/js/cookie.min.js"></script>
	    <script src="<?php echo $root_path; ?>/assets/js/exam/login.js?=<?php echo $r; ?>"></script>
	    <script>
            var timestamp = "<?php echo time();?>";
	    	
	    </script>
	  </body> 
  </html>     
