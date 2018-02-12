<?php  

	// 车辆管理

	header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','add','addoperate','edit','editoperate','del')) ? $op : 'index';

	$mcar = new mcar($db);

	if($op == 'index') {

		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 10;
		$pagestart = ($page - 1) * $limit;

		$car_num = count($mcar->getCarList()); //总数量
		$calcpagecnt = calcpagecnt($car_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $car_num, 'index.php?action=car', array('op'=>'index'));

		$carlist = $mcar->getCarList($pagestart, $limit);
		$smarty->assign('calcpagecnt', $calcpagecnt); //分页数
		$smarty->assign('car_num', $car_num); //总条数
		$smarty->assign('pagehtml', $pagehtml); //总条数
		$smarty->assign('carlist', $carlist);
		$smarty->display('car/index.html');

	} else if($op == 'edit') {

		$id = !empty($_REQUEST['id']) ? max(1, intval($_REQUEST['id'])) : 0;
		$carinfo = $mcar->getCarById($id);

		$smarty->assign('id', $id); 
		$smarty->assign('carinfo', $carinfo); 
		$smarty->display('car/edit.html');

	} else if($op == 'editoperate') {
		
		$arr = array();
		$arr['id'] = $_POST['car_id'];
		$arr['car_name'] = $_POST['car_name'];
		$arr['car_no'] = $_POST['car_no'];
		$arr['car_type'] = $_POST['car_type'];
		$car_img = $_FILES['car_img'];

		$img_url = array();
		if($car_img) {

			//文件存放目录，和本php文件同级 
			$dir = 'upload/car/'; 
			$i = 0; 
			foreach ($car_img['tmp_name'] as $value) {
				$filename = $car_img['name'][$i]; 
				if ($value) {
					$savepath = $dir.$filename;
					$state = move_uploaded_file($value, $savepath); 

					//如果上传成功，预览 
					if($state) {
						$img_url[] = HTTP_HOST.$savepath;
					} else {
						$img_url[] = '';
					}
				}
				$i++; 
			} 
		}

		// print_r(json_encode($img_url));
		// exit;
		$arr['img_url'] = json_encode($img_url);
		$res = $mcar->updateCarInfo($arr);
		if($res) {
			echo "<script>alert('更新成功！');location.href='index.php?action=car&op=index';</script>";
			exit();
		} else {
			echo "<script>alert('更新失败！');location.href='index.php?action=car&op=edit&id=".$arr['id']."';</script>";
			exit();
		}

	} else if($op == 'del') {
		$id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : 0;
		$res = $mcar->deleteCarInfo($id);
		
		if($res) {
			$ret = 1;
		} else {
			$ert = 0;
		}
		echo $ret;
		exit();

	} else if($op == 'add') {

		$smarty->display('car/add.html');

	} else if($op == 'addoperate') {
		$arr['car_name'] = $_POST['car_name'];
		$arr['car_no'] = $_POST['car_no'];
		$car_img = $_FILES['car_img'];

		if($car_img) {

			//文件存放目录，和本php文件同级
			$dir = 'upload/car/'; 
			$i = 0; 
			foreach ($car_img['tmp_name'] as $value) {
				$filename = $car_img['name'][$i]; 
				if ($value) {
					$savepath = $dir.$filename;
					$state = move_uploaded_file($value, $savepath); 

					//如果上传成功，预览 
					if($state) {
						$img_url[] = $savepath;
					} else {
						$img_url[] = '';
					}
				}
				$i++; 
			} 

		} else {
			$img_url = array();
		}
		$arr['img_url'] = json_encode($img_url);
		$res = $mcar->InsertCarInfo($arr);
		if($res) {
			echo "<script>alert('添加成功！');location.href='index.php?action=car&op=index';</script>";
			exit();
		} else {
			echo "<script>alert('添加失败！');location.href='index.php?action=car&op=edit&id=".$arr['id']."';</script>";
			exit();
		}
	}
?>