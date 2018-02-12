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


		public function getCarList($page='', $limit='') {

			if($page !== '' && $limit !== '') {
				$sql = "SELECT * FROM {$this->_dbtabpre}cars ORDER BY `addtime` DESC LIMIT $page, $limit";
			} else {
				$sql = "SELECT * FROM {$this->_dbtabpre}cars ORDER BY `addtime` DESC";
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
			$row['imgurl'] = json_decode($row['imgurl']);
			return $row;
		}

		/**
		 * 更新车辆信息
		 * @return array 教练列表
		 */
		public function updateCarInfo($arr) {
			$sql = "UPDATE `{$this->_dbtabpre}cars` SET `name` = '".$arr['car_name']."', `car_no` = '".$arr['car_no']."', `imgurl` = '".$arr['img_url']."', `car_type` = '".$arr['car_type']."' WHERE `id` = ".$arr['id'];
			$query = $this->_db->query($sql);
			return $query;	
		}

		/**
		 * 新增车辆信息
		 * @return array 教练列表
		 */
		public function InsertCarInfo($arr) {
			$sql = "INSERT INTO `{$this->_dbtabpre}cars` (`name`, `car_no`, `imgurl`, `addtime`) VALUES ('".$arr['car_name']."', '".$arr['car_no']."','".$arr['img_url']."',".time().")";
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

	}
?>