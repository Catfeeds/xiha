<?php
	header("Content-Type:text/html; charset=UTF-8");
	include 'include/Mcrypt.class.php';
	include 'include/config.php';
	$ia = isset($_GET['ia']) ? htmlspecialchars($_GET['ia']) : 2;
	if($ia == 1) {
		$_token = isset($_GET['token']) ? urldecode($_GET['token']) : '';
//		echo $_token;
		if(!$_token) {
			echo "<script>alert('参数错误');return false;</script>";
			exit;
		}
     	$key = 'xhxueche';
       	$Mcrypt = new Crypt();
       	$token = $Mcrypt->encrypt($_token, $key);
		echo "<script>location.href='qrcode.php?token=".urlencode($token)."'</script>";
	}
	
?>
<html>
  <head>
    <!-- Required meta tags-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- Your app title -->
    <title>电子教练</title>
    <!-- Path to Framework7 iOS CSS theme styles-->
    <link rel="stylesheet" href="assets/css/framework7.ios.min.css">
    <!-- Path to Framework7 iOS related color styles -->
    <link rel="stylesheet" href="assets/css/framework7.ios.colors.min.css">
    <!-- Path to your custom app styles-->
    <link rel="stylesheet" href="assets/css/my-app.css">
  </head>
  <body>
    <!-- Views -->
    <div class="views">
      <!-- Your main view, should have "view-main" class -->
      <div class="view view-main">
        <!-- Top Navbar-->
        <div class="navbar">
          <div class="navbar-inner">
            <div class="center sliding">登录</div>
            <div class="right">
              <a href="#" class="link icon-only open-panel"><i class="icon icon-bars-blue"></i></a>
            </div>
          </div>
        </div>
        <div class="pages navbar-through toolbar-through">
          <div data-page="index" class="page">
            <div class="page-content">
            	<form action="<?php echo HOST; ?>/api/login.php" method="post" class="ajax-submit">
	              <div class="list-block">
			        <ul>
			          <li>
			            <div class="item-content">
			              <div class="item-inner"> 
			                <div class="item-title label">号码</div>
			                <div class="item-input">
			                  <input type="text" id="phone" name="phone" placeholder="您的号码"/>
			                </div>
			              </div>
			            </div>
			          </li>
			          <li>
			            <div class="item-content">
			              <div class="item-inner"> 
			                <div class="item-title label">密码</div>
			                <div class="item-input">
			                  <input type="password" id="pass" name="pass" placeholder="您的密码"/>
			                </div>
			              </div>
			            </div>
			          </li>
			        </ul>
			        <div class="list-block inset">
		        	 <ul>
		        	 	<li><input type="hidden" name="type" value="1" /> <input type="submit" value="登录" class="button button-big button-fill"/></li>
			           <!--<li><a href="about.php" class="external list-button item-link color-red">登 录</a></li>-->
			           <!-- 加上 class属性 external就可以实现href跳转 -->
			           <!--<li><a href="about.php" class="list-button item-link color-red">登 录</a></li>-->
			           <!--<li><a class="button button-big">登 录</a></li>-->
			         </ul>
			      </div>
	            </div>
        	</form>
          </div>
        </div>
		<div style="position: absolute; bottom: 20px; color: #555; text-align: center; width: 100%;">
			&copy;2015 安徽嘻哈网络技术有限公司
		</div>
      </div>
    </div>
    <script type="text/javascript" src="assets/js/framework7.min.js"></script>
    <script type="text/javascript" src="assets/js/my-app.js"></script>
  </body>
</html> 