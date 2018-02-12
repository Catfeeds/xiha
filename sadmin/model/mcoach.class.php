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

		public function getCoachlist($page='', $limit='', $school_id, $type='default', $order='desc') {

			Global $lisence_config, $lesson_config;
			$order = strtolower($order) == 'desc' ? 'DESC' : 'ASC';
			if(!$school_id) {
				return array();
			}
			
			$sql = "SELECT * FROM {$this->_dbtabpre}coach WHERE `s_school_name_id` = $school_id";
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
			Global $lesson_config, $lisence_config;
			$sql = "SELECT * FROM `{$this->_dbtabpre}coach` WHERE `l_coach_id` = ".$id;
			$res = $this->_getFirstRecord($sql);
			if($res) {
				$res['coach_lesson_arr_id'] = isset($res['s_coach_lesson_id']) ? explode(',', $res['s_coach_lesson_id']) : array();
				$res['coach_lisence_arr_id'] = isset($res['s_coach_lisence_id']) ? explode(',', $res['s_coach_lisence_id']) : array();

				$lesson_list = array();
				$lisence_list = array();
				foreach ($lesson_config as $key => $value) {
					$lesson_list[$key]['lesson_name'] = $value;

					if(in_array($key, $res['coach_lesson_arr_id'])) {
						$lesson_list[$key]['is_set'] = 1;

					} else {
						$lesson_list[$key]['is_set'] = 2;

					}
				}

				foreach ($lisence_config as $key => $value) {
					$lisence_list[$key]['lisence_name'] = $value;

					if(in_array($key, $res['coach_lisence_arr_id'])) {
						$lisence_list[$key]['is_set'] = 1;

					} else {
						$lisence_list[$key]['is_set'] = 2;

					}
				}
				$res['lesson_config'] = $lesson_list;
				$res['lisence_config'] = $lisence_list;
			}
			return $res;
		}

		/**
		 * 生成可变的日期配置
		 * @param $id int 教练ID
		 * @return array 
		 */
        public function getCoachTimeConfig($school_id, $coach_id=0) {
            $current_time = time();
            $year = date('Y', $current_time); //年
            $month = date('m', $current_time); //月
            $day = date('d', $current_time); //日

            // 构建一个时间
            $build_date_timestamp = mktime(0,0,0,$month,$day,$year);

            // 循环7天日期
            $date_config = array();
            for($i = 0; $i <= 30; $i++) {
                // $date_config['date'][] = date('m-d', strtotime("+".($i)." day")); //或者这种算法
                $date_config['datetime'][$i]['fulldate'] = date('Y-m-d', $build_date_timestamp + ( 24 * 3600 * $i));	
                $date_config['datetime'][$i]['date'] = date('m-d', $build_date_timestamp + ( 24 * 3600 * $i));	
            }

            // 获取驾校的时间配置
            $sql = "SELECT `s_time_list`, `is_automatic` FROM `{$this->_dbtabpre}school_config` WHERE `l_school_id` = '{$school_id}'";
            $school_config = $this->_getFirstRecord($sql);
            $s_time_list = array();
            $is_automatic = 1;
            if($school_config) {
                $s_time_list = array_filter(explode(',', $school_config['s_time_list']));
                $is_automatic = $school_config['is_automatic'];
            }

            // 获取教练时间配置
            $sql = "SELECT `s_am_subject`, `s_pm_subject`, `s_am_time_list`, `s_pm_time_list` FROM `{$this->_dbtabpre}coach` WHERE `l_coach_id` = '{$coach_id}'";
            $coach_info = $this->_getFirstRecord($sql);
            $s_am_subject = 2;
            $s_pm_subject = 3;
            $s_am_time_list = array();
            $s_pm_time_list = array();

            if($coach_info) {
                $s_am_subject = $coach_info['s_am_subject'];
                $s_pm_subject = $coach_info['s_pm_subject'];
                $s_am_time_list = isset($coach_info['s_am_time_list']) ? array_filter(explode(',', $coach_info['s_am_time_list'])) : array();  // 2016-08-25 过虑掉空值
                $s_pm_time_list = isset($coach_info['s_pm_time_list']) ? array_filter(explode(',', $coach_info['s_pm_time_list'])) : array();
            }

            if(!empty($s_am_time_list) && !empty($s_pm_time_list)) {
                $time_config_ids_arr = array_merge($s_am_time_list, $s_pm_time_list);
            }  else {
                $time_config_ids_arr = $s_time_list;
            }

            // 数据表获取当前时间配置
            $sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config` WHERE `status` = 1 AND `school_id` = 1";

            $time_config_ids_arr = array_filter($time_config_ids_arr);
            if(!empty($time_config_ids_arr)) {
                $sql .= " AND `id` IN (".implode(',', $time_config_ids_arr).")";
                $sql .= " ORDER BY `start_time` ASC ";
            }
            $row = $this->_getAllRecords($sql);
            $am_list = array();
            $pm_list = array();

            if($row) {
                // 对每一天的时间进行安排
                foreach ($row as $key => $value) {
                    $start_minute = $value['start_minute'] == 0 ? '00' : $value['start_minute'];
                    $end_minute = $value['end_minute'] == 0 ? '00' : $value['end_minute'];
                    $row[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                    $row[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;

                    if(!empty($s_am_time_list) && !empty($s_pm_time_list)) {
                        // 上下午都不空
                        if(in_array($value['id'], $s_am_time_list)) {
                            $am_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                            $am_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
                            $am_list[$key]['id'] = $value['id'];
                            $am_list[$key]['license_no'] = $value['license_no'];
                            $am_list[$key]['subjects'] = $value['subjects'];
                            $am_list[$key]['price'] = $value['price'];
                            $am_list[$key]['status'] = $value['status'];

                            if($s_am_subject == 1) {
                                $am_list[$key]['subjects'] = '科目一';

                            } else if($s_am_subject == 2) {
                                $am_list[$key]['subjects'] = '科目二';

                            } else if($s_am_subject == 3) {
                                $am_list[$key]['subjects'] = '科目三';

                            } else if($s_am_subject == 4) {
                                $am_list[$key]['subjects'] = '科目四';

                            }
                        }

                        if(in_array($value['id'], $s_pm_time_list)) {

                            $pm_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                            $pm_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
                            $pm_list[$key]['id'] = $value['id'];
                            $pm_list[$key]['license_no'] = $value['license_no'];
                            $pm_list[$key]['subjects'] = $value['subjects'];
                            $pm_list[$key]['price'] = $value['price'];
                            $pm_list[$key]['status'] = $value['status'];

                            if($s_pm_subject == 1) {
                                $pm_list[$key]['subjects'] = '科目一';

                            } else if($s_pm_subject == 2) {
                                $pm_list[$key]['subjects'] = '科目二';

                            } else if($s_pm_subject == 3) {
                                $pm_list[$key]['subjects'] = '科目三';

                            } else if($s_pm_subject == 4) {
                                $pm_list[$key]['subjects'] = '科目四';

                            }
                        }
                    } else {
                        // 上下午都空
                        if($value['end_time'] <= 12) {
                            // 上午
                            $am_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                            $am_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
                            $am_list[$key]['id'] = $value['id'];
                            $am_list[$key]['license_no'] = $value['license_no'];
                            $am_list[$key]['subjects'] = $value['subjects'];
                            $am_list[$key]['price'] = $value['price'];
                            $am_list[$key]['status'] = $value['status'];

                            if($s_am_subject == 1) {
                                $am_list[$key]['subjects'] = '科目一';

                            } else if($s_am_subject == 2) {
                                $am_list[$key]['subjects'] = '科目二';

                            } else if($s_am_subject == 3) {
                                $am_list[$key]['subjects'] = '科目三';

                            } else if($s_am_subject == 4) {
                                $am_list[$key]['subjects'] = '科目四';

                            }
                        } else {
                            // 下午
                            $pm_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                            $pm_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
                            $pm_list[$key]['id'] = $value['id'];
                            $pm_list[$key]['license_no'] = $value['license_no'];
                            $pm_list[$key]['subjects'] = $value['subjects'];
                            $pm_list[$key]['price'] = $value['price'];
                            $pm_list[$key]['status'] = $value['status'];

                            if($s_pm_subject == 1) {
                                $pm_list[$key]['subjects'] = '科目一';

                            } else if($s_pm_subject == 2) {
                                $pm_list[$key]['subjects'] = '科目二';

                            } else if($s_pm_subject == 3) {
                                $pm_list[$key]['subjects'] = '科目三';

                            } else if($s_pm_subject == 4) {
                                $pm_list[$key]['subjects'] = '科目四';

                            }
                        } // 下午 end
                    }
                } // 每一天循环设置 end
            }

            $date_config['am_time'] = $am_list;
            $date_config['pm_time'] = $pm_list;

            return $date_config;

		}

		/**
		 * 更新时间配置
		 * @param $arr array 数组
		 * @return bool 
		 */
		public function updateCoachTime($arr) {

			// 查找当前时间段是否被预约
			$sql = "SELECT o.`time_config_id` FROM `{$this->_dbtabpre}study_orders` as o LEFT JOIN `{$this->_dbtabpre}coach_appoint_time` as a ON a.`id` = o.`appoint_time_id` WHERE a.`id` != '' AND a.`coach_id` = '{$arr['coach_id']}' AND o.`i_status` != 3 AND o.`i_status` != 101 AND a.`year` = '{$arr['year']}' AND a.`month` = '{$arr['month']}' AND a.`day` = '{$arr['day']}'";
			$is_appoint = $this->_getAllRecords($sql);
			$time_config_ids = array();
			$time_config_id_arr = array();
			if($is_appoint) {
				foreach ($is_appoint as $k => $v) {
					$time_config_ids = array_filter(explode(',', $v['time_config_id']));
					foreach ($time_config_ids as $e => $t) {
						$time_config_id_arr[] = $t;
					}
				}
			}
			$time_config_id = explode(',', $arr['time_config_id']);
			$diff = array_diff($time_config_id_arr, $time_config_id);
			if(!empty($diff)) {
				$sql = "SELECT `start_time`, `end_time` FROM `{$this->_dbtabpre}coach_time_config` WHERE `id` IN (".implode(',', $diff).")";
				$time_list = $this->_getAllRecords($sql);
				$time_arr = array();
				if($time_list) {
					foreach ($time_list as $key => $value) {
						$time_arr[] = $value['start_time'].':00'.'-'.$value['end_time'].':00';
					}
				}
				$data = array('code'=>2, 'data'=>$time_arr);
				return $data;
			}

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
				$sql .= " `addtime` = ".time()." WHERE `coach_id` = '".$arr['coach_id']."' AND `year` = ".$arr['year']." AND `month` = ".$arr['month']." AND `day` = ".$arr['day'];
				
			} else {
				$sql = "INSERT  INTO `{$this->_dbtabpre}current_coach_time_configuration` ";
				$sql .= " (`current_time`, `time_config_money_id`, `time_config_id`, `time_lisence_config_id`, `time_lesson_config_id`, `coach_id`, `year`, `month`, `day`, `addtime`)";
				$sql .= " VALUES(".$arr['current_time'].", '".$arr['time_config_money_id']."', '".$arr['time_config_id']."', '".$arr['time_lisence_config_id']."', '".$arr['time_lesson_config_id']."', '".$arr['coach_id']."', ".$arr['year'].", ".$arr['month'].", ".$arr['day'].",".time().")";
			}
			$query = $this->_db->query($sql);
			if($query) {
				$data = array('code'=>1, 'data'=>'更新成功');
			} else {
				$data = array('code'=>-1, 'data'=>'更新失败');
			}
			return $data;
		}

		/**
		 * 删除前一天时间数据
		 * @param $arr array 数组
		 * @return bool 
		 */
		public function delPreTime($coach_id) {

			$pre_time = strtotime('-1 day');
			$year = date('Y', $pre_time);
			$month = date('m', $pre_time);
			$day = date('d', $pre_time);

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

			// 查询科目 牌照是否在已设置时间配置

			$sql = "UPDATE `{$this->_dbtabpre}coach` SET ";
			$sql .= " `s_coach_name` = '".$arr['s_coach_name']."', ";
			$sql .= " `s_teach_age` = '".$arr['s_teach_age']."', ";
			$sql .= " `s_coach_sex` = '".$arr['s_coach_sex']."', ";
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
			$sql .= " `s_coach_imgurl` = '".$arr['s_coach_imgurl']."', ";
			$sql .= " `s_coach_original_imgurl` = '".$arr['s_coach_original_imgurl']."' ";
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

			// 判断当前有无教练信息
			$sql = "SELECT * from `{$this->_dbtabpre}coach` WHERE `s_coach_phone` = '".$arr['s_coach_phone']."'";
			$stmt = $this->_getFirstRecord($sql);
			if($stmt) {
				return 1;
			}

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

			$sql = "INSERT INTO `{$this->_dbtabpre}coach` (";
			$sql .= " `s_coach_name`,";
			$sql .= " `s_teach_age`,";
			$sql .= " `s_coach_sex`,";
			$sql .= " `s_coach_phone`,";
			$sql .= " `s_school_name_id`,";
			$sql .= " `s_coach_lesson_id`,";
			$sql .= " `s_coach_lisence_id`,";
			$sql .= " `s_coach_car_id`,";
			$sql .= " `dc_coach_distance_x`,";
			$sql .= " `dc_coach_distance_y`,";
			$sql .= " `i_coach_star`,";
			$sql .= " `s_coach_address`,";
			$sql .= " `province_id`,";
			$sql .= " `city_id`,";
			$sql .= " `area_id`,";
			$sql .= " `order_receive_status`,";
			$sql .= " `i_type`,";
			$sql .= " `s_coach_imgurl`,";
			$sql .= " `s_coach_original_imgurl`,";
			$sql .= " `addtime`";
			$sql .= ") VALUES (";
			$sql .= " '".$arr['s_coach_name']."',";
			$sql .= " '".$arr['s_teach_age']."',";
			$sql .= " '".$arr['s_coach_sex']."',";
			$sql .= " '".$arr['s_coach_phone']."',";
			$sql .= " '".$arr['s_school_name_id']."',";
			$sql .= " '".$arr['s_coach_lesson_id']."',";
			$sql .= " '".$arr['s_coach_lisence_id']."',";
			$sql .= " '".$arr['s_coach_car_id']."',";
			$sql .= " '".$dc_coach_distance_x."',";
			$sql .= " '".$dc_coach_distance_y."',";
			$sql .= " '3', ";
			$sql .= " '".$arr['s_address']."',";
			$sql .= " '".$arr['province_id']."',";
			$sql .= " '".$arr['city_id']."',";
			$sql .= " '".$arr['area_id']."',";
			$sql .= " '".$arr['is_online']."',";
			$sql .= " '".$arr['i_type']."',";
			$sql .= " '".$arr['s_coach_imgurl']."',";
			$sql .= " '".$arr['s_coach_original_imgurl']."',";
			$sql .= " '".time()."')";
			
			$query = $this->_db->query($sql);

			if($query) {
				$id = $this->lastInertId();
				// 插入到user表中
                $sql = " INSERT INTO `{$this->_dbtabpre}user` ( ";
                $sql .= " `coach_id`, ";
                $sql .= " `s_username`, ";
                $sql .= " `s_password`, ";
                $sql .= " `i_user_type`, ";
                $sql .= " `i_status`, ";
                $sql .= " `s_real_name`, ";
                $sql .= " `s_phone`, ";
                $sql .= " `s_imgurl` ";
                $sql .= " ) VALUES ( ";
                $sql .= " {$id}, ";
                $sql .= " '{$arr['s_coach_name']}', ";
                $sql .= " 'e10adc3949ba59abbe56e057f20f883e', ";
                $sql .= " 1, ";
                $sql .= " 0, ";
                $sql .= " '{$arr['s_coach_name']}', ";
                $sql .= " '{$arr['s_coach_phone']}', ";
                $sql .= " '{$arr['s_coach_imgurl']}' ";
                $sql .= " ) ";
                $query = $this->_db->query($sql);
                /*$uid = $this->lastInertId();
                $fields = array(
                    'user_id' => $uid,
                );
                $whereFields = array(
                    'l_coach_id' => $id,
                );
                $query = $this->_updateRecord($fields, $whereFields, 'coach');
                return $query;*/
                if ($query) {
                    $uid = $this->lastInertId();
                    // 插入到users_info表中
                    $fields = array(
                        'user_id' => $uid,
                    );
                    $query = $this->_insertRecord($fields, 'users_info');
                    return $query;

                } else {
                    return false;
                }
				/*$fields = array(
					'coach_id'=>$id,
					's_username'=>$arr['s_coach_name'],
 					's_password'=>md5('123456'),
					'i_user_type'=>1,
					'i_status'=>0,
					's_real_name'=>$arr['s_coach_name'],
					's_phone'=>$arr['s_coach_phone'],
					's_imgurl'=>$arr['s_coach_imgurl']
					);
				$query = $this->_insertRecord($fields, 'user');
                if ($query) {
                    $uid = $this->lastInertId();
                    // 将信息插入到users_info中
                    $field = array(
                        'user_id' => $uid,
                        );
                    $query = $this->_insertRecord($field, 'users_info');
				    return $query;
                } else {
                    return false;
                }*/
			} else {
				return false;
			}
		}

		// 删除教练
		public function deleteCoach($id) {
			$sql = "DELETE FROM `{$this->_dbtabpre}coach` WHERE `l_coach_id` IN ($id)";
			$query = $this->_db->query($sql);
			if($query) {
				$sql = "DELETE FROM `{$this->_dbtabpre}user` WHERE `coach_id` IN ($id)";
				$res = $this->_db->query($sql);
				return $res;
			} else {
				return false;
			}
		}

        /**
         * 获取当前教练时间配置以及被预约信息
         * @param $school_id int 驾校ID
         * @param $id int 教练ID
         * @param $date string 日期
         * @return array
         * @author cx
         **/
		public function getCoachCurrentTimeConfig($school_id, $id, $date) {

			Global $lesson_config, $lisence_config;
			$list = array();
			$date_config = $this->getCoachDateTimeConfig();
			$list['date_time'] = $date_config;

			$date_format = array();
			$date_format = explode('-', $date);
			$year =	$date_format[0];
			$month = $date_format[1];
			$day = $date_format[2];

			// 获取驾校的时间配置
			$sql = "SELECT `s_time_list`, `is_automatic` FROM `{$this->_dbtabpre}school_config` WHERE `l_school_id` = '{$school_id}'";
			$school_config = $this->_getFirstRecord($sql);
			$s_time_list = array();
			$is_automatic = 1;
			if($school_config) {
				$s_time_list = array_filter(explode(',', $school_config['s_time_list']));
				$is_automatic = $school_config['is_automatic'];
			}

			// 获取教练时间配置
			$sql = "SELECT `s_am_subject`, `s_pm_subject`, `s_am_time_list`, `s_pm_time_list`, `s_coach_lisence_id`, `s_coach_lesson_id` FROM `{$this->_dbtabpre}coach` WHERE `l_coach_id` = '{$id}'";
			$coach_info = $this->_getFirstRecord($sql);
			$s_am_subject = 2;
			$s_pm_subject = 3;
			$s_am_time_list = array();
			$s_pm_time_list = array();
			$s_coach_lisence_id_list = array();
			$s_coach_lesson_id_list = array();
			if($coach_info) {
				$s_am_subject = $coach_info['s_am_subject'];
				$s_pm_subject = $coach_info['s_pm_subject'];
				$s_am_time_list = isset($coach_info['s_am_time_list']) ? array_filter(explode(',', $coach_info['s_am_time_list'])) : array(); 
				$s_pm_time_list = isset($coach_info['s_pm_time_list']) ? array_filter(explode(',', $coach_info['s_pm_time_list'])) : array();
				$s_coach_lisence_id_list = isset($coach_info['s_coach_lisence_id']) ? array_filter(explode(',', $coach_info['s_coach_lisence_id'])) : array();
				$s_coach_lesson_id_list = isset($coach_info['s_coach_lesson_id']) ? array_filter(explode(',', $coach_info['s_coach_lesson_id'])) : array();
			}
            
			if(!empty($s_am_time_list) && !empty($s_pm_time_list)) {
		 		$time_config_ids_arr = array_merge($s_am_time_list, $s_pm_time_list);
		 	}  else {
		 		$time_config_ids_arr = $s_time_list;
		 	}

			$sql = "SELECT o.`time_config_id` FROM `{$this->_dbtabpre}study_orders` as o LEFT JOIN `{$this->_dbtabpre}coach_appoint_time` as a ON a.`id` = o.`appoint_time_id` WHERE a.`id` != '' AND a.`coach_id` = $id AND o.`i_status` != 3 AND o.`i_status` != 101 AND a.`year` = '{$year}' AND a.`month` = '{$month}' AND a.`day` = '{$day}'";
			$is_appoint = $this->_getAllRecords($sql);
			$time_config_ids = array();
			$time_config_id_arr = array();
			if($is_appoint) {
				foreach ($is_appoint as $k => $v) {
					$time_config_ids = array_filter(explode(',', $v['time_config_id']));
					foreach ($time_config_ids as $e => $t) {
						$time_config_id_arr[] = $t;
					}
				}
			}

			// 获取当前教练所设置的时间端配置
			$sql = "SELECT * FROM `cs_current_coach_time_configuration` WHERE `coach_id` = $id AND `year` = '{$year}' AND `month` = '{$month}' AND `day` = '{$day}'";
			$current_time_config = $this->_getFirstRecord($sql);

			$sql = "SELECT * FROM `cs_coach_time_config` WHERE `status` = 1 AND `school_id` = 1";
			if(!empty($time_config_ids_arr) && empty($current_time_config)) {
				$sql .= " AND `id` IN (".implode(',', $time_config_ids_arr).")";
				$sql .= " ORDER BY `start_time` DESC";
			}

			$coach_time_config = $this->_getAllRecords($sql);		
			$time_config_id = array();
			$time_lisence_config_id = array();
			$time_lesson_config_id = array();
			$time_config_money_id = array();
			$_coach_time_config = array();

			// print_r($current_time_config);
			// print_r($coach_time_config);

			if($current_time_config) {
				$time_config_id = explode(',', $current_time_config['time_config_id']);
				$time_lisence_config_id = json_decode($current_time_config['time_lisence_config_id'], true);
				$time_lesson_config_id = json_decode($current_time_config['time_lesson_config_id'], true);
				$time_config_money_id = json_decode($current_time_config['time_config_money_id'], true);
			}

			foreach ($coach_time_config as $key => $value) {
				$_coach_time_config[$key]['id'] = $value['id'];
				$_coach_time_config[$key]['license_no'] = $value['license_no'];
				$_coach_time_config[$key]['status'] = $value['status'];
				$_coach_time_config[$key]['start_minute'] = $value['start_minute'];
				$_coach_time_config[$key]['end_minute'] = $value['end_minute'];
				$_coach_time_config[$key]['start_time'] = $value['start_time'];
				$_coach_time_config[$key]['end_time'] = $value['end_time'];
				$_coach_time_config[$key]['price'] = $value['price'];
				$_coach_time_config[$key]['subjects'] = $value['subjects'];
               
				if(count($s_coach_lisence_id_list) == 1 && is_array($s_coach_lisence_id_list)) {
					$_coach_time_config[$key]['license_no'] = isset($lisence_config[$s_coach_lisence_id_list[0]]) ? $lisence_config[$s_coach_lisence_id_list[0]] : 'C1';
				}
				if(count($s_coach_lesson_id_list) == 1 && is_array($s_coach_lesson_id_list)) {
					$_coach_time_config[$key]['subjects'] = isset($lesson_config[$s_coach_lesson_id_list[0]]) ? $lesson_config[$s_coach_lesson_id_list[0]] : '科目二';
				}

				// 判断有没有设置时间配置
				if($current_time_config) {
					if(in_array($value['id'], $time_config_id)) {
						$_coach_time_config[$key]['is_set'] = 1; // 设置
						$_coach_time_config[$key]['price'] = $time_config_money_id[$value['id']];
						$_coach_time_config[$key]['license_no'] = $time_lisence_config_id[$value['id']];
						$_coach_time_config[$key]['subjects'] = $time_lesson_config_id[$value['id']];
					} else {
						$_coach_time_config[$key]['is_set'] = 2; // 未设置
						$_coach_time_config[$key]['subjects'] = $value['subjects'];
					}
					$_coach_time_config[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);

					if(!empty($time_config_id_arr)) {
						if(in_array($value['id'], $time_config_id_arr)) {
							$_coach_time_config[$key]['is_appointed'] = 1; //被预约
						} else {
							$_coach_time_config[$key]['is_appointed'] = 2; //没被预约
						}
					} else {
						$_coach_time_config[$key]['is_appointed'] = 2; //没被预约

					}

				} else {

					// 如果教练设置了上午时间和下午时间
					if(!empty($s_am_time_list) && !empty($s_pm_time_list)) {
						if(in_array($value['id'], $s_am_time_list)) {
							if($s_am_subject == 1) {
								$_coach_time_config[$key]['subjects'] = '科目一';

							} else if($s_am_subject == 2) {
								$_coach_time_config[$key]['subjects'] = '科目二';

							} else if($s_am_subject == 3) {
								$_coach_time_config[$key]['subjects'] = '科目三';

							} else if($s_am_subject == 4) {
								$_coach_time_config[$key]['subjects'] = '科目四';

							}
						}

						if(in_array($value['id'], $s_pm_time_list)) {

							if($s_pm_subject == 1) {
								$_coach_time_config[$key]['subjects'] = '科目一';

							} else if($s_pm_subject == 2) {
								$_coach_time_config[$key]['subjects'] = '科目二';

							} else if($s_pm_subject == 3) {
								$_coach_time_config[$key]['subjects'] = '科目三';

							} else if($s_pm_subject == 4) {
								$_coach_time_config[$key]['subjects'] = '科目四';

							}
						}

					// 教练没有设置，驾校有设置
					} else {
						if($value['end_time'] <= 12) {
							if($s_am_subject == 1) {
								$_coach_time_config[$key]['subjects'] = '科目一';

							} else if($s_am_subject == 2) {
								$_coach_time_config[$key]['subjects'] = '科目二';

							} else if($s_am_subject == 3) {
								$_coach_time_config[$key]['subjects'] = '科目三';

							} else if($s_am_subject == 4) {
								$_coach_time_config[$key]['subjects'] = '科目四';

							}
						} else {

							if($s_pm_subject == 1) {
								$_coach_time_config[$key]['subjects'] = '科目一';

							} else if($s_pm_subject == 2) {
								$_coach_time_config[$key]['subjects'] = '科目二';

							} else if($s_pm_subject == 3) {
								$_coach_time_config[$key]['subjects'] = '科目三';

							} else if($s_pm_subject == 4) {
								$_coach_time_config[$key]['subjects'] = '科目四';

							}
						}
					}
					$_coach_time_config[$key]['is_set'] = 1;

					if(!empty($time_config_id_arr)) {
						if(in_array($value['id'], $time_config_id_arr)) {
							$_coach_time_config[$key]['is_appointed'] = 1; //被预约
						} else {
							$_coach_time_config[$key]['is_appointed'] = 2; //没被预约
						}
					} else {
						$_coach_time_config[$key]['is_appointed'] = 2; //没被预约

					}
				}
					
			}

			// print_r($_coach_time_config);

			$am_list = array();
			$pm_list = array();
			foreach ($_coach_time_config as $key => $value) {
				$start_minute = $value['start_minute'] == 0 ? '00' : $value['start_minute'];
				$end_minute = $value['end_minute'] == 0 ? '00' : $value['end_minute'];
				$row[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
				$row[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;

				if(!empty($s_am_time_list) && !empty($s_pm_time_list)) {
					if(in_array($value['id'], $s_am_time_list)) {
						$am_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
						$am_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
						$am_list[$key]['id'] = $value['id'];
						$am_list[$key]['license_no'] = $value['license_no'];
						$am_list[$key]['subjects'] = $value['subjects'];
						$am_list[$key]['price'] = $value['price'];
						$am_list[$key]['status'] = $value['status'];
						$am_list[$key]['is_appointed'] = $value['is_appointed'];
						$am_list[$key]['is_set'] = $value['is_set'];

					}

					if(in_array($value['id'], $s_pm_time_list)) {

						$pm_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
						$pm_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
						$pm_list[$key]['id'] = $value['id'];
						$pm_list[$key]['license_no'] = $value['license_no'];
						$pm_list[$key]['subjects'] = $value['subjects'];
						$pm_list[$key]['price'] = $value['price'];
						$pm_list[$key]['status'] = $value['status'];
						$pm_list[$key]['is_appointed'] = $value['is_appointed'];
						$pm_list[$key]['is_set'] = $value['is_set'];

					}
				} else {

					if($value['end_time'] <= 12) {
						$am_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
						$am_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
						$am_list[$key]['id'] = $value['id'];
						$am_list[$key]['license_no'] = $value['license_no'];
						$am_list[$key]['subjects'] = $value['subjects'];
						$am_list[$key]['price'] = $value['price'];
						$am_list[$key]['status'] = $value['status'];
						$am_list[$key]['is_appointed'] = $value['is_appointed'];
						$am_list[$key]['is_set'] = $value['is_set'];

					} else {
						$pm_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
						$pm_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
						$pm_list[$key]['id'] = $value['id'];
						$pm_list[$key]['license_no'] = $value['license_no'];
						$pm_list[$key]['subjects'] = $value['subjects'];
						$pm_list[$key]['price'] = $value['price'];
						$pm_list[$key]['status'] = $value['status'];
						$pm_list[$key]['is_appointed'] = $value['is_appointed'];
						$pm_list[$key]['is_set'] = $value['is_set'];

					}
				}
			}

			// print_r($am_list);
			// print_r($pm_list);
			$list['time_list'] = $coach_time_config; 
			$list['am_time_list'] = $am_list; 
			$list['pm_time_list'] = $pm_list; 
			return $list;
		}

		/**
		 * 获取从今天开始的一个星期的月份
		 *
		 * @return array
		 * @author cx
		 **/
		public function getCoachDateTimeConfig() {
			$current_time = time();
			$year = date('Y', $current_time); //年
			$month = intval(date('m', $current_time)); //月
			$day = intval(date('d', $current_time)); //日

			// 构建一个时间
			$build_date_timestamp = mktime(0,0,0,$month,$day,$year);

			// 循环7天日期
			$date_config = array();
			for($i = 0; $i <= 7; $i++) {
				// $date_config['date'][] = date('m-d', strtotime("+".($i)." day")); //或者这种算法
				$date_config[$i]['fulldate'] = date('Y-m-d', $build_date_timestamp + ( 24 * 3600 * $i));	
				$date_config[$i]['date'] = date('m-d', $build_date_timestamp + ( 24 * 3600 * $i));	
				$date_config[$i]['month'] = intval(date('m', $build_date_timestamp + ( 24 * 3600 * $i)));	
				$date_config[$i]['day'] = intval(date('d', $build_date_timestamp + ( 24 * 3600 * $i)));	
				$date_config[$i]['date_format'] = intval(date('m', $build_date_timestamp + ( 24 * 3600 * $i))).'-'.intval(date('d', $build_date_timestamp + ( 24 * 3600 * $i)));
			}
			return $date_config;
		}

		/**
		 * 获取当前教练的时间配置ID
		 * @param $date string 日期
		 * @param $id int 驾校ID 
		 * @return array
		 * @author chenxi
		 **/

		public function getCurrentCoachTimeConfigIdByDate($date, $id, $school_id) {
			if(!preg_match('|-|', $date)) {
				return 1;
			}
			$date_arr = explode('-', $date);
			$year = $date_arr[0];
			$month = $date_arr[1];
			$day = $date_arr[2];

			$time_config_arr = array();
			$sql = "SELECT `time_config_id`, `time_config_money_id`, `time_lisence_config_id` FROM `{$this->_dbtabpre}current_coach_time_configuration` WHERE `coach_id` = '".$id."' AND `year` = '".$year."' AND `month` = '".$month."' AND `day` = '".$day."'";
			$current_time_list = $this->_getFirstRecord($sql);
			$list = array();
			if($current_time_list) {
				$time_config_arr = explode(',', $current_time_list['time_config_id']);
				$time_money_config_arr = json_decode($current_time_list['time_config_money_id'], true);
				$time_lisence_config_arr = explode(',', $current_time_list['time_lisence_config_id']);
                $list['setting'] = $time_config_arr;
                $list['price'] = isset($time_money_config_arr) ? $time_money_config_arr : array();
			} else {
                $date_config = $this->getCoachTimeConfig($school_id, $id);
                $am_list = $date_config['am_time'];
                $pm_list = $date_config['pm_time'];
                $price = array();
                foreach ($am_list as $key => $value) {
                    $price[$value['id']] = $value['price'];
                }
                foreach ($pm_list as $key => $value) {
                    $price[$value['id']] = $value['price'];
                }
                $list['price'] = $price ? $price : array();
				$list['setting'] = array();
				// return 2;
			}
			$sql = "SELECT * FROM `{$this->_dbtabpre}study_orders` as o LEFT JOIN `{$this->_dbtabpre}coach_appoint_time` as a ON a.`id` = o.`appoint_time_id` WHERE a.`id` != '' AND a.`coach_id` = $id AND o.`i_status` != 3 AND o.`i_status` != 101 AND a.`year` = '{$year}' AND a.`month` = '{$month}' AND a.`day` = '{$day}'";
			$is_appoint = $this->_getAllRecords($sql);
			$time_config_ids = array();
			$time_config_id_arr = array();
			if($is_appoint) {
				foreach ($is_appoint as $k => $v) {
					$time_config_ids = array_filter(explode(',', $v['time_config_id']));
					foreach ($time_config_ids as $e => $t) {
						$time_config_id_arr[] = $t;
					}
				}
			}
			$list['appointing'] = $time_config_id_arr;
            // var_dump($list);exit;
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


		// /**
		//   * 设置时间配置
		//   **/
		// public function setTimeConfig($arr) {
		// 	// $sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config` WHERE `school_id` = ".$arr['school_id'];
		// 	// $stmt = $this->_getAllRecords($sql);
		// 	foreach ($arr as $key => $value) {
		// 		$sql = "INSERT INTO `{$this->_dbtabpre}coach_time_config` (`start_time`, `end_time`, `license_no`, `subjects`, `price`, `school_id`, `addtime`) VALUES(";
		// 		$sql .= "'".$value['start_time']."'";
		// 	}
				
		// } 

		/**
		 * 获取教练详情中学员列表
		 *
		 * @return array
		 * @author dalishuishou
		 **/
		public function getStudentList($page='', $limit='', $id) {
			if (!$id) {
				return array();
			}
			if ($page !== '' && $limit !== '') {
			$sql = "SELECT `l_user_id`, `s_user_phone`, `s_lisence_name` FROM `{$this->_dbtabpre}study_orders` WHERE `l_coach_id` = '".$id."' GROUP BY `l_user_id` LIMIT $page, $limit";
			} else {
				$sql = "SELECT `l_user_id`, `s_user_phone`, `s_lisence_name` FROM `{$this->_dbtabpre}study_orders` WHERE `l_coach_id` = '".$id."' GROUP BY `l_user_id`";
			}
			$study_info = $this->_getAllRecords($sql);
			$list = array();
			if ($study_info) {
				foreach ($study_info as $key => $value) {
					$list[$key]['s_user_phone'] = $value['s_user_phone'];
					$list[$key]['s_lisence_name'] = $value['s_lisence_name'];
					$list[$key]['l_user_id'] = $value['l_user_id'];

					$user_id = $value['l_user_id'];
					$sql = "SELECT `s_real_name` FROM `{$this->_dbtabpre}user` WHERE `l_user_id` = '".$user_id."'";
					$realname = $this->_getFirstRecord($sql);
					if ($realname) {
						$list[$key]['s_real_name'] = $realname['s_real_name'];
					} else {
						$list[$key]['s_real_name'] = '';      //如果不存在，在模板中处理，当为空时赋”嘻哈学员“的值
					}
					$sql = "SELECT * FROM `{$this->_dbtabpre}users_info` WHERE `user_id` = '".$user_id."'";
					$student_info = $this->_getFirstRecord($sql);
					if ($student_info) {
						$list[$key]['sex'] 				= $student_info['sex'];
						$list[$key]['age'] 				= $student_info['age'];
						$list[$key]['identity_id'] 		= $student_info['identity_id'];
						$list[$key]['address'] 			= $student_info['address'];
						$list[$key]['learncar_status'] 	= $student_info['learncar_status'];
						$school_id = $student_info['school_id'];
						if ($school_id) {
							$sql = "SELECT `s_school_name` FROM `{$this->_dbtabpre}school` WHERE `l_school_id` = '".$school_id."'";
							$school_name = $this->_getFirstRecord($sql);
							if ($school_name) {
								$list[$key]['school_name'] = $school_name['s_school_name'];
							} else {
								$list[$key]['school_name'] = '';
							}
						} else {
							$list[$key]['school_name'] = '';   //如果不存在，在模板中处理，当为空时赋”嘻哈驾校“的值
						}

					} else {
						$list[$key]['sex'] 				= '';
						$list[$key]['age'] 				= '';
						$list[$key]['identity_id']		= '';
						$list[$key]['address'] 			= '';
						$list[$key]['learncar_status'] 	= '';
						$list[$key]['school_name'] 		= '';
					}
				}
			} 
		    return $list;
		}
		/**
		 * 教练详情中学员列表搜索 
		 *
		 * @return array
		 * @author 大力水手
		 **/
		public function getSearchStuList($page='', $limit='', $coach_id, $conditiontype) {
			if (!$coach_id) {
				return array();
			}
			$arr = array(
				'1'=>'科目一学习中',	
				'2'=>'科目二学习中',	
				'3'=>'科目三学习中',	
				'4'=>'科目四学习中',	
				'5'=>'已领证'
			);
			$learncar_status = isset($arr[$conditiontype]) ? $arr[$conditiontype] : $arr['1'];
			$sql = "SELECT  s.`l_user_id`, s.`s_user_phone`, s.`s_lisence_name` FROM `{$this->_dbtabpre}study_orders` as s LEFT JOIN `{$this->_dbtabpre}users_info` as u ON u.`user_id` = s.`l_user_id` WHERE s.`l_coach_id` = '".$coach_id."'";
			$sql .= " AND u.`learncar_status` LIKE '%{$learncar_status}%'";
			$sql .= " GROUP BY s.`l_user_id`";
			if($page !== '' && $limit !== '') {
				$sql .= " LIMIT $page, $limit";
			} else {
				$sql .= "";
			}

			$study_info = $this->_getAllRecords($sql);
			$list = array();
			if ($study_info) {
				foreach ($study_info as $key => $value) {
					$list[$key]['s_user_phone'] = $value['s_user_phone'];
					$list[$key]['s_lisence_name'] = $value['s_lisence_name'];
					$list[$key]['l_user_id'] = $value['l_user_id'];
					$user_id = $value['l_user_id'];
					$sql = "SELECT `s_real_name` FROM `{$this->_dbtabpre}user` WHERE `l_user_id` = '".$user_id."'";
					$realname = $this->_getFirstRecord($sql);
					if ($realname) {
						$list[$key]['s_real_name'] = $realname['s_real_name'];
					} else {
						$list[$key]['s_real_name'] = '';      //如果不存在，在模板中处理，当为空时赋”嘻哈学员“的值
					}
					$sql = "SELECT * FROM `{$this->_dbtabpre}users_info` WHERE `user_id` = '".$user_id."'";
					$student_info = $this->_getFirstRecord($sql);
					if ($student_info) {
						$list[$key]['sex'] 				= $student_info['sex'];
						$list[$key]['age'] 				= $student_info['age'];
						$list[$key]['identity_id'] 		= $student_info['identity_id'];
						$list[$key]['address'] 			= $student_info['address'];
						$list[$key]['learncar_status'] 	= $student_info['learncar_status'];
						$school_id = $student_info['school_id'];
						if ($school_id) {
							$sql = "SELECT `s_school_name` FROM `{$this->_dbtabpre}school` WHERE `l_school_id` = '".$school_id."'";
							$school_name = $this->_getFirstRecord($sql);
							if ($school_name) {
								$list[$key]['school_name'] = $school_name['s_school_name'];
							} else {
								$list[$key]['school_name'] = '';
							}
						} else {
							$list[$key]['school_name'] = '';   //如果不存在，在模板中处理，当为空时赋”嘻哈驾校“的值
						}

					} else {
						$list[$key]['sex'] 				= '';
						$list[$key]['age'] 				= '';
						$list[$key]['identity_id']		= '';
						$list[$key]['address'] 			= '';
						$list[$key]['learncar_status'] 	= '';
						$list[$key]['school_name'] 		= '';
					}
				}
			} 
		    return $list;
		}	
		
		/**
		 * 获取搜索教练列表
		 * @param $page int 页码
		 * @param $limit int 限制每页显示数量
		 * @param $school_id int 驾校ID
		 * @param $conditiontype int 搜索类型
		 * @param $keywords int 关键词
		 * @return array
		 * @author cx
		 **/
		public function getSearchCoachlist($page='', $limit='', $school_id, $conditiontype=1, $type="default", $order="desc", $keywords='') {

			Global $lisence_config, $lesson_config;
			$order = strtolower($order) == 'desc' ? 'DESC' : 'ASC';

			if(!$school_id) {
				return array();
			}
			
			$sql = "SELECT * FROM {$this->_dbtabpre}coach WHERE `s_school_name_id` = $school_id";
			switch ($conditiontype) {
				case '1':
					$sql .= " AND `l_coach_id` = '{$keywords}'";
					break;
				case '2':
					$sql .= " AND `s_coach_name` LIKE '%{$keywords}%'";
					break;
				case '3':
					$sql .= " AND `s_coach_phone` LIKE '%{$keywords}%'";
					break;
				default:
					$sql .= " AND `l_coach_id` = '{$keywords}'";
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

		// 教练时间段设置
		public function updateCoachTimeLimit($arr) {
			$sql = "UPDATE `cs_coach` SET ";
			$sql .= "`s_am_time_list` = '{$arr['am_time_config_id']}',";
			$sql .= "`s_pm_time_list` = '{$arr['pm_time_config_id']}',";
			$sql .= "`s_am_subject` = '{$arr['morning_subjects']}',";
			$sql .= "`s_pm_subject` = '{$arr['afternoon_subjects']}'";
			$sql .= " WHERE `l_coach_id` = '{$arr['coach_id']}'";
			$res = $this->_db->query($sql);
			return $res;
		}

		// 获取所有教练
		public function getSysCoachlist($school_id) {
			$sql = "SELECT c.`l_coach_id`, c.`s_coach_name`, a.`name`, a.`car_type`, a.`car_no` FROM `{$this->_dbtabpre}coach` as c LEFT JOIN `{$this->_dbtabpre}cars` as a ON a.`id` = c.`s_coach_car_id` WHERE c.`s_school_name_id` = '{$school_id}' ORDER BY c.`l_coach_id` DESC";
			$coach_list = $this->_getAllRecords($sql);
			if($coach_list) {
				foreach ($coach_list as $key => $value) {
					if($value['car_type'] == 1) {
						$coach_list[$key]['car_type_name'] = '普通车型';

					} else if($value['car_type'] == 2) {
						$coach_list[$key]['car_type_name'] = '加强车型';

					} else if($value['car_type'] == 3) {
						$coach_list[$key]['car_type_name'] = '模拟车型';
						
					} else {
						$coach_list[$key]['car_type_name'] = '普通车型';

					}
				}
			}
			return $coach_list;
		}

		// 获取上午下午时间配置
		public function getAnPmTimeConfig() {
			$sql = "SELECT * FROM `{$this->_dbtabpre}coach_time_config` WHERE `status` = 1 ORDER BY `start_time` ASC";
			$time_list = $this->_getAllRecords($sql);
			$list = array();
			if($time_list) {
				foreach ($time_list as $key => $value) {
					$start_minute = $value['start_minute'] == 0 ? '00' : $value['start_minute'];
					$end_minute = $value['end_minute'] == 0 ? '00' : $value['end_minute'];
					$value['final_start_time'] = $value['start_time'].':'.$start_minute;
					$value['final_end_time'] = $value['end_time'].':'.$end_minute;

					if($value['end_time'] <= 12) {
						$list['am_time_list'][$key] = $value;
					} else {
						$list['pm_time_list'][$key] = $value;
					}
				}
			}
			return $list;
		}

	}
?>
