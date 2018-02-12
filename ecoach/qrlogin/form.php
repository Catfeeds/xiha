<?php  
	header('Content-Type:text/html; charset=UTF-8');
	include 'include/config.php';
	include 'include/Mcrypt.class.php';

    $is_ajax = isset($_REQUEST['is_ajax']) ? trim($_REQUEST['is_ajax']) : 0; 
	$_token = isset($_REQUEST['token']) ? urldecode($_REQUEST['token']) : '';
	if($_token == '') {
		echo 'fail';
		exit();
	}
	$key = 'xhxueche';
	$Mcrypt = new Crypt();
    $token = $Mcrypt->decrypt($_token, $key);
    $token_arr = explode('|', $token);
    if($is_ajax == 1) {
	    $data = array(
	    	'phone' => $token_arr[0],
	    	'pass' => $token_arr[1],
	    	'type' => 0,	
		);
	    $res = request_post(HOST.'/api/login.php', $data);
	    echo $res;
	    exit();
    }
    	

    /**
     * 模拟post进行url请求
     * @param string $url
     * @param array $post_data
     */
    function request_post($url = '', $post_data = array()) {
        if (empty($url) || empty($post_data)) {
            return false;
        }
        
        $o = "";
        foreach ( $post_data as $k => $v ) 
        { 
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        $postUrl = $url;
        $curlPost = $post_data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        
        return $data;
    }
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>登录验证</title>
	<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
	<script src="http://libs.useso.com/js/jquery/2.1.1/jquery.min.js"></script>
</head>
<body>
	<div id="login_div" style="margin:0px auto; width:300px; text-align:center; padding-top:40%;">
		<button id="submit" style="width:130px; height:50px; background:#f60; border-radius:4px; border:none; color:#fff; box-shadow:none; ">确认登录</button>
	</div>
	<script>
		$('#submit').click(function() {
			$.ajax({
				type:"POST",
				url:"form.php",
				dataType:"JSON",
				data:{'token':"<?php echo $_token; ?>", "is_ajax":1},
				beforeSend:function() {
					$('#submit').html('正在登陆中');
				},
				success:function(data) {
					$('#submit').html('确认登录');
					if(data.code == 200) {
						$('#login_div').html('登陆成功！');
					} else {
						$('#login_div').html('登陆失败！');
					}
				},
				error:function() {

				}
			})
		});
	</script>
</body>
</html>
