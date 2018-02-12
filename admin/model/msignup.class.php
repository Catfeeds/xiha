<?php  
	// 报名管理

	!defined('IN_FILE') && exit('Access Denied');

	class msignup extends mbase {

		
		public function getShiftsOrderList($page='', $limit='') {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE  `so_order_status` != 101  ORDER BY `id` DESC ";
			if($page === '' || $limit === '') {
				$sql .= "";
			} else {
				$sql .= "LIMIT $page, $limit";
			}
			$shifts_list = $this->_getAllRecords($sql);

			if($shifts_list) {
				foreach ($shifts_list as $key => $value) {
					// 获取班制信息
					$sql = "SELECT `sh_title` FROM `{$this->_dbtabpre}school_shifts` WHERE `id` = ".$value['so_shifts_id'];
					$shifts_info = $this->_getFirstRecord($sql);
					if($shifts_info) {
						$shifts_list[$key]['sh_title'] = $shifts_info['sh_title'];
					} else {
						$shifts_list[$key]['sh_title'] = '';
					}

					$shifts_list[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);
					$shifts_list[$key]['pay_status'] = '废弃订单';
					$shifts_list[$key]['order_status'] = '废弃订单';

					// 线上（支付宝/微信/银联）
					if($value['so_pay_type'] == 1 || $value['so_pay_type'] == 3 || $value['so_pay_type'] == 4) {
						if($value['so_order_status'] == 1) {
							$shifts_list[$key]['order_status'] = '报名成功已付款';
							
						} else if($value['so_order_status'] == 2) {
							$shifts_list[$key]['order_status'] = '申请退款中';

						} else if($value['so_order_status'] == 3) {
							$shifts_list[$key]['order_status'] = '报名取消';
							
						} else if($value['so_order_status'] == 4) {
							$shifts_list[$key]['order_status'] = '报名成功未付款';
						}
						if ($value['so_pay_type'] == 1) {
							$shifts_list[$key]['pay_status'] = '支付宝支付';
						} elseif ($value['so_pay_type'] == 3) {
							$shifts_list[$key]['pay_status'] = '微信支付';
						} elseif ($value['so_pay_type'] == 4) {
							$shifts_list[$key]['pay_status'] = '银联支付';
						}

					// 线下支付
					} else if($value['so_pay_type'] == 2) {
						if($value['so_order_status'] == 1) {
							$shifts_list[$key]['order_status'] = '报名成功未付款';

						} else if($value['so_order_status'] == 2) {
							$shifts_list[$key]['order_status'] = '报名取消';

						} else if($value['so_order_status'] == 3) {
							$shifts_list[$key]['order_status'] = '报名成功已付款';

						} else if($value['so_order_status'] == 4) {
							$shifts_list[$key]['order_status'] = '申请退款中';

						}
						$shifts_list[$key]['pay_status'] = '线下支付';

					
					} 
					if($value['so_comment_status'] == 1) {
						$shifts_list[$key]['so_comment_status'] = '未评价';
					} else {
						$shifts_list[$key]['so_comment_status'] = '已评价';
					}
					//获取驾校名称
					$sql = "SELECT `s_school_name` FROM `{$this->_dbtabpre}school` WHERE `l_school_id` = ".$value['so_school_id'];
					$school_name = $this->_getFirstRecord($sql);
					if($shifts_info) {
						$shifts_list[$key]['school_name'] = $school_name['s_school_name'];
					} else {
						$shifts_list[$key]['school_name'] = '';
					}
						
				}
				return $shifts_list;
			} else {
				return array();
			}
		}
		
		// 删除报名驾校订单
		public function delSignupOrder($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `id` = $id AND `so_order_status` != 101";
			$stmt = $this->_getFirstRecord($sql);
			if($stmt) {
				//
				$sql = "UPDATE `{$this->_dbtabpre}school_orders` SET `so_order_status` = 101 WHERE `id` = $id";
				$res = $this->_db->query($sql);
				return $res;
			} else {
				return false;
			}
		}

		/**
		 * 设置订单状态
		 * @param int $order_id 订单ID
		 * @param int $pay_type 订单支付方式 1：支付宝 2：线下 3：微信 4：银行卡
		 * @param int $order_status 当前订单状态 
		 * @param int $type 需要设置的订单状态
		 * @return void
		 * @author 
		 **/

		// 设置订单状态
		public function setOrderStatus($order_id, $pay_type, $type) {

			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `id` = $order_id AND `so_order_status` != 101";
			$stmt = $this->_getFirstRecord($sql);

			if($stmt) {
				// 支付宝、微信、银联
				if($pay_type == 1 || $pay_type == 3 || $pay_type == 4 ) {

					$sql = "UPDATE `{$this->_dbtabpre}school_orders` SET `so_order_status` = '{$type}' WHERE `id` = $order_id";
					$res = $this->_db->query($sql);
					return $res;

				// 线下支付
				} else if($pay_type == 2) {

					$sql = "UPDATE `{$this->_dbtabpre}school_orders` SET `so_order_status` = '{$type}' WHERE `id` = $order_id";
					$res = $this->_db->query($sql);
					return $res;

				} else {
					return false;
				}
			} 

		}
		//报名驾校订单搜索
		public function getSignupOrders($page, $limit, $keyword, $membertype, $paytype) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_order_status` != 101 ";
			switch ($membertype) {
				case '1'://学员姓名
					$sql .= " AND `so_username` LIKE '%".$keyword."%' ";
					break;
				case '2'://学员号码
					$sql .= " AND `so_phone` LIKE '".$keyword."%' ";
					break;
				case '3'://订单号
					$sql .= " AND `so_order_no` LIKE '%".$keyword."%' ";
					break;
				case '4'://支付唯一识别码
					$sql .= " AND `s_zhifu_dm` LIKE '%".$keyword."%' ";
					break;		
				default:
					$sql .= " AND `so_username` LIKE '%".$keyword."%' ";
					break;
			}
			if($paytype == 0) {
				$sql .= '';
			} else {
				$sql .= ' AND `so_pay_type` = '.$paytype;
			}
			if($page == '' || $limit == '') {
				$sql .= "";
			} else {
				$sql .= "LIMIT $page, $limit";
			}	
			$order_list = $this->_getAllRecords($sql);

			if($order_list) {
				foreach ($order_list as $key => $value) {
					// 获取班制信息
					$sql = "SELECT `sh_title` FROM `{$this->_dbtabpre}school_shifts` WHERE `id` = ".$value['so_shifts_id'];
					$shifts_info = $this->_getFirstRecord($sql);
					if($shifts_info) {
						$order_list[$key]['sh_title'] = $shifts_info['sh_title'];
					} else {
						$order_list[$key]['sh_title'] = '';
					}

					$order_list[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);
					$order_list[$key]['pay_status'] = '废弃订单';
					$order_list[$key]['order_status'] = '废弃订单';

					// 线上（支付宝/微信/银联）
					if($value['so_pay_type'] == 1 || $value['so_pay_type'] == 3 || $value['so_pay_type'] == 4) {
						if($value['so_order_status'] == 1) {
							$order_list[$key]['order_status'] = '报名成功已付款';
							
						} else if($value['so_order_status'] == 2) {
							$order_list[$key]['order_status'] = '申请退款中';

						} else if($value['so_order_status'] == 3) {
							$order_list[$key]['order_status'] = '报名取消';
							
						} else if($value['so_order_status'] == 4) {
							$order_list[$key]['order_status'] = '报名成功未付款';
						}
						if ($value['so_pay_type'] == 1) {
							$order_list[$key]['pay_status'] = '支付宝支付';
						} elseif ($value['so_pay_type'] == 3) {
							$order_list[$key]['pay_status'] = '微信支付';
						} elseif ($value['so_pay_type'] == 4) {
							$order_list[$key]['pay_status'] = '银联支付';
						}

					// 线下支付
					} else if($value['so_pay_type'] == 2) {
						if($value['so_order_status'] == 1) {
							$order_list[$key]['order_status'] = '报名成功未付款';

						} else if($value['so_order_status'] == 2) {
							$order_list[$key]['order_status'] = '报名取消';

						} else if($value['so_order_status'] == 3) {
							$order_list[$key]['order_status'] = '报名成功已付款';

						} else if($value['so_order_status'] == 4) {
							$order_list[$key]['order_status'] = '申请退款中';

						}
						$order_list[$key]['pay_status'] = '线下支付';

					
					} 
					if($value['so_comment_status'] == 1) {
						$order_list[$key]['so_comment_status'] = '未评价';
					} else {
						$order_list[$key]['so_comment_status'] = '已评价';
					}
						
				}
				return $order_list;
			} else {
				return array();
			}
		}

	}
?>