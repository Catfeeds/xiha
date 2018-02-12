mui.init({
	swipeBack: false
});
(function($$) {
//	var loginauth = localStorage.getItem('loginauth');
	var loginauth = sessionStorage.getItem('loginauth');
    if(loginauth) {
    	location.href = root_path+'exam/index-'+sid+'-'+os+'.html';
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
		$$.ajax({
			type:"post",
			url:host+"/web_login.php",
			async:true,
			data:params,
			dataType:"json",
			success:function(data) {
				if(data.code == 200) {
//					localStorage.setItem('loginauth', JSON.stringify(data.data));
					sessionStorage.setItem('loginauth', JSON.stringify(data.data));
					$$.alert('登录成功');
                	location.href = root_path+"exam/exercise-"+sid+"-"+ctype+"-"+stype+"-"+t+"-1"+"-"+os+".html";
				} else {
					$$.toast(data.data);
				}
					
			},
			error:function() {
				$$.toast('网络错误，请检查网络');
			}
		});	
	});
	
	//忘记密码
	var forget_pass = document.getElementById('forget_pass');
	forget_pass.addEventListener('tap', function(e){
		location.href = root_path+"exam/forget/r="+sid+','+ctype+','+stype+','+t+',1,'+os;
		return false;
	})
	
	//获取验证码
	getcode.addEventListener('tap', function(e) {
		var timestamp = Date.now()/1000|0;
		var sign = (0x1000000+(Math.random())*0xffffff).toString(16).substr(1,6);
		var user_phone_2 = document.getElementById('user_phone_2').value;
		getvalidatecode(this, user_phone_2.trim(), sign, timestamp);
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
		var params = {
			'phone':user_phone_2.trim(), 
			//'pass':md5(user_password_2.trim()), 
			'pass':user_password_2.trim(), 
			'code':validate_code.value,
			'sid':sid
		};
		$$.ajax({
		    type:"post",
		    url:host+"/v2/ucenter/register.php",
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
//		    error:function(XMLHttpRequest, textStatus, errorThrown) {
//				document.write(XMLHttpRequest.responseText)
//			}
	     	error:function() {
		       $$.toast('网络错误,请检查网络！');
		       return false;
	     	}
	  	});

	});
	
	// 获取验证码
	function getvalidatecode(o, user_phone, sign, timestamp) {
	  	if(!phonevalidate(user_phone)) {
		    $$.toast('请输入正确的手机号');
		    return false;
	  	}
	  	if(user_phone == '') {
		    $$.toast('请先填写手机号！');
		    return false; 
	  	}
	  	var params = {
	  		'phone' : user_phone,
	  		'type' : 0,
	  		'sign' : sign,
	  		'timestamp' : timestamp,
	  	};
	  // 倒计时
	  time(o);
	  $$.ajax({
	    type:"get",
	    url:host+"/_get_verification_code.php",
	    data:params,
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
