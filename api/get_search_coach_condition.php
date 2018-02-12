<?php  

	/**
	 * 获取教练搜索条件
	 * @param $week_id 周ID 1,2,3,4,6,7
	 * @param $coach_id 教练ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->get('/','getSearchCondition');
	$app->run();

	function getSearchCondition() {

		// 配置驾照类型 
		$lisence_config = array(
			array(
				'car_type'=>1,
				'lisence_id' => 1,
				'lisence_name' =>'C1',
				'lesson' => array(
					array(
						'lesson_id' => 2,
						'lesson_name' => '科目二'
					),
					array(
						'lesson_id' => 3,
						'lesson_name' => '科目三'
					)
				)
			),
			array(
				'car_type'=>1,
				'lisence_id' => 2,
				'lisence_name' =>'C2',
				'lesson' => array(
					array(
						'lesson_id' => 2,
						'lesson_name' => '科目二'
					),
					array(
						'lesson_id' => 3,
						'lesson_name' => '科目三'
					)
				)
			),
			array(
				'car_type'=>1,
				'lisence_id' => 3,
				'lisence_name' =>'C3',
				'lesson' => array(
					array(
						'lesson_id' => 2,
						'lesson_name' => '科目二'
					),
					array(
						'lesson_id' => 3,
						'lesson_name' => '科目三'
					)
				)
			),
			array(
				'car_type'=>2,
				'lisence_id' => 4,
				'lisence_name' =>'A1',
				'lesson' => array(
					array(
						'lesson_id' => 2,
						'lesson_name' => '科目二'
					),
					array(
						'lesson_id' => 3,
						'lesson_name' => '科目三'
					)
				)
			),
			array(
				'car_type'=>2,
				'lisence_id' => 6,
				'lisence_name' =>'B1',
				'lesson' => array(
					array(
						'lesson_id' => 2,
						'lesson_name' => '科目二'
					),
					array(
						'lesson_id' => 3,
						'lesson_name' => '科目三'
					)
				)
			),
			array(
				'car_type'=>3,
				'lisence_id' => 5,
				'lisence_name' =>'A2',
				'lesson' => array(
					array(
						'lesson_id' => 2,
						'lesson_name' => '科目二'
					),
					array(
						'lesson_id' => 3,
						'lesson_name' => '科目三'
					)
				)
			),
			array(
				'car_type'=>3,
				'lisence_id' => 7,
				'lisence_name' =>'B2',
				'lesson' => array(
					array(
						'lesson_id' => 2,
						'lesson_name' => '科目二'
					),
					array(
						'lesson_id' => 3,
						'lesson_name' => '科目三'
					)
				)
			),
			array(
				'car_type'=>4,
				'lisence_id' => 8,
				'lisence_name' =>'D',
				'lesson' => array(
					array(
						'lesson_id' => 2,
						'lesson_name' => '科目二'
					),
					array(
						'lesson_id' => 3,
						'lesson_name' => '科目三'
					)
				)
			),
			array(
				'car_type'=>4,
				'lisence_id' => 9,
				'lisence_name' =>'E',
				'lesson' => array(
					array(
						'lesson_id' => 2,
						'lesson_name' => '科目二'
					),
					array(
						'lesson_id' => 3,
						'lesson_name' => '科目三'
					)
				)
			),
			array(
				'car_type'=>4,
				'lisence_id' => 10,
				'lisence_name' =>'F',
				'lesson' => array(
					array(
						'lesson_id' => 2,
						'lesson_name' => '科目二'
					),
					array(
						'lesson_id' => 3,
						'lesson_name' => '科目三'
					)
				)
			)

		);

		$data = array('code'=>200, 'data'=>$lisence_config);
		echo json_encode($data);
		exit();

	}

?>