<?php /* Smarty version 3.1.27, created on 2015-08-30 10:16:27
         compiled from "E:\AppServ\www\service\admin\templates\manager\map.html" */ ?>
<?php
/*%%SmartyHeaderCode:1161155e2677b486279_11419748%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a94af170fbb59cbc707f18afcc1fbb21bb77b0da' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\manager\\map.html',
      1 => 1439052996,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1161155e2677b486279_11419748',
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_55e2677b4c7458_56441395',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_55e2677b4c7458_56441395')) {
function content_55e2677b4c7458_56441395 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '1161155e2677b486279_11419748';
?>
<!-- <html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <style type="text/css">
    body, html,#allmap {width: 100%;height: 100%;overflow: hidden;margin:0;font-family:"微软雅黑";}
  </style>
  <?php echo '<script'; ?>
 type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=g5Yz4xW8t4FG32kH9Cgqr4MV"><?php echo '</script'; ?>
>
  <title>浏览器定位</title>
</head>
<body>
  <div id="allmap"></div>
</body>
</html>
<?php echo '<script'; ?>
 type="text/javascript">
  // 百度地图API功能
  var map = new BMap.Map("allmap");
  var point = new BMap.Point(117.202059,31.859031);
  map.centerAndZoom(point,12);

  var geolocation = new BMap.Geolocation();
  geolocation.getCurrentPosition(function(r){
    if(this.getStatus() == BMAP_STATUS_SUCCESS){
      var mk = new BMap.Marker(r.point);
      map.addOverlay(mk);
      map.panTo(r.point);
      alert('您的位置：'+r.point.lng+','+r.point.lat);
    }
    else {
      alert('failed'+this.getStatus());
    }        
  },{enableHighAccuracy: true})
  //关于状态码
  //BMAP_STATUS_SUCCESS 检索成功。对应数值“0”。
  //BMAP_STATUS_CITY_LIST 城市列表。对应数值“1”。
  //BMAP_STATUS_UNKNOWN_LOCATION  位置结果未知。对应数值“2”。
  //BMAP_STATUS_UNKNOWN_ROUTE 导航结果未知。对应数值“3”。
  //BMAP_STATUS_INVALID_KEY 非法密钥。对应数值“4”。
  //BMAP_STATUS_INVALID_REQUEST 非法请求。对应数值“5”。
  //BMAP_STATUS_PERMISSION_DENIED 没有权限。对应数值“6”。(自 1.1 新增)
  //BMAP_STATUS_SERVICE_UNAVAILABLE 服务不可用。对应数值“7”。(自 1.1 新增)
  //BMAP_STATUS_TIMEOUT 超时。对应数值“8”。(自 1.1 新增)
<?php echo '</script'; ?>
>
 -->

 <html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <style type="text/css">
    body, html,#allmap {width: 100%;height: 100%;overflow: hidden;margin:0;font-family:"微软雅黑";}
  </style>
  <?php echo '<script'; ?>
 type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=g5Yz4xW8t4FG32kH9Cgqr4MV"><?php echo '</script'; ?>
>
  <?php echo '<script'; ?>
 src="templates/assests/layer/layer.js"><?php echo '</script'; ?>
>

  <title>单击获取点击的经纬度</title>
</head>
<body>
  <div id="allmap"></div>

</body>
</html>
<?php echo '<script'; ?>
 type="text/javascript">
  // 百度地图API功能
  var map = new BMap.Map("allmap");            
  map.centerAndZoom("合肥",12);           
  //单击获取点击的经纬度
  map.addEventListener("click",function(e){

    //给父页面传值

    parent.$('#school_location_x').val(e.point.lng); //经度
    parent.$('#school_location_y').val(e.point.lat); //纬度

  });
<?php echo '</script'; ?>
><?php }
}
?>