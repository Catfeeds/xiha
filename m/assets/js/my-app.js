// Initialize app
var myApp = new Framework7();

// If we need to use custom DOM library, let's save it to $$ variable:
var $$ = Framework7.$;

// Add view
var mainView = myApp.addView('.view-main', {
  // Because we want to use dynamic navbar, we need to enable it for this view:
  dynamicNavbar: true
});

// Now we need to run the code that will be executed only for About page.
// For this case we need to add event listener for "pageInit" event

// Option 1. Using one 'pageInit' event handler for all pages (recommended way):
//$$(document).on('pageInit', function (e) {
//	// Get page data from event data
//	var page = e.detail.page;
//	if (page.name === 'about') {
//	    // Following code will be executed for page with data-page attribute equal to "about"
//	    myApp.alert('Here comes About page');
//	}
//})

// Option 2. Using live 'pageInit' event handlers for each page
//$$(document).on('pageInit', '.page[data-page="about"]', function (e) {
//// Following code will be executed for page with data-page attribute equal to "about"
//	myApp.alert('Here comes About page');
//})
myApp.onPageInit('preloader', function (page) {
    $$('.demo-indicator').on('click', function () {
        myApp.showIndicator();
        setTimeout(function () {
            myApp.hideIndicator();
        }, 2000);
    });
    $$('.demo-preloader').on('click', function () {
        myApp.showPreloader();
        setTimeout(function () {
            myApp.hidePreloader();
        }, 2000);
    });
    $$('.demo-preloader-custom').on('click', function () {
        myApp.showPreloader('My text...');
        setTimeout(function () {
            myApp.hidePreloader();
        }, 2000);
    });
});

//$$('form.ajax-submit').on('beforeSubmit', function (e) {
//	myApp.showPreloader('正在登陆');
//  setTimeout(function () {
//      myApp.hidePreloader();
//  }, 2000);
//});
	
$$('form.ajax-submit').on('submitted', function (e) {
	
	var phone = $$('#phone').val();
	var pass = $$('#pass').val();
	if(phone.trim() == '') {
		myApp.alert('请填写手机号', '警告！');
		return false;
	}
	if(pass.trim() == '') {
		myApp.alert('请填写密码', '警告！');
		return false;
	}
	myApp.showPreloader('正在登录中...');
//  return false;
    setTimeout(function () {
        myApp.hidePreloader();
    }, 1500);
//	var xhr = e.detail.xhr; // actual XHR object
	var data = JSON.parse( e.detail.data ); // Ajax repsonse from action file
  	if(data.code == 200) {
		myApp.alert('登陆成功','电子教练', function() {
			var s_username = data.data.s_real_name.trim() != '' ? data.data.s_real_name : data.data.s_username; 
			var param = data.data.s_phone+'|'+data.data.identity_id+'|'+data.data.l_user_id+'|'+s_username+'|'+data.data.photo_id;
			location.href = "index.php?token="+encodeURIComponent(param)+"&ia=1";
		});
//		location.href="qrcode.php";
  	} else {
		myApp.alert('登陆失败','电子教练', function() {

		});
  	}
  // do something with response data
});