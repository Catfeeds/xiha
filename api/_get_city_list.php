<?php  
	/**
	 * 抓取省份城市区域列表 (1：报名成功，2：申请退款中 3：退款成功 4：报名取消 5:已评价)
	 * @param $cityid 城市ID  
	 * 获取省份列表：http://user.jxedt.com/ajax/getCity?provinceid=0 (全部省份)
	 * 获取城市列表：http://user.jxedt.com/ajax/getCity?provinceid=1 (根据每个省份ID获取城市列表)
	 * 获取区域列表：http://user.jxedt.com/ajax/getCity?cityid=234 (根据每个城市ID获取区域列表)
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/
	header('Content-Type:text/html; charset=GBK');
	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getCityList');
	$app->run();

	// 获取教练学员信息
	function getCityList() {
		Global $app , $crypt;
		$request = $app->request();
		$provinceid = $request->params('provinceid');
		$cityid = $request->params('cityid');
		$type = $request->params('type');
		$provinceid = isset($provinceid) ? $provinceid : 0;
		$type = isset($type) ? $type : 1;
		$cityid = isset($cityid) ? $cityid : 1;

		try {
			$db = getConnection();


			$data = array(
				'rd' => randomFloat(),
				'provinceid' => $provinceid
			);
			$url = 'http://user.jxedt.com/ajax/getCity';
			$res = request_post($url, $data);
			$_list = json_decode($res, true, 512, JSON_BIGINT_AS_STRING);

			if($type == 1) { // 省份

				$sql = "SELECT * FROM `cs_province`";
				$stmt = $db->query($sql);
				$province_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$list = array();
				foreach ($province_list as $key => $value) {
					foreach ($_list as $k => $v) {
						$province = $value['province'];
						$name = $v['name'];					 
						if(preg_match('/(省|市)/', $name)) {
							$name = preg_replace('/(省|市)/i', '', $name);
						}
						// echo $name;
						// echo $province;
						// $res = MyMaxStrIdentical($name, $province);
						// print_r($res);
						// if($res != '') {
						// 	$list[$key]['pid'] = $v['id'];
						// 	$list[$key]['name'] = $v['name'];
						// 	$list[$key]['province_id'] = $value['provinceid'];
						// }
						// echo strpos($province, $name);
						// exit();
						if(strpos($province, $name) !== false) {
							$list[$key]['pid'] = $v['id'];
							$list[$key]['name'] = $v['name'];
							$list[$key]['province_id'] = $value['provinceid'];
						}
					}
				}
				// 将省份对应的数据存储到数据库
				$sql = '';
				foreach ($list as $key => $value) {
					$sql = "SELECT * FROM `cs_temp_province` WHERE `pid` = '{$value['pid']}'";
					$stmt = $db->query($sql);
					$res = $stmt->fetch(PDO::FETCH_ASSOC);
					$str = '';
					if($res) {
						$sql = "UPDATE `cs_temp_province` SET `pid` = '{$value['pid']}', `name` = '{$value['name']}', `province_id` = '{$value['province_id']}' WHERE `pid` = '{$value['pid']}'";
						$result = $db->query($sql);

						if($result) {
							$str .= "当前省份ID：".$value['pid']."，更新抓取完成！(".date('Y-m-d H:i', time()).")";
							echo $str.'<br>';
							setlog($str);
						} else {
							$str .= "当前省份ID：".$value['pid']."，更新抓取完成！(".date('Y-m-d H:i', time()).")";
							echo $str.'<br>';
							setlog($str);
						}

					} else {
						$sql = "INSERT INTO `cs_temp_province` (`id`, `pid`, `name`, `province_id`) VALUES (NULL, '{$value['pid']}', '{$value['name']}', '{$value['province_id']}')";
						$result = $db->query($sql);
						if($result) {
							$str .= "当前省份ID：".$value['pid']."，新增抓取完成！(".date('Y-m-d H:i', time()).")";
							echo $str.'<br>';
							setlog($str);
						} else {
							$str .= "当前省份ID：".$value['pid']."，新增抓取完成！(".date('Y-m-d H:i', time()).")";
							echo $str.'<br>';
							setlog($str);
						}	
					}
				}

			} else if($type == 2) { // 城市列表
				$sql = "SELECT `province_id` FROM `cs_temp_province` WHERE `pid` = '{$provinceid}'";
				$stmt = $db->query($sql);
				$_province_id = $stmt->fetch(PDO::FETCH_ASSOC);

				$list = array();
				$city_list = array();
				if($_province_id) {
					$sql = "SELECT * FROM `cs_city` WHERE `fatherid` = '{$_province_id['province_id']}' AND `cityid` NOT IN (110000,120000,310000,500000)";
					$stmt = $db->query($sql);
					$city_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
				}
				foreach ($city_list as $key => $value) {
					foreach ($_list as $k => $v) {
						$city = $value['city'];
						$name = $v['name'];					 
						if(preg_match('/(省|市|区)/', $name)) {
							$name = preg_replace('/(省|市|区)/i', '', $name);
						}
						if(strpos($city, $name) !== false) {
							$list[$key]['cid'] = $v['id'];
							$list[$key]['name'] = $v['name'];
							$list[$key]['city_id'] = $value['cityid'];
						}
					}
				}

				$sql = '';
				foreach ($list as $key => $value) {
					$sql = "SELECT * FROM `cs_temp_city` WHERE `cid` = '{$value['cid']}'";
					$stmt = $db->query($sql);
					$res = $stmt->fetch(PDO::FETCH_ASSOC);
					$str = '';
					if($res) {
						$sql = "UPDATE `cs_temp_city` SET `cid` = '{$value['cid']}', `name` = '{$value['name']}', `city_id` = '{$value['city_id']}', `province_id` = '{$provinceid}' WHERE `cid` = '{$value['cid']}'";
						$result = $db->query($sql);

						if($result) {
							$str .= "当前城市ID：".$value['cid']."，更新抓取完成！(".date('Y-m-d H:i', time()).")";
							echo $str.'<br>';
							setlog($str);
						} else {
							$str .= "当前城市ID：".$value['cid']."，更新抓取完成！(".date('Y-m-d H:i', time()).")";
							echo $str.'<br>';
							setlog($str);
						}

					} else {
						$sql = "INSERT INTO `cs_temp_city` (`id`, `cid`, `name`, `city_id`, `province_id`) VALUES (NULL, '{$value['cid']}', '{$value['name']}', '{$value['city_id']}', '{$provinceid}')";
						$result = $db->query($sql);
						if($result) {
							$str .= "当前城市ID：".$value['cid']."，新增抓取完成！(".date('Y-m-d H:i', time()).")";
							echo $str.'<br>';
							setlog($str);
						} else {
							$str .= "当前城市ID：".$value['cid']."，新增抓取完成！(".date('Y-m-d H:i', time()).")";
							echo $str.'<br>';
							setlog($str);
						}	
					}
				}

				// echo "<pre>";
				// print_r($city_list);
				// print_r($_list);
				// print_r($list);

			} else if($type == 3) {  // 区域
				$sql = "SELECT `cid` FROM `cs_temp_city` WHERE `province_id` = '{$provinceid}'";
				$stmt = $db->query($sql);
				$cityid_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
				print_r($cids);
				foreach ($cityid_list as $key => $value) {
					
					$url = "http://user.jxedt.com/ajax/getCity?cityid=".$value['cid'];

				}
			}

			echo json_encode($data);

		} catch(PDOException $e) {
			setapilog('_get_city_list:params[type:'.$type.', provinceid:'.$provinceid.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}
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

	function MyMaxStrIdentical($txt,$str){
	    $strlen = strlen($txt);
	    for($i=0; $i<$strlen;$i++) {
	           for ($a = 0 ,$b = $strlen-$i; $b!=$strlen+1; $a++,$b++) {               
	                   $key  = substr($txt,$a,$b);  // 每一次的遍历的Key进行字符串查找
	                    if(strpos($str,$key) !==false) {  //找到了
	                         return  $key;
	                }
	           }
	    }
	}

	function setlog($word='') {
		$fp = fopen("citylog.txt","a");
		flock($fp, LOCK_EX) ;
		fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time()+8*3600)."\n".$word."\n");
		flock($fp, LOCK_UN);
		fclose($fp);
	}

	function randomFloat($min = 0, $max = 1) {  
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);  
    }  
?>