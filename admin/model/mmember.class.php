<?php  

	// 订单模块
	

	!defined('IN_FILE') && exit('Access Denied');

	class mmember extends mbase {
		
		
		/**
		 * 获取会员列表
		 * @param $page int 页码
		 * @param $limit int 限制每页显示数量
		 * 用户状态(0,正常，1 封存 2:删除) i_status
		 * 用户类型(0 学员 1 教练) i_user_type
		 * @return array
		 */

		public function getMemberList($page='', $limit='', $i_status='') {
			if($page !== '' && $limit !== '') {
				$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`i_user_type` = 0 ";

				if($i_status == '') {
					$sql .= " AND u.`i_status` != 2 ORDER BY u.`l_user_id` DESC LIMIT $page, $limit";
				} else {
					$sql .= " AND u.`i_status` = $i_status ORDER BY u.`l_user_id` DESC LIMIT $page, $limit";
				}
					
			} else {
				$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`i_user_type` = 0 ";

				if($i_status == '') {
					$sql .= " AND u.`i_status` != 2 ORDER BY u.`l_user_id` DESC";
				} else {
					$sql .= " AND u.`i_status` = $i_status ORDER BY u.`l_user_id` DESC";	
				}

					
			}
			$res = $this->_getAllRecords($sql);
			return $res;
		}
		
		public function getMemberListNum() {
			$sql = "SELECT count(*) as num FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`i_user_type` = 0";
			$res = $this->_getFirstRecord($sql);
			return $res;
		}
		

		// 获取单个会员信息
		public function getMemberInfo($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`l_user_id` = ".$id;
			$row = $this->_getFirstRecord($sql);
			return $row;
		}

		/**
		 * 搜索学员列表
		 * @param $page int 页码
		 * @param $limit int 限制每页数量
		 * @param $conditiontype int 搜索条件限制（1：学员ID 2：学员姓名 3：学员号码 4：身份证号）
		 * @param $onlinetype int 线上线下（1：线上 (0:苹果 1:安卓) 2：线下）
		 * @param $keyword string 关键词
		 * @return void
		 * @author 
		 **/
		public function getSearchMemberList($page='', $limit='', $conditiontype, $onlinetype, $keyword) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`i_user_type` = 0  AND u.`i_status` != 2 ";	
			if($onlinetype != '') {
				if($onlinetype == 1) {
					$sql .= " AND u.`i_from` != 2";
				} else {
					$sql .= " AND u.`i_from` = $onlinetype";
				}
			}
					
			switch ($conditiontype) {
				case '1':
					$sql .= " AND u.`l_user_id` = '".$keyword."'";
					break;
				case '2':
					$sql .= " AND u.`s_real_name` LIKE '%".$keyword."%'";
					break;
				case '3':
					$sql .= " AND u.`s_phone` LIKE '%".$keyword."%'";
					break;
				case '4':
					$sql .= " AND i.`identity_id` LIKE '%".$keyword."%'";
					break;
				default:
					$sql .= " AND u.`l_user_id` = '".$keyword."'";
					break;
			}
			$sql .= " ORDER BY u.`l_user_id` DESC";
			if($page !== '' && $limit !== '') {
				$sql .= " LIMIT $page, $limit";
			} else {
				$sql .= "";
			}
			$row = $this->_getAllRecords($sql);
			return $row;
		}

		// 删除学员
		public function delMember($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}user` WHERE `l_user_id` = $id";
			$stmt = $this->_getFirstRecord($sql);
			if($stmt) {
				// 更改状态
				$sql = "UPDATE `{$this->_dbtabpre}user` SET `i_status` = 2 WHERE `l_user_id` = $id";
				$res = $this->_db->query($sql);
				return $res;
			} else {
				return false;
			}
		}
		// 删除多个
		public function deleteMoreMember($id) {
			$ids = array_filter(explode(',', $id));
			$sql = "SELECT * FROM `{$this->_dbtabpre}user` WHERE `l_user_id` IN (".implode(',', $ids).")";
			$stmt = $this->_getAllRecords($sql);
			if($stmt) {
				$sql = "UPDATE `{$this->_dbtabpre}user` SET `i_status` = 2 WHERE `l_user_id` IN (".implode(',', $ids).")";
				$res = $this->_db->query($sql);
				return $res;
			} else {
				return false;
			}
		}
		// 新增用户记录
		public function insertUserInfo($arr) {

			// 查询手机号是否存在
			$sql = "SELECT * FROM `{$this->_dbtabpre}user` WHERE `s_phone` = '".$arr['user_phone']."' AND `i_user_type` = 0";
			$res = $this->_getFirstRecord($sql);
			if($res) {
				return 1;
			}

			$sql = "SELECT * FROM `{$this->_dbtabpre}users_info` as i LEFT JOIN `{$this->_dbtabpre}user` as u ON u.`l_user_id` = i.`user_id` WHERE i.`identity_id` = '".$arr['identity_id']."' AND u.`i_user_type` = 0";
			$res = $this->_getFirstRecord($sql);
			if($res) {
				return 5;
			}

			$sql = "INSERT INTO `{$this->_dbtabpre}user` (";
			$sql .= " `s_username`,";
			$sql .= " `s_password`,";
			$sql .= " `i_user_type`,";
			$sql .= " `i_status`,";
			$sql .= " `s_real_name`,";
			$sql .= " `i_from`,";
			$sql .= " `s_phone`,";
			$sql .= " `content`";
			$sql .= ") VALUES (";
			$sql .= " '".$arr['user_name']."',";
			$sql .= " '".$arr['s_password']."',";
			$sql .= " '".$arr['i_user_type']."',";
			$sql .= " '".$arr['i_status']."',";
			$sql .= " '".$arr['real_name']."',";
			$sql .= " '".$arr['from']."',";
			$sql .= " '".$arr['user_phone']."',";
			$sql .= " '".$arr['content']."')";

			$res = $this->_db->query($sql);
			if($res) {
				$insert_id = $this->lastInertId();
				$sql = "INSERT INTO `{$this->_dbtabpre}users_info` (";
				$sql .= " `user_id`, `sex`, `age`, `identity_id`, `address`, `user_photo`, `province_id`, `city_id`, `area_id`) VALUES (";
				$sql .= " '".$insert_id."',";
				$sql .= " '".$arr['sex']."',";
				$sql .= " '".$arr['age']."',";
				$sql .= " '".$arr['identity_id']."',";
				$sql .= " '".$arr['address']."',";
				$sql .= " '".$arr['user_photo']."',";
				$sql .= " '".$arr['province']."',";
				$sql .= " '".$arr['city']."',";
				$sql .= " '".$arr['area']."')";

				$result = $this->_db->query($sql);
				if($result) {
					// 插入订单
					$sql = "INSERT INTO `{$this->_dbtabpre}school_orders` (";
					$sql .= "`so_school_id`,`so_final_price`, `so_original_price`,  `so_pay_type`,  `so_user_id`, `so_user_identity_id`, `so_username`, `so_phone`, `addtime`, `s_zhifu_dm`, `dt_zhifu_time`) VALUES (";			
					$sql .= "0, '".$arr['so_final_price']."',";
					$sql .= "'".$arr['so_original_price']."',";
					$sql .= "'".$arr['so_pay_type']."',";
					$sql .= "'".$insert_id."',";
					$sql .= "'".$arr['identity_id']."',";
					$sql .= "'".$arr['real_name']."',";
					$sql .= "'".$arr['user_phone']."',";
					$sql .= "'".time()."', '', 0)";
					$res = $this->_db->query($sql);
					if($res) {
						return 2;
					} else {
						return 7;
					}
						
				} else {
					return 3;
				}
			} else {
				return 4;
			}
		}
}

?>
