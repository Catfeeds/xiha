<?php  
	/**
	 * 获取教练车辆记录
	 * @param $id int 车辆ID
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	require 'include/functions.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->get('/id/(:id)','getCarInfo');
	$app->run();

	// 获取车辆记录
	function getCarInfo($id = 0) {
		Global $app, $crypt;
        if (intval($id) <= 0) {
            slimLog($app->request(), $app->response(), null, 'car_id:'.$id);
            $data = array(
                'code' => 200,
                'data' => array(
                    'id' => '',
                    'name' => '暂无车型',
                    'car_no' => '暂无牌照',
                    'imgurl' => array(),
                    'original_imgurl' => '',
                    'car_type' => '',
                    'car_cate_id' => '',
                    'school_id' => '',
                    'addtime' => '',
                    'car_info' => array(
                        'annual_list' => array(),
                        'fuel_list' => array(),
                        'insure_list' => array(),
                    ),
                ),
            );
            ajaxReturn($data);
        }

		try {
			$sql = "SELECT * FROM `cs_cars` WHERE `id` = $id";
			$db = getConnection();
			$stmt = $db->query($sql);
			$carinfo = $stmt->fetch(PDO::FETCH_ASSOC);

			// 获取车辆记录
			if($carinfo) {
				$imgurl_arr = array();
				if($carinfo['imgurl'] && $carinfo['imgurl'] != 'null') {
					$imgurl = json_decode($carinfo['imgurl'], true);
					if(is_array($imgurl)) {
						foreach ($imgurl as $key => $value) {
							if(file_exists(__DIR__.'/../sadmin/'.$value)) {
								$imgurl_arr[] = S_HTTP_HOST.$value;
                            } elseif(file_exists(__DIR__.'/../admin/'.$value)) {
								$imgurl_arr[] = HTTP_HOST.$value;
                            } elseif(file_exists(__DIR__.'/../'.$value)) {
								$imgurl_arr[] = HOST.$value;
							}
						}
					}		
				}
				$carinfo['imgurl'] = $imgurl_arr;

				$sql = "SELECT * FROM `cs_cars_info` WHERE `car_id` = $id ORDER BY `addtime` DESC";
				$stmt = $db->query($sql);
				$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$list = array();
				if($row) {
					foreach ($row as $key => $value) {
						switch($value['type_id']) {
							case '1':
							// 加油记录
								$list['fuel_list'][] = $value['content'];
								break;
							case '2':
							// 保险记录
								$list['insure_list'][] = $value['content'];
								break;
							case '3':
							// 年检记录
								$list['annual_list'][] = $value['content'];
								break;	
							default:
								break;
						}
					}
				} else {
					$list['fuel_list'] = array();
					$list['insure_list'] = array();
					$list['annual_list'] = array();

				}
					
				$carinfo['car_info'] = $list;
				$data = array('code'=>200, 'data'=>$carinfo);
				echo json_encode($data);
				exit();

			} else {
				$data = array('code'=>-1, 'data'=>'参数错误');
				echo json_encode($data);
				exit();
			}

		} catch(PDOException $e) {
			setapilog('get_car_info:params[id:'.$id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
		} catch(ErrorException $e) {
			setapilog('get_car_info:params[id:'.$id.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
		}
	}
?>
