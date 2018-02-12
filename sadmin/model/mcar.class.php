<?php 
	
	// 教练模块

	!defined('IN_FILE') && exit('Access Denied');

	class mcar extends mbase {

		/**
		 * 获取车辆列表
		 * @param $page int 页码
		 * @param $limit int 限制每页显示数量
		 * @return array 教练列表
		 */


		public function getCarList($page='', $limit='', $school_id) {

			if(!$school_id) {
				return array();
			}
			if($page !== '' && $limit !== '') {
				$sql = "SELECT * FROM {$this->_dbtabpre}cars WHERE `school_id` = $school_id ORDER BY `addtime` DESC LIMIT $page, $limit";
			} else {
				$sql = "SELECT * FROM {$this->_dbtabpre}cars WHERE `school_id` = $school_id ORDER BY `addtime` DESC";
			}
			$query = $this->_db->query($sql);
			$i = 0;
			$list = array();
			while($row = $this->_db->fetch_array($query)) {
				$list[$i]['id'] = $row['id'];
				$list[$i]['name'] = $row['name'];
				$list[$i]['car_no'] = $row['car_no'];
				$list[$i]['car_type'] = $row['car_type'];
				$list[$i]['imgurl'] = json_decode($row['imgurl']);
				$list[$i]['addtime'] = date('Y-m-d H:i', $row['addtime']);
				$i++;
			} 
			return $list;
		}

		/**
		 * 根据车辆ID获取测量信息
		 * @param $id int ID
		 * @return array 教练列表
		 */

		public function getCarById($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}cars` WHERE `id` = ".$id;
			$row = $this->_getFirstRecord($sql);
			$list = array();
			$list['imgurl'] = !empty($row['imgurl']) ? json_decode($row['imgurl']) : '';
			$list['original_imgurl'] = !empty($row['original_imgurl']) ? json_decode($row['original_imgurl']) : '';
			$list['car_no'] = $row['car_no'];
			$list['addtime'] = date('Y-m-d H:i', $row['addtime']);
			$list['car_type'] = $row['car_type'];
			$list['name'] = $row['name'];
			return $list;
		}

		/**
		 * 更新车辆信息
		 * @return array 教练列表
		 */
		public function updateCarInfo($arr) {
			$sql = "UPDATE `{$this->_dbtabpre}cars` SET `name` = '".$arr['car_name']."', `car_no` = '".$arr['car_no']."', `imgurl` = '".$arr['img_url']."', `original_imgurl` = '".$arr['original_img_url']."', `car_type` = '".$arr['car_type']."' WHERE `id` = ".$arr['id'];
			$query = $this->_db->query($sql);
			return $query;	
		}

		/**
		 * 新增车辆信息
		 * @return array 教练列表
		 */
		public function InsertCarInfo($arr) {
			$sql = "INSERT INTO `{$this->_dbtabpre}cars` (`name`, `car_no`, `imgurl`, `original_imgurl`, `addtime`, `car_type`, `school_id`) VALUES ('".$arr['car_name']."','".$arr['car_no']."','".$arr['img_url']."','".$arr['original_img_url']."','".time()."','".$arr['car_type']."','".$arr['school_id']."')";
			$query = $this->_db->query($sql);
			return $query;	
		}

		// 删除车辆信息
		public function deleteCarInfo($id) {
			$sql = "SELECT `imgurl` FROM `{$this->_dbtabpre}cars` WHERE `id` = $id";
			$row = $this->_getFirstRecord($sql);
			if(!$row) {
				return false;
			}
			
			// if($row['imgurl']) {
			// 	$img_url = json_decode($row['imgurl'], true);
			// 	foreach ($img_url as $key => $value) {
			// 		// 删除图片文件
			// 		unlink($value);
			// 	}
			// }
			$sql = "DELETE FROM `{$this->_dbtabpre}cars` WHERE `id` = $id";
			$res = $this->_db->query($sql);
			return $res;
		}
		public function checkCarNo($car_no, $school_id){
             $sql = "SELECT * FROM `{$this->_dbtabpre}cars` WHERE `car_no` = '".$car_no."' AND `school_id` = $school_id";
             $row = $this->_getFirstRecord($sql);
             if ($row) {
             	return true;
             }else{
             	return false;
             }
		}
	}
?>