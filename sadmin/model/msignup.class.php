<?php  
	// 报名管理

	!defined('IN_FILE') && exit('Access Denied');

	class msignup extends mbase {

		// 获取预约计时班的订单
		public function getShiftsOrderList($page='', $limit='', $school_id, $shifts_id) {
			if($school_id == '') {
				return array();
			}

			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = $school_id  AND `so_order_status` != 101 AND `so_shifts_id` = $shifts_id ORDER BY `id` DESC ";
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
						
				}
				return $shifts_list;

			} else {
				return array();
			}
		}

		// 获取普通班的订单
		public function getGeneralShiftsList($page='', $limit='', $school_id) {
			if($school_id == '') {
				return array();
			}
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = $school_id  AND `so_order_status` != 101 AND `so_shifts_id` = 2";
			$shifts_list = $this->_getAllRecords($sql);
			return $shifts_list;
		}

		// 获取预约计时班的订单
		public function getVipShiftsList($page='', $limit='', $school_id) {
			if($school_id == '') {
				return array();
			}

			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = $school_id  AND `so_order_status` != 101 AND `so_shifts_id` = 3";
			$shifts_list = $this->_getAllRecords($sql);
			return $shifts_list;
		}

		// 通过身份证获取userid
		public function getUserInfoById($id) {
			$sql = "SELECT `user_id` FROM `{$this->_dbtabpre}users_info` WHERE `identity_id` = '".$id."'";
			$stmt = $this->_getFirstRecord($sql);
			return $stmt;
		}

		// 检测手机号在订单的重复性
		public function phonecheck($phone) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_phone` = '".$phone."' AND `so_order_status` != 101";
			$res = $this->_getFirstRecord($sql);
			return $res;
		} 

		// 检测身份证是否报名驾校
		public function identitycheck($identity_id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_user_identity_id` = '".$identity_id."' AND `so_order_status` != 101";
			$stmt = $this->_getFirstRecord($sql);
			return $stmt;
		}

		// 添加订单
		public function addSchoolOrder($arr) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_phone` = '".$arr['so_phone']."' AND `so_order_status` != 101";
			$stmt = $this->_getFirstRecord($sql);
			if($stmt) {
				return 1; // 号码已报名
			}

			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_user_identity_id` = '".$arr['so_user_identity_id']."' AND `so_order_status` != 101";
			$stmt = $this->_getFirstRecord($sql);
			if($stmt) {
				return 2;
			}

			// 插入订单
			$sql = "INSERT INTO `{$this->_dbtabpre}school_orders` (";
			$sql .= "`so_school_id`,`so_final_price`, `so_original_price`, `so_shifts_id`, `so_pay_type`, `so_order_status`, `so_comment_status`, `so_order_no`, `so_user_id`, `so_user_identity_id`, `so_licence`, `so_username`, `so_phone`, `addtime` ) VALUES (";
			$sql .= "'".$arr['so_school_id']."',";
			$sql .= "'".$arr['so_final_price']."',";
			$sql .= "'".$arr['so_original_price']."',";
			$sql .= "'".$arr['so_shifts_id']."',";
			$sql .= "'".$arr['so_pay_type']."',";
			$sql .= "'".$arr['so_order_status']."',";
			$sql .= "'".$arr['so_comment_status']."',";
			$sql .= "'".$arr['so_order_no']."',";
			$sql .= "'".$arr['so_user_id']."',";
			$sql .= "'".$arr['so_user_identity_id']."',";
			$sql .= "'".$arr['so_licence']."',";
			$sql .= "'".$arr['so_username']."',";
			$sql .= "'".$arr['so_phone']."',";
			$sql .= "'".time()."')";
			$res = $this->_db->query($sql);
			if($res) {
				return 3;
			} else {
				return 4;
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
        public function setOrderStatus($order_id, $pay_type, $order_status, $type) {

            $sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `id` = $order_id AND `so_order_status` != 101"; // so_order_status=101-订单删除
            $stmt = $this->_getFirstRecord($sql);

            if($stmt) {
                if (($pay_type == 1 || $pay_type == 3 || $pay_type == 4) && ($type = 1)) {
                    $pay_type = 2; // 通过后台修改订单为已付款时，要将支付方式变为线下支付
                }

                if($pay_type == 1) {
                    // 支付宝

                    $sql = "UPDATE `{$this->_dbtabpre}school_orders` SET `so_order_status` = '{$type}', `so_pay_type` = '{$pay_type}' WHERE `id` = $order_id";
                    $res = $this->_db->query($sql);
                    return $res;

                } else if($pay_type == 2) {
                    // 线下支付

                    $sql = "UPDATE `{$this->_dbtabpre}school_orders` SET `so_order_status` = '{$type}', `so_pay_type` = '{$pay_type}' WHERE `id` = $order_id";
                    $res = $this->_db->query($sql);
                    return $res;

                } else if($pay_type == 3) {
                    // 微信支付

                    $sql = "UPDATE `{$this->_dbtabpre}school_orders` SET `so_order_status` = '{$type}', `so_pay_type` = '{$pay_type}' WHERE `id` = $order_id";
                    $res = $this->_db->query($sql);
                    return $res;
                } else if ($pay_type == 4) {
                    // 银联支付
                    $sql = "UPDATE `{$this->_dbtabpre}school_orders` SET `so_order_status` = '{$type}', `so_pay_type` = '{$pay_type}' WHERE `id` = $order_id";
                    $res = $this->_db->query($sql);
                    return $res;
                }

            } else {
                return false;
            }
        }

		// 获取当前订单详情
		public function getOrderDetail($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `id` = $id AND `so_order_status` != 101";
			$stmt = $this->_getFirstRecord($sql);
			return $stmt;
		}

		// 修改订单
		public function updateSchoolOrders($arr) {
			$sql = "UPDATE `{$this->_dbtabpre}school_orders` SET";
			$sql .= " `so_username` = '".$arr['so_username']."', ";	
			$sql .= " `so_phone` = '".$arr['so_phone']."', ";	
			$sql .= " `so_licence` = '".$arr['so_licence']."', ";	
			$sql .= " `so_final_price` = '".$arr['so_final_price']."', ";	
			$sql .= " `so_original_price` = '".$arr['so_original_price']."', ";	
			$sql .= " `so_shifts_id` = '".$arr['so_shifts_id']."', ";	
			$sql .= " `so_pay_type` = '".$arr['so_pay_type']."', ";	
			$sql .= " `so_order_status` = '".$arr['so_order_status']."', ";	
			$sql .= " `so_comment_status` = '".$arr['so_comment_status']."', ";	
			$sql .= " `so_order_no` = '".$arr['so_order_no']."', ";	
			$sql .= " `so_user_identity_id` = '".$arr['so_user_identity_id']."' ";	
			$sql .= " WHERE `id` = '".$arr['id']."'";
			$res = $this->_db->query($sql);
			return $res;
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

		// 获取班制列表
		public function getShiftslist($school_id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_shifts` WHERE `sh_school_id` = $school_id";
			$list = $this->_getAllRecords($sql);
			if($list) {
				foreach ($list as $key => $value) {
					$list[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);
				}
			} else {
				return array();
			}
			return $list;
		}

		/**
		 * 搜索根据驾校设置的班制获取相应的订单
		 * @param $page int 开始页码
		 * @param $limit int 限制条数
		 * @param $school_id int 驾校ID
		 * @param $shifts_id int 班制ID
		 * @param $keyword string 关键词
		 * @param $paytype string 支付方式 （0：未选择 1：支付宝 2：线下支付）
		 * @param $membertype string 学员条件 （1：学员姓名 2：学员号码 3：订单号）
		 * @return array
		 * @author chenxi 
		 **/

		public function getSchoolOrdersByShifts($page, $limit, $school_id, $shifts_id, $keyword, $membertype, $paytype) {
			// 
			if($school_id == '') {
				return array();
			}
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = $school_id  AND `so_order_status` != 101 AND `so_shifts_id` = $shifts_id ";
			switch ($membertype) {
				case '1': // 学员姓名
					$sql .= " AND `so_username` LIKE '%".$keyword."%'";
					if($paytype == 0) {
						$sql .= '';
					} else {
						$sql .= ' AND `so_pay_type` = '.$paytype;
					}
					$sql .= ' ORDER BY `id` DESC';
					break;
				case '2': // 学员号码
					$sql .= " AND `so_phone` LIKE '%".$keyword."%'";
					if($paytype == 0) {
						$sql .= '';
					} else {
						$sql .= ' AND `so_pay_type` = '.$paytype;
					}
					break;
				case '3': // 订单号
					$sql .= " AND `so_order_no` LIKE '%".$keyword."%'";
					if($paytype == 0) {
						$sql .= '';
					} else {
						$sql .= ' AND `so_pay_type` = '.$paytype;
					}
					break;
				case '4': // 唯一支付识别码
					$sql .= " AND `s_zhifu_dm` LIKE '%".$keyword."%'";
					if($paytype == 0) {
						$sql .= '';
					} else {
						$sql .= ' AND `so_pay_type` = '.$paytype;
					}
					break;	
				default:
					$sql .= " AND `so_username` LIKE '%".$keyword."%'";
					if($paytype == 0) {
						$sql .= '';
					} else {
						$sql .= ' AND `so_pay_type` = '.$paytype;
					}
					break;
			}
		
			if($page == '' || $limit == '') {
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
					$shifts_list[$key]['pay_status'] = $value['so_pay_type'] == 1 ? '支付宝' : '线下支付';

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
						
				}
				return $shifts_list;
				
			} else {
				return array();
			}

		}

		/**
		 * 获取报名驾校订单信息(订单号，学员号码，下单时间，订单状态)
		 * @param $school_id 驾校id  
		 * @param $num 获取条数 
		 * @return $order_list 
		 * @author sun
		 **/
		public function getSignupOrderTips($school_id, $num=10) {
			if($school_id == '') {
				return array();
			}
			$sql = "SELECT `so_order_no`, `so_username`, `addtime`, `so_phone`, `so_pay_type`, `so_order_status` FROM `{$this->_dbtabpre}school_orders` ";
			$sql .= " WHERE `so_school_id` = ".$school_id." "; 
			$sql .= " AND `so_order_status` != 101 AND ((`so_pay_type` IN (1, 3, 4) AND `so_order_status` IN (1, 4) ) ";
			$sql .= " OR (`so_pay_type` = 2 AND `so_order_status` IN (1, 3) ))";
			$sql .= " ORDER BY `addtime` DESC LIMIT 10 ";
			$order_list = array();
			$order_list = $this->_getAllRecords($sql);
			if ($order_list) {
				foreach ($order_list as $key => $value) {
					// 线上（支付宝/微信/银联）
					if($value['so_pay_type'] == 1 || $value['so_pay_type'] == 3 || $value['so_pay_type'] == 4) {
						if($value['so_order_status'] == 1) {
							$order_list[$key]['so_order_status'] = '已付款';
						} else if($value['so_order_status'] == 4) {
							$order_list[$key]['so_order_status'] = '未付款';
						}
					// 线下支付
					} else if($value['so_pay_type'] == 2) {
						if($value['so_order_status'] == 1) {
							$order_list[$key]['so_order_status'] = '未付款';
						} else if($value['so_order_status'] == 3) {
							$order_list[$key]['so_order_status'] = '已付款';
						}
					} 
					//处理时间
					$order_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
				}
			}
			return $order_list;
		}

	}
?>
