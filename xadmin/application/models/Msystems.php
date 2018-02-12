<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Msystems extends CI_Model {

    public $action_tbl = 'cs_actions';
    public $action_log_tbl = 'cs_action_log';
    public $admin_tbl = 'cs_admin';
    public $roles_tbl = 'cs_roles';
    public $tag_config_tbl = 'cs_system_tag_config';
    public $pay_config_tbl = 'cs_pay_account_config';
    public $user_tbl = 'cs_user';
    public $coach_tbl = 'cs_coach';
    public $sms_tbl = 'cs_sms_sender';
    public $school_config_tbl = 'cs_school_config';
    public $school_tbl = 'cs_school';
    public $coach_time_config_tbl = 'cs_coach_time_config';
    public $user_tag_tbl = 'cs_user_tag';
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

// 1.系统日志
    /**
     * 获取系统日志的页数信息
     * @param 
     * @return void
     **/
    public function getSystemsPageNum($param, $role_id = NULL, $school_id = NULL, $limit, $type)
    {
        $map = [];
        $complex = [];
        if ( $type == 'log') {
            if ($role_id != 1 AND $school_id == 0) {

                $map['roles.l_role_id'] = $role_id;
                $map['admin.school_id'] = 0;

            } elseif ($role_id != 1 AND $school_id != 0) {

                $map['roles.l_role_id'] = $role_id;
                $map['admin.school_id'] = $role_id;
            }

            if ($param) {
                if ($param['role'] != '') {
                    $map['roles.l_role_id'] = $param['role'];
                }

                if ($param['keywords'] != '') {
                    $complex['admin.content'] = $param['keywords'];
                    $complex['action.title'] = $param['keywords'];
                }

            }

            if ( ! empty($complex)) {
                $query = $this->db
                    ->from("{$this->action_log_tbl} as log")
                    ->join("{$this->action_tbl} as action", "action.id = log.action_id", "LEFT")
                    ->join("{$this->admin_tbl} as admin", "admin.id = log.user_id", "LEFT")
                    ->join("{$this->roles_tbl} as roles", "roles.l_role_id = admin.role_id", "LEFT")
                    ->where($map)
                    ->group_start()
                        ->or_like($complex)
                    ->group_end();

            } else {

                $query = $this->db
                    ->from("{$this->action_log_tbl} as log")
                    ->join("{$this->action_tbl} as action", "action.id = log.action_id", "LEFT")
                    ->join("{$this->admin_tbl} as admin", "admin.id = log.user_id", "LEFT")
                    ->join("{$this->roles_tbl} as roles", "roles.l_role_id = admin.role_id", "LEFT")
                    ->where($map);

            }
        } elseif ($type == 'action') {
            if ($param) {
                if ($param['status'] != 0) {
                    $map['status'] = $param['status'];
                } 

                if ($param['keywords']) {
                    $complex['name'] = $param['keywords'];
                    $complex['title'] = $param['keywords'];
                }
            }

            if ( ! empty($complex)) {
                $query = $this->db 
                    ->from($this->action_tbl)
                    ->where($map)
                    ->group_start()
                        ->or_like($complex)
                    ->group_end();
            } else {
                $query = $this->db 
                    ->from($this->action_tbl)
                    ->where($map);
            }

        } elseif ( $type == 'tag') {

            if ($param) {
                if ($param['user_type'] != '') {
                    $map['user_type'] = $param['user_type'];
                }
                if ($param['keywords'] != '') {
                    $complex['tag_name'] = $param['keywords'];
                    $complex['tag_slug'] = $param['keywords'];
                }
            }

            if ( ! empty($complex)) {
                $query = $this->db
                    ->from($this->tag_config_tbl)
                    ->where($map)
                    ->group_start()
                        ->or_like($complex)
                    ->group_end();
            } else {
                $query = $this->db
                    ->from($this->tag_config_tbl)
                    ->where($map);
            }

        } elseif ( $type == 'utag') {

            if ($param) {
                if ($param['user_type'] != '') {
                    $map['user_type'] = $param['user_type'];
                }

                if ($param['is_system'] != '') {
                    $map['is_system'] = $param['is_system'];
                }

                if ($param['keywords'] != '') {
                    $complex['tag_name'] = $param['keywords'];
                    $complex['tag_slug'] = $param['keywords'];
                }

            }

            if ( ! empty($complex)) {
                $query = $this->db
                    ->from($this->user_tag_tbl)
                    ->where($map)
                    ->group_start()
                        ->or_like($complex)
                    ->group_end();
            } else {
                $query = $this->db
                    ->from($this->user_tag_tbl)
                    ->where($map);
            }

        } elseif ($type == 'pay') {

            if ($param) {
                if ($param['is_open'] != '') {
                    $map['is_open'] = $param['is_open'];
                }

                if ($param['is_bank'] != '') {
                    $map['is_bank'] = $param['is_bank'];
                }

                if ($param['pay_type'] != '') {
                    $map['pay_type'] = $param['pay_type'];
                }

                if ($param['keywords'] != '') {
                    $complex['account_name'] = $param['keywords'];
                    $complex['account_slug'] = $param['keywords'];
                }
            }

            if ( ! empty($complex)) {
                $query = $this->db
                    ->from($this->pay_config_tbl)
                    ->where($map)
                    ->group_start()
                        ->or_like($complex)
                    ->group_end();
            } else {
                $query = $this->db
                    ->from($this->pay_config_tbl)
                    ->where($map);
            }

        } elseif ($type == 'message') {

            $map['member_type'] = 1;
            if ($param) {
                if ($param['member_type'] != '') {
                    $map['member_type'] = $param['member_type'];
                }

                if ($param['i_yw_type'] != '') {
                    $map['i_yw_type'] = $param['i_yw_type'];
                }

                if ($param['is_read'] != '') {
                    $map['is_read'] = $param['is_read'];
                } 

                $complex['s_beizhu'] = $param['keywords'];
                // $complex['s_content'] = $param['keywords'];

                if ($map['member_type'] == 1) {
                    $map['user.i_user_type'] = 0; 
                    $complex['user.s_real_name'] = $param['keywords'];
                    $complex['user.s_phone'] = $param['keywords'];

                } else {
                    $complex['coach.s_coach_name'] = $param['keywords'];
                    $complex['coach.s_coach_phone'] = $param['keywords'];
                }

            }
            if ($map['member_type'] == 1) {
                $count = $this->db
                    ->from("{$this->sms_tbl} as sms")
                    ->join("{$this->user_tbl} as user", "user.l_user_id = sms.member_id", "LEFT")
                    ->where($map)
                    ->where_in('is_read', [1, 2])
                    ->group_start()
                        ->or_like($complex)
                    ->group_end()
                    ->count_all_results();
            } else {
                $count = $this->db
                    ->from("{$this->sms_tbl} as sms")
                    ->join("{$this->coach_tbl} as coach", "coach.l_coach_id = sms.member_id", "LEFT")
                    ->where($map)
                    ->where_in('is_read', [1, 2])
                    ->group_start()
                        ->or_like($complex)
                    ->group_end()
                    ->count_all_results();
            }

        } elseif ($type == 'sconf') {
            if ( $school_id != '') {
                $map['sconf.l_school_id'] = $school_id;
            }

            if ($param) {
                if ($param['is_auto'] != '') {
                    $map['is_automatic'] = $param['is_auto'];
                }

                if ($param['keywords'] != '') {
                    $complex['school.s_school_name'] = $param['keywords'];
                }
            }

            if ( ! empty($complex)) {

                $query = $this->db
                    ->from("{$this->school_config_tbl} as sconf")
                    ->join("{$this->school_tbl} as school", "school.l_school_id = sconf.l_school_id", "LEFT")
                    ->where($map)
                    ->where('school.l_school_id > 0')
                    ->like($complex);

            } else {

                $query = $this->db
                    ->from("{$this->school_config_tbl} as sconf")
                    ->join("{$this->school_tbl} as school", "school.l_school_id = sconf.l_school_id", "LEFT")
                    ->where($map)
                    ->where('school.l_school_id > 0')
                    ->like($complex);
            }

        }

        if ($type == 'message') {
            $page_num = (int) ceil( $count / $limit);
        } else {
            $count = $query->count_all_results();
            $page_num = (int) ceil( $count / $limit);
        }

        $page_info = [
            'pagenum'   => $page_num,
            'count'     => $count
        ];

        return $page_info;

    }

    /**
     * 获取系统日志中的数据
     * @param $data = [$param, $role_id, $school_id, $start, $limit, $type]
     * @return list
     **/
    public function getSystemsList($param, $role_id = NULL, $school_id = NULL, $start, $limit, $type)
    {
        $map = [];
        $complex = [];

        if ( $type == 'log') {

            if ($role_id != 1 AND $school_id == 0) {
                $map['roles.l_role_id'] = $role_id;
                $map['admin.school_id'] = 0;

            } elseif ($role_id != 1 AND $school_id != 0) {
                $map['roles.l_role_id'] = $role_id;
                $map['admin.school_id'] = $role_id;
            }

            if ($param) {
                if ($param['role'] != '') {
                    $map['roles.l_role_id'] = $param['role'];
                }

                $complex['admin.content'] = $param['keywords'];
                $complex['action.title'] = $param['keywords'];
            }

            $list = $this->db
                ->select(
                    'log.*,
                     admin.content,
                     roles.l_role_id,
                     roles.s_rolename,
                     action.title'
                )
                ->from("{$this->action_log_tbl} as log")
                ->join("{$this->action_tbl} as action", "action.id = log.action_id", "LEFT")
                ->join("{$this->admin_tbl} as admin", "admin.id = log.user_id", "LEFT")
                ->join("{$this->roles_tbl} as roles", "roles.l_role_id = admin.role_id", "LEFT")
                ->where($map)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->order_by('log.id', 'desc')
                ->limit($limit, $start)
                ->get()
                ->result_array();

            if ( ! empty($list)) {
                foreach ($list as $key => $value) {
                    if ( $value['create_time'] != '' AND $value['create_time'] != 0) {

                        $list[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
                    } else {
                        $list[$key]['create_time'] = '--';
                    }

                }
            }


        } elseif ( $type == 'action') {
            if ($param) {
                if ($param['status'] != 0) {
                    $map['status'] = $param['status'];
                } 

                $complex['name'] = $param['keywords'];
                $complex['title'] = $param['keywords'];
            }

            $list = $this->db 
                ->from($this->action_tbl)
                ->where($map)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->order_by('id', 'desc')
                ->limit($limit, $start)
                ->get()
                ->result_array();
            if ( ! empty($list)) {
                foreach ($list as $key => $value) {
                    if ($value['add_time'] != '' AND $value['add_time'] != 0) {
                        $list[$key]['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
                    } else {
                        $list[$key]['add_time'] = '--';
                    }

                    if ($value['update_time'] != '' AND $value['update_time'] != 0) {
                        $list[$key]['update_time'] = date('Y-m-d H:i:s', $value['update_time']);
                    } else {
                        $list[$key]['update_time'] = '--';
                    }
                }
            }

        } elseif ( $type == 'tag') {
            if ($param) {
                if ($param['user_type'] != '') {
                    $map['user_type'] = $param['user_type'];
                }

                $complex['tag_name'] = $param['keywords'];
                $complex['tag_slug'] = $param['keywords'];
            }

            $list = $this->db 
                ->from($this->tag_config_tbl)
                ->where($map)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->order_by('order', 'desc')
                ->order_by('id', 'desc')
                ->limit($limit, $start)
                ->get()
                ->result_array();
                
            if ( ! empty($list)) {
                foreach ($list as $key => $value) {
                    if ($value['addtime'] != '' AND $value['addtime'] != 0) {
                        $list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                    } else {
                        $list[$key]['addtime'] = '--';
                    }

                    if ($value['updatetime'] != '' AND $value['updatetime'] != 0) {
                        $list[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                    } else {
                        $list[$key]['updatetime'] = '--';
                    }
                }
            }

        } elseif ( $type == 'utag') {

            if ($param) {
                if ($param['user_type'] != '') {
                    $map['user_type'] = $param['user_type'];
                }

                if ($param['is_system'] != '') {
                    $map['is_system'] = $param['is_system'];
                }

                // if ($param['keywords'] != '') {
                    $complex['tag_name'] = $param['keywords'];
                    $complex['tag_slug'] = $param['keywords'];
                // }
            }

            $list = $this->db 
                ->from($this->user_tag_tbl)
                ->where($map)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->order_by('order', 'desc')
                ->order_by('id', 'desc')
                ->limit($limit, $start)
                ->get()
                ->result_array();
                
            if ( ! empty($list)) {
                foreach ($list as $key => $value) {
                    if ($value['addtime'] != '' AND $value['addtime'] != 0) {
                        $list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                    } else {
                        $list[$key]['addtime'] = '--';
                    }

                    if ($value['updatetime'] != '' AND $value['updatetime'] != 0) {
                        $list[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                    } else {
                        $list[$key]['updatetime'] = '--';
                    }
                }
            }


        } elseif ( $type == 'pay') {

            if ($param) {
                if ($param['is_open'] != '') {
                    $map['is_open'] = $param['is_open'];
                }

                if ($param['is_bank'] != '') {
                    $map['is_bank'] = $param['is_bank'];
                }

                if ($param['pay_type'] != '') {
                    $map['pay_type'] = $param['pay_type'];
                }

                $complex['account_name'] = $param['keywords'];
                $complex['account_slug'] = $param['keywords'];
            }

            $list = $this->db 
                ->from($this->pay_config_tbl)
                ->where($map)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->order_by('order', 'desc')
                ->order_by('id', 'desc')
                ->limit($limit, $start)
                ->get()
                ->result_array();
                
            if ( ! empty($list)) {
                foreach ($list as $key => $value) {

                    if ($value['addtime'] != '' AND $value['addtime'] != 0) {
                        $list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                    } else {
                        $list[$key]['addtime'] = '--';
                    }

                    if ($value['pay_type'] == 0 OR $value['pay_type'] == '') {
                        $list[$key]['pay_type'] = 0;
                    }

                }
            }

        } elseif ($type == "message") {

            $map['member_type'] = 1;
            if ($param) {
                if ($param['member_type'] != '') {
                    $map['member_type'] = $param['member_type'];
                }

                if ($param['i_yw_type'] != '') {
                    $map['i_yw_type'] = $param['i_yw_type'];
                }

                if ($param['is_read'] != '') {
                    $map['is_read'] = $param['is_read'];
                } 

                $complex['s_beizhu'] = $param['keywords'];
                // $complex['s_content'] = $param['keywords'];

                if ($map['member_type'] == 1) {
                    $map['user.i_user_type'] = 0; 
                    $complex['user.s_real_name'] = $param['keywords'];
                    $complex['user.s_phone'] = $param['keywords'];

                } else {
                    $complex['coach.s_coach_name'] = $param['keywords'];
                    $complex['coach.s_coach_phone'] = $param['keywords'];
                }

            }

            if ($map['member_type'] == 1) {
                $list = $this->db 
                    ->from("{$this->sms_tbl} as sms")
                    ->join("{$this->user_tbl} as user", "user.l_user_id = sms.member_id", "LEFT")
                    ->select(
                        'sms.*,
                         user.l_user_id,
                         user.s_real_name as user_name,
                         user.s_phone as user_phone'
                    )
                    ->where($map)
                    ->where_in('is_read', [1, 2])
                    ->group_start()
                        ->or_like($complex)
                    ->group_end()
                    ->order_by('sms.id', 'desc')
                    ->limit($limit, $start)
                    ->get()
                    ->result_array();
            } else {
                $list = $this->db 
                    ->from("{$this->sms_tbl} as sms")
                    ->join("{$this->coach_tbl} as coach", "coach.l_coach_id = sms.member_id", "LEFT")
                    ->select(
                        'sms.*,
                         coach.s_coach_name as user_name,
                         coach.s_coach_phone as user_phone'
                    )
                    ->where($map)
                    ->where_in('is_read', [1, 2])
                    ->group_start()
                        ->or_like($complex)
                    ->group_end()
                    ->order_by('sms.id', 'desc')
                    ->limit($limit, $start)
                    ->get()
                    ->result_array();
            }

            if ( ! empty($list)) {
                foreach ($list as $key => $value) {

                    if ($value['addtime'] != '' AND $value['addtime'] != 0) {
                        $list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                    } else {
                        $list[$key]['addtime'] = '--';
                    }

                    if ($value['dt_sender'] != '' AND $value['dt_sender'] != 0) {
                        $list[$key]['dt_sender'] = date('Y-m-d H:i:s', $value['dt_sender']);
                    } else {
                        $list[$key]['dt_sender'] = '--';
                    }

                }
            }

        } elseif ( $type == 'sconf') {

            if ( $school_id != '') {
                $map['sconf.l_school_id'] = $school_id;
            }

            if ($param) {
                if ($param['is_auto'] != '') {
                    $map['is_automatic'] = $param['is_auto'];
                }

                // if ($param['keywords'] != '') {
                $complex['school.s_school_name'] = $param['keywords'];
                // }
            }


            $list = $this->db
                ->select(
                    'sconf.l_school_id as id,
                     sconf.i_cancel_order_time as cancel_order_time,
                     sconf.i_sum_appoint_time as sum_appoint_time,
                     sconf.s_time_list as time_list,
                     sconf.is_automatic as is_auto,
                     sconf.cancel_in_advance,
                     school.s_school_name as school_name'
                )
                ->from("{$this->school_config_tbl} as sconf")
                ->join("{$this->school_tbl} as school", "school.l_school_id = sconf.l_school_id", "LEFT")
                ->where($map)
                ->where('school.l_school_id > 0')
                ->like($complex)
                ->order_by('sconf.l_school_id', 'desc')
                ->limit($limit, $start)
                ->get()
                ->result_array();

        }

        $page_info = $this->getSystemsPageNum($param, $role_id, $school_id, $limit, $type);
        $count = $page_info['count'];
        $pagenum = $page_info['pagenum'];

        $data = [
            'list' => $list,
            'pagenum' => $pagenum,
            'count' => $count
        ];

        return $data;

    }

    /**
     * 新增数据
     * @param $data
     * @param $tblname
     * @return void
     **/
    public function add($data, $tblname)
    {
         $result = $this->db
            ->insert($tblname, $data);
        return $this->db->insert_id();
    }

    /**
     * 修改数据
     * @param $data
     * @param $tblname
     * @return void
     **/
    public function edit($field, $data, $tblname)
    {
        if ($tblname == 'cs_school_config') {
            $id = $data['l_school_id'];
        } else {
            $id = $data['id'];
        }
        $condition = [
            $field => $id
        ];
        $result = $this->db
            ->from($tblname)
            ->where($condition)
            ->update($tblname, $data);
        return $result;
    }

    /**
     * 搜索[学员 | 教练]
     * @param 
     * @return void
     **/
    public function search($key, $type, $limit)
    {
        $map = [];
        $where = [];
        if ($type == 'stu') 
        {                   // 学员
            $map['i_user_type'] = 0;
            $map['i_status'] = 0;
            $where['s_phone'] = $key;
            $list = $this->db 
                ->select(
                    'l_user_id,
                     s_username,
                     s_phone,
                     s_real_name'
                )
                ->limit($limit)
                ->where($map)
                ->like($where)
                ->get($this->user_tbl, $limit)
                ->result();
        } 
        elseif ( $type == 'coach')  // 教练
        {
            $where['s_coach_phone'] = $key;
            $map['user.i_user_type'] = 1;
            $map['user.i_status'] = 0;
            $list = $this->db 
                ->from("{$this->coach_tbl} as coach")
                ->join("{$this->user_tbl} as user", "user.l_user_id = coach.user_id", "LEFT")
                ->where($map)
                ->like($where)
                ->limit($limit)
                ->get()
                ->result();

        }
        return $list;
    }

    /**
     * 获取驾校时间配置信息（通过驾校ID）
     * @param $coach_id;
     * @return void
     **/
    public function getSchoolConfigById($id) 
    {   
        $condition = ['school_config.l_school_id' => $id];
        $school_config_list = $this->db 
            ->select(
                'school_config.l_school_id,
                 school_config.i_cancel_order_time as cancel_order_time,
                 school_config.i_sum_appoint_time as sum_appoint_time,
                 school_config.s_time_list as time_list,
                 school_config.is_automatic as is_auto,
                 school_config.cancel_in_advance as cancel_in_advance,
                 school.l_school_id as school_id,
                 school.s_school_name as school_name'
            )
            ->from("{$this->school_config_tbl} as school_config")
            ->join("{$this->school_tbl} as school", "school.l_school_id = school_config.l_school_id", "LEFT")
            ->where($condition)
            ->get()
            ->result_array();

        $school_config_arr = [];

        if ( $school_config_list) {
            foreach ($school_config_list as $key => $value) {
                $school_config_arr = $value;
                // $school_config_arr['timelist'] = explode(',', $value['time_list']);
            }
        }
        
        return $school_config_arr;

    }

    /**
     * 获取教练信息（通过教练ID）
     * @param $coach_id;
     * @return void
     **/
    public function getCoachInfoById($coach_id)
    {      
        $condition = [
            'l_coach_id' => $coach_id,
            'user.i_user_type' => 1,
            'user.i_status' => 0,
        ];

        $coach_list = $this->db 
            ->from("{$this->coach_tbl} as coach")
            ->join("{$this->user_tbl} as user", "user.l_user_id = coach.user_id", "LEFT")
            ->select(
                'l_coach_id,
                 s_coach_phone,
                 s_coach_name'
            )
            ->where($condition)
            ->get()
            ->result_array();
        $coach_info = [];
        if ( ! empty($coach_list)) {
            foreach ($coach_list as $key => $value) {
                $coach_info = $value;
            }
        }

        return $coach_info;

    }

    /**
     * 获取单条数据
     * @param id
     * @return void
     **/
    public function getTagListById($id)
    {
        $where = ['id' => $id];
        $tag_list = $this->db
            ->from($this->tag_config_tbl)
            ->where($where)
            ->get()
            ->result_array();
        $list = [];
        if ( ! empty($tag_list)) {
            foreach ($tag_list as $index => $value) {
                $list = $value;
            }
        }

        return $list;

    }

    /**
     * 获取单条数据[日志行为]
     * @param id
     * @return void
     **/
    public function getActionListById($id)
    {
        $where = ['id' => $id];
        $action_list = $this->db
            ->from($this->action_tbl)
            ->where($where)
            ->get()
            ->result_array();
        $list = [];
        if ( ! empty($action_list)) {
            foreach ($action_list as $index => $value) {
                $list = $value;
            }
        }

        return $list;
    }

    /**
     * 获取单条支付配置数据
     * @param id
     * @return void
     **/
    public function getPayConfigById($id)
    {
        $where = ['id' => $id];
        $payconfiglist = $this->db
            ->from($this->pay_config_tbl)
            ->where($where)
            ->get()
            ->result_array();
        $list = [];
        if ( ! empty($payconfiglist)) {
            foreach ($payconfiglist as $index => $value) {
                $list = $value;
            }
        }

        return $list;
    }

    /**
     * 删除数据
     * @param  $id
     * @return void
     **/
    public function del($field, $value, $tblname)
    {
        $result = $this->db
            ->from($tblname)
            ->where_in($field, $value)
            ->delete();
        return $result;
    }
    
    /**
     * 获取时间配置列表
     * @param $school_id 
     * @return coach_time_config_list
     **/
    public function getCoachTimeConfigList($school_id = NULL)
    {
        $map = [];
        $school_config_arr = [];
        $time_arr = [];
        if ($school_id != '') {
            $map['l_school_id'] = $school_id;
            $school_config_list = $this->getSchoolConfigById($school_id);
            $time_list = $school_config_list['time_list'];
            $time_arr = explode(',', $time_list);
        }

        $list = $this->db 
            ->from($this->coach_time_config_tbl)
            ->where('status', 1)
            ->order_by('start_time', 'asc')
            ->get()
            ->result_array();
        
        if ( ! empty($list)) {
            foreach ($list as $key => $value) {

                $list[$key]['start'] = (($value['start_time'] < 10) ? '0'.$value['start_time'] : $value['start_time']).':'.(($value['start_minute'] < 10) ? '0'.$value['start_minute'] : $value['start_minute']);

                $list[$key]['end'] = (($value['end_time'] < 10) ? '0'.$value['end_time'] : $value['end_time']).':'.(($value['end_minute'] < 10) ? '0'.$value['end_minute'] : $value['end_minute']);
                if ( in_array($value['id'], $time_arr)) {
                    $list[$key]['is_selected'] = 1;
                } else {
                    $list[$key]['is_selected'] = 2;
                }

            }
        }

        return $list;
    }

    /**
     * 检查驾校设置否重复
     * @param school_id
     * @return $result
     **/
    public function checkSchoolConfig($school_id)
    {   
        $result = $this->db
            ->select(
                'school_config.l_school_id,
                 school.s_school_name as school_name'
            )
            ->from("{$this->school_config_tbl} as school_config")
            ->join("{$this->school_tbl} as school", "school.l_school_id = school_config.l_school_id ", 'LEFT')
            ->where('school.l_school_id', $school_id)
            ->get()
            ->result_array();
        $list = [];
        if ( ! empty($result)) {
            foreach ($result as $key => $value) {
                $list = $value;
            }
        }
        return $list;
    }

}