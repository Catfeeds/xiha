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
	$app->post('/','getSchoolDetail');
	$app->run();

	// 获取教练学员信息
	function getSchoolDetail() {
		Global $app, $crypt;
		$request = $app->request();
		$page = $request->params('page');
		
		$db = getConnection();
		$num = 10;
		$start = $num * ($page - 1);
		$sql = "SELECT * FROM `cs_school` LIMIT $start, $num";
		$stmt = $db->query($sql);
		$school_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$list = array();
		foreach ($school_list as $key => $value) {
			$list[$key]['name'] = $value['s_school_name'];
		}
		$data = array('code'=>200, 'data'=>$list);
		echo json_encode($data);

	}