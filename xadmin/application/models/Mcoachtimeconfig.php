<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcoachtimeconfig extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->coach_tbl = $this->db->dbprefix('coach');
        $this->coach_time_config_tbl = $this->db->dbprefix('coach_time_config');
        $this->school_config_tbl = $this->db->dbprefix('school_config');
        $this->study_orders_tbl = $this->db->dbprefix('study_orders');
        $this->coach_appoint_time_tbl = $this->db->dbprefix('coach_appoint_time');
        $this->curr_coach_time_cfg_tbl = $this->db->dbprefix('current_coach_time_configuration');
    }

    // 获取教练上午下午时间配置
    public function getCoachAmPmConfig ($coach_id)
    {
        $coach_time_list = $this->db->select('s_am_subject, s_pm_subject, s_am_time_list, s_pm_time_list')
            ->from("{$this->coach_tbl}")
            ->where(['l_coach_id'=>$coach_id])
            ->get()->row_array();
        $time_list = $this->db->from("{$this->coach_time_config_tbl}")
            ->where(['status'=>1])
            ->order_by('start_time', 'ASC')
            ->get()
            ->result_array();
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

            $list['am_time_selectd_list'] = []; 
            $list['pm_time_selectd_list'] = []; 
            if ( ! empty($coach_time_list)) {
                $coach_time_list['s_am_time_list'] = explode(',', $coach_time_list['s_am_time_list']);
                $coach_time_list['s_pm_time_list'] = explode(',', $coach_time_list['s_pm_time_list']);
                foreach ($list['am_time_list'] as $am_time_list_index => $am_time_list_value) {
                    if (in_array($am_time_list_value['id'], $coach_time_list['s_am_time_list'])) {
                        $list['am_time_selectd_list'][$am_time_list_value['id']] = $am_time_list_value;
                        $list['am_time_list'][$am_time_list_index]['is_set'] = 1; // 此时间段已经设置
                    } else {
                        $list['am_time_list'][$am_time_list_index]['is_set'] = 2; // 此时间段未设置
                    }
                }

                foreach ($list['pm_time_list'] as $pm_time_list_index => $pm_time_list_value) {
                    if (in_array($pm_time_list_value['id'], $coach_time_list['s_pm_time_list'])) {
                        $list['pm_time_selectd_list'][$pm_time_list_value['id']] = $pm_time_list_value; 
                        $list['pm_time_list'][$pm_time_list_index]['is_set'] = 1; // 此时间段已经设置
                    } else {
                        $list['pm_time_list'][$pm_time_list_index]['is_set'] = 2; // 此时间段未设置
                    }
                }
                $list['am_subject'] = $coach_time_list['s_am_subject'];
                $list['pm_subject'] = $coach_time_list['s_pm_subject'];
            } else {
                foreach ($list['am_time_list'] as $am_time_list_index => $am_time_list_value) {
                    $list['am_time_list'][$am_time_list_index]['is_set'] = 2; // 此时间段未设置
                }
                foreach ($list['pm_time_list'] as $pm_time_list_index => $pm_time_list_value) {
                    $list['pm_time_list'][$pm_time_list_index]['is_set'] = 2; // 此时间段未设置
                }
                $list['am_subject'] = '';
                $list['pm_subject'] = '';
            }
        }
        return $list;
    }

    /*获取教练的时间配置情况(同时获取可变的日期配置*/
    public function getCoachTimeConfig ($school_id, $coach_id = 0) {
        $date_config = [];
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
        $school_config = $this->db->select('s_time_list, is_automatic')
            ->from("{$this->school_config_tbl}")
            ->where(['l_school_id'=>$school_id])
            ->get()->row_array();
        $s_time_list = [];
        $is_automatic = 1;
        if ( ! empty($school_config)) {
            $s_time_list = array_filter(explode(',', $school_config['s_time_list']));
            $is_automatic = $school_config['is_automatic'];
        }

        // 获取教练的时间配置
        $coach_info = $this->db->select('s_am_subject, s_pm_subject, s_am_time_list, s_pm_time_list')
            ->from("{$this->coach_tbl}")
            ->where(['l_coach_id'=>$coach_id])
            ->get()->row_array();            
        $s_am_subject = 2;
        $s_pm_subject = 3;
        $s_am_time_list = [];
        $s_pm_time_list = [];
        if ( ! empty($coach_info)) {
            $s_am_subject = $coach_info['s_am_subject'] !='' ? $coach_info['s_am_subject'] : "2";
            $s_pm_subject = $coach_info['s_pm_subject'] !='' ? $coach_info['s_pm_subject'] : "3";
            $s_am_time_list = isset($coach_info['s_am_time_list']) ? array_filter(explode(',', $coach_info['s_am_time_list'])) : [];
            $s_pm_time_list = isset($coach_info['s_pm_time_list']) ? array_filter(explode(',', $coach_info['s_pm_time_list'])) : [];
        }

        if ( ! empty($s_am_time_list) && ! empty($s_pm_time_list)) {
            $time_config_ids_arr = array_merge($s_am_time_list, $s_pm_time_list);
        } else {
            $time_config_ids_arr = $s_time_list;
        }

        // 获取当前时间配置表中的相关信息
        $time_config_ids_arr = array_filter($time_config_ids_arr);
        if ( ! empty($time_config_ids_arr)) {
            $coach_time_config = $this->db->from("{$this->coach_time_config_tbl}")
                ->where(['status' => 1])
                ->where_in('id', $time_config_ids_arr)
                ->order_by('start_time', 'ASC')
                ->get()->result_array();
        } else {
            $coach_time_config = $this->db->from("{$this->coach_time_config_tbl}")
                ->where(['status' => 1])
                ->order_by('start_time', 'ASC')
                ->get()->result_array();
        }
        if ( ! empty($coach_time_config)) {
            foreach ($coach_time_config as $key => $value) {
                $start_minute = $value['start_minute'] == 0 ? '00' : $value['start_minute'];
                $end_minute = $value['end_minute'] == 0 ? '00' : $value['end_minute'];
                // $value['final_start_time'] = $value['start_time'].':'.$start_minute;
                // $value['final_end_time'] = $value['end_time'].':'.$end_minute;

                // 上午下午都不为空
                if ( ! empty($s_am_time_list) && ! empty($s_pm_time_list)) {
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
     * @return  void
     **/
    public function setCoachTimeConfig ($param) {
        // 判断当前时间是否被预约
        $map = [];
        $map['coach_id'] = $param['coach_id'];
        $map['appoint.year'] = $param['year'];
        $map['appoint.month'] = $param['month'];
        $map['appoint.day'] = $param['day'];
        $appoint_info = $this->db->select('orders.time_config_id')
            ->from("{$this->study_orders_tbl}  as orders")
            ->join("{$this->coach_appoint_time_tbl} as appoint", 'appoint.id=orders.appoint_time_id', 'left')
            ->where($map)
            ->where_not_in('orders.i_status', [101, 3])
            ->get()->result_array();
		$time_config_ids = [];
		$time_config_ids_arr = [];
		if ( ! empty($appoint_info)) {
			foreach ($appoint_info as $key => $value) {
				$time_config_ids = array_filter(explode(',', $value['time_config_id']));
				foreach ($time_config_ids as $index => $time) {
					$time_config_ids_arr[] = $time;
				}
			}
		}
        $time_config_id = explode(',', $param['time_config_id']);
		$diff = array_diff($time_config_ids_arr, $time_config_id);
		if ( ! empty($diff)) {
            $coach_time_config = $this->db->select('start_time,end_time')
                ->from("{$this->coach_time_config_tbl}")
                ->where_in('id', $diff)
                ->get()->result_array();
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
		$condition = array(
			'coach_id' => $param['coach_id'],
			'year' => $param['year'],
			'month' => $param['month'],
			'day' => $param['day'],
		);
        $current_time_config = $this->db->from("{$this->curr_coach_time_cfg_tbl}")
            ->where($condition)->get()->row_array();
		if ( ! empty($current_time_config)) {
			// 1.更新原来的时间设置
            $param['updatetime'] = time();
            $result = $this->db->update($this->curr_coach_time_cfg_tbl, $param, array('id'=>$current_time_config['id']));
		} else {
			// 2.添加原来的时间设置
            $param['addtime'] = time();
            $result = $this->db->insert($this->curr_coach_time_cfg_tbl, $param);
		}
		if ($result) {
			$data = array('code' => 1, 'msg' => '更新成功', 'data' => $result);
		} else {
			$data = array('code' => 400, 'msg' => '更新失败', 'data' => '');
		}
		return $data;
    }


}
