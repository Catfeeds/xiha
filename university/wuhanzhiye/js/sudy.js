// JavaScript Document
//日期
function getCalendar() {
var calendar = new Date();
var day = calendar.getDay();
var month = calendar.getMonth();
var date = calendar.getDate();
var year = calendar.getFullYear()
if (year< 200) {
	year = 1900 + year;
}
var cent = parseInt(year/100);
var g = year % 19;
var k = parseInt((cent - 17)/25);
var i = (cent - parseInt(cent/4) - parseInt((cent - k)/3) + 19*g + 15) % 30;
i = i - parseInt(i/28)*(1 - parseInt(i/28)*parseInt(29/(i+1))*parseInt((21-g)/11));
var j = (year + parseInt(year/4) + i + 2 - cent + parseInt(cent/4)) % 7;
var l = i - j
var emonth = 3 + parseInt((l + 40)/44);
var edate = l + 28 - 31*parseInt((emonth/4));
emonth--;
														
var dayname = new Array ("星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六");
var monthname =new Array ("1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月" );
return ( '今天是 '+year+'年'+monthname[month]+date + '日 '+ dayname[day]);
}
document.getElementById("dateTime").innerHTML = getCalendar();
//新闻切换
function tab(m,n) { 
    var tli=document.getElementById("tabmenu"+m).getElementsByTagName("li"); 
    var lli=document.getElementById("tablist"+m).getElementsByTagName("li");
    var mli=document.getElementById("tabmore"+m).getElementsByTagName("span"); 
    for(i=0;i<tli.length;i++){ 
        tli[i].className=i==n?"hover":""; 
        lli[i].style.display=i==n?"block":"none";
        mli[i].style.display=i==n?"block":"none";
    } 
}
//专题样式
function zT(){
var zTd = document.getElementById("zt").getElementsByTagName("td");
if (zTd.length > 0) {
    var i, j, k;
    var zLink = [];
    for (i = 0, j = 0; i < zTd.length; i++) {
	    if (zTd[i].className == "zlink") {
	        zLink[j] = zTd[i];
		    j++;    
	    }    
    }
    for (k = 0; k < zLink.length; k++) {
        if (k%2 == 0) {
	        zLink[k].className += " eve";
	    }	
    }
}
}
//横向导航
function menu() {
     document.getElementById("menu1").style.cssText = "top:36px; right:0px; z-index:1000; visibility:visible;";
}