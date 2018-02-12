<?php  
	// 教练模块

	!defined('IN_FILE') && exit('Access Denied');

	class mcoach extends mbase {

		/**
		 * 获取教练列表
		 * @param $page int 页码
		 * @param $limit int 限制每页显示数量
		 * @return array 教练列表
		 */

		public function getCoachlist($page='', $limit='') {

			Global $lisence_config, $lesson_config;
			
			if($page !== '' && $limit !== '') {
				$sql = "SELECT * FROM {$this->_dbtabpre}coach ORDER BY `l_coach_id` DESC LIMIT $page, $limit";
			} else {
				$sql = "SELECT * FROM {$this->_dbtabpre}coach ORDER BY `l_coach_id` DESC";
			}
			$query = $this->_db->query($sql);
			$list = array();
			$i = 0;
			while($row = $this->_db->fetch_array($query)) {
				$coach_lesson_arr = array();
				$coach_lisence_arr = array();
				$list[$i]['coach_id'] = $row['l_coach_id'];
				$list[$i]['coach_name'] = $row['s_coach_name'];
				$list[$i]['coach_phone'] = $row['s_coach_phone'];
				$list[$i]['school_name_id'] = $row['s_school_name_id'];

				// 通过学校ID获取学校记录
				$sql = "SELECT `s_school_name` FROM `{$this->_dbtabpre}school` WHERE `l_school_id` = {$row['s_school_name_id']}";
				$school_name = $this->_getFirstResult($sql);
				$list[$i]['school_name'] = $school_name;

				// 转换课程
				$s_coach_lesson_id = explode(',', $row['s_coach_lesson_id']);
				foreach ($s_coach_lesson_id as $key => $lesson_id) {
					if(in_array($lesson_id, array_keys($lesson_config))) {
						$coach_lesson_arr[] = $lesson_config[$lesson_id];
					}
				}
				$coach_lesson_str = implode(',', $coach_lesson_arr);

				// 转换牌照
				$s_coach_lisence_id = explode(',', $row['s_coach_lisence_id']);
				foreach ($s_coach_lisence_id as $key => $lisence_id) {
					if(in_array($lisence_id, array_keys($lisence_config))) {
						$coach_lisence_arr[] = $lisence_config[$lisence_id];
					}
				}
				$coach_lisence_str = implode(',', $coach_lisence_arr);

				$list[$i]['coach_lesson'] = $coach_lesson_str;
				$list[$i]['coach_lisence'] = $coach_lisence_str;
				if($row['s_coach_car_id']) {
					$sql = "SELECT `name` FROM `{$this->_dbtabpre}cars` WHERE `id` IN ($row[s_coach_car_id])";
					$coach_car_name_arr = $this->_getAllRecords($sql);
					$car_name = '';
					if($coach_car_name_arr) {
						foreach ($coach_car_name_arr as $key => $value) {
							$car_name .= $value['name']."<br>";
						}
					}
					
				} else {
					$car_name = '暂无设置';
				}
				
				$list[$i]['coach_car_name'] = $car_name;
				$list[$i]['coach_star'] = $row['i_coach_star'];
				$list[$i]['good_coach_star'] = $row['good_coach_star'];
				$list[$i]['service_count'] = $row['i_service_count']; // 教练服务次数
				$list[$i]['success_count'] = $row['i_success_count']; // 教练服务通过人数
				$list[$i]['coach_address'] = $row['s_coach_address']; // 教练地址
				$list[$i]['is_online'] = $row['order_receive_status']; // 教练地址
				if($row['order_receive_status'] == 1) {
					$list[$i]['online_status'] = '在线';
				} else if($row['order_receive_status'] == 0){
					$list[$i]['online_status'] = '不在线';
				}
				$list[$i]['i_type'] = $row['i_type'];
				if($row['i_type'] == 0) {
					$list[$i]['type'] = '内部教练'; // 教练类型： 0:挂靠教练 1:内部教练
				} else if($row['i_type'] == 1){
					$list[$i]['type'] = '挂靠教练'; //
				}

				$list[$i]['addtime'] = date('Y-m-d H:i', $row['addtime']); // 教练类型： 挂靠教练 内部教练
				$i++;
			}

			return $list;
		}

		/**
		 * 获取搜索教练列表
		 * @param 	$page int 页码
		 * @param 	$limit int 限制每页显示数量
		 * @param 	$conditiontype int 搜索类型
		 * @param 	$keywords int 关键词
		 * @return 	array
		 * @author 	wl
		 * @date	August 30, 2016
		 **/
		public function getSearchCoachlist($page='', $limit='', $conditiontype=1, $type="default", $order="desc", $keywords='') {

			Global $lisence_config, $lesson_config;
			$order = strtolower($order) == 'desc' ? 'DESC' : 'ASC';
			
			$sql = "SELECT * FROM {$this->_dbtabpre}coach WHERE ";
			switch ($conditiontype) {
				case '1':
					$sql .= " `l_coach_id` = '{$keywords}' ";
					break;
				case '2':
					$sql .= " `s_coach_name` LIKE '%{$keywords}%' ";
					break;
				case '3':
					$sql .= " `s_coach_phone` LIKE '%{$keywords}%' ";
					break;
				default:
					$sql .= " `l_coach_id` = '{$keywords}' ";
					break;
			}
			switch ($type) {
				case 'default':
					$sql .= " ORDER BY `l_coach_id` $order";
					break;
				case 'star':
					$sql .= " ORDER BY `i_coach_star` $order, `l_coach_id` DESC";
					break;
				case 'comment':
					$sql .= " ORDER BY `good_coach_star` $order";
					break;
				default:
					$sql .= " ORDER BY `l_coach_id` $order";
					break;
			}

			// echo $sql;
			if($page !== '' && $limit !== '') {
				$sql .= " LIMIT $page, $limit";
			} else {
				$sql .= "";
			}
			$query = $this->_db->query($sql);
			$list = array();
			$i = 0;
			while($row = $this->_db->fetch_array($query)) {
				$coach_lesson_arr = array();
				$coach_lisence_arr = array();
				$list[$i]['coach_id'] = $row['l_coach_id'];
				$list[$i]['coach_name'] = $row['s_coach_name'];
				$list[$i]['coach_age'] = $row['s_teach_age'];
				$list[$i]['coach_sex'] = $row['s_coach_sex'] == 1 ? '男' : '女';
				$list[$i]['coach_phone'] = $row['s_coach_phone'];
				$list[$i]['school_name_id'] = $row['s_school_name_id'];

				// 通过学校ID获取学校记录
				$sql = "SELECT `s_school_name` FROM `{$this->_dbtabpre}school` WHERE `l_school_id` = {$row['s_school_name_id']}";
				$school_name = $this->_getFirstResult($sql);
				$list[$i]['school_name'] = $school_name;

				// 转换课程
				$s_coach_lesson_id = explode(',', $row['s_coach_lesson_id']);
				foreach ($s_coach_lesson_id as $key => $lesson_id) {
					if(in_array($lesson_id, array_keys($lesson_config))) {
						$coach_lesson_arr[] = $lesson_config[$lesson_id];
					}
				}
				$coach_lesson_str = implode(',', $coach_lesson_arr);

				// 转换牌照
				$s_coach_lisence_id = explode(',', $row['s_coach_lisence_id']);
				foreach ($s_coach_lisence_id as $key => $lisence_id) {
					if(in_array($lisence_id, array_keys($lisence_config))) {
						$coach_lisence_arr[] = $lisence_config[$lisence_id];
					}
				}
				$coach_lisence_str = implode(',', $coach_lisence_arr);

				$list[$i]['coach_lesson'] = $coach_lesson_str;
				$list[$i]['coach_lisence'] = $coach_lisence_str;
				if($row['s_coach_car_id']) {
					$sql = "SELECT `name` FROM `{$this->_dbtabpre}cars` WHERE `id` IN ($row[s_coach_car_id])";
					$coach_car_name_arr = $this->_getAllRecords($sql);
					$car_name = '';
					if($coach_car_name_arr) {
						foreach ($coach_car_name_arr as $key => $value) {
							$car_name .= $value['name']."<br>";
						}
					}
					
				} else {
					$car_name = '暂无设置';
				}
				
				$list[$i]['coach_car_name'] = $car_name;
				$list[$i]['coach_star'] = $row['i_coach_star']; // 平均星级
				$list[$i]['good_coach_star'] = $row['good_coach_star']; // 好评
				$list[$i]['service_count'] = $row['i_service_count']; // 教练服务次数
				$list[$i]['success_count'] = $row['i_success_count']; // 教练服务通过人数
				$list[$i]['coach_address'] = $row['s_coach_address']; // 教练地址
				$list[$i]['is_online'] = $row['order_receive_status']; // 教练地址
				if($row['order_receive_status'] == 1) {
					$list[$i]['online_status'] = '在线';
				} else if($row['order_receive_status'] == 0){
					$list[$i]['online_status'] = '不在线';
				}
				$list[$i]['i_type'] = $row['i_type'];
				if($row['i_type'] == 0) {
					$list[$i]['type'] = '金牌教练'; // 教练类型： 0:金牌教练 1:普通教练
				} else if($row['i_type'] == 1){
					$list[$i]['type'] = '普通教练'; //
				}

				$list[$i]['addtime'] = date('Y-m-d H:i', $row['addtime']); // 教练类型：  0:金牌教练 1:普通教练
				$i++;
			}
			$this->_db->free_result($query);
			return $list;
		}
		/**
		 * 获取教练列表
		 * @param $id int 教练ID
		 * @return boolean 成功与否
		 */

		public function setCoachOnlineStatus($id) {
			$sql = "SELECT `order_receive_status` FROM {$this->_dbtabpre}coach WHERE `l_coach_id` = ".$id;
			$order_receive_status = $this->_getFirstResult($sql);
			if($order_receive_status == 1) {
				$sql = "UPDATE {$this->_dbtabpre}coach SET `order_receive_status` = 0 WHERE `l_coach_id` = ".$id;
			} else {
				$sql = "UPDATE {$this->_dbtabpre}coach SET `order_receive_status` = 1 WHERE `l_coach_id` = ".$id;
			}
			$query = $this->_db->query($sql);
			return $query;
		}

		/**
		 * 获取教练详情
		 * @param $id int 教练ID
		 * @return array 
		 */
		public function getCoachDetail($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}coach` WHERE `l_coach_id` = ".$id;
			$res = $this->_getFirstRecord($sql);
			return $res;
		}

		/**
		 * 生成可变的日期配置
		 * @param $id int 教练ID
		 * @return array 
		 */
		public function getCoachTimeConfig() {
			$current_time = time();
			$year = date('Y', $current_time); //年
			$month = date('m', $current_time); //月
			$day = date('d', $current_time); //日

			// 构建一个时间
			$build_date_timestamp = mktime(0,0,0,$month,$day,$year);

			// 循环7天日期
			$date_config = array();
			for($i = 0; $i <= 6; $i++) {
				// $date_config['date'][] = date('m-d', strtotime("+".($i)." day")); //或者这种算法
				$date_config['date'][] = date('m-d', $build_date_timestamp + ( 24 * 3600 * $i));	
			}

			// 数据表获取当前时间配置
			$sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config`";
			$row = $this->_getAllRecords($sql);
			$date_config['time'] = $row;

			return $date_config;

		}

		/**
		 * 更新时间配置
		 * @param $arr array 数组
		 * @return bool 
		 */
		public function updateCoachTime($arr) {

			// 查询当前日期的数据
			$sql = "SELECT * FROM `{$this->_dbtabpre}current_coach_time_configuration` WHERE `coach_id` = ".$arr['coach_id']." AND `year` = ".$arr['year']." AND `month` = ".$arr['month']." AND `day` = ".$arr['day'];
			$row = $this->_getFirstRecord($sql);

			if($row) {
				$sql = "UPDATE `{$this->_dbtabpre}current_coach_time_configuration` SET";
				$sql .= " `current_time` = ".$arr['current_time'].",";
				$sql .= " `time_config_money_id` = '".$arr['time_config_money_id']."',";
				$sql .= " `time_config_id` = '".$arr['time_config_id']."',";
				$sql .= " `time_lisence_config_id` = '".$arr['time_lisence_config_id']."',";
				$sql .= " `time_lesson_config_id` = '".$arr['time_lesson_config_id']."',";
				$sql .= " `addtime` = ".time()." WHERE `coach_id` = ".$arr['coach_id']." AND `year` = ".$arr['year']." AND `month` = ".$arr['month']." AND `day` = ".$arr['day'];
				
			} else {
				$sql = "INSERT  INTO `{$this->_dbtabpre}current_coach_time_configuration` ";
				$sql .= " (`current_time`, `time_config_money_id`, `time_config_id`, `time_lisence_config_id`, `time_lesson_config_id`, `coach_id`, `year`, `month`, `day`, `addtime`)";
				$sql .= " VALUES(".$arr['current_time'].", '".$arr['time_config_money_id']."', '".$arr['time_config_id']."', '".$arr['time_lisence_config_id']."', '".$arr['time_lesson_config_id']."', '".$arr['coach_id']."', ".$arr['year'].", ".$arr['month'].", ".$arr['day'].",".time().")";
			}

			$query = $this->_db->query($sql);
			return $query;
		}

		/**
		 * 删除前一天时间数据
		 * @param $arr array 数组
		 * @return bool 
		 */
		public function delPreTime($coach_id) {

			$pre_time = time() - 16*3600;
			$year = date('Y', $pre_time);
			$month = date('m', $pre_time);
			$day = date('d', $pre_time);
			echo $day;

			$sql = "DELETE FROM `{$this->_dbtabpre}current_coach_time_configuration` ";
			$sql .=" WHERE `coach_id` = ".$coach_id." AND `year` = ".$year." AND `month` = ".$month." AND `day` = ".$day;
			$query = $this->_db->query($sql);
			return $query;
		}

		/**
		 * 删除前一天时间数据
		 * @param $arr array 数组
		 * @return bool 
		 */
		public function delAllTime($coach_id) {
			$sql = "DELETE FROM `{$this->_dbtabpre}current_coach_time_configuration` WHERE `coach_id` = ".$coach_id;
			$query = $this->_db->query($sql);
			return $query;
		}

		/**
		 * 更新教练信息
		 * @param $arr array 数组
		 * @return bool 
		 */
		public function updateCoachInfo($arr) {

			$sql = "UPDATE `{$this->_dbtabpre}coach` SET ";
			$sql .= " `s_coach_name` = '".$arr['s_coach_name']."', ";
			$sql .= " `s_coach_phone` = '".$arr['s_coach_phone']."', ";
			$sql .= " `s_school_name_id` = '".$arr['s_school_name_id']."', ";
			$sql .= " `s_coach_lesson_id` = '".$arr['s_coach_lesson_id']."', ";
			$sql .= " `s_coach_lisence_id` = '".$arr['s_coach_lisence_id']."', ";
			$sql .= " `s_coach_car_id` = '".$arr['s_coach_car_id']."', ";
			$sql .= " `s_coach_address` = '".$arr['s_address']."', ";
			$sql .= " `province_id` = '".$arr['province_id']."', ";
			$sql .= " `city_id` = '".$arr['city_id']."', ";
			$sql .= " `area_id` = '".$arr['area_id']."', ";
			$sql .= " `order_receive_status` = '".$arr['is_online']."', ";
			$sql .= " `i_type` = '".$arr['i_type']."', ";
			// $sql .= " `s_yh_name` = '".$arr['s_yh_name']."' ";
			// $sql .= " `s_yh_zhanghao` = '".$arr['s_yh_zhanghao']."', ";
			// $sql .= " `s_yh_huming` = '".$arr['s_yh_huming']."', ";
			$sql .= " `s_coach_imgurl` = '".$arr['s_coach_imgurl']."' ";
			$sql .= " WHERE `l_coach_id` = ".$arr['l_coach_id'];

			$query = $this->_db->query($sql);
			return $query;
		}

		/**
		 * 新增教练信息
		 * @param $arr array 数组
		 * @return bool 
		 */
		public function insertCoachInfo($arr) {
			// 获取当前驾校的经纬度
			$sql = "SELECT `s_location_x`, `s_location_y` FROM `{$this->_dbtabpre}school` WHERE `l_school_id` = ".$arr['s_school_name_id'];
			$school_info = $this->_getFirstRecord($sql);
			if($school_info) {
				$dc_coach_distance_x = $school_info['s_location_x'];
				$dc_coach_distance_y = $school_info['s_location_y'];
			} else {
				$dc_coach_distance_x = '0.000000';
				$dc_coach_distance_y = '0.000000';
			}

			$sql = "INSERT INTO `{$this->_dbtabpre}coach` ";
			$sql .= " (`s_coach_name`, `s_coach_phone`, `s_school_name_id`, `s_coach_lesson_id`, `s_coach_lisence_id`,";
			$sql .= " `s_coach_car_id`,`dc_coach_distance_x`, `dc_coach_distance_y`, `s_coach_address`, `province_id`, `city_id`, `area_id`, `order_receive_status`, `i_type`,";
			$sql .= " `s_coach_imgurl`) VALUES (";
			$sql .= " '".$arr['s_coach_name']."', '".$arr['s_coach_phone']."', '".$arr['s_school_name_id']."',";
			$sql .= " '".$arr['s_coach_lesson_id']."', '".$arr['s_coach_lisence_id']."', '".$arr['s_coach_car_id']."', '".$dc_coach_distance_x."', '".$dc_coach_distance_y."',";
			$sql .= " '".$arr['s_address']."', '".$arr['province_id']."', '".$arr['city_id']."', '".$arr['area_id']."',";
			$sql .= " '".$arr['is_online']."', '".$arr['i_type']."','".$arr['s_coach_imgurl']."')";

			$query = $this->_db->query($sql);
			return $query;
		}

		// 删除教练

		public function deleteCoach($id) {
			$sql = "SELECT `user_id` FROM `{$this->_dbtabpre}coach` WHERE `l_coach_id` IN ($id) ";
			$user_id = $this->_getAllRecords($sql);
			$user_ids = array();
			if ($user_id) {
				foreach ($user_id as $key => $value) {
					$user_ids[] = $value['user_id']; 
				}
			}
			$uid = implode(",", $user_ids);
			$sql = "DELETE FROM `{$this->_dbtabpre}coach` WHERE `l_coach_id` IN ($id)";
			$query = $this->_db->query($sql);
			if ($query) {
				$sql = "UPDATE `{$this->_dbtabpre}user` SET `i_status` = 2  WHERE `l_user_id` IN ($uid) ";
				$query = $this->_db->query($sql);
			}
			return $query;
		}

		// 获取当前教练时间配置
		public function getCoachCurrentTimeConfig($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}current_coach_time_configuration` WHERE `coach_id` = $id ORDER BY `current_time` ASC";
			$row = $this->_getAllRecords($sql);
			if(!$row) {
				return array();
			}

			$list = array();
			foreach ($row as $key => $value) {

				$time_list = array();

				// 获取时间配置
				$money_config = json_decode($value['time_config_money_id'], true);
				$lisence_config = json_decode($value['time_lisence_config_id'], true);
				$lesson_config = json_decode($value['time_lesson_config_id'], true);
				$time_config = explode(',', $value['time_config_id']);
				$date_config[] = $value['month'].'-'.$value['day'];

				// 找到当前的时间配置
				$sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config` WHERE `id` IN (".implode(',', $time_config).")";
				$time_row = $this->_getAllRecords($sql);

				foreach ($time_row as $k => $v) {
					$i = 1;
					$time_list[$k]['id'] = $v['id'];
					$time_list[$k]['start_time'] = $v['start_time'];
					$time_list[$k]['end_time'] = $v['end_time'];
					$time_list[$k]['lisence_name'] = $lisence_config[$v['id']];
					$time_list[$k]['lesson_name'] = $lesson_config[$v['id']];
					$time_list[$k]['money'] = $money_config[$v['id']];
					$i++;
				}
				$list['time_list'][$key] = $time_list;
			}
			$list['date_time'] = $date_config;
			return $list;	
		}

		// 保存更新姓名

		public function saveCoachName($arr) {
			$sql = "UPDATE `{$this->_dbtabpre}coach` SET `s_coach_name` = '".$arr['coach_name']."' WHERE `l_coach_id` = ".$arr['id'];
			$query = $this->_db->query($sql);
			return $query;
		}

		// 保存更新电话

		public function saveCoachPhone($arr) {
			$sql = "UPDATE `{$this->_dbtabpre}coach` SET `s_coach_phone` = '".$arr['coach_phone']."' WHERE `l_coach_id` = ".$arr['id'];
			$query = $this->_db->query($sql);
			return $query;
		}

		// 保存更新所属车辆

		public function saveCoachCar($arr) {
			$sql = "UPDATE `{$this->_dbtabpre}coach` SET `s_coach_car_id` = '".$arr['coach_phone']."', `s_coach_car_no` = '".$arr['coach_car_no']."' WHERE `l_coach_id` = ".$arr['id'];
			$query = $this->_db->query($sql);
			return $query;
		}

		// 保存更新地址

		public function saveCoachAddress($arr) {
			$sql = "UPDATE `{$this->_dbtabpre}coach` SET `s_coach_address` = '".$arr['coach_address']."', `province_id` = '".$arr['province_id']."', `city_id` = '".$arr['city_id']."', `area_id` = '".$arr['area_id']."' WHERE `l_coach_id` = ".$arr['id'];
			$query = $this->_db->query($sql);
			return $query;
		}

		/************************************************************** 
		 * 
		 *  使用特定function对数组中所有元素做处理 
		 *  @param  string  &$array     要处理的字符串 
		 *  @param  string  $function   要执行的函数 
		 *  @return boolean $apply_to_keys_also     是否也应用到key上 
		 *  @access public 
		 * 
		 *************************************************************/  
		public function arrayRecursive(&$array, $function, $apply_to_keys_also = false)  
		{  
		    static $recursive_counter = 0;  
		    if (++$recursive_counter > 1000) {  
		        die('possible deep recursion attack');  
		    }  
		    foreach ($array as $key => $value) {  
		        if (is_array($value)) {  
		            arrayRecursive($array[$key], $function, $apply_to_keys_also);  
		        } else {  
		            $array[$key] = $function($value);  
		        }  
		   
		        if ($apply_to_keys_also && is_string($key)) {  
		            $new_key = $function($key);  
		            if ($new_key != $key) {  
		                $array[$new_key] = $array[$key];  
		                unset($array[$key]);  
		            }  
		        }  
		    }  
		    $recursive_counter--;  
		} 

		/************************************************************** 
		 * 
		 *  将数组转换为JSON字符串（兼容中文） 
		 *  @param  array   $array      要转换的数组 
		 *  @return string      转换得到的json字符串 
		 *  @access public 
		 * 
		 *************************************************************/  
		public function JSON($array) {  
		    $this->arrayRecursive($array, 'urlencode', true);  
		    $json = json_encode($array);  
		    return urldecode($json);  
		}
	}
?>