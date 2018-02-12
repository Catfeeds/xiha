
//根据经纬度获取当前定位的具体地址
function _getCurrentPosition(lng, lat, show_id) {
	//逆地理编码(获得具体地址)
    AMap.service('AMap.Geocoder',function(){//回调函数
        //实例化Geocoder
        geocoder = new AMap.Geocoder({
            city: "010"//城市，默认：“全国”
        });
        //TODO: 使用geocoder 对象完成相关功能
        var lnglatXY=[lng, lat];//地图上所标点的坐标
        geocoder.getAddress(lnglatXY, function(status, result) {
            if (status === 'complete' && result.info === 'OK') {
               	//获得了有效的地址信息:
               	//即，result.regeocode.formattedAddress
               	document.getElementById(show_id).innerHTML = result.regeocode.formattedAddress;
            }else{
               //获取地址失败
               document.getElementById(show_id).innerHTML = '定位失败';
            }
        });
    })
}

//获取经纬度
function getPositionLngLat(object_id) {
	var map, geolocation;
	//加载地图，调用浏览器定位服务
	map = new AMap.Map(object_id, {
	    resizeEnable: true
	});
	map.plugin('AMap.Geolocation', function() {
	    geolocation = new AMap.Geolocation({
	        enableHighAccuracy: true,//是否使用高精度定位，默认:true
	        timeout: 10000,          //超过10秒后停止定位，默认：无穷大
	        buttonOffset: new AMap.Pixel(10, 20),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
	        zoomToAccuracy: true,      //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
	        buttonPosition:'RB'
	    });
	    map.addControl(geolocation);
	    geolocation.getCurrentPosition();
	    
	    AMap.event.addListener(geolocation, 'complete', onComplete);//返回定位信息
	    AMap.event.addListener(geolocation, 'error', onError);      //返回定位出错信息
	});
}
//解析定位结果
function onComplete(data) {
//  var str=['定位成功'];
//  str.push('经度：' + data.position.getLng());
//  str.push('纬度：' + data.position.getLat());
//  str.push('精度：' + data.accuracy + ' 米');
//  str.push('是否经过偏移：' + (data.isConverted ? '是' : '否'));
//  console.log({'lng':data.position.getLng(),'lat':data.position.getLat()});
	document.getElementById('location_info').innerHTML = JSON.stringify({'lng':data.position.getLng(),'lat':data.position.getLat()});
}
//解析定位错误信息
function onError(data) {
	document.getElementById('location_info').innerHTML = JSON.stringify({'code':404, 'msg':'定位失败'});
}

//定位城市名称
function showCityInfo(object_id, second_id) {
    //实例化城市查询类
    var citysearch = new AMap.CitySearch();
    //自动获取用户IP，返回当前城市
    citysearch.getLocalCity(function(status, result) {
        if (status === 'complete' && result.info === 'OK') {
            if (result && result.city && result.bounds) {
                var cityinfo = result.city;
                var citybounds = result.bounds;
//              console.log({'lng':result.bounds.Ua.lng,'lat':result.bounds.Ua.lat});
                //地图显示当前城市
//				$.toast("定位当前城市："+cityinfo);
                document.getElementById(object_id).innerHTML = cityinfo;
                if(second_id.trim() != '') {
	                document.getElementById(second_id).innerHTML = cityinfo;
                }
                if(!localStorage.getItem('cityid')) {
	               	$.ajax({
						type:"get",
	//					url:"http://192.168.100.8/php/api/get_city_name.php/cityname/"+cityinfo,
						url:"http://api2.xihaxueche.com:8001/api/_get_city_name.php/cityname/"+cityinfo,
						async:true,
						dataType : 'jsonp',  
	        			jsonp:"jsoncallback", 
	//					dataType:"json",
						success:function(data) {
							localStorage.setItem('cityid', data.data);
						},
						error:function() {
							$.toast('获取当前城市数据失败，请检查网络');
							localStorage.setItem('cityid', '340100');
						}
					});
                }
            }
        } else {
        	$.toast(result.info+',请手动选择城市');
            document.getElementById(object_id).innerHTML = result.info;
        }
    });
}