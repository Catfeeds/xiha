
// 获取定位
function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.watchPosition(showPosition, handleLocationError, {maximumAge:20000});
//  navigator.geolocation.getCurrentPosition(showPosition);
  } else {
  	Cookies.set('lng', '117.145039');
		Cookies.set('lat', '31.840252');
  }
}
function showPosition(position) {
  var location = {'lng':position.coords.longitude,'lat':position.coords.latitude};
  Cookies.set('lng', position.coords.longitude);
  Cookies.set('lat', position.coords.latitude);
//return location.serializeArray();
}

function handleLocationError(error) {
    switch(error.code)
    {
    case 0:
    case 1:
    case 2:
    case 3:
    	mui.toast('您可能拒绝定位服务，请清除缓存后刷新');
	    Cookies.set('lng', '117.145039');
			Cookies.set('lat', '31.840252');
      break;
    }
}
