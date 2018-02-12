<?php  

	// 车辆管理

	header("Content-type: text/html; charset=utf-8");
	!defined('IN_FILE') && exit('Access Denied');
	$op = in_array($op, array('index','add','addoperate','edit','editoperate','del')) ? $op : 'index';

	if(!isset($_SESSION['school_loginauth'])) {
		echo "<script>location.href='index.php?action=admin&op=login';</script>";
		exit();
	}
	$loginauth_str = authcode($_SESSION['school_loginauth'], 'DECODE');
	$loginauth_arr = explode('\t', $loginauth_str);
	$school_id = $loginauth_arr[2];

	$mcar = new mcar($db);

	if($op == 'index') {

		$page = !empty($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
		$limit = 20;
		$pagestart = ($page - 1) * $limit;

		$car_num = count($mcar->getCarList('', '', $school_id)); //总数量
		$calcpagecnt = calcpagecnt($car_num, $limit); //计算分页数
		$pagehtml = ShowPage($page, $limit, $calcpagecnt, $car_num, 'index.php?action=car', array('op'=>'index'));

		$carlist = $mcar->getCarList($pagestart, $limit, $school_id);
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
		$arr['car_no'] = trim($_POST['car_no']);
		$arr['car_type'] = $_POST['car_type'];
		$car_img = $_FILES['car_img'];
		$original_car_img = isset($_POST['original_car_img']) ? $_POST['original_car_img'] : array();
		$thumb_car_img = isset($_POST['thumb_car_img']) ? $_POST['thumb_car_img'] : array();
		$img_url = array();
		$original_img_url = array();

		if(in_array('0', array_values($car_img['error']))) {
			//文件存放目录，和本php文件同级 
			$dir = '../upload/car/'; 
			$path = getFolder($dir.$school_id);
			$i = 0;

			if(count($car_img['tmp_name'])  > 5) {
				echo "<script>alert('最多只能添加5张图 ');location.href='index.php?action=car&op=add';</script>";
				exit();
			}

			foreach ($car_img['tmp_name'] as $value) {
				$filename = time().rand(1 , 10000).strtolower(strrchr($car_img['name'][$i], '.')); 

				if ($value) {
					$savepath = $path.'/'.$filename;
					if($car_img['size'][$i] > 300*1024) {
						echo "<script>alert('名称为".$car_img['name'][$i]."的图片超过了300KB');location.href='index.php?action=car&op=edit&id=".$arr['id']."';</script>";
						exit();
					}
					$state = move_uploaded_file($value, $savepath); 
					$show_pic_scal = showPicScal($savepath, 320, 240);
					$thumbpath = resize($savepath, $show_pic_scal[0], $show_pic_scal[1], 3);

					//如果上传成功，预览 
					if($state) {
						$img_url[] = $thumbpath;
						$original_img_url[] = $savepath;
					} else {
						$img_url[] = '';
						$original_img_url[] = '';
					}
				}
				$i++; 
			}
			$arr['img_url'] = json_encode($img_url);
			$arr['original_img_url'] = json_encode($original_img_url);
		} else {
			$arr['img_url'] = json_encode($thumb_car_img);
			$arr['original_img_url'] = json_encode($original_car_img);
		}
			
		$res = $mcar->updateCarInfo($arr);
		if($res) {
			echo "<script>alert('更新成功！');location.href='index.php?action=car&op=index';</script>";
			exit();
		} else {
			echo "<script>alert('更新失败！');location.href='index.php?action=car&op=edit&id=".$arr['id']."';</script>";
			exit();
		}

	// 删除车辆
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
		$type = isset($_GET['type']) ? trim($_GET['type']) : '';
		$smarty->assign('type', $type);
		$smarty->display('car/add.html');

	} else if($op == 'addoperate') {
		$arr = array();
		$arr['type'] = $_POST['type'];
		$arr['car_name'] = $_POST['car_name'];
		$arr['car_no'] = $_POST['car_no'];
		$arr['car_type'] = $_POST['car_type'];
		$car_img = $_FILES['car_img'];
        $res = $mcar->checkCarNo($arr['car_no'], $school_id);
		if ($res) {
			echo "<script>alert('车牌号不能重复!');location.href='index.php?action=car&op=add'</script>";
			exit;
		}
		$img_url = array();
		$original_img_url = array();

		if($car_img) {

			//文件存放目录，和本php文件同级
			$dir = '../upload/car/'; 
			$path = getFolder($dir.$school_id);
			$i = 0;

			if(count($car_img['tmp_name'])  > 5) {
				echo "<script>alert('最多只能添加5张图 ');location.href='index.php?action=car&op=add';</script>";
				exit();
			}

			foreach ($car_img['tmp_name'] as $value) {
				$filename = time().rand(1 , 10000).strtolower(strrchr($car_img['name'][$i], '.')); 
				if ($value) {
					$savepath = $path.'/'.$filename;
					if($car_img['size'][$i] > 2*1024*1024) {
						echo "<script>alert('名称为".$car_img['name'][$i]."的图片超过了2M');location.href='index.php?action=car&op=add';</script>";
						exit();
					}
					$state = move_uploaded_file($value, $savepath); 
					$show_pic_scal = showPicScal($savepath, 320, 240);
					$thumbpath = resize($savepath, $show_pic_scal[0], $show_pic_scal[1], 3);

					//如果上传成功，预览 
					if($state) {
						$img_url[] = $thumbpath;
						$original_img_url[] = $savepath;
					} else {
						$img_url[] = '';
						$original_img_url[] = '';
					}
				}
				$i++; 
			}
		}

		$arr['img_url'] = json_encode($img_url);
		$arr['original_img_url'] = json_encode($original_img_url);
		$arr['school_id'] = $school_id;

		$res = $mcar->InsertCarInfo($arr);
		if($res) {
			if($arr['type'] == 'coachadd') {
				echo "<script>alert('添加成功！');location.href='index.php?action=coach&op=add';</script>";
			} else {
				echo "<script>alert('添加成功！');location.href='index.php?action=car&op=index';</script>";
			}
				
			exit();
		} else {
			echo "<script>alert('添加失败！');location.href='index.php?action=car&op=edit&id=".$arr['id']."';</script>";
			exit();
		}
	}
?>