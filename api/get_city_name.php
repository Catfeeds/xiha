<?php  
	/**
	 * 获取所有驾校评价列表
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
	$app->get('/cityname/:cityname','getSchoolCommentList');
	$app->run();

	// 获取教练学员信息
	function getSchoolCommentList($cityname) {
		Global $app, $crypt;

		try {
			$db = getConnection();
			$sql = "SELECT * FROM `cs_city` WHERE `city` = '".$cityname."'";
			$stmt = $db->query($sql);
			$cityinfo = $stmt->fetch(PDO::FETCH_ASSOC);
			if(empty($cityinfo)) {
				$sql = "SELECT * FROM `cs_city` WHERE `city` LIKE '%".$cityname."%'";
				$stmt = $db->query($sql);
				$cityinfo = $stmt->fetch(PDO::FETCH_ASSOC);
				if(empty($cityinfo)) {
					$data = array('code'=>-1, 'data'=>array());
				} else {
					$data = array('code'=>200, 'data'=>$cityinfo['cityid']);	
				}
			} else {
				$data = array('code'=>200, 'data'=>$cityinfo['cityid']);	
			}
			echo json_encode($data);

		} catch(PDOException $e) {
			setapilog('get_city_name:params[cityname:'.$cityname.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}

	}
?>