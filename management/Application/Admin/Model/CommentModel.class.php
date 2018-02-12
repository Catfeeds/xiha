<?php 
namespace Admin\Model;
use Think\Model;
use Think\Page;
class CommentModel extends BaseModel {
    public $tableName   = 'coach_comment';
    private $_link      = array(
    
    );

// 1.学员评价教练模块
    /**
     * 获得学员评价教练的列表
     *
     * @return  void
     * @author  wl
     * @date    August 01, 2016
     **/
    public function StudentCommentCoachList ($school_id) {
        $coach_comment_lists = array();
        $map = array();
        if ($school_id == 0) {
            $map['u.i_user_type'] = 0;
            $map['u.i_status'] = 0;
            $map['cc.coach_id'] = array('neq', 0);
        } else {
            $map['u.i_user_type'] = 0;
            $map['u.i_status'] = 0;
            $map['cc.coach_id'] = array('neq', 0);
            $map['s.l_school_id'] = $school_id;
        }
        $count = $this->alias('cc')
            ->join(C('DB_PREFIX').'user u ON u.l_user_id = cc.user_id')
            ->join(C('DB_PREFIX').'coach c ON c.l_coach_id = cc.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = c.s_school_name_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $coach_comment_list = $this->alias('cc')
            ->field(
                'cc.id,
                 cc.addtime, 
                 l_school_id, 
                 s_school_name, 
                 l_coach_id, 
                 s_coach_name, 
                 s_coach_phone,
                 s_school_name_id, 
                 u.l_user_id, 
                 s_username, 
                 s_phone, 
                 cc.type, 
                 coach_content, 
                 coach_star, 
                 order_no'
             )
            ->join(C('DB_PREFIX').'user u ON u.l_user_id = cc.user_id', 'LEFT')
            ->join(C('DB_PREFIX').'coach c ON c.l_coach_id = cc.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = c.s_school_name_id', 'LEFT')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->fetchSql(false)
            ->order('cc.id DESC')
            ->select();
        if (!empty($coach_comment_list)) {
            foreach ($coach_comment_list as $key => $value) {
                if ($value['addtime'] != 0) {
                    $coach_comment_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $coach_comment_list[$key]['addtime'] = '';
                }

                if ($value['s_coach_name'] == '') {
                    $coach_comment_list[$key]['s_coach_name'] = '--';
                }

                if ($value['s_school_name'] == '') {
                    $coach_comment_list[$key]['s_school_name'] = '--';
                }
            }
        }
        $coach_comment_lists = array('coach_comment_lists' => $coach_comment_list, 'page' => $page, 'count' => $count);
        return $coach_comment_lists;
    }

    /**
    * 根据条件搜索学员评价教练列表
    *
    * @return  void
    * @author  wl
    * @date    August 09, 02
    **/
    public function searchStuCommentCoach ($param, $school_id) {
        $map = array();
        $complex = array();
        $coach_comment_lists = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['order_no'] = array('LIKE', $s_keyword);
            $complex['s_coach_name'] = array('LIKE', $s_keyword);
            $complex['s_coach_phone'] = array('LIKE', $s_keyword);
            $complex['s_username'] = array('LIKE', $s_keyword);
            $complex['s_phone'] = array('LIKE', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_info']] = array('LIKE', $s_keyword);
        }
        $map['_complex'] = $complex;

        // if ($param['search_type'] != '') {
        //     $map['type'] = array('EQ', $param['search_type']);
        // }

        if ($param['search_star'] != '') {
            $map['coach_star'] = array('EQ', $param['search_star']);
        }
        
        if ($school_id == 0) {
            $map['u.i_user_type'] = 0;
            $map['u.i_status'] = 0;
            $map['cc.coach_id'] = array('neq', 0);
        } else {
            $map['u.i_user_type'] = 0;
            $map['u.i_status'] = 0;
            $map['cc.coach_id'] = array('neq', 0);
            $map['s.l_school_id'] = $school_id;
        }
        $count = $this->alias('cc')
            ->join(C('DB_PREFIX').'user u ON u.l_user_id = cc.user_id')
            ->join(C('DB_PREFIX').'coach c ON c.l_coach_id = cc.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = c.s_school_name_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $coach_comment_list = $this->alias('cc')
            ->field(
                'cc.id,
                 cc.addtime, 
                 l_school_id, 
                 s_school_name, 
                 l_coach_id, 
                 s_coach_name, 
                 s_coach_phone,
                 s_school_name_id, 
                 u.l_user_id, 
                 s_username, 
                 s_phone, 
                 cc.type, 
                 coach_content, 
                 coach_star, 
                 order_no'
             )
            ->join(C('DB_PREFIX').'user u ON u.l_user_id = cc.user_id', 'LEFT')
            ->join(C('DB_PREFIX').'coach c ON c.l_coach_id = cc.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = c.s_school_name_id', 'LEFT')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->fetchSql(false)
            ->order('cc.id DESC')
            ->select();
        if (!empty($coach_comment_list)) {
            foreach ($coach_comment_list as $key => $value) {
                if ($value['addtime'] != 0) {
                    $coach_comment_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $coach_comment_list[$key]['addtime'] = '';
                }

                if ($value['s_coach_name'] == '') {
                    $coach_comment_list[$key]['s_coach_name'] = '--';
                }

                if ($value['s_school_name'] == '') {
                    $coach_comment_list[$key]['s_school_name'] = '--';
                }
            }
        }
        $coach_comment_lists = array('coach_comment_lists' => $coach_comment_list, 'page' => $page, 'count' => $count);
        return $coach_comment_lists;
    }

    /**
     * 删除评价（教练或者驾校）[学员评价教练和学员评价驾校都有用到]
     *
     * @return void
     * @author wl
     * @date   August 09, 2016
     **/
    public function delComment ($comment_id) {
        if (!is_numeric($comment_id)) {
            return false;
        }
        $del = M('coach_comment')
            ->where(array('id'=>$comment_id))
            ->delete();
        return $del;
    }

// 2.学员评价驾校模块
    /**
     * 获取学员评价驾校的列表展示
     *
     * @return void
     * @author wl
     * @date   August 09, 01
     **/
    public function studentCommentSchoolList ($school_id) {
        $school_comment_lists = array();
        $map = array();
        if ($school_id == 0) {
            $map['u.i_user_type'] = array('eq', 0);
            // $map['u.i_status'] = array('eq', 0);
            $map['cc.school_id'] = array('neq', 0);
        } else {
            $map['u.i_user_type'] = array('eq', 0);
            // $map['u.i_status'] = array('eq', 0);
            $map['cc.school_id'] = array('neq', 0);
            $map['school_id'] = $school_id;
        }
        $count = $this->table(C('DB_PREFIX').'coach_comment cc')
            ->join(C('DB_PREFIX').'user u ON u.l_user_id = cc.user_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = cc.school_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $school_comment_list = $this->table(C('DB_PREFIX').'coach_comment cc')
            ->field(
                'cc.id,
                 cc.addtime,
                 cc.type, 
                 school_content, 
                 school_star,
                 order_no, 
                 cc.school_id,
                 cc.user_id, 
                 u.l_user_id, 
                 u.s_username, 
                 u.s_phone, 
                 u.s_real_name, 
                 s.l_school_id, 
                 s.s_school_name'
            )
            ->join(C('DB_PREFIX').'user u ON u.l_user_id = cc.user_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = cc.school_id', 'LEFT')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->fetchSql(false)
            ->order('cc.id DESC')
            ->select();
        
        if (!empty($school_comment_list)) {
            foreach ($school_comment_list as $key => $value) {
                if ($value['addtime'] != 0) {
                    $school_comment_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $school_comment_list[$key]['addtime'] = '';
                }

                if ($value['s_username'] == '') {
                    $school_comment_list[$key]['s_username'] = '--';
                }

                if ($value['s_real_name'] == '') {
                    $school_comment_list[$key]['s_real_name'] = '--';
                }

                if ($value['s_phone'] == '') {
                    $school_comment_list[$key]['s_phone'] = '--';
                }

                // if ($value['school_content'] == '') {
                //     $school_comment_list[$key]['school_content'] = '--';
                // }

                if ($value['s_school_name'] == '') {
                    $school_comment_list[$key]['s_school_name'] = '--';
                }

            }
        }
        $school_comment_lists = array('school_comment_lists' => $school_comment_list, 'page' => $page, 'count' => $count);
        return $school_comment_lists;
    }
    /**
     * 根据搜索条件搜索学员评价驾校的列表
     *
     * @return  void
     * @author  wl
     * @date    August 09, 01
     **/
    public function searchStuCommentSchool ($param, $school_id) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['order_no'] = array('LIKE', $s_keyword);
            $complex['s_school_name'] = array('LIKE', $s_keyword);
            $complex['s_phone'] = array('LIKE', $s_keyword);
            // $complex['s_real_name'] = array('LIKE', $s_keyword);
            $complex['s_username'] = array('LIKE', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_info']] = array('LIKE', $s_keyword);
        }
        $map['_complex'] = $complex;

        // if ($param['search_type'] != '') {
        //     $map['type'] = array('LIKE', $param['search_type']); 
        // }

        if ($param['search_star'] != '') {
            $map['school_star'] = array('LIKE', $param['search_star']); 
        }

        if ($school_id == 0) {
            $map['u.i_user_type'] = array('eq', 0);
            // $map['u.i_status'] = array('eq', 0);
            $map['cc.school_id'] = array('neq', 0);
        } else {
            $map['u.i_user_type'] = array('eq', 0);
            // $map['u.i_status'] = array('eq', 0);
            $map['cc.school_id'] = array('neq', 0);
            $map['school_id'] = $school_id;
        }

        $school_comment_lists = array();
        $count = $this->table(C('DB_PREFIX').'coach_comment cc')
            ->join(C('DB_PREFIX').'user u ON u.l_user_id = cc.user_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = cc.school_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $school_comment_list = $this->table(C('DB_PREFIX').'coach_comment cc')
            ->field(
                'cc.id,
                 cc.addtime,
                 cc.type, 
                 school_content, 
                 school_star,
                 order_no, 
                 cc.school_id,
                 cc.user_id, 
                 u.l_user_id, 
                 u.s_username, 
                 u.s_phone, 
                 u.s_real_name, 
                 s.l_school_id, 
                 s.s_school_name'
            )
            ->join(C('DB_PREFIX').'user u ON u.l_user_id = cc.user_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = cc.school_id', 'LEFT')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->fetchSql(false)
            ->order('cc.id DESC')
            ->select();
        if (!empty($school_comment_list)) {
            foreach ($school_comment_list as $key => $value) {
                if ($value['addtime'] != 0) {
                    $school_comment_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $school_comment_list[$key]['addtime'] = '';
                }

                if ($value['s_username'] == '') {
                    $school_comment_list[$key]['s_username'] = '--';
                }

                if ($value['s_real_name'] == '') {
                    $school_comment_list[$key]['s_real_name'] = '--';
                }

                if ($value['s_phone'] == '') {
                    $school_comment_list[$key]['s_phone'] = '--';
                }

                // if ($value['school_content'] == '') {
                //     $school_comment_list[$key]['school_content'] = '--';
                // }

                if ($value['s_school_name'] == '') {
                    $school_comment_list[$key]['s_school_name'] = '--';
                }
            }
        }
        $school_comment_lists = array('school_comment_lists' => $school_comment_list, 'page' => $page, 'count' => $count);
        return $school_comment_lists;
    }

    /**
     * 获取学员的相关信息
     *
     * @return  void
     * @author  wl
     * @date    Mar 16, 2017
     **/
    public function getUserList ($school_id) {
        if (!is_numeric($school_id)) {
            return false;
        }
        $condition = array(
            'user.i_status' => array('eq', 0),
            'user.i_user_type' => array('eq', 0),
            'users_info.school_id' => $school_id,
            'school_orders.so_school_id' => $school_id,
            'users_info.user_id' => array('gt', 0),
            'school_orders.so_order_status' => array('neq', 101)
        );
        $userlist = $this->table(C('DB_PREFIX').'users_info users_info')
            ->field(
                'users_info.user_id as user_id,
                 user.l_user_id as l_user_id,
                 user.s_phone as user_phone,
                 user.s_username as s_username,
                 user.s_real_name as real_name,
                 school_orders.so_order_no as order_no'
            )
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = users_info.user_id', 'LEFT')
            ->join(C('DB_PREFIX').'school_orders school_orders ON school_orders.so_user_id = user.l_user_id', 'LEFT')
            // ->limit(1, 2000) // 由于八一驾校注册人数过多，导致数据展示出现错误，故带上条数
            ->where($condition)
            ->select();
        $userlists = array();
        if (!empty($userlist)) {
            foreach ($userlist as $key => $value) {
                if ($value['l_user_id'] != '') {
                    $userlists[$key]['l_user_id'] = $value['l_user_id'];
                }
                if ($value['real_name'] == '') {
                    if ($value['s_username'] == '') {
                        if ($value['user_phone'] != '') {
                            $userlists[$key]['user_name'] = '嘻哈学车'.substr($value['user_phone'], -4, 4);
                        }
                    } else {
                        $userlists[$key]['user_name'] = $value['s_username'];
                    }
                } else {
                    $userlists[$key]['user_name'] = $value['real_name'];
                }
            }
            return $userlists;
        } else {
            return array();
        }
    }

    /**
     * 获取订单号（报名驾校）
     *
     * @return  void
     * @author  wl
     * @date    Mar 16, 2017
     **/
    public function getOrderNoList ($user_id) {
        if (!is_numeric($user_id)) {
            return false;
        }
        $condition = array('so_user_id' => $user_id);
        $ordernolist = $this->table(C('DB_PREFIX').'school_orders school_orders')
            ->where($condition)
            ->getField('so_order_no');
            // ->field('so_order_no')
            // ->find();
        if (!empty($ordernolist)) {
            return $ordernolist;
        } else {
            return '';
        }
    }

    /**
     * 根据id获取学员评价教练的相关信息
     *
     * @return  void
     * @author  wl
     * @date    Mar 17, 2017
     **/
    public function getStuComSchoolById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $where = array(
            'id' => $id,
            'user.i_status' => 0,
            'user.i_user_type' => 0,
        );
        $stucomschoollist = $this->table(C('DB_PREFIX').'coach_comment coach_comment')
            ->field(
                'coach_comment.id as id,
                 coach_comment.school_star as school_star,
                 coach_comment.school_content as school_content,
                 coach_comment.user_id as user_id,
                 coach_comment.order_no as order_no,
                 coach_comment.school_id as school_id,
                 coach_comment.type as type,
                 user.s_phone as user_phone,
                 user.s_username as s_username,
                 user.s_real_name as real_name'
            )
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = coach_comment.user_id', 'LEFT')
            ->where($where)
            ->find();
        if (!empty($stucomschoollist)) {
            if ($stucomschoollist['real_name'] == '') {
                if ($stucomschoollist['s_username'] == '') {
                    if ($stucomschoollist['user_phone'] != '') {
                        $stucomschoollist['user_name'] = '嘻哈学车'.substr($stucomschoollist['user_phone'], -4, 4);
                    }
                } else {
                    $stucomschoollist['user_name'] = $stucomschoollist['s_username'];
                }
            } else {
                $stucomschoollist['user_name'] = $stucomschoollist['real_name'];
            }
            if ($stucomschoollist['school_id'] != '') {
                $school_id = $stucomschoollist['school_id'];
                $stucomschoollist['school_name'] = $this->table(C('DB_PREFIX').'school school')
                    ->where(array('l_school_id' => $school_id))
                    ->getField('s_school_name');
            }
            return $stucomschoollist;
        } else {
            return array();
        }
    }


// 3.教练评价学员模块
    /**
     * 获取教练评价学员列表
     *
     * @return  void
     * @author  wl
     * @date    August 09, 2016
     * @update  Dec 01, 2016
     **/
    public function CoachCommentStudentList ($school_id) {
        $map = array();
        $student_comment_lists  = array();
        if ($school_id == 0) {
            $map['user.i_status'] = 0;
            $map['i_user_type'] = 0;
        } else {
            $map['user.i_status'] = 0;
            $map['user.i_user_type'] = 0;
            $map['l_school_id'] = $school_id;
        }
        $count = $this->table(C('DB_PREFIX').'student_comment student_comment')
            ->join(C('DB_PREFIX').'coach coach ON coach.l_coach_id = student_comment.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = student_comment.user_id', 'LEFT')
            ->fetchSql(false)
            ->where($map)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $student_comment_list = $this->table(C('DB_PREFIX').'student_comment student_comment')
            ->field(
                'student_comment.*,
                 coach.l_coach_id, 
                 coach.s_coach_name, 
                 s_coach_phone,  
                 s_school_name_id, 
                 school.l_school_id, 
                 school.s_school_name, 
                 user.l_user_id, 
                 user.s_real_name,
                 user.s_username, 
                 user.s_phone'
            )
            ->join(C('DB_PREFIX').'coach coach ON coach.l_coach_id = student_comment.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = student_comment.user_id', 'LEFT')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('student_comment.id DESC')
            ->fetchSql(false)
            ->select();
        if ($student_comment_list) {
            foreach ($student_comment_list as $key => $value) {
                if ($value['addtime'] != 0) {
                    $student_comment_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $student_comment_list[$key]['addtime'] = '';
                }

                if ($value['s_school_name'] == '') {
                    $student_comment_list[$key]['s_school_name'] = '---';
                }

                if ($value['s_coach_name'] == '') {
                    $student_comment_list[$key]['s_coach_name'] = '---';
                }

                if ($value['s_username'] == '') {
                    $student_comment_list[$key]['s_username'] = '---';
                }

                if ($value['s_real_name'] == '') {
                    $student_comment_list[$key]['s_real_name'] = '---';
                }

                if ($value['s_phone'] == '') {
                    $student_comment_list[$key]['s_phone'] = '---';
                }

                if ($value['s_coach_phone'] == '') {
                    $student_comment_list[$key]['s_coach_phone'] = '---';
                }
            }   
        } 
        $student_comment_lists = array('student_comment_lists' => $student_comment_list, 'page' => $page, 'count' => $count);
        return $student_comment_lists; 
    }
    /**
     * 根据条件搜索评价列表
     *
     * @return  void
     * @author  wl
     * @author  August 09, 2016
     **/
    public function searchCoachCommentStu ($param,$school_id) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['order_no'] = array('LIKE', $s_keyword);
            $complex['s_coach_name'] = array('LIKE', $s_keyword);
            $complex['s_coach_phone'] = array('LIKE', $s_keyword);
            $complex['s_username'] = array('LIKE', $s_keyword);
            $complex['s_real_name'] = array('LIKE', $s_keyword);
            $complex['s_phone'] = array('LIKE', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_info']] = array('LIKE', $s_keyword);
        }
        $map['_complex'] = $complex;

        if ($param['search_star'] != '') {
            $map['star_num'] = array('LIKE', $param['search_star']); 
        }
        $student_comment_lists  = array();
        if ($school_id == 0) {
            $map['user.i_status'] = 0;
            $map['i_user_type'] = 0;
        } else {
            $map['user.i_status'] = 0;
            $map['user.i_user_type'] = 0;
            $map['l_school_id'] = $school_id;
        }
        $count = $this->table(C('DB_PREFIX').'student_comment student_comment')
            ->join(C('DB_PREFIX').'coach coach ON coach.l_coach_id = student_comment.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = student_comment.user_id', 'LEFT')
            ->fetchSql(false)
            ->where($map)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $student_comment_list = $this->table(C('DB_PREFIX').'student_comment student_comment')
            ->field(
                'student_comment.*,
                 coach.l_coach_id, 
                 coach.s_coach_name, 
                 s_coach_phone,  
                 s_school_name_id, 
                 school.l_school_id, 
                 school.s_school_name, 
                 user.l_user_id, 
                 user.s_real_name,
                 user.s_username, 
                 user.s_phone'
            )
            ->join(C('DB_PREFIX').'coach coach ON coach.l_coach_id = student_comment.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = student_comment.user_id', 'LEFT')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('student_comment.id DESC')
            ->fetchSql(false)
            ->select();
        if ($student_comment_list) {
            foreach ($student_comment_list as $key => $value) {
                if ($value['addtime'] != 0) {
                    $student_comment_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $student_comment_list[$key]['addtime'] = '';
                }

                if ($value['s_school_name'] == '') {
                    $student_comment_list[$key]['s_school_name'] = '---';
                }

                if ($value['s_coach_name'] == '') {
                    $student_comment_list[$key]['s_coach_name'] = '---';
                }

                if ($value['s_username'] == '') {
                    $student_comment_list[$key]['s_username'] = '---';
                }

                if ($value['s_real_name'] == '') {
                    $student_comment_list[$key]['s_real_name'] = '---';
                }

                if ($value['s_phone'] == '') {
                    $student_comment_list[$key]['s_phone'] = '---';
                }

                if ($value['s_coach_phone'] == '') {
                    $student_comment_list[$key]['s_coach_phone'] = '---';
                }
            }   
        } 
        $student_comment_lists = array('student_comment_lists' => $student_comment_list, 'page' => $page, 'count' => $count);
        return $student_comment_lists;
    }

    /**
     * 删除教练对学员的评价
     *
     * @return  void
     * @author  wl
     * @author  August 09, 2016
     **/
    public function delCoachCommentStu ($id) {
        $result = M('student_comment')
            ->where('id = :comment_id')
            ->bind(['comment_id' => $id])
            ->fetchSql(false)
            ->delete();
        return $result;
    }    

















}
?>