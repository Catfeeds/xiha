<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcomment extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->cc_tbl = $this->db->dbprefix('coach_comment');
        $this->user_tbl = $this->db->dbprefix('user');
        $this->school_tbl = $this->db->dbprefix('school');
        $this->coach_tbl =  $this->db->dbprefix('coach');
        $this->sc_tbl = $this->db->dbprefix('student_comment');
        $this->load->database();
    }

// 1.学员评价教练
    /**
     * 获取学员评价教练页面信息
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $limit      限定数目
     * @return  $pageinfo
     **/
    public function getStuCommentCoachPageNum($school_id, $param, $limit)
    {
        $map = [];
        $complex = [];
        // $map['user.i_status'] = 0;
        $map['user.i_user_type'] = 0;
        if ($school_id != 0) {
            $map['coach.s_school_name_id'] = $school_id;
        }
        if ($param) {
            if ($param['star'] != '') {
                $map['comment.coach_star'] = $param['star'];
            }
            $keywords = $param['keywords'];
            if ( $keywords != '') {
                $complex = [
                    'coach.s_coach_name' => $keywords,
                    'school.s_school_name' => $keywords,
                    'comment.order_no' => $keywords,
                    'user.s_real_name' => $keywords,
                    'user.s_username' => $keywords,
                    'user.s_phone' => $keywords
                ];
            }
        }
        $query = $this->db
            ->from("{$this->cc_tbl} as comment")
            ->join("{$this->user_tbl} as user", "user.l_user_id=comment.user_id", "LEFT")
            ->join("{$this->coach_tbl} as coach", "coach.l_coach_id=comment.coach_id", "LEFT")
            ->join("{$this->school_tbl} as school", "school.l_school_id=coach.s_school_name_id", "LEFT")
            ->where_not_in('comment.coach_id', [0])
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }

        $count = $query->count_all_results();
        return $pageinfo = [
            'count' => $count,
            'pagenum' => ceil ( $count / $limit)
        ];
    }

    /**
     * 获取学员评价教练的列表信息
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $start      开始数
     * @param   int     $limit      限定数目
     * @return  $commentlist
     **/
    public function getStuCommentCoachList($school_id, $param, $start, $limit)
    {
        $map = [];
        $complex = [];
        // $map['user.i_status'] = 0;
        $map['user.i_user_type'] = 0;
        if ($school_id != 0) {
            $map['coach.s_school_name_id'] = $school_id;
        }
        if ($param) {
            if ($param['star'] != '') {
                $map['comment.coach_star'] = $param['star'];
            }
            $keywords = $param['keywords'];
            if ( $keywords != '') {
                $complex = [
                    'coach.s_coach_name' => $keywords,
                    'school.s_school_name' => $keywords,
                    'comment.order_no' => $keywords,
                    'user.s_real_name' => $keywords,
                    'user.s_username' => $keywords,
                    'user.s_phone' => $keywords
                ];
            }
        }
        $query = $this->db
            ->select(
                'comment.id,
                 comment.coach_star,
                 comment.coach_content,
                 comment.coach_id,
                 comment.user_id,
                 comment.order_no,
                 comment.school_id,
                 comment.type,
                 comment.addtime,
                 coach.s_coach_name as coach_name,
                 coach.s_coach_phone as coach_phone,
                 school.s_school_name as school_name,
                 user.s_phone,
                 user.s_username,
                 user.s_real_name'
            )
            ->from("{$this->cc_tbl} as comment")
            ->join("{$this->user_tbl} as user", "user.l_user_id=comment.user_id", "LEFT")
            ->join("{$this->coach_tbl} as coach", "coach.l_coach_id=comment.coach_id", "LEFT")
            ->join("{$this->school_tbl} as school", "school.l_school_id=coach.s_school_name_id", "LEFT")
            ->where_not_in('comment.coach_id', [0])
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }
        $commentlist = $query->limit($limit, $start)
            ->order_by('id', 'desc')
            ->get()
            ->result_array();
        if ( ! empty($commentlist)) {
            foreach ($commentlist as $index => $comment) {

                if ($comment['s_username'] == '') {
                    $commentlist[$index]['s_username'] = '--';
                } 

                if ($comment['s_real_name'] == '') {
                    $commentlist[$index]['s_real_name'] = '--';
                } 

                if ($comment['s_phone'] == '') {
                    $commentlist[$index]['s_phone'] = '--';
                }

                if ($comment['addtime'] != 0 AND $comment['addtime'] != '') {
                    $commentlist[$index]['addtime'] = date('Y-d-m H:i:s', $comment['addtime']);
                } else {
                    $commentlist[$index]['addtime'] = '--';
                }

                if ($comment['school_name'] == '') {
                    $commentlist[$index]['school_name'] = '--';
                }

                if ($comment['coach_content'] == '') {
                    $commentlist[$index]['coach_content'] = '--';
                }

                if ($comment['coach_name'] == '') {
                    $commentlist[$index]['coach_name'] = '--';
                }

                if ($comment['coach_phone'] == '') {
                    $commentlist[$index]['coach_phone'] = '--';
                }
            }
        }
        return $commentlist;
    }

    public function getStuCommentCoachInfo ($id) 
    {
        $info = $this->db->select(
                'cc.id,
                cc.order_no,
                cc.type,
                cc.coach_star,
                cc.coach_content,
                u.l_user_id,
                u.s_username,
                s.l_school_id,
                s.s_school_name'
                )
                ->from("{$this->cc_tbl} as cc")
                ->join("{$this->user_tbl} as u", 'u.l_user_id = cc.user_id', 'left')
                ->join("{$this->coach_tbl} as c", 'c.l_coach_id = cc.coach_id', 'left')
                ->join("{$this->school_tbl} as s", 's.l_school_id = c.s_school_name_id', 'left')
                ->where("cc.id='{$id}'")
                ->get()->row_array();
        return $info;
    }

// 2.学员评价驾校模块
    /**
     * 获取学员评价驾校的页码信息
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $limit      限定数目
     * @return  $pageinfo
     **/
    public function getStuCommentSchoolPageNum($school_id, $param, $limit)
    {
        $map = [];
        $complex = [];
        // $map['user.i_status'] = 0;
        $map['user.i_user_type'] = 0;
        $map['comment.coach_id'] = 0;
        if ($school_id != 0) {
            $map['comment.school_id'] = $school_id;
        }
        if ($param) {
            if ($param['star'] != '') {
                $map['comment.school_star'] = $param['star'];
            }
            $keywords = $param['keywords'];
            if ( $keywords != '') {
                $complex = [
                    'school.s_school_name' => $keywords,
                    'comment.order_no' => $keywords,
                    'user.s_real_name' => $keywords,
                    'user.s_username' => $keywords,
                    'user.s_phone' => $keywords
                ];
            }
        }
        $query = $this->db
            ->from("{$this->cc_tbl} as comment")
            ->join("{$this->user_tbl} as user", "user.l_user_id=comment.user_id", "LEFT")
            ->join("{$this->school_tbl} as school", "school.l_school_id=comment.school_id", "LEFT")
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }
        $count = $query->count_all_results();
        return $pageinfo = [
            'count' => $count,
            'pagenum' => ceil ( $count / $limit)
        ];
    }

     /**
     * 获取学员评价驾校的列表信息
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $start      开始数
     * @param   int     $limit      限定数目
     * @return  $commentlist
     **/
    public function getStuCommentSchoolList($school_id, $param, $start, $limit)
    {
        $map = [];
        $complex = [];
        // $map['user.i_status'] = 0;
        $map['user.i_user_type'] = 0;
        $map['comment.coach_id'] = 0;
        if ($school_id != 0) {
            $map['comment.school_id'] = $school_id;
        }
        if ($param) {
            if ($param['star'] != '') {
                $map['comment.school_star'] = $param['star'];
            }
            $keywords = $param['keywords'];
            if ( $keywords != '') {
                $complex = [
                    'school.s_school_name' => $keywords,
                    'comment.order_no' => $keywords,
                    'user.s_username' => $keywords,
                    'user.s_real_name' => $keywords,
                    'user.s_phone' => $keywords
                ];
            }
        }
        $query = $this->db
            ->select(
                'comment.id,
                 comment.school_star,
                 comment.school_content,
                 comment.user_id,
                 comment.order_no,
                 comment.school_id,
                 comment.type,
                 comment.addtime,
                 school.s_school_name as school_name,
                 user.s_phone,
                 user.s_username,
                 user.s_real_name'
            )
            ->from("{$this->cc_tbl} as comment")
            ->join("{$this->user_tbl} as user", "user.l_user_id=comment.user_id", "LEFT")
            ->join("{$this->school_tbl} as school", "school.l_school_id=comment.school_id", "LEFT")
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }

        $commentlist = $query->limit($limit, $start)
            ->order_by('id', 'desc')
            ->get()
            ->result_array();
        if ( ! empty($commentlist)) {
            foreach ($commentlist as $index => $comment) {

                if ($comment['s_username'] == '') {
                    $commentlist[$index]['s_username'] = '--';
                } 

                if ($comment['s_real_name'] == '') {
                    $commentlist[$index]['s_real_name'] = '--';
                } 

                if ($comment['addtime'] != 0 AND $comment['addtime'] != '') {
                    $commentlist[$index]['addtime'] = date('Y-d-m H:i:s', $comment['addtime']);
                } else {
                    $commentlist[$index]['addtime'] = '--';
                }

                if ($comment['school_name'] == '') {
                    $commentlist[$index]['school_name'] = '--';
                }

                if ($comment['school_content'] == '') {
                    $commentlist[$index]['school_content'] = '--';
                }
            }
        }
        return $commentlist;
    }

    /**
     * 获取用户订单
     * @param   int     $user_id    用户ID
     * @return  void
     **/
    public function getUserOrderNo($user_id)
    {
        $order_list = $this->db 
            ->select('so_order_no')
            ->from("{$this->muser->shiftsorder_tablename} as order")
            ->where('so_user_id', $user_id)
            ->where_not_in('so_order_status', [101])
            ->get()
            ->row_array();
        $order_no = '';
        if ( ! empty($order_list)) {
            $order_no = $order_list['so_order_no'];
        }
        return $order_no;
    }

    public function editStuCommentInfo ($data) {
        return $this->db->where('id', $data['id'])->update($this->cc_tbl, $data);
    }

    public function getStuCommentSchoolInfo ($id) 
    {
        $info = $this->db->select(
                'cc.id,
                cc.order_no,
                cc.type,
                cc.school_star,
                cc.school_content,
                u.l_user_id,
                u.s_username,
                u.s_real_name,
                u.s_phone,
                s.l_school_id,
                s.s_school_name'
                )
                ->from("{$this->cc_tbl} as cc")
                ->join("{$this->user_tbl} as u", 'u.l_user_id = cc.user_id', 'left')
                ->join("{$this->school_tbl} as s", 's.l_school_id = cc.school_id', 'left')
                ->where("cc.id='{$id}'")
                ->get()->row_array();
        $info['user_name'] = '';
        if ( ! empty($info)) {
            $info['user_name'] = $info['s_username'] != '' ? $info['s_username'] : $info['s_real_name'];
        }
        return $info;
    }

// 3.教练评价学员模块
    /**
     * 获取教练评价学员页码信息
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $limit      限定数目
     * @return  $pageinfo
     **/
    public function getCoaCommentStudentPageNum($school_id, $param, $limit)
    {
        $map = [];
        $complex = [];
        // $map['user.i_status'] = 0;
        $map['user.i_user_type'] = 0;
        if ($school_id != 0) {
            $map['coach.s_school_name_id'] = $school_id;
        }
        if ($param) {
            if ($param['star'] != '') {
                $complex['comment.star_num'] = (int)$param['star'];
            }
            $keywords = $param['keywords'];
            if ( $keywords != '') {
                $complex = [
                    'coach.s_coach_name' => $keywords,
                    'school.s_school_name' => $keywords,
                    'comment.order_no' => $keywords,
                    'user.s_real_name' => $keywords,
                    'user.s_username' => $keywords,
                    'user.s_phone' => $keywords
                ];
            }
        }
        $query = $this->db
            ->from("{$this->sc_tbl} as comment")
            ->join("{$this->user_tbl} as user", "user.l_user_id=comment.user_id", "LEFT")
            ->join("{$this->coach_tbl} as coach", "coach.l_coach_id=comment.coach_id", "LEFT")
            ->join("{$this->school_tbl} as school", "school.l_school_id=coach.s_school_name_id", "LEFT")
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }
        $count = $query->count_all_results();
        return $pageinfo = [
            'count' => $count,
            'pagenum' => ceil ( $count / $limit)
        ];
    }

    /**
     * 获取学员评价驾校的列表信息
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $start      开始数
     * @param   int     $limit      限定数目
     * @return  $commentlist
     **/
    public function getCoaCommentStudentList($school_id, $param, $start, $limit)
    {
        $map = [];
        $complex = [];
        // $map['user.i_status'] = 0;
        $map['user.i_user_type'] = 0;
        if ($school_id != 0) {
            $map['coach.s_school_name_id'] = $school_id;
        }
        if ($param) {
            if ($param['star'] != '') {
                $complex['comment.star_num'] = (int)$param['star'];
            }
            $keywords = $param['keywords'];
            if ( $keywords != '') {
                $complex = [
                    'coach.s_coach_name' => $keywords,
                    'school.s_school_name' => $keywords,
                    'comment.order_no' => $keywords,
                    'user.s_real_name' => $keywords,
                    'user.s_username' => $keywords,
                    'user.s_phone' => $keywords
                ];
            }
        }
        $query = $this->db
            ->select(
                'comment.id,
                 comment.star_num,
                 comment.content,
                 comment.user_id,
                 comment.order_no,
                 comment.coach_id,
                 comment.addtime,
                 school.s_school_name as school_name,
                 coach.s_coach_name as coach_name,
                 coach.s_coach_phone as coach_phone,
                 user.s_phone,
                 user.s_username,
                 user.s_real_name'
            )
            ->from("{$this->sc_tbl} as comment")
            ->join("{$this->user_tbl} as user", "user.l_user_id=comment.user_id", "LEFT")
            ->join("{$this->coach_tbl} as coach", "coach.l_coach_id=comment.coach_id", "LEFT")
            ->join("{$this->school_tbl} as school", "school.l_school_id=coach.s_school_name_id", "LEFT")
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }

        $commentlist = $query->limit($limit, $start)
            ->order_by('id', 'desc')
            ->get()
            ->result_array();
        if ( ! empty($commentlist)) {
            foreach ($commentlist as $index => $comment) {

                if ($comment['s_username'] == '') {
                    $commentlist[$index]['s_username'] = '--';
                } 

                if ($comment['s_real_name'] == '') {
                    $commentlist[$index]['s_real_name'] = '--';
                } 
                
                if ($comment['s_phone'] == '') {
                    $commentlist[$index]['s_phone'] = '--';
                } 

                if ($comment['addtime'] != 0 AND $comment['addtime'] != '') {
                    $commentlist[$index]['addtime'] = date('Y-d-m H:i:s', $comment['addtime']);
                } else {
                    $commentlist[$index]['addtime'] = '--';
                }

                if ($comment['school_name'] == '') {
                    $commentlist[$index]['school_name'] = '--';
                }

                if ($comment['content'] == '') {
                    $commentlist[$index]['content'] = '--';
                }

                if ($comment['coach_name'] == '') {
                    $commentlist[$index]['coach_name'] = '--';
                }

                if ($comment['coach_phone'] == '') {
                    $commentlist[$index]['coach_phone'] = '--';
                }
            }
        }
        return $commentlist;
    }

    public function getCoaCommentStudentInfo ($id) 
    {
        $info = $this->db->select(
                'sc.id,
                sc.order_no,
                sc.star_num,
                sc.content,
                c.l_coach_id,
                c.s_coach_name,
                c.s_coach_phone,
                c.s_school_name_id,
                u.l_user_id,
                u.s_real_name,
                u.s_username,
                u.s_phone'
                )
                ->from("{$this->sc_tbl} as sc")
                ->join("{$this->coach_tbl} as c", 'c.l_coach_id=sc.coach_id', 'left')
                ->join("{$this->user_tbl} as u", 'u.l_user_id=sc.user_id', 'left')
                ->where("sc.id='{$id}'")
                ->get()->row_array();
        return $info;
    }

    public function editCoaCommentInfo ($data) 
    {
        return $this->db->where('id', $data['id'])->update($this->sc_tbl, $data);
    }

    public function delInfo($tbname, $data) 
    {
        return $this->db->delete($tbname, $data);
    } 

    public function addStuCommentSchoolInfo($data) 
    {
        $data['addtime'] = time();
        $this->db->insert($this->cc_tbl, $data);
        return $this->db->insert_id();
    }

}