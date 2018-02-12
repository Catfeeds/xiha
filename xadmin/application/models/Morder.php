<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class Morder extends CI_Model {

    public $shiftsorder_tablename = 'cs_school_orders';
    public $timingorder_tablename = 'cs_study_orders';
    public $school_tablename = 'cs_school';
    public $coach_tablename = 'cs_coach';
    public $user_tablename = 'cs_user';
    public $userinfo_tablename = 'cs_users_info';
    public $test_tablename = 'cs_test_account';
    public $withdraw_tablename = 'cs_withdraw';
    public $coach_appoint_time = "cs_coach_appoint_time";
    public $coach_time_config = "cs_coach_time_config";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('mcoach');
        $this->load->model('mcoupon');
        $this->load->model('muser');
        $this->load->model('mschool');
    }

// 1.报名班制
    // 获取班制列表
    public function getShiftsOrderList($start='', $limit='') {
        $query = $this->db->order_by('id', 'DESC')->get($this->shiftsorder_tablename, $limit, $start);
        foreach($query->result() as $key => $value) {
            $query->result()[$key]->user_info = $this->muser->getUserInfoByCondition($this->muser->user_tablename, ['l_user_id'=>$value->so_user_id]);
            $query->result()[$key]->shift_info = $this->mschool->getShiftInfo('sh_title, sh_type, is_package', ['id'=>$value->so_shifts_id]);
            $school_info = $this->mschool->getSchoolParamsInfo('s_school_name, s_address', $value->so_school_id);
            $query->result()[$key]->school_info = $school_info['list'] ? $school_info['list'][0] : [];
            $query->result()[$key]->addtime = date('Y-m-d H:i:s', $value->addtime);
        }
        return ['list'=>$query->result()];
    }

    // 获取条件下的班制列表
    public function getShiftsOrderListByCondition($start='', $limit='', $order_status, $pay_type) {
        $query = $this->db->where('so_order_status', $order_status)->where_in('so_pay_type', $pay_type)->order_by('id', 'DESC')->get($this->shiftsorder_tablename, $limit, $start);
        foreach($query->result() as $key => $value) {
            $query->result()[$key]->user_info = $this->muser->getUserInfoByCondition($this->muser->userinfo_tablename, ['user_id'=>$value->so_user_id]);
            $query->result()[$key]->shift_info = $this->mschool->getShiftInfo('sh_title, sh_type, is_package', ['id'=>$value->so_shifts_id]);
            $school_info = $this->mschool->getSchoolParamsInfo('s_school_name, s_address', $value->so_school_id);
            $query->result()[$key]->school_info = $school_info['list'] ? $school_info['list'][0] : [];
            $query->result()[$key]->addtime = date('Y-m-d H:i:s', $value->addtime);
        }
        return ['list'=>$query->result()];
    }

    // 获取条件下的订单信息
    public function getShiftOrderInfoByCondition($wherecondition) {
        $query = $this->db->where($wherecondition)->get($this->shiftsorder_tablename);
        $order_info = $query->row_array();
        if($order_info) {
            $order_info['user_info'] = $this->muser->getUserInfoByCondition($this->muser->userinfo_tablename, ['user_id'=>$order_info['so_user_id']]);
            $order_info['shift_info'] = $this->mschool->getShiftInfo('sh_title, sh_type, is_package', ['id'=>$order_info['so_shifts_id']]);
            $school_info = $this->mschool->getSchoolParamsInfo('s_school_name, s_address', $order_info['so_school_id']);
            $order_info['school_info'] = $school_info['list'] ? $school_info['list'][0] : [];
            $order_info['addtime'] = date('Y-m-d H:i:s', $order_info['addtime']);
        }
        return $order_info;
    }

    // 根据驾校关键词获取驾校列表
    public function getSearchUserList($key) {
        $query = $this->db->select('so_user_id, s_user_name')->like('s_user_name', $key)->get($this->shiftsorder_tablename, 20);
        return ['list'=>$query->result()];
    }

    // 添加班制
    public function addUserShiftsInfo($data) {
        $this->db->insert($this->shiftsorder_tablename, $data);
        return $this->db->insert_id();
    }

    // 获取条件下报名班制的数据页码和总数
    public function getOrderPageNumByCondition($tablename, $order_status, $pay_type, $limit) {
        $count = $this->db->where('so_order_status', $order_status)->where_in('so_pay_type', $pay_type)->count_all_results($tablename);
        return ['pn'=>(int) ceil($count / $limit), 'count'=>$count];
    }

// 1.报名班制
    /**
     * 获取报名班制订单的页码信息
     * @param   int     $school_id      驾校ID
     * @param   array   $param          搜索条件
     * @param   int     $limit          限定数目
     * @param   string  $ot             线上：online | 线下：line
     * @return  $pageinfo
     **/
    public function getShiftsOrderPageNum($school_id, $param, $limit, $ot)
    {
        $map = [];
        $complex = [];
        if ($school_id != 0) {
            $map['so_school_id'] = $school_id;
        }

        if ($param) {
            if ($param['pay_type'] != '') {
                $map['so_pay_type'] = $param['pay_type'];
            }

            if ($param['order_status'] != '' AND $param['order_status'] != 200) {
                $map['so_order_status'] = $param['order_status'];
            }

            $keywords = $param['keywords'];
            if ($keywords != '') {
                $complex = [
                    'school.s_school_name' => $keywords,
                    'order.id' => $keywords,
                    'order.so_order_no' => $keywords,
                    'order.s_zhifu_dm' => $keywords,
                    'order.so_username' => $keywords,
                    'order.so_phone' => $keywords,
                ];
            }
        }

        if ($ot == "online") { // 线上
            $pay_type_arr = [1, 3, 4];
        } else { // 线下
            $pay_type_arr = [2];
        }

        $query = $this->db
            ->from("{$this->shiftsorder_tablename} as order")
            ->join("{$this->school_tablename} as school", "school.l_school_id = order.so_school_id", "LEFT")
            ->where($map)
            ->where_in('so_pay_type', $pay_type_arr);

        if ($param['order_status'] == 200 ) {
            $query = $query->where_not_in('so_order_status', [101]);
        }

        if ( ! empty($complex)) {
            $query = $query->group_start()
                ->or_like($complex)
                ->group_end();
        }

        $count = $query->count_all_results();

        $pageinfo = [
            'pagenum'   => ceil ( $count / $limit ),
            'count'     => $count,
        ];
        return $pageinfo;
    }

    /**
     * 获取报名驾校班制订单
     * @param   int     $school_id      驾校ID
     * @param   array   $param          搜索条件
     * @param   int     $start          开始数
     * @param   int     $limit          限定数目
     * @param   string  $ot             线上：online | 线下：line
     * @return  $pageinfo
     **/
    public function getSchoolOrderList($school_id, $param, $start, $limit, $ot)
    {
        $map = [];
        $complex = [];
        if ($school_id != 0) {
            $map['so_school_id'] = $school_id;
        }

        if ($param) {
            if ($param['pay_type'] != '') {
                $map['so_pay_type'] = $param['pay_type'];
            }

            if ($param['order_status'] != '' AND $param['order_status'] != 200) {
                $map['so_order_status'] = $param['order_status'];
            }

            $keywords = $param['keywords'];
            if ($keywords != '') {
                $complex = [
                    'school.s_school_name' => $keywords,
                    'order.id' => $keywords,
                    'order.so_order_no' => $keywords,
                    'order.s_zhifu_dm' => $keywords,
                    'order.so_username' => $keywords,
                    'order.so_phone' => $keywords,
                ];
            }
        }

        if ($ot == "online") { // 线上
            $pay_type_arr = [1, 3, 4];
        } else { // 线下
            $pay_type_arr = [2];
        }

        $query = $this->db
            ->select('order.*, school.s_school_name as school_name')
            ->from("{$this->shiftsorder_tablename} as order")
            ->join("{$this->school_tablename} as school", "school.l_school_id = order.so_school_id", "LEFT")
            ->where($map)
            ->where_in('so_pay_type', $pay_type_arr)
            ->limit($limit, $start)
            ->order_by('id', 'desc');

        if ($param['order_status'] == '200' ) {
            $query = $query->where_not_in('so_order_status', [101]);
        }

        if ( ! empty($complex)) {
            $query = $query->group_start()
                ->or_like($complex)
                ->group_end();
        }

        $order_list = $query->get()->result_array();
        $list = [];
        if ( ! empty($order_list)) {
            foreach ($order_list as $index => $order) {
                if ( $order['addtime'] != '' AND $order['addtime'] != 0 ) {
                    $order_list[$index]['addtime'] = date('Y-m-d H:i:s', $order['addtime']);
                } else {
                    $order_list[$index]['addtime'] = '--';
                }

                if ( $order['cancel_time'] != '' AND $order['cancel_time'] != 0 ) {
                    $order_list[$index]['cancel_time'] = date('Y-m-d H:i:s', $order['cancel_time']);
                } else {
                    $order_list[$index]['cancel_time'] = '--';
                }

                if ( $order['s_zhifu_dm'] == '') {
                    $order_list[$index]['s_zhifu_dm'] = '--';
                }

                if ( $order['cancel_time'] != '' AND $order['cancel_time'] != 0 ) {
                    $order_list[$index]['cancel_time'] = date('Y-m-d H:i:s', $order['cancel_time']);
                } else {
                    $order_list[$index]['cancel_time'] = '--';
                }

                $coach_name = '--';
                if ($order['so_coach_id'] != '') {
                    $coach_info = $this->getCoachInfoById($order['so_coach_id']);
                    if ( ! empty($coach_info)) {
                        $coach_name = $coach_info['s_coach_name'];
                    }
                }
                
                $shifts_name = '--';
                $shifts_license_name = '--';
                $shifts_info = $this->mschool->getShiftsInfo($order['so_shifts_id'], $order['so_school_id']);
                $order_list[$index]['shifts_info'] = [];
                if ( ! empty($shifts_info)) {
                    if ($shifts_info['sh_title'] != '') {
                        $shifts_name = $shifts_info['sh_title'];
                    } 
                    if ($shifts_info['sh_license_name'] != '') {
                        $shifts_license_name = $shifts_info['sh_license_name'];
                    } 
                    $order_list[$index]['shifts_info'] = $shifts_info;
                }

                $order_list[$index]['coach_name'] = $coach_name;
                $order_list[$index]['shifts_name'] = $shifts_name;
                $order_list[$index]['shifts_license_name'] = $shifts_license_name;
            }
        }
        
        return $order_list;
    }

    // 获取报名班制列表
    public function getSchoolOrderStatus($id)
    {
        $query = $this->db
            ->get_where("{$this->shiftsorder_tablename}", ['id' => $id]);
        $order_info = $query->row_array();
        $order_status = 0;
        if ( ! empty($order_info)) {
            $order_status = (int)$order_info['so_order_status'];
        }
        return $order_status;
    }

    /**
     * 添加订单
     * @param   array $data
     * @return  void
     **/
    public function addOrder($tablename, $data)
    {
        $query = $this->db->insert($tablename, $data);
        return $this->db->insert_id();
    }

    /**
     * 检测用户是否报过名
     * $param   int     $phone  用户手机
     * @return  void
     **/
    public function checkOrder($phone)
    {
        $order_info = $this->db
            ->from("{$this->shiftsorder_tablename}") 
            ->where('so_phone', $phone)
            ->get()
            ->row_array();
        return $order_info;
    }

    /**
     * 获取用户信息
     * @param   string  $phone  用户手机
     * @return  void
     **/
    public function getUserInfoByphone($phone)
    {   
        $map = ['i_status' => 0, 'i_user_type' => 0, 's_phone' => $phone];
        $user_info = $this->db
            ->from("{$this->user_tablename} as user")
            ->where($map)
            ->get()
            ->row_array();
        return $user_info;
    }

    /**
     * 获取牌照信息
     * @param   int     $license_id     牌照ID
     * @param   $license_name
     **/
    public function getLicenseName($license_id)
    {
        $license_info = $this->db
            ->from("{$this->mschool->liceconfig_tablename} as license")
            ->where('license_id', $license_id)
            ->get()
            ->row_array();
        $license_name = '';
        if ( ! empty($license_info)) {
            $license_name = $license_info['license_name'];
        }
        return $license_name;
    }

     // 获取条件下的报名班制订单信息
     public function getShiftOrderInfoById($id) {
        $query = $this->db->where('id', $id)->get($this->shiftsorder_tablename);
        $order_info = $query->row_array();
        if($order_info) {
            $pay_type_arr = ['1' => '支付宝', '2' => '线下', '3' => '微信', '4' => '银联'];
            if ( in_array($order_info['so_pay_type'], [1, 3, 4])) {
                $order_status_arr = ['1' => '已付款', '2' => '退款中', '3' => '已取消', '4' => '未付款', '1011' => '已完成', '1007' => '已退款', '101' => '已删除'];
            } else {
                $order_status_arr = ['1' => '未付款', '2' => '已取消', '3' => '已付款', '4' => '退款中', '1011' => '已完成','1007' => '已退款', '101' => '已删除'];
            }
            $order_info['order_status_text'] = $order_status_arr[$order_info['so_order_status']];
            $order_info['pay_type_text'] = $pay_type_arr[$order_info['so_pay_type']];
            $shifts_info = $this->mschool->getShiftInfo('id as sh_id, sh_title, sh_type, is_package', [ 'id' => $order_info['so_shifts_id']]);
            $order_info['sh_id'] = '';
            $order_info['sh_title'] = '';
            $order_info['school_name'] = '';
            if ( ! empty($shifts_info)) {
                $order_info['sh_id'] = $shifts_info['sh_id'];
                $order_info['sh_title'] = $shifts_info['sh_title'];
            }

            $school_info = $this->mschool->getSchoolParamsInfo('s_school_name', $order_info['so_school_id'], 1);
            $order_info['school_name'] = $school_info ? $school_info['s_school_name'] : '嘻哈平台';
        }
        return $order_info;
    }


// 2.预约计时
    /**
     * 获取预约计时页码
     * @param   int     $school_id    驾校ID
     * @param   array   $param        条件
     * @param   int     $limit        限定数
     * @param   bool    $filters      默认是false
     * @return  $pageinfo
     **/
    public function getTimingOrderPageNum($school_id, $param, $limit, $filters = false)
    {
        $map = [];
        $complex = [];
        if ($school_id != '') {
            $map['school.l_school_id'] = $school_id;
        }

        if ($param) {
            if ($param['order_status'] != '' AND $param['order_status'] != 200) {
                $map['i_status'] = $param['order_status'];
            }

            if ($param['deal_type'] != '') {
                $map['deal_type'] = $param['deal_type'];
            }
            // if ($param['keywords'] != '') {
                $complex = [
                    'school.s_school_name' => $param['keywords'],
                    'order.l_study_order_id' => $param['keywords'],
                    'order.s_user_name' => $param['keywords'],
                    'order.s_user_phone' => $param['keywords'],
                    'order.s_coach_name' => $param['keywords'],
                    'order.s_coach_phone' => $param['keywords'],
                    'order.s_order_no' => $param['keywords'],
                ];
            // }
        }

        $filter = [];
        if ($filters) {
            $filter = $this->getTestInfo();
        }
        
        $query = $this->db
            ->from("{$this->timingorder_tablename} as order")
            ->join("{$this->coach_tablename} as coach", "coach.l_coach_id = order.l_coach_id", "LEFT")
            ->join("{$this->school_tablename} as school", "school.l_school_id = coach.s_school_name_id", "LEFT")
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end();

        if ( ! empty($filter) ) {
            $query = $query->where_not_in('order.s_user_phone', $filter);
        } 

        if ($param['order_status'] == 200) {
            $query = $query->where_not_in('i_status', [101]);
        }

        $count = $query->count_all_results();
        $pageinfo = [
            'pagenum' => ceil ( $count / $limit ),
            'count' => $count
        ];
        return $pageinfo;
    }

    /**
     * 获取预约学车订单列表
     * @param   int     $school_id    驾校ID
     * @param   array   $param        条件
     * @param   int     $limit        限定数
     * @param   int     $start        开始条数
     * @param   bool    $filters      默认是false
     * @return  $list
     **/
    public function getTimingOrderList($school_id, $param, $start, $limit, $filters = false)
    {
        $map = [];
        $complex = [];
        if ($school_id != '') {
            $map['school.l_school_id'] = $school_id;
        }

        if ($param) {
            if ($param['order_status'] != '' AND $param['order_status'] != 200) {
                $map['i_status'] = $param['order_status'];
            }

            if ($param['deal_type'] != '') {
                $map['deal_type'] = $param['deal_type'];
            }
            // if ($param['keywords'] != '') {
                $complex = [
                    'school.s_school_name' => $param['keywords'],
                    'order.l_study_order_id' => $param['keywords'],
                    'order.s_user_name' => $param['keywords'],
                    'order.s_user_phone' => $param['keywords'],
                    'order.s_coach_name' => $param['keywords'],
                    'order.s_coach_phone' => $param['keywords'],
                    'order.s_order_no' => $param['keywords'],
                ];
            // }
        }

        $filter = [];
        if ($filters) {
            $filter = $this->getTestInfo();
        }

        $query = $this->db
            ->from("{$this->timingorder_tablename} as order")
            ->join("{$this->coach_tablename} as coach", "coach.l_coach_id = order.l_coach_id", "LEFT")
            ->join("{$this->school_tablename} as school", "school.l_school_id = coach.s_school_name_id", "LEFT")
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end();

        if ( ! empty($filter) ) {
            $query = $query->where_not_in('order.s_user_phone', $filter);
        } 
        
        if ($param['order_status'] == 200) {
            $query = $query->where_not_in('i_status', [101]);
        }

        $orderlist = $query->select('order.*, school.s_school_name, school.s_address')
            ->order_by('l_study_order_id', 'desc')
            ->limit($limit, $start)
            ->get()
            ->result_array();

        $service_time = $this->db
            ->from("{$this->timingorder_tablename} as order")
            ->join("{$this->coach_tablename} as coach", "coach.l_coach_id = order.l_coach_id", "LEFT")
            ->join("{$this->school_tablename} as school", "school.l_school_id = coach.s_school_name_id", "LEFT")
            ->where($map)
            ->where('order.i_status', '2')
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->select_sum('order.i_service_time')
            ->get()
            ->result_array();
        $total_service_time = 0;
        if ( ! empty ($service_time)) {
            foreach ($service_time as $time) {
                $total_service_time = intval($time['i_service_time']);
            }
        }

        if ( ! empty($orderlist)) {
            foreach ($orderlist as $index => $order) {
                if ($order['dt_order_time'] != 0 AND $order['dt_order_time'] != '') {
                    $orderlist[$index]['dt_order_time'] = date('Y-m-d H:i:s', $order['dt_order_time']);
                } else {
                    $orderlist[$index]['dt_order_time'] = '--';
                }

                if ($order['cancel_time'] != 0 AND $order['cancel_time'] != '') {
                    $orderlist[$index]['cancel_time'] = date('Y-m-d H:i:s', $order['cancel_time']);
                } else {
                    $orderlist[$index]['cancel_time'] = '--';
                }

                if ($order['cancel_type'] == 1) {
                    $orderlist[$index]['cancel_type'] = '学员端';
                } else if ($order['cancel_type'] == 2) {
                    $orderlist[$index]['cancel_type'] = '教练端';
                } else if ($order['cancel_type'] == 3) {
                    $orderlist[$index]['cancel_type'] = '后台';
                } else {
                    $orderlist[$index]['cancel_type'] = '--';
                }

                if ($order['cancel_reason'] == '') {
                    $orderlist[$index]['cancel_reason'] = '--';
                }

                if ($order['s_user_name'] == '') {
                    $orderlist[$index]['s_user_name'] = '--';
                }
                if ($order['s_user_phone'] == '') {
                    $orderlist[$index]['s_user_phone'] = '--';
                }
                if ($order['s_address'] == '') {
                    $orderlist[$index]['s_address'] = '--';
                }
                if ($order['s_lisence_name'] == '') {
                    $orderlist[$index]['s_lisence_name'] = '--';
                }
                if ($order['s_lesson_name'] == '') {
                    $orderlist[$index]['s_lesson_name'] = '--';
                }

                if ($order['s_zhifu_dm'] == '') {
                    $orderlist[$index]['s_zhifu_dm'] = '--';
                }

                if ($order['s_school_name'] == '') {
                    $orderlist[$index]['s_school_name'] = '--';
                }

                $appoint_time_id = $order['appoint_time_id'];
                //  获取预约时间
                $_query = $this->db
                    ->get_where("{$this->coach_appoint_time}", ['id' => $appoint_time_id]);
                $appoint_time = $_query->row_array();
                $time_config_id_arr = array();
                if ($appoint_time) {
                    $time_config_id_arr = array_filter(explode(',', $appoint_time['time_config_id']));
                    // 获取时间配置中的id
                    if ($time_config_id_arr) {
                        $time_config_time = array();
                        $time_config_arr = $this->db->from("{$this->coach_time_config}")
                            ->where_in('id', $time_config_id_arr)
                            ->get()
                            ->result_array();
                        if ($time_config_arr) {
                            foreach ($time_config_arr as $time_config_index => $time_config_value) {
                                $time_config_time[] = $time_config_value['start_time'].':00-'.$time_config_value['end_time'].':00';
                            }
                        }
                        $orderlist[$index]['appoint_time_date'] = date("Y-m-d", strtotime($order['dt_appoint_time']));
                        $orderlist[$index]['appoint_time'] = implode('； ', $time_config_time);
                        $orderlist[$index]['appoint_time_list'] = date("Y-m-d", strtotime($order['dt_appoint_time'])).' '.implode(' ', $time_config_time);
                    } else {
                        $orderlist[$index]['appoint_time_date'] = date("Y-m-d", strtotime($order['dt_appoint_time']));
                        $orderlist[$index]['appoint_time_list'] = date("Y-m-d", strtotime($order['dt_appoint_time']));
                    }
                }

            }
        }
        $list = [
            'total_service_time' => $total_service_time,
            'list' => $orderlist
        ];
        return $list;
    }

    // 获取测试者手机号等信息
    public function getTestInfo()
    {
        $filter = [];
        $filterlist = $this->db->from($this->test_tablename)
            ->select('value')
            ->where('field', 'stu_phone')
            ->get()
            ->result_array();
        if ( ! empty($filterlist)) {
            foreach ($filterlist as $index => $filters) {
                if (isset($filters['value'])) {
                    $filter[] = $filters['value'];
                }
            }
        }

        return $filter;
    }

    // 获取预约学车订单详情
    public function getStudyOrderInfo($id)
    {
        $query = $this->db
            ->get_where("{$this->timingorder_tablename}", ['l_study_order_id' => $id]);
        $order_info = $query->row_array();
        $list = [];
        if ( ! empty($order_info)) {
            $list['id'] = $order_info['l_study_order_id'];
            $list['order_no'] = $order_info['s_order_no'];
            if ($order_info['dt_order_time'] != 0 AND $order_info['dt_order_time'] != '') {
                $list['dt_order_time'] = date('Y-m-d H:i:s', $order_info['dt_order_time']);
            } else {
                $list['dt_order_time'] = '--';
            }
            $list['user_name'] = $order_info['s_user_name'];
            $list['user_phone'] = $order_info['s_user_phone'];
            $list['coach_name'] = $order_info['s_coach_name'];
            $list['coach_phone'] = $order_info['s_coach_phone'];
            $list['license_name'] = $order_info['s_lisence_name'];
            $list['lesson_name'] = $order_info['s_lesson_name'];
            $list['price'] = $order_info['dc_money'];
            $list['pay_type'] = $order_info['deal_type'];
            $list['order_status'] = $order_info['i_status'];
            $list['service_time'] = $order_info['i_service_time'];
            $list['zhifu_dm'] = $order_info['s_zhifu_dm'];
            $list['zhifu_time'] = $order_info['dt_zhifu_time'];
            $list['cancel_type'] = $order_info['cancel_type'];
            $list['cancel_reason'] = $order_info['cancel_reason'];
            if ($order_info['cancel_time'] != 0 AND $order_info['cancel_time'] != '') {
                $list['cancel_time'] = date('Y-m-d H:i:s', $order_info['cancel_time']);
            } else {
                $list['cancel_time'] = '--';
            }
            $list['beizhu'] = $order_info['s_beizhu'];

            // 时间段组装
            $list['appoint_time_date'] = '';
            $list['appoint_time'] = '';
            $list['appoint_time_list'] = '';
            $appoint_time_id = $order_info['appoint_time_id'];
            $dt_appoint_time = $order_info['dt_appoint_time'];
            $time_list = $this->getAppointTimeList($appoint_time_id, $dt_appoint_time);
            if ( ! empty($time_list)) {
                $list['appoint_time_date'] = $time_list['appoint_time_date'];
                $list['appoint_time'] = $time_list['appoint_time'];
                $list['appoint_time_list'] = $time_list['appoint_time_list'];
            }

            $user_id = $order_info['l_user_id'];
            $coach_id = $order_info['l_coach_id'];

            // 获取学员信息
            $user_info = $this->getUserInfoByUserId($user_id);
            $list['user_info'] = $user_info;

            // 获取教练信息
            $coach_info = $this->getCoachInfoById($coach_id);
            $list['coach_info'] = $coach_info;
        }
        return $list;
    }

    // 获取学员信息
    public function getUserInfoByUserId($user_id)
    {
        $query = $this->db
            ->select('user_id, sex, age, identity_id, license_name, lesson_name, address, user_photo, license_num, school_id, photo_id')
            ->get_where("{$this->userinfo_tablename}", ['user_id' => $user_id]);
        $user_info = $query->row_array();
        if ( ! empty($user_info) ) {
            $user_info['user_photo'] = $this->mbase->buildUrl($user_info['user_photo']);
            $school_info = $this->mschool->getSchoolInfo($user_info['school_id']);
            $school_name = "嘻哈平台";
            if ( ! empty($school_info)) {
                $school_name = $school_info['s_school_name'];
            }
            $user_info['school_name'] = $school_name;
            if ($user_info['license_name'] == '') {
                $user_info['license_name'] = '--';
            }
            if ($user_info['lesson_name'] == '') {
                $user_info['lesson_name'] = '--';
            }
        }
        return $user_info;
    }

    // 获取教练信息
    public function getCoachInfoById($coach_id)
    {
        $query = $this->db
            ->select(
                's_teach_age as age, 
                 s_coach_name,
                 s_coach_sex as sex,
                 s_coach_imgurl as imgurl, 
                 certification_status, 
                 s_school_name_id as school_id, 
                 s_coach_lesson_id as lesson_id, 
                 s_coach_lisence_id as license_id, 
                 lesson2_pass_rate, lesson3_pass_rate, 
                 i_coach_star as coach_star, 
                 s_coach_address as coach_address, 
                 order_receive_status'
            )
            ->get_where("{$this->coach_tablename}", ['l_coach_id' => $coach_id]);
        $coach_info = $query->row_array();
        $license_confings = $this->mcoach->getLicenseInfo();
        $lesson_confings = $this->mcoach->getLessonInfo();
        if ( ! empty($coach_info) ) {
            $coach_info['imgurl'] = $this->mbase->buildUrl($coach_info['imgurl']);
            $school_info = $this->mschool->getSchoolInfo($coach_info['school_id']);
            $school_name = "嘻哈平台";
            if ( ! empty($school_info)) {
                $school_name = $school_info['s_school_name'];
            }
            $coach_info['school_name'] = $school_name;
            $license_id_arr = explode(',', $coach_info['license_id']);
            $lesson_id_arr = explode(',', $coach_info['lesson_id']);
            $coach_license_ids = [];
            $coach_lesson_ids = [];
            if ( ! empty($license_id_arr) AND count($license_id_arr) > 0) {
                foreach ($license_id_arr as $license_index => $license) {
                    if ( array_key_exists($license, $license_confings)) {
                        $license_name = $license_confings[$license]['license_name'];
                        $coach_license_ids[] = $license_name;
                    }
                }
            }
            $coach_info['license_name'] = implode(',', $coach_license_ids);

            if ( ! empty($lesson_id_arr) AND count($lesson_id_arr) > 0) {
                foreach ($lesson_id_arr as $lesson_index => $lesson) {
                    if ( array_key_exists($lesson, $lesson_confings)) {
                        $lesson_name = $lesson_confings[$lesson]['lesson_name'];
                        $coach_lesson_ids[] = $lesson_name;
                    }
                }
            }
            $coach_info['lesson_name'] = implode(',', $coach_lesson_ids);
        }
        return $coach_info;
    }

    // 获取预约时间段
    public function getAppointTimeList($appoint_time_id, $dt_appoint_time)
    {
        $list = [];
        //  获取预约时间
        $_query = $this->db
            ->get_where("{$this->coach_appoint_time}", ['id' => $appoint_time_id]);
        $appoint_time = $_query->row_array();
        $time_config_id_arr = array();
        if ($appoint_time) {
            $time_config_id_arr = array_filter(explode(',', $appoint_time['time_config_id']));
            // 获取时间配置中的id
            if ($time_config_id_arr) {
                $time_config_time = array();
                $time_config_arr = $this->db->from("{$this->coach_time_config}")
                    ->where_in('id', $time_config_id_arr)
                    ->get()
                    ->result_array();
                if ($time_config_arr) {
                    foreach ($time_config_arr as $time_config_index => $time_config_value) {
                        $time_config_time[] = $time_config_value['start_time'].':00-'.$time_config_value['end_time'].':00';
                    }
                }
                $list['appoint_time_date'] = date("Y-m-d", strtotime($dt_appoint_time));
                $list['appoint_time'] = implode('； ', $time_config_time);
                $list['appoint_time_list'] = date("Y-m-d", strtotime($dt_appoint_time)).' '.implode(' ', $time_config_time);
            } else {
                $list['appoint_time_date'] = date("Y-m-d", strtotime($dt_appoint_time));
                $list['appoint_time_list'] = date("Y-m-d", strtotime($dt_appoint_time));
            }
        }
        return $list;
    }

    // 获取单条预约计时的订单状态
    public function getOrderStatus($id)
    {
        $query = $this->db
            ->get_where("{$this->timingorder_tablename}", ['l_study_order_id' => $id]);
        $study_order_info = $query->row_array();
        $order_status = 0;
        if ( ! empty($study_order_info)) {
            $order_status = (int)$study_order_info['i_status'];
        }
        return $order_status;
    }

    // 获取条件下预约计时的数据页码和总数
    public function getTimingOrderPageNumByCondition($tablename, $order_status, $pay_type, $limit) {
        $count = $this->db->where('i_status', $order_status)->where_in('deal_type', $pay_type)->count_all_results($tablename);
        return ['pn'=>(int) ceil($count / $limit), 'count'=>$count];
    }

    // 获取条件下的预约计时订单列表
    public function getTimingOrderListByCondition($start='', $limit='', $order_status, $pay_type) {
        $query = $this->db->where('i_status', $order_status)->where_in('deal_type', $pay_type)->order_by('l_study_order_id', 'DESC')->get($this->timingorder_tablename, $limit, $start);
        foreach($query->result() as $key => $value) {
            $query->result()[$key]->user_info = $this->muser->getUserInfoByCondition($this->muser->userinfo_tablename, ['user_id'=>$value->l_user_id]);
            $query->result()[$key]->addtime = date('Y-m-d H:i:s', $value->addtime);
        }
        return ['list'=>$query->result()];
    }

    // 获取订单的价格统计
    public function getSchoolOrderTotalCountByCondition($tablename, $order_status, $pay_type, $select) {
        $query = $this->db->select($select)->where('so_order_status', $order_status)->where_in('so_pay_type', $pay_type)->get($tablename);
        return ['list'=>$query->result()];
    }

}
?>
