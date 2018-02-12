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
				$sql = "SELECT * FROM {$this->_dbtabpre}school ORDER BY `l_school_id` DESC LIMIT $page, $limit";
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
				$i++;
			}

			return $list;
		}

		// 获取学校详情
		public function getSchoolDetail($id) {
			$sql = "SELECT * FROM {$this->_dbtabpre}school WHERE `l_school_id` = '{$id}'";
			$res = $this->_getFirstRecord($sql);
			$res['s_thumb'] = $res['s_thumb'] == '' ? 'upload/school/school_thumb.jpg' : $res['s_thumb'];
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
			$sql .= " `s_thumb` 			= '".$arr['s_thumb']."', ";
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
			$sql .= " `shifts_intro` 		= '".$arr['shifts_intro']."', ";
			$sql .= " `s_location_x` 		= '".$arr['s_location_x']."', ";
			$sql .= " `s_location_y` 		= '".$arr['s_location_y']."' ";
			$sql .= " WHERE `l_school_id` 	= ".$arr['l_school_id'];

			$query = $this->_db->query($sql);
			return $query;
		}

		// 增加驾校信息
		public function InsertSchoolInfo($arr) {
			$sql = "INSERT `{$this->_dbtabpre}school` (";
			$sql .= "`s_school_name`, `s_frdb`, `s_frdb_mobile`, `s_frdb_tel`, ";
			$sql .= "`s_yyzz`, `s_zzjgdm`, `i_dwxz`, `province_id`, `city_id`, ";
			$sql .= "`s_address`, `dc_base_je`, `dc_bili`, `s_yh_name`, `s_yh_zhanghao`, `s_yh_huming`, `s_shuoming`) VALUES (";
			$sql .= "'".$arr['s_school_name']."', ";
			$sql .= "'".$arr['s_frdb']."', ";
			$sql .= "'".$arr['s_frdb_mobile']."', ";
			$sql .= "'".$arr['s_frdb_tel']."', ";
			$sql .= "'".$arr['lisence_imgurl']."', ";
			$sql .= "'".$arr['s_zzjgdm']."', ";
			$sql .= "'".$arr['i_dwxz']."', ";
			$sql .= "'".$arr['province_id']."', ";
			$sql .= "'".$arr['city_id']."', ";
			$sql .= "'".$arr['s_address']."', ";
			$sql .= "'".$arr['dc_base_je']."', ";
			$sql .= "'".$arr['dc_bili']."', ";
			$sql .= "'".$arr['s_yh_name']."', ";
			$sql .= "'".$arr['s_yh_zhanghao']."', ";
			$sql .= "'".$arr['s_yh_huming']."', ";
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

		// 获取班制信息
		public function getShiftsList($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_shifts` WHERE `sh_school_id` = ".$id;
			$shifts_list = $this->_getAllRecords($sql);
			if($shifts_list) {
				foreach ($shifts_list as $key => $value) {
					$shifts_list[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);				
				}
			}
			return $shifts_list;
		}

		// 获取报名点信息
		public function getTrainAddressList($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_train_location` WHERE `tl_school_id` = $id";
			$address_list = $this->_getAllRecords($sql);
			if($address_list) {
				foreach ($address_list as $key => $value) {
					$address_list[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);
					if($value['tl_imgurl']) {
						$th_imgurl = json_decode($value['tl_imgurl'], true);
						$address_list[$key]['tl_imgurl_arr'] = $th_imgurl;
					} else {

					}
				}
			}
			return $address_list;
		}

		// 新增班制
		public function setShifts($arr) {
			// $sql = "SELECT * FROM `{$this->_dbtabpre}school_shifts` WHERE `sh_school_id` = ".$arr['sh_school_id']." AND `sh_type` = ".$arr['sh_type'];
			// $shifts_info = $this->_getFirstRecord($sql);

			// if($shifts_info) {
			// 	$sql = "UPDATE `{$this->_dbtabpre}school_shifts` SET ";
			// 	$sql .= " `sh_title` 			= '".$arr['sh_title']."', ";
			// 	$sql .= " `sh_money` 			= '".$arr['sh_money']."', ";
			// 	$sql .= " `sh_original_money` 	= '".$arr['sh_original_money']."', ";
			// 	$sql .= " `sh_description_1` 	= '".$arr['sh_description_1']."', ";
			// 	$sql .= " `sh_description_2` 	= '".$arr['sh_description_2']."', ";
			// 	$sql .= " `addtime` 			= '".$arr['addtime']."' ";
			// 	$sql .= " WHERE `sh_school_id` 	= '".$arr['sh_school_id']."' AND `sh_type` = '".$arr['sh_type']."'";

			// 	$res = $this->_db->query($sql);
			// 	return $res;
			// } else {
				$sql = "INSERT INTO `{$this->_dbtabpre}school_shifts` (`sh_school_id`, `sh_title`, `sh_money`, `sh_original_money`, `sh_type`, `sh_description_2`, `sh_description_1`, `addtime`) VALUES (";
				$sql .= " '".$arr['sh_school_id']."', '".$arr['sh_title']."', '".$arr['sh_money']."', '".$arr['sh_original_money']."', '".$arr['sh_type']."', '".$arr['sh_description_2']."', '".$arr['sh_description_1']."', '".$arr['addtime']."' )";
				$res = $this->_db->query($sql);
				return $res;	
			// }
		}

		// 更新班制
		public function updateShifts($arr) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_shifts` WHERE `id` = ".$arr['id'];
			$shifts_info = $this->_getFirstRecord($sql);

			if($shifts_info) {
				$sql = "UPDATE `{$this->_dbtabpre}school_shifts` SET ";
				$sql .= " `sh_title` 			= '".$arr['sh_title']."', ";
				$sql .= " `sh_money` 			= '".$arr['sh_money']."', ";
				$sql .= " `sh_type` 			= ".$arr['sh_type'].", ";
				$sql .= " `sh_original_money` 	= '".$arr['sh_original_money']."', ";
				$sql .= " `sh_description_1` 	= '".$arr['sh_description_1']."', ";
				$sql .= " `sh_description_2` 	= '".$arr['sh_description_2']."', ";
				$sql .= " `deleted` 			= ".$arr['deleted'].", ";
				$sql .= " `addtime` 			= ".$arr['addtime']." ";
				$sql .= " WHERE `id` 			= ".$arr['id'];

				$res = $this->_db->query($sql);
				return $res;
			}
		}

		// 获取当前班制信息
		public function getShiftsDetail($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_shifts` WHERE `id`= $id";
			$res = $this->_getFirstRecord($sql);
			$res['addtime'] = date('Y-m-d H:i', $res['addtime']);
			return $res;
		}

		// 添加报名点
		public function insertAddress($arr) {
			$sql = "INSERT INTO `{$this->_dbtabpre}school_train_location` (";
			$sql .= "`tl_school_id`, `tl_train_address`, `tl_location_x`, `tl_location_y`, `tl_phone`, `addtime`) VALUES (";
			$sql .= "'".$arr['tl_school_id']."', '".$arr['tl_train_address']."', '".$arr['tl_location_x']."', '".$arr['tl_location_y']."', '".$arr['tl_phone']."', '".$arr['addtime']."')";

			$res = $this->_db->query($sql);
			return $res;
		}

		// 获取报名点详情
		public function getAddressDetail($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_train_location` WHERE `id` = $id";
			$res = $this->_getFirstRecord($sql);
			$res['addtime'] = date('Y-m-ds H:i', $res['addtime']);
			return $res;
		}

		// 更新报名点
		public function updateAddressInfo($arr) {
			$sql = "UPDATE `{$this->_dbtabpre}school_train_location` SET ";
			$sql .= "`tl_train_address` = '".$arr['tl_train_address']."', ";
			$sql .= "`tl_location_x` = '".$arr['tl_location_x']."', ";
			$sql .= "`tl_location_y` = '".$arr['tl_location_y']."', ";
			$sql .= "`tl_phone` = '".$arr['tl_phone']."', ";
			$sql .= "`addtime` = '".$arr['addtime']."' ";
			$sql .= " WHERE `id` = '".$arr['id']."' AND `tl_school_id` = '".$arr['tl_school_id']."'";

			$res = $this->_db->query($sql);
			return $res;
		}

		// 删除班制
		public function delSchoolShifts($id) {
			$sql = "SELECT 1 FROM `{$this->_dbtabpre}school_shifts` WHERE `id` = $id";
			$shifts_info = $this->_getFirstRecord($sql);
			if($shifts_info) {
				//$sql = "DELETE FROM `{$this->_dbtabpre}school_shifts` WHERE `id` = $id";
                //逻辑删除 deleted=1-正常 2-删除
				$sql = " UPDATE `{$this->_dbtabpre}school_shifts` SET `deleted` = 2 WHERE `id` = $id";
				$res = $this->_db->query($sql);
				return $res;
			} else {
				return false;
			}
				
		}

		// 删除班制
		public function delSchoolAddress($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_train_location` WHERE `id` = $id";
			$school_address_info = $this->_getFirstRecord($sql);

			if($school_address_info) {
				$sql = "DELETE FROM `{$this->_dbtabpre}school_train_location` WHERE `id` = $id";
				$res = $this->_db->query($sql);
				return $res;
			} else {
				return false;
			}		
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

		/**
		 * 通过ajax获取数据统计
		 * @param $school_id int 学校ID
		 * @param $type int 订单类型 1：报名驾校 2：预约学车
		 * @param $status int 订单状态
		 * @param $group int 是否以学员分组 0：不分组 1：分组
		 * @return int
		 * @author chenxi
		 **/
		public function getAjaxStatistics($school_id, $type, $status, $group=0) {
			switch ($type) {
				case '1': // 报名驾校订单统计
					if($status == 1) {
						$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = '{$school_id}' AND `so_order_status` != 101 AND ((`so_pay_type` = 1 AND `so_order_status` = 1) OR (`so_pay_type` = 2 AND `so_order_status` = 3)  OR (`so_pay_type` = 2 AND `so_order_status` = 1) OR (`so_pay_type` = 3 AND `so_order_status` = 1))";

					} else if($status == 2) {
						$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = '{$school_id}' AND `so_order_status` = 1";

					} else if($status == 3) {
						$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = '{$school_id}' AND ((`so_pay_type` = 1 AND `so_order_status` = 3) OR (`so_pay_type` = 2 AND `so_order_status` = 2) OR (`so_pay_type` = 3 AND `so_order_status` = 3))";

					} else {
						$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = '{$school_id}' AND `so_order_status` = '{$status}'";

					}
						
					if($group == 1) {
						$sql .= " GROUP BY `so_user_id`";
					}
					break;
				case '2': // 预约学车订单统计
					// 获取学校教练ID
					$coach_ids = array();
					$csql = "SELECT `l_coach_id` FROM `{$this->_dbtabpre}coach` WHERE `s_school_name_id` = '{$school_id}'";
					$coach_info = $this->_getAllRecords($csql);
					if($coach_info) {
						foreach ($coach_info as $key => $value) {
							$coach_ids[] = $value['l_coach_id'];
						}
					} else {
						$coach_ids[] = 0;
					}
					$sql = "SELECT * FROM `{$this->_dbtabpre}study_orders` WHERE l_coach_id IN (".implode(',', $coach_ids).") AND `i_status` = '{$status}'";
					if($group == 1) {
						$sql .= " GROUP BY `l_user_id`";
					}
					break;
				case '3': // 本校学员
					$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE i.`school_id` = '{$school_id}' AND u.`i_status` != 2";
					if($status == 2) {
						$sql .= " AND u.`i_from` = '{$status}'";
					} else {
						$sql .= " AND u.`i_from` != 2";
					}
					if($group == 1) {
						$sql .= " GROUP BY u.`l_user_id`";
					}
					// echo $sql;
					break;
				default:
					$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = '{$school_id}' AND `so_order_status` = '{$status}'";
					if($group == 1) {
						$sql .= " GROUP BY `so_user_id`";
					}
					break;
			}
			$res = $this->_getAllRecords($sql);
			return count($res);
		}

		/**
		 * 获取一年的驾校报名驾校和预约学车订单数据
		 * @param $school_id int 学校ID
		 * @param $type int 订单类型 1：报名驾校 2：预约学车
		 * @return array
		 * @author chenxi
		 **/
		public function getAjaxOrder($school_id, $type) {

			$list = array();
			$yearmonth = $this->getYearMonth();
			if($yearmonth) {
				// 获取教练ID
				$sql = "SELECT `l_coach_id` FROM `{$this->_dbtabpre}coach` WHERE `s_school_name_id` = '{$school_id}'";
				$coach_ids_arr = $this->_getAllRecords($sql);
				$coach_ids = array();
				if($coach_ids_arr) {
					foreach ($coach_ids_arr as $key => $value) {
						$coach_ids[] = $value['l_coach_id'];
					}
				} else {
					$coach_ids[] = 0;
				}
				foreach ($yearmonth as $key => $value) {
					// 报名驾校
					$sql = "SELECT count(*) as num FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = '{$school_id}' AND `addtime` > '{$value['start_dateformat']}' AND `addtime` < '{$value['end_dateformat']}' AND `so_order_status` != 101 AND ((`so_pay_type` = 1 AND `so_order_status` != 3) OR (`so_pay_type` = 2 AND `so_order_status` != 2))";
					$signup_order = $this->_getFirstRecord($sql);
					$list['signup'][$key] = $signup_order['num'];

					// 预约学车
					$sql = "SELECT count(*) as num FROM `{$this->_dbtabpre}study_orders` as o LEFT JOIN `cs_coach_appoint_time` as t ON o.`appoint_time_id` = t.`id` WHERE o.`l_coach_id` IN (".implode(',', $coach_ids).") AND o.`dt_order_time` > '".$value['start_dateformat']."' AND o.`dt_order_time` < '".$value['end_dateformat']."'  AND o.`i_status` = 3";
					$learncar_order = $this->_getFirstRecord($sql);
					$list['learncar'][$key] = $learncar_order['num'];
				}
			}
			return $list;
		}

        /**
         * 获取一年中每个月的天数以及开始时间和结束时间
         *
         * @return array
         * @author chenxi
         **/
        private function getYearMonth() {
			$list = array();
			$year = date('Y', time());

			for($i=1; $i<=12; $i++) {
				$t0 = date('t', strtotime($year.'-'.$i.'-1'));      // 一个月一共有几天
				$t1 = mktime(0,0,0,$i,1,$year);        // 创建当月开始时间 
				$t2 = mktime(23,59,59,$i,$t0,$year);       // 创建当月结束时间
				$list[$i]['start_dateformat'] = $t1;
				$list[$i]['end_dateformat'] = $t2;
				$list[$i]['date'] = $year.'-'.$i.'-1';
				$list[$i]['monthday'] = $t0;
			}
			return $list;
		}
		/**
		 * 获取月时间和订单数目
		 * @param $school_id int 学校ID
		 * @param $type int 是否当月 1：是 2：不是
		 * @param $m int 月份
		 * @return array
		 * @author 
		 **/
		public function getAjaxMonthOrders($school_id, $type=2, $m='') {
			$list = array();
			$year = date('Y', time());
			if($type == 1) {
				$month = date('m', time());
			} else {
				$month = $m;
			}

			$t0 = date('t', strtotime($year.'-'.$month.'-1')); // 获取当前月份的天数
			$t1 = mktime(0,0,0,$month,1,$year);        // 创建当月开始时间 
			$t2 = mktime(23,59,59,$month,$t0,$year);   // 创建当月结束时间
			$list['month'] = $month;
			$list['monthday'] = $t0;
			$list['start_dateformat'] = $t1;
			$list['end_dateformat'] = $t2;

			// 获取当前学校教练ID
			$sql = "SELECT `l_coach_id` FROM `{$this->_dbtabpre}coach` WHERE `s_school_name_id` = '{$school_id}'";
			$coach_ids_arr = $this->_getAllRecords($sql);
			$coach_ids = array();
			if($coach_ids_arr) {
				foreach ($coach_ids_arr as $key => $value) {
					$coach_ids[] = $value['l_coach_id'];
				}
			} else {
				$coach_ids[] = 0;
			}
			for ($i=1; $i<=$t0; $i++) {
				$d0 = mktime(0,0,0,$month,$i,$year); // 当月当天开始时间
				$d1 = mktime(23,59,59,$month,$i,$year); // 当月当天结束时间
				$list['day'][$i]['start_dateformat'] = $d0;
				$list['day'][$i]['end_dateformat'] = $d1;
				// 获取每一天的订单统计
				// 报名驾校

				$sql = "SELECT count(*) as num FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = '{$school_id}' AND `addtime` > '{$d0}' AND `addtime` < '{$d1}' AND `so_order_status` != 101 AND ((`so_pay_type` = 1 AND `so_order_status` != 3) OR (`so_pay_type` = 2 AND `so_order_status` != 2))";
				$signup_order = $this->_getFirstRecord($sql);
				$list['signup'][$i] = $signup_order['num'];

				// 预约学车
				$sql = "SELECT count(*) as num FROM `{$this->_dbtabpre}study_orders` as o LEFT JOIN `cs_coach_appoint_time` as t ON o.`appoint_time_id` = t.`id` WHERE o.`l_coach_id` IN (".implode(',', $coach_ids).") AND o.`dt_order_time` > '".$d0."' AND o.`dt_order_time` < '".$d1."' AND o.`i_status` = 3";
				$learncar_order = $this->_getFirstRecord($sql);
				$list['learncar'][$i] = $learncar_order['num'];
			}
			return $list;
		}
		/**
		 * 获取日的订单统计
		 * @param $school_id int 学校ID
		 * @param $m int 月
		 * @param $d int 天 
		 * @return array
		 * @author chenxi
		 **/
		public function getAjaxDayOrders($school_id, $m='', $d='') {
			// 获取时间配置
			// $sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config` WHERE `school_id` = '{$school_id}' AND `status` = 1";
			$sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config` WHERE `status` = 1";
			$coach_time_config = $this->_getAllRecords($sql);
			$list = array();
			$year = date('Y', time());
			$list['month'] = $m;
			// 获取当前学校教练ID
			$sql = "SELECT `l_coach_id` FROM `{$this->_dbtabpre}coach` WHERE `s_school_name_id` = '{$school_id}'";
			$coach_ids_arr = $this->_getAllRecords($sql);
			$coach_ids = array();
			if($coach_ids_arr) {
				foreach ($coach_ids_arr as $key => $value) {
					$coach_ids[] = $value['l_coach_id'];
				}
			} else {
				$coach_ids[] = 0;
			}
			if($coach_time_config) {
				foreach ($coach_time_config as $key => $value) {
					$start_order_time = mktime($value['start_time'], 0, 0, $m, $d, $year);
					$end_order_time = mktime($value['end_time'], 0, 0, $m, $d, $year);
					$list['day'][$key]['start_time'] = $value['start_time'];
					$list['day'][$key]['end_time'] = $value['end_time'];
					$list['day'][$key]['start_order_time'] = $start_order_time;
					$list['day'][$key]['end_order_time'] = $end_order_time;
					// 获取每时间段订单总数
					// 报名驾校
					$sql = "SELECT count(*) as num FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = '{$school_id}' AND `addtime` > '{$start_order_time}' AND `addtime` < '{$end_order_time}' AND `so_order_status` != 101 AND ((`so_pay_type` = 1 AND `so_order_status` != 3) OR (`so_pay_type` = 2 AND `so_order_status` != 2))";
					// echo $sql;
					$signup_order = $this->_getFirstRecord($sql);
					$list['signup'][$key] = $signup_order['num'];

					// 预约学车
					$sql = "SELECT count(*) as num FROM `{$this->_dbtabpre}study_orders` as o LEFT JOIN `cs_coach_appoint_time` as t ON o.`appoint_time_id` = t.`id` WHERE o.`l_coach_id` IN (".implode(',', $coach_ids).") AND o.`dt_order_time` > '".$start_order_time."' AND o.`dt_order_time` < '".$end_order_time."' AND o.`i_status` = 3";
					$learncar_order = $this->_getFirstRecord($sql);
					$list['learncar'][$key] = $learncar_order['num'];
				}
			}
			return $list;
		}

		/**
		  * 显示驾校时间配置
		  **/
		public function getSchoolTimeConfig($school_id) {

			$sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config` WHERE `school_id` = '{$school_id}'";
			$_school_time_config = $this->_getAllRecords($sql);
			$is_set = array();
			if ($_school_time_config) {
				foreach($_school_time_config as $key => $value ) {
					$is_set[] = $value['start_time'];
				}
			}
			$sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config` WHERE `school_id` = 1";
			$school_time_config = $this->_getAllRecords($sql);
			$list = array();

			if ($school_time_config) {
				foreach($school_time_config as $key => $value ) {
					if(!empty($is_set)) {
						if(in_array($value['start_time'], $is_set)) {
							$list[$key]['is_set'] = 2;
						} else {
							$list[$key]['is_set'] = 1;
						}
					} else {
						$list[$key]['is_set'] = 1;	
					}

					$list[$key]['id'] = $value['id'];
					$list[$key]['start_time'] = $value['start_time'];
					$list[$key]['end_time'] = $value['end_time'];
					$list[$key]['license_no'] = $value['license_no'];
					$list[$key]['subjects'] = $value['subjects'];
					$list[$key]['price'] = $value['price'];

				}
			}
			return $list;
		}

		/**
		 * 更新驾校时间配置信息
		 * @param $school_id int 驾校ID
		 * @param $price array 价格数组
		 * @param $license_no array 牌照数组
		 * @param $subjects array 科目数组
		 * @param $start_time array 开始时间
		 * @param $end_time array 结束时间
		 * @return bool
		 * @author 
		 **/
		public function updateSchoolTime($school_id, $price, $license_no, $subjects, $start_time, $end_time) {

			if(!is_array($price) || !is_array($license_no) || !is_array($subjects) || !is_array($start_time) || !is_array($end_time)) {
				return false;
			}
			// 查找存不存在记录
			$sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config` WHERE `school_id` = '{$school_id}'";
			$stmt = $this->_getFirstRecord($sql);

			if($stmt) {
				$sql = "DELETE FROM `{$this->_dbtabpre}coach_time_config` WHERE `school_id` = '{$school_id}'";
				$query = $this->_db->query($sql);
				if(!$query) {
					return false;
				}
			}
			$sql = "";
			
			foreach ($price as $key => $value) {
				$sql = "INSERT INTO `{$this->_dbtabpre}coach_time_config` (`start_time`,`end_time`,`license_no`,`subjects`,`price`,`school_id`,`addtime`,`status`) VALUES ('{$start_time[$key]}','{$end_time[$key]}','{$license_no[$key]}','{$subjects[$key]}','{$price[$key]}','{$school_id}','".time()."', 1)";
				$query = $this->_db->query($sql);
			}
			return $query;
	     }

        // 更新驾校时间系统配置
        public function updateSchoolTimeLimit($arr) {
        	$sql = "SELECT * FROM `{$this->_dbtabpre}school_config` WHERE `l_school_id` = '{$arr['l_school_id']}'";
        	$stmt = $this->_getFirstRecord($sql);
        	if($stmt) {
	            $sql = "UPDATE `{$this->_dbtabpre}school_config` SET `i_cancel_order_time` = '{$arr['order_limit_time']}', `i_sum_appoint_time` = '{$arr['appoint_limit_time']}', `s_time_list` = '{$arr['s_time_list']}', `is_automatic` = '{$arr['is_automatic']}'";
	            $sql .= " WHERE `l_school_id` = '{$arr['l_school_id']}'";
	            $res = $this->_db->query($sql);
        	} else {
        		$sql = "INSERT INTO `{$this->_dbtabpre}school_config` (`l_school_id`, `i_cancel_order_time`, `i_sum_appoint_time`, `s_time_list`, `is_automatic`) VALUES (";
        		$sql .= "'{$arr['l_school_id']}', '{$arr['order_limit_time']}', '{$arr['appoint_limit_time']}', '{$arr['s_time_list']}', '{$arr['is_automatic']}')";	
				$res = $this->_db->query($sql);
        	}
            return $res;
        }

        // 获取驾校系统配置
        public function getSchoolSysconfig($school_id) {
    		$sql = "SELECT * FROM `{$this->_dbtabpre}school_config` WHERE `l_school_id` = '{$school_id}'";
    		$stmt = $this->_getFirstRecord($sql);
    		if($stmt) {
    			$stmt['s_time_arr_list'] = explode(',', $stmt['s_time_list']);
    		}
    		$list = array();
    		$list['sysconfig_info'] = $stmt;
    		$sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config` WHERE `status` = 1 AND `school_id` = 1";
    		$row = $this->_getAllRecords($sql);
    		if($row) {
    			foreach ($row as $key => $value) {
    				$start_minute = $value['start_minute'] == 0 ? '00' : $value['start_minute'];
    				$end_minute = $value['end_minute'] == 0 ? '00' : $value['end_minute'];
    				$row[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
    				$row[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;

    				if($stmt['s_time_arr_list']) {
    					if(in_array($value['id'], $stmt['s_time_arr_list'])) {
    						$row[$key]['is_set'] = 1;
    					} else {
    						$row[$key]['is_set'] = 2;
    					}
    				} else {
						$row[$key]['is_set'] = 2;
    				}
    			}
    		}
    		$list['time_list'] = $row;
    		return $list;
        }

        // 
      }

?>
