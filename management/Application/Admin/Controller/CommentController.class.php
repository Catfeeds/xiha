<?php 
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
use Think\Crypt;
// 评价管理模块
class CommentController extends BaseController {
    //构造函数，判断是否是登录状态
    public function _initialize() {
        if (!session('loginauth')) {
            $this->redirect('Public/login');
            exit();
        }
    }
    
// 学员评价教练模块
    /**
     * 学员评价教练列表的展示
     *
     * @return  void
     * @author  wl
     * @date    August 01, 2016
     **/
    public function index(){
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        $coach_comment_lists = D('Comment')->StudentCommentCoachList($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $coach_comment_lists['page']);
        $this->assign('count', $coach_comment_lists['count']);
        $this->assign('comment_list', $coach_comment_lists['coach_comment_lists']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }

    /**
     * 根据条件搜索学员评价教练列表
     *
     * @return  void
     * @author  wl
     * @date    August 09, 02
     **/
    public function searchStuCommentCoach () {
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        // $search_type = trim((int)$param['search_type']);
        $search_star = trim((string)$param['search_star']);
        $search_info = trim((string)$param['search_info']);
        if ($s_keyword == '' && $search_type == '' && $search_star == '' && $search_info == '' ) {
            $this->redirect('Comment/index');
        } else {
            $coach_comment_lists   = D('Comment')->searchStuCommentCoach($param, $school_id);
            $this->assign('school_id', $school_id);
            $this->assign('s_keyword', $s_keyword);
            // $this->assign('search_type', $search_type);
            $this->assign('search_star', $search_star);
            $this->assign('search_info', $search_info);
            $this->assign('count', $coach_comment_lists['count']);
            $this->assign('page', $coach_comment_lists['page']);
            $this->assign('comment_list', $coach_comment_lists['coach_comment_lists']);
            $this->display('Comment/index');
        }
    }

    /**
     * 删除评论教练
     *
     * @return 
     * @author wl
     * @update August 09, 01
     **/
    public function delStuCommentCoach () {
        if (IS_AJAX) {
            $role_id = $this->getRoleId();
            $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
            if (true !== $permission_check) {
                $this->ajaxReturn(array('code' => 400, 'msg' => 'Permission denied!', 'data' => ''));
            }
            $comment_id = I('post.id');
            $del = D('Student')->delComment($comment_id);
            if ($del) { 
                $data = array('code'=>'200', 'msg'=>'删除成功', 'data'=>$comment_id);
            } else {
                $data = array('code'=>'400', 'msg'=>'删除失败', 'data'=>'');
            }
        } else {
            $data = array('code'=>'400', 'msg'=>'删除失败', 'data'=>'');
        }
        action_log('del_stucomment_coach', 'coach_comment', $comment_id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }


// 2.学员评价驾校模块
    /**
     * 学员评价驾校列表的展示
     *
     * @return  void
     * @author  wl
     * @date    August 09, 01
     **/
    public function studentCommentSchool () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Comment/index'));
        }
        $school_id = $this->getLoginauth();
        $school_comment_lists = D('Comment')->studentCommentSchoolList($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $school_comment_lists['page']);
        $this->assign('count', $school_comment_lists['count']);
        $this->assign('comment_list', $school_comment_lists['school_comment_lists']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }


    /**
     * 根据条件搜索学员评价驾校列表的展示
     *
     * @return  void
     * @author  wl
     * @date    August 09, 01
     **/
    public function searchStuCommentSchool () {
        $school_id = $this->getLoginauth();
        $param = I('param.');
        // $search_type = trim((int)$param['search_type']);
        $search_star = trim((string)$param['search_star']);
        $s_keyword = trim((string)$param['s_keyword']);
        $search_info = trim((string)$param['search_info']);
        if ($s_keyword == '' && $search_star == '' && $search_type == '' && $search_info == '') {
            $this->redirect('Comment/studentCommentSchool');
        } else {
            $school_comment_lists   = D('Comment')->searchStuCommentSchool($param, $school_id);
            $this->assign('school_id', $school_id);
            $this->assign('s_keyword', $s_keyword);
            // $this->assign('search_type', $search_type);
            $this->assign('search_star', $search_star);
            $this->assign('search_info', $search_info);
            $this->assign('count', $school_comment_lists['count']);
            $this->assign('page', $school_comment_lists['page']);
            $this->assign('comment_list', $school_comment_lists['school_comment_lists']);
            $this->display('Comment/studentCommentSchool');
        }
    }

    /**
     * 获取用户列表
     *
     * @return  void
     * @author  wl
     * @date    Mar 16, 2017
     **/
    public function getUserList () {
        if (IS_AJAX) {
            $post = I('post.');
            $school_id = $post['school_id'];
            $userlist = D('Comment')->getUserList($school_id);
            if (!empty($userlist)) {
                $data = array('code' => 200, 'msg' => '获取成功', 'data' => $userlist);
            } else {
                $data = array('code' => 400, 'msg' => '获取失败', 'data' => array());
            }
        } else {
            $data = array('code' => 400, 'msg' => '获取失败', 'data' => array());
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 获取用户列表
     *
     * @return  void
     * @author  wl
     * @date    Mar 16, 2017
     **/
    public function getOrderNoList () {
        if (IS_AJAX) {
            $post = I('post.');
            $user_id = $post['user_id'];
            $ordernolist = D('Comment')->getOrderNoList($user_id);
            if (!empty($ordernolist)) {
                $data = array('code' => 200, 'msg' => '获取成功', 'data' => $ordernolist);
            } else {
                $data = array('code' => 400, 'msg' => '获取失败', 'data' => array());
            }
        } else {
            $data = array('code' => 400, 'msg' => '获取失败', 'data' => array());
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 添加学员评价驾校
     *
     * @return  void
     * @author  wl
     * @date    Mar 17, 2017
     **/
    public function addStudentCommentSchool () {
        $school_list = D('Manager')->getSchoolList();
        if (IS_POST) {
            $post = I('post.');
            $data['coach_id'] = 0;
            $data['coach_star'] = 0.0;
            if ($post['star'] > 5) {
                $data['school_star'] = 5;
            } else if ($post['star'] < 1) {
                $data['school_star'] = 1;
            } else {
                $data['school_star'] = $post['star'] != '' ? $post['star'] : 1;
            }
            $data['coach_content'] = '';
            $data['school_content'] = $post['content'] !='' ? $post['content'] : '';
            $data['user_id'] = $post['user_id'] != '' ? $post['user_id'] : '';
            $data['order_no'] = $post['order_no'] != '' ? $post['order_no'] : '';
            $data['school_id'] = $post['school_id'] != '' ? $post['school_id'] : '';
            $data['type'] = $post['type'] != '' ? $post['type'] : 2; // 1：预约学车；2：报名驾校
            $data['addtime'] = time();
            $coach_comment = D('coach_comment');
            if ($res = $coach_comment->create($data)) {
                $result = $coach_comment->add($res);
                if ($result) {
                    $this->success('添加成功', U('Comment/addStudentCommentSchool'));
                } else {
                    $this->error('添加失败', U('Comment/studentCommentSchool'));
                }
            } else {
                $this->error('添加失败', U('Comment/studentCommentSchool'));
            }
        } else {
            $this->assign('school_list', $school_list);
            $this->display('Comment/addStudentCommentSchool');
        }
    }

    /**
     * 编辑学员评价驾校的相关信息
     *
     * @return  void
     * @author  wl
     * @date    Mar 17, 2017
     **/
    public function editStudentCommentSchool () {
        $param = I('param.');
        $id = $param['id'];
        $comschoollist = D('Comment')->getStuComSchoolById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['id'] = $post['id'];
            // $data['coach_id'] = 0;
            // $data['coach_star'] = 0.0;
            if ($post['star'] > 5) {
                $data['school_star'] = 5;
            } else if ($post['star'] < 1) {
                $data['school_star'] = 1;
            } else {
                $data['school_star'] = $post['star'] != '' ? $post['star'] : $comschoollist['school_star'];
            }

            // $data['coach_content'] = '';
            $data['school_content'] = $post['content'] !='' ? $post['content'] : $comschoollist['school_scontent'];
            $data['user_id'] = $post['user_id'] != '' ? $post['user_id'] : $comschoollist['user_id'];
            $data['order_no'] = $post['order_no'] != '' ? $post['order_no'] : $comschoollist['order_no'];
            $data['school_id'] = $post['school_id'] != '' ? $post['school_id'] : $comschoollist['school_id'];
            $data['type'] = $post['type'] != '' ? $post['type'] : 2; // 1：预约学车；2：报名驾校
            // $data['addtime'] = time();
            $coach_comment = D('coach_comment');
            if ($res = $coach_comment->create($data)) {
                $result = $coach_comment->where(array('id' => $data['id']))->fetchSql(false)->save($res);
                if ($result) {
                    $this->success('修改成功', U('Comment/editStudentCommentSchool'));
                } else {
                    $this->error('修改失败', U('Comment/studentCommentSchool'));
                }
            } else {
                $this->error('修改失败', U('Comment/studentCommentSchool'));
            }
        } else {
            $this->assign('comschoollist', $comschoollist);
            $this->display('Comment/editStudentCommentSchool');
        }
    }

    /**
     * 删除学员评价驾校
     *
     * @return 
     * @author  wl
     * @date    Dec 01, 2016
     **/
    public function delStuCommentSchool () {
        if (IS_AJAX) {
            $role_id = $this->getRoleId();
            $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
            if (true !== $permission_check) {
                $this->ajaxReturn(array('code' => 400, 'msg' => 'Permission denied!', 'data' => ''));
            }
            $comment_id = I('post.id');
            $del = D('Comment')->delComment($comment_id);
            if ($del) { 
                $data = array('code'=>'200', 'msg'=>'删除成功', 'data'=>$comment_id);
            } else {
                $data = array('code'=>'400', 'msg'=>'删除失败', 'data'=>'');
            }
        } else {
            $data = array('code'=>'400', 'msg'=>'删除失败', 'data'=>'');
        }
        action_log('del_stucomment_school', 'coach_comment', $comment_id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    
// 3.教练评价学员
    /**
     * 教练评价学员列表展示
     *
     * @return  void
     * @author  wl
     * @date    August 09, 2016
     **/
    public function coachCommentStudent () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Comment/index'));
        }
        $school_id = $this->getLoginauth();
        $student_comment_lists = D('Comment')->CoachCommentStudentList($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $student_comment_lists['page']);
        $this->assign('count', $student_comment_lists['count']);
        $this->assign('comment_list', $student_comment_lists['student_comment_lists']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }

    /**
     * 根据条件搜索教练评价学员的列表
     *
     * @return  void
     * @author  wl
     * @author  August 09, 2016
     **/
    public function searchCoachCommentStu () {
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $search_star = trim((string)$param['search_star']);
        $s_keyword = trim((string)$param['s_keyword']);
        $search_info = trim((string)$param['search_info']);
        if ($s_keyword == '' && $search_star == '' && $search_info == '') {
            $this->redirect('Comment/coachCommentStudent');
        } else {
            $student_comment_lists   = D('Comment')->searchCoachCommentStu($param, $school_id);
            $this->assign('school_id', $school_id);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('search_info', $search_info);
            $this->assign('search_star', $search_star);
            $this->assign('count', $student_comment_lists['count']);
            $this->assign('page', $student_comment_lists['page']);
            $this->assign('comment_list', $student_comment_lists['student_comment_lists']);
            $this->display('Comment/coachCommentStudent');
        }
    }

    /**
     * 删除教练评论学员
     *
     * @return  void
     * @author  wl 
     * @date    August 09, 2016
     **/
    public function delCoachCommentStu() {
        if (IS_AJAX) {
            $role_id = $this->getRoleId();
            $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
            if (true !== $permission_check) {
                $this->error('Permission Denied!', U('Comment/coachCommentStudent'));
            }
            $id = I('post.id');
            $del = D('Comment')->delCoachCommentStu($id);
            if ($del) { 
                $data = array('code'=>'200', 'msg'=>'删除成功', 'data'=>$id);
            } else {
                $data = array('code'=>'400', 'msg'=>'删除失败', 'data'=>'');
            }
        } else {
            $data = array('code'=>'400', 'msg'=>'删除失败', 'data'=>'');
        }
        action_log('del_coach_commentstu', 'student_comment', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }
}
