function time()
{
  var now= new Date();
  var year=now.getFullYear();
  var month=now.getMonth();
  var date=now.getDate();
  var week=new Array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");
  var echoweek = week[now.getDay()];

  //写入相应id
  document.getElementById("echoData").innerHTML="今天是："+year+"年"+(month+1)+"月"+date+"日"+"&nbsp;&nbsp;"+echoweek;
} 

function passportLogin() {
		engine.client.loginWaittingView();
		var local_username	= document.getElementById('username').value;
		var local_password	= document.getElementById('password').value;
		if(!local_username){
			alert('请输入帐号!');
			document.getElementById('username').focus();
			return false;
		}

		if(!local_password){
			alert('请输入密码!');
			document.getElementById('password').focus();
			return false;
		}
		
		engine.passport.doLogin(local_username,local_password,false);
		return false;
}

function tabDiv(obj,num,len,cn)
{
 var cssname=cn;
 for(var id = 1;id<=len;id++)
 {
  var ss=obj+id;
  var snav =obj+"nav"+id;
  if(id==num){
  try{document.getElementById(ss).style.display="block"}catch(e){};
  try{document.getElementById(snav).className=cssname}catch(e){};
  }else{
  try{document.getElementById(ss).style.display="none"}catch(e){};
  try{document.getElementById(snav).className=""}catch(e){};
  }
 }  
}

jQuery.extend({
	//选项卡
	tab:function(id){
		var O_tab = $("#" + id);
		var O_holder = O_tab.find(".tab-holder");
		var O_panel = O_tab.find(".tab-panel");
		O_holder.find("li").each(function(index){
			$(this).mouseover(function(){
				$(this)
					.addClass("current")
					.siblings().removeClass("current");
				O_panel.children("div").eq(index).siblings().hide();
				O_panel.children("div").eq(index).show();
			});
		});
	},
	
	tab1:function(id){
		var O_tab1 = $("#" + id);
		var O_holder1 = O_tab1.find(".tab1-holder");
		var O_panel1 = O_tab1.find(".tab1-panel");
		O_holder1.find("li").each(function(index){
			$(this).mouseover(function(){
				$(this)
					.addClass("current")
					.siblings().removeClass("current");
				O_panel1.children("div").eq(index).siblings().hide();
				O_panel1.children("div").eq(index).show();
			});
		});
	},
	
	tab2:function(id){
		var O_tab2 = $("#" + id);
		var O_holder2 = O_tab2.find(".tab2-holder");
		var O_panel2 = O_tab2.find(".tab2-panel");
		O_holder2.find("li").each(function(index){
			$(this).mouseover(function(){
				$(this)
					.addClass("current")
					.siblings().removeClass("current");
				O_panel2.children("div").eq(index).siblings().hide();
				O_panel2.children("div").eq(index).show();
			});
		});
	},
	
	scroll_left:function(srcoll_id,scroll_begin_id,scroll_end_id){
		var speed = 10;
		var O_scroll_begin = document.getElementById(scroll_begin_id);
		var O_scroll_end = document.getElementById(scroll_end_id);
		var O_scroll_div = document.getElementById(srcoll_id);
		O_scroll_end.innerHTML = O_scroll_begin.innerHTML;
			function Marquee(){
				if(O_scroll_end.offsetWidth - O_scroll_div.scrollLeft <= 0){
					O_scroll_div.scrollLeft -= O_scroll_begin.offsetWidth;
				}					
				else{
					O_scroll_div.scrollLeft++
				}
			}
		var MyMar = setInterval(Marquee,speed);
		O_scroll_div.onmouseover = function(){clearInterval(MyMar);}
		O_scroll_div.onmouseout = function(){MyMar = setInterval(Marquee,speed);}
	},
	//带按钮的翻动
	u_btn_roll:function(id,b_p,b_n,time,p){
		var O_parent = $("." + p);
		var O_obj = $("#" + id);
		var O_p = $("#" + b_p);
		var O_n = $("#" + b_n);
		var linewidth = O_obj.find("li").width();
		speed = time *1000;
		function marquee(){
			O_obj.animate({
				marginLeft: -linewidth },1000,function(){
				$(this).css({marginLeft:"0px"}).find("li:first").appendTo(this);
			});
		}
		var mar = setInterval(marquee, speed);
		O_parent.hover(function(){
			if(mar){
				clearInterval(mar);
			}},
			function(){
				clearInterval(mar);
				mar = setInterval(marquee, speed);
		});
			
		O_p.click(function(){
			O_obj.stop(true,true);
			O_obj.find("li:last").prependTo(O_obj);
			O_obj.css({marginLeft: -linewidth });
			O_obj.animate({marginLeft: 0},1000);
		});
		O_n.click(function(){
			O_obj.stop(true,true);
			O_obj.animate({
			marginLeft: -linewidth },1000,function(){
				$(this).css({marginLeft:"0px"}).find("li:first").appendTo(this);
			});
		});
	},
	//不带按钮的翻动
	u_roll:function(id,time,lines){
		var O_obj = $("#" + id);
		var linehieght = O_obj.find("li").outerHeight(true) * lines ;
		speed = time *1000;
		function marquee(){
			O_obj.animate({
				marginTop: -linehieght},1000,function(){
					for(i=1;i<=lines;i++){
						//_this.find("li:first").appendTo(_this);
						$(this).css({marginTop:"0px"}).find("li:first").appendTo(this);
					}
				
			});
		}
		if(O_obj.find("li").length > 1){
			var mar = setInterval(marquee, speed);
		}
		
		O_obj.hover(function(){
			if(mar){
				clearInterval(mar);
			}},
			function(){
				clearInterval(mar);
				mar = setInterval(marquee, speed);
		});
	},
	//不带按钮的翻动
	l_roll:function(id,time){
		var O_obj = $("#" + id);
		var linewidth = O_obj.find("li").outerWidth(true);
		var speed = time *1000;
		var l = O_obj.find("li").length;
		O_obj.width(linewidth * l);
		function marquee(){
			O_obj.animate({
				marginLeft: -linewidth},1000,function(){
				$(this).css({marginLeft:"0px"}).find("li:first").appendTo(this);
			});
		}
		if(l > 3){
			var mar = setInterval(marquee, speed);
		}
		O_obj.hover(function(){
			if(mar){
				clearInterval(mar);
			}},
			function(){
				clearInterval(mar);
				mar = setInterval(marquee, speed);
		});
	},
	//幻灯
	slide_player:function(slide_id,time){
		var index = 1;
		var O_slide = $("#" + slide_id);
		var O_list = O_slide.find(".slide-list");
		var O_title = O_slide.find(".slide-title");
		var O_trigger = O_slide.find(".slide-triggers");
		var F_time = time * 1000;
		var len = O_list.find("img").length;
		var html = '';
			
		for(var i = 1; i <= len; i++){
			if(i == 1){
				html += '<span class="current">' + i + '</span>';
			}
			else{
				html += '<span>' + i + '</span>';
			}
		}
		O_trigger.html(html);	//渲染数字触发器
			var loopTime = setInterval(function(){	//循环切换
				slidePlayer(index);
				index++;
				if(index == len){
					index = 0;
				}
			},F_time);
			
		O_trigger.find("span").mouseover(function(){	//鼠标事件
			index = O_trigger.find("span").index(this);
			slidePlayer(index);
		});
		O_trigger.hover(function(){
			if(loopTime){
				clearInterval(loopTime);
			}},
			function(){
				loopTime = setInterval(function(){
					slidePlayer(index);
					index++;
					if(index == len){
						index = 0;
					}},F_time);
		});
			
		function slidePlayer(index){	//定义幻灯播放效果
			O_list.find("li")
			.eq(index).addClass("current")
			.siblings().removeClass("current");
			O_title.find("li")
			.eq(index).show()
			.siblings().hide();
			O_trigger.find("span")
			.eq(index).addClass("current")
			.siblings().removeClass("current");
		}
	},
	trigger_tab:function(obj){
		var obj = $("#" + obj);
		var panel = obj.find(".trigger-panel");
		var trigger = obj.find(".wt");
		var prev = obj.find(".prev");
		var next = obj.find(".next");
		var i = 0;
		var index = 1;
		var n = panel.children("div").length;
		/*var columnar = obj.find(".vote-columnar");
		columnar.each(function(t){
			var p = $(this).siblings(".vote-columnar").find("span").html();
			$(this).find("i").css("height",p);
		})*/		
		var trigger_html = "";
		for (s = 1; s <= n; s++){
			if(s == 1){
				trigger_html += "<i class='current'></i>";
			}
			else{
				trigger_html += "<i></i>";
			}
		}
		trigger.html(trigger_html);
		obj.find(".trigger").width(trigger.outerWidth(true) + prev.outerWidth(true) + next.outerWidth(true));
		var looptrigger = setInterval(player,6000);
		obj.hover(function(){
			if(looptrigger){
				clearInterval(looptrigger);
			}},
			function(){
				looptrigger = setInterval(player,6000);
		});
		
		next.click(function(){
			player();				
		})
		prev.click(function(){
			i--;
			if(i < 0){i = n-1;}
			player_right(i);
			
		})
		function player(){
			i++;
			if(i == n){i = 0;}
			player_left(i);
		}
		function player_left(i){
			panel.children("div").eq(i).show()
			.siblings("div").hide();
			trigger.find("i").eq(i).addClass("current")
			.siblings("i").removeClass("current");
		}
		function player_right(i){
			panel.children("div").eq(i).show()
			.siblings("div").hide();
			trigger.find("i").eq(i).addClass("current")
			.siblings("i").removeClass("current");
		}
	},
	//背投广告
	ad_rp:function(id,time){
		var O_obj = $("#" + id);
		var O_height = O_obj.height();
		speed = time * 1000;
		function rp_play(){
			//O_obj.animate({marginTop: -O_height},1000);
			O_obj.hide();		
		}
		var mar = setTimeout(rp_play,speed);
	},
	//图片切换
	ad_change:function(show_id,trigger_id,c){
		var O_show = $("#" + show_id);
		var O_trigger = $("#" + trigger_id);
		O_trigger.find(c).each(function(index){
			$(this).mouseover(function(){
				if(c == "li"){
					$(this).addClass("current")
					.siblings().removeClass("current");
					O_show.find("li").eq(index).siblings().hide();
					O_show.find("li").eq(index).show();
				}
				else{
					O_show.children().eq(index).siblings().hide();
					O_show.children().eq(index).show();
				}
			})
		})
	},
	//关闭
	hidden:function(id){
		var O_obj = $("#" + id);
		O_obj.hide();
	},
	//设为首页
	set_homepage:function(){　 // 设置首页
		if(document.all){
			document.body.style.behavior = 'url(#default#homepage)';
			document.body.setHomePage('http://www.ahwang.cn');
		}
		else if (window.sidebar) {
			if (window.netscape) {
				try {
					netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
				}
				catch (e) {
					alert("该操作被浏览器拒绝，如果想启用该功能，请在地址栏内输入 about:config,然后将项 signed.applets.codebase_principal_support 值该为true");
				}
			}
			var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
			prefs.setCharPref('browser.startup.homepage', 'http://www.ahwang.cn');
		}
	},
	//窗口广告
	ad_win:function(id){
		var projectionWidth = document.documentElement.clientWidth;
		var projectionHeight = document.documentElement.clientHeight;
		var ad_obj = $("#" + id);
		var ad_objWidth = ad_obj.width();
		var ad_objHeight = ad_obj.height();
		var close_obj = ad_obj.children(".close");
		//获取滚动条高度
		function getScrollTop() {    
		    var scrollPos = 0;     
		    if (typeof window.pageYOffset != 'undefined') {     
		       scrollPos = window.pageYOffset;     
		    }     
		    else if (typeof window.document.compatMode != 'undefined' &&     
		       window.document.compatMode != 'BackCompat') {     
		       scrollPos = window.document.documentElement.scrollTop;     
		    }     
		    else if (typeof window.document.body != 'undefined') {     
		       scrollPos = window.document.body.scrollTop;     
		    }     
		    return scrollPos;    
		}
		ad_obj.css({"top":projectionHeight,"right":0});
		ad_obj.animate({"top":projectionHeight - ad_objHeight},2000);
		if($.browser.msie && (parseInt($.browser.version) == 6)){
			$(window).scroll(function(){
				ad_obj.css("top",projectionHeight - ad_objHeight + getScrollTop());
			});
		}
		close_obj.click(function(){
			//if($.browser.msie && (parseInt($.browser.version) == 6)){
				ad_obj.hide();
			//}
		});
	},
	//切换广告
	ad_switch:function(s_id,b_id,fs_id,fb_id,data_img,data_img1,data_url,data_title,h_s,w_s,h_b,w_b){
		var flash_s = $("#" + s_id);
		var flash_b = $("#" + b_id);
		var path_s = data_img;
		var path_b = data_img1;
		if(path_s==''|| path_b==''){
			flash_s.hide();
			flash_b.hide();
		}else{	
			flash_s.mouseover(function(){
				$(this).hide();
				flash_b.show();
			});
			flash_b.mouseout(function(){
				$(this).hide();
				flash_s.show();
			});
		}
		var i = path_s.lastIndexOf('.');        
		var len = path_s.length;                
		var str = path_s.substring(len,i+1);    
		var exName = "JPG,GIF,PNG";               
		var k = exName.indexOf(str.toUpperCase());
		if(k == -1)                                
		{
			var params = {};
				params.allowscriptaccess = "always";
				params.allownetworking = "all";
			var attributes = {};
				attributes.wmode = "opaque";
			swfobject.embedSWF(path_s, fs_id, w_s, h_s, "9", "", params, attributes);
			swfobject.embedSWF(path_b, fb_id, w_b, h_b, "9", "", params, attributes);
		}else{
			$("#" + fs_id).html("<a href='"+ data_url +"'><img alt='" + data_title + "' width='" + w_s + "' height='" + h_s + "' src='" + path_s + "' /></a>");
			$("#" + fb_id).html("<a href='"+ data_url +"'><img alt='" + data_title + "' width='" + w_b + "' height='" + h_b + "' src='" + path_b + "' /></a>");
		}
	}
});

(function($){
	$.fn.slider = function(o){
	var d = {
		slider:'#slider',				//整个对象
		num:'#slider_num li',			//点击的对象
		pic:'#slider_pic',				//需要切换的对象
		direction:'left',				//滚动方向top,left,right,bottom默认显示可以是包含空值的任何东西
		index:0,						//默认显示第一项,从零开始
		time:3000,						//自动切换的时间,值为0不自动滚动
		movetime:0,					//图片动画时间*
		prev:'#prevbtn',				//上一页按钮*
		next:'#nextbtn'					//下一页按钮*
	};
	var o = $.extend(d,o);
	/*图片切换*/
	var move = function(){
		$(d.num).eq(d.index).addClass('cur').siblings().removeClass('cur');
		var num = $(d.num).eq(d.index).index();
		$(d.pic).css({'position':'relative'});
		if(d.direction =='top'){
			var basic = $(d.slider).height();
			var distance = num*basic;
			$(d.pic).stop(true,true).animate(
				{'top':-distance},
				{duration:d.movetime});
		}else if(d.direction =='left'){
			var basic = $(d.slider).width();
			var distance = num*basic;
			var width =  $(d.num).length*basic;
			$(d.pic).css({'width': width});
			$(d.pic).children('li').css({'float':'left'});
			$(d.pic).stop(true,true).animate(
				{'left':-distance},
				{duration:d.movetime});
		}else{
			$(d.pic).children('li').eq(d.index).stop(true,true).fadeIn(d.movetime).siblings('li').stop(true,true).fadeOut(d.movetime);
		};
	};
	/*自动切换*/
	var automove = function(){
		d.index++;
		if(d.index >= $(d.num).length){
			d.index = 0 ;
		};
		move();
	};
	if(d.time == 0){
		d.time = 1000000000;
	};
	var autotime = setInterval(automove,d.time);
	/*延迟函数*/
	var delay = function(t,fn){//接收两个参数 t延迟时间秒为单位，fn要执行的函数
	var _this = this,//请注意还要个免费的this参数可以让这个delay更完美
	d = setInterval(function(){fn.apply(_this);},t);
    _this.onmouseout = function(){
        clearInterval(d);
		};
	};
	/*鼠标切换*/
	$(d.num).mouseenter(function(){
		if(!$(this).hasClass('cur')){
			d.index = $(this).index();
			delay.apply(this,[500,function(){move()}]);
		};
	});
	/*鼠标悬浮停止切换*/
	$(d.slider).hover(
		function(){
			clearInterval(autotime);
		},function(){
			autotime = setInterval(automove,d.time);
		}
	);
	};
})(jQuery);

  //轮播图
$().slider({
	slider:'.edu_fouce_img_list',					//整个对象
	num:'.edu_fouce_img_list ol li',			//点击的对象
	pic:'.edu_fouce_img_list ul',					//需要切换的对象
	direction:'left',				//滚动方向top,left,默认显示可以是包含空值的任何东西
	index:0,								//默认显示第一项,从零开始
	time:3000,						//自动切换的时间,值为0不自动滚动
	movetime:500				//图片动画时间*
});
