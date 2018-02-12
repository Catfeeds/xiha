<?php  
	/**
	 * 获取驾校详情
	 * @param $id 驾校ID 1
	 * @param $lng 学员经度 117.144356
	 * @param $lat 学员维度 31.839411
	 * @param $member_id int 学员或者教练ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getSchoolAddressList');
	$app->run();
	
	function getSchoolAddressList() {
		Global $app, $crypt;

		$request = $app->request();
		$id = $request->params('id');
		$lng = $request->params('lng');
		$lat = $request->params('lat');

		$lng = !empty($lng) ? $lng : '117.144356';
		$lat = !empty($lat) ? $lat : '31.839411';

		$sql = "SELECT * FROM `cs_school_train_location` WHERE `tl_school_id` = $id";
		try {
			$db = getConnection();
			$stmt = $db->query($sql);
			$addresslist = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$list = array();
			if($addresslist) {
				foreach ($addresslist as $key => $value) {
					// 获取所有距离
					$list[$key]['tl_train_address'] = $value['tl_train_address'];
					$list[$key]['tl_phone'] 		= $value['tl_phone'];
					$list[$key]['tl_location_x'] 	= $value['tl_location_x'];
					$list[$key]['tl_location_y'] 	= $value['tl_location_y'];
					$list[$key]['distance'] = floor(getDistance($lat, $lng, $value['tl_location_y'], $value['tl_location_x'])/1000);
				}
				$list = multiArraySort($list, 'distance');
			}
			$db = null;
			$data = array('code'=>200, 'data'=>$list);	
			echo json_encode($data);

		} catch(PDOException $e) {
			setapilog('get_school_address_list:params[id:'.$id.',lng:'.$lng.',lat:'.$lat.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}
	}

	// 计算教练当前距离
	function getDistance($lat1, $lng1, $lat2, $lng2)  {
		$earthRadius = 6367000;
	
		$lat1 = ($lat1 * pi()) / 180;  
		$lng1 = ($lng1 * pi()) / 180;    
		$lat2 = ($lat2 * pi()) / 180;  
		$lng2 = ($lng2 * pi()) / 180;    

		$calcLongitude = $lng2 - $lng1;
		$calcLatitude = $lat2 - $lat1;  
		$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
		$stepTwo = 2 * asin(min(1, sqrt($stepOne)));  
		$calculatedDistance = $earthRadius * $stepTwo;
		return $calculatedDistance;
	}

	// 多维数组排序
	function multiArraySort($arr, $field, $sort = 'SORT_ASC') {
		$sort = array(
		        'direction' => $sort, //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
		        'field'     => $field,       //排序字段  
		);

		// 多维数组根据某个字段排序
		$arrSort = array();  
		foreach($arr AS $uniqid => $row){  
		    foreach($row AS $key=>$value){  
		        $arrSort[$key][$uniqid] = $value;  
		    }  
		}
		if($sort['direction']){  
		    array_multisort($arrSort[$sort['field']], constant($sort['direction']), $arr);  
		}
		return $arr;
	}
?>