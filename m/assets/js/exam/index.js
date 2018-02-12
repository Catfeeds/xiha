(function($, doc) {
    var ischeck = localStorage.getItem('ischeck') ? localStorage.getItem('ischeck') : 0;
    if(ischeck == 0) {
        document.getElementById('update').style.display = 'initial';
    }

    // 弹出引导页
    var mySwiper = new Swiper('.swiper-container',{
       loop: false,
       autoplay: 3000,
       pagination: '.swiper-pagination',
    });

    $('#close').on('tap', '.closeupdate', function(e) {
        document.getElementById('update').style.display = 'none';
        localStorage.setItem('ischeck', 1);
    });
    
    Cookies.set('ctype', 'C1');
    Cookies.set('stype', '1');
    
    var exam_btn = doc.getElementById('exam-btn');
    //点击选择驾照类型
    $('#car_type').on('tap', '.mui-table-view-cell', function(e) {
        var car_type = this.getAttribute('id');
        var child = doc.querySelector('#'+car_type+' .choose');
        var f_child = doc.querySelector('#'+car_type+' .mui-media-body');
        Cookies.set('ctype', car_type);
        child.className += " choosed";
        f_child.className += " active";
        var siblings = sibling(child.parentNode.parentNode);
        var f_siblings = sibling(f_child.parentNode.parentNode);
        cancelSiblings(siblings, 'choose', 'iconfont choose');
        cancelSiblings(f_siblings, 'mui-media-body', 'mui-media-body choose');
    });
    //点击选择科目选择
    $('#lesson_type').on('tap', '.mui-table-view-cell', function(e) {
        var lesson_id = this.getAttribute('id');
        var lesson_type = this.getAttribute('data-id');
        Cookies.set('stype', lesson_type);
        var l_child = doc.querySelector('#'+lesson_id+' .lesson-span');
        l_child.className += " choosed";
        var l_siblings = sibling(l_child.parentNode);
        cancelSiblings(l_siblings, 'lesson-span', 'lesson-span choose');
    });
    
    //点击开始练习
    exam_btn.addEventListener('tap', function(e) {
        var ctype = Cookies.get('ctype');
        var stype = Cookies.get('stype');
        location.href = root_path+"exam/default-"+sid+"-"+ctype+"-"+stype+"-"+os+".html";
    });

    //关闭底部提示
    var closebannerbtn = document.getElementById('closebanner');
    closebannerbtn.addEventListener('tap', function(e) {
        var tikubanner = document.getElementById('tikubanner');
        if ('object' == typeof tikubanner) {
            tikubanner.style.display = 'none';
        }
    });
})(mui, document);

//找到所有不包括自己的兄弟节点
function sibling( elem ) {
    var r = [];
    var n = elem.parentNode.firstChild;
    for ( ; n; n = n.nextSibling ) {
        if ( n.nodeType === 1 && n !== elem ) {
            r.push( n );
        }
    }
    return r;
}

//取消选中其它兄弟节点
function cancelSiblings( obj, targetClass, newClass ) {
    for (var i = 0; i < obj.length; i++) {
        var id = obj[i].getAttribute('id');
        var child = document.querySelector("#" + id + " ." + targetClass);
//                  console.log(child);
        child.className = newClass;
    }
}

//清除缓存
function clearcache() {
    var index = layer.open({
	    content: '<p style="color:#333; line-height:30px; font-size:1rem;">清除缓存，你的做题记录，收藏以及登录信息等都将全部清除，你确定吗？</p>',
	    btn: ['确定', '<a style="color:#999;">不要</a>'],
	    yes: function(index){
		    localStorage.removeItem('question_list');
		    localStorage.removeItem('qtotal');
		    localStorage.clear();
		    layer.open({
		        content: '<span style="color:#555">清理成功</span>',
		        style: 'background-color:#fff; color:#fff; border:none;',
		        time:2,
		    });
		    document.getElementById('topPopover').className = 'mui-popover';
		    document.querySelector('.mui-backdrop').parentNode.removeChild(document.querySelector('.mui-backdrop'))
	        layer.closeAll();
	    }
	});

}

// 退出成功
function logout() {
    sessionStorage.removeItem('loginauth');
    layer.open({
        content: '<span style="color:#555">退出成功</span>',
        style: 'background-color:#fff; color:#fff; border:none;',
        time:1,
    });        
}

// 清除题库
function clearQues() {
    window.indexedDB.deleteDatabase('questions');
    layer.open({
        content: '<span style="color:#555">退出成功</span>',
        style: 'background-color:#fff; color:#fff; border:none;',
        time:1,
    });
}
