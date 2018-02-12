<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mstudent extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->user_tbl = $this->db->dbprefix('user');
        $this->usersinfo_tbl = $this->db->dbprefix('users_info');
        $this->school_tbl = $this->db->dbprefix('school');
        $this->uexam_tbl = $this->db->dbprefix('user_exam_records');
        $this->lesson_config_tbl = $this->db->dbprefix('lesson_config');
        $this->load->database();
    }

// 1.学员列表
    /**
     * 获取学员列表页码数
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $limit      限定数
     * @return  void
     **/
    public function getStudentPageNum($school_id, $param, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            if ($param['status'] != '') {
                $map['user.i_status'] = $param['status'];
                if ($param['status'] == 1) {
                    $map['user.i_status'] = 0;
                }
            }
            $keywords = $param['keywords'];
            if ($keywords != '') {
                $complex = [
                    'user.l_user_id' => $keywords,
                    'user.s_username' => $keywords,
                    'user.s_phone' => $keywords,
                    'school.s_school_name' => $keywords,
                    'info.identity_id' => $keywords
                ];
            }
        }

        if ($school_id != '') {
            $map['info.school_id'] = $school_id;
            $map['user.i_status'] = 0;
        }
        $map['user.i_user_type'] = 0;
        $query = $this->db
            ->from("{$this->user_tbl} as user")
            ->join("{$this->usersinfo_tbl} as info", "info.user_id=user.l_user_id", "LEFT")
            ->join("{$this->school_tbl} as school", "school.l_school_id=info.school_id", "LEFT")
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }
        $count = $query->count_all_results();
        $pageinfo = [
            'pagenum' => ceil ( $count / $limit ),
            'count' => $count,
        ];
        return $pageinfo;
    }

    /**
     * 获取学员列表数据
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $limit      限定数
     * @return  void
     **/
    public function getStudentListByCondition($school_id, $param, $start, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            if ($param['status'] != '') {
                $map['user.i_status'] = $param['status'];
                if ($param['status'] == 1) {
                    $map['user.i_status'] = 0;
                }
            }
            $keywords = $param['keywords'];
            if ($keywords != '') {
                $complex = [
                    'user.l_user_id' => $keywords,
                    'user.s_username' => $keywords,
                    'user.s_phone' => $keywords,
                    'school.s_school_name' => $keywords,
                    'info.identity_id' => $keywords
                ];
            }
        }
        if ($school_id != '') {
            $map['info.school_id'] = $school_id;
            $map['user.i_status'] = 0;
        }
        $map['user.i_user_type'] = 0;
        $query = $this->db
            ->select(
                'user.l_user_id,
                 user.s_username as user_name,
                 user.i_user_type as user_type,
                 user.i_status as status,
                 user.s_real_name as real_name,
                 user.s_phone as user_phone,
                 user.s_imgurl,
                 user.addtime,
                 user.updatetime,
                 info.sex,
                 info.age,
                 info.identity_id,
                 info.user_photo,
                 info.address,
                 info.license_num,
                 info.school_id,
                 info.lesson_id,
                 info.lesson_name,
                 info.license_id,
                 info.license_name,
                 info.exam_license_name,
                 info.province_id,
                 info.city_id,
                 info.area_id,
                 info.photo_id,
                 info.learncar_status,
                 school.s_school_name as school_name'
            )
            ->from("{$this->user_tbl} as user")
            ->join("{$this->usersinfo_tbl} as info", "info.user_id=user.l_user_id", "LEFT")
            ->join("{$this->school_tbl} as school", "school.l_school_id=info.school_id", "LEFT")
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }
        $student_list = $query
            ->limit($limit, $start)
            ->order_by('l_user_id', 'desc')
            ->get()
            ->result_array();
        if ( ! empty($student_list)) {
            foreach ($student_list as $index => $student) {
                
                $province = '';
                $city = '';
                $area = '';
                $provinceinfo = $this->mcity->getProvinceInfoByCondition('province', ['provinceid' => $student['province_id']]);
                $cityinfo = $this->mcity->getCityInfoByCondition('city', ['cityid' => $student['city_id']]);
                $areainfo = $this->mcity->getAreaInfoByCondition('area', ['areaid' => $student['area_id']]);
                if ( ! empty($provinceinfo)) {
                    $province = $provinceinfo['province'];
                } 

                if ( ! empty($cityinfo)) {
                    $city = $cityinfo['city'];
                } 

                if ( ! empty($areainfo)) {
                    $area = $areainfo['area'];
                } 

                $stu_address = $province.$city.$area.$student['address'];
                if ($stu_address != '') {
                    $student_list[$index]['stu_address'] = $province.$city.$area.$student['address'];
                } else {
                    $student_list[$index]['stu_address'] = '--';
                }
                
                if ($student['school_name'] != '') {
                    $student_list[$index]['school_name'] = $student['school_name'];
                } else {
                    $student_list[$index]['school_name'] = '--';
                }
                if ($student['addtime'] != 0) {
                    $student_list[$index]['addtime'] = date('Y-m-d H:i:s', $student['addtime']);
                } else {
                    $student_list[$index]['addtime'] = '--';
                }
                if ($student['updatetime'] != 0) {
                    $student_list[$index]['updatetime'] = date('Y-m-d H:i:s', $student['updatetime']);
                } else {
                    $student_list[$index]['updatetime'] = '--';
                }

                if ($student['address'] != '') {
                    $student_list[$index]['address'] = $student['address'];
                } else {
                    $student_list[$index]['address'] = '';
                }


                if ($student['identity_id'] != '') {
                    $student_list[$index]['identity_id'] = $student['identity_id'];
                } else {
                    $student_list[$index]['identity_id'] = '--';
                }

                if ($student['user_phone'] == '') {
                    $student_list[$index]['user_phone'] = '--';
                }

                if ($student['real_name'] == '') {
                    $student_list[$index]['real_name'] = '--';
                }

                if ($student['lesson_name'] == '') {
                    $student_list[$index]['lesson_name'] = '--';
                }
                if ($student['license_name'] == '') {
                    $student_list[$index]['license_name'] = '--';
                }
                if ($student['learncar_status'] == '') {
                    $student_list[$index]['learncar_status'] = '--';
                }
                if ($student['user_name'] == '') {
                    $student_list[$index]['user_name'] = '--';
                }
            }
        }
        return $student_list;
    }

    // 获取科目名称
    public function getLessonName($lesson_id)
    {
        $lesson_info = $this->db
            ->from("{$this->lesson_config_tbl} as lesson")
            ->where('lesson_id', $lesson_id)
            ->get()
            ->row_array();
        $lesson_name = '';
        if ( ! empty($lesson_info)) {
            $lesson_name = $lesson_info['lesson_name'];
        }
        return $lesson_name;
    }
     
    //  获得学员列表分页
    public function getPageNum($param=array(), $limit) 
    {
        $items = $map = array();
        $filter_1 = $filter_2 = '';
        $map = "u.i_user_type='0'";
        if($param) {
            if($param['status']) {
                $filter_1 = " AND u.i_status='2'";
            }else {
                $filter_1 = " AND u.i_status='0'";
            }
            if($param['type'] == 'l_user_id') {
                $filter_2 = " AND u.l_user_id LIKE '%" .$param['value']. "%'";
            }else if($param['type'] == 's_real_name') {
                $filter_2 = " AND u.s_real_name LIKE '%" .$param['value']."%'";
            }else if($param['type'] == 's_phone') {
                $filter_2 = " AND u.s_phone LIKE '%" .$param['value']."%'";
            }else if($param['type'] == 'identity_id') {
                $filter_2 = " AND uf.identity_id LIKE '%" .$param['value']."%'";
            }else if($param['type'] == 's_school_name') {
                $filter_2 = " AND s.s_school_name LIKE '%" .$param['value']."%'";
            }
        }
        $map .= $filter_1 . $filter_2;
        $count = $this->db->from("{$this->user_tbl} as u")
            ->join("{$this->usersinfo_tbl} as uf", 'uf.user_id = u.l_user_id', 'left')
            ->join("{$this->school_tbl} as s", 's.l_school_id=uf.school_id', 'left')
            ->where($map)
            ->count_all_results();
        return ['pn'=>(int) ceil($count / $limit), 'count'=>$count];   
    }

    //  获得学员列表
    public function getStudentList($param=array(), $start, $limit)
    {
        $map = $items = array();
        $filter_1 = $filter_2 = '';
        $map = "u.i_user_type='0'";
        if($param) {
            if($param['status']) {
                $filter_1 = " AND u.i_status='2'";
            }else {
                $filter_1 = " AND u.i_status='0'";
            }
            if($param['type'] == 'l_user_id') {
                $filter_2 = " AND u.l_user_id LIKE '%" .$param['value']. "%'";
            }else if($param['type'] == 's_real_name') {
                $filter_2 = " AND u.s_real_name LIKE '%" .$param['value']."%'";
            }else if($param['type'] == 's_phone') {
                $filter_2 = " AND u.s_phone LIKE '%" .$param['value']."%'";
            }else if($param['type'] == 'identity_id') {
                $filter_2 = " AND uf.identity_id LIKE '%" .$param['value']."%'";
            }else if($param['type'] == 's_school_name') {
                $filter_2 = " AND s.s_school_name LIKE '%" .$param['value']."%'";
            }
        }
        $map .= $filter_1 . $filter_2;
        $count = $this->db->from("{$this->user_tbl} as u")
            ->join("{$this->usersinfo_tbl} as uf", 'uf.user_id = u.l_user_id', 'left')
            ->join("{$this->school_tbl} as s", 's.l_school_id=uf.school_id', 'left')
            ->where($map)
            ->count_all_results();
        $page = (int) ceil($count / $limit);
        $items = $this->db->select(
                'u.l_user_id,
                u.s_username,
                u.i_user_type,
                u.i_status,
                u.s_real_name,
                u.i_from,
                u.s_phone,
                u.is_first,
                u.addtime,
                u.updatetime,
                uf.sex,
                uf.age,
                uf.identity_id,
                uf.address,
                uf.lesson_name,
                uf.license_name,
                uf.learncar_status,
                uf.xiha_coin,
                uf.signin_num,
                uf.addtime as regtime,
                uf.updatetime as uptime,
                s.l_school_id,
                s.s_school_name'
                )
            ->from("{$this->user_tbl} as u")
            ->join("{$this->usersinfo_tbl} as uf", 'uf.user_id=u.l_user_id', 'left')
            ->join("{$this->school_tbl} as s", 's.l_school_id=uf.school_id', 'left')
            ->where($map)->order_by('u.l_user_id', 'DESC')
            ->limit($limit, $start)->get()->result_array();
        if ($items) {
            foreach ($items as $key => $value) {
                if ($value['s_school_name'] != '') {
                    $items[$key]['s_school_name'] = $value['s_school_name'];
                } else {
                    $items[$key]['s_school_name'] = '--';
                }
                if ($value['regtime'] != 0) {
                    $items[$key]['regtime'] = date('Y-m-d H:i:s', $value['regtime']);
                } else {
                    $items[$key]['regtime'] = '--';
                }
                if ($value['uptime'] != 0) {
                    $items[$key]['uptime'] = date('Y-m-d H:i:s', $value['uptime']);
                } else {
                    $items[$key]['uptime'] = '--';
                }

                if ($value['address'] != '') {
                    $items[$key]['address'] = $value['address'];
                } else {
                    $items[$key]['address'] = '--';
                }

                if ($value['identity_id'] != '') {
                    $items[$key]['identity_id'] = $value['identity_id'];
                } else {
                    $items[$key]['identity_id'] = '--';
                }

                if ($value['s_phone'] == '') {
                    $items[$key]['s_phone'] = '--';
                }

                if ($value['s_real_name'] == '') {
                    $items[$key]['s_real_name'] = '--';
                }

                if ($value['lesson_name'] == '') {
                    $items[$key]['lesson_name'] = '--';
                }
                if ($value['license_name'] == '') {
                    $items[$key]['license_name'] = '--';
                }
                if ($value['learncar_status'] == '') {
                    $items[$key]['learncar_status'] = '--';
                }
                if ($value['s_username'] == '') {
                    $items[$key]['s_username'] = '--';
                }

                if ($value['xiha_coin'] == '') {
                    $items[$key]['xiha_coin'] = '--';
                }
                if ($value['signin_num'] == '') {
                    $items[$key]['signin_num'] = '--';
                }
                if ($value['age'] == '') {
                    $items[$key]['age'] = '--';
                }
            }
        }
        return array('items' => $items, 'pn' => $page, 'count' => $count);
    }

// 2.模拟考试
    /**
     * 获取模拟考试成绩
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $limit      限定数量
     * @return  $pageinfo
     **/
    public function getRecordsPageNum($school_id, $param, $limit)
    {   
        $map = [];
        $complex = [];
        $map['user.i_user_type'] = 0;
        $map['user.i_status'] = 0;
        if ( $school_id != '') {
            $map['school_id'] = $school_id;
        }
        if ($param) {
            $keywords = $param['keywords'];
            if ($keywords != '') {
                $complex = [
                    // 'user.s_real_name' => $keywords,
                    // 'user.s_username' => $keywords,
                    'realname' => $keywords,
                    'phone_num' => $keywords,
                    'identify_id' => $keywords,
                    's_school_name' => $keywords,
                ];
            }
        }

        $query = $this->db
            ->from("{$this->uexam_tbl} as exam")
            ->join("{$this->user_tbl} as user", "user.l_user_id=exam.user_id", "LEFT")
            ->join("{$this->mschool->school_tablename} as school", "school.l_school_id=exam.school_id", "LEFT")
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query->group_start()
                ->or_like($complex)
                ->group_end();
        }

        $count = $query->count_all_results();
        $pageinfo = [
            'pagenum' => ceil ( $count / $limit ),
            'count' => $count
        ];
        return $pageinfo;
    }

    /**
     * 获取模拟考试成绩
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $start      起始数
     * @param   int     $limit      限定数量
     * @return  $pageinfo
     **/
    public function getExamRecordsList($school_id, $param, $start, $limit)
    {
        $map = [];
        $complex = [];
        $map['user.i_user_type'] = 0;
        $map['user.i_status'] = 0;
        if ( $school_id != '') {
            $map['school_id'] = $school_id;
        }
        if ($param) {
            $keywords = $param['keywords'];
            if ($keywords != '') {
                $complex = [
                    // 'user.s_real_name' => $keywords,
                    // 'user.s_username' => $keywords,
                    'realname' => $keywords,
                    'phone_num' => $keywords,
                    'identify_id' => $keywords,
                    's_school_name' => $keywords,
                ];
            }
        }
        $query = $this->db
            ->select(
                'exam.*,
                 school.s_school_name as school_name,
                 school.l_school_id,
                 user.s_username,
                 user.s_real_name,
                 user.s_phone'
            )
            ->from("{$this->uexam_tbl} as exam")
            ->join("{$this->user_tbl} as user", "user.l_user_id=exam.user_id", "LEFT")
            ->join("{$this->mschool->school_tablename} as school", "school.l_school_id=exam.school_id", "LEFT")
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query->group_start()
                ->or_like($complex)
                ->group_end();
        }
        $recordslist = $query
            ->limit($limit, $start)
            ->order_by('id', 'desc')
            ->get()
            ->result_array();
        $list = [];
        if ( ! empty($recordslist)) {
            foreach ($recordslist as $index => $records) {

                if($records['realname'] == '') {
                    $recordslist[$index]['realname'] = '--';
                }

                $recordslist[$index]['phone_num'] = $records['phone_num'] != '' ? $records['phone_num'] : '--';

                $recordslist[$index]['identify_id'] = $records['identify_id'] == '' ? '--' : $records['identify_id'];

                // 牌照
                $recordslist[$index]['ctype'] = $records['ctype'] != '' ? $records['ctype'] : '--';
                $recordslist[$index]['car_type'] = $records['car_type'] != '' ? $records['car_type'] : '--';

                // 考试的科目
                $course = $records['course'];
                $course_arr = ['kemu1' => '科目一', 'kemu4' => '科目四'];
                if ($course != '') {
                    $course_name = $course_arr[$course];
                } else {
                    $course_name = '--';
                }
                $recordslist[$index]['course_name'] = $course_name;

                if($records['stype'] == 1){
                    $recordslist[$index]['stype_name']= '科目一';
                } else if ($records['stype'] == 4) {
                    $recordslist[$index]['stype_name']= '科目四';
                } else {
                    $recordslist[$index]['stype_name']= '--';
                }

                // 考试的总时间
                if($records['exam_total_time'] >= 60) {
                    $min = floor($records['exam_total_time'] / 60);
                    $sec = $records['exam_total_time'] - 60 * $min;
                    if ($sec != 0) {
                        $recordslist[$index]['exam_total_time']= $min . '分' . $sec . '秒';
                    } else {
                        $recordslist[$index]['exam_total_time']= $min . '分钟';
                    }
                } else {
                    $min_sec = $records['exam_total_time'];
                    $recordslist[$index]['exam_total_time']= $min_sec . '秒';
                }

                // 考试的分数
                $recordslist[$index]['score']= $records['score'];

                // 交卷的时间
                if ($records['addtime'] != 0) {
                    $recordslist[$index]['addtime']= date('Y-m-d H:i:s', $records['addtime']);
                } else {
                    $recordslist[$index]['addtime']= '';
                }

                if ($records['school_name'] != '') {
                    $recordslist[$index]['school_name']= $records['school_name'];
                } else {
                    $recordslist[$index]['school_name']= '--';
                }


            }
        }

        return $recordslist;

    }


    //  获得学员考试记录分页
    public function getPageNumR($param=array(), $limit)
    {
        $items  = array();
        $filter = '';
        $map = "u.i_status='0' AND u.i_user_type='0'";
        if($param) {
            if($param['type'] == 's_real_name') {
                $filter = " AND u.s_real_name LIKE '%".$param['value']."%'";
            }else if($param['type'] == 's_phone') {
                $filter = " AND u.s_phone LIKE '%".$param['value']."%'";
            }else if($param['type'] == 'identity_id') {
                $filter = " AND e.identify_id LIKE '%".$param['value']."%'";
            }
        }
        $map .= $filter;
        $count = $this->db->from("{$this->uexam_tbl} as e")
            ->join("{$this->user_tbl} as u", 'u.l_user_id=e.user_id', 'left')
            ->where($map)
            ->count_all_results();
        $page = (int) ceil($count / $limit);
        return ['pn'=>(int) ceil($count / $limit), 'count'=>$count];   
    }

    //  根据不同驾校(school_id)获得学员考试记录信息
    public function getExamRecords($param=array(), $start, $limit)
    {
        $items  = array();
        $filter = '';
        $map = "u.i_status='0' AND u.i_user_type='0'";
        if($param) {
            if($param['type'] == 's_real_name') {
                $filter = " AND u.s_real_name LIKE '%".$param['value']."%'";
            }else if($param['type'] == 's_phone') {
                $filter = " AND u.s_phone LIKE '%".$param['value']."%'";
            }else if($param['type'] == 'identity_id') {
                $filter = " AND e.identify_id LIKE '%".$param['value']."%'";
            }
        }
        $map .= $filter;
        $count = $this->db->from("{$this->uexam_tbl} as e")
            ->join("{$this->user_tbl} as u", 'u.l_user_id=e.user_id', 'left')
            ->where($map)
            ->count_all_results();
        $page = (int) ceil($count / $limit);

        $items = $this->db->select(
            'e.*, 
            e.addtime as add_time,
            u.*, u.addtime as user_add_time, 
            s.l_school_id, 
            s.s_school_name'
            )
            ->from("{$this->uexam_tbl} as e")
            ->join("{$this->user_tbl} as u", 'u.l_user_id=e.user_id', 'left')
            ->join("{$this->school_tbl} as s", 's.l_school_id=e.school_id', 'left')
            ->where($map)->order_by('e.id', 'DESC')
            ->limit($limit, $start)->get()->result_array();
   
        if($items) {
            foreach($items as $key => $value) {
                // 考试的id
                $items[$key]['id']= $value['id'];
                // 用户姓名或者登陆账号
                if($value['realname']) {
                    $items[$key]['realname']= $value['realname'];
                } else {
                    $items[$key]['realname']= $value['realname'] == '' ? $value['s_real_name'] : $value['s_username'];
                }
                // 用户的手机号码
                $items[$key]['s_phone']= $value['phone_num'] == '' ? $value['s_phone'] : $value['phone_num'];
                // 用户的身份证号
                $items[$key]['identify_id']= $value['identify_id'] == '' ? '--' : $value['identify_id'];
                // $list[$key]['identify_id']= $value['identify_id'];
                // 牌照
                $items[$key]['ctype']= $value['ctype'];
                // 考试的科目
                if($value['stype'] == 1){
                    $items[$key]['stype_name']= '科目一';
                } else if ($value['stype'] == 4) {
                    $items[$key]['stype_name']= '科目四';
                }
                // 考试的总时间
                if($value['exam_total_time'] >= 60) {
                    $min = floor($value['exam_total_time'] / 60);
                    $sec = $value['exam_total_time'] - 60 * $min;
                    $items[$key]['exam_total_time']= $min . '分' . $sec . '秒';
                } else {
                    $min_sec = $value['exam_total_time'];
                    $items[$key]['exam_total_time']= $min_sec . '秒';
                }
                // 考试的分数
                $items[$key]['score']= $value['score'];
                // 交卷的时间
                if ($value['add_time'] != 0) {
                    $items[$key]['add_time']= date('Y-m-d H:i:s', $value['add_time']);
                } else {
                    $items[$key]['add_time']= '';
                }
                if ($value['s_school_name'] != '') {
                    $items[$key]['s_school_name']= $value['s_school_name'];
                } else {
                    $items[$key]['s_school_name']= '--';
                }
            }
        }
        return array('items' => $items, 'pn' => $page, 'count' => $count);
    }

    public function addStudentInfo($data) {
        $this->db->insert($this->school_tablename, $data);
        return $this->db->insert_id();
    }

    public function editStudentInfo($data) {
        return $this->db->where('l_school_id', $data['l_school_id'])->update($this->school_tablename, $data);
    }

    public function delInfo($tbname, $data) {
        return $this->db->delete($tbname, $data);
    }

    public function getStudentInfo($id) {
        $query = $this->db->get_where($this->school_tablename, ['l_school_id'=>$id]);
        return $query->row_array();
    }
}