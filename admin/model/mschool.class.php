<?php  
	// 驾校模块

	!defined('IN_FILE') && exit('Access Denied');

	class mschool extends mbase {

		/**
		 * 获取驾校列表
		 * @return array 分类ID对应的品牌列表
		 */

		public function getSchoollist($page='', $limit='') {
			if($page !== '' && $limit !== '') {
				$sql = "SELECT * FROM {$this->_dbtabpre}school ORDER BY `brand` DESC,`l_school_id` DESC LIMIT $page, $limit";
			} else {
				$sql = "SELECT * FROM {$this->_dbtabpre}school";
			}
			$query = $this->_db->query($sql);
			$list = array();
			$i = 0;
			while($row = $this->_db->fetch_array($query)) {
				$list[$i]['school_id'] = $row['l_school_id'];
				$list[$i]['school_name'] = $row['s_school_name'];
				$list[$i]['frdb'] = $row['s_frdb'];
				$list[$i]['frdb_mobile'] = $row['s_frdb_mobile'];
				$list[$i]['zzjgdm'] = $row['s_zzjgdm'];
				$list[$i]['dwxz'] = $row['i_dwxz'];
				$list[$i]['address'] = $row['s_address'];
				$list[$i]['brand'] = $row['brand'];
				$list[$i]['is_show'] = $row['is_show'];
				// 查找账号
				$sql = "SELECT `password`, `name` FROM `{$this->_dbtabpre}admin` WHERE `school_id` = '{$row['l_school_id']}'";
				$admin_info = $this->_getFirstRecord($sql);
				if($admin_info) {
					$list[$i]['login_account'] = $admin_info['name'];
					if(md5('123456') == $admin_info['password']) {
						$list[$i]['password_tips'] = '默认';
					} else {
						$list[$i]['password_tips'] = '修改';
					}
						
				} else {
					$list[$i]['login_account'] = '';
					$list[$i]['password_tips'] = '';
				}
					
				$i++;
			}

			return $list;
		}

		// 获取学校详情
		public function getSchoolDetail($id) {
			$sql = "SELECT * FROM {$this->_dbtabpre}school WHERE `l_school_id` = ".$id;
			$res = $this->_getFirstRecord($sql);
			$res['s_thumb'] = $res['s_thumb'];
			$res['_s_thumb'] = HTTP_SHOST.$res['s_thumb'];
			return $res;
		}

		// 获取省份列表
		public function getProvinceList() {
			$sql = "SELECT * FROM `{$this->_dbtabpre}province`";
			// $query = $this->_db->query($sql);
			$res = $this->_getAllRecords($sql);
			return $res;
		}

		// 根据省份id获取所有城市列表
		public function getCityList($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}city` WHERE `fatherid` = ".$id;
			$res = $this->_getAllRecords($sql);
			return $res;
		}

		// 根据城市id获取所有区域列表
		public function getAreaList($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}area` WHERE `fatherid` = ".$id;
			$res = $this->_getAllRecords($sql);
			return $res;
		}

		// 根据城市id获取所有城市详情
		public function getCityDetail($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}city` WHERE `cityid` = ".$id;
			$res = $this->_getFirstRecord($sql);
			return $res;
		}

		// 根据城市id获取所有城市详情
		public function getAreaDetail($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}area` WHERE `areaid` = ".$id;
			$res = $this->_getFirstRecord($sql);
			return $res;
		}

		// 更新驾校信息
		public function updateSchoolInfo($arr) {
			$sql = "UPDATE `{$this->_dbtabpre}school` ";
			$sql .= " SET `s_school_name` 	= '".$arr['s_school_name']."', ";
			$sql .= " `s_frdb` 				= '".$arr['s_frdb']."', ";
			$sql .= " `s_frdb_mobile` 		= '".$arr['s_frdb_mobile']."', ";
			$sql .= " `s_frdb_tel` 			= '".$arr['s_frdb_tel']."', ";
			$sql .= " `s_yyzz` 				= '".$arr['lisence_imgurl']."', ";
			$sql .= " `s_zzjgdm` 			= '".$arr['s_zzjgdm']."', ";
			$sql .= " `i_dwxz` 				= '".$arr['i_dwxz']."', ";
			$sql .= " `province_id` 		= '".$arr['province_id']."', ";
			$sql .= " `city_id` 			= '".$arr['city_id']."', ";
			$sql .= " `s_address` 			= '".$arr['s_address']."', ";
			$sql .= " `dc_base_je` 			= '".$arr['dc_base_je']."', ";
			$sql .= " `dc_bili` 			= '".$arr['dc_bili']."', ";
			$sql .= " `s_yh_name` 			= '".$arr['s_yh_name']."', ";
			$sql .= " `s_yh_zhanghao` 		= '".$arr['s_yh_zhanghao']."', ";
			$sql .= " `s_yh_huming` 		= '".$arr['s_yh_huming']."', ";
			$sql .= " `s_shuoming` 			= '".$arr['s_shuoming']."', ";
			$sql .= " `s_location_x` 		= '".$arr['s_location_x']."', ";
			$sql .= " `s_location_y` 		= '".$arr['s_location_y']."', ";
			$sql .= " `s_thumb` 			= '".$arr['s_thumb']."', ";
			$sql .= " `brand` 			    = '".$arr['brand']."' ";
			$sql .= " WHERE `l_school_id` 	= ".$arr['l_school_id'];


			$query = $this->_db->query($sql);
			return $query;
		}

		// 增加驾校信息
		public function InsertSchoolInfo($arr) {
			$sql = "INSERT `{$this->_dbtabpre}school` (";
			$sql .= "`s_school_name`, `s_frdb`, `s_frdb_mobile`, `s_frdb_tel`, ";
			$sql .= "`s_yyzz`, `s_zzjgdm`, `i_dwxz`, `brand`, `province_id`, `city_id`, ";
			$sql .= "`s_address`, `dc_base_je`, `dc_bili`, `s_yh_name`, `s_yh_zhanghao`, `s_yh_huming`, `s_thumb`, `s_location_x`, `s_location_y`, `s_shuoming`) VALUES (";
			$sql .= "'".$arr['s_school_name']."', ";
			$sql .= "'".$arr['s_frdb']."', ";
			$sql .= "'".$arr['s_frdb_mobile']."', ";
			$sql .= "'".$arr['s_frdb_tel']."', ";
			$sql .= "'".$arr['lisence_imgurl']."', ";
			$sql .= "'".$arr['s_zzjgdm']."', ";
			$sql .= "'".$arr['i_dwxz']."', ";
			$sql .= "'".$arr['brand']."', ";
			$sql .= "'".$arr['province_id']."', ";
			$sql .= "'".$arr['city_id']."', ";
			$sql .= "'".$arr['s_address']."', ";
			$sql .= "'".$arr['dc_base_je']."', ";
			$sql .= "'".$arr['dc_bili']."', ";
			$sql .= "'".$arr['s_yh_name']."', ";
			$sql .= "'".$arr['s_yh_zhanghao']."', ";
			$sql .= "'".$arr['s_yh_huming']."', ";
			$sql .= "'".$arr['s_thumb']."', ";
			$sql .= "'".$arr['s_location_x']."', ";
			$sql .= "'".$arr['s_location_y']."', ";
			$sql .= "'".$arr['s_shuoming']."')";
			
			$query = $this->_db->query($sql);
			return $query;
		}

		// 删除学校
		public function delSchool($id) {
			$sql = "DELETE FROM `{$this->_dbtabpre}school` WHERE `l_school_id` = ".$id;
			$query = $this->_db->query($sql);
			return $query;
		}

		//设置品牌驾校
		public function setSchoolBrand($id) {
			$sql = "SELECT `brand` FROM {$this->_dbtabpre}school WHERE `l_school_id` = ".$id;
			$brand = $this->_getFirstRecord($sql);
			if($brand) {
				if($brand['brand'] == 1) {
					$sql = "UPDATE {$this->_dbtabpre}school SET `brand` = 2 WHERE `l_school_id` = ".$id;
				} else {
					$sql = "UPDATE {$this->_dbtabpre}school SET `brand` = 1 WHERE `l_school_id` = ".$id;
				}
				$query = $this->_db->query($sql);
				return $query;	
			} else {
				return false;
			}
		}

		// 获取班制信息
		public function getShiftsList() {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_shifts` ";
			$shifts_list = $this->_getAllRecords($sql);
			if($shifts_list) {
				foreach ($shifts_list as $key => $value) {
					$shifts_list[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);				
				}
			}
			return $shifts_list;
		}

		// 设置驾校在线状态
		public function setSchoolOnlineStatus($id) {
			$sql = "SELECT `is_show` FROM {$this->_dbtabpre}school WHERE `l_school_id` = '{$id}'";
			$is_show = $this->_getFirstResult($sql);
			if($is_show == 1) {
				$sql = "UPDATE {$this->_dbtabpre}school SET `is_show` = 2 WHERE `l_school_id` = '{$id}'";
			} else {
				$sql = "UPDATE {$this->_dbtabpre}school SET `is_show` = 1 WHERE `l_school_id` = '{$id}'";
			}
			$query = $this->_db->query($sql);
			return $query;
		}

		// 搜索驾校
		public function getSearchSchoolList($page='', $limit='', $type=1, $keyword='') {

			// 驾校名称
			if($type == 1) {
				$sql = "SELECT * FROM {$this->_dbtabpre}school WHERE `s_school_name` LIKE '%{$keyword}%' ORDER BY `brand` DESC,`l_school_id` DESC";
				if($page !== '' && $limit !== '') {
					$sql .= " LIMIT $page, $limit";
				} else {
					$sql .= "";
				}

			// 城市名称
			} else if($type == 2) {
				$sql = "SELECT `cityid` FROM `{$this->_dbtabpre}city` WHERE `city` LIKE '%{$keyword}%'";
				$cityinfo = $this->_getFirstResult($sql);

				$sql = "SELECT * FROM {$this->_dbtabpre}school WHERE `city_id` = '{$cityinfo}' ORDER BY `brand` DESC,`l_school_id` DESC";
				if($page !== '' && $limit !== '') {
					$sql .= " LIMIT $page, $limit";
				} else {
					$sql .= "";
				}

			} else if($type == 3) {

				$sql = "SELECT `provinceid` FROM `{$this->_dbtabpre}province` WHERE `province` LIKE '%{$keyword}%'";
				$provinceid = $this->_getFirstResult($sql);
				$sql = "SELECT * FROM {$this->_dbtabpre}school WHERE `province_id` = '{$provinceid}' ORDER BY `brand` DESC,`l_school_id` DESC";
				if($page !== '' && $limit !== '') {
					$sql .= " LIMIT $page, $limit";
				} else {
					$sql .= "";
				}
			}

			$query = $this->_db->query($sql);
			$list = array();
			$i = 0;
			while($row = $this->_db->fetch_array($query)) {
				$list[$i]['school_id'] = $row['l_school_id'];
				$list[$i]['school_name'] = $row['s_school_name'];
				$list[$i]['frdb'] = $row['s_frdb'];
				$list[$i]['frdb_mobile'] = $row['s_frdb_mobile'];
				$list[$i]['zzjgdm'] = $row['s_zzjgdm'];
				$list[$i]['dwxz'] = $row['i_dwxz'];
				$list[$i]['address'] = $row['s_address'];
				$list[$i]['brand'] = $row['brand'];
				$list[$i]['is_show'] = $row['is_show'];
				// 查找账号
				$sql = "SELECT `password`, `name` FROM `{$this->_dbtabpre}admin` WHERE `school_id` = '{$row['l_school_id']}'";
				$admin_info = $this->_getFirstRecord($sql);
				if($admin_info) {
					$list[$i]['login_account'] = $admin_info['name'];
					if(md5('123456') == $admin_info['password']) {
						$list[$i]['password_tips'] = '默认';
					} else {
						$list[$i]['password_tips'] = '修改';
					}
						
				} else {
					$list[$i]['login_account'] = '';
					$list[$i]['password_tips'] = '';
				}
				$i++;
			}

			return $list;
		}

		/**
		 * 获取校长列表
		 * @return array 
		 */

		public function getHeadmasterList($page='', $limit='') {
			if($page !== '' && $limit !== '') {
				$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE `i_user_type` = 2 AND `i_status` = 0 ORDER BY `l_user_id` ASC LIMIT $page, $limit";
			} else {
				$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE `i_user_type` = 2 AND `i_status` = 0 ORDER BY `l_user_id` ASC";
			}
			$list = array();
			$res = $this->_getAllRecords($sql);
			if ($res) {
				$list = $res;
				foreach ($list as $key => $value) {
					$school_id = $value['school_id'];
					$sql = "SELECT `s_school_name` FROM `{$this->_dbtabpre}school` WHERE `l_school_id` = '{$school_id}'";
					$res = $this->_getFirstRecord($sql);
					$list[$key]['school_name'] = $res['s_school_name'];
				}
			}
			return $list;
		}

		/**
		 * 删除单个校长
		 *
		 * @return 
		 * @author sun
		 **/
		public function delHeadmaster($id) {
			$sql = "UPDATE {$this->_dbtabpre}user SET `i_status` = 2 WHERE `l_user_id` = '{$id}'";
			$query = $this->_db->query($sql);
			return $query;	
		}

		// 新增用户记录
		public function insertHeadmasterInfo($arr) {

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
			$sql .= " `s_phone`,";
			$sql .= " `content`";
			$sql .= ") VALUES (";
			$sql .= " '嘻哈校长".$arr['user_phone']."',";
			$sql .= " '".$arr['s_password']."',";
			$sql .= " '".$arr['i_user_type']."',";
			$sql .= " '".$arr['i_status']."',";
			$sql .= " '".$arr['real_name']."',";
			$sql .= " '".$arr['user_phone']."',";
			$sql .= " '".$arr['content']."')";

			$res = $this->_db->query($sql);
			if($res) {
				$insert_id = $this->lastInertId();
				$sql = "INSERT INTO `{$this->_dbtabpre}users_info` (";
				$sql .= " `user_id`, `sex`, `age`, `identity_id`, `address`, `user_photo`, `school_id`, `province_id`, `city_id`, `area_id`) VALUES (";
				$sql .= " '".$insert_id."',";
				$sql .= " '".$arr['sex']."',";
				$sql .= " '".$arr['age']."',";
				$sql .= " '".$arr['identity_id']."',";
				$sql .= " '".$arr['address']."',";
				$sql .= " '".$arr['user_photo']."',";
				$sql .= " '".$arr['school_id']."',";
				$sql .= " '".$arr['province']."',";
				$sql .= " '".$arr['city']."',";
				$sql .= " '".$arr['area']."')";
				$result = $this->_db->query($sql);
				return 2;		
			} else {
				return 3;
			}
		}

		// 搜索校长
		public function getSearchHeadmasterList($page='', $limit='', $type=1, $keyword='') {

			// 校长姓名
			if($type == 1) {
				$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE `i_user_type` = 2 AND `i_status` = 0 AND `s_real_name` LIKE '%{$keyword}%' ORDER BY `l_user_id` ASC";
				if($page !== '' && $limit !== '') {
					$sql .= " LIMIT $page, $limit";
				} else {
					$sql .= "";
				}

			// 手机号码
			} else if($type == 2) {
				$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE `i_user_type` = 2 AND `i_status` = 0 AND `s_phone` LIKE '%{$keyword}%' ORDER BY `l_user_id` ASC";
				if($page !== '' && $limit !== '') {
					$sql .= " LIMIT $page, $limit";
				} else {
					$sql .= "";
				}
			//驾校名称
			} else if($type == 3) {
				$sql = "SELECT `l_school_id` FROM `{$this->_dbtabpre}school` WHERE `s_school_name` LIKE '%{$keyword}%' ";
				$school_id = $this->_getAllRecords($sql);
				$ids = array();
				if ($school_id) {
					foreach ($school_id as $key => $value) {
						$ids[] = $value['l_school_id']; 
					}	
				}
				$school_ids = implode(',', $ids);
				$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE `i_user_type` = 2 AND `i_status` = 0 AND i.`school_id` IN ($school_ids) ORDER BY `l_user_id` ASC";
				if($page !== '' && $limit !== '') {
					$sql .= " LIMIT $page, $limit";
				} else {
					$sql .= "";
				}
			
			} 
			$list = array();
			$res = $this->_getAllRecords($sql);
			if ($res) {
				$list = $res;
				foreach ($list as $key => $value) {
					$school_id = $value['school_id'];
					$sql = "SELECT `s_school_n ame` FROM `{$this->_dbtabpre}school` WHERE `l_school_id` = '{$school_id}'";
					$res = $this->_getFirstRecord($sql);
					$list[$key]['school_name'] = $res['s_school_name'];
				}
			}
			return $list;
		}

		//搜索驾校名称,驾校id及城市
		public function searchSchoolName($school_name) {
			$sql = "SELECT `s_school_name`, `l_school_id`, `s_address` FROM `{$this->_dbtabpre}school` WHERE `s_school_name` LIKE '%{$school_name}%'";
			$list = array();
			$res = $this->_getAllRecords($sql);
			if ($res) {
				$list = $res;
			}
			return $list;
		}

		// 获取所有轮播图
		public function getSchoolBannners($id) {
			$sql = "SELECT `s_imgurl` FROM `{$this->_dbtabpre}school` WHERE `l_school_id` = $id";
			$bannerlist = $this->_getFirstRecord($sql);
			$banner_list = array();
			$list = array();
			if($bannerlist['s_imgurl']) {
				$banner_list = json_decode($bannerlist['s_imgurl'], true);
				if(is_array($banner_list)) {
					foreach ($banner_list as $key => $value) {
						$list[$key]['s_all_imgurl'] = HTTP_HOST.$value;
						$list[$key]['s_imgurl'] = $value;
					}
				}	
				return $list;
			} else {
				return array();
			}
		}

		// 添加banner图
		public function addSchoolBanners($banner, $id) {

			$sql = "SELECT `s_imgurl` FROM `{$this->_dbtabpre}school` WHERE `l_school_id` = $id";
			$bannerlist = $this->_getFirstRecord($sql);

			$list = array();
			if($bannerlist['s_imgurl'] && $bannerlist['s_imgurl'] != null) {
				$banner_list = json_decode($bannerlist['s_imgurl'], true);
				$list = array_merge($banner_list, $banner);
			} else {
				$list = $banner;
			}
			$s_imgurl = json_encode($list);
			$sql = "UPDATE `{$this->_dbtabpre}school` SET `s_imgurl` = '".$s_imgurl."' WHERE `l_school_id` = $id";
			$res = $this->_db->query($sql);
			return $res;
		}

		// 删除banner图
		public function delBanner($url ,$id) {
			$sql = "SELECT `s_imgurl` FROM `{$this->_dbtabpre}school` WHERE `l_school_id` = $id";
			$school_info = $this->_getFirstRecord($sql);
			if($school_info['s_imgurl']) {
				$s_imgurl = json_decode($school_info['s_imgurl'], true);
				foreach ($s_imgurl as $key => $value) {
					if($url == $value) {
						unset($s_imgurl[$key]);
					}
				}
				// 删除图片
				if(file_exists($url)) {
					unlink($url);
				}
					
				// 删除数据库中
				$sql = "UPDATE `{$this->_dbtabpre}school` SET `s_imgurl` = '".json_encode($s_imgurl)."' WHERE `l_school_id` = $id";
				$res = $this->_db->query($sql);
				return $res;

			} else {
				return false;
			}
		}















	}
?>