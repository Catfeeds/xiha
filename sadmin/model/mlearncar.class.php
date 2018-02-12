<?php  

	// 订单模块
	

	!defined('IN_FILE') && exit('Access Denied');

	class mlearncar extends mbase {
		
		/**
		 * 获取订单列表
		 * @param $page int 页码
		 * @param $limit int 限制每页显示数量
		 * @return array
		 */

		public function getOrderList($page='', $limit='', $school_id, $type=1) {

			if(!$school_id) {
				return array();
			}
			// 通过school_id获取当前教练ID
			$sql = "SELECT `l_coach_id` FROM `{$this->_dbtabpre}coach` WHERE `s_school_name_id` = $school_id";
			$coach_info = $this->_getAllRecords($sql);
			$l_coach_id = array();

			if($coach_info) {
				foreach ($coach_info as $key => $value) {
					$l_coach_id[] = $value['l_coach_id'];	
				}
			}
			if(empty($l_coach_id)) {
				return array();
			}
			$sql = "SELECT * FROM {$this->_dbtabpre}study_orders WHERE `l_coach_id` IN (".implode(',', $l_coach_id).") AND `i_status` = $type ORDER BY `l_study_order_id` DESC ";
			if($page !== '' && $limit !== '') {
			 	$sql .= "LIMIT $page, $limit";
			} else {
				$sql .= "";
			}
			$study_orders = $this->_getAllRecords($sql);
			// print_r($study_orders);
			$lisence_name = array();
			$lesson_name = array();
			if($study_orders) {
				foreach ($study_orders as $key => $value) {

					$study_orders[$key]['dt_order_time'] = date('Y-m-d H:i:s', $value['dt_order_time']);
					// 获取学员身份证
					$sql = "SELECT `identity_id` FROM `{$this->_dbtabpre}users_info` WHERE `user_id` = ".$value['l_user_id'];
					$identity_info = $this->_getFirstRecord($sql);
					if($identity_info) {
						$study_orders[$key]['identity_id'] = $identity_info['identity_id'];
					} else {
						$study_orders[$key]['identity_id'] = '';
					}

					$lisence_name = array_filter(explode(',', $value['s_lisence_name'])); 
					$lesson_name = array_filter(explode(',', $value['s_lesson_name'])); 
					
					$study_orders[$key]['s_lisence_name'] = implode(',', $lisence_name);
					$study_orders[$key]['s_lesson_name'] = implode(',', $lesson_name);

					// 获得时间配置
					$sql = "SELECT * FROM `{$this->_dbtabpre}coach_appoint_time` WHERE `id` = ".$value['appoint_time_id'];
					$appoint_time = $this->_getFirstRecord($sql);
					$time_config_id_arr = array();
					if($appoint_time) {
						// 获取预约的时间配置
						$time_config_id_arr = array_filter(explode(',', $appoint_time['time_config_id']));
						$sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config` WHERE `id` IN (".implode(',', $time_config_id_arr).")";
						$time_config_arr = $this->_getAllRecords($sql);
						$time_config_time = array();
						if($time_config_arr) {
							foreach ($time_config_arr as $k => $v) {
								$time_config_time[] = $v['start_time'].':00-'.$v['end_time'].":00";
							}
						}
						$study_orders[$key]['dt_appoint_time'] = date("Y-m-d", strtotime($value['dt_appoint_time']));
						$study_orders[$key]['time_list'] = $time_config_time;
						$study_orders[$key]['appoint_time_list'] = date("Y-m-d", strtotime($value['dt_appoint_time'])).' '.implode(',', $time_config_time);
					} else {
						$study_orders[$key]['time_list'] = array();
						$study_orders[$key]['appoint_time_list'] = date("Y-m-d", strtotime($value['dt_appoint_time']));
					}
				}	
			}
			return $study_orders;
		}

		// 删除订单
		public function delOrder($id) {
			$sql = "DELETE FROM `{$this->_dbtabpre}study_orders` WHERE `l_study_order_id` IN ($id)";
			$query = $this->_db->query($sql);
			return $query;
		}

		// 获取当前订单详情
		public function getOrderDetail($id) {
			$sql = "SELECT * FROM {$this->_dbtabpre}study_orders WHERE `l_study_order_id` = ".$id;
			
			$row = $this->_getFirstRecord($sql);
			return $row;
		}

		// 获取报名驾校订单
		public function getSchoolOrderList($school_id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = $school_id AND `so_pay_type` = 2 AND `so_order_status` = 1 ORDER BY `addtime` DESC LIMIT 0,5";
			$stmt = $this->_getAllRecords($sql);
			if($stmt) {
				foreach ($stmt as $key => $value) {
					// 获取班制信息
					$sql = "SELECT * FROM `{$this->_dbtabpre}school_shifts` WHERE `id` = ".$value['so_shifts_id'];
					$res = $this->_getFirstRecord($sql);

					if($res) {
						$stmt[$key]['shifts_name'] = $res['sh_title'];
					} else {
						$stmt[$key]['shifts_name'] = '';
					}
						
				}
			}	
			return $stmt;
		}

		// 获取搜索订单
		public function getSearchOrderList($start='', $limit='', $school_id, $keyword, $paytype, $conditiontype, $order_id) {
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
			$sql = "SELECT * FROM `{$this->_dbtabpre}study_orders` WHERE `deal_type` = $paytype AND `i_status` = $order_id AND `l_coach_id` IN (".implode(',', $coach_ids).")";
			if($conditiontype == 1) { // 学员姓名
				$sql .= " AND `s_user_name` LIKE '%".$keyword."%'";
			} else if($conditiontype == 2) { // 学员号码
				$sql .= " AND `s_user_phone` LIKE '%".$keyword."%'";
			} else if($conditiontype == 3) { // 教练号码
				$sql .= " AND `s_coach_phone` LIKE '%".$keyword."%'";
			} else if($conditiontype == 4) { // 订单号
				$sql .= " AND `s_order_no` LIKE '%".$keyword."%'";
			} else if($conditiontype == 5) { // 唯一识别码
				$sql .= " AND `s_zhifu_dm` LIKE '%".$keyword."%'";
			}
			if($start !== '' && $limit !== '') {
				$sql .= " LIMIT $start, $limit";
			} else {
				$sql .= "";
			}
			$lisence_name = array();
			$lesson_name = array();

			$study_orders = $this->_getAllRecords($sql);
			foreach ($study_orders as $key => $value) {
				// 获取学员身份证
				$sql = "SELECT `identity_id` FROM `{$this->_dbtabpre}users_info` WHERE `user_id` = ".$value['l_user_id'];
				$identity_info = $this->_getFirstRecord($sql);
				if($identity_info) {
					$study_orders[$key]['identity_id'] = $identity_info['identity_id'];
				} else {
					$study_orders[$key]['identity_id'] = '';
				}
				$lisence_name = array_filter(explode(',', $value['s_lisence_name'])); 
				$lesson_name = array_filter(explode(',', $value['s_lesson_name'])); 
				
				$study_orders[$key]['s_lisence_name'] = implode(',', $lisence_name);
				$study_orders[$key]['s_lesson_name'] = implode(',', $lesson_name);

				$study_orders[$key]['dt_order_time'] = date('Y-m-d H:i:s', $value['dt_order_time']);
				// 获得时间配置
				$sql = "SELECT * FROM `{$this->_dbtabpre}coach_appoint_time` WHERE `id` = ".$value['appoint_time_id'];
				$appoint_time = $this->_getFirstRecord($sql);
				
				if($appoint_time) {
					// 获取预约的时间配置
					$time_config_id_arr = array_filter(explode(',', $appoint_time['time_config_id']));
					$sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config` WHERE `id` IN (".implode(',', $time_config_id_arr).")";
					$time_config_arr = $this->_getAllRecords($sql);
					$time_config_time = array();
					if($time_config_arr) {
						foreach ($time_config_arr as $k => $v) {
							$time_config_time[] = $v['start_time'].':00-'.$v['end_time'].":00";
						}
					}
					$study_orders[$key]['dt_appoint_time'] = date("Y-m-d", strtotime($value['dt_appoint_time']));
					$study_orders[$key]['time_list'] = $time_config_time;
					$study_orders[$key]['appoint_time_list'] = date("Y-m-d", strtotime($value['dt_appoint_time'])).' '.implode(',', $time_config_time);
				} else {
					$study_orders[$key]['dt_appoint_time'] = date("Y-m-d", strtotime($value['dt_appoint_time']));
					$study_orders[$key]['time_list'] = array();
					$study_orders[$key]['appoint_time_list'] = date("Y-m-d", strtotime($value['dt_appoint_time']));
				}
			}
			return $study_orders;
		}


		// 删除报名驾校订单
		public function delLearnCarOrder($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}study_orders` WHERE `l_study_order_id` = $id AND `i_status` != 101";
			$stmt = $this->_getFirstRecord($sql);
			if($stmt) {
				$sql = "UPDATE `{$this->_dbtabpre}study_orders` SET `i_status` = 101 WHERE `l_study_order_id` = $id";
				$res = $this->_db->query($sql);
				return $res;
			} else {
				return false;
			}
		}

		// 设置预约学车订单状态
		public function setOrderStatus($id, $status) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}study_orders` WHERE `l_study_order_id` = $id";
			$stmt = $this->_getFirstRecord($sql);
			if($stmt) {
				// 更新学车状态
				$sql = "UPDATE `{$this->_dbtabpre}study_orders` SET `i_status` = $status WHERE `l_study_order_id` = $id";
				$res = $this->_db->query($sql);
				if($res) {
					return $status;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		// 获取学员姓名
		public function getUsername($phone, $type) {
			$sql = "SELECT `s_real_name` FROM `{$this->_dbtabpre}user` WHERE `s_phone` = '".$phone."' AND `i_user_type` = $type";
			$stmt = $this->_getFirstRecord($sql);
			if($stmt) {
				return $stmt;
			} else {
				return false;
			}
		}

		// 添加订单
		public function insertStudyOrder($arr) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}study_orders` WHERE `s_order_no` = '".$arr['s_order_no']."'";
			$order_info = $this->_getFirstRecord($sql);

			if($order_info) {
				return 1;
			}

			$appoint_date = strtotime($arr['appoint_date']);
			$year = date('Y', $appoint_date);
			$month = date('m', $appoint_date);
			$day = date('d', $appoint_date);

			// 通过用户手机号获取用户ID
			$sql = "SELECT `l_user_id` FROM `{$this->_dbtabpre}user` WHERE `s_phone` = '".$arr['s_user_phone']."'";
			$user_info = $this->_getFirstRecord($sql);
			$user_id = 0;
			if($user_info) {
				$user_id = $user_info['l_user_id'];
			}

			// 判断当前教练的时间段是否被预约并且当前时间段是否被预约
			$sql = "SELECT o.`time_config_id`, o.`i_service_time` FROM `{$this->_dbtabpre}study_orders` as o LEFT JOIN `{$this->_dbtabpre}coach_appoint_time` as a ON o.`appoint_time_id` = a.`id` WHERE o.`l_coach_id` = '{$arr['coach_id']}' AND o.`l_user_id` = '{$user_id}' AND a.`year` = '{$year}' AND a.`month` = '{$month}' AND a.`day` = '{$day}'";
			$_service_time = $this->_getFirstRecord($sql);

			$time_config_ids = array();
			$service_time_count = 0;

			if($_service_time) {
				$time_config_ids = explode(',', $_service_time['time_config_id']);
				$intersect = array_intersect($arr['time_config_id'], $time_config_ids);
				if(!empty($intersect)) {
					return 6;
				}
				$service_time_count = $_service_time['i_service_time'];
			}
			if($service_time_count+$arr['i_service_time'] > 2) {
				return 2;
			}

			// 插入
			$sql = "INSERT INTO `{$this->_dbtabpre}coach_appoint_time` (`coach_id`, `time_config_id`, `user_id`, `year`, `month`, `day`, `addtime`) ";
			$sql .= "VALUES ('".$arr['coach_id']."', '".implode(',', $arr['time_config_id'])."', '".$user_id."', '".$year."', '".$month."', '".$day."', ".time().")";
			$res = $this->_db->query($sql);
			if($res) {
				$appoint_time_id = $this->_db->insert_id();
				// 生成订单
				$sql = "INSERT INTO `{$this->_dbtabpre}study_orders` (";
				$sql .= " `s_order_no`,";
				$sql .= " `dt_order_time`,"; 
				$sql .= " `appoint_time_id`,";
				$sql .= " `time_config_id`,";
				$sql .= " `l_user_id`,";
				$sql .= " `s_user_name`,";
				$sql .= " `s_user_phone`,";
				$sql .= " `l_coach_id`,";
				$sql .= " `s_coach_name`,";
				$sql .=	" `s_coach_phone`,";
				$sql .= " `s_lisence_name`,";
				$sql .= " `s_lesson_name`,";
				$sql .= " `dc_money`,";
				$sql .= " `dt_appoint_time`,";
				$sql .= " `i_service_time`,";
				$sql .= " `i_status`,";
				$sql .= " `s_zhifu_dm`,";
				$sql .= " `dt_zhifu_time`,";
				$sql .= " `deal_type`";
				$sql .= " ) VALUES (";
				$sql .= " '".$arr['s_order_no']."',";
				$sql .= " '".time()."',";
				$sql .= " '".$appoint_time_id."',";
				$sql .= " '".implode(',', $arr['time_config_id'])."',";
				$sql .= " '".$user_id."',";
				$sql .= " '".$arr['s_user_name']."',";
				$sql .= " '".$arr['s_user_phone']."',";
				$sql .= " '".$arr['coach_id']."',";
				$sql .= " '".$arr['s_coach_name']."',";
				$sql .= " '".$arr['coach_phone']."',";
				$sql .= " '".implode(',', $arr['lisence_id'])."',";
				$sql .= " '".implode(',', $arr['lesson_id'])."',";
				$sql .= " '".$arr['dc_money']."',";
				$sql .= " '".$arr['appoint_date']."',";
				$sql .= " '".$arr['i_service_time']."', 1, ";
				$sql .= " '".$this->guid(false)."',";
				$sql .= " '".time()."',";
				$sql .= " '".$arr['deal_type']."')";
				$row = $this->_db->query($sql);
				if($row) {
					return 200;
				} else {
					// 删除appoint_time数据
					$sql = "DELETE FROM `{$this->_dbtabpre}coach_appoint_time` WHERE `id` = $appoint_time_id";
					$stmt = $this->_db->query($sql);
					if($stmt) {
						return 6;
					} else {
						return 4;
					}
				}
			} else {
				return 3;
			}
		}

		// 随机生成字符串
		public function guid($opt = true){       //  Set to true/false as your default way to do this.

		    if( function_exists('com_create_guid')) {
		        if( $opt ){ 
		        	return com_create_guid(); 
		        } else { 
		        	return trim( com_create_guid(), '{}' ); 
		        }
		    } else {
		        mt_srand( (double)microtime() * 10000 );    // optional for php 4.2.0 and up.
		        $charid = strtoupper( md5(uniqid(rand(), true)) );
		        $hyphen = chr( 45 );    // "-"
		        $left_curly = $opt ? chr(123) : "";     //  "{"
		        $right_curly = $opt ? chr(125) : "";    //  "}"
		        $uuid = $left_curly
		            . substr( $charid, 0, 8 ) . $hyphen
		            . substr( $charid, 8, 4 ) . $hyphen
		            . substr( $charid, 12, 4 ) . $hyphen
		            . substr( $charid, 16, 4 ) . $hyphen
		            . substr( $charid, 20, 12 )
		            . $right_curly;
		        return $uuid;
		    }
		}
		
		// 检测订单号是否重复
		public function getLearncarOrdernoCheck($no) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}study_orders` WHERE `s_order_no` = '{$no}'";
			$stmt = $this->_getFirstRecord($sql);
			if($stmt) {
				return true;
			} else {
				return false;
			}
		}
	}

?>