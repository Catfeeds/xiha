<?php  
	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getSchoolList');
	$app->run();

	// 获取教练学员信息
	function getSchoolList() {
		Global $app, $crypt;
		$db = getConnection();
		$request = $app->request();
		$province_id =$request->params('province_id');

		// 获取城市列表
		$sql = "SELECT `city_id`, `cid` FROM `cs_temp_city` WHERE `province_id` = '{$province_id}'";
		$stmt = $db->query($sql);
		$city_id_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$city_ids = array();
		if($city_id_list) {
			foreach ($city_id_list as $key => $value) {
				$city_ids[] = $value['cid'];
			}
		}

		$pagecnt = 50;
		for ($i=1; $i <= $pagecnt; $i++) { 
			foreach ($city_ids as $k => $v) {
				// $data = array();
				$data = array(
					'os' => 'andriod',
					'time' => time(),
					'lon' => '117.139461', // 经度
					'productid' => 1, // 暂无用
					'type' => 'jx', // 驾校
					'channel' => 7, // 暂无用
					'lat' => '31.833969', // 纬度
					'version' => '3.9.6', // 版本
					'cityid' => '176' // 城市ID
				);
				$data['pageindex'] = $i;
				$data['filterparams'] = json_encode(array('filtercityid' => $v));

				$url = 'http://api.jxedt.com/list/';
				$res = request_post($url, $data);
				$result = json_decode($res, true, 512, JSON_BIGINT_AS_STRING);

				$list = array();

				if($result['result']['jx']['pagesize'] == 0) {
					$data = array('code'=>200, 'data'=>'抓取完成');
					echo json_encode($data);
					exit();
				}
				foreach ($result['result']['jx']['infolist'] as $key => $value) {
				  $list[$key]['infoid'] = isset($value['infoid']) ? $value['infoid'] : '';

				  // 获取驾校详情
				  $url = "http://api.jxedt.com/detail/".$value['infoid']."/?type=jx";
				  $detail_res = file_get_contents($url);
				  $school_info = json_decode($detail_res, true, 512, JSON_BIGINT_AS_STRING);
				  $list[$key]['lng'] = !empty($school_info['result']['info']['baseinfoarea']['mapaddr']['action']['extparam']) ? trim($school_info['result']['info']['baseinfoarea']['mapaddr']['action']['extparam']['lon']) : '';
				  $list[$key]['lat'] = !empty($school_info['result']['info']['baseinfoarea']['mapaddr']['action']['extparam']) ? trim($school_info['result']['info']['baseinfoarea']['mapaddr']['action']['extparam']['lat']) : '';

			  	}

			  	foreach ($list as $key => $value) {
			  		$sql = "UPDATE `cs_temp_school` SET ";
			  		$sql .= "`lng` = '{$value['lng']}', ";
			  		$sql .= "`lat` = '{$value['lat']}' ";
			  		$sql .= "WHERE `infoid` = '{$value['infoid']}'";
			  		// echo $sql;
					$res = $db->query($sql);	
			  	}
		  	}
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
?>