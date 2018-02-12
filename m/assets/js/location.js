var totalDistance = 0.0;
var lastLat;
var lastLong;

function toRadians(degree) {
  return this * Math.PI / 180;
}

//计算移动距离
function distance(latitude1, longitude1, latitude2, longitude2) {
  // R是地球半径（KM）
  var R = 6371;

  var deltaLatitude = toRadians(latitude2-latitude1);
  var deltaLongitude = toRadians(longitude2-longitude1);
  latitude1 = toRadians(latitude1);
  latitude2 = toRadians(latitude2);

  var a = Math.sin(deltaLatitude/2) *
          Math.sin(deltaLatitude/2) +
          Math.cos(latitude1) *
          Math.cos(latitude2) *
          Math.sin(deltaLongitude/2) *
          Math.sin(deltaLongitude/2);

  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  var d = R * c;
  return d;
}

//显示消息
function updateStatus(message) {
	alert(message);
}

function loadlocation() {
    if(navigator.geolocation) {
//      updateStatus("浏览器支持HTML5 Geolocation。");
        navigator.geolocation.watchPosition(updateLocation, handleLocationError, {maximumAge:20000});
    }
}

function updateLocation(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;
//  var accuracy = position.coords.accuracy;
	
//		return latitude+'|'+longitude;
    document.getElementById("latitude").value = latitude;
    document.getElementById("longitude").value = longitude;
//  document.getElementById("accuracy").innerHTML = accuracy;

//  // 如果accuracy的值太大，我们认为它不准确，不用它计算距离
//  if (accuracy >= 500) {
//      updateStatus("这个数据太不靠谱，请打开GPS，需要更准确的数据来计算本次移动距离。");
//      return;
//  }
//
//  // 计算移动距离
//
//  if ((lastLat != null) && (lastLong != null)) {
//      var currentDistance = distance(latitude, longitude, lastLat, lastLong);
//      document.getElementById("currDist").innerHTML =
//        "本次移动距离：" + currentDistance.toFixed(4) + " 千米";
//
//      totalDistance += currentDistance;
//
//      document.getElementById("totalDist").innerHTML =
//        "总计移动距离：" + currentDistance.toFixed(4) + " 千米";
//  }
//
//  lastLat = latitude;
//  lastLong = longitude;
//
//  updateStatus("计算移动距离成功。");
}

function handleLocationError(error) {
    switch(error.code)
    {
    case 0:
      updateStatus("尝试获取您的位置信息时发生错误：" + error.message);
      break;
    case 1:
      updateStatus("用户拒绝了获取位置信息请求。");
      break;
    case 2:
      updateStatus("浏览器无法获取您的位置信息：" + error.message);
      break;
    case 3:
      updateStatus("获取您位置信息超时。");
      break;
    }
}