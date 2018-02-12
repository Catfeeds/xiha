<?php  

	// 会员模块
	

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

		public function getMemberList($page='', $limit='', $school_id, $i_status='') {
			if($page !== '' && $limit !== '') {
				$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`i_user_type` = 0 AND i.`school_id` = $school_id ";

				if($i_status == '') {
					$sql .= " AND u.`i_status` != 2 ORDER BY u.`l_user_id` DESC LIMIT $page, $limit";
				} else {
					$sql .= " AND u.`i_status` = $i_status ORDER BY u.`l_user_id` DESC LIMIT $page, $limit";
				}
					
			} else {
				$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`i_user_type` = 0 AND i.`school_id` = $school_id ";

				if($i_status == '') {
					$sql .= " AND u.`i_status` != 2 ORDER BY u.`l_user_id` DESC";
				} else {
					$sql .= " AND u.`i_status` = $i_status ORDER BY u.`l_user_id` DESC";	
				}

					
			}
			$res = $this->_getAllRecords($sql);
			return $res;
		}

		// 获取单个会员信息
		public function getMemberInfo($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`l_user_id` = ".$id;
			$row = $this->_getFirstRecord($sql);
			return $row;
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

			// 判断当前订单号是否已被使用
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_order_no` = '".$arr['so_order_no']."'";
			$stmt = $this->_getFirstRecord($sql);
			if($stmt) {
				return 6;
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
				$sql .= " `user_id`, `sex`, `age`, `identity_id`, `address`, `user_photo`, `school_id`, `province_id`, `city_id`, `area_id`, `photo_id`, `learncar_status`) VALUES (";
				$sql .= " '".$insert_id."',";
				$sql .= " '".$arr['sex']."',";
				$sql .= " '".$arr['age']."',";
				$sql .= " '".$arr['identity_id']."',";
				$sql .= " '".$arr['address']."',";
				$sql .= " '".$arr['user_photo']."',";
				$sql .= " '".$arr['school_id']."',";
				$sql .= " '".$arr['province']."',";
				$sql .= " '".$arr['city']."',";
				$sql .= " '".$arr['area']."',";
				$sql .= " '1',";
				$sql .= " '".$arr['learncar_status']."')";

				$result = $this->_db->query($sql);
				if($result) {
					// 插入订单
					$sql = "INSERT INTO `{$this->_dbtabpre}school_orders` (";
					$sql .= "`so_school_id`,`so_final_price`, `so_original_price`, `so_shifts_id`, `so_pay_type`, `so_order_status`, `so_comment_status`, `so_order_no`, `so_user_id`, `so_user_identity_id`, `so_licence`, `so_username`, `so_phone`, `addtime` ) VALUES (";
					$sql .= "'".$arr['school_id']."',";
					$sql .= "'".$arr['so_final_price']."',";
					$sql .= "'".$arr['so_original_price']."',";
					$sql .= "'".$arr['so_shifts_id']."',";
					$sql .= "'".$arr['so_pay_type']."',";
					$sql .= "'".$arr['so_order_status']."',";
					$sql .= "'".$arr['so_comment_status']."',";
					$sql .= "'".$arr['so_order_no']."',";
					$sql .= "'".$insert_id."',";
					$sql .= "'".$arr['identity_id']."',";
					$sql .= "'".$arr['so_licence']."',";
					$sql .= "'".$arr['real_name']."',";
					$sql .= "'".$arr['user_phone']."',";
					$sql .= "'".time()."')";
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

		// 检测手机号
		public function getPhoneCheck($phone) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}user` WHERE `s_phone` = ".$phone." AND `i_user_type` = 0";
			$res = $this->_getFirstRecord($sql);
			if($res) {
				return true;
			} else {
				return false;
			}
		}

		// 检测身份证
		public function getIdentityCheck($identity_id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}users_info` as i LEFT JOIN `{$this->_dbtabpre}user` as u ON u.`l_user_id` = i.`user_id` WHERE i.`identity_id` = ".$identity_id;
			$res = $this->_getFirstRecord($sql);
			if($res) {
				return true;
			} else {
				return false;
			}
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
		
		// 修改学员
		public function updateUserInfo($arr) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}user` WHERE `l_user_id` = ".$arr['l_user_id'];
			$query = $this->_getFirstRecord($sql);
			if($query) {
				$sql = "UPDATE `{$this->_dbtabpre}user` as u, `{$this->_dbtabpre}users_info` as i SET ";
				$sql .= "u.`s_username` = '".$arr['s_username']."', ";
				$sql .= "u.`s_real_name` = '".$arr['s_real_name']."', ";
				$sql .= "u.`s_phone` = '".$arr['s_phone']."', ";
				$sql .= "u.`i_from` = '".$arr['i_from']."', ";
				$sql .= "i.`sex` = '".$arr['sex']."', ";
				$sql .= "i.`age` = '".$arr['age']."', ";
				$sql .= "i.`identity_id` = '".$arr['identity_id']."', ";
				$sql .= "i.`address` = '".$arr['s_address']."', ";
				$sql .= "i.`user_photo` = '".$arr['user_photo']."', ";
				$sql .= "i.`province_id` = '".$arr['province_id']."', ";
				$sql .= "i.`city_id` = '".$arr['city_id']."', ";
				$sql .= "i.`area_id` = '".$arr['area_id']."', ";
				$sql .= "i.`learncar_status` = '".$arr['learncar_status']."', ";
				$sql .= "i.`photo_id` = '1', ";
				$sql .= "i.`school_id` = '".$arr['school_id']."' ";
				$sql .= "WHERE u.`l_user_id` = i.`user_id` AND u.`l_user_id` = ".$arr['l_user_id'];
				$res = $this->_db->query($sql);
				return $res;
			} else {
				return false;
			}
		} 

		// 学员彻底删除
		public function deleteUserInfo($id) {
			$ids = explode(',', $id);
			$sql = "SELECT * FROM `{$this->_dbtabpre}user` WHERE `l_user_id` IN (".implode(',', $ids).")";
			$stmt = $this->_getAllRecords($sql);
			if($stmt) {
				$sql = "DELETE FROM `{$this->_dbtabpre}user` WHERE `l_user_id` IN (".implode(',', $ids).")";
				$res = $this->_db->query($sql);
				if($res) {
					$sql = "DELETE FROM `{$this->_dbtabpre}users_info` WHERE `user_id` IN (".implode(',', $ids).")";
					$rs = $this->_db->query($sql);
					return $rs;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		// 还原回收站的学员
		public function restoreUserInfo($id) {
			$ids = explode(',', $id);
			$sql = "SELECT * FROM `{$this->_dbtabpre}user` WHERE `l_user_id` IN (".implode(',', $ids).")";
			$stmt = $this->_getAllRecords($sql);
			if($stmt) {
				$sql = "UPDATE `{$this->_dbtabpre}user` SET `i_status` = 0 WHERE `l_user_id` IN (".implode(',', $ids).")";
				$res = $this->_db->query($sql);
				return $res;
			} else {
				return false;
			}
		}

		// 检测重复订单
		public function getOrdernoCheck($no) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_order_no` = '".$no."'";
			$stmt = $this->_getFirstRecord($sql);
			if($stmt) {
				return true;
			} else {
				return false;
			}
		}
		/**
		 * 搜索学员列表
		 * @param $page int 页码
		 * @param $limit int 限制每页数量
		 * @param $school_id int 驾校ID
		 * @param $conditiontype int 搜索条件限制（1：学员ID 2：学员姓名 3：学员号码 4：身份证号）
		 * @param $onlinetype int 线上线下（1：线上 (0:苹果 1:安卓) 2：线下）
		 * @param $keyword string 关键词
		 * @return void
		 * @author 
		 **/
		public function getSearchMemberList($page='', $limit='', $school_id, $conditiontype, $onlinetype, $keyword) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`i_user_type` = 0 AND i.`school_id` = $school_id AND u.`i_status` != 2 ";	
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

		/**
		 * 获取学员相关学车信息（报名驾校， 预约学车，在线模拟等）
		 *
		 * @return array
		 * @author chenxi, gaodacheng
		 **/
		public function showMemberInfo($school_id, $id, $type, $conditiontype='', $page='', $limit='') {
			$userinfo = $this->getMemberInfo($id);

			// 获取预约学车情况
			$list = array();
			$i_service_time = 0;
			if($type == 'appoint') {
				// 获取当前驾校的教练ID
				$sql = "SELECT `l_coach_id` FROM `{$this->_dbtabpre}coach` WHERE `s_school_name_id` = '{$school_id}'";
				$coach_info = $this->_getAllRecords($sql);
				$coach_ids = array();
				foreach ($coach_info as $key => $value) {
					$coach_ids[] = $value['l_coach_id'];
				}
				// 获取当前学员预约学车情况
				$sql = " SELECT so.*, at.* FROM `{$this->_dbtabpre}study_orders` AS so LEFT JOIN `{$this->_dbtabpre}coach_appoint_time` AS at ON so.`appoint_time_id` = at.`id` AND so.`l_coach_id` = at.`coach_id` WHERE so.`l_user_id` = '{$id}' AND so.`l_coach_id` IN (".implode(',', $coach_ids).") AND so.`i_status` = 2 ORDER BY so.`dt_order_time` DESC ";
                // i_status = 2-已完成学车流程
				if($conditiontype !== '') {
					$sql .= " AND so.`s_lesson_name` = '{$conditiontype}'";
				}
				if($page !== '' && $limit !== '') {
					$sql .= " LIMIT $page, $limit";
				}
				$study_orders = $this->_getAllRecords($sql);

                // 获取教练时间配置
                $sql = " SELECT * FROM `{$this->_dbtabpre}coach_time_config` WHERE 1 ";
                $_coach_time_config_list = $this->_getAllRecords($sql);
                $coach_time_config_list = array();
                if ($_coach_time_config_list) {
                    foreach ($_coach_time_config_list as $index => $coach_time_config) {

                        $coach_time_config_list[$coach_time_config['id']] = $coach_time_config;
                    }
                }

                // 求总学时
                // i_status = 2-已完成学车流程
                // i_service_time记录的是每条订单里的学时，有一小时，有两小时，单位：小时
				$sql = " SELECT SUM(`i_service_time`) AS total_learn_time FROM `{$this->_dbtabpre}study_orders` WHERE `l_user_id` = '{$id}' AND `l_coach_id` IN (".implode(',', $coach_ids).") AND `i_status` = 2 ORDER BY `dt_order_time` DESC ";
				$student_learn_time = $this->_getFirstRecord($sql);
                if (isset($student_learn_time['total_learn_time'])) {
                    $i_service_time = intval($student_learn_time['total_learn_time']);
                }

				if($study_orders) {
					foreach ($study_orders as $key => $value) {
						$list['orderlist'][$key]['l_study_order_id'] = $value['l_study_order_id'];
						$list['orderlist'][$key]['s_order_no'] = $value['s_order_no'];
						$list['orderlist'][$key]['s_lisence_name'] = $value['s_lisence_name'];
						$list['orderlist'][$key]['s_coach_name'] = $value['s_coach_name'];
						$list['orderlist'][$key]['s_coach_phone'] = $value['s_coach_phone'];
						$list['orderlist'][$key]['s_lesson_name'] = $value['s_lesson_name'];
						$list['orderlist'][$key]['dc_money'] = $value['dc_money'];
						$list['orderlist'][$key]['i_service_time'] = $value['i_service_time'];
						$list['orderlist'][$key]['deal_type'] = $value['deal_type'];
						$list['orderlist'][$key]['l_user_id'] = $value['l_user_id'];
						$list['orderlist'][$key]['l_coach_id'] = $value['l_coach_id'];
						$list['orderlist'][$key]['dt_order_time'] = $value['dt_order_time'] ? date('Y-m-d H:i', $value['dt_order_time']) : '';
						//$list['orderlist'][$key]['dt_appoint_time'] = explode(' ', $value['dt_appoint_time'])[0];
                        $so_time_config_id_list = explode(',', $value['time_config_id']);
                        $appoint_time = array();
                        if ($so_time_config_id_list && count($so_time_config_id_list) > 0) {
                            foreach ($so_time_config_id_list as $so_time_config_index => $so_time_config_id) {
                                if (array_key_exists($so_time_config_id, $coach_time_config_list)) {
                                    $year = $value['year'];
                                    $month = $value['month'];
                                    $day = $value['day'];
                                    $start_hour = $coach_time_config_list[$so_time_config_id]['start_time'];
                                    $end_hour = $coach_time_config_list[$so_time_config_id]['end_time'];
                                    $start_minute = $coach_time_config_list[$so_time_config_id]['start_minute'];
                                    if (intval($start_minute) < 10) {
                                        $start_minute = '0'.$start_minute;
                                    }
                                    $end_minute = $coach_time_config_list[$so_time_config_id]['end_minute'];
                                    if (intval($end_minute) < 10) {
                                        $end_minute = '0'.$end_minute;
                                    }
                                    $final_appoint_time_element = $year.'-'.$month.'-'.$day.' '.$start_hour.':'.$start_minute.'-'.$end_hour.':'.$end_minute;
                                    $appoint_time[] = $final_appoint_time_element;
                                }
                            }
                        }
						$list['orderlist'][$key]['dt_appoint_time'] = implode('<br />', $appoint_time);
						//$i_service_time += $value['i_service_time']; // 此方法计算的只是一个分页的学时数，不是总学时数，所以不对的。
					}
				} else {
					$list['orderlist'] = array();
				}
					
				$list['service_time'] = $i_service_time;
			} elseif ( $type == 'signup' ) {
				// 获取报名驾校情况
				$sql = "SELECT * FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = '{$school_id}' AND `so_user_id` = '{$id}' AND `so_order_status` != 101";
				$school_orders = $this->_getAllRecords($sql);
				if($school_orders) {
					foreach ($school_orders as $key => $value) {
						$list['orderlist']['so_final_price'] = $value['so_final_price'];
						$list['orderlist']['so_original_price'] = $value['so_original_price'];
						$list['orderlist']['so_shifts_id'] = $value['so_shifts_id'];
						$list['orderlist']['so_pay_type'] = $value['so_pay_type'];
						$list['orderlist']['so_order_status'] = $value['so_order_status'];
						$list['orderlist']['so_comment_status'] = $value['so_comment_status'];
						$list['orderlist']['so_order_no'] = $value['so_order_no'];
						$list['orderlist']['so_user_id'] = $value['so_user_id'];
						$list['orderlist']['so_user_identity_id'] = $value['so_user_identity_id'];
						$list['orderlist']['so_licence'] = $value['so_licence'];
						$list['orderlist']['so_username'] = $value['so_username'];
						$list['orderlist']['so_phone'] = $value['so_phone'];

						// 通过班制ID获取班制名称等信息
						$sql = "SELECT `sh_title` FROM `{$this->_dbtabpre}school_shifts` WHERE `id` = '{$value['so_shifts_id']}'";
						$sh_title = $this->_getFirstRecord($sql);
						$list['orderlist']['sh_title'] = $sh_title['sh_title'];
					}
				} else {
					$list['orderlist'] = array();
				}
			} elseif ( $type == 'exam' ) {
                //获取在线模拟考试记录
                //按用户id查考试记录
                $user_id = $userinfo['l_user_id'];
                //按时间顺序获取 10条
                $sql = "SELECT * FROM `{$this->_dbtabpre}user_exam_records` WHERE `user_id` = '{$user_id}' ORDER BY `addtime` DESC LIMIT 10";
                //$sql = "SELECT * FROM `{$this->_dbtabpre}user_exam_records` ORDER BY `addtime` DESC LIMIT 10";
                $exam_records = $this->_getAllRecords($sql);
                if ( $exam_records ) {
                    $_exam_records = array();
                    foreach ( $exam_records as $key => $value) {
                        //考试的记录id
                        $_exam_records[$key]['record_id'] = $value['id'];
                        //考试分数
                        $_exam_records[$key]['score'] = $value['score'];
                        //考试科目
                        if ( $value['stype'] == 1 ) {
                            $_exam_records[$key]['subject'] = '科目一';
                        } elseif ( $value['stype'] == 4 ) {
                            $_exam_records[$key]['subject'] = '科目四';
                        }
                        //考试类型
                        $_exam_records[$key]['license'] = $value['ctype'];
                        //考试用时
                        if ( $value['exam_total_time'] >= 60 ) {
                            $min = floor( $value['exam_total_time'] / 60 );
                            $sec = $value['exam_total_time'] - 60 * $min;
                            $min_sec = $min . '分' . $sec . '秒';
                            $_exam_records[$key]['exam_use_time'] = $min_sec;
                        } else {
                            $min_sec = $value['exam_total_time'] . '秒';
                            $_exam_records[$key]['exam_use_time'] = $min_sec;
                        }
                        //考试结束日期
                        $_exam_records[$key]['adddate'] = date('Y-m-d H:i:s', $value['addtime']);
                    }
                    $list['orderlist'] = $_exam_records;
                } else {
                    $list['orderlist'] = array();
                }
            }

			return $list;
		}

		/**
		 * 根据驾校id获取驾校学员id
		 * @param $school_id string 驾校id
		 * @return void
		 * @author 
		 **/
		public function getUsersBySchoolid($school_id) {
			$sql = "SELECT `so_user_id` FROM `{$this->_dbtabpre}school_orders` WHERE `so_school_id` = '{$school_id}' ";
			$userid_list = $this->_getAllRecords($sql);
			$user_ids = array();
			if ($userid_list) {
				foreach ($userid_list as $key => $value) {
					$user_ids[] = $value['so_user_id'];
				}	
			}
			return $user_ids;
		}


		/**
		 * 根据学员id数组获取学员模拟考试记录
		 * @param $user_ids array() 学员id数组
		 * @return void
		 * @author 
		 **/
		public function getExamRecords($school_id, $page='', $limit='') {
			$sql = "SELECT * FROM `{$this->_dbtabpre}user_exam_records` WHERE `school_id` = '{$school_id}' ORDER BY `addtime` DESC";
			if($page !== '' && $limit !== '') {
				$sql .= " LIMIT $page, $limit";
			} else {
				$sql .= "";
			}
			$exam_records = array();
			$_exam_records = array();
			$exam_records = $this->_getAllRecords($sql);
			if ($exam_records) {
				foreach ($exam_records as $key => $value) {
					$sql = "SELECT `s_real_name`, `s_username`, `s_phone` FROM `{$this->_dbtabpre}user` WHERE `l_user_id` = '{$value['user_id']}'";
					$res = $this->_getFirstRecord($sql);
					if($value['realname']) {
						$_exam_records[$key]['real_name'] = $value['realname'];
					} else {
						$_exam_records[$key]['real_name'] = $res['s_real_name'] == '' ? $res['s_username'] : $res['s_real_name'];
					}
						
					$_exam_records[$key]['phone'] = $res['s_phone'] == '' ? $value['phone_num'] : $res['s_phone'];
					$_exam_records[$key]['identify_id'] = $value['identify_id'];
					 //考试的记录id
                    $_exam_records[$key]['record_id'] = $value['id'];
                    //考试分数
                    $_exam_records[$key]['score'] = $value['score'];
                    //考试科目
                    if ( $value['stype'] == 1 ) {
                        $_exam_records[$key]['subject'] = '科目一';
                    } elseif ( $value['stype'] == 4 ) {
                        $_exam_records[$key]['subject'] = '科目四';
                    }
                    //考试类型
                    $_exam_records[$key]['license'] = $value['ctype'];
                    //考试用时
                    if ( $value['exam_total_time'] >= 60 ) {
                        $min = floor( $value['exam_total_time'] / 60 );
                        $sec = $value['exam_total_time'] - 60 * $min;
                        $min_sec = $min . '分' . $sec . '秒';
                        $_exam_records[$key]['exam_use_time'] = $min_sec;
                    } else {
                        $min_sec = $value['exam_total_time'] . '秒';
                        $_exam_records[$key]['exam_use_time'] = $min_sec;
                    }
                    //考试结束日期
                    $_exam_records[$key]['adddate'] = date('Y-m-d H:i:s', $value['addtime']);

				}			
			}
			return $_exam_records;
		}

		/**
		 * 根据学员信息获取学员模拟考试记录
		 * @param $user_ids array() 学员id数组
		 * @return void
		 * @author 
		 **/
		public function getSearchExamRecords($school_id, $page='', $limit='', $conditiontype, $keyword) {
			if(!$school_id) {
				return array();
			}
			$sql = "SELECT r.* FROM `{$this->_dbtabpre}user_exam_records` as r LEFT JOIN `{$this->_dbtabpre}user` as u ON u.`l_user_id` = r.`user_id` WHERE r.`school_id` = '{$school_id}'";
			if($conditiontype == 1) {
				$sql .= " AND (r.`realname` LIKE '%{$keyword}%' OR u.`s_real_name` LIKE '%{$keyword}%' OR  u.`s_username` LIKE '%{$keyword}%') ";

			} else if($conditiontype == 2) {
				$sql .= " AND (r.`phone_num` LIKE '%{$keyword}%' OR u.`s_phone` LIKE '%{$keyword}%') ";

			}
			$sql .= " ORDER BY `addtime` DESC";
			if($page !== '' && $limit !== '') {
				$sql .= " LIMIT $page, $limit";
			} else {
				$sql .= "";
			}
			// echo $sql;
			$exam_records = array();
			$_exam_records = array();
			$exam_records = $this->_getAllRecords($sql);
			if ($exam_records) {
				foreach ($exam_records as $key => $value) {
					$sql = "SELECT `s_real_name`, `s_username`, `s_phone` FROM `{$this->_dbtabpre}user` WHERE `l_user_id` = '{$value['user_id']}'";
					$res = $this->_getFirstRecord($sql);
					if($value['realname']) {
						$_exam_records[$key]['real_name'] = $value['realname'];
					} else {
						$_exam_records[$key]['real_name'] = $res['s_real_name'] == '' ? $res['s_username'] : $res['s_real_name'];
					}
						
					$_exam_records[$key]['phone'] = $res['s_phone'] == '' ? $value['phone_num'] : $res['s_phone'];
					$_exam_records[$key]['identify_id'] = $value['identify_id'];
					 //考试的记录id
                    $_exam_records[$key]['record_id'] = $value['id'];
                    //考试分数
                    $_exam_records[$key]['score'] = $value['score'];
                    //考试科目
                    if ( $value['stype'] == 1 ) {
                        $_exam_records[$key]['subject'] = '科目一';
                    } elseif ( $value['stype'] == 4 ) {
                        $_exam_records[$key]['subject'] = '科目四';
                    }
                    //考试类型
                    $_exam_records[$key]['license'] = $value['ctype'];
                    //考试用时
                    if ( $value['exam_total_time'] >= 60 ) {
                        $min = floor( $value['exam_total_time'] / 60 );
                        $sec = $value['exam_total_time'] - 60 * $min;
                        $min_sec = $min . '分' . $sec . '秒';
                        $_exam_records[$key]['exam_use_time'] = $min_sec;
                    } else {
                        $min_sec = $value['exam_total_time'] . '秒';
                        $_exam_records[$key]['exam_use_time'] = $min_sec;
                    }
                    //考试结束日期
                    $_exam_records[$key]['adddate'] = date('Y-m-d H:i:s', $value['addtime']);

				}			
			}
			return $_exam_records;
		}

	}

?>
