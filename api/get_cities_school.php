<?php  
	/**
	 * 获取城市列表(当前城市，常浏览城市和签约城市)
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/


	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getCitieslist');
	$app->run();

	// 获取城市列表
	function getCitieslist() {
		Global $app, $crypt;
		$request = $app->request();
		$city_id = $request->params('city_id');		
		$city_ids = $request->params('city_ids');
		$city_id = !empty($city_id) ? $city_id : '1';
		$city_ids = !empty($city_ids) ? $city_ids : '1';

		if($city_ids != 0) {
			$city_ids = array_filter(explode(',', $city_ids));
		} else {
			$city_ids = explode(',', $city_ids);
		}

		try {
			$db = getConnection();

			$list = array();

			// 获取当前城市
			$sql = "SELECT * FROM `cs_city` WHERE `cityid` = $city_id";
			$stmt = $db->query($sql);
			$city_info = $stmt->fetch(PDO::FETCH_ASSOC);
			if($city_info) {
				$list['current_city'] = $city_info;
			} else {
				$list['current_city'] = '';
			}

			// 获取常浏览的城市
			$sql = "SELECT * FROM `cs_city` WHERE `cityid` IN (".implode(',',$city_ids).")";
			$stmt = $db->query($sql);
			$city_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($city_infos) {
				$list['always_look_city'] = $city_infos;
			} else {
				$list['always_look_city'] = '';
			}

			// 获取所有签约的城市
			$sql = "SELECT `city_id` FROM `cs_school`";
			$stmt = $db->query($sql);
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($res as $key => $value) {
				$city_id_arr[] = $value['city_id'];
			}
			$city_id_str = implode(',', $city_id_arr);
			$sql = "SELECT * FROM `cs_city` WHERE `cityid` IN (".$city_id_str.")";
			$stmt = $db->query($sql);
			$city_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($city_arr) {
				$list['sign_city'] = $city_arr;
			} else {
				$list['sign_city'] = '';
			}

			$data = array('code'=>200, 'data'=>$list);
			echo json_encode($data);
			exit();

		} catch (PDOException $e) {
			setapilog('get_cities_school:params[city_id:'.$city_id.',city_ids:'.$city_ids.'], error:'.$e->getMessage());
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');

			echo json_encode($data);
			exit;
		}
	}
?>