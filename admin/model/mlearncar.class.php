<?php  
	// 报名订单模块

	!defined('IN_FILE') && exit('Access Denied');

	class mlearncar extends mbase {

		/**
		 * 获取报名驾校的学车订单列表
		 * @param $page int 页码
		 * @param $limit int 限制每页显示数量
		 * @return array 报名订单列表
		 */

		public function getSignuplist($page='', $limit='') {
			
			if($page !== '' && $limit !== '') {
				$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` ORDER BY `addtime` DESC LIMIT $page, $limit";
			} else {
				$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` ORDER BY `addtime` DESC";
			}
			$query = $this->_db->query($sql);
			$list = array();
			$i = 0;
			while($row = $this->_db->fetch_array($query)) {
				$list[$i]['signup_id'] = $row['id'];//ID
				$list[$i]['signup_no'] = $row['so_order_no'];      //订单号
				$list[$i]['school_id'] = $row['so_school_id'];//驾校ID
				$list[$i]['user_name'] = $row['so_username'];//用户名
				$list[$i]['user_phone'] = $row['so_phone'];//用户手机号
				$list[$i]['user_licence'] = $row['so_licence'];//驾照类型			
				if($row['so_order_status'] == 1){   //订单状态 1：未完成 2：已完成 3：取消订单
					$list[$i]['signup_status'] = "未完成";
				}elseif ($row['so_order_status'] == 2) {
					$list[$i]['signup_status'] = "已完成";
				}else if ($row['so_order_status'] == 3){
					$list[$i]['signup_status'] = "已取消订单";
				} 
				if($row['so_pay_type'] == 1) {   //交易类型  1：支付宝支付 2：线下
					$list[$i]['pay_type'] = "支付宝支付";
				}else{
					$list[$i]['pay_type'] = "线下支付";
				}					
				$list[$i]['final_price'] = $row['so_final_price']; //最终价格
				$list[$i]['addtime'] = date('Y-m-d H:i', $row['addtime']); //下单时间
				$i++;
			}

			return $list;
		}

		/**
		 * 获取预约学车订单列表
		 * @param $page int 页码
		 * @param $limit int 限制每页显示数量
		 * @return array
		 */

		public function getOrderList($page='', $limit='', $type=1) {

			// 通过school_id获取当前教练ID
			$sql = "SELECT `l_coach_id` FROM `{$this->_dbtabpre}coach`";
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
			//$sql = "SELECT * FROM {$this->_dbtabpre}study_orders WHERE `l_coach_id` IN (".implode(',', $l_coach_id).") AND `i_status` = $type ORDER BY `l_study_order_id` DESC ";
            //关联查询，获取驾校名称
			$sql = "SELECT ord.*, school.s_school_name FROM {$this->_dbtabpre}study_orders AS ord LEFT JOIN {$this->_dbtabpre}coach AS coach ON coach.l_coach_id = ord.l_coach_id LEFT JOIN {$this->_dbtabpre}school AS school ON school.l_school_id = coach.s_school_name_id WHERE ord.`l_coach_id` IN (".implode(',', $l_coach_id).") AND ord.`i_status` = $type ORDER BY ord.`l_study_order_id` DESC ";
			if($page !== '' && $limit !== '') {
			 	$sql .= "LIMIT $page, $limit";
			} else {
				$sql .= "";
			}
			$study_orders = $this->_getAllRecords($sql);
			$lisence_name = array();
			$lesson_name = array();
			if($study_orders) {
                // 获取订单用户的信息
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
                        $sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config` WHERE `id` IN " ; 
                        //(".implode(',', $time_config_id_arr).")";
                        if ($time_config_id_arr) {
                            $sql .= "(" . implode(',', $time_config_id_arr) . ")";
                        } else {
                            $sql .= "(0)";
                        }
						$time_config_arr = $this->_getAllRecords($sql);
						$time_config_time = array();
						if($time_config_arr) {
							foreach ($time_config_arr as $k => $v) {
								$time_config_time[] = $v['start_time'].':00-'.$v['end_time'].":00";
							}
						}
						$study_orders[$key]['appoint_time_list'] = date("Y-m-d", strtotime($value['dt_appoint_time'])).' '.implode(',', $time_config_time);
					} else {
						$study_orders[$key]['appoint_time_list'] = date("Y-m-d", strtotime($value['dt_appoint_time']));
					}
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
		
		// 获取搜索订单
		public function getSearchOrderList($start='', $limit='', $keyword, $paytype, $conditiontype, $order_id) {
			// 获取教练ID
			$sql = "SELECT `l_coach_id` FROM `{$this->_dbtabpre}coach` ";
			$coach_ids_arr = $this->_getAllRecords($sql);
			$coach_ids = array();
			if($coach_ids_arr) {
				foreach ($coach_ids_arr as $key => $value) {
					$coach_ids[] = $value['l_coach_id'];
				}
			} else {
				$coach_ids[] = 0;
			}
			//$sql = "SELECT ord.*, school.s_school_name FROM {$this->_dbtabpre}study_orders AS ord LEFT JOIN {$this->_dbtabpre}coach AS coach ON coach.l_coach_id = ord.l_coach_id LEFT JOIN {$this->_dbtabpre}school AS school ON school.l_school_id = coach.s_school_name_id WHERE ord.`l_coach_id` IN (".implode(',', $l_coach_id).") AND ord.`i_status` = $type ORDER BY ord.`l_study_order_id` DESC ";
            // 添加驾校名称关联
			$sql = "SELECT ord.*, school.s_school_name FROM {$this->_dbtabpre}study_orders AS ord LEFT JOIN {$this->_dbtabpre}coach AS coach ON coach.l_coach_id = ord.l_coach_id LEFT JOIN {$this->_dbtabpre}school AS school ON school.l_school_id = coach.s_school_name_id WHERE ord.`l_coach_id` IN (".implode(',', $coach_ids).")";

            // 支付方式
            if ($paytype > 0) {
                $sql .= " AND ord.`deal_type` = $paytype ";
            }

			if($conditiontype == 1) { // 学员姓名
				$sql .= " AND ord.`s_user_name` LIKE '%".$keyword."%'";
			} else if($conditiontype == 2) { // 学员号码
				$sql .= " AND ord.`s_user_phone` LIKE '%".$keyword."%'";
			} else if($conditiontype == 3) { // 教练号码
				$sql .= " AND ord.`s_coach_phone` LIKE '%".$keyword."%'";
			} else if($conditiontype == 4) { // 订单号
				$sql .= " AND ord.`s_order_no` LIKE '%".$keyword."%'";
			} else if($conditiontype == 5) { // 唯一支付生成码
				$sql .= " AND ord.`s_zhifu_dm` LIKE '%".$keyword."%'";
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
					$study_orders[$key]['appoint_time_list'] = date("Y-m-d", strtotime($value['dt_appoint_time'])).' '.implode(',', $time_config_time);
				} else {
					$study_orders[$key]['appoint_time_list'] = date("Y-m-d", strtotime($value['dt_appoint_time']));
				}
			}
			return $study_orders;
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

	}
?>
