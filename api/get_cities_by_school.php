<?php  
	/**
	 * 获取城市列表
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/


	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->get('/','getCitieslist');
	$app->run();

	// 获取城市列表
	function getCitieslist() {
		$sql = "SELECT `city_id` FROM `cs_school`";
		try {
			$db = getConnection();
			$stmt = $db->query($sql);
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($res as $key => $value) {
				$city_id_arr[] = $value['city_id'];
			}
			$city_id_str = implode(',', $city_id_arr);
			$sql = "SELECT * FROM `cs_city` WHERE `cityid` IN (".$city_id_str.") ORDER BY `leter` ASC";
			$stmt = $db->query($sql);
			$city_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$data = array('code'=>200, 'data'=>$city_arr);
			echo json_encode($data);
			exit();

		} catch (PDOException $e) {
			
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('get_cities_by_school:error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');

			echo json_encode($data);
			exit;
		}
	}
?>