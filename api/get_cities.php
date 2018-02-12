<?php  

	/**
	 * 获取所有城市
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

	function getCitieslist() {

			try {
				// 所有城市
				$sql = "SELECT * FROM `cs_city` ORDER BY `leter` ASC";
				$db = getConnection();
				$stmt = $db->query($sql);
				$city_all = $stmt->fetchAll(PDO::FETCH_ASSOC);

				// 可预约城市
				$sql = "SELECT `city_id` FROM `cs_school`";
				$stmt = $db->query($sql);
				$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach ($res as $key => $value) {
					$city_id_arr[] = $value['city_id'];
				}
				$city_id_str = implode(',', $city_id_arr);
				$sql = "SELECT * FROM `cs_city` WHERE `cityid` IN (".$city_id_str.") ORDER BY `leter` ASC";
				$stmt = $db->query($sql);
				$city_part = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$all_cities = array('all'=>$city_all, 'part'=>$city_part);
				$data = array('code'=>200, 'data'=>$all_cities);
				$db = null;
				echo json_encode($data);
				exit();

			} catch (PDOException $e) {				
				// $data = array('code'=>1, 'data'=>$e->getMessage());
				setapilog('get_articles:error:'.$e->getMessage());
				$data = array('code'=>1, 'data'=>'网络错误');
				echo json_encode($data);
				exit;
			}

	}

?>