//admin.js

// z左侧导航点击事件

function redirecturl(c, a, obj) {
	var redirecturl = "index.php?action="+c+"&op="+a;
	$('#rightMain').attr('src', redirecturl); 
	$(obj).parent().addClass('active').siblings().removeClass('active');
	$('.second_nav').html($(obj).html());
}

// 主导航点击事件
function redirecttopurl(c, a, obj) {
	var redirecturl = "index.php?action="+c+"&op="+a;
	$('#rightMain').attr('src', redirecturl); 
	$(obj).parents('li').addClass('active').siblings().removeClass('active');
	$('.nav_crumbs').html($(obj).html());
}


