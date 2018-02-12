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