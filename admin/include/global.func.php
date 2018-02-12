<?php

/**
 * 全局函数定义文件
 */

!defined('IN_FILE') && exit('Access Denied');

/**
 * 自动载入模型对象
 * @param string $classname 模型名
 * @return null
 */
function loadmodel($classname) {
	$require_file = MOBILE_ROOT.'./model/'.$classname.'.class.php';
	if(file_exists($require_file)) {
		require_once $require_file;
	}
}

/**
 * 发送原始HTTP header
 * @param string $string HTTP header内容
 * @param bool $replace 是否替换之前的header内容
 * @param int $http_response_code 指定HTTP响应代码
 * @return null
 */
function iheader($string, $replace = true, $http_response_code = 0) {
	$string = str_replace(array("\r", "\n"), '', $string);
	if(empty($http_response_code)) {
		@header($string, $replace);
	} else {
		@header($string, $replace, $http_response_code);
	}
	if(preg_match('/^\s*location:/is', $string)) {
		exit();
	}
}

/**
 * 生成随机字符串
 * @param int $length 字符串长度
 * @param bool $numeric 是否为纯数字字符串
 * @return string 随机字符串
 */
function random($length, $numeric = false) {
	mt_srand((double)microtime() * 1000000);
	if($numeric) {
		$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
	} else {
		$hash = '';
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$max = strlen($chars) - 1;
		for($i = 0; $i < $length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}
	}
	return $hash;
}

/**
 * 显示对话框
 * @param string $text 对话框内容
 * @param string $title 对话框标题
 * @return null
 */
function showdialog($text, $title = '') {
	global $smarty;
	$smarty->assign('text', $text);
	$smarty->assign('title', $title === '' ? '提示信息' : $title);
	$smarty->display('dialog.htm');
	exit();
}

/**
 * 是否为合法E-mail地址
 * @param string $email E-mail地址
 * @return bool 是否合法
 */
function isemail($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-]+(\.[\w\-]+)*@[\w\-]+(\.[\w\-]+)+$/", $email);
}

/**
 * 是否为合法手机号码
 * @param string $mobile 手机号码
 * @return bool 是否合法
 */
function ismobile($mobile) {
	return preg_match("/^1\d{10}$/", $mobile);
}

/**
 * 是否为合法邮政编码
 * @param string $zipcode 邮政编码
 * @return bool 是否合法
 */
function iszipcode($zipcode) {
	return preg_match("/^\d{6}$/", $zipcode);
}

/**
 * 是否为合法用户名
 * @param string $username 用户名
 * @return bool 是否合法
 */
function isuname($uname) {
	return preg_match("/^[a-zA-Z0-9]{1,}$/", $uname);
}

/**
 * mysql_escape_string的简写, 转义一个字符串安全用于mysql_query
 * @param string $str 字符串
 * @return string 转义后的字符串
 */
function mysqlesc($str) {
	return mysql_real_escape_string($str);
}

/**
 * htmlspecialchars的简写, 转义一个字符串安全用于显示
 * @param string $str 字符串
 * @return string 转义后的字符串
 */
function htmlchars($str) {
	return htmlspecialchars($str);
}

/**
 * 获取客户端IP地址
 * @return string IP地址
 */
function getip() {
	$ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
		foreach ($matches[0] AS $xip) {
			if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
				$ip = $xip;
				break;
			}
		}
	}
	return $ip;
}

/**
 * 可逆加解密
 * @param string $string 待加密的明文或待解密的密文
 * @param string $operation 加解密操作(加密ENCODE, 解密DECODE)
 * @param string $key 加解密密钥
 * @param int $expiry 过期时间
 * @return string 加密后的密文或解密后的明文
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key ? $key : AUTH_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);
	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

/**
 * 写入COOKIE
 * @param string $var COOKIE名
 * @param string $value COOKIE值
 * @param string $life COOKIE有效期
 * @param bool $prefix 是否添加COOKIE前缀
 * @return null
 */
function isetcookie($var, $value, $life = 0, $prefix = true) {
	$cookielife = $life ? $GLOBALS['timestamp'] + $life : 0;
	$cookiepre = $prefix ? $GLOBALS['cookie_pre'] : '';
	setcookie($cookiepre.$var, $value, $cookielife, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
}

/**
 * 读取COOKIE
 * @param string $var COOKIE名
 * @param bool $prefix 是否添加COOKIE前缀
 * @return string COOKIE值
 */
function getcookie($var, $prefix = true) {
	global $_CONFIG;
	$cookiepre = $prefix ? $GLOBALS['cookie_pre'] : '';
	return isset($_COOKIE[$cookiepre.$var]) ? $_COOKIE[$cookiepre.$var] : '';
}

/**
 * 计算查询起始偏移量
 * @param int $amount 总记录条数
 * @param int $limit 最大行数
 * @return int 起始偏移量
 */
function calcquerystart($amount, $limit = 20) {
	return (calccurpage($amount, $limit) - 1) * $limit;
}

/**
 * 计算当前页
 * @param int $amount 总记录条数
 * @param int $limit 最大行数
 * @return int 分页数
 */
function calccurpage($amount, $limit = 20) {
	$pagecnt = calcpagecnt($amount, $limit);
	$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
	$page = $page > $pagecnt ? 1 : $page;
	return $page;
}

/**
 * 计算分页数
 * @param int $amount 总记录条数
 * @param int $limit 最大行数
 * @return int 分页数
 */
function calcpagecnt($amount, $limit = 20) {
	return $amount ? (($amount < $limit) ? 1 : (($amount % $limit) ? ((int)($amount / $limit) + 1) : ($amount / $limit))) : 0;
}

/**
 * 生成JSON数据
 * @param mixed $data 原始数据
 * @param int $errno 错误代码
 * @return null
 */
function makejson($data, $errno = 0) {
	$errno = intval($errno);
	$json = array(
		'errno' => $errno,
		'content' => $data
	);
	iheader("Content-Type:application/json");
	echo json_encode($json);
	exit();
}


/**
 * 格式化时间
 * @param int $timestamp 时间戳
 * @param string $format 格式字符串
 * @return string 格式化的时间
 */
function timeformat($timestamp, $format = 'Y-m-d H:i:s') {
	return date($format, $timestamp);
}

/**
 * 格式化价格
 * @param float $price 价格
 * @param string $format 格式字符串
 * @return string 格式化的价格
 */
function priceformat($price, $format = '￥%s') {
	return str_replace('%s', number_format(floatval($price), 2), $format);
}

/**
 * 是否为搜索引擎机器人
 * @param string $useragent 用户代理
 * @return bool 是否为搜索引擎机器人
 */
function checkrobot($useragent = '') {
	$useragent = strtolower(empty($useragent) ? $_SERVER['HTTP_USER_AGENT'] : $useragent);
	if(preg_match('/bot|crawl|spider|slurp|sohu-search|lycos|robozilla/i', $useragent)) {
		return true;
	} else {
		return false;
	}
}

// 单图片上传
/*array (size=1)
  'image1' => 
    array (size=5)
      'name' => string 'd1716a42ce902731.jpg' (length=20)
      'type' => string 'image/jpeg' (length=10)
      'tmp_name' => string 'D:\wamp\tmp\php89FB.tmp' (length=23)
      'error' => int 0
      'size' => int 4797
   */
function uploadimg($file, $path, $id) {
	chmod($path, 0777);
	$filename = $path.$id.'.jpg';
	if(file_exists($filename)) {
		unlink($filename);
	}
	if(!move_uploaded_file($file['tmp_name'], $filename)) {
		return 1;
	}
	return $filename;
}

/**
 * 根据不同需求排序商品
 * @param int $page 当前的页数是第几页
 * @param int $pagesize 单页显示的记录数
 * @param int $pagecnt 总的分页数
 * @param int $total 总的记录数
 * @param int $url 请求的地址
 * @param int $params 请求时的参数
 * @return string
 */

  function ShowPage($page, $pagesize, $pagecnt, $total, $url, $params) {

	$page_html = '';

	$page_html .= "总数 ".$total." 共 ".$pagecnt." 页当前第 ".$page."页<br/>"; 
	if($pagecnt > 1) {
		$request_url = $url.'&';

		foreach($params as $key=>$value) {
			$request_url .= $key.'='.$value.'&';
		}

		$request_url .= 'page=';

		$page_html .= '<ul class="pagination pagination-group">';

		//第一页
		$page_html .= '<li><a href="'.$request_url .'1">首页</a></li>';

		//上一页
		if($page==1) {
			$page_html .= '<li><a href="javascript:;">&laquo;</a></li>';
		}else {
			$page_html .= '<li><a href="'.$request_url.($page-1).'">&laquo;</a></li>';
		}
		if($pagecnt > 2) {
			if($page <= 3 ) {
				for($i=1; $i<=3; $i++) {
					if($page == $i) {
						$page_html .= '<li><a class="active" href="'.$request_url.($i).'">'.$i.'</a></li>';
					} else {
						$page_html .= '<li><a href="'.$request_url.($i).'">'.$i.'</a></li>';
					}
				}
			} else {
				for($i=$page-2; $i<=$page; $i++) {
					if($page == $i) {
						$page_html .= '<li><a class="active" href="'.$request_url.($i).'">'.$i.'</a></li>';
					} else {
						$page_html .= '<li><a href="'.$request_url.($i).'">'.$i.'</a></li>';
					}	
				}
			}
		} else {
			for($i=1; $i<=2; $i++) {
				if($page == $i) {
					$page_html .= '<li><a class="active" href="'.$request_url.($i).'">'.$i.'</a></li>';
				} else {
					$page_html .= '<li><a href="'.$request_url.($i).'">'.$i.'</a></li>';
				}
			}
		}
		if($pagecnt > 3) {
			$page_html .= '<li><a href="javascript:;" data-toggle="modal" data-target="#myModal">...</a><li>';
		}

		//下一页
		if($page == $pagecnt) {
			$page_html .= '<li><a href="javascript:;">&raquo;</a></li>';  
		}  else {
			$page_html .= '<li><a href="'.$request_url.($page+1).'">&raquo;</a></li>';
		}
		$page_html .= '<li><a href="'.$request_url.$pagecnt.'">末页</a></li>';

		if($pagecnt > 3) {
			$page_html .= '<li><select class="input" id="selectpage">';
			for ($i=1; $i<=$pagecnt; $i++) { 
				$page_html .= '<option value="'.$i.'">'.$i.'</option>';
			}
			$page_html .= '</select></li>';
			$page_html .= '<button id="skipping" class="button border-main" type="button">跳转</button>';
		}

		$page_html .= "</ul>";
	}
		
	return $page_html;
}

	// 创建目录
	function mkFolder($path) {
	    if(!is_readable($path)) {
	        is_file($path) or mkdir($path,0700);  
	    }
        return $path;
	}


	/**
	 * 按照日期自动创建存储文件夹
	 * @return string
	 */
	function getFolder($pathStr)
	{
	    if ( strrchr( $pathStr , "/" ) != "/" ) {
	        $pathStr .= "/";
	    }
	    $pathStr .= date( "Ymd" );
	    if ( !file_exists( $pathStr ) ) {
	        if ( !mkdir( $pathStr , 0777 , true ) ) {
	            return false;
	        }
	    }
	    var_dump($pathStr);exit;
	    return $pathStr;
	}

	/**   
	* 缩略图主函数 (保留缩略图和原图)    
	* @param string $src 图片路径   
	* @param int $w 缩略图宽度   
	* @param int $h 缩略图高度   
	* @param int $type 缩放类型 1：等比例缩放 2:100*100 3:320*240
	* @return mixed 返回缩略图路径   
	* **/    
	    
	function resize($src,$w,$h,$type=2) {     
	    $temp=pathinfo($src);     
	    $name='thumb'.$temp["basename"];//文件名     
	    $dir=$temp["dirname"];//文件所在的文件夹     
	    $extension=$temp["extension"];//文件扩展名     
	    $savepath="{$dir}/{$name}";//缩略图保存路径,新的文件名为*.thumb.jpg     
	    
	    //获取图片的基本信息     
	    $info=getImageInfo($src);
	    $width=$info[0];//获取图片宽度     
	    $height=$info[1];//获取图片高度     

	    $per1=round($width/$height,2);//计算原图长宽比     
	    $per2=round($w/$h,2);//计算缩略图长宽比     
	    if($type == 1) {
	        
	        //计算缩放比例     
	        if($per1>$per2||$per1==$per2)     
	        {     
	            //原图长宽比大于或者等于缩略图长宽比，则按照宽度优先     
	            $per=$w/$width;     
	        }     
	        if($per1<$per2)     
	        {     
	            //原图长宽比小于缩略图长宽比，则按照高度优先     
	            $per=$h/$height;     
	        }     
	        $temp_w=intval($width*$per);//计算原图缩放后的宽度     
	        $temp_h=intval($height*$per);//计算原图缩放后的高度

	    } else if($type == 2) {
	        $temp_w = 100;
	        $temp_h = 100;  
	    } else if($type == 3) {
    	 	$temp_w = 320;
	        $temp_h = 240;  
	    }

	    $temp_img=imagecreatetruecolor($temp_w,$temp_h);//创建画布     
	    $im=create($src);     
	    imagecopyresampled($temp_img,$im,0,0,0,0,$temp_w,$temp_h,$width,$height);     
	    if($type == 1) {
	        if($per1>$per2)     
	        {     
	            imagejpeg($temp_img,$savepath, 100);     
	            imagedestroy($im);     
	            return addBg($savepath,$w,$h,"w");     
	            //宽度优先，在缩放之后高度不足的情况下补上背景     
	        }     
	        if($per1==$per2)     
	        {     
	            imagejpeg($temp_img,$savepath, 100);     
	            imagedestroy($im);     
	            return $savepath;     
	            //等比缩放     
	        }     
	        if($per1<$per2)     
	        {     
	            imagejpeg($temp_img,$savepath, 100);     
	            imagedestroy($im);     
	            return addBg($savepath,$w,$h,"h");     
	            //高度优先，在缩放之后宽度不足的情况下补上背景     
	        }     
	    } else {
	        imagejpeg($temp_img,$savepath, 100);     
	        imagedestroy($im);
	        return $savepath;         
	        // return addBg($savepath,$w,$h,"w");   
	    }
	        
	}

	/**
	 * 获取图片压缩后的等比尺寸
	 * @param int $width 宽度
	 * @param int $height 高度
	 * @return void
	 * @author 
	 **/
	function showPicScal($picpath, $width, $height) {     
	    $imginfo = getImageInfo($picpath);     
	    $imgw = $imginfo [0];     
	    $imgh = $imginfo [1];     
	         
	    $ra = number_format(($imgw / $imgh), 1 ); //宽高比     
	    $ra2 = number_format(($imgh / $imgw), 1 ); //高宽比     
	         
	    
	    if ($imgw > $width || $imgh > $height) {     
	        if ($imgw > $imgh) {     
	            $newWidth = $width;     
	            $newHeight = round ( $newWidth / $ra );     
	             
	        } elseif ($imgw < $imgh) {     
	            $newHeight = $height;     
	            $newWidth = round ( $newHeight / $ra2 );     
	        } else {     
	            $newWidth = $width;     
	            $newHeight = round ( $newWidth / $ra );     
	        }     
	    } else {     
	        $newHeight = $imgh;     
	        $newWidth = $imgw;     
	    }     
	    $newsize [0] = $newWidth;     
	    $newsize [1] = $newHeight;     
	         
	    return $newsize;     
	}

	function getImageInfo($src) {     
	    return getimagesize($src);     
	}

	/**   
	* 创建图片，返回资源类型   
	* @param string $src 图片路径   
	* @return resource $im 返回资源类型    
	* **/    
	function create($src) {     
	    $info=getImageInfo($src);     
	    switch ($info[2])     
	    {     
	        case 1:     
	            $im=imagecreatefromgif($src);     
	            break;     
	        case 2:     
	            $im=imagecreatefromjpeg($src);     
	            break;     
	        case 3:     
	            $im=imagecreatefrompng($src);     
	            break;     
	    }     
	    return $im;     
	} 

	/**   
	* 添加背景   
	* @param string $src 图片路径   
	* @param int $w 背景图像宽度   
	* @param int $h 背景图像高度   
	* @param String $first 决定图像最终位置的，w 宽度优先 h 高度优先 wh:等比   
	* @return 返回加上背景的图片   
	* **/    
	function addBg($src,$w,$h,$fisrt="w") {     
	    $bg=imagecreatetruecolor($w,$h);     
	    $white = imagecolorallocate($bg,255,255,255);     
	    imagefill($bg,0,0,$white);//填充背景     
	    
	    //获取目标图片信息     
	    $info=getImageInfo($src);     
	    $width=$info[0];//目标图片宽度     
	    $height=$info[1];//目标图片高度     
	    $img=create($src);     
	    if($fisrt=="wh")     
	    {     
	        //等比缩放     
	        return $src;     
	    }     
	    else    
	    {     
	        if($fisrt=="w")     
	        {     
	            $x=0;     
	            $y=($h-$height)/2;//垂直居中     
	        }     
	        if($fisrt=="h")     
	        {     
	            $x=($w-$width)/2;//水平居中     
	            $y=0;     
	        }     
	        imagecopymerge($bg,$img,$x,$y,0,0,$width,$height,100);     
	        imagejpeg($bg,$src,100);     
	        imagedestroy($bg);     
	        imagedestroy($img);     
	        return $src;     
	    }       
	}

?>