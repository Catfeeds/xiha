<?php

	/**
	 * 获得所有教练列表
	 * @param $type string 排序 综合最优 教练评分 距离最近 时间段
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->get('/type/:type','getCoach');

	// 获取所有教练
	function getCoach($type) {
		Global $crypt;
		$sql = "SELECT * FROM `cs_coach` WHERE `order_receive_status` = 1";
		switch ($type) {
			case 'default':
 				$sql .= " ORDER BY `integrated_excellent` DESC";
				break;
			case 'star':
				$sql .= " ORDER BY `i_coach_star` DESC";
				break;
			case 'distance':
				$sql .= " ORDER BY `coach_student_distance` ASC";
				break;
			case 'time':
				$sql .= " ORDER BY `time_quantum` DESC";
				break;
			default:
 				$sql .= " ORDER BY `integrated_excellent` DESC";
				break;
		}

		try {
			$db = getConnection();
			$stmt = $db->query($sql);
			$coach = $stmt->fetchAll(PDO::FETCH_OBJ);	
			$db = null;
			$data = array('code'=>200, 'data'=>$coach);
			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));

		} catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('get_coach:params[type:'.$type.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			// echo $crypt->encrypt(json_encode($data));
		}
	}

	$app->run();
?>