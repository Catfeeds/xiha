<?php  
	// 评价模块

	!defined('IN_FILE') && exit('Access Denied');

	 class mcomment extends mbase {
	 	

		/**
		 * 获取评价驾校列表
		 * @param $page int 页码
		 * @param $limit int 限制每页显示数量
		 * @return array 教练评价列表
		 */

		public function getSchoolCommentList($page='', $limit='', $school_id) {

			if (!$school_id) {
				return array();
			}

			if ($page !== '' && $limit !== '') {
				$sql = "SELECT * FROM `{$this->_dbtabpre}coach_comment` WHERE `school_id` = $school_id AND `type` = 2 ORDER BY `addtime` DESC LIMIT $page, $limit";
			}else{
				$sql = "SELECT * FROM `{$this->_dbtabpre}coach_comment` WHERE `school_id` = $school_id AND `type` = 2 ORDER BY `addtime` DESC ";
			}
				$query = $this->_db->query($sql);
			$list = array();
			$i = 0;
			while($school_comment = $this->_db->fetch_array($query)) {
				$list[$i]['id'] = $school_comment['id'];
				$list[$i]['school_star'] = intval($school_comment['school_star']);
				$list[$i]['school_content'] = $school_comment['school_content'];
				$list[$i]['user_id'] = $school_comment['user_id'] ;
				$list[$i]['order_no'] = $school_comment['order_no'];
				$list[$i]['addtime'] = date('Y-m-d H:i', $school_comment['addtime']);
				//搜索学员信息
				$sql = "SELECT * FROM `{$this->_dbtabpre}user` WHERE `l_user_id` = '".$school_comment['user_id']."'";		
				$user_info = $this->_getFirstRecord($sql);
				if($user_info) {
					$list[$i]['s_username'] = $user_info['s_username'];
					$list[$i]['s_real_name'] = $user_info['s_real_name'];
					$list[$i]['s_phone'] = $user_info['s_phone'];
				} else {
					$list[$i]['s_username'] = '';
					$list[$i]['s_real_name'] = '';
					$list[$i]['s_phone'] = '';
					
				}
				$i++;	
			}

			return $list;

		}


		/**
		 * 获取评价教练列表
		 * @param $page int 页码
		 * @param $limit int 限制每页显示数量
		 * @return array 驾校评价列表
		 */
		public function getCoachCommentList($page='', $limit='', $school_id) {

			if (!$school_id) {
				return array();
			}
			$sql = "SELECT `l_coach_id` FROM `{$this->_dbtabpre}coach` WHERE `s_school_name_id` = '{$school_id}'";
			$coach_id_info = $this->_getAllRecords($sql);
			$coach_ids = array(0); 
			if($coach_id_info) {
				foreach ($coach_id_info as $key => $value) {
					$coach_ids[] = $value['l_coach_id'];
				}
			}
			if ($page !== '' && $limit !== '') {
				$sql = "SELECT * FROM `{$this->_dbtabpre}coach_comment` WHERE `coach_id` IN (".implode(',', $coach_ids).") AND `type` = 1 ORDER BY `addtime` DESC LIMIT $page, $limit";
			}else{
				$sql = "SELECT * FROM `{$this->_dbtabpre}coach_comment` WHERE `coach_id` IN (".implode(',', $coach_ids).") AND `type` = 1 ORDER BY `addtime` DESC ";
			}
				$query = $this->_db->query($sql);
			$list = array();
			$i = 0;
			while($school_comment = $this->_db->fetch_array($query)) {
				$list[$i]['id'] = $school_comment['id'];
				$list[$i]['coach_star'] = intval($school_comment['coach_star']);
				$list[$i]['coach_content'] = $school_comment['coach_content'];
				$list[$i]['user_id'] = $school_comment['user_id'] ;
				$list[$i]['order_no'] = $school_comment['order_no'];
				$list[$i]['addtime'] = date('Y-m-d H:i', $school_comment['addtime']);
				//搜索学员信息
				$sql = "SELECT * FROM `{$this->_dbtabpre}user` WHERE `l_user_id` = '".$school_comment['user_id']."'";		
				$user_info = $this->_getFirstRecord($sql);
				if($user_info) {
					$list[$i]['s_username'] = $user_info['s_username'];	
				} else {
					$list[$i]['s_username'] = '';		
					
				}
				// 查询教练信息
					$sql = "SELECT * FROM `{$this->_dbtabpre}coach` WHERE `l_coach_id` = '".$school_comment['coach_id']."'";
					$coach_info = $this->_getFirstRecord($sql);
					if($coach_info) {	
						$list[$i]['s_coach_name'] = $coach_info['s_coach_name'];
						$list[$i]['s_coach_imgurl'] = HTTP_HOST.$coach_info['s_coach_imgurl'];
						$list[$i]['s_coach_phone'] = $coach_info['s_coach_phone'];
					} else {
						$list[$i]['s_coach_name'] = '';
						$list[$i]['s_coach_imgurl'] = '';
						$list[$i]['s_coach_phone'] = '';	
					}
				$i++;	
			}

			return $list;

		}
		

		/**
		 * 获取评价学员列表
		 * @param $page int 页码
		 * @param $limit int 限制每页显示数量
		 * @return array 学员评价列表
		 */

		public function getStudentCommentList($page='', $limit='', $school_id) {
			
			if(!$school_id) {
				return array();
			}
			// 获取所有教练ID
			$sql = "SELECT `l_coach_id` FROM `{$this->_dbtabpre}coach` WHERE `s_school_name_id` = $school_id";
			$coach_ids = $this->_getAllRecords($sql);
			$coach_id_arr = array();
			if($coach_ids) {
				foreach ($coach_ids as $key => $value) {
					$coach_id_arr[] = $value['l_coach_id'];
				}
			} else {
				$coach_id_arr[] = 0;
			}
			$sql = "SELECT * FROM `{$this->_dbtabpre}student_comment` WHERE `coach_id` IN (".implode(',', $coach_id_arr).") ORDER BY `id` DESC"; 	
			$student_comment_list = $this->_getAllRecords($sql);
			$list = array();
			if($student_comment_list) {
				foreach ($student_comment_list as $key => $value) {
					$list[$key]['id'] = $value['id'];
					$list[$key]['order_no'] = $value['order_no'];
					$list[$key]['star_num'] = intval($value['star_num']);
					$list[$key]['content'] = $value['content'];
					$list[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);
					// 搜索学员信息
					$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`l_user_id` = '".$value['user_id']."'";
					$user_info = $this->_getFirstRecord($sql);
					if($user_info) {
						$list[$key]['s_username'] = $user_info['s_username'];
						$list[$key]['s_real_name'] = $user_info['s_real_name'];
						$list[$key]['s_phone'] = $user_info['s_phone'];
						$list[$key]['sex'] = $user_info['sex'];
						$list[$key]['age'] = $user_info['age'];
						$list[$key]['identity_id'] = $user_info['identity_id'];
						$list[$key]['address'] = $user_info['address'];
						$list[$key]['photo_id'] = $user_info['photo_id'];
						$list[$key]['province_id'] = $user_info['province_id'];
						$list[$key]['city_id'] = $user_info['city_id'];
						$list[$key]['area_id'] = $user_info['area_id'];
						$list[$key]['user_photo'] = HTTP_HOST.$user_info['user_photo'];
						$list[$key]['learncar_status'] = $user_info['learncar_status'];
					} else {
						$list[$key]['s_username'] = '';
						$list[$key]['s_real_name'] = '';
						$list[$key]['s_phone'] = '';
						$list[$key]['sex'] = '';
						$list[$key]['age'] = '';
						$list[$key]['identity_id'] = '';
						$list[$key]['address'] = '';
						$list[$key]['photo_id'] = '';
						$list[$key]['province_id'] = '';
						$list[$key]['city_id'] = '';
						$list[$key]['area_id'] = '';
						$list[$key]['user_photo'] = '';
						$list[$key]['learncar_status'] = '';
					}

					// 搜索教练
					$sql = "SELECT `l_coach_id`, `s_coach_name`, `s_coach_imgurl`, `s_coach_phone` FROM `{$this->_dbtabpre}coach` WHERE `l_coach_id` = '".$value['coach_id']."'";
					$coach_info = $this->_getFirstRecord($sql);
					if($coach_info) {
						$list[$key]['l_coach_id'] = $coach_info['l_coach_id'];
						$list[$key]['s_coach_name'] = $coach_info['s_coach_name'];
						$list[$key]['s_coach_imgurl'] = HTTP_HOST.$coach_info['s_coach_imgurl'];
						$list[$key]['s_coach_phone'] = $coach_info['s_coach_phone'];
					} else {
						$list[$key]['l_coach_id'] = '';
						$list[$key]['s_coach_name'] = '';
						$list[$key]['s_coach_imgurl'] = '';
						$list[$key]['s_coach_phone'] = '';
					}
				}
			}
			
		   return $list;		
		}

		// 删除多个驾校、教练评价
		public function deleteMoreCComment($id) {

			$ids = array_filter(explode(',', $id));
			$sql = "SELECT * FROM `{$this->_dbtabpre}coach_comment` WHERE `id` IN (".implode(',', $ids).")";
			$stmt = $this->_getAllRecords($sql);
			if($stmt) {
				$sql = "DELETE FROM `{$this->_dbtabpre}coach_comment` WHERE `id` IN (".implode(',', $ids).")";
				$res = $this->_db->query($sql);
				return $res;
			} else {
				return false;
			}
		}
		// 删除多个学员评价
		public function deleteMoreSComment($id) {

			$ids = array_filter(explode(',', $id));
			$sql = "SELECT * FROM `{$this->_dbtabpre}student_comment` WHERE `id` IN (".implode(',', $ids).")";
			$stmt = $this->_getAllRecords($sql);
			if($stmt) {
				$sql = "DELETE FROM `{$this->_dbtabpre}student_comment` WHERE `id` IN (".implode(',', $ids).")";
				$res = $this->_db->query($sql);
				return $res;
			} else {
				return false;
			}
		}

			// 删除教练评价信息
		public function deleteCoachComment($id) {
			$sql = "DELETE FROM `{$this->_dbtabpre}coach_comment` WHERE `id` = $id";
			$res = $this->_db->query($sql);
			if($res) {
				return $res;
			} else {
				return false;
			}
		}
		
			// 删除驾校评价信息
		public function deleteSchoolComment($id) {
			$sql = "DELETE FROM `{$this->_dbtabpre}coach_comment` WHERE `id` = $id";
			$res = $this->_db->query($sql);
			if($res) {
				return $res;
			} else {
				return false;
			}
		}
			// 删除学员评价信息
		public function deleteStudentComment($id) {
			$sql = "DELETE FROM `{$this->_dbtabpre}student_comment` WHERE `id` = $id";
			$res = $this->_db->query($sql);
			if($res) {
				return $res;
			} else {
				return false;
			}
		}


		/**
		 * 搜索评价教练列表
		 * @param $page int 页码
		 * @param $limit int 限制每页数量
		 * @param $school_id int 驾校ID
		 * @param $conditiontype int 搜索条件限制（1：教练ID 2：教练姓名 3：教练号码 ）
		 *
		 * @param $keyword string 关键词
		 * @return void
		 * @author 
		 **/
		public function getSearchCCommentList($page='', $limit='', $school_id, $conditiontype, $keyword) {
			if(!$school_id){
				return array();
			}
			$list = array();
			$sql = "SELECT * FROM `{$this->_dbtabpre}coach` as h LEFT JOIN `{$this->_dbtabpre}coach_comment` as c ON c.`coach_id` = h.`l_coach_id` WHERE c.`school_id` = '".$school_id."'  AND c.`type` = 1";
			switch ($conditiontype) {
				case '1':
					$sql .= " AND h.`s_coach_name`  LIKE '%".$keyword."%'";
					break;
				case '2':
					$sql .= " AND h.`s_coach_phone` LIKE '%".$keyword."%'";
					break;
				default:
					$sql .= " AND h.`s_coach_name`  LIKE '%".$keyword."%'";
					break;
			}
			$sql .= " ORDER BY c.`addtime` DESC";
			if($page !=='' && $limit !== '') {
				$sql .= " LIMIT $page, $limit";
			}else {
				$sql .= "";
			}
			
			$res = $this->_getAllRecords($sql);
			if($res) {
				foreach ($res as $key => $value) {
					$list[$key]['id'] 				= $value['id'];
					$list[$key]['coach_star'] 		= intval($value['coach_star']);
					$list[$key]['coach_content'] 	= $value['coach_content'];
					$list[$key]['order_no'] 		= $value['order_no'];
					$list[$key]['addtime'] 			= date('Y-m-d H:i', $value['addtime']);
					$list[$key]['s_coach_name'] 	= $value['s_coach_name'];
					$list[$key]['s_coach_phone'] 	= $value['s_coach_phone'];
					$list[$key]['s_coach_imgurl'] 	= HTTP_HOST.$value['s_coach_imgurl'];
					$sql = "SELECT `s_username` FROM `{$this->_dbtabpre}user` WHERE `l_user_id` = '".$value['user_id']."'";
					$user_name = $this->_getFirstResult($sql);
					$list[$key]['s_username'] 		= $user_name;
				}
				
			}
			return $list;
		}
		

		/**
		 * 搜索评价驾校列表
		 * @param $page int 页码
		 * @param $limit int 限制每页数量
		 * @param $school_id int 驾校ID
		 * @param $conditiontype int 搜索条件限制（1：教练ID 2：教练姓名 3：教练号码 ）
		 *
		 * @param $keyword string 关键词
		 * @return void
		 * @author 
		 **/
		public function getSearchSCommentList($page='', $limit='', $school_id, $conditiontype) {
			if(!$school_id){
				return array();
			}
			$list = array();
			$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}coach_comment` as c ON c.`user_id` = u.`l_user_id` WHERE c.`school_id` = '".$school_id."'  AND c.`type` = 2";
			$sql .= " AND c.`school_star` = '{$conditiontype}'";
			$sql .= " ORDER BY c.`addtime` DESC";
			if($page !=='' && $limit !== '') {
				$sql .= " LIMIT $page, $limit";
			}else {
				$sql .= "";
			}
			$res = $this->_getAllRecords($sql);
			if($res) {
				foreach ($res as $key => $value) {
					$list[$key]['id'] 				= $value['id'];
					$list[$key]['school_star'] 		= intval($value['school_star']);
					$list[$key]['school_content'] 	= $value['school_content'];
					$list[$key]['order_no'] 		= $value['order_no'];
					$list[$key]['addtime'] 			= date('Y-m-d H:i', $value['addtime']);
					$list[$key]['s_username'] 		= $value['s_username'];
					$list[$key]['s_real_name'] 		= $value['s_real_name'];
					$list[$key]['s_phone'] 			= $value['s_phone'];
				}
			}
			return $list;
		}
		
		


		/**
			 * 搜索评价学员列表
			 * @param $page int 页码
			 * @param $limit int 限制每页数量
			 * @param $school_id int 驾校ID
			 * @param $coachconditiontype int 教练搜索条件限制 0：没有条件 1；教练姓名 2：教练号码
			 * @param $studentconditiontype int 学员搜索条件限制 1：评价星级 2：订单号 3：学员昵称 4：学员姓名 5：学员号码
			 * @param $keyword string 关键词
			 * @return array
			 * @author 
			 **/
		public function getSearchStuCommentList($page='', $limit='', $school_id, $studentconditiontype, $keyword) {
			if(!$school_id) {
				return array();
			}
			// 获取所有教练ID
			$sql = "SELECT c.`l_coach_id` FROM `{$this->_dbtabpre}coach` as c LEFT JOIN `{$this->_dbtabpre}user` as u ON u.`coach_id` = c.`l_coach_id`  WHERE c.`s_school_name_id` = $school_id";
			switch ( $studentconditiontype) {
				case '1':
					$sql .= " AND u.`s_real_name` LIKE '%".$keyword."%'";
					break;
				case '2':
					$sql .= " AND u.`s_phone` LIKE '%".$keyword."%'";
					break;
				default:
					$sql .= " AND u.`s_real_name` LIKE '%".$keyword."%'";
					break;
			}
			// echo $sql;
			$coach_ids = $this->_getAllRecords($sql);
			$coach_id_arr = array();
			if($coach_ids) {
				foreach ($coach_ids as $key => $value) {
					$coach_id_arr[] = $value['l_coach_id'];
				}
			} else {
				$coach_id_arr[] = 0;
			}
			$sql = "SELECT * FROM `{$this->_dbtabpre}student_comment` WHERE `coach_id` IN (".implode(',', $coach_id_arr).") ORDER BY `id` DESC"; 	
			$student_comment_list = $this->_getAllRecords($sql);
			$list = array();
			if($student_comment_list) {
				foreach ($student_comment_list as $key => $value) {
					$list[$key]['id'] = $value['id'];
					$list[$key]['order_no'] = $value['order_no'];
					$list[$key]['star_num'] = intval($value['star_num']);
					$list[$key]['content'] = $value['content'];
					$list[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);
					// 搜索学员信息
					$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`l_user_id` = '".$value['user_id']."'";
					
					$user_info = $this->_getFirstRecord($sql);
					if($user_info) {
						$list[$key]['s_username'] = $user_info['s_username'];
						$list[$key]['s_real_name'] = $user_info['s_real_name'];
						$list[$key]['s_phone'] = $user_info['s_phone'];
						$list[$key]['sex'] = $user_info['sex'];
						$list[$key]['age'] = $user_info['age'];
						$list[$key]['identity_id'] = $user_info['identity_id'];
						$list[$key]['address'] = $user_info['address'];
						$list[$key]['photo_id'] = $user_info['photo_id'];
						$list[$key]['province_id'] = $user_info['province_id'];
						$list[$key]['city_id'] = $user_info['city_id'];
						$list[$key]['area_id'] = $user_info['area_id'];
						$list[$key]['user_photo'] = HTTP_HOST.$user_info['user_photo'];
						$list[$key]['learncar_status'] = $user_info['learncar_status'];
					} else {
						$list[$key]['s_username'] = '';
						$list[$key]['s_real_name'] = '';
						$list[$key]['s_phone'] = '';
						$list[$key]['sex'] = '';
						$list[$key]['age'] = '';
						$list[$key]['identity_id'] = '';
						$list[$key]['address'] = '';
						$list[$key]['photo_id'] = '';
						$list[$key]['province_id'] = '';
						$list[$key]['city_id'] = '';
						$list[$key]['area_id'] = '';
						$list[$key]['user_photo'] = '';
						$list[$key]['learncar_status'] = '';
					}

					// 搜索教练
					$sql = "SELECT `l_coach_id`, `s_coach_name`, `s_coach_imgurl`, `s_coach_phone` FROM `{$this->_dbtabpre}coach` WHERE `l_coach_id` = '".$value['coach_id']."'";
					$coach_info = $this->_getFirstRecord($sql);
					if($coach_info) {
						$list[$key]['l_coach_id'] = $coach_info['l_coach_id'];
						$list[$key]['s_coach_name'] = $coach_info['s_coach_name'];
						$list[$key]['s_coach_imgurl'] = HTTP_HOST.$coach_info['s_coach_imgurl'];
						$list[$key]['s_coach_phone'] = $coach_info['s_coach_phone'];
					} else {
						$list[$key]['l_coach_id'] = '';
						$list[$key]['s_coach_name'] = '';
						$list[$key]['s_coach_imgurl'] = '';
						$list[$key]['s_coach_phone'] = '';
					}
				}
			}
			
		   return $list;		
			// // 搜索评价列表
			// $sql = "SELECT * FROM `{$this->_dbtabpre}student_comment` WHERE `coach_id` IN (".implode(',', array_unique($coach_ids)).")";
			// switch ($coachconditiontype) {
			// 	case '0': // 教练无条件
			// 		$sql .= $this->searchStudentConditionSql($studentconditiontype, $keyword);
			// 		break;
			// 	case '1': // 教练姓名
			// 		$sql .= $this->searchStudentConditionSql($studentconditiontype, $keyword);
			// 		$coach_sql = "SELECT `l_coach_id` FROM `{$this->_dbtabpre}coach` WHERE `s_coach_name` LIKE '%".$keyword."%'";
			// 		$_coach_ids_arr = $this->_getAllRecords($coach_sql);
			// 		$_coach_ids = array();
			// 		if($_coach_ids_arr) {
			// 			foreach ($_coach_ids_arr as $key => $value) {
			// 				$_coach_ids[] = $value['l_coach_id'];
			// 			}
			// 			$sql .= " AND `coach_id` IN (".implode(',', array_unique($_coach_ids)).")";
			// 		} else {
			// 			$sql .= " AND `coach_id` = 0";
			// 		}
			// 		break;

			// 	case '2': // 教练号码
			// 		$sql .= $this->searchStudentConditionSql($studentconditiontype, $keyword);
			// 		$coach_sql = "SELECT `l_coach_id` FROM `{$this->_dbtabpre}coach` WHERE `s_coach_phone` LIKE '%".$keyword."%'";
			// 		$_coach_ids_arr = $this->_getAllRecords($coach_sql);
			// 		$_coach_ids = array();
			// 		if($_coach_ids_arr) {
			// 			foreach ($_coach_ids_arr as $key => $value) {
			// 				$_coach_ids[] = $value['l_coach_id'];
			// 			}
			// 			$sql .= " AND `coach_id` IN (".implode(',', array_unique($_coach_ids)).")";
			// 		} else {
			// 			$sql .= " AND `coach_id` = 0";
			// 		}
						
			// 		break;
			// 	default:
			// 		$sql .= $this->searchStudentConditionSql($studentconditiontype, $keyword);
			// 		break;
			// }
			// $commentlist = $this->_getAllRecords($sql);
			// if($commentlist) {
			// 	foreach ($commentlist as $key => $value) {
			// 		$list[$key]['id'] = $value['id'];
			// 		// 查询学员信息
			// 		$sql = "SELECT * FROM `{$this->_dbtabpre}user` as u LEFT JOIN `{$this->_dbtabpre}users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`l_user_id` = '".$value['user_id']."'";
			// 		$user_info = $this->_getFirstRecord($sql);
			// 		if($user_info) {
			// 			$list[$key]['s_username'] = $user_info['s_username'];
			// 			$list[$key]['s_real_name'] = $user_info['s_real_name'];
			// 			$list[$key]['s_phone'] = $user_info['s_phone'];
			// 			$list[$key]['sex'] = $user_info['sex'];
			// 			$list[$key]['age'] = $user_info['age'];
			// 			$list[$key]['identity_id'] = $user_info['identity_id'];
			// 			$list[$key]['address'] = $user_info['address'];
			// 			$list[$key]['photo_id'] = $user_info['photo_id'];
			// 			$list[$key]['province_id'] = $user_info['province_id'];
			// 			$list[$key]['city_id'] = $user_info['city_id'];
			// 			$list[$key]['area_id'] = $user_info['area_id'];
			// 			$list[$key]['user_photo'] = HTTP_HOST.$user_info['user_photo'];
			// 			$list[$key]['learncar_status'] = $user_info['learncar_status'];
			// 		} else {
			// 			$list[$key]['s_username'] = '';
			// 			$list[$key]['s_real_name'] = '';
			// 			$list[$key]['s_phone'] = '';
			// 			$list[$key]['sex'] = '';
			// 			$list[$key]['age'] = '';
			// 			$list[$key]['identity_id'] = '';
			// 			$list[$key]['address'] = '';
			// 			$list[$key]['photo_id'] = '';
			// 			$list[$key]['province_id'] = '';
			// 			$list[$key]['city_id'] = '';
			// 			$list[$key]['area_id'] = '';
			// 			$list[$key]['user_photo'] = '';
			// 			$list[$key]['learncar_status'] = '';
			// 		}
					
			// 		// 查询教练信息
			// 		$sql = "SELECT * FROM `{$this->_dbtabpre}coach` WHERE `l_coach_id` = '".$value['coach_id']."'";
			// 		$coach_info = $this->_getFirstRecord($sql);
			// 		if($coach_info) {
			// 			$list[$key]['s_coach_lesson_id'] = $coach_info['s_coach_lesson_id'];
			// 			$list[$key]['i_type'] = $coach_info['i_type'];
			// 			$list[$key]['l_coach_id'] = $coach_info['l_coach_id'];
			// 			$list[$key]['s_coach_name'] = $coach_info['s_coach_name'];
			// 			$list[$key]['s_coach_phone'] = $coach_info['s_coach_phone'];
			// 			$list[$key]['l_coach_id'] = $coach_info['l_coach_id'];
			// 			$list[$key]['i_coach_star'] = $coach_info['i_coach_star'];
			// 			$list[$key]['addtime'] = date('Y-m-d H:i', $coach_info['addtime']);
			// 			$list[$key]['type'] = '预约学员';
			// 			$list[$key]['user_id'] = $coach_info['user_id'];
	
			// 		} else {
			// 			$list[$key]['s_coach_lesson_id'] = $coach_info['s_coach_lesson_id'];
			// 			$list[$key]['i_type'] = $coach_info['i_type'];
			// 			$list[$key]['l_coach_id'] = $coach_info['l_coach_id'];
			// 			$list[$key]['s_coach_name'] = $coach_info['s_coach_name'];
			// 			$list[$key]['s_coach_phone'] = $coach_info['s_coach_phone'];
			// 			$list[$key]['i_coach_star'] = $coach_info['i_coach_star'];
			// 			$list[$key]['addtime'] = date('Y-m-d H:i', $coach_info['addtime']);
			// 			$list[$key]['type'] = '预约学员'; 
			// 			$list[$key]['user_id'] = $coach_info['user_id'];
			// 		}
						
			// 		$list[$key]['order_no'] = $value['order_no'];
			// 		$list[$key]['content'] = $value['content'];
			// 		$list[$key]['star_num'] = $value['star_num'];
			// 		$list[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);
			// 	}
			// }
	  // 	 	return $list;		
		
			
		}

		/**
		 * 学员搜索条件sql语句
		 * @param int $studentconditiontype 学员条件
		 * @return string
		 * @author 
		 **/

		private function searchStudentConditionSql($studentconditiontype, $keyword) {
			$sql = '';
			switch ($studentconditiontype) {
				case '0':
					$sql .= "";
					break;
				case '1':
					$sql .= " AND `star_num` = '".$keyword."'";
					break;
				case '2':
					$sql .= " AND `order_no` LIKE '%".$keyword."%'";
					break;
				case '3':
					$user_sql = "SELECT `l_user_id` FROM `{$this->_dbtabpre}user` WHERE `s_username` LIKE '%".$keyword."%'";
					$user_ids_arr = $this->_getAllRecords($user_sql);
					$user_ids = array();
					if($user_ids_arr) {
						foreach ($user_ids_arr as $key => $value) {
							$user_ids[] = $value['l_user_id'];
						}
						$sql .= " AND `user_id` IN (".implode(',', array_unique($user_ids)).")";
					} else {
						$sql .= " AND `user_id` = 0";
					}
					break;
				case '4':
					$user_sql = "SELECT `l_user_id` FROM `{$this->_dbtabpre}user` WHERE `s_real_name` LIKE '%".$keyword."%'";
					$user_ids_arr = $this->_getAllRecords($user_sql);
					$user_ids = array();
					if($user_ids_arr) {
						foreach ($user_ids_arr as $key => $value) {
							$user_ids[] = $value['l_user_id'];
						}
						$sql .= " AND `user_id` IN (".implode(',', array_unique($user_ids)).")";
					} else {
						$sql .= " AND `user_id` = 0";
					}
					break;
				case '5':
					$user_sql = "SELECT `l_user_id` FROM `{$this->_dbtabpre}user` WHERE `s_phone` LIKE '%".$keyword."%'";
					$user_ids_arr = $this->_getAllRecords($user_sql);
					$user_ids = array();
					if($user_ids_arr) {
						foreach ($user_ids_arr as $key => $value) {
							$user_ids[] = $value['l_user_id'];
						}
						$sql .= " AND `user_id` IN (".implode(',', array_unique($user_ids)).")";
					} else {
						$sql .= " AND `user_id` = 0";
					}
					break;
				default:
					$sql .= " AND `star_num` = '".$keyword."'";
					break;
			}
			return $sql;
		}
		
	}	


?>