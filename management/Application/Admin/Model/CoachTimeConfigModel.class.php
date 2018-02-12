<?php 
namespace Admin\Model;
use Think\Model;
use Think\Page;

class CoachTimeConfigModel extends BaseModel {
// 1.教练时间模板的设置
	//获取上午下午时间配置
	// public function getCoachTimeConfig() {
	// 	$time_list = $this->table(C('DB_PREFIX').'coach_time_config')
	// 		->where(array('status'=>1))
	// 		->order('start_time asc')
	// 		->fetchSql(false)
	// 		->select();
	// 	$list = array();
	// 	if ($time_list) {
	// 		foreach ($time_list as $key => $value) {
	// 			$start_minute = $value['start_minute'] == 0 ? '00' : $value['start_minute'];
	// 			$end_minute = $value['end_minute'] == 0 ? '00' : $value['end_minute'];
	// 			$value['final_start_time'] = $value['start_time'].':'.$start_minute;
	// 			$value['final_end_time'] = $value['end_time'].':'.$end_minute;

	// 			if($value['end_time'] <= 12) {
	// 				$list['am_time_list'][$key] = $value;
	// 			} else {
	// 				$list['pm_time_list'][$key] = $value;
	// 			}
	// 		}
	// 		return $list;		
	// 	}
	// }

	// 获取教练上午下午时间配置
	public function getCoachAmPmConfig ($coach_id) {
		$coach_time_list = $this->table(C('DB_PREFIX').'coach')
			->where(array('l_coach_id' => $coach_id))
			->field('s_am_subject, s_pm_subject, s_am_time_list, s_pm_time_list')
			->find();
		$time_list = $this->table(C('DB_PREFIX').'coach_time_config')
			->where(array('status'=>1))
			->order('start_time asc')
			->fetchSql(false)
			->select();
		$list = array();
		if ($time_list) {
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
			if (!empty($coach_time_list)) {
				$coach_time_list['s_am_time_list'] = explode(',', $coach_time_list['s_am_time_list']);
				$coach_time_list['s_pm_time_list'] = explode(',', $coach_time_list['s_pm_time_list']);
				foreach ($list['am_time_list'] as $am_time_list_index => $am_time_list_value) {
					if (in_array($am_time_list_value['id'], $coach_time_list['s_am_time_list'])) {
						$list['am_time_list'][$am_time_list_index]['is_set'] = 1; // 此时间段已经设置
					} else {
						$list['am_time_list'][$am_time_list_index]['is_set'] = 2; // 此时间段未设置
					}
				}

				foreach ($list['pm_time_list'] as $pm_time_list_index => $pm_time_list_value) {
					if (in_array($pm_time_list_value['id'], $coach_time_list['s_pm_time_list'])) {
						$list['pm_time_list'][$pm_time_list_index]['is_set'] = 1; // 此时间段已经设置
					} else {
						$list['pm_time_list'][$pm_time_list_index]['is_set'] = 2; // 此时间段未设置
					}
				}
				$list['am_subject'] = $coach_time_list['s_am_subject'];
				$list['pm_subject'] = $coach_time_list['s_pm_subject'];
			} else {
				$list['am_time_list'][$am_time_list_index]['is_set'] = 2; // 此时间段未设置
				$list['pm_time_list'][$pm_time_list_index]['is_set'] = 2; // 此时间段未设置
				$list['am_subject'] = '';
				$list['pm_subject'] = '';
			}
		}
		return $list;
	}

	// 教练时间段设置
    public function updateCoachTime ($post) {
        if (!$post) {
            return false;
        } 
        $coach = D('coach');
        if ($res = $coach->create($post)) {
        	$result = $coach->where(array('l_coach_id' => $post['l_coach_id']))
        		->save($res);
        	if ($result) {
        		return $result;
        	} else {
        		return false;
        	}
        } else {
    		return false;
    	}
    }

// 2.教练最终时间配置(旧表)
    /**
     * 获取教练的时间配置情况(同时获取可变的日期配置)
     *
     * @return 	void
     * @author 	wl
     * @date	Jan 12, 2017
     **/
    public function getCoachTimeConfig ($school_id, $coach_id = 0) {
    	$date_config = array();
    	$current_time = time();
    	$year = date('Y', $current_time);
    	$month = date('m', $current_time);
    	$day = date('d', $current_time);

    	// 构建一个时间戳
    	$build_date_timestamp = mktime(0, 0, 0, $month, $day, $year);

    	// 循环30天日期
    	for ($i = 0; $i <= 30; $i++) {
    		$date_config['date_time'][$i]['fulldate'] = date('Y-m-d', $build_date_timestamp + (24 * 3600 * $i));
    		$date_config['date_time'][$i]['date'] = date('m-d', $build_date_timestamp + (24 * 3600 * $i));
    	}
    	// 获取驾校的时间配置
    	$school_config = $this->table(C('DB_PREFIX').'school_config')
    		->where('l_school_id = :school_id')
    		->bind(['school_id' => $school_id])
    		->field('s_time_list, is_automatic')
    		->find();
		$s_time_list = array();
		$is_automatic = 1;
		if (!empty($school_config)) {
			$s_time_list = array_filter(explode(',', $school_config['s_time_list']));
			$is_automatic = $school_config['is_automatic'];
		}

		// 获取教练的时间配置
		$coach_info = $this->table(C('DB_PREFIX').'coach')
			->where('l_coach_id = :coach_id')
			->bind(['coach_id' => $coach_id])
			->field('s_am_subject, s_pm_subject, s_am_time_list, s_pm_time_list')
			->find();
		$s_am_subject = 2;
		$s_pm_subject = 3;
		$s_am_time_list = array();
		$s_pm_time_list = array();
		if (!empty($coach_info)) {
			$s_am_subject = $coach_info['s_am_subject'] !='' ? $coach_info['s_am_subject'] : "2";
			$s_pm_subject = $coach_info['s_pm_subject'] !='' ? $coach_info['s_pm_subject'] : "3";
			$s_am_time_list = isset($coach_info['s_am_time_list']) ? array_filter(explode(',', $coach_info['s_am_time_list'])) : array();
			$s_pm_time_list = isset($coach_info['s_pm_time_list']) ? array_filter(explode(',', $coach_info['s_pm_time_list'])) : array();
		}

		if (!empty($s_am_time_list) && !empty($s_pm_time_list)) {
			$time_config_ids_arr = array_merge($s_am_time_list, $s_pm_time_list);
		} else {
			$time_config_ids_arr = $s_time_list;
		}

		// 获取当前时间配置表中的相关信息
		$time_config_ids_arr = array_filter($time_config_ids_arr);
		if (!empty($time_config_ids_arr)) {
			$condition = array(
				'id' => array('in', implode(',', $time_config_ids_arr)),
				'status' => 1,
			);
			$coach_time_config = $this->table(C('DB_PREFIX').'coach_time_config')
				->where($condition)
				->order('start_time ASC')
				->select();
		} else {
			$coach_time_config = $this->table(C('DB_PREFIX').'coach_time_config')
				->where(array('status' => 1))
				->select();
		}

		if (!empty($coach_time_config)) {
			foreach ($coach_time_config as $key => $value) {
				$start_minute = $value['start_minute'] == 0 ? '00' : $value['start_minute'];
				$end_minute = $value['end_minute'] == 0 ? '00' : $value['end_minute'];
				// $coach_time_config[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
				// $coach_time_config[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;

				// 上午下午都不为空
				if (!empty($s_am_time_list) && !empty($s_pm_time_list)) {
					// 上午
					if (in_array($value['id'], $s_am_time_list)) {
						$am_list[$key]['id'] = $value['id'];
						$am_list[$key]['price'] = $value['price'];
						$am_list[$key]['status'] = $value['status'];
						$am_list[$key]['license_no'] = $value['license_no'];
						$am_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
						$am_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;

						if ($s_am_subject == 1) {
							$am_list[$key]['subjects'] = '科目一';

 						} elseif ($s_am_subject == 2) {
							$am_list[$key]['subjects'] = '科目二';

 						} elseif ($s_am_subject == 3) {
							$am_list[$key]['subjects'] = '科目三';

 						} elseif ($s_am_subject == 4) {
							$am_list[$key]['subjects'] = '科目四';

 						}
					}

					// 下午
					if (in_array($value['id'], $s_pm_time_list)) {
						$pm_list[$key]['id'] = $value['id'];
						$pm_list[$key]['price'] = $value['price'];
						$pm_list[$key]['status'] = $value['status'];
						$pm_list[$key]['license_no'] = $value['license_no'];
						$pm_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
						$pm_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
						if ($s_pm_subject == 1) {
							$pm_list[$key]['subjects'] = '科目一';

 						} elseif ($s_pm_subject == 2) {
							$pm_list[$key]['subjects'] = '科目二';

 						} elseif ($s_pm_subject == 3) {
							$pm_list[$key]['subjects'] = '科目三';

 						} elseif ($s_pm_subject == 4) {
							$pm_list[$key]['subjects'] = '科目四';
 						}
					}

				} else {
					// 上午下午都为空
					if ($value['end_time'] <= 12) {
						// 上午
						$am_list[$key]['id'] = $value['id'];
						$am_list[$key]['price'] = $value['price'];
						$am_list[$key]['status'] = $value['status'];
						$am_list[$key]['license_no'] = $value['license_no'];
						$am_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
						$am_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;

						if ($s_am_subject == 1) {
							$am_list[$key]['subjects'] = '科目一';

 						} elseif ($s_am_subject == 2) {
							$am_list[$key]['subjects'] = '科目二';

 						} elseif ($s_am_subject == 3) {
							$am_list[$key]['subjects'] = '科目三';

 						} elseif ($s_am_subject == 4) {
							$am_list[$key]['subjects'] = '科目四';

 						}
					} else {
						// 下午
						$pm_list[$key]['id'] = $value['id'];
						$pm_list[$key]['price'] = $value['price'];
						$pm_list[$key]['status'] = $value['status'];
						$pm_list[$key]['license_no'] = $value['license_no'];
						$pm_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
						$pm_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
						if ($s_pm_subject == 1) {
							$pm_list[$key]['subjects'] = '科目一';

 						} elseif ($s_pm_subject == 2) {
							$pm_list[$key]['subjects'] = '科目二';

 						} elseif ($s_pm_subject == 3) {
							$pm_list[$key]['subjects'] = '科目三';

 						} elseif ($s_pm_subject == 4) {
							$pm_list[$key]['subjects'] = '科目四';
 						}
					}
				}
			}
		}
		$date_config['am_list'] = $am_list;
		$date_config['pm_list'] = $pm_list;
		return $date_config;
    }

    /**
     * 添加或者更新最终时间配置
     *
     * @return 	void
     * @author 	wl
     * @date	Jan 13, 2017
     **/
    public function setCoachTimeConfig ($post) {
    	// 判断当前时间是否被预约
    	$condition = array(
    		'orders.l_coach_id' => $post['coach_id'],
    		'orders.i_status' => array('not in', array('101', '3')),
    		'appoint.year' => $post['year'],
    		'appoint.month' => $post['month'],
    		'appoint.day' => $post['day'],
		);
		$appoint_info = $this->table(C('DB_PREFIX').'study_orders orders')
			->join(C('DB_PREFIX').'coach_appoint_time appoint ON appoint.id = orders.appoint_time_id ', 'LEFT')
			->where($condition)
			->field('orders.time_config_id')
			->select(); 
		$time_config_ids = array();
		$time_config_ids_arr = array();
		if (!empty($appoint_info)) {
			foreach ($appoint_info as $key => $value) {
				$time_config_ids = array_filter(explode(',', $value['time_config_id']));
				foreach ($time_config_ids as $index => $time) {
					$time_config_ids_arr[] = $time;
				}
			}
		}
		$time_config_id = explode(',', $post['time_config_id']);
		$diff = array_diff($time_config_ids_arr, $time_config_id);
		if (!empty($diff)) {
			$coach_time_config = $this->table(C('DB_PREFIX').'coach_time_config')
				->where(array('id' => array('in', $diff)))
				->field('start_time, end_time')
				->select();
			$coach_time_list = array();
			if (!empty($coach_time_config)) {
				foreach ($coach_time_config as $time_index => $time_value) {
					$coach_time_list[] = $time_value['start_time'].':00-'.$time_value['end_time'].':00';	
				}
			}
			$data = array('code' => 2, 'msg' => '参数错误', 'data' => $coach_time_list);
			return $data;
		}	

		// 查询当前日期的数据
		$map = array(
			'coach_id' => $post['coach_id'],
			'year' => $post['year'],
			'month' => $post['month'],
			'day' => $post['day'],
		);
		$current_time_config = $this->table(C('DB_PREFIX').'current_coach_time_configuration')
			->fetchSql(false)
			->where($map)
			->find();
		if (!empty($current_time_config)) {
			// 1.更新原来的时间设置
			$current_coach_time_config = D('current_coach_time_configuration');
			if ($res = $current_coach_time_config->create($post)) {
				$res['updatetime'] = time();
				$result = $current_coach_time_config->where(array('id' => $current_time_config['id']))
					->fetchSql(false)
					->save($res);
			}
		} else {
			// 2.添加原来的时间设置
			$current_coach_time_config = D('current_coach_time_configuration');
			if ($res = $current_coach_time_config->create($post)) {
				$res['addtime'] = time();
				$result = $current_coach_time_config
					->fetchSql(false)
					->add($res);
			}
		}
		if ($result) {
			$data = array('code' => 1, 'msg' => '更新成功', 'data' => $result);
		} else {
			$data = array('code' => 400, 'msg' => '更新失败', 'data' => '');
		}
		return $data;
    }

    /**
     * 点击日期获取教练的时间配置
     *
     * @return 	void
     * @author 	wl
     * @date	Jan 13, 2016
     **/
    public function getCoachCurrentTime ($coach_id, $date) {
    	$time_list = array();
    	$date_config_arr = explode('-', $date);
    	$year = $date_config_arr[0];
    	$month = $date_config_arr[1];
    	$day = $date_config_arr[2];

    	// 教练最终配置表中判断教练有无设置时间段
    	$condition = array(
    		'coach_id' => $coach_id,
    		'year' => $year,
    		'month' => $month,
    		'day' => $day,
		);
    	$current_time_config = $this->table(C('DB_PREFIX').'current_coach_time_configuration')
    		->where($condition)
    		->fetchSql(false)
    		->find();
		if (!empty($current_time_config)) {
			$time_list['time_config_id'] = explode(',', $current_time_config['time_config_id']);
			$time_list['time_config_money_id'] = json_decode($current_time_config['time_config_money_id'], true);
			$time_list['time_lisence_config_id'] = json_decode($current_time_config['time_lisence_config_id'], true);
			$time_list['time_lesson_config_id'] = json_decode($current_time_config['time_lesson_config_id'], true);
		} else {
			$time_list['time_config_id'] = array();
			$time_list['time_config_money_id'] = array();
			$time_list['time_lisence_config_id'] = array();
			$time_list['time_lesson_config_id'] = array();
		}

		// 判断此段时间预约的情况
		$map = array(
    		'orders.l_coach_id' => $coach_id,
    		'orders.i_status' => array('not in', array('101', '3')),
    		'appoint.year' => $year,
    		'appoint.month' => $month,
    		'appoint.day' => $day,
		);
		$appoint_info = $this->table(C('DB_PREFIX').'study_orders orders')
			->join(C('DB_PREFIX').'coach_appoint_time appoint ON appoint.id = orders.appoint_time_id ', 'LEFT')
			->where($map)
			->field('orders.time_config_id')
			->fetchSql(false)
			->select(); 
		$time_config_ids = array();
		$time_config_ids_arr = array();
		if (!empty($appoint_info)) {
			foreach ($appoint_info as $key => $value) {
				$time_config_ids = array_filter(explode(',', $value['time_config_id']));
				foreach ($time_config_ids as $index => $time) {
					$time_config_ids_arr[] = $time;
				}
			}
		}
		// if (!empty($time_config_ids_arr)) {
			$time_list['is_appoint'] = $time_config_ids_arr;
		// } 
		return $time_list;
    }

    /**
     * 点击日期获取教练当前设置的时间段
     *
     * @return 	void
     * @author 	wl
     * @date	Jan 16, 2017
     **/
    public function getCoachCurrentFinalTime ($school_id, $coach_id, $date) {
    	$final_time_config = array();
    	$date_config = explode('-', $date);
    	$year = $date_config[0];
    	$month = $date_config[1];
    	$day = $date_config[2];

    	// 获取驾校配置信息
    	$school_config = $this->table(C('DB_PREFIX').'school_config')
    		->where(array('l_school_id' => $school_id))
    		->field('s_time_list, is_automatic')
    		->find();
		$s_time_list = array();
		$is_automatic = 1;
		if (!empty($school_config)) {
			$s_time_list = array_filter(explode(',', $school_config['s_time_list']));
			$is_automatic = $school_config['is_automatic'];
		}
		// 获取教练设置的时间配置
		$coach_config_info = $this->table(C('DB_PREFIX').'coach')
			->where(array('l_coach_id' => $coach_id))
			->field('s_am_subject, s_pm_subject, s_am_time_list, s_pm_time_list')
			->find();
		$s_am_subject = 2;
		$s_pm_subject = 2;
		$s_am_time_list = array();
		$s_pm_time_list = array();
		if (!empty($coach_config_info)) {
			$s_am_subject = $coach_config_info['s_am_subject'] != '' ? $coach_config_info['s_am_subject'] : '2';
			$s_pm_subject = $coach_config_info['s_pm_subject'] != '' ? $coach_config_info['s_pm_subject'] : '3';
			$s_am_time_list = isset($coach_config_info['s_am_time_list']) ? array_filter(explode(',', $coach_config_info['s_am_time_list'])) : array();
			$s_pm_time_list = isset($coach_config_info['s_pm_time_list']) ? array_filter(explode(',', $coach_config_info['s_pm_time_list'])) : array();
		}

		if (!empty($s_am_time_list) && !empty($s_pm_time_list)) {
			$time_config_ids_arr = array_merge($s_am_time_list, $s_pm_time_list);
		} else {
			$time_config_ids_arr = $s_time_list;
		}

        // 获取当前时间配置表中的相关信息
        $time_config_ids_arr = array_filter($time_config_ids_arr);
        $final_time_config['time_config_ids'] = $time_config_ids_arr;
        foreach ($time_config_ids_arr as $key => $value) {
            $final_time_config['time_config_ids_arr'][$value] = $value;
        }
        if (!empty($time_config_ids_arr)) {
            $condition = array(
                'id' => array('in', implode(',', $time_config_ids_arr)),
                'status' => 1,
            );
            $coach_time_config = $this->table(C('DB_PREFIX').'coach_time_config')
                ->where($condition)
                ->order('start_time ASC')
                ->select();
        } else {
            $coach_time_config = $this->table(C('DB_PREFIX').'coach_time_config')
                ->where(array('status' => 1))
                ->select();
        }
        
		// 获取教练当前时间配置中的相关信息
		$condition = array(
			'l_coach_id' => $coach_id,
			'year' => $year,
			'month' => $month,
			'day' => $day,
		);
		$current_time_config = $this->table(C('DB_PREFIX').'current_coach_time_configuration')
			->where($condition)
			->find();
        if (!empty($current_time_config)) {
			$time_config_id_arr = array_filter(explode(',', $current_time_config['time_config_id']));
			$time_lisence_config_id_arr = json_decode($current_time_config['time_lisence_config_id'], true);
			$time_lesson_config_id_arr = json_decode($current_time_config['time_lesson_config_id'], true);
			$time_config_money_id_arr = json_decode($current_time_config['time_config_money_id'], true);
		}

		// 检查此时间段有无预约
		$map = array(
			'orders.l_coach_id' => $coach_id,
			'orders.i_status' => array('not in', array('101', '3')),
			'appoint.year' => $year,
			'appoint.month' => $month,
			'appoint.day' => $day,
		);
		$study_orders_info = $this->table(C('DB_PREFIX').'study_orders orders')
			->join(C('DB_PREFIX').'coach_appoint_time appoint ON appoint.id = orders.appoint_time_id', 'LEFT')
			->where($map)
            ->fetchSql(false)
            ->field('orders.time_config_id')
            ->select();
        $appoint_time_config_id = array();
        $appoint_time_config_ids_arr = array();
        foreach ($study_orders_info as $orders_index => $orders_value) {
            $appoint_time_config_id = explode(',', $orders_value['time_config_id']);
            foreach ($appoint_time_config_id as $key => $value) {
                $appoint_time_config_ids_arr[] = $value;
            }
        }

        if (!empty($coach_time_config)) {
            foreach ($coach_time_config as $key => $value) {
                $start_minute = $value['start_minute'] == 0 ? '00' : $value['start_minute'];
                $end_minute = $value['end_minute'] == 0 ? '00' : $value['end_minute'];
                // $coach_time_config[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                // $coach_time_config[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;

                // 上午下午都不为空
                if (!empty($s_am_time_list) && !empty($s_pm_time_list)) {
                    // 上午
                    if (in_array($value['id'], $s_am_time_list)) {

                        $am_list[$key]['id'] = $value['id'];
                        $am_list[$key]['status'] = $value['status'];
                        // $am_list[$key]['price'] = $value['price'];
                        // $am_list[$key]['license_no'] = $value['license_no'];
                        $am_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                        $am_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;

                        if (in_array($value['id'], $appoint_time_config_ids_arr)) {
                            $am_list[$key]['is_appoint'] = 1; // 1:被预约;2:未被预约
                        } else {
                            $am_list[$key]['is_appoint'] = 2; 
                        }

                        if (in_array($value['id'], $time_config_id_arr)) {
                            $am_list[$key]['is_setting'] = 1; // 1:可以;2:不可以
                        } else {
                            $am_list[$key]['is_setting'] = 2; 
                        }

                        // 价格
                        if (!empty($time_config_money_id_arr)) {
                            if (in_array($value['price'], $time_config_money_id_arr)) {
                                $am_list[$key]['price'] = $value['price']; 
                            } else {
                                foreach ($time_config_money_id_arr as $k => $v) {
                                    if ($v != $value['price']) {
                                        $am_list[$key]['price'] = $v; 
                                    }
                                }
                            }
                        } else {
                            $am_list[$key]['price'] = $value['price']; 
                        } 

                        // 牌照
                        if (!empty($time_lisence_config_id_arr)) {
                            if (in_array($value['license_no'], $time_lisence_config_id_arr)) {
                                foreach ($time_lisence_config_id_arr as $k => $v) {
                                    if ($value['license_no'] != $v) {
                                        $am_list[$key]['license_no'] = $v; 
                                    }
                                }
                            } else {
                                $am_list[$key]['license_no'] = $value['license_no']; 
                            }
                        } else {
                            $am_list[$key]['license_no'] = $value['license_no']; 
                        }

                        // 科目
                        if (!empty($time_lesson_config_id_arr)) {
                            if (!empty($time_lesson_config_id_arr)) {
                                foreach ($time_lesson_config_id_arr as $k => $v) {
                                    if (trim($value['subjects']) != trim($v)) {
                                        $am_list[$key]['subjects'] = $v; 
                                    } 
                                }
                            } else {
                                if ($s_am_subject == 1) {
                                    $am_list[$key]['subjects'] = '科目一';

                                } elseif ($s_am_subject == 2) {
                                    $am_list[$key]['subjects'] = '科目二';

                                } elseif ($s_am_subject == 3) {
                                    $am_list[$key]['subjects'] = '科目三';

                                } elseif ($s_am_subject == 4) {
                                    $am_list[$key]['subjects'] = '科目四';

                                } 
                            }
                        } else {
                            if ($s_am_subject == 1) {
                                $am_list[$key]['subjects'] = '科目一';

                            } elseif ($s_am_subject == 2) {
                                $am_list[$key]['subjects'] = '科目二';

                            } elseif ($s_am_subject == 3) {
                                $am_list[$key]['subjects'] = '科目三';

                            } elseif ($s_am_subject == 4) {
                                $am_list[$key]['subjects'] = '科目四';

                            } 
                        }

                    }

                    // 下午
                    if (in_array($value['id'], $s_pm_time_list)) {
                        $pm_list[$key]['id'] = $value['id'];
                        // $pm_list[$key]['price'] = $value['price'];
                        $pm_list[$key]['status'] = $value['status'];
                        // $pm_list[$key]['license_no'] = $value['license_no'];
                        $pm_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                        $pm_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
                        if (in_array($value['id'], $appoint_time_config_ids_arr)) {
                            $pm_list[$key]['is_appoint'] = 1; // 1:被预约;2:未被预约
                        } else {
                            $pm_list[$key]['is_appoint'] = 2; 
                        }
                        
                        if (in_array($value['id'], $time_config_id_arr)) {
                            $pm_list[$key]['is_setting'] = 1; // 1:可以;2:不可以
                        } else {
                            $pm_list[$key]['is_setting'] = 2; 
                        }

                        // 价格
                        if (!empty($time_config_money_id_arr)) {
                            if (in_array($value['price'], $time_config_money_id_arr)) {
                                $pm_list[$key]['price'] = $value['price']; 
                            } else {
                                foreach ($time_config_money_id_arr as $k => $v) {
                                    if ($v != $value['price']) {
                                        $pm_list[$key]['price'] = $v; 
                                    }
                                }
                            }
                        } else {
                            $pm_list[$key]['price'] = $value['price']; 
                        } 

                         // 牌照
                        if (!empty($time_lisence_config_id_arr)) {
                            if (in_array($value['license_no'], $time_lisence_config_id_arr)) {
                                foreach ($time_lisence_config_id_arr as $k => $v) {
                                    if ($value['license_no'] != $v) {
                                        $pm_list[$key]['license_no'] = $v; 
                                    }
                                }
                            } else {
                                $pm_list[$key]['license_no'] = $value['license_no']; 
                            }
                        } else {
                            $pm_list[$key]['license_no'] = $value['license_no']; 
                        }

                        // 科目
                        if (!empty($time_lesson_config_id_arr)) {
                            if (!empty($time_lesson_config_id_arr)) {
                                foreach ($time_lesson_config_id_arr as $k => $v) {
                                    if (trim($value['subjects']) != trim($v)) {
                                        $pm_list[$key]['subjects'] = $v; 
                                    } 
                                }
                            } else {
                                if ($s_am_subject == 1) {
                                    $pm_list[$key]['subjects'] = '科目一';

                                } elseif ($s_am_subject == 2) {
                                    $pm_list[$key]['subjects'] = '科目二';

                                } elseif ($s_am_subject == 3) {
                                    $pm_list[$key]['subjects'] = '科目三';

                                } elseif ($s_am_subject == 4) {
                                    $pm_list[$key]['subjects'] = '科目四';

                                } 
                            }
                        } else {
                            if ($s_am_subject == 1) {
                                $pm_list[$key]['subjects'] = '科目一';

                            } elseif ($s_am_subject == 2) {
                                $pm_list[$key]['subjects'] = '科目二';

                            } elseif ($s_am_subject == 3) {
                                $pm_list[$key]['subjects'] = '科目三';

                            } elseif ($s_am_subject == 4) {
                                $pm_list[$key]['subjects'] = '科目四';

                            } 
                        }

                    }

                } else {
                    // 上午下午都为空
                    if ($value['end_time'] <= 12) {
                        // 上午
                        $am_list[$key]['id'] = $value['id'];
                        // $am_list[$key]['price'] = $value['price'];
                        $am_list[$key]['status'] = $value['status'];
                        // $am_list[$key]['license_no'] = $value['license_no'];
                        $am_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                        $am_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
                        if (in_array($value['id'], $appoint_time_config_ids_arr)) {
                            $am_list[$key]['is_appoint'] = 1; // 1:被预约;2:未被预约
                        } else {
                            $am_list[$key]['is_appoint'] = 2; 
                        }

                        if (in_array($value['id'], $time_config_id_arr)) {
                            $am_list[$key]['is_setting'] = 1; // 1:可以;2:不可以
                        } else {
                            $am_list[$key]['is_setting'] = 2; 
                        }

                        // 价格
                        if (!empty($time_config_money_id_arr)) {
                            if (in_array($value['price'], $time_config_money_id_arr)) {
                                $am_list[$key]['price'] = $value['price']; 
                            } else {
                                foreach ($time_config_money_id_arr as $k => $v) {
                                    if ($v != $value['price']) {
                                        $am_list[$key]['price'] = $v; 
                                    }
                                }
                            }
                        } else {
                            $am_list[$key]['price'] = $value['price']; 
                        } 

                        // 牌照
                        if (!empty($time_lisence_config_id_arr)) {
                            if (in_array($value['license_no'], $time_lisence_config_id_arr)) {
                                foreach ($time_lisence_config_id_arr as $k => $v) {
                                    if ($value['license_no'] != $v) {
                                        $am_list[$key]['license_no'] = $v; 
                                    }
                                }
                            } else {
                                $am_list[$key]['license_no'] = $value['license_no']; 
                            }
                        } else {
                            $am_list[$key]['license_no'] = $value['license_no']; 
                        }

                        // 科目
                        if (!empty($time_lesson_config_id_arr)) {
                            if (!empty($time_lesson_config_id_arr)) {
                                foreach ($time_lesson_config_id_arr as $k => $v) {
                                    if (trim($value['subjects']) != trim($v)) {
                                        $am_list[$key]['subjects'] = $v; 
                                    } 
                                }
                            } else {
                                if ($s_am_subject == 1) {
                                    $am_list[$key]['subjects'] = '科目一';

                                } elseif ($s_am_subject == 2) {
                                    $am_list[$key]['subjects'] = '科目二';

                                } elseif ($s_am_subject == 3) {
                                    $am_list[$key]['subjects'] = '科目三';

                                } elseif ($s_am_subject == 4) {
                                    $am_list[$key]['subjects'] = '科目四';

                                } 
                            }
                        } else {
                            if ($s_am_subject == 1) {
                                $am_list[$key]['subjects'] = '科目一';

                            } elseif ($s_am_subject == 2) {
                                $am_list[$key]['subjects'] = '科目二';

                            } elseif ($s_am_subject == 3) {
                                $am_list[$key]['subjects'] = '科目三';

                            } elseif ($s_am_subject == 4) {
                                $am_list[$key]['subjects'] = '科目四';

                            } 
                        }
                    } else {
                        // 下午
                        $pm_list[$key]['id'] = $value['id'];
                        // $pm_list[$key]['price'] = $value['price'];
                        $pm_list[$key]['status'] = $value['status'];
                        // $pm_list[$key]['license_no'] = $value['license_no'];
                        $pm_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                        $pm_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
                        if (in_array($value['id'], $appoint_time_config_ids_arr)) {
                            $pm_list[$key]['is_appoint'] = 1; // 1:被预约;2:未被预约
                        } else {
                            $pm_list[$key]['is_appoint'] = 2; 
                        }
                        if (in_array($value['id'], $time_config_id_arr)) {
                            $pm_list[$key]['is_setting'] = 1; // 1:可以;2:不可以
                        } else {
                            $pm_list[$key]['is_setting'] = 2; 
                        }

                        // 价格
                        if (!empty($time_config_money_id_arr)) {
                            if (in_array($value['price'], $time_config_money_id_arr)) {
                                $pm_list[$key]['price'] = $value['price']; 
                            } else {
                                foreach ($time_config_money_id_arr as $k => $v) {
                                    if ($v != $value['price']) {
                                        $pm_list[$key]['price'] = $v; 
                                    }
                                }
                            }
                        } else {
                            $pm_list[$key]['price'] = $value['price']; 
                        } 

                         // 牌照
                        if (!empty($time_lisence_config_id_arr)) {
                            if (in_array($value['license_no'], $time_lisence_config_id_arr)) {
                                foreach ($time_lisence_config_id_arr as $k => $v) {
                                    if ($value['license_no'] != $v) {
                                        $pm_list[$key]['license_no'] = $v; 
                                    }
                                }
                            } else {
                                $pm_list[$key]['license_no'] = $value['license_no']; 
                            }
                        } else {
                            $pm_list[$key]['license_no'] = $value['license_no']; 
                        }

                        // 科目
                        if (!empty($time_lesson_config_id_arr)) {
                            if (!empty($time_lesson_config_id_arr)) {
                                foreach ($time_lesson_config_id_arr as $k => $v) {
                                    if (trim($value['subjects']) != trim($v)) {
                                        $pm_list[$key]['subjects'] = $v; 
                                    } 
                                }
                            } else {
                                if ($s_am_subject == 1) {
                                    $pm_list[$key]['subjects'] = '科目一';

                                } elseif ($s_am_subject == 2) {
                                    $pm_list[$key]['subjects'] = '科目二';

                                } elseif ($s_am_subject == 3) {
                                    $pm_list[$key]['subjects'] = '科目三';

                                } elseif ($s_am_subject == 4) {
                                    $pm_list[$key]['subjects'] = '科目四';

                                } 
                            }
                        } else {
                            if ($s_am_subject == 1) {
                                $pm_list[$key]['subjects'] = '科目一';

                            } elseif ($s_am_subject == 2) {
                                $pm_list[$key]['subjects'] = '科目二';

                            } elseif ($s_am_subject == 3) {
                                $pm_list[$key]['subjects'] = '科目三';

                            } elseif ($s_am_subject == 4) {
                                $pm_list[$key]['subjects'] = '科目四';

                            } 
                        }

                    }
                }
            }
        }
        $time_list['am_list'] = $am_list;
        $time_list['pm_list'] = $pm_list;
        return $time_list;
    } 


// 3.系统管理中的驾校/教练的时间设置
	/**
	 * 获取教练的系统配置时间
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Sep 18, 2016
	 **/
	public function getCoachConfig () {
		$coach_config = $this->table(C('DB_PREFIX').'coach_time_config') 
			->select();
		if ($coach_config) {
			foreach ($coach_config as $key => $value) {
				$start_minute = $value['start_minute'] = 0 ? '00' : $value['start_minute'];
				$end_minute = $value['end_minute'] = 0 ? '00' : $value['end_minute'];
				$coach_config[$key]['final_start_time'] = $value['start_time'] .':'. $start_minute;
				$coach_config[$key]['final_end_time'] = $value['end_time'] .':'. $end_minute;
			}
		}
		return $coach_config;
	}

	/**
	 * 获取教练的系统配置时间
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Sep 19, 2016
	 **/
	public function getCoachConfigList () {
		$count = $this->table(C('DB_PREFIX').'coach_time_config_new') ->count();
		$Page = new Page($count, 10);
		$page = $this->getPage($count, 10);
		$coach_configs = array();
		$coach_config = $this->table(C('DB_PREFIX').'coach_time_config_new')
			->limit($Page->firstRow.',',$page->listRows)
			->order('id DESC')
			->select();
		if ($coach_config) {
			foreach ($coach_config as $key => $value) {
				$coach_config[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
			}
		}
		$coach_configs = array('coach_config' => $coach_config, 'page' => $page, 'count' => $count);
		return $coach_configs;
	}

	/**
	 * 设置教练配置信息是否发布的状态设置
	 *
	 * @return  void
	 * @author  wl
	 * @date    Sep 19, 2016
	 **/
	public function setPublishStatus ($id, $status) {
		if (!$id) {
			return false;
		}
		$list = array();
		$data = array('is_publish' => $status);
		$result = M('coach_time_config_new')->where('id = :cid')
			->bind(['cid' => $id])
			->fetchSql(false)
			->data($data)
			->save();
		$list['is_publish']	= $result;
		$list['id']			= $id;
		return $list;
	}

	/**
	* 设置教练配置信息是否在线的状态设置
	*
	* @return  void
	* @author  wl
	* @date    Sep 19, 2016
	**/
	public function setOnlineStatus ($id, $status) {
		if (!$id) {
			return false;
		}
		$list = array();
		$data = array('is_online' => $status);
		$result = M('coach_time_config_new')->where('id = :cid')
			->bind(['cid' => $id])
			->fetchSql(false)
			->data($data)
			->save();
		$list['is_online']	= $result;
		$list['id']			= $id;
		return $list;
	}

	/**
	 * 获取教练信息
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Sep 19, 2016
	 **/
	public function getCoachInfo () {
		$coach_list = $this->table(C('DB_PREFIX').'coach c')
			// ->join(C('DB_PREFIX').'school s ON s.l_school_id = c.s_school_name_id', 'LEFT')
			// ->join(C('DB_PREFIX').'cars cs ON cs.school_id = s.l_school_id', 'LEFT')
			// ->field('c.l_coach_id, c.s_coach_name, s.l_school_id, s_school_name, cs.name, cs.car_no')
			->field('c.l_coach_id, c.s_coach_name')
			->select();
		if (!$coach_list) {
			return array();
		}
		$coach_list = array_filter($coach_list);
		return $coach_list;
	}

	/**
	 * 获取上午和下午的时间
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Sep 19, 2016
	 **/
	public function getAmPmTimeConfig() {
		$time_list = $this->table(C('DB_PREFIX').'coach_time_config_new')
			->where(array('is_online' => 1))
			->order('start_time asc')
			->fetchSql(false)
			->select();
		$list = array();
		if ($time_list) {
			foreach ($time_list as $key => $value) {
				$time_list[$key]['start_minute'] = $value['start_minute'] == 0 ? '00' : $value['start_minute'];
				$time_list[$key]['end_minute'] = $value['end_minute'] == 0 ? '00' : $value['end_minute'];
				if($value['end_hour'] <= 12) {
					$list['am_time_list'][$key] = $value;
				} else {
					$list['pm_time_list'][$key] = $value;
				}
			}
		return $list;		
		}
	}

	/**
	 * 删除教练配置信息
	 *
	 * @return  void
	 * @author  wl
	 * @date   	Sep 19, 2016
	 **/
	public function delCoachConfig ($id) {
		if (!$id) {
			return false;
		}
		$result = M('coach_time_config_new')
			->where(array('id' => $id))
			->save(array('is_online' => 2));
			// ->delete();
		return $result;
	}

	/**
	 * 获取单条教练配置时间
	 *
	 * @return  void
	 * @author  wl
	 * @date   	Sep 19, 2016
	 **/
	public function getCoachConfigById ($id) {
		if (!is_numeric($id)) {
			return false;
		}
		$coach_config = $this->table(C('DB_PREFIX').'coach_time_config_new')
			->where(array('id' => $id))
			->find();
		if (!$coach_config) {
			return array();
		}
		return $coach_config;
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