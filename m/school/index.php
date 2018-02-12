<?php
	$id = htmlspecialchars($_GET['id']);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
	<link rel="stylesheet" href="../assets/css/mui.min.css" />

	<meta charset="UTF-8" />
	<title></title>
</head>
<body style="">
	<div class="mui-content" style="margin: 0px auto; text-align: center;padding: 0px;">
		<div class="mui-loading" style="background: #000; margin:30%; padding:20px; opacity: 0.8; border-radius: 8px;">
			<div class="mui-spinner">
			</div>
			<p id="tips" style="text-align: center;padding-top:10px;">正在加载中</p>
		</div>
	</div>
</body>
<script src="../assets/js/mui.min.js"></script>
<script src="../assets/js/cookie.min.js"></script>
<script src="../assets/js/getlocation.js"></script>
<script>
	var id = "<?php echo $id; ?>";
	setInterval('redirect()', 200);
	function redirect() {
		getLocation();
		var loginauth = localStorage.getItem('loginauth');
		if(!loginauth) {
			localStorage.setItem('loginauth', '{}');
		}
		if(Cookies.get('lng') && Cookies.get('lat')) {
			location.href="default.php?id="+id;
		} else {
			document.getElementById('tips').innerHTML = '获取定位中';
		}
	
	}
</script>
</html>
