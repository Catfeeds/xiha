<?php 
	/**
	 * 抓取驾校列表缩略图
	 * @param $page 页码 
	 * @param $filtercityid 城市ID 
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 * 获取学校列表：http://api.jxedt.com/list/?os=android&time=1452210078833&lon=117.139461&productid=1&pageindex=1&filterparams={"":"","filtercityid":"234"}&type=jx&channel=7&lat=31.833969&version=3.9.6&cityid=176
	 * 获取学校详情：http://api.jxedt.com/detail/23441/?type=jx
	 **/
	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getSchoolThumb');
	$app->run();

	function getSchoolThumb() {
		Global $app, $crypt;
		$request = $app->request();
		$page = $request->params('page');
		$page = isset($page) ? $page : 1;
		$limit = 50;
		$start = ($page - 1) * $limit;
		try {
			$db = getConnection();
			$sql = "SELECT `id`, `imageurl`, `infoid` FROM `cs_temp_school` WHERE `is_finished` != 1 ORDER BY `id` ASC LIMIT $start, $limit";
			$stmt = $db->query($sql);
			$school_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$imageurl_arr = array();
			if($school_list) {
				foreach ($school_list as $key => $value) {
					$imageurl_arr[$value['infoid']] = $value['imageurl'];	
				}
			}
			$imgurl = array();
			foreach ($imageurl_arr as $key => $value) {
				$imgurl[$key] = getImage($value, 'upload/school/thumb/'.uniqid().'.jpg',1);
			}

			foreach ($imgurl as $key => $value) {
				$sql = "UPDATE `cs_temp_school` SET `imageurl` = '{$value}', `is_finished` = 1 WHERE `infoid` = '{$key}'";
				$stmt = $db->query($sql);
			}

			print_r($imgurl);
		} catch(PDOException $e) {
			setapilog('_get_school_thumb_list:params[], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}
	}

	/*
	*功能：php多种方式完美实现下载远程图片保存到本地
	*参数：文件url,保存文件名称，使用的下载方式
	*当保存文件名称为空时则使用远程文件原来的名称
	*/
	function getImage($url, $filename='', $type=0){
	    if($url==''){return false;}
	    if($filename==''){
	        $ext=strrchr($url,'.');
	        if($ext!='.gif' && $ext!='.jpg'){return false;}
	        $filename=time().$ext;
	    }
	    //文件保存路径
	    if($type){
			$ch=curl_init();
			$timeout=5;
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			$img=curl_exec($ch);
			curl_close($ch);
	    }else{
			ob_start();
			readfile($url);
			$img=ob_get_contents();
			ob_end_clean();
	    }
	    $size=strlen($img);
	    //文件大小
	    $fp2=@fopen($filename,'a');

	    fwrite($fp2,$img);
	    fclose($fp2);
	    return $filename;
	}
?>