<?php 
//  error_reporting(E_ALL);
//  ini_set('display_errors', 1);
	header("Content-Type:text/html; charset=UTF-8");
	include 'phpqrcode/qrlib.php';
	include 'include/config.php';
	include 'include/Mcrypt.class.php';
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
	if (!file_exists($PNG_TEMP_DIR))
	    mkdir($PNG_TEMP_DIR);
	$filename = $PNG_TEMP_DIR.'test.png';
    $errorCorrectionLevel = 'H';
    $matrixPointSize = 5;
    $PNG_WEB_DIR = 'temp/';
	$_token = isset($_GET['token']) ? urldecode($_GET['token']) : '';
   	$key = 'xhxueche';
   	$Mcrypt = new Crypt();
   	$tok = $Mcrypt->decrypt($_token, $key);
	$tok = $tok . '|' . rand();
    $token_arr = explode('|', $tok);
	$token = implode(',', $token_arr);
    $token = 'XHUSER,' . $token . ';';
//  $_token = '18656999023,3424231992234432,1,陈曦,1';  // 号码，身份证，用户ID，姓名，头像ID
    QRcode::png($token, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
	$imgurl = $PNG_WEB_DIR . basename($filename);
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="assets/css/framework7.ios.min.css">
    <link rel="stylesheet" href="assets/css/framework7.ios.colors.min.css">
	<title>电子教练登录</title>
</head>
<body id="particles-js" style="background: #2472b4;">
	<!--<div id="particles-js" style="background: #2472b4;"></div>-->
	<div id="showqrcode" style="margin:0px auto; width:100%; position:absolute; text-align:center; padding-top:40%;">
		<div class="col-25" style="color: #fff;">
			<span style="width:42px; height: 42px;" class="preloader preloader-white preloader-big"></span>
	        <br><br>正在生成二维码
      	</div>
	</div>
	<div style="position: absolute; bottom: 20px; color: #fff; text-align: center; width: 100%;">
		&copy;2015 安徽嘻哈网络技术有限公司
	</div>
    <script type="text/javascript" src="assets/js/framework7.min.js"></script>
    <script src="assets/js/particles.js"></script>
	<script>
		var myApp = new Framework7();
		var $$ = Framework7.$;
//		myApp.showPreloader('正在生成二维码...');
	    setTimeout(function () {
//	        myApp.hidePreloader();
		    var imghtml = '<img src="<?php echo $imgurl; ?>" ><br><br><span style="color:#fff">扫描二维码登录</span>';
		    $$('#showqrcode').html(imghtml);
	    }, 2000);
	</script>
</body>
</html>
