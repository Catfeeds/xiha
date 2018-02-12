<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcoach extends CI_Model {

    public $coach_tablename = 'cs_coach';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->license_tbl = $this->db->dbprefix('license_config');
        $this->lesson_tbl = $this->db->dbprefix('lesson_config');
        $this->coach_tbl = $this->db->dbprefix('coach');
        $this->school_tbl = $this->db->dbprefix('school');
        $this->cars_tbl = $this->db->dbprefix('cars');
        $this->user_tbl = $this->db->dbprefix('user');
        $this->province_tbl = $this->db->dbprefix('province');
        $this->city_tbl = $this->db->dbprefix('city');
        $this->area_tbl = $this->db->dbprefix('area');
        $this->coach_user_rela_tbl = $this->db->dbprefix('coach_user_relation');
        $this->coach_time_config_tbl = $this->db->dbprefix('coach_time_config');
        $this->coach_comment_tbl = $this->db->dbprefix('coach_comment');
        $this->school_shifts_tbl = $this->db->dbprefix('school_shifts');
        $this->school_orders_tbl = $this->db->dbprefix('school_orders');
    }
// 1.教练列表
    /**
     * 获取教练列表页码信息
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $limit      限定条数
     * @return  $pageinfo
     **/
    public function getCoachPageNum($school_id, $param, $limit)
    {
        $map = [];
        $complex = [];
        $map['user.i_user_type'] = 1;
        if ($school_id != '') {
            $map['school.l_school_id'] = $school_id;
        }
        if ($param) {
            if ($param['status'] !== '') {
                $map['user.i_status'] = (int)$param['status'];
            }
            if ($param['star'] != '') {
                $map['coach.i_coach_star'] = $param['star'];
            }
            if ($param['verify'] != '') {
                $map['coach.certification_status'] = $param['verify'];
            }
            $keywords = $param['keywords'];
            if ( $keywords != '') {
                $complex = [
                    'l_coach_id' => $keywords,
                    's_coach_name' => $keywords,
                    's_coach_phone' => $keywords,
                    'school.s_school_name' => $keywords,
                ];
            }
        }
        $query = $this->db 
            ->from("{$this->coach_tbl} as coach")
            ->join("{$this->user_tbl} as user", "user.l_user_id=coach.user_id", "LEFT")
            ->join("{$this->school_tbl} as school", "school.l_school_id=coach.s_school_name_id", "LEFT")
            // ->join("{$this->cars_tbl} as cars", "cars.id=coach.s_coach_car_id", "LEFT")
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }
        $count = $query->count_all_results();
        return ['pagenum' => (int) ceil($count / $limit), 'count' => $count];
    }

    /**
     * 获取教练列表
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $start      开始数
     * @param   int     $limit      限定条数
     * @return  void
     **/
    public function getCoachList($school_id, $param, $start, $limit)
    {
        $map = [];
        $complex = [];
        $map['user.i_user_type'] = 1;
        if ($school_id != '') {
            $map['school.l_school_id'] = $school_id;
        }
        if ($param) {
            if ($param['status'] !== '') {
                $map['user.i_status'] = (int)$param['status'];
            }
            if ($param['star'] != '') {
                $map['coach.i_coach_star'] = $param['star'];
            }
            if ($param['verify'] != '') {
                $map['coach.certification_status'] = $param['verify'];
            }
            $keywords = $param['keywords'];
            if ( $keywords != '') {
                $complex = [
                    'l_coach_id' => $keywords,
                    's_coach_name' => $keywords,
                    's_coach_phone' => $keywords,
                    'school.s_school_name' => $keywords,
                ];
            }
        }
        $query = $this->db 
            ->select(
                'coach.*,
                 user.i_status,
                 school.s_school_name as school_name'
            )
            ->from("{$this->coach_tbl} as coach")
            ->join("{$this->user_tbl} as user", "user.l_user_id=coach.user_id", "LEFT")
            ->join("{$this->school_tbl} as school", "school.l_school_id=coach.s_school_name_id", "LEFT")
            // ->join("{$this->cars_tbl} as cars", "cars.id=coach.s_coach_car_id", "LEFT")
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }
        $list = $query
            ->limit($limit, $start)
            // ->order_by('i_order', 'desc')
            ->order_by('l_coach_id', 'desc')
            ->get()
            ->result_array();
        // 获取驾照表中的所有字段与值
        $license_configs = $this->getLicenseInfo();
        // 获取科目表中的所有字段与值
        $lesson_configs = $this->getLessonInfo();
        if ( ! empty($list)) {
            foreach ($list as $index => $coach) {
                $_coach_license_id = explode(',', $coach['s_coach_lisence_id']);
                $_coach_lesson_id = explode(',', $coach['s_coach_lesson_id']);
                $coach_lesson_ids = array();
                $coach_license_ids = array();
                if ($_coach_license_id && count($_coach_license_id) > 0) {
                    foreach ($_coach_license_id as $coach_license_index => $coach_license_id) {
                        if (array_key_exists($coach_license_id, $license_configs)) { // 或者isset也可以
                            $license_name = $license_configs[$coach_license_id]['license_name'];
                            $coach_license_ids[] = $license_name;
                        }
                    }
                }

                if ($_coach_lesson_id && count($_coach_lesson_id) > 0) {
                    foreach ($_coach_lesson_id as $coach_lesson_index => $coach_lesson_id) {
                        if (array_key_exists($coach_lesson_id, $lesson_configs)) {
                            $lesson_name = $lesson_configs[$coach_lesson_id]['lesson_name'];
                            $coach_lesson_ids[] = $lesson_name;
                        }
                    }
                }
                $list[$index]['coach_license'] = implode(',', $coach_license_ids);
                $list[$index]['coach_lesson'] = implode(',', $coach_lesson_ids);

                if ($coach['s_coach_content'] == '') { // 教练签名
                    $list[$index]['s_coach_content'] = '--';
                }
                
                if ($coach['s_coach_address'] == '') {
                    $list[$index]['s_coach_address'] = '--';
                }

                if ($coach['school_name'] == '') {
                    $list[$index]['school_name'] = '--';
                }

                if ($coach['s_teach_age'] == '') {
                    $list[$index]['s_teach_age'] = '0';
                }

                if ($coach['must_bind'] === '0') {
                    $list[$index]['must_bind_text'] = '未设置';

                } else if ($coach['must_bind'] == '1') {
                    $list[$index]['must_bind_text'] = '需绑定';

                } else if ($coach['must_bind'] == '2') {
                    $list[$index]['must_bind_text'] = '不需绑';

                }

                if ($coach['addtime'] != 0) {
                    $list[$index]['addtime'] = date('Y-m-d H:i:s', $coach['addtime']);
                } else {
                    $list[$index]['addtime'] = '--';
                }

                if ($coach['updatetime'] != 0) {
                    $list[$index]['updatetime'] = date('Y-m-d H:i:s', $coach['updatetime']);
                } else {
                    $list[$index]['updatetime'] = '--';
                }

                if ($coach['s_coach_name'] == '') {
                    $list[$index]['s_coach_name'] = '--';
                }

                if ($coach['s_coach_phone'] == '') {
                    $list[$index]['s_coach_phone'] = '--';
                }

                if ($coach['s_coach_lesson_id'] == '') {
                    $list[$index]['coach_lesson'] = '--';
                }

                if ($coach['s_coach_lisence_id'] == '') {
                    $list[$index]['coach_license'] = '--';
                }

                if ($coach['lesson2_pass_rate'] == '') {
                    $list[$index]['lesson2_pass_rate'] = '--';
                } else {
                    $empty = explode('%', $coach['lesson2_pass_rate']);
                    if (count($empty) > 1) {
                        $coach['lesson2_pass_rate'] = str_replace('%', '', $coach['lesson2_pass_rate']);
                    }
                    $list[$index]['lesson2_pass_rate'] = $coach['lesson2_pass_rate'];
                }

                if ($coach['lesson3_pass_rate'] == '') {
                    $list[$index]['lesson3_pass_rate'] = '--';
                } else {
                    $empty = explode('%', $coach['lesson3_pass_rate']);
                    if (count($empty) > 1) {
                        $coach['lesson3_pass_rate'] = str_replace('%', '', $coach['lesson3_pass_rate']);
                    }
                    $list[$index]['lesson3_pass_rate'] = $coach['lesson3_pass_rate'];
                }

                if ($coach['s_coach_car_id'] != '') {
                    $cars_info = $this->getCoachCarsInfo($coach['s_coach_car_id']);
                    if ( ! empty($cars_info) AND $cars_info['name'] != '') {
                        $list[$index]['car_name'] = $cars_info['name'];
                    } else {
                        $list[$index]['car_name'] = '--';
                    }
                } else {
                    $list[$index]['car_name'] = '--';
                }

            }

        }
        return $list;
    }

    // 获取教练车辆信息
    public function getCoachCarsInfo($car_id) 
    {
        $condition = ['cars.id' => $car_id];
        $car_info = $this->db
            ->select('cars.name')
            ->from("{$this->cars_tbl} as cars")
            ->where($condition)
            ->get()
            ->row_array();
        return $car_info;
    }

// 2.学员绑定教练列表
     /**
     * 获取学员绑定教练列表表页码信息
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $limit      限制条件
     * @return  void
     **/
    public function getcoachUserPageNum($school_id, $param, $limit)
    {
        $map = [];
        $complex = [];
        $map['user.i_user_type'] = 0;
        $map['user.i_status'] = 0;
        if ($school_id != 0) {
            $map['school.l_school_id'] = $school_id;
        }
       
        if ($param) {
            if ($param['status'] != '') {
                $map['bind_status'] = $param['status'];
            }

            $keywords = $param['keywords'];
            if ($keywords != '') {
                $complex['s_coach_name'] = $keywords;
                $complex['s_coach_phone'] = $keywords;
                $complex['s_username'] = $keywords;
                $complex['s_real_name'] = $keywords;
                $complex['s_phone'] = $keywords;
                $complex['school.s_school_name'] = $keywords;
            }
        }
        
        $query = $this->db 
            ->from("{$this->coach_user_rela_tbl} as relation")
            ->join("{$this->user_tbl} as user",'user.l_user_id=relation.user_id','left')
            ->join("{$this->coach_tbl} as coach",'coach.l_coach_id=relation.coach_id','left')
            ->join("{$this->school_tbl} as school",'school.l_school_id=coach.s_school_name_id','left')
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }
        $count = $query->count_all_results();
        return ['pagenum' => (int) ceil($count / $limit), 'count' => $count];
    }

    /**
     * 获取学员绑定教练列表表页码信息
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $limit      限制条件
     * @return  void
     **/
    public function getCoachUserList($school_id, array $param, $start = 0, $limit = 10)
    {
        $map = [];
        $complex = [];
        $map['user.i_user_type'] = 0;
        $map['user.i_status'] = 0;
        if ($school_id != 0) {
            $map['school.l_school_id'] = $school_id;
        }
       
        if ($param) {
            if ($param['status'] != '') {
                $map['bind_status'] = $param['status'];
            }

            $keywords = $param['keywords'];
            if ($keywords != '') {
                $complex['s_coach_name'] = $keywords;
                $complex['s_coach_phone'] = $keywords;
                $complex['s_username'] = $keywords;
                $complex['s_real_name'] = $keywords;
                $complex['s_phone'] = $keywords;
                $complex['school.s_school_name'] = $keywords;
            }
        }
        
        $query = $this->db 
            ->select(
                'relation.*,
                 coach.s_coach_name as coach_name,
                 coach.s_coach_phone as coach_phone,
                 user.s_real_name as real_name,
                 user.s_username as user_name,
                 user.s_phone as user_phone,
                 school.s_school_name as school_name'
            )
            ->from("{$this->coach_user_rela_tbl} as relation")
            ->join("{$this->user_tbl} as user",'user.l_user_id=relation.user_id','left')
            ->join("{$this->coach_tbl} as coach",'coach.l_coach_id=relation.coach_id','left')
            ->join("{$this->school_tbl} as school",'school.l_school_id=coach.s_school_name_id','left')
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }
        $list = $query
            ->limit($limit, $start)
            ->order_by('id', 'desc')
            ->get()
            ->result_array();
        if ( ! empty($list)) {
            foreach ($list as $index => $relation) {
                if ($relation['school_name'] == '') {
                    $list[$index]['school_name'] = '--';
                }

                if ($relation['coach_name'] == '') {
                    $list[$index]['coach_name'] = '--';
                }

                if ($relation['coach_phone'] == '') {
                    $list[$index]['coach_phone'] = '--';
                }

                if ($relation['real_name'] == '') {
                    $list[$index]['real_name'] = '--';
                }

                if ($relation['user_name'] == '') {
                    $list[$index]['user_name'] = '--';
                }

                if ($relation['user_phone'] == '') {
                    $list[$index]['user_phone'] = '--';
                }

                if ($relation['lesson_name'] == null) {
                    $list[$index]['lesson_name'] = '--';
                }

                if ($relation['license_name'] == null) {
                    $list[$index]['license_name'] = '--';
                }

                if ($relation['addtime'] != '') {
                    $list[$index]['addtime'] = date('Y-m-d H:i:s', $relation['addtime']);
                } else {
                    $list[$index]['addtime'] = '--';
                }

                if ($relation['updatetime'] != '') {
                    $list[$index]['updatetime'] = date('Y-m-d H:i:s', $relation['updatetime']);
                } else {
                    $list[$index]['updatetime'] = '--';
                }
            }
        }
        return $list;
    }

// 3.教练认证列表
    /**
     * 获取教练认证列表页码信息
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $limit      限制条件
     * @return  void
     **/
    public function getcoachVerifyPageNum($school_id, $param, $limit)
    {
        $map = [];
        $complex = [];
        $map['user.i_user_type'] = 1;
        $map['user.i_status'] = 0;
        if ($school_id != 0) {
            $map['school.l_school_id'] = $school_id;
        }
       
        if ($param) {
            if ($param['status'] != '') {
                $map['certification_status'] = $param['status'];
            }

            $keywords = $param['keywords'];
            if ($keywords != '') {
                $complex['s_coach_name'] = $keywords;
                $complex['s_coach_phone'] = $keywords;
                $complex['school.s_school_name'] = $keywords;
            }
        }

        $query = $this->db 
            ->from("{$this->coach_tbl} as coach")
            ->join("{$this->school_tbl} as school", 'school.l_school_id=coach.s_school_name_id', 'left')
            ->join("{$this->user_tbl} as user", 'user.l_user_id=coach.user_id', 'left')
            ->where($map);

        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }

        $count = $query->count_all_results();
        return $pageinfo = ['pagenum' => ceil ($count / $limit), 'count' => $count];
    }

    /**
     * 获取教练认证列表
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $limit      限制条件
     * @return  void
     **/
    public function getCoachVerifyList($school_id, $param, $start, $limit)
    {
        $map = [];
        $complex = [];
        $map['user.i_user_type'] = 1;
        $map['user.i_status'] = 0;
        if ($school_id != 0) {
            $map['school.l_school_id'] = $school_id;
        }
       
        if ($param) {
            if ($param['status'] != '') {
                $map['certification_status'] = $param['status'];
            }

            $keywords = $param['keywords'];
            if ($keywords != '') {
                $complex['s_coach_name'] = $keywords;
                $complex['s_coach_phone'] = $keywords;
                $complex['school.s_school_name'] = $keywords;
            }
        }
        $query = $this->db 
            ->select(
                'coach.l_coach_id as id,
                 coach.s_coach_name as coach_name,
                 coach.s_coach_phone as coach_phone,
                 coach.certification_status,
                 coach.coach_license_imgurl,
                 coach.id_card_imgurl,
                 coach.personal_image_url,
                 coach.coach_car_imgurl,
                 coach.addtime,
                 coach.updatetime,
                 school.l_school_id,
                 school.s_school_name as school_name'
            )
            ->from("{$this->coach_tbl} as coach")
            ->join("{$this->school_tbl} as school", 'school.l_school_id=coach.s_school_name_id', 'left')
            ->join("{$this->user_tbl} as user", 'user.l_user_id=coach.user_id', 'left')
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }

        $list = $query
            ->limit($limit, $start)
            ->order_by('l_coach_id', 'desc')
            ->get()
            ->result_array();
        if ( ! empty($list)) {
            foreach ($list as $index => $coach) {
                if ($coach['school_name'] == '') {
                    $list[$index]['school_name'] = '--';
                }

                if ($coach['coach_name'] == '') {
                    $list[$index]['coach_name'] = '--';
                }

                if ($coach['coach_phone'] == '') {
                    $list[$index]['coach_phone'] = '--';
                }

                if ($coach['updatetime'] != 0) {
                    $list[$index]['updatetime'] = date('Y-m-d H:i:s', $coach['updatetime']);
                } else {
                    $list[$index]['updatetime'] = '--';
                }

                $list[$index]['license_imgurl'] = $this->mbase->buildUrl($coach['coach_license_imgurl']);
                $list[$index]['idcard_imgurl'] = $this->mbase->buildUrl($coach['id_card_imgurl']);
                $list[$index]['personal_imgurl'] = $this->mbase->buildUrl($coach['personal_image_url']);
                $list[$index]['car_imgurl'] = $this->mbase->buildUrl($coach['coach_car_imgurl']);

            }
        }
        return $list;
    }

    /**
     * 设置教练的各种状态
     * @param   int     $id     教练ID 
     * @param   array   $data   教练的状态
     * @return  void
     **/
    public function setStatus(array $condition, array $data, $tablename)
    {
        $result = $this->db->where($condition)->update($tablename, $data);
        return $result;
    }

    /**
     * 获取教练的各种状态
     * @param   array   $condition  条件 
     * @param   string  $data       教练的状态
     * @param   string  $tablename  表名
     * @return  void
     **/
    public function getCoachStatus(array $condition, string $data, $tablename)
    {
        $coach_info = $this->db
            ->select($data)
            ->get_where($tablename, $condition)
            ->row_array();
        $status = '';
        if ( ! empty($coach_info)) {
            $status = $coach_info[$data];
        }
        return $status;
    }

    /**
     * 检测此手机的教练是否存在
     * @param   int     $coach_phone    教练手机
     *
     * @return  void
     **/
    public function checkCoachPhone(string $phone)
    {
        $map = [
            'user.i_user_type' => 1,
            'user.i_status' => 0,
            'coach.s_coach_phone' => $phone
        ];
        $coach_info = $this->db 
            ->select(
                'coach.l_coach_id,
                 coach.s_coach_name,
                 user.l_user_id,
                 user.i_status,
                 user.i_user_type'
            )
            ->from("{$this->coach_tablename} as coach")
            ->join("{$this->user_tbl} as user", "user.l_user_id=coach.user_id", "LEFT")
            ->where($map)
            ->get()
            ->row_array();
        return $coach_info;
    }

    public function addCoachInfo(array $data) {
        $this->db->insert($this->coach_tablename, $data);
        return $this->db->insert_id();
    }

    /**
     * 添加教练教练信息于user表中
     *
     * @return  void
     **/
    public function addUser(array $data)
    {
        $this->db->insert($this->user_tbl, $data);
        return $this->db->insert_id();
    }

    /**
     * 更新教练表中的信息
     *
     * @return  void
     **/
    public function editCoachInfo(array $data) {
        return $this->db->where('l_coach_id', $data['l_coach_id'])->update($this->coach_tablename, $data);
    }
    
    /**
     * 更新用户表中的信息
     * @param   array   $data   更新的信息
     * @param   int     $old_phone  教练老手机号
     * @return  void
     **/
    public function updateUser(array $data, string $old_phone, array $condition = [])
    {
        if (empty($condition)) {
            $condition = [
                'i_user_type' => 1,
                'i_status' => 0,
                's_phone' => $old_phone
            ];
        }
        return $this->db->where($condition)->update($this->user_tbl, $data);
    }

    /**
     * 删除教练信息
     *
     * @return  void
     **/
    public function delCoachInfo($data) {
        return $this->db->delete($this->coach_tablename, $data);
    }
    
    /**
     * 获取用户信息
     * @param   int   $id   教练ID
     * @return  void
     **/
    public function getCoachInfo($id) {
        $coach_info = $this->db
            ->select(
                'coach.*,
                 school.l_school_id,
                 school.s_school_name,
                 cars.name,
                 cars.id car_id,
                 cars.car_no'
            )
            ->from("{$this->coach_tbl} as coach")
            ->join("{$this->school_tbl} as school", 'school.l_school_id = coach.s_school_name_id', 'left')
            ->join("{$this->cars_tbl} as cars", 'cars.id = coach.s_coach_car_id', 'left')
            ->where(array('l_coach_id'=>$id))
            ->get()
            ->row_array();
        $license_configs = $this->getLicenseInfo(); // 牌照信息
        $lesson_configs = $this->productLesson(); // 科目信息
        if ($coach_info) {
            $coach_info['http_coach_imgurl'] = $this->mbase->buildUrl($coach_info['s_coach_imgurl']);

            $coach_license_id_arr = isset($coach_info['s_coach_lisence_id']) ? explode(',', $coach_info['s_coach_lisence_id']) : ['1'];
            $coach_lesson_id_arr = isset($coach_info['s_coach_lesson_id']) ? explode(',', $coach_info['s_coach_lesson_id']) : ['1'];
            $license_list = [];
            $lesson_list = [];
            foreach ($license_configs as $index => $license) {
                if ( in_array($license['license_id'], $coach_license_id_arr) ) {
                    $license_list[] = $license['license_name'];
                }
            }
            
            foreach ($lesson_configs as $index => $lesson) {
                if ( in_array($lesson['lesson_id'], $coach_lesson_id_arr) ) {
                    $lesson_list[] = $lesson['lesson_name'];
                }
            }
            $coach_info['coach_license'] = implode('","', $license_list);
            $coach_info['coach_lesson'] = implode('","',$lesson_list);

        }
        return $coach_info;
    }


    public function getCoachTimeConfigLisById($ids)
    {
        if(!$ids) {
            return false;
        }else {
            $ids = explode(',', $ids);
        }
        $items = array();
        $items = $this->db->from("{$this->coach_time_config_tbl}")->where_in('id', $ids)->get()->result_array();
        if($items) {
            foreach($items as $k=>$v) {
                $items[$k]['start_time'] = $v['start_time'] . ':00';
                $items[$k]['end_time'] = $v['end_time'] . ':00';
            }
        }
        return $items;
    }

    public function productLesson () {
        $lessonInfo = array();
        $lessonInfo = array(
            array('lesson_id' => '1', 'lesson_name' => '科目一'),
            array('lesson_id' => '2', 'lesson_name' => '科目二'),
            array('lesson_id' => '3', 'lesson_name' => '科目三'),
            array('lesson_id' => '4', 'lesson_name' => '科目四'),
        );
        return $lessonInfo;
    }

    public function iType()
    {
        $items = array(
            array('value' => "0", 'name' => '金牌教练员'),
            array('value' => "1", 'name' => '普通教练员'),
            array('value' => "2", 'name' => '二级优秀教练员'),
            array('value' => "3", 'name' => '三级优秀教练员'),
            array('value' => "4", 'name' => '四级优秀教练员'),
            array('value' => "5", 'name' => '二级优秀教练员 全国优秀教练员荣誉'),
            array('value' => "6", 'name' => '三级优秀教练员 全国优秀教练员荣誉'),
        );
        return $items;
    }

    // 根据驾校关键词获取驾校列表
    public function getSearchCoachList($key) {
        $query = $this->db->select('l_coach_id, s_coach_name')->like('s_coach_name', $key)->get($this->coach_tablename, 20);
        // return ['list'=>$query->result(), 'query'=>$this->db->last_query()];
        return ['list'=>$query->result()];
    }

    // 获取自定义字段的驾校数据
    public function getCoachParamsInfo($select, $id) {
        $query = $this->db->select($select)->get_where($this->coach_tablename, ['l_coach_id'=>$id]);
        return ['list'=>$query->result()];
    }

    //  获得牌照表中的信息
    public function getLicenseInfo ()
    {
        $items = $this->db->select('ls.*')
                ->from("{$this->license_tbl} as ls")
                ->order_by('ls.order', 'DESC')
                ->get()
                ->result_array();
        $list = [];
        if ( ! empty($items)) {
            foreach ($items as $index => $license) {
                $list[$license['id']] = $license;
            }
        }
        return $list;
    }

    //  获得科目表中的信息
    public function getLessonInfo ()
    {
        $items = $this->db->select('ls.*')
                ->from("{$this->lesson_tbl} as ls")
                ->order_by('ls.order', 'DESC')
                ->get()
                ->result_array();
        $list = [];
        if ( ! empty($items)) {
            foreach ($items as $index => $lesson) {
                $list[$lesson['id']] = $lesson;
            }
        }
        return $list;
    }

    public function getSchoolList() {
        $items = $this->db->select('l_school_id,s_school_name')
            ->from("{$this->school_tbl}")
            ->where(array('is_show'=>1))
            ->limit(100,0)
            ->get()
            ->result_array();
        return $items;
    }

    public function getCoachDateTimeConfig()
    {
        $current_time = time();
        $year = date('Y', $current_time);
        $month = date('m', $current_time);
        $day = date('d', $current_time);

        // 构建一个时间
        $build_date_timestamp = mktime(0, 0, 0, $month, $day, $year);

        // 循环7天时间
        $date_config = array();
        for ($i = 0; $i <= 7; $i++) {
            $date_config[$i]['fulldate'] = date('Y-m-d', $build_date_timestamp + ( 24 * 3600 * $i));
            $date_config[$i]['date'] = date('m-d', $build_date_timestamp + ( 24 * 3600 * $i));
            $date_config[$i]['month'] = date('m', $build_date_timestamp + ( 24 * 3600 * $i));
            $date_config[$i]['day'] = date('d', $build_date_timestamp + ( 24 * 3600 * $i));
            $date_config[$i]['date_format'] = intval(date('m', $build_date_timestamp + ( 24 * 3600 * $i))).'-'.intval(date('d', $build_date_timestamp + ( 24 * 3600 * $i)));
        }
        return $date_config;
    }

    public function updateCoachBindStatus($id, $status)
    {
        if($id != (int)$id || $status != (int)$status) {
            return false;
        }
        $result = $this->db->where('id', $id)->update($this->coach_user_rela_tbl, array('bind_status'=>$status));
        return $result;
    }

    /*获取预约计时的评价星级*/
    public function getStudyCommentInfo ($coach_id) {
        if (!$coach_id) {
            return false;
        }
        // 预约及时评价信息
        $star_info = array();
        $comment_stars = array();
        $count =  $this->db->from("{$this->coach_comment_tbl} as comment")
            ->where(array('coach_id' => $coach_id, 'type' => 1, 'coach_star'))
            ->count_all_results();
        $comment_info = $this->db->select('coach_star')
            ->from("{$this->coach_comment_tbl} as comment")
            ->where(array('coach_id' => $coach_id, 'type' => 1))
            ->get()->result_array();
        $star_info['star_bad_count'] = $star_info['star_mid_count'] = $star_info['star_good_count'] = 0;
        if (!empty($comment_info)) {
            foreach ($comment_info as $key => $value) {
                foreach ($value as $k => $v) {
                    $comment_stars[$key] = $v;
                }
            }
            foreach($comment_stars as $k=>$v) {
                if($v < 3 && $v > 0) {
                    $star_info['star_bad_count'] ++;
                }
                if($v >= 3 && $v < 4) {
                    $star_info['star_mid_count'] ++;
                }
                if($v >= 4) {
                    $star_info['star_good_count'] ++;
                }
            }
        }
        return $star_info;
    }

    /*获取报名班制的评价星级*/
    public function getShiftsCommentInfo ($coach_id, $school_id) {
        if (!$coach_id) {
            return false;
        }
        // 预约及时评价信息
        $star_info = array();
        $comment_stars = array();
        $count = $this->db->from("{$this->coach_comment_tbl} as comment")
            ->where(array('coach_id' => $coach_id, 'type' => 2, 'school_id' => $school_id))
            ->count_all_results();
        $comment_info = $this->db->select('coach_star')
            ->from("{$this->coach_comment_tbl} as comment")
            ->where(array('coach_id' => $coach_id, 'type' => 2, 'school_id' => $school_id))
            ->get()->result_array();
        $star_info['star_bad_count'] = $star_info['star_mid_count'] = $star_info['star_good_count'] = 0;
        if (!empty($comment_info)) {
            foreach ($comment_info as $key => $value) {
                foreach ($value as $k => $v) {
                    $comment_stars[$key] = $v;
                }
            }
            foreach($comment_stars as $k=>$v) {
                if($v < 3 && $v > 0) {
                    $star_info['star_bad_count'] ++;
                }
                if($v >= 3 && $v < 4) {
                    $star_info['star_mid_count'] ++;
                }
                if($v >= 4) {
                    $star_info['star_good_count'] ++;
                }
            }
        }
        return $star_info;
    }

    public function getCoachSignUpInfo($coach_id)
    {
        if (!is_numeric($coach_id)) {
            return array();
        }
        $coach_shifts = $this->db->from("{$this->school_shifts_tbl} as shifts")
            ->where(array('coach_id'=>$coach_id))
            ->get()->result_array();

        if ($coach_shifts) {
            return $coach_shifts;
        } else {
            return array();
        }
    }

    public function getCoachSignUpList($coach_id)
    {
        if (!is_numeric($coach_id)) {
            return $count = 0;
        }
        $coach_shifts = array();
        $so_shifts_ids = $this->db->select('id')
            ->from("{$this->school_shifts_tbl} as shifts")
            ->where(array('coach_id'=>$coach_id))
            ->get()->result_array();
        if (!empty($so_shifts_ids)) {
            foreach ($so_shifts_ids as $key => $value) {
                foreach ($value as $k => $v) {
                    $count = $this->db->from("{$this->school_orders_tbl} as orders")
                        ->where(
                            array(
                                'so_order_status' => array('in', array('1', '4')),
                                'so_coach_id' => $coach_id,
                                'so_shifts_id' => $v
                            )
                        )
                        ->count_all_results();
                    $shifts_title = $this->db->select('shifts.sh_title')
                        ->from("{$this->school_shifts_tbl} as shifts")
                        ->join("{$this->school_orders_tbl} as orders",'shifts.id=orders.so_shifts_id','left')
                        ->where(
                            array(
                                'so_order_status' => array('in', array('1', '4')),
                                'so_coach_id' => $coach_id,
                                'shifts.coach_id' => $coach_id,
                                'so_shifts_id' => $v
                            )
                        )
                        ->get()->result_array();
                    if ($count != 0 && $shifts_title != '') {
                        $coach_shifts[$key]['count'] = $count;
                        $coach_shifts[$key]['shifts_title'] = $shifts_title;
                    } else {
                        $coach_shifts[$key]['count'] = 0;
                        $coach_shifts[$key]['shifts_title'] = '暂无';
                    }
                }
            }
            return $coach_shifts;
        } else {
            return array();
        }
    }
}
?>
