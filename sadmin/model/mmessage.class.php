<?php  
	// 评价模块

	!defined('IN_FILE') && exit('Access Denied');

	 class mmessage extends mbase {
	 	
	 	/**
	 	 * 获取消息列表
	 	 * @param $school_id int 学校ID
	 	 * @param $i_type int 消息类型
	 	 * @param $is_read int 是否未读
	 	 * @param $member_type int 用户类型
	 	 * @return array
	 	 * @author chenxi
	 	 **/
	 	public function getMessageList($page='', $limit='', $school_id, $i_type, $i_read, $member_type) {
	 		
			$sql = "SELECT * FROM `{$this->_dbtabpre}sms_sender` WHERE `member_type` = '{$member_type}'";
			$sql .= " AND `i_yw_type` = '{$i_type}' ";
			if($i_read != '') {
				$sql .= " AND `is_read` = '{$i_read}'";
			}

			$member_ids = array();
 			switch ($member_type) {

 				case '1': // 学员
 					$msql = "SELECT `user_id` FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE i.`school_id` = '{$school_id}'";
 					$users_info = $this->_getAllRecords($msql);
 					if($users_info) {
 						foreach ($users_info as $key => $value) {
 							$member_ids[] = $value['user_id'];
 						}
 					} else {
 						$member_ids[] = 0;
 					}
 					break;

 				case '2': // 教练
	 				$csql = "SELECT `l_coach_id` FROM `{$this->_dbtabpre}coach` WHERE `s_school_name_id` = '{$school_id}'";
	 				$coach_ids = $this->_getAllRecords($csql);

	 				if($coach_ids) {
	 					foreach ($coach_ids as $key => $value) {
	 						$member_ids[] = $value['l_coach_id'];
	 					}
	 				} else {
	 					$member_ids[] = 0;
	 				}
	 
 					break;

 				default:
					$member_ids[] = 0;
 					break;
 			}

			$sql .= " AND `member_id` IN (".implode(',', $member_ids).")";
			$sql .= " ORDER BY `addtime` DESC";

 			if($page !== '' && $limit !== '') {
				$sql .= " LIMIT $page, $limit";
			} else {
				$sql .= "";
			}
			// echo $sql;
	 		$res = $this->_getAllRecords($sql);
	 		if($res) {
	 			foreach ($res as $key => $value) {
	 				// 获取消息发送者
	 				// 学员
	 				if($member_type == 1) {
	 					$sql = "SELECT u.`s_username`, u.`s_real_name`, u.`l_user_id`, u.`s_phone` FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`l_user_id` = '{$value['member_id']}'";
	 					$user_info = $this->_getFirstRecord($sql);
	 					if($user_info) {
	 						$res[$key]['name'] = $user_info['s_real_name'] == '' ? $user_info['s_username'] : $user_info['s_real_name'];
	 						$res[$key]['phone'] = $user_info['s_phone'];
	 					} else {
							$res[$key]['name'] = '';
	 						$res[$key]['phone'] = '';
	 					}
 					// 教练
	 				} else if($member_type == 2) {
 						$sql = "SELECT `l_coach_id`, `s_coach_name`, `s_coach_phone` FROM `{$this->_dbtabpre}coach` WHERE `l_coach_id` = '{$value['member_id']}'";
 						$coach_info = $this->_getFirstRecord($sql);
 						if($coach_info) {
 							$res[$key]['name'] = $coach_info['s_coach_name'];
 							$res[$key]['phone'] = $coach_info['s_coach_phone'];
 						} else {
							$res[$key]['name'] = '';
	 						$res[$key]['phone'] = '';
 						}
	 				}

					$res[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);	 				
					if($value['is_read'] == 1) {
						$res[$key]['read'] = '已读';	 				

					} else if($value['is_read'] == 2) {
						$res[$key]['read'] = '未读';	 				

					} else if($value['is_read'] == 101) {
						$res[$key]['read'] = '已删除';	 				

					}

					if($value['i_yw_type'] == 1) {
						$res[$key]['i_yw'] = '通知消息';	 				

					} else if($value['i_yw_type'] == 0) {
						$res[$key]['i_yw'] = '正常消息';	 				

					} else {
						$res[$key]['i_yw'] = '正常消息';	 				
						
					}
	 			}
	 		}
	 		return $res;
	 	}

	 	// 获取人员列表
	 	public function getMemberList($type, $school_id) {
	 		// 学员

	 		$list = array();
	 		if($type == 1) {

	 			$sql = "SELECT u.`s_phone`, u.`l_user_id`, u.`s_username`, u.`s_real_name` FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE i.`school_id` = '{$school_id}'";
	 			$list = $this->_getAllRecords($sql);

	 		} else if($type == 2) {

	 			$sql = "SELECT `l_coach_id`, `s_coach_name`, `s_coach_phone` FROM `{$this->_dbtabpre}coach` WHERE `s_school_name_id` = '{$school_id}'";
	 			$list = $this->_getAllRecords($sql);
	 		}
	 		return $list;
	 	}

	 	// 发送消息
	 	public function sendmessage($arr) {

	 		if($arr['member_type'] == 2) {
				// 教练端
				$app_key = COACH_APPKEYS;
				$master_secret = COACH_SECRET;

			} else {
				// 学员端
				$app_key = STUDENTS_APPKEYS;
				$master_secret = STUDENTS_SECRET;		

	 		}
	 		// 根据驾校id获取驾校名称
	 		$sql = "SELECT `s_school_name` FROM `{$this->_dbtabpre}school` WHERE `l_school_id` = '{$arr['school_id']}'";
	 		$stmt = $this->_getFirstRecord($sql);

	 		$Jpush = new Jpush($app_key, $master_secret);
 			$result = $Jpush->send_pub($arr['receive'], $arr['content'], $arr['title'], $arr['content'], $arr['m_time']);
 			$result = json_decode($result, true);
 			// return $result;
 			if($result) {
 				// $arr = array();
 				$arr['sendno'] = isset($result['sendno']) ? $result['sendno'] : '';
 				$arr['msg_id'] = isset($result['msg_id']) ? $result['msg_id'] : '';
 				$arr['s_from'] = '嘻哈学车';
 				$arr['time'] = time();

	 			$sql = "INSERT INTO `cs_sms_sender` (`dt_sender`, `i_jpush_sendno`, `i_jpush_msg_id`, `s_content`, `s_from`, `s_beizhu`, `member_id`, `member_type`, `i_yw_type`, `addtime`) VALUES (";
	 			$sql .= "'".$arr['time']."', '".$arr['sendno']."', '".$arr['msg_id']."', '".$arr['content']."', '".$arr['s_from']."', '".$arr['s_beizhu']."', '".$arr['member_id']."', '".$arr['member_type']."', '".$arr['i_yw_type']."', '".$arr['time']."')";
 				$stmt = $this->_db->query($sql);
 				return $stmt;
 			} else {
 				return false;
 			}
	 	}

 	}

?>